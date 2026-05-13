<!doctype html>

<html
  lang="en"
  class="layout-menu-fixed layout-compact"
  data-assets-path="{{ asset('property/assets/') }}/"
  data-template="vertical-menu-template-free">
  @php
    $assetPath = asset('property');
 @endphp
 @include('Backend.partial.style')
   @stack('styles')

  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
       @include('Backend.partial.sidebar')

        <!-- Layout container -->
        <div class="layout-page">
          <!-- Navbar -->

      @include('Backend.partial.header')

          <!-- / Navbar -->

          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->


            @yield('content')
            <!-- / Content -->

            <!-- Footer -->
           @include('Backend.partial.footer')
            <!-- / Footer -->

            <div class="content-backdrop fade"></div>
          </div>
          <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
      </div>

      <!-- Overlay -->
      <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->

    <style>
      :root {
        --sea-deep: #9a3412;
        --sea-main: #ea580c;
        --sea-aqua: #fb923c;
        --sea-foam: #fda4af;
        --sea-sand: #fde68a;
        --sea-ink: #4a1d0b;
      }

      @keyframes seaWave {
        0% {
          background-position: 0% 50%;
        }
        50% {
          background-position: 100% 50%;
        }
        100% {
          background-position: 0% 50%;
        }
      }

      @keyframes shoreWave {
        0% {
          background-position: 0% 50%;
        }
        50% {
          background-position: 100% 50%;
        }
        100% {
          background-position: 0% 50%;
        }
      }

      body {
        background: linear-gradient(180deg, #fff7ed 0%, #fff1f2 45%, #fef3c7 100%);
      }

      .layout-page {
        background: transparent;
      }

      .content-wrapper {
        background: transparent;
      }

      .layout-navbar {
        background: linear-gradient(90deg, var(--sea-main) 0%, var(--sea-aqua) 55%, var(--sea-foam) 100%) !important;
      }

      .layout-navbar .navbar-nav .nav-link,
      .layout-navbar .icon-base,
      .layout-navbar .form-check-label {
        color: #ffffff !important;
      }

      .card {
        border: 1px solid rgba(234, 88, 12, 0.16) !important;
        box-shadow: 0 10px 24px rgba(194, 65, 12, 0.08);
      }

      .card-header {
        background: linear-gradient(90deg, rgba(251, 146, 60, 0.16) 0%, rgba(253, 164, 175, 0.2) 100%);
        border-bottom: 1px solid rgba(234, 88, 12, 0.14) !important;
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

      .page-link {
        color: var(--sea-main);
      }

      .page-item.active .page-link {
        background-color: var(--sea-main);
        border-color: var(--sea-main);
      }

      .table thead th {
        background: rgba(251, 146, 60, 0.14);
        color: var(--sea-ink);
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

      .content-footer {
        background: linear-gradient(90deg, rgba(251, 146, 60, 0.16) 0%, rgba(253, 164, 175, 0.2) 55%, rgba(254, 230, 138, 0.24) 100%);
        border-top: 1px solid rgba(234, 88, 12, 0.16);
      }

      .content-footer,
      .content-footer a {
        color: var(--sea-ink) !important;
      }

      .dark-layout body {
        background: linear-gradient(180deg, #431407 0%, #7c2d12 45%, #9a3412 100%) !important;
      }

      .dark-layout .layout-navbar {
        background: linear-gradient(90deg, #9a3412 0%, #ea580c 55%, #fb923c 100%) !important;
      }

      .dark-layout .card {
        background-color: #4a1d0b !important;
        border-color: rgba(253, 186, 116, 0.22) !important;
        color: #fff3e0 !important;
      }

      .dark-layout .card-header {
        background: rgba(251, 146, 60, 0.2) !important;
      }

      .dark-layout .content-footer {
        background: rgba(122, 45, 18, 0.85) !important;
        border-top-color: rgba(253, 186, 116, 0.24) !important;
      }

      .dark-layout .content-footer,
      .dark-layout .content-footer a {
        color: #fff3e0 !important;
      }
    </style>

    @include('Backend.partial.js')
    @stack('scripts')
  </body>
</html>
