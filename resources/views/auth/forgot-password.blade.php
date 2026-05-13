{{--  <x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Email Password Reset Link') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>  --}}


<!doctype html>
<html
  lang="en"
  class="layout-wide customizer-hide"
  @php
    $assetPath = asset('property');
  @endphp
  data-assets-path="{{ $assetPath }}/assets/"
  data-template="vertical-menu-template-free"
>
<head>
    <meta charset="utf-8" />
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Forgot Password - Dashboard</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ $assetPath }}/assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap"
      rel="stylesheet" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ $assetPath }}/assets/vendor/css/core.css" />
    <link rel="stylesheet" href="{{ $assetPath }}/assets/css/demo.css" />
    <link rel="stylesheet" href="{{ $assetPath }}/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="{{ $assetPath }}/assets/vendor/css/pages/page-auth.css" />

    <!-- Helpers -->
    <script src="{{ $assetPath }}/assets/vendor/js/helpers.js"></script>
    <script src="{{ $assetPath }}/assets/js/config.js"></script>
</head>

<body>
<div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner">
            <!-- Forgot Password -->
            <div class="card px-sm-6 px-0">
                <div class="card-body">
                    <!-- Logo -->
                    <div class="app-brand justify-content-center mb-6">
                        <a href="{{ url('/') }}" class="app-brand-link gap-2">
                            <span class="app-brand-logo demo">
                                <span class="text-primary">
                                    <!-- Your SVG logo here -->
                                </span>
                            </span>
                            <span class="app-brand-text demo text-heading fw-bold">Sneat</span>
                        </a>
                    </div>
                    <!-- /Logo -->

                    <h4 class="mb-1">Forgot Password? 🔒</h4>
                    <p class="mb-6">
                        Enter your email and we'll send you instructions to reset your password
                    </p>

                    <!-- Laravel Password Reset Form -->
                    @if (session('status'))
                        <div class="alert alert-success mb-3">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}" class="mb-6">
                        @csrf

                        <div class="mb-6">
                            <label for="email" class="form-label">Email</label>
                            <input
                                type="email"
                                class="form-control @error('email') is-invalid @enderror"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="Enter your email"
                                required
                                autofocus
                            />
                            @error('email')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary d-grid w-100">
                            Send Reset Link
                        </button>
                    </form>

                    <div class="text-center">
                        <a href="{{ route('login') }}" class="d-flex justify-content-center">
                            <i class="icon-base bx bx-chevron-left me-1"></i>
                            Back to login
                        </a>
                    </div>
                </div>
            </div>
            <!-- /Forgot Password -->
        </div>
    </div>
</div>

<!-- Core JS -->
<script src="{{ $assetPath }}/assets/vendor/libs/jquery/jquery.js"></script>
<script src="{{ $assetPath }}/assets/vendor/libs/popper/popper.js"></script>
<script src="{{ $assetPath }}/assets/vendor/js/bootstrap.js"></script>
<script src="{{ $assetPath }}/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="{{ $assetPath }}/assets/vendor/js/menu.js"></script>
<script src="{{ $assetPath }}/assets/js/main.js"></script>
</body>
</html>
