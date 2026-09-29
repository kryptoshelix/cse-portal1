@extends('layouts.admin')
@section('title','Edit Account')
@section('content')
<h1 class="h4 fw-bold mb-3">Edit account — {{ $user->name }}</h1>
<form method="POST" action="{{ route('admin.accounts.update',$user) }}" class="card border-0 shadow-sm col-lg-6">
  @csrf @method('PUT')
  <div class="card-body">
    <div class="mb-3"><label for="name" class="form-label">Name</label><input id="name" name="name" required class="form-control" value="{{ old('name',$user->name) }}"></div>
    <div class="mb-3"><label class="form-label">Email</label><input class="form-control" value="{{ $user->email }}" disabled></div>
    <div class="mb-3"><label for="role" class="form-label">Role <span class="text-danger">*</span></label>
      <select id="role" name="role" class="form-select @error('role') is-invalid @enderror">
        @foreach(['student','faculty','admin'] as $r)<option value="{{ $r }}" @selected(old('role',$user->role->value)===$r)>{{ ucfirst($r) }}</option>@endforeach
      </select>
      @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
      <div class="form-text">Changing a role changes portal access immediately.</div></div>
    <div class="mb-3"><label for="status" class="form-label">Status <span class="text-danger">*</span></label>
      <select id="status" name="status" class="form-select @error('status') is-invalid @enderror">
        @foreach(['active','pending','suspended'] as $s)<option value="{{ $s }}" @selected(old('status',$user->status->value)===$s)>{{ ucfirst($s) }}</option>@endforeach
      </select>
      @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="d-flex gap-2"><button class="btn btn-cse">Save account</button><a class="btn btn-outline-secondary" href="{{ route('admin.accounts.show',$user) }}">Cancel</a></div>
  </div>
</form>
@endsection
