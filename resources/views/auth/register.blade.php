<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register – {{ config('app.name','Laravel') }}</title>

    <!-- Link the same retro CSS -->
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body>
<canvas id="matrix-canvas"></canvas>

<div class="login-box">
    <h1>The Forum</h1>
    <h2>Register</h2>

    @if ($errors->any())
        <div class="error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <label for="name">Username:</label>
        <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" value="{{ old('email') }}" required>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>

        <label for="password_confirmation">Confirm Password:</label>
        <input type="password" name="password_confirmation" id="password_confirmation" required>

        <button type="submit">Register</button>
    </form>

    <div class="note">
        Already have an account? <a href="{{ route('login') }}">Login</a>
    </div>
</div>
<script src="{{ asset('js/matrix.js') }}"></script>
</body>
</html>
