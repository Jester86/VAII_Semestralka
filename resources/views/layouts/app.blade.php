<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'The Forum') }}</title>

    <!-- Bootstrap CSS and JS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom dashboard CSS -->
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body>
<!-- Navbar -->
@include('partials.navbar')

<!-- Main content wrapper -->
<div class="dashboard-wrapper">
    @yield('content')
</div>

<script src="{{ asset('js/clock.js') }}"></script>
</body>
</html>
