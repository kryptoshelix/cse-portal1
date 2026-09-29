@extends('admin.content._form')
@section('extraFields')
  <div class="col-md-6"><label for="caption" class="form-label">Caption</label>
    <input id="caption" name="caption" class="form-control" value="{{ old('caption',$item->caption) }}"></div>
  <div class="col-md-6"><label for="event_name" class="form-label">Event name</label>
    <input id="event_name" name="event_name" class="form-control" value="{{ old('event_name',$item->event_name) }}"></div>
  <div class="col-md-6"><label for="album" class="form-label">Album</label>
    <input id="album" name="album" list="albums" class="form-control" value="{{ old('album',$item->album) }}">
    <datalist id="albums"><option value="Annual Tech Fest"><option value="Convocation"><option value="Campus Life"><option value="Workshops"></datalist></div>
  <div class="col-md-6"><label for="taken_at" class="form-label">Taken on</label>
    <input type="date" id="taken_at" name="taken_at" class="form-control" value="{{ old('taken_at',$item->taken_at?->format('Y-m-d')) }}"></div>
@endsection
