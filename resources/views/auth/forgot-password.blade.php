@extends('layouts.public')
@section('title', 'Forgot password')
@section('content')
<div class="container py-5">
  <div class="row justify-content-center"><div class="col-md-6">
    <div class="card shadow-sm border-0"><div class="card-body p-4 p-md-5">
      <h1 class="h4 fw-bold mb-1">Reset your password</h1>
      <p class="text-muted small mb-4">Enter your registered email and we will send you a secure reset link.</p>
      @include('partials.alerts')
      <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="mb-3">
          <label for="email" class="form-label">Email address</label>
          <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autofocus>
          @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <button class="btn btn-cse w-100">Email password reset link</button>
        <p class="text-center small mt-3 mb-0"><a href="{{ route('login') }}">Back to login</a></p>
      </form>
    </div></div>
  </div></div>
</div>
@endsection
