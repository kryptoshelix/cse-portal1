@extends('layouts.public')
@section('title', 'Login')
@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
      <div class="card shadow-sm border-0 auth-card">
        <div class="card-body p-4 p-md-5">
          <h1 class="h4 fw-bold text-center mb-1">Welcome back</h1>
          <p class="text-muted text-center small mb-4">Sign in to the student or faculty portal.</p>
          @include('partials.alerts')
          <form method="POST" action="{{ route('login.attempt') }}" novalidate>
            @csrf
            <div class="mb-3">
              <label for="email" class="form-label">Email address</label>
              <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autocomplete="username" autofocus>
              @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required autocomplete="current-password">
              @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-check mb-3">
              <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1">
              <label class="form-check-label" for="remember">Remember me on this device</label>
            </div>
            <button type="submit" class="btn btn-cse w-100 py-2">Sign in</button>
          </form>
          <div class="d-flex justify-content-between mt-3 small">
            <a href="{{ route('password.request') }}">Forgot password?</a>
            <a href="{{ route('register') }}">Create an account</a>
          </div>
        </div>
      </div>
      <p class="text-center text-muted small mt-3"><i class="bi bi-lock-shield me-1"></i>New accounts require administrator approval before first login.</p>
    </div>
  </div>
</div>
@endsection
