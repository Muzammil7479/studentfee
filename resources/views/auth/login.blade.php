@extends('layouts.guest')

@section('title', 'Login - School Fee Management System')

@section('content')
<div class="auth-card">
    <div class="card-top">
        <div class="auth-logo">
            <i class="fa-solid fa-school"></i>
        </div>
        <h1>School Fee Management System</h1>
        <p class="subtitle">Sign in to continue to your dashboard</p>
    </div>

    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" novalidate>
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">Username or Email</label>
                <input
                    type="text"
                    class="form-control @error('email') is-invalid @enderror"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="admin@school.com"
                    autofocus
                    required
                >
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <input
                        type="password"
                        class="form-control @error('password') is-invalid @enderror"
                        id="password"
                        name="password"
                        placeholder="Enter your password"
                        required
                    >
                    <button class="btn btn-toggle-password" type="button" id="togglePassword" tabindex="-1" aria-label="Show password">
                        <i class="fa-solid fa-eye" id="togglePasswordIcon"></i>
                    </button>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember" name="remember">
                    <label class="form-check-label" for="remember">Remember Me</label>
                </div>
            </div>

            <button type="submit" class="btn btn-login btn-primary w-100 text-white">
                <i class="fa-solid fa-right-to-bracket me-2"></i>Login
            </button>
        </form>

        <p class="demo-hint">
            Admin demo: admin@school.com / admin123 &nbsp;·&nbsp; User demo: user@school.com / user123
        </p>
    </div>
</div>
@endsection

@section('scripts')
<script>
    (function () {
        const toggleBtn = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const icon = document.getElementById('togglePasswordIcon');

        toggleBtn.addEventListener('click', function () {
            const isHidden = passwordInput.type === 'password';
            passwordInput.type = isHidden ? 'text' : 'password';
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
            toggleBtn.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
        });
    })();
</script>
@endsection
