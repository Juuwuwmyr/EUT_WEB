<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'EUT Restaurant') }}</title>
    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css?family=Nunito:400,600,700" rel="stylesheet">
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md space-y-8">
            <div>
                <img class="mx-auto h-12 w-auto" src="{{ asset('images/logo.png') }}" alt="EUT Restaurant">
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    {{ config('app.name', 'EUT Restaurant') }}
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Please verify your email address to continue.
                </p>
            </div>

            @component('layouts.partials.alert')
                @slot('title')
                    Message
                @endslot
                {{-- Alert content will be injected here --}}
            @endcomponent

            @yield('content')
        </div>
    </div>
</body>
</html>