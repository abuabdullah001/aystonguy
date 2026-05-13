@extends('Backend.Layouts.Dashboard.master')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">SMTP Settings</h5>
        </div>
        <div class="card-body">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('admin.settings.smtp.update') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Mailer</label>
                        <input type="text" name="mail_mailer" class="form-control" value="{{ old('mail_mailer', $settings['mail_mailer'] ?? 'smtp') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Host</label>
                        <input type="text" name="mail_host" class="form-control" value="{{ old('mail_host', $settings['mail_host'] ?? '') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Port</label>
                        <input type="number" name="mail_port" class="form-control" value="{{ old('mail_port', $settings['mail_port'] ?? 587) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Username</label>
                        <input type="text" name="mail_username" class="form-control" value="{{ old('mail_username', $settings['mail_username'] ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Password</label>
                        <input type="password" name="mail_password" class="form-control" value="{{ old('mail_password', $settings['mail_password'] ?? '') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Encryption</label>
                        <input type="text" name="mail_encryption" class="form-control" value="{{ old('mail_encryption', $settings['mail_encryption'] ?? 'tls') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">From Address</label>
                        <input type="email" name="mail_from_address" class="form-control" value="{{ old('mail_from_address', $settings['mail_from_address'] ?? '') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">From Name</label>
                        <input type="text" name="mail_from_name" class="form-control" value="{{ old('mail_from_name', $settings['mail_from_name'] ?? '') }}">
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Save SMTP Settings</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
