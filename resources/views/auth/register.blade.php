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

    <title>Register - Dashboard</title>

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

    <style>
        :root {
            --sea-deep: #9a3412;
            --sea-main: #ea580c;
            --sea-aqua: #fb923c;
            --sea-foam: #fda4af;
            --sea-sand: #fde68a;
            --sea-ink: #4a1d0b;
            --bs-primary: var(--sea-main);
            --bs-primary-rgb: 234, 88, 12;
        }

        body {
            background: linear-gradient(180deg, #fff7ed 0%, #fff1f2 45%, #fef3c7 100%);
        }

        .btn-primary {
            background-color: var(--sea-main) !important;
            border-color: var(--sea-main) !important;
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background-color: var(--sea-deep) !important;
            border-color: var(--sea-deep) !important;
        }

        .btn-outline-primary {
            color: var(--sea-main) !important;
            border-color: var(--sea-main) !important;
        }

        .btn-outline-primary:hover,
        .btn-outline-primary:focus {
            background-color: var(--sea-main) !important;
            border-color: var(--sea-main) !important;
            color: #ffffff !important;
        }

        .text-primary {
            color: var(--sea-main) !important;
        }

        a {
            color: var(--sea-main);
        }

        a:hover {
            color: var(--sea-deep);
        }
    </style>
</head>

<body>
<div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner">
            <div class="card px-sm-6 px-0">
                <div class="card-body">
                    <!-- Logo -->
                    <div class="app-brand justify-content-center mb-4">
                        <a href="{{ url('/') }}" class="app-brand-link gap-2">
                            <span class="app-brand-logo demo">
                                <span class="text-primary">
                                    <!-- SVG Logo here (keep your original) -->
                                </span>
                            </span>
                            <span class="app-brand-text demo text-heading fw-bold">Sneat</span>
                        </a>
                    </div>
                    <!-- /Logo -->

                    <h4 class="mb-1">Adventure starts here</h4>
                    <p class="mb-6">Make your app management easy and fun!</p>

                    <!-- Laravel Register Form -->
                    <form id="formAuthentication" class="mb-6" method="POST" action="{{ route('register') }}">
                        @csrf

                        <!-- Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   id="name"
                                   name="name"
                                   value="{{ old('name') }}"
                                   placeholder="Enter your name"
                                   required
                                   autofocus />
                            @error('name')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   id="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   placeholder="Enter your email"
                                   required />
                            @error('email')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-3 form-password-toggle">
                            <label class="form-label" for="password">Password</label>
                            <div class="input-group input-group-merge">
                                <input type="password"
                                       id="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       name="password"
                                       placeholder="••••••••••••"
                                       required />
                                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                            </div>
                            @error('password')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-3 form-password-toggle">
                            <label class="form-label" for="password_confirmation">Confirm Password</label>
                            <div class="input-group input-group-merge">
                                <input type="password"
                                       id="password_confirmation"
                                       class="form-control @error('password_confirmation') is-invalid @enderror"
                                       name="password_confirmation"
                                       placeholder="••••••••••••"
                                       required />
                                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                            </div>
                            @error('password_confirmation')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="mb-3">
                            <button class="btn btn-primary d-grid w-100" type="submit">Sign up</button>
                        </div>
                    </form>

                    <!-- Login Link -->
                    <p class="text-center">
                        <span>Already have an account?</span>
                        <a href="{{ route('login') }}">
                            <span>Sign in instead</span>
                        </a>
                    </p>
                </div>
            </div>
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
