<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – {{ config('app.name','Laravel') }}</title>

    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body>
<canvas id="matrix-canvas"></canvas>

<div class="login-box">
    <h1>The Forum</h1>
    <h2>Login</h2>

    @if ($errors->any())
        <div class="error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <label for="login">Email / User ID:</label>
        <input type="text" name="login" id="login" value="{{ old('login') }}" required autofocus>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>

        <button type="submit">Login</button>
    </form>

    <div class="note">
        Don't have an account? <a href="{{ route('register') }}">Register</a>
    </div>
</div>

<script src="{{ asset('js/matrix.js') }}"></script>
</body>
</html>
