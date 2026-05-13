@extends('Backend.Layouts.Dashboard.master')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs5.min.css">
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ $video ? 'Edit Video' : 'Create Video' }}</h5>
                <a href="{{ route('admin.videos.index') }}" class="btn btn-outline-secondary">Back</a>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ $video ? route('admin.videos.update', $video) : route('admin.videos.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select class="form-select" name="category_id" required>
                            <option value="">Select category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id', $video?->category_id) == $category->id)>
                                    {{ $category->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control" name="title" value="{{ old('title', $video?->title) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control js-summernote" name="description" rows="6">{{ old('description', $video?->description) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Image</label>
                        <input type="file" class="form-control" name="image" accept="image/*">
                        @if (!empty($imageUrl))
                            <div class="mt-2">
                                <a href="{{ $imageUrl }}" target="_blank" rel="noopener">Current image</a>
                            </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Video</label>
                        <input type="file" class="form-control" name="video" accept="video/*">
                        @if (!empty($videoUrl))
                            <div class="mt-2">
                                <a href="{{ $videoUrl }}" target="_blank" rel="noopener">Current video</a>
                            </div>
                        @endif
                    </div>

                    <div class="d-grid d-sm-flex gap-2">
                        <button type="submit" class="btn btn-primary">{{ $video ? 'Update Video' : 'Create Video' }}</button>
                        <a href="{{ route('admin.videos.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs5.min.js"></script>
    <script>
        $(function() {
            $('.js-summernote').summernote({
                height: 200,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link']],
                    ['view', ['codeview']]
                ]
            });
        });
    </script>
@endpush
