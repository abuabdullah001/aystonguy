@extends('Backend.Layouts.Dashboard.master')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Website Settings</h5>
        </div>
        <div class="card-body">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('admin.settings.website.update') }}" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Site Name</label>
                        <input type="text" name="site_name" class="form-control" value="{{ old('site_name', $settings['site_name'] ?? '') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tagline</label>
                        <input type="text" name="site_tagline" class="form-control" value="{{ old('site_tagline', $settings['site_tagline'] ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Support Email</label>
                        <input type="email" name="site_email" class="form-control" value="{{ old('site_email', $settings['site_email'] ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Support Phone</label>
                        <input type="text" name="site_phone" class="form-control" value="{{ old('site_phone', $settings['site_phone'] ?? '') }}">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Address</label>
                        <input type="text" name="site_address" class="form-control" value="{{ old('site_address', $settings['site_address'] ?? '') }}">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Site Logo</label>
                        <input type="file" id="site_logo" name="site_logo" class="form-control" accept=".jpg,.jpeg,.png,.webp,.svg">
                        <div class="mt-2" id="site_logo_preview_wrap" @if (empty($settings['site_logo'])) style="display: none;" @endif>
                            <img
                                id="site_logo_preview"
                                src="{{ !empty($settings['site_logo']) ? asset($settings['site_logo']) : '' }}"
                                alt="Site Logo Preview"
                                style="max-height: 60px; width: auto;"
                            >
                        </div>
                        @error('site_logo')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Save Website Settings</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const logoInput = document.getElementById('site_logo');
    const logoPreview = document.getElementById('site_logo_preview');
    const logoPreviewWrap = document.getElementById('site_logo_preview_wrap');

    if (!logoInput || !logoPreview || !logoPreviewWrap) {
        return;
    }

    logoInput.addEventListener('change', function (event) {
        const file = event.target.files && event.target.files[0];

        if (!file) {
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            logoPreview.src = e.target.result;
            logoPreviewWrap.style.display = 'block';
        };
        reader.readAsDataURL(file);
    });
});
</script>
@endpush
