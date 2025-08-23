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

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- HTMX -->
        <script src="https://unpkg.com/htmx.org@2.0.0"></script>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-50">
            @yield('content')
        </div>
        
        <!-- HTMX Config -->
        <script>
            document.body.addEventListener('htmx:configRequest', function(evt) {
                evt.detail.headers['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            });
        </script>
    </body>
</html>
