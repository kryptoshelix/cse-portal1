@extends('layouts.admin')
@section('title','Achievements')
@php $portal='admin'; @endphp
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
  <h1 class="h4 fw-bold mb-0">Achievement submissions</h1>
  <a href="{{ route('admin.achievements.create') }}" class="btn btn-cse"><i class="bi bi-plus-lg me-1"></i>Add record</a>
</div>
<form method="GET" class="card card-body bg-white border-0 shadow-sm row g-2 align-items-end mb-3">
  <div class="col-md-3"><label for="q" class="small text-muted mb-1">Search title</label><input id="q" name="q" value="{{ $search }}" class="form-control form-control-sm"></div>
  <div class="col-md-2"><label for="status" class="small text-muted mb-1">Status</label>
    <select id="status" name="status" class="form-select form-select-sm"><option value="">All</option>
      @foreach(['draft','pending','rejected','approved','published'] as $s)<option value="{{ $s }}" @selected($statusFilter===$s)>{{ ucfirst($s) }}</option>@endforeach
    </select></div>
  <div class="col-md-2"><label for="category" class="small text-muted mb-1">Category</label>
    <select id="category" name="category" class="form-select form-select-sm"><option value="">All</option>
      @foreach($categories as $c)<option value="{{ $c->id }}" @selected($categoryId==(string)$c->id)>{{ $c->name }}</option>@endforeach
    </select></div>
  <div class="col-md-2"><label for="level" class="small text-muted mb-1">Level</label>
    <select id="level" name="level" class="form-select form-select-sm"><option value="">All</option>
      @foreach(['institute','regional','national','international'] as $l)<option value="{{ $l }}" @selected($level===$l)>{{ ucfirst($l) }}</option>@endforeach
    </select></div>
  <div class="col-md-3 d-flex gap-2"><button class="btn btn-sm btn-cse flex-fill">Apply filters</button><a href="{{ route('admin.achievements.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a></div>
</form>
<div class="card border-0 shadow-sm"><div class="table-responsive">
  <table class="table table-hover align-middle mb-0">
    <thead><tr><th>Title</th><th>Owner</th><th>Category</th><th>Date</th><th>Level</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
    <tbody>
    @forelse($items as $a)
      <tr>
        <td><a class="text-decoration-none fw-medium" href="{{ route('admin.achievements.show', $a) }}">{{ Str::limit($a->title, 50) }}</a>@if($a->featured)<i class="bi bi-star-fill text-warning ms-1" title="Featured"></i>@endif</td>
        <td class="small">{{ $a->owner?->name ?? '—' }}<div class="text-muted">{{ $a->owner?->student?->roll_number ?? ($a->owner ? 'Faculty' : '') }}</div></td>
        <td class="small text-muted">{{ $a->category?->name }}</td>
        <td class="small">{{ $a->achievement_date?->format('d M Y') }}</td>
        <td class="small text-capitalize">{{ $a->level }}</td>
        <td>@include('partials.status-badge', ['item'=>$a])</td>
        <td class="text-end">
          <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.achievements.show', $a) }}"><i class="bi bi-eye"></i></a>
          <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.achievements.edit', $a) }}"><i class="bi bi-pencil"></i></a>
          @can('delete', $a)
            @include('partials.confirm-form', ['action'=>route('admin.achievements.destroy',$a),'method'=>'DELETE','btnClass'=>'btn-outline-danger','confirm'=>'Soft-delete this achievement record?','slot'=>'<i class="bi bi-trash"></i>'])
          @endcan
        </td>
      </tr>
    @empty
      <tr><td colspan="7" class="text-center py-5 text-muted"><i class="bi bi-inbox display-6 d-block mb-2"></i>No achievements match these filters.</td></tr>
    @endforelse
    </tbody>
  </table>
</div><div class="card-footer bg-white border-0">{{ $items->links() }}</div></div>
@endsection
