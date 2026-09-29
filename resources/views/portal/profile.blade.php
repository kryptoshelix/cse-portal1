@extends('layouts.portal')
@section('title','My Profile')
@php $portal = $portal ?? 'student'; @endphp
@section('content')
<h1 class="h4 fw-bold mb-4">Profile &amp; settings</h1>
<div class="row g-4">
  <div class="col-lg-7">
    <form method="POST" action="{{ route($portal.'.profile.update') }}" class="card border-0 shadow-sm">
      @csrf @method('PUT')
      <div class="card-header bg-white fw-semibold">Personal details</div>
      <div class="card-body row g-3">
        <div class="col-md-6">
          <label for="name" class="form-label">Full name</label>
          <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', auth()->user()->name) }}" required>
          @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label">Email (sign-in identity)</label>
          <input type="email" class="form-control" value="{{ auth()->user()->email }}" disabled>
          <div class="form-text">Contact the office to change your registered email.</div>
        </div>
        <div class="col-md-6">
          <label for="phone" class="form-label">Phone</label>
          <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', auth()->user()->phone) }}">
        </div>
        <div class="col-md-6">
          <label for="address" class="form-label">Address</label>
          <input type="text" class="form-control" id="address" name="address" value="{{ old('address', auth()->user()->address) }}">
        </div>
        @if($portal === 'student')
          <div class="col-md-6">
            <label for="program" class="form-label">Program</label>
            <input type="text" class="form-control" id="program" name="program" value="{{ old('program', $profile?->program) }}">
          </div>
          <div class="col-md-6">
            <label for="batch" class="form-label">Batch year</label>
            <input type="number" min="1950" max="2100" class="form-control @error('batch') is-invalid @enderror" id="batch" name="batch" value="{{ old('batch', $profile?->batch) }}">
            @error('batch')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-12 form-check ms-3">
            <input class="form-check-input" type="checkbox" value="1" id="public_display_consent" name="public_display_consent" @checked(old('public_display_consent', $profile?->public_display_consent))>
            <label class="form-check-label" for="public_display_consent">Allow my name, roll number and program to appear next to published achievements on the public website.</label>
          </div>
        @else
          <div class="col-md-6"><label for="designation" class="form-label">Designation</label>
            <input type="text" class="form-control" id="designation" name="designation" value="{{ old('designation', $profile?->designation) }}"></div>
          <div class="col-md-6"><label for="specialization" class="form-label">Specialization</label>
            <input type="text" class="form-control" id="specialization" name="specialization" value="{{ old('specialization', $profile?->specialization) }}"></div>
          <div class="col-md-6"><label for="qualification" class="form-label">Highest qualification</label>
            <input type="text" class="form-control" id="qualification" name="qualification" value="{{ old('qualification', $profile?->qualification) }}"></div>
          <div class="col-12"><label for="bio" class="form-label">Short bio (shown in the public directory if you consent there)</label>
            <textarea class="form-control" id="bio" name="bio" rows="3">{{ old('bio', $profile?->bio) }}</textarea></div>
        @endif
      </div>
      <div class="card-footer bg-white text-end"><button class="btn btn-cse">Save profile</button></div>
    </form>
  </div>
  <div class="col-lg-5">
    <form method="POST" action="{{ route($portal.'.profile.password') }}" class="card border-0 shadow-sm">
      @csrf @method('PUT')
      <div class="card-header bg-white fw-semibold"><i class="bi bi-key me-1"></i>Change password</div>
      <div class="card-body">
        <div class="mb-3"><label for="current_password" class="form-label">Current password</label>
          <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required autocomplete="current-password">
          @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="mb-3"><label for="new_password" class="form-label">New password</label>
          <input type="password" class="form-control @error('new_password') is-invalid @enderror" id="new_password" name="new_password" required autocomplete="new-password">
          <div class="form-text">At least 8 characters.</div>
          @error('new_password')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="mb-3"><label for="new_password_confirmation" class="form-label">Confirm new password</label>
          <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required autocomplete="new-password"></div>
        <button class="btn btn-outline-cse w-100">Update password</button>
      </div>
    </form>
    <div class="alert alert-secondary mt-3 small"><i class="bi bi-info-circle me-1"></i>Your role (<strong>{{ auth()->user()->role->label() }}</strong>) and account status are managed by the department office and cannot be changed here.</div>
  </div>
</div>
@endsection
