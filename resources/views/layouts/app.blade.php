<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">-->

        <!-- Scripts -->
        @if(app()->environment('local'))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            @php
                $manifestPath = public_path('build/manifest.json');
                $cssFile = null;
                $jsFile = null;
                $debug = [];

                // Debug: Check what files exist
                $debug['manifest_exists'] = file_exists($manifestPath);
                $debug['build_dir_exists'] = is_dir(public_path('build'));
                $debug['assets_dir_exists'] = is_dir(public_path('build/assets'));

                // Try to read from manifest first
                if (file_exists($manifestPath)) {
                    $manifest = json_decode(file_get_contents($manifestPath), true);
                    $debug['manifest_keys'] = array_keys($manifest);
                    if (isset($manifest['resources/css/app.css']['file'])) {
                        $cssFile = 'build/' . $manifest['resources/css/app.css']['file'];
                        $debug['css_from_manifest'] = $cssFile;
                        $debug['css_file_exists'] = file_exists(public_path($cssFile));
                    }
                    if (isset($manifest['resources/js/app.js']['file'])) {
                        $jsFile = 'build/' . $manifest['resources/js/app.js']['file'];
                        $debug['js_from_manifest'] = $jsFile;
                        $debug['js_file_exists'] = file_exists(public_path($jsFile));
                    }
                }

                // Fallback: scan build directory for CSS and JS files
                if (!$cssFile || !$jsFile) {
                    $buildDir = public_path('build/assets');
                    if (is_dir($buildDir)) {
                        $files = array_diff(scandir($buildDir), ['.', '..']);
                        $debug['found_files'] = $files;
                        foreach ($files as $file) {
                            if (!$cssFile && pathinfo($file, PATHINFO_EXTENSION) === 'css') {
                                $cssFile = 'build/assets/' . $file;
                                $debug['css_from_scan'] = $cssFile;
                            }
                            if (!$jsFile && pathinfo($file, PATHINFO_EXTENSION) === 'js') {
                                $jsFile = 'build/assets/' . $file;
                                $debug['js_from_scan'] = $jsFile;
                            }
                        }
                    }
                }

                $debug['final_css'] = $cssFile;
                $debug['final_js'] = $jsFile;
            @endphp

            @if($cssFile)
                <link rel="stylesheet" href="{{ secure_asset($cssFile) }}">
            @endif

            @if($jsFile)
                <script src="{{ secure_asset($jsFile) }}" defer></script>
            @endif
        @endif

    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>


       <!-- <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
        -->

        <footer class="bg-gray-800 text-white py-6 mt-12">
            <div class="max-w-7xl mx-auto px-4 text-center">
                <p>&copy; {{ date('Y') }} {{ config('app.name', 'XenoDent') }}. All rights reserved.</p>
                <p>Powered by <a href="https://laravel.com" target="_blank" class="text-blue-400">Laravel</a></p>
            </div>
        </footer>

    </body>
</html>
