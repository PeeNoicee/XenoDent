<nav x-data="{ open: false }" class="dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('homepage') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <!-- Navigation Links (Hidden on smaller screens) -->
                <div class="hidden space-x-8 sm:flex ml-10">
                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-gray-800 dark:text-gray-200" style="color: rgb(156, 163, 175);">
                    {{ __('Back to Dashboard') }}
                </x-nav-link>

                <!-- Actions Dropdown -->
                <div class="relative ml-4 flex justify-center items-center" style="flex: 1;">
                    <button id="navbarDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="color:rgb(156, 163, 175);">
                        Actions
                    </button>
                    <ul class="dropdown-menu absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-gray-800 ring-1 ring-black ring-opacity-5 z-20 hidden group-hover:block" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="#" style="color: rgb(156, 163, 175);">Analytics</a></li>
                        <li><hr class="dropdown-divider bg-secondary"></li>
                        <li><a class="dropdown-item" href="{{ url('xrayPage') }}" style="color: rgb(156, 163, 175);">X-ray AI</a></li>
                        <li><hr class="dropdown-divider bg-secondary"></li>
                        @if($prem === 0)
                            <li><a class="dropdown-item disabled text-muted" href="#">For Premium user</a></li>
                        @else
                            <li><a class="dropdown-item" href="#" style="color: rgb(156, 163, 175);">Feature Request</a></li>
                        @endif
                    </ul>
                </div>
                </div>
            </div>

            <!-- Hamburger Icon for Small Screens -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <!-- Hamburger icon (bars) -->
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <!-- Close icon (X) -->
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu (Hidden by default, displayed when `open` is true) -->
    <div :class="{'block': open, 'hidden': ! open}" class="sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <!-- Dashboard Link -->
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-gray-800 dark:text-gray-200">
                {{ __('Back to Dashboard') }}
            </x-responsive-nav-link>

            <!-- Actions Dropdown in the Mobile Menu -->
            <div class="relative ml-4">
                <button class="text-light w-full text-left text-gray-800 dark:text-gray-200" id="navbarDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    Actions
                </button>
                <ul class="dropdown-menu mt-2 w-full rounded-md shadow-lg bg-gray-800 ring-1 ring-black ring-opacity-5 z-20" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item text-gray-800 dark:text-gray-200" href="#">Analytics</a></li>
                    <li><hr class="dropdown-divider bg-secondary"></li>
                    <li><a class="dropdown-item text-gray-800 dark:text-gray-200" href="{{ url('xrayPage') }}">X-ray AI</a></li>
                    <li><hr class="dropdown-divider bg-secondary"></li>
                    @if($prem === 0)
                        <li><a class="dropdown-item disabled text-muted" href="#">For Premium user</a></li>
                    @else
                        <li><a class="dropdown-item text-gray-800 dark:text-gray-200" href="#">Feature Request</a></li>
                    @endif
                </ul>
            </div>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" class="text-gray-800 dark:text-gray-200">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();" class="text-gray-800 dark:text-gray-200">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
