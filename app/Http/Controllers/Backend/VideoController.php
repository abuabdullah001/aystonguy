<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Video;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class VideoController extends Controller
{
    private const DEFAULT_UPLOAD_DISK = 'public';

    private function uploadDisk(): string
    {
        /**
         * Allow using local storage when AWS S3 isn't configured.
         * Set VIDEO_UPLOAD_DISK=s3 when ready.
         */
        $disk = (string) env('VIDEO_UPLOAD_DISK', self::DEFAULT_UPLOAD_DISK);

        return $disk !== '' ? $disk : self::DEFAULT_UPLOAD_DISK;
    }

    public function index(): View
    {
        return view('Backend.videos.index');
    }

    public function create(): View
    {
        return view('Backend.videos.form', [
            'categories' => Category::query()->orderBy('title')->get(),
            'video' => null,
            'imageUrl' => null,
            'videoUrl' => null,
        ]);
    }

    public function editPage(Video $video): View
    {
        return view('Backend.videos.form', [
            'categories' => Category::query()->orderBy('title')->get(),
            'video' => $video,
            'imageUrl' => $this->publicUrl($video->image),
            'videoUrl' => $this->publicUrl($video->video),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $videos = Video::query()->with('category')->latest();

        return DataTables::eloquent($videos)
            ->addColumn('category_title', function (Video $video) {
                return $video->category?->title ?? '-';
            })
            ->addColumn('image_preview', function (Video $video) {
                if (empty($video->image)) {
                    return '-';
                }

                $url = $this->publicUrl($video->image);
                if (empty($url)) {
                    return '-';
                }

                return '<img src="'.$url.'" alt="image" style="height:40px;width:60px;object-fit:cover;border-radius:4px;" />';
            })
            ->addColumn('video_link', function (Video $video) {
                if (empty($video->video)) {
                    return '-';
                }

                $url = $this->publicUrl($video->video);
                if (empty($url)) {
                    return '-';
                }

                return '<a class="btn btn-sm btn-outline-primary" href="'.$url.'" target="_blank" rel="noopener">View</a>';
            })
            ->addColumn('description_preview', function (Video $video) {
                return str((string) $video->description)->stripTags()->limit(80)->toString();
            })
            ->addColumn('action', function (Video $video) {
                return '<div class="d-flex gap-1">'
                    .'<a class="btn btn-sm btn-primary" href="'.route('admin.videos.edit-page', $video, false).'">Edit</a>'
                    .'<button type="button" class="btn btn-sm btn-danger js-delete-video" data-id="'.$video->id.'">Delete</button>'
                    .'</div>';
            })
            ->rawColumns(['image_preview', 'video_link', 'action'])
            ->toJson();
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            // NOTE: Laravel `max` is in kilobytes. 250MB = 256000 KB.
            'video' => ['nullable', 'file', 'mimes:mp4,mov,avi,wmv,webm,mkv', 'max:256000'],
        ], [
            'video.max' => 'Video size must be 250 MB or less.',
            'video.mimes' => 'Video type must be: mp4, mov, avi, wmv, webm, mkv.',
            'video.uploaded' => 'Video upload failed. Please check the file size and try again.',
        ]);

        $payload = [
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'image' => null,
            'video' => null,
        ];

        if ($request->hasFile('image')) {
            $payload['image'] = $this->storeUploadedFile(
                $request->file('image'),
                'uploads/videos/images',
                'img',
                'image'
            );
        }

        if ($request->hasFile('video')) {
            $payload['video'] = $this->storeUploadedFile(
                $request->file('video'),
                'uploads/videos/files',
                'vid',
                'video'
            );
        }

        $video = Video::query()->create($payload);

        if ($request->expectsJson()) {
            return response()->json([
                'status' => true,
                'message' => 'Video created successfully.',
                'data' => [
                    'id' => $video->id,
                ],
            ]);
        }

        return redirect()
            ->route('admin.videos.index')
            ->with('success', 'Video created successfully.');
    }

    public function edit(Video $video): JsonResponse
    {
        return response()->json([
            'status' => true,
            'data' => [
                'id' => $video->id,
                'category_id' => $video->category_id,
                'title' => $video->title,
                'description' => $video->description,
                'image' => $video->image,
                'image_url' => $this->publicUrl($video->image),
                'video' => $video->video,
                'video_url' => $this->publicUrl($video->video),
            ],
        ]);
    }

    public function update(Request $request, Video $video): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            // NOTE: Laravel `max` is in kilobytes. 250MB = 256000 KB.
            'video' => ['nullable', 'file', 'mimes:mp4,mov,avi,wmv,webm,mkv', 'max:256000'],
        ], [
            'video.max' => 'Video size must be 250 MB or less.',
            'video.mimes' => 'Video type must be: mp4, mov, avi, wmv, webm, mkv.',
            'video.uploaded' => 'Video upload failed. Please check the file size and try again.',
        ]);

        $payload = [
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
        ];

        if ($request->hasFile('image')) {
            $this->deleteStoredFileIfExists($video->image);
            $payload['image'] = $this->storeUploadedFile(
                $request->file('image'),
                'uploads/videos/images',
                'img',
                'image'
            );
        }

        if ($request->hasFile('video')) {
            $this->deleteStoredFileIfExists($video->video);
            $payload['video'] = $this->storeUploadedFile(
                $request->file('video'),
                'uploads/videos/files',
                'vid',
                'video'
            );
        }

        $video->update($payload);

        if ($request->expectsJson()) {
            return response()->json([
                'status' => true,
                'message' => 'Video updated successfully.',
            ]);
        }

        return redirect()
            ->route('admin.videos.index')
            ->with('success', 'Video updated successfully.');
    }

    public function destroy(Request $request, Video $video): JsonResponse|RedirectResponse
    {
        $this->deleteStoredFileIfExists($video->image);
        $this->deleteStoredFileIfExists($video->video);

        $video->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'status' => true,
                'message' => 'Video deleted successfully.',
            ]);
        }

        return redirect()
            ->route('admin.videos.index')
            ->with('success', 'Video deleted successfully.');
    }

    private function storeUploadedFile(UploadedFile $file, string $path, string $prefix, string $fieldName): string
    {
        $filename = $prefix.'_'.Str::uuid()->toString().'.'.$file->getClientOriginalExtension();

        $diskName = $this->uploadDisk();
        $disk = Storage::disk($diskName);

        if ($diskName === 'rustfs') {
            $this->assertRustFsReachable($fieldName);
        }

        if (in_array($diskName, ['s3', 'rustfs'], true)) {
            $missing = [];
            $config = (array) config('filesystems.disks.'.$diskName, []);

            if (empty($config['bucket'] ?? null)) {
                $missing[] = $diskName === 's3' ? 'AWS_BUCKET' : 'RUSTFS_BUCKET';
            }
            if (empty($config['region'] ?? null)) {
                $missing[] = $diskName === 's3' ? 'AWS_DEFAULT_REGION' : 'RUSTFS_DEFAULT_REGION';
            }
            if (empty($config['key'] ?? null)) {
                $missing[] = $diskName === 's3' ? 'AWS_ACCESS_KEY_ID' : 'RUSTFS_ACCESS_KEY_ID';
            }
            if (empty($config['secret'] ?? null)) {
                $missing[] = $diskName === 's3' ? 'AWS_SECRET_ACCESS_KEY' : 'RUSTFS_SECRET_ACCESS_KEY';
            }

            if ($diskName === 'rustfs' && empty($config['endpoint'] ?? null)) {
                $missing[] = 'RUSTFS_ENDPOINT';
            }

            if (! empty($missing)) {
                throw ValidationException::withMessages([
                    $fieldName => [strtoupper($diskName).' is not configured. Please set: '.implode(', ', $missing).' in your .env'],
                ]);
            }
        }

        if (method_exists($disk, 'throw')) {
            $disk = call_user_func([$disk, 'throw']);
        }

        try {

        
            $storedPath = $disk->putFileAs(
                trim($path, '/'),
                $file,
                $filename
            );
        } catch (Throwable $e) {
            throw ValidationException::withMessages([
                $fieldName => ['Upload failed on '.$diskName.': '.$e->getMessage()],
            ]);
        }

        if (empty($storedPath)) {
            throw ValidationException::withMessages([
                $fieldName => ['Upload failed on '.$diskName.'. Please check endpoint/bucket/keys/permissions and try again.'],
            ]);
        }

        return $storedPath;
    }

    private function assertRustFsReachable(string $fieldName): void
    {
        $endpoint = (string) config('filesystems.disks.rustfs.endpoint');
        if ($endpoint === '') {
            return;
        }

        $parts = parse_url($endpoint);
        if (! is_array($parts) || empty($parts['host'])) {
            return;
        }

        $host = (string) $parts['host'];
        $scheme = (string) ($parts['scheme'] ?? 'http');
        $port = (int) ($parts['port'] ?? ($scheme === 'https' ? 443 : 80));

        $socket = @fsockopen($host, $port, $errno, $errstr, 1.5);
        if (is_resource($socket)) {
            fclose($socket);
            return;
        }

        $details = trim($errstr) !== '' ? $errstr : ('errno '.$errno);
        throw ValidationException::withMessages([
            $fieldName => ['RustFS is offline at '.$endpoint.'. Start RustFS and ensure the port is free. ('.$details.')'],
        ]);
    }

    private function deleteStoredFileIfExists(?string $path): void
    {
        if (empty($path)) {
            return;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return;
        }

        Storage::disk($this->uploadDisk())->delete($path);
    }

    private function publicUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        $diskName = $this->uploadDisk();
        if ($diskName === 'public') {
            return url('storage/'.ltrim($path, '/'));
        }

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk($diskName);

        if (method_exists($disk, 'temporaryUrl')) {
            try {
                return $disk->temporaryUrl($path, now()->addMinutes(10));
            } catch (Throwable) {
                // Fall back to the public URL if signed URL generation fails.
            }
        }

        return $disk->url($path);
    }
}
