@extends('layouts.public')
@section('title', 'Reset password')
@section('content')
<div class="container py-5">
  <div class="row justify-content-center"><div class="col-md-6">
    <div class="card shadow-sm border-0"><div class="card-body p-4 p-md-5">
      <h1 class="h4 fw-bold mb-3">Choose a new password</h1>
      @include('partials.alerts')
      <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $email ?? '') }}" required readonly>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">New password</label>
          <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required autocomplete="new-password">
          @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
          <label for="password_confirmation" class="form-label">Confirm new password</label>
          <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
        </div>
        <button class="btn btn-cse w-100">Reset password</button>
      </form>
    </div></div>
  </div></div>
</div>
@endsection
