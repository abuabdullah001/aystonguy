<!-- Menu -->
@php
  $siteLogo = \App\Models\Setting::getValue('website', 'site_logo');
  $siteLogoUrl = $siteLogo
      ? ((str_starts_with($siteLogo, 'http://') || str_starts_with($siteLogo, 'https://')) ? $siteLogo : asset($siteLogo))
      : null;
@endphp
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <a href="{{ route('admin.dashboard') }}" class="app-brand-link" style="display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
      <span class="app-brand-logo demo" style="display: flex; align-items: center; justify-content: center; min-width: 40px;">
        @if ($siteLogoUrl)
          <img src="{{ $siteLogoUrl }}" alt="Site Logo" style="max-height: 32px; width: auto;" />
        @else
        <span class="text-primary">
          <svg
            width="25"
            viewBox="0 0 25 42"
            version="1.1"
            xmlns="http://www.w3.org/2000/svg"
            xmlns:xlink="http://www.w3.org/1999/xlink">
            <defs>
              <path
                d="M13.7918663,0.358365126 L3.39788168,7.44174259 C0.566865006,9.69408886 -0.379795268,12.4788597 0.557900856,15.7960551 C0.68998853,16.2305145 1.09562888,17.7872135 3.12357076,19.2293357 C3.8146334,19.7207684 5.32369333,20.3834223 7.65075054,21.2172976 L7.59773219,21.2525164 L2.63468769,24.5493413 C0.445452254,26.3002124 0.0884951797,28.5083815 1.56381646,31.1738486 C2.83770406,32.8170431 5.20850219,33.2640127 7.09180128,32.5391577 C8.347334,32.0559211 11.4559176,30.0011079 16.4175519,26.3747182 C18.0338572,24.4997857 18.6973423,22.4544883 18.4080071,20.2388261 C17.963753,17.5346866 16.1776345,15.5799961 13.0496516,14.3747546 L10.9194936,13.4715819 L18.6192054,7.984237 L13.7918663,0.358365126 Z"
                id="path-1"></path>
              <path
                d="M5.47320593,6.00457225 C4.05321814,8.216144 4.36334763,10.0722806 6.40359441,11.5729822 C8.61520715,12.571656 10.0999176,13.2171421 10.8577257,13.5094407 L15.5088241,14.433041 L18.6192054,7.984237 C15.5364148,3.11535317 13.9273018,0.573395879 13.7918663,0.358365126 C13.5790555,0.511491653 10.8061687,2.3935607 5.47320593,6.00457225 Z"
                id="path-3"></path>
              <path
                d="M7.50063644,21.2294429 L12.3234468,23.3159332 C14.1688022,24.7579751 14.397098,26.4880487 13.008334,28.506154 C11.6195701,30.5242593 10.3099883,31.790241 9.07958868,32.3040991 C5.78142938,33.4346997 4.13234973,34 4.13234973,34 C4.13234973,34 2.75489982,33.0538207 2.37032616e-14,31.1614621 C-0.55822714,27.8186216 -0.55822714,26.0572515 -4.05231404e-15,25.8773518 C0.83734071,25.6075023 2.77988457,22.8248993 3.3049379,22.52991 C3.65497346,22.3332504 5.05353963,21.8997614 7.50063644,21.2294429 Z"
                id="path-4"></path>
              <path
                d="M20.6,7.13333333 L25.6,13.8 C26.2627417,14.6836556 26.0836556,15.9372583 25.2,16.6 C24.8538077,16.8596443 24.4327404,17 24,17 L14,17 C12.8954305,17 12,16.1045695 12,15 C12,14.5672596 12.1403557,14.1461923 12.4,13.8 L17.4,7.13333333 C18.0627417,6.24967773 19.3163444,6.07059163 20.2,6.73333333 C20.3516113,6.84704183 20.4862915,6.981722 20.6,7.13333333 Z"
                id="path-5"></path>
            </defs>
            <g id="g-app-brand" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
              <g id="Brand-Logo" transform="translate(-27.000000, -15.000000)">
                <g id="Icon" transform="translate(27.000000, 15.000000)">
                  <g id="Mask" transform="translate(0.000000, 8.000000)">
                    <mask id="mask-2" fill="white">
                      <use xlink:href="#path-1"></use>
                    </mask>
                    <use fill="currentColor" xlink:href="#path-1"></use>
                    <g id="Path-3" mask="url(#mask-2)">
                      <use fill="currentColor" xlink:href="#path-3"></use>
                      <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-3"></use>
                    </g>
                    <g id="Path-4" mask="url(#mask-2)">
                      <use fill="currentColor" xlink:href="#path-4"></use>
                      <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-4"></use>
                    </g>
                  </g>
                  <g
                    id="Triangle"
                    transform="translate(19.000000, 11.000000) rotate(-300.000000) translate(-19.000000, -11.000000) ">
                    <use fill="currentColor" xlink:href="#path-5"></use>
                    <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-5"></use>
                  </g>
                </g>
              </g>
            </g>
          </svg>
        </span>
        @endif
      </span>
      <span class="app-brand-text demo menu-text fw-bold">Dashboard</span>
    </a>

    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
      <i class="bi bi-chevron-left d-block d-xl-none align-middle"></i>
    </a>
  </div>

  <div class="menu-divider mt-0"></div>
  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">
    <li class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
      <a href="{{ route('admin.dashboard') }}" class="menu-link">
        <i class="menu-icon bi bi-speedometer2"></i>
        <div class="text-truncate">Dashboard</div>
      </a>
    </li>


   <li class="menu-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
      <a href="{{ route('admin.users.index') }}" class="menu-link">
        <i class="menu-icon bi bi-people"></i>
        <div class="text-truncate">Users</div>
      </a>
    </li>

    {{-- category --}}
    <li class="menu-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
      <a href="{{ route('admin.categories.index') }}" class="menu-link">
        <i class="menu-icon bi bi-list"></i>
        <div class="text-truncate">Categories</div>
      </a>
    </li>



    {{-- video --}}
    <li class="menu-item {{ request()->routeIs('admin.videos.*') ? 'active' : '' }}">
      <a href="{{ route('admin.videos.index') }}" class="menu-link">
        <i class="menu-icon bi bi-camera-video"></i>
        <div class="text-truncate">Videos</div>
      </a>
    </li>




    <li class="menu-item {{ request()->is('admin/settings/*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon bi bi-gear"></i>
        <div class="text-truncate">Settings</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item {{ request()->routeIs('admin.settings.smtp') ? 'active' : '' }}">
          <a href="{{ route('admin.settings.smtp') }}" class="menu-link">
            <div class="text-truncate">SMTP Settings</div>
          </a>
        </li>
        <li class="menu-item {{ request()->routeIs('admin.settings.website') ? 'active' : '' }}">
          <a href="{{ route('admin.settings.website') }}" class="menu-link">
            <div class="text-truncate">Website Settings</div>
          </a>
        </li>
        <li class="menu-item {{ request()->routeIs('admin.settings.admin') ? 'active' : '' }}">
          <a href="{{ route('admin.settings.admin') }}" class="menu-link">
            <div class="text-truncate">Admin Settings</div>
          </a>
        </li>
        <li class="menu-item {{ request()->routeIs('admin.settings.stripe') ? 'active' : '' }}">
          <a href="{{ route('admin.settings.stripe') }}" class="menu-link">
            <div class="text-truncate">Stripe Setup</div>
          </a>
        </li>
        <li class="menu-item {{ request()->routeIs('admin.settings.dynamic.page*') ? 'active' : '' }}">
          <a href="{{ route('admin.settings.dynamic.page') }}" class="menu-link">
            <div class="text-truncate">Dynamic Page</div>
          </a>
        </li>
        <li class="menu-item {{ request()->routeIs('admin.settings.roles.permissions') ? 'active' : '' }}">
          <a href="{{ route('admin.settings.roles.permissions') }}" class="menu-link">
            <div class="text-truncate">Roles & Permissions</div>
          </a>
        </li>
      </ul>
    </li>


  </ul>
</aside>
<!-- / Menu -->

<style>
  @keyframes waveTopBottom {
    0% {
      background: linear-gradient(180deg, #ea580c 0%, #fb923c 25%, #fda4af 50%, #f59e0b 75%, #ea580c 100%);
    }
    25% {
      background: linear-gradient(180deg, #fb923c 0%, #fda4af 25%, #f59e0b 50%, #ea580c 75%, #fb923c 100%);
    }
    50% {
      background: linear-gradient(180deg, #fda4af 0%, #f59e0b 25%, #ea580c 50%, #fb923c 75%, #fda4af 100%);
    }
    75% {
      background: linear-gradient(180deg, #f59e0b 0%, #ea580c 25%, #fb923c 50%, #fda4af 75%, #f59e0b 100%);
    }
    100% {
      background: linear-gradient(180deg, #ea580c 0%, #fb923c 25%, #fda4af 50%, #f59e0b 75%, #ea580c 100%);
    }
  }

  #layout-menu {
    animation: waveTopBottom 16s ease-in-out infinite;
  }

  #layout-menu .menu-link,
  #layout-menu .menu-item > a,
  #layout-menu .menu-link span,
  #layout-menu .menu-text,
  #layout-menu .text-truncate {
    color: #fff !important;
  }

  #layout-menu .menu-icon {
    color: #fff !important;
  }

  @keyframes waveTopBottomDark {
    0% {
      background: linear-gradient(180deg, #7c2d12 0%, #c2410c 25%, #fb923c 50%, #be123c 75%, #7c2d12 100%);
    }
    25% {
      background: linear-gradient(180deg, #c2410c 0%, #fb923c 25%, #be123c 50%, #7c2d12 75%, #c2410c 100%);
    }
    50% {
      background: linear-gradient(180deg, #fb923c 0%, #be123c 25%, #7c2d12 50%, #c2410c 75%, #fb923c 100%);
    }
    75% {
      background: linear-gradient(180deg, #be123c 0%, #7c2d12 25%, #c2410c 50%, #fb923c 75%, #be123c 100%);
    }
    100% {
      background: linear-gradient(180deg, #7c2d12 0%, #c2410c 25%, #fb923c 50%, #be123c 75%, #7c2d12 100%);
    }
  }

  .dark-layout #layout-menu {
    animation: waveTopBottomDark 16s ease-in-out infinite;
  }

  .dark-layout #layout-menu .menu-link,
  .dark-layout #layout-menu .menu-item > a,
  .dark-layout #layout-menu .menu-link span,
  .dark-layout #layout-menu .menu-text,
  .dark-layout #layout-menu .text-truncate {
    color: #fff !important;
  }

  .dark-layout #layout-menu .menu-icon {
    color: #fff !important;
  }

  /* Active Menu Item Styling */


  #layout-menu .menu-item.active .menu-link,
  #layout-menu .menu-item.active .menu-link span,
  #layout-menu .menu-item.active .menu-link i {
    color: #fff !important;
    font-weight: bold !important;
  }



  /* Submenu Active Items */


</style>
