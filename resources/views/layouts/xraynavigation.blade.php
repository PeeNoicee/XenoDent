<div class="col" style="margin-top: 10px">
    <nav class="navbar navbar-expand-lg" style="background-color: #1F2937;">
        <div class="container-fluid">
            <!-- Logo and Link -->
            <a href="{{ route('dashboard') }}">
                <x-application-logo class="block h-9 w-auto fill-current text-gray-100" />
            </a>

            <!-- Toggler Button (for mobile view) -->
            <button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navbar Content -->
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Nav Links -->
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link text-light {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            Back to Dashboard
                        </a>
                    </li>
                    <!-- <li class="nav-item">
                        <a class="nav-link text-light {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">
                            About XenoDent
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-light {{ request()->routeIs('how-to-use') ? 'active' : '' }}" href="{{ route('how-to-use') }}">
                            How to Use
                        </a>
                    </li> -->

                    <!-- Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-light" href="#" id="navbarDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                            Actions
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown" style="background-color: #1F2937;">
                            <li><a class="dropdown-item text-light" href="#">Analytics</a></li>
                            <li><hr class="dropdown-divider bg-secondary"></li>
                            <li><a class="dropdown-item text-light" href="#">X-ray AI</a></li>
                            <li><hr class="dropdown-divider bg-secondary"></li>
                            @if($prem === 0)
                                <li><a class="dropdown-item disabled text-muted" href="#">For Premium user</a></li>
                            @else
                                <li><a class="dropdown-item text-light" href="#">Feature Request</a></li>
                            @endif
                        </ul>
                    </li>
                </ul>

                <!-- Right Side Button -->
                @if($prem === 0)
                    <div class="d-flex">
                        <a href="{{ route('premiumPage') }}">
                            <button class="btn btn-outline-success">Go PREMIUM</button>
                        </a>
                    </div>
                @else
                    <div class="d-flex">
                        <button class="btn btn-outline-success">Need Support?</button>
                    </div>
                @endif
            </div>
        </div>
    </nav>
</div>
