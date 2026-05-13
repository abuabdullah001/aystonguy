@extends('Backend.Layouts.Dashboard.master')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Stripe Setup</h5>
        </div>
        <div class="card-body">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('admin.settings.stripe.update') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6 d-flex align-items-end">
                        <div class="form-check">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="stripe_status"
                                value="1"
                                id="stripe_status"
                                {{ old('stripe_status', $settings['stripe_status'] ?? '0') === '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="stripe_status">
                                Enable Stripe payments
                            </label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Currency</label>
                        <input
                            type="text"
                            name="stripe_currency"
                            class="form-control"
                            value="{{ old('stripe_currency', $settings['stripe_currency'] ?? 'USD') }}"
                            maxlength="10"
                            required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Publishable Key</label>
                        <input
                            type="text"
                            name="stripe_publishable_key"
                            class="form-control"
                            value="{{ old('stripe_publishable_key', $settings['stripe_publishable_key'] ?? '') }}"
                            placeholder="pk_test_...">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Secret Key</label>
                        <input
                            type="text"
                            name="stripe_secret_key"
                            class="form-control"
                            value="{{ old('stripe_secret_key', $settings['stripe_secret_key'] ?? '') }}"
                            placeholder="sk_test_...">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Webhook Secret</label>
                        <input
                            type="text"
                            name="stripe_webhook_secret"
                            class="form-control"
                            value="{{ old('stripe_webhook_secret', $settings['stripe_webhook_secret'] ?? '') }}"
                            placeholder="whsec_...">
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Save Stripe Setup</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
