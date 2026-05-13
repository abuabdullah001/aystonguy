@extends('Backend.Layouts.Dashboard.master')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row g-4">
        <div class="col-12 col-xl-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <img
                            id="profile-preview"
                            src="{{ $user->profile_image ? asset($user->profile_image) : asset('property/assets/img/avatars/1.png') }}"
                            alt="Profile Image"
                            class="rounded-circle"
                            style="width: 110px; height: 110px; object-fit: cover;"
                        >
                    </div>
                    <h5 class="mb-1">{{ $user->name }} {{ $user->last_name }}</h5>
                    <p class="text-muted mb-2">{{ $user->email }}</p>
                    <span class="badge bg-label-primary">{{ $user->roles->first()->name ?? 'User' }}</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">My Profile</h5>
                </div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success">{{ session('status') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label">First Name</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $user->last_name) }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Address</label>
                                <input type="text" name="address" class="form-control" value="{{ old('address', $user->address) }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Profile Image</label>
                                <input type="file" name="profile_image" id="profile_image_input" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('profile_image_input');
    const preview = document.getElementById('profile-preview');

    if (!input || !preview) {
        return;
    }

    input.addEventListener('change', function (event) {
        const file = event.target.files && event.target.files[0];
        if (!file) {
            return;
        }

        const objectUrl = URL.createObjectURL(file);
        preview.src = objectUrl;
    });
});
</script>
@endpush
