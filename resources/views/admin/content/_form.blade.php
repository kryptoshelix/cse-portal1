@extends('layouts.admin')
@section('title', $item->exists ? 'Edit '.$moduleLabel : 'New '.$moduleLabel)
@php $action = $item->exists ? route($routeBase.'.update',$item) : route($routeBase.'.store'); @endphp
@section('content')
<h1 class="h4 fw-bold mb-3">{{ $item->exists ? 'Edit '.$moduleLabel : 'Add '.$moduleLabel }}</h1>
<form method="POST" enctype="multipart/form-data" action="{{ $action }}" class="card border-0 shadow-sm">
  @csrf @if($item->exists) @method('PUT') @endif
  <div class="card-body row g-3">
    <div class="col-md-8"><label for="title" class="form-label">Title <span class="text-danger">*</span></label>
      <input id="title" name="title" required maxlength="200" class="form-control @error('title') is-invalid @enderror" value="{{ old('title',$item->title) }}">
      @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="col-md-4"><label for="status" class="form-label">Status</label>
      <select id="status" name="status" class="form-select @error('status') is-invalid @enderror">
        @foreach(['draft','pending','approved','published'] as $s)<option value="{{ $s }}" @selected(old('status',$item->status)===$s)>{{ ucfirst($s) }}</option>@endforeach
      </select>
      @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="col-12"><label for="description" class="form-label">Description <span class="text-danger">*</span></label>
      <textarea id="description" name="description" rows="5" required maxlength="10000" class="form-control @error('description') is-invalid @enderror">{{ old('description',$item->description) }}</textarea>
      @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    @yield('extraFields')
    <div class="col-md-6"><label for="image" class="form-label">{{ $module === 'gallery' ? 'Photo *' : 'Cover image (optional)' }}</label>
      <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp" class="form-control @error('image') is-invalid @enderror" {{ $module==='gallery' && !$item->exists ? 'required':'' }}>
      @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
      @if($item->image_path)<div class="form-text"><i class="bi bi-image me-1"></i>Current: <img src="{{ asset('storage/'.$item->image_path) }}" alt="" style="max-height:60px" class="rounded border mt-1 d-block"></div>@endif</div>
    <div class="col-md-3"><label for="event_date" class="form-label">{{ $module==='activity' ? 'Event date' : 'Date' }}</label>
      <input type="date" id="event_date" name="event_date" class="form-control" value="{{ old('event_date', $item->event_date?->format('Y-m-d') ?? $item->publication_date?->format('Y-m-d')) }}"></div>
    <div class="col-md-3 d-flex align-items-end"><div class="form-check"><input class="form-check-input" type="checkbox" value="1" id="featured" name="featured" @checked(old('featured',$item->featured))><label class="form-check-label" for="featured">Featured on home page</label></div></div>
  </div>
  <div class="card-footer bg-white d-flex gap-2 justify-content-end">
    <a class="btn btn-outline-secondary me-auto" href="{{ route($routeBase.'.index') }}">Cancel</a>
    <button class="btn btn-cse">{{ $item->exists ? 'Save changes' : 'Create '.$moduleLabel }}</button>
  </div>
</form>
@endsection
