<nav
    class="layout-navbar container-xxl navbar-detached navbar navbar-expand-xl align-items-center bg-navbar-theme"
    id="layout-navbar">
    @php
        $authUser = auth()->user();
        $avatarUrl = $authUser && $authUser->profile_image
            ? asset($authUser->profile_image)
            : $assetPath.'/assets/img/avatars/1.png';
        $roleName = $authUser?->roles?->first()?->name ?? 'User';
    @endphp

    <div class="navbar-nav-right d-flex align-items-center justify-content-end" id="navbar-collapse">
        <!-- Search -->
        <div class="navbar-nav align-items-center me-auto">
            <div class="nav-item d-flex align-items-center">
                <span class="w-px-22 h-px-22"><i class="icon-base bx bx-search icon-md"></i></span>
            </div>
        </div>
        <!-- /Search -->

        <ul class="navbar-nav flex-row align-items-center ms-md-auto">
             <li class="nav-item me-2">
                <button type="button" class="btn btn-sm d-inline-flex align-items-center layout-menu-toggle sidebar-toggle-btn" aria-label="Toggle sidebar">
                    <i class="icon-base bx bx-menu-alt-left icon-md" aria-hidden="true"></i>
                    <span class="menu-line-fallback ms-1" aria-hidden="true">≡</span>
                </button>
            </li>

            <!-- Dark Mode Toggle -->
            <li class="nav-item me-3 d-flex align-items-center">
                <div class="form-check form-switch m-0">
                    <input class="form-check-input" type="checkbox" id="darkModeToggle" />
                    <label class="form-check-label small mb-0 ms-2" for="darkModeToggle">Dark Mode</label>
                </div>
            </li>

            <!-- User -->
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                        <img src="{{ $avatarUrl }}" alt class="w-px-40 h-auto rounded-circle" style="object-fit: cover;" />
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.profile') }}">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-online">
                                        <img src="{{ $avatarUrl }}" alt class="w-px-40 h-auto rounded-circle" style="object-fit: cover;" />
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ $authUser->name ?? 'User' }}</h6>
                                    <small class="text-body-secondary">{{ ucfirst($roleName) }}</small>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li><div class="dropdown-divider my-1"></div></li>
                    <li><a class="dropdown-item" href="{{ route('admin.profile') }}"><i class="icon-base bx bx-user icon-md me-3"></i><span>My Profile</span></a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.settings.admin') }}"><i class="icon-base bx bx-cog icon-md me-3"></i><span>Settings</span></a></li>
                    <li><div class="dropdown-divider my-1"></div></li>
                    <li>
                        <a class="dropdown-item" href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                           <i class="icon-base bx bx-power-off icon-md me-3"></i><span>Log Out</span>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                </ul>
            </li>
            <!--/ User -->
        </ul>
    </div>
</nav>

<!-- Dark Mode JS (add at the bottom of page or in main.js) -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const toggle = document.getElementById('darkModeToggle');
    const htmlEl = document.documentElement;
    const sidebarToggleButtons = document.querySelectorAll('.layout-menu-toggle');
    const overlay = document.querySelector('.layout-overlay');

    // Apply saved theme
    if (localStorage.getItem('theme') === 'dark') {
        htmlEl.classList.add('dark-layout');
        toggle.checked = true;
    }

    toggle.addEventListener('change', function() {
        if (this.checked) {
            htmlEl.classList.add('dark-layout');
            localStorage.setItem('theme', 'dark');
        } else {
            htmlEl.classList.remove('dark-layout');
            localStorage.setItem('theme', 'light');
        }
    });

    // Fallback sidebar open/close handler
    sidebarToggleButtons.forEach(function (btn) {
        btn.addEventListener('click', function (event) {
            event.preventDefault();

            if (window.innerWidth < 1200) {
                htmlEl.classList.toggle('layout-menu-expanded');
            } else {
                htmlEl.classList.toggle('layout-menu-collapsed');
            }
        });
    });

    if (overlay) {
        overlay.addEventListener('click', function () {
            htmlEl.classList.remove('layout-menu-expanded');
        });
    }
});
</script>

<!-- Dark Mode CSS -->
<style>
/* Smooth transition */
body, .layout-menu, .layout-navbar, .card {
    transition: background-color 0.3s ease, color 0.3s ease;
}

.sidebar-toggle-btn {
    min-width: 82px;
    color: #ea580c !important;
    border: 1px solid #ea580c !important;
    background-color: #ffffff !important;
}

.sidebar-toggle-btn:hover,
.sidebar-toggle-btn:focus {
    color: #ffffff !important;
    background-color: #ea580c !important;
    border-color: #ea580c !important;
}

.menu-line-fallback {
    font-size: 1rem;
    line-height: 1;
    font-weight: 700;
}

@media (max-width: 767.98px) {
    .sidebar-toggle-btn {
        min-width: auto;
        padding: 0.35rem 0.5rem;
    }
}
</style>
