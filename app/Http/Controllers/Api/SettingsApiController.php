<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingsApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $group = $request->string('group')->toString();

        if ($group !== '') {
            return response()->json([
                'group' => $group,
                'settings' => Setting::getGroup($group),
            ]);
        }

        $grouped = Setting::query()
            ->get()
            ->groupBy('group_name')
            ->map(fn ($items) => $items->pluck('value', 'key'));

        return response()->json($grouped);
    }

    public function update(Request $request, string $group): JsonResponse
    {
        $validated = $request->validate([
            'settings' => ['required', 'array'],
            'settings.*' => ['nullable', 'string'],
        ]);

        foreach ($validated['settings'] as $key => $value) {
            Setting::setValue($group, (string) $key, $value);
        }

        return response()->json([
            'message' => 'Settings updated successfully.',
            'group' => $group,
            'settings' => Setting::getGroup($group),
        ]);
    }
}
