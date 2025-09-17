<!DOCTYPE html>
<html class="scroll-smooth" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="application-name" content="{{ config('app.name') }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>{{ $title ?? config('app.name') }}</title>

    <!-- favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/custom.css">

    <!--FontAwesome CSS-->
    <script src="https://kit.fontawesome.com/6d07745da9.js" crossorigin="anonymous"></script>

    @filamentStyles
    @vite('resources/css/app.css')
    {{-- @fluxAppearance --}}
</head>

<body>
    @include('partials.navbar')
    {{ $slot }}
    @include('partials.footer')

    <!-- Back-to-top Button start -->
    <button onclick="topFunction()" id="back-to-top"
        class="fixed hover:cursor-pointer hover:scale-105 rounded z-10 bottom-5 end-5 px-1.5 text-xl text-center bg-gradient-to-b from-[#FFCD03] to-[#ffcf0e] text-white leading-8 transition-all duration-500 block ">
        <span class="fa-solid fa-angle-up text-2xl/none"></span>
    </button>
    <!-- Back-to-top Button end -->

    @livewire('notifications')

    @filamentScripts
    @vite('resources/js/app.js')
    {{-- @fluxScripts --}}

    <script src="assets/js/custom.js" data-navigate-once></script>
</body>

</html>