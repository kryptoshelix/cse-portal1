<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Home') — CSE Department Portal</title>
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/portal.css') }}">
    @stack('styles')
</head>
<body @yield('bodyattrs')>
<a class="skip-link" href="#main-content">Skip to main content</a>
@yield('body')
<script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
@stack('scripts')
</body>
</html>
