@extends('layouts.app')

@section('content')
    <div class="dashboard-container">
        <h1>Dashboard</h1>

        <div class="welcome-box">
            <p>Welcome, <strong>{{ auth()->user()->name }}</strong>!</p>
            <p>Your role: {{ auth()->user()->role }}</p>
            <p>Reputation: {{ auth()->user()->reputation }}</p>
        </div>

        <!-- Live clock and uptime -->
        <div class="clock-box">
            <p>Current Time: <span id="live-clock">--:--:--</span></p>
            <p>Uptime: <span id="uptime">--:--:--</span></p>
        </div>

        <p>This is your dashboard page.</p>
    </div>
@endsection
