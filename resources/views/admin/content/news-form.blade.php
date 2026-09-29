@extends('admin.content._form')
@section('extraFields')
  <div class="col-md-6"><label for="news_category" class="form-label">News category</label>
    <input id="news_category" name="news_category" list="ncats" maxlength="80" class="form-control" value="{{ old('news_category',$item->news_category) }}">
    <datalist id="ncats"><option value="Announcement"><option value="Event"><option value="Research"><option value="Placement"></datalist></div>
  <div class="col-md-6"><label for="published_at" class="form-label">Publish date</label>
    <input type="date" id="published_at" name="published_at" class="form-control" value="{{ old('published_at', $item->published_at?->format('Y-m-d')) }}"></div>
@endsection
