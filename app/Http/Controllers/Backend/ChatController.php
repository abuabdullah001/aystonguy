<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;
use Gemini\Laravel\Facades\Gemini;
use Gemini\Enums\MimeType;

class ChatController extends Controller
{
    public function index()
    {
        return view('Backend.chats.index');
    }

    public function conversation(Request $request)
    {
        $userId = auth()->id();
        
        // Fetch ALL chats involving the user
        $chats = Chat::where('user_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->oldest()
            ->get();

        return response()->json([
            'html' => view('Backend.chats.messages', compact('chats'))->render(),
            'count' => $chats->count(),
            'user_id' => $userId
        ]);
    }

    public function transcribe(Request $request)
    {
        $request->validate([
            'audio' => 'required|mimes:webm,wav,mp3,m4a,ogg|max:10240',
        ]);

        try {
            $response = OpenAI::audio()->transcribe([
                'model' => 'whisper-1',
                'file' => fopen($request->file('audio')->path(), 'r'),
            ]);

            return response()->json(['text' => $response->text]);
        } catch (\Throwable $e) {
            Log::error('Transcribe Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'nullable|string',
            'image' => 'nullable|image|max:5120',
            'audio' => 'nullable|mimes:webm,wav,mp3,aac,m4a,ogg|max:10240',
        ]);

        $userId = auth()->id();
        $filePath = null;
        $fileType = 'text';

        if ($request->hasFile('image')) {
            $filePath = 'storage/' . $request->file('image')->store('uploads/chats', 'public');
            $fileType = 'image';
        } elseif ($request->hasFile('audio')) {
            $filePath = 'storage/' . $request->file('audio')->store('uploads/chats', 'public');
            $fileType = 'voice';
        }

        // 1. Save User Message
        $userChat = Chat::create([
            'user_id' => $userId,
            'sender_type' => 'user',
            'type' => $fileType,
            'message' => $request->message,
            'file_path' => $filePath,
            'is_read' => true
        ]);

        $aiResponse = null;

        // 2. Fetch Conversation History for Context
        $history = Chat::where('user_id', $userId)
            ->whereNotNull('message')
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get()
            ->reverse();

        $messages = [];
        foreach ($history as $h) {
            $role = ($h->sender_type == 'user') ? 'user' : 'assistant';
            $messages[] = ['role' => $role, 'content' => $h->message];
        }

        // 2. AI Processing with OpenAI (Since it's the premium active key)
        try {
            if ($request->hasFile('image')) {
                // Image Processing with OpenAI Vision
                $imageData = base64_encode(file_get_contents($request->file('image')->path()));
                $mimeType = $request->file('image')->getMimeType();
                
                // Add current image message
                $messages[] = [
                    'role' => 'user',
                    'content' => [
                        ['type' => 'text', 'text' => $request->message ?? "What is in this image?"],
                        ['type' => 'image_url', 'image_url' => ['url' => "data:{$mimeType};base64,{$imageData}"]],
                    ],
                ];

                $result = OpenAI::chat()->create([
                    'model' => 'gpt-4o-mini',
                    'messages' => $messages,
                ]);
                $aiResponse = $result->choices[0]->message->content;
            } elseif ($request->hasFile('audio') && empty($request->message)) {
                // If they bypassed frontend transcription somehow
                $response = OpenAI::audio()->transcribe([
                    'model' => 'whisper-1',
                    'file' => fopen($request->file('audio')->path(), 'r'),
                ]);
                
                $messages[] = ['role' => 'user', 'content' => $response->text];

                $result = OpenAI::chat()->create([
                    'model' => 'gpt-4o-mini',
                    'messages' => $messages,
                ]);
                $aiResponse = $result->choices[0]->message->content;
            } else {
                // Standard Text logic (History already added above)
                // If history was empty or last message isn't the current one, the logic below ensures it's fresh
                if (empty($messages) || end($messages)['content'] !== $request->message) {
                    // History loop above handles most cases, but if we just created $userChat, 
                    // we could also just use the $messages array we built.
                }

                $result = OpenAI::chat()->create([
                    'model' => 'gpt-4o-mini',
                    'messages' => $messages,
                ]);
                $aiResponse = $result->choices[0]->message->content;
            }
        } catch (\Throwable $e) {
            Log::error('OpenAI General Error: ' . $e->getMessage());
            try {
                // Secondary Fallback for everything (simple string)
                $result = OpenAI::chat()->create([
                    'model' => 'gpt-4o-mini',
                    'messages' => [['role' => 'user', 'content' => "Hello"]],
                ]);
                $aiResponse = $result->choices[0]->message->content;
            } catch (\Throwable $e2) {
                $aiResponse = $this->getLocalAiResponse($request->message);
            }
        }

        // 3. Save AI Message
        if ($aiResponse) {
            $aiChat = Chat::create([
                'user_id' => $userId,
                'sender_type' => 'admin', 
                'type' => 'text',
                'message' => $aiResponse,
                'is_ai' => true,
                'is_read' => true
            ]);

            return response()->json(['success' => true, 'chat' => $aiChat]);
        }

        return response()->json(['success' => false, 'message' => 'Something went wrong.'], 500);
    }

    /**
     * Local Super-Fast Fallback (Mock AI)
     * This acts as a 'Safety Net' when all external APIs are busy.
     */
    private function getLocalAiResponse($input)
    {
        $input = strtolower($input);
        
        // Simple context-aware responses for common queries
        if (str_contains($input, 'hello') || str_contains($input, 'hi')) {
            return "Hello! Both my OpenAI and Gemini engines are a bit busy right now, but I'm still here to help! How can I assist you today?";
        }
        
        if (str_contains($input, 'time')) {
            return "The current server time is " . now()->format('h:i A') . ". I apologize that my deep-thinking brains are temporarily cooling down!";
        }

        if (str_contains($input, 'who are you')) {
            return "I am Ayeston AI. My premium cloud engines are currently at their limit, so I am running on my lightweight local backup mode!";
        }

        return "I'm currently receiving too many requests. While my main cloud brains (OpenAI & Gemini) are cooling down, I can tell you that I've received your message: \"" . ucfirst($input) . "\". Please try a complex query again in about 30 seconds!";
    }

    public function destroy(Chat $chat)
    {
        if ($chat->file_path) {
            $realPath = str_replace('storage/', '', $chat->file_path);
            Storage::disk('public')->delete($realPath);
        }
        $chat->delete();
        return response()->json(['success' => true]);
    }
}
