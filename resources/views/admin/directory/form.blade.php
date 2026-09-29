@extends('layouts.admin')
@section('title', ($person?->exists ?? false) ? 'Edit Profile' : 'Add Record')
@section('content')
@php $isEdit = ($person?->exists ?? false); @endphp
<h1 class="h4 fw-bold mb-3">{{ $isEdit ? 'Edit' : 'Add' }} {{ ucfirst($type) }} record</h1>
<form method="POST" class="card border-0 shadow-sm col-lg-8"
      action="{{ $isEdit ? route('admin.'.$type.'.update',$person) : route('admin.'.$type.'.store') }}">
  @csrf @if($isEdit) @method('PUT') @endif
  <div class="card-body row g-3">
    <div class="col-md-6"><label for="name" class="form-label">Full name <span class="text-danger">*</span></label>
      <input id="name" name="name" required class="form-control @error('name') is-invalid @enderror" value="{{ old('name', optional($person)->name) }}">
      @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="col-md-6"><label for="email" class="form-label">Email <span class="text-danger">*</span></label>
      <input type="email" id="email" name="email" required class="form-control @error('email') is-invalid @enderror" value="{{ old('email', optional($person)->email) }}">
      @if(!$isEdit)<div class="form-text">Creates a portal-ready record; the person sets a password via “Forgot password”.</div>@endif
      @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="col-md-6"><label for="phone" class="form-label">Phone</label>
      <input id="phone" name="phone" class="form-control" value="{{ old('phone', optional($person)->phone) }}"></div>
    @if($type === 'students')
      <div class="col-md-6"><label for="roll_number" class="form-label">Roll number <span class="text-danger">*</span></label>
        <input id="roll_number" name="roll_number" required class="form-control @error('roll_number') is-invalid @enderror" value="{{ old('roll_number', optional(optional($person)->student)->roll_number) }}">
        @error('roll_number')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
      <div class="col-md-6"><label for="program" class="form-label">Program</label>
        <input id="program" name="program" class="form-control" value="{{ old('program', optional(optional($person)->student)->program ?? 'B.Tech CSE') }}"></div>
      <div class="col-md-6"><label for="batch" class="form-label">Batch year</label>
        <input type="number" min="1950" max="2100" id="batch" name="batch" class="form-control @error('batch') is-invalid @enderror" value="{{ old('batch', optional(optional($person)->student)->batch) }}">
        @error('batch')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    @else
      <div class="col-md-6"><label for="employee_id" class="form-label">Employee ID <span class="text-danger">*</span></label>
        <input id="employee_id" name="employee_id" required class="form-control @error('employee_id') is-invalid @enderror" value="{{ old('employee_id', optional(optional($person)->facultyProfile)->employee_id) }}">
        @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
      <div class="col-md-6"><label for="designation" class="form-label">Designation</label>
        <input id="designation" name="designation" class="form-control" value="{{ old('designation', optional(optional($person)->facultyProfile)->designation) }}"></div>
      <div class="col-md-6"><label for="specialization" class="form-label">Specialization</label>
        <input id="specialization" name="specialization" class="form-control" value="{{ old('specialization', optional(optional($person)->facultyProfile)->specialization) }}"></div>
      <div class="col-md-6"><label for="qualification" class="form-label">Qualification</label>
        <input id="qualification" name="qualification" class="form-control" value="{{ old('qualification', optional(optional($person)->facultyProfile)->qualification) }}"></div>
      <div class="col-12"><label for="bio" class="form-label">Short bio (public directory)</label>
        <textarea id="bio" name="bio" rows="3" maxlength="2000" class="form-control">{{ old('bio', optional(optional($person)->facultyProfile)->bio) }}</textarea></div>
      <div class="col-12 form-check ms-3"><input class="form-check-input" type="checkbox" value="1" id="public_display_consent" name="public_display_consent" @checked(old('public_display_consent', optional(optional($person)->facultyProfile)->public_display_consent))>
        <label class="form-check-label" for="public_display_consent">List in public faculty directory</label></div>
    @endif
  </div>
  <div class="card-footer bg-white d-flex gap-2 justify-content-end">
    <a class="btn btn-outline-secondary me-auto" href="{{ route('admin.'.$type.'.index') }}">Cancel</a>
    <button class="btn btn-cse">{{ $isEdit ? 'Save changes' : 'Create record' }}</button>
  </div>
</form>
@endsection
