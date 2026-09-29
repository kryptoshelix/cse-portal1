@extends('admin.content._form')
@section('extraFields')
  <div class="col-12"><label for="authors" class="form-label">Authors <span class="text-danger">*</span></label>
    <input id="authors" name="authors" required class="form-control @error('authors') is-invalid @enderror" placeholder="A. Student, B. Teacher, C. Researcher" value="{{ old('authors',$item->authors) }}">
    @error('authors')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
  <div class="col-md-6"><label for="venue" class="form-label">Venue / publication name <span class="text-danger">*</span></label>
    <input id="venue" name="venue" required class="form-control @error('venue') is-invalid @enderror" value="{{ old('venue',$item->venue) }}">
    @error('venue')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
  <div class="col-md-3"><label for="publication_year" class="form-label">Year</label>
    <input type="number" min="1950" max="2100" id="publication_year" name="publication_year" class="form-control" value="{{ old('publication_year',$item->publication_year ?? date('Y')) }}"></div>
  <div class="col-md-3"><label for="doi" class="form-label">DOI</label>
    <input id="doi" name="doi" class="form-control" value="{{ old('doi',$item->doi) }}"></div>
  <div class="col-md-6"><label for="publication_type" class="form-label">Type</label>
    <select id="publication_type" name="publication_type" class="form-select">
      @foreach(['journal','conference','book_chapter','preprint'] as $t)<option value="{{ $t }}" @selected(old('publication_type',$item->publication_type ?? 'journal')===$t)>{{ str_replace('_',' ',$t) }}</option>@endforeach</select></div>
  <div class="col-md-6"><label for="indexed_in" class="form-label">Indexed in</label>
    <input id="indexed_in" name="indexed_in" placeholder="Scopus, IEEE Xplore" class="form-control" value="{{ old('indexed_in',$item->indexed_in) }}"></div>
  <div class="col-12"><label for="external_link" class="form-label">Link to paper</label>
    <input type="url" id="external_link" name="external_link" class="form-control @error('external_link') is-invalid @enderror" value="{{ old('external_link',$item->external_link) }}">
    @error('external_link')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
@endsection
