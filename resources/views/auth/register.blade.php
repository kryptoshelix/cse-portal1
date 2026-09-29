@extends('layouts.public')
@section('title', 'Register')
@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card shadow-sm border-0">
        <div class="card-body p-4 p-md-5">
          <h1 class="h4 fw-bold mb-1">Create your portal account</h1>
          <p class="text-muted small mb-4">Registration is open to CSE students and faculty only. Your account will be reviewed and approved by a department administrator.</p>
          @include('partials.alerts')
          <form method="POST" action="{{ route('register.submit') }}" novalidate>
            @csrf
            <div class="row g-3">
              <div class="col-md-6">
                <label for="name" class="form-label">Full name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label for="email" class="form-label">Institutional email <span class="text-danger">*</span></label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required autocomplete="new-password">
                <div class="form-text">Minimum 8 characters.</div>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label for="password_confirmation" class="form-label">Confirm password <span class="text-danger">*</span></label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
              </div>
              <div class="col-12">
                <label class="form-label">I am a… <span class="text-danger">*</span></label>
                <div class="d-flex gap-3">
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="account_type" id="typeStudent" value="student" {{ old('account_type','student')==='student'?'checked':'' }}>
                    <label class="form-check-label" for="typeStudent">Student</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="account_type" id="typeFaculty" value="faculty" {{ old('account_type')==='faculty'?'checked':'' }}>
                    <label class="form-check-label" for="typeFaculty">Faculty member</label>
                  </div>
                </div>
                @error('account_type')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-4">
                <label for="roll_number" class="form-label">Roll number (students)</label>
                <input type="text" class="form-control @error('roll_number') is-invalid @enderror" id="roll_number" name="roll_number" value="{{ old('roll_number') }}">
                @error('roll_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-4">
                <label for="program" class="form-label">Program</label>
                <input type="text" class="form-control" id="program" name="program" value="{{ old('program','B.Tech CSE') }}">
              </div>
              <div class="col-md-4">
                <label for="batch" class="form-label">Batch year</label>
                <input type="number" class="form-control @error('batch') is-invalid @enderror" id="batch" name="batch" min="1950" max="2100" value="{{ old('batch') }}">
                @error('batch')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label for="employee_id" class="form-label">Employee ID (faculty)</label>
                <input type="text" class="form-control @error('employee_id') is-invalid @enderror" id="employee_id" name="employee_id" value="{{ old('employee_id') }}">
                @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label for="specialization" class="form-label">Specialization (faculty)</label>
                <input type="text" class="form-control" id="specialization" name="specialization" value="{{ old('specialization') }}">
              </div>
            </div>
            <div class="d-grid mt-4">
              <button type="submit" class="btn btn-cse py-2">Submit registration for approval</button>
            </div>
            <p class="text-center small text-muted mt-3 mb-0">Administrator roles cannot be requested here. <a href="{{ route('login') }}">Already registered? Sign in</a></p>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
