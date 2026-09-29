@extends('admin.content._form')
@section('extraFields')
  <div class="col-12"><label for="inventors" class="form-label">Inventors <span class="text-danger">*</span></label>
    <input id="inventors" name="inventors" required class="form-control @error('inventors') is-invalid @enderror" value="{{ old('inventors',$item->inventors) }}">
    @error('inventors')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
  <div class="col-md-4"><label for="patent_number" class="form-label">Patent number</label>
    <input id="patent_number" name="patent_number" class="form-control" value="{{ old('patent_number',$item->patent_number) }}"></div>
  <div class="col-md-4"><label for="filing_date" class="form-label">Filing date</label>
    <input type="date" id="filing_date" name="filing_date" class="form-control" value="{{ old('filing_date',$item->filing_date?->format('Y-m-d')) }}"></div>
  <div class="col-md-4"><label for="grant_date" class="form-label">Grant date</label>
    <input type="date" id="grant_date" name="grant_date" class="form-control" value="{{ old('grant_date',$item->grant_date?->format('Y-m-d')) }}"></div>
  <div class="col-md-6"><label for="jurisdiction" class="form-label">Jurisdiction / office</label>
    <input id="jurisdiction" name="jurisdiction" placeholder="IPO India / USPTO" class="form-control" value="{{ old('jurisdiction',$item->jurisdiction) }}"></div>
  <div class="col-md-6"><label for="status_label" class="form-label">IP status</label>
    <select id="status_label" name="status_label" class="form-select">
      @foreach(['filed','granted','expired'] as $s)<option value="{{ $s }}" @selected(old('status_label',$item->status_label ?? 'filed')===$s)>{{ ucfirst($s) }}</option>@endforeach</select></div>
@endsection
