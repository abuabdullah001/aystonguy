@extends('Backend.Layouts.Dashboard.master')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Admin Settings</h5>
        </div>
        <div class="card-body">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('admin.settings.admin.update') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Timezone</label>
                        <input type="text" name="timezone" class="form-control" value="{{ old('timezone', $settings['timezone'] ?? config('app.timezone')) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Date Format</label>
                        <input type="text" name="date_format" class="form-control" value="{{ old('date_format', $settings['date_format'] ?? 'Y-m-d') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Items Per Page</label>
                        <input type="number" name="items_per_page" class="form-control" value="{{ old('items_per_page', $settings['items_per_page'] ?? 15) }}" required>
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="maintenance_mode" value="1" id="maintenance_mode" {{ old('maintenance_mode', $settings['maintenance_mode'] ?? '0') === '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="maintenance_mode">
                                Enable maintenance mode flag
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Save Admin Settings</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
