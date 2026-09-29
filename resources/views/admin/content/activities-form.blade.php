@extends('admin.content._form')
@section('extraFields')
  <div class="col-md-6"><label for="location" class="form-label">Location</label>
    <input id="location" name="location" maxlength="150" class="form-control" value="{{ old('location',$item->location) }}"></div>
  <div class="col-md-3"><label for="start_time" class="form-label">Start time</label>
    <input type="time" id="start_time" name="start_time" class="form-control" value="{{ old('start_time', $item->start_time?->format('H:i')) }}"></div>
  <div class="col-md-3"><label for="end_time" class="form-label">End time</label>
    <input type="time" id="end_time" name="end_time" class="form-control" value="{{ old('end_time', $item->end_time?->format('H:i')) }}"></div>
@endsection
