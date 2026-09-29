@extends('layouts.admin')
@section('title', $moduleLabel.'s')
@php
  $dateField = $module === 'news' ? 'published_at' : ($module === 'activity' ? 'event_date' : ($module === 'publication' ? 'publication_date' : null));
@endphp
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
  <h1 class="h4 fw-bold mb-0">{{ Str::plural($moduleLabel) }}</h1>
  <a href="{{ route($routeBase.'.create') }}" class="btn btn-cse"><i class="bi bi-plus-lg me-1"></i>New {{ strtolower($moduleLabel) }}</a>
</div>
<form method="GET" class="card card-body bg-white border-0 shadow-sm row g-2 align-items-end mb-3">
  <div class="col-md-5"><label for="q" class="small text-muted mb-1">Search title</label><input id="q" name="q" value="{{ $search }}" class="form-control form-control-sm"></div>
  <div class="col-md-3"><label for="status" class="small text-muted mb-1">Status</label>
    <select id="status" name="status" class="form-select form-select-sm"><option value="">All</option>
      @foreach(['draft','pending','approved','published'] as $s)<option value="{{ $s }}" @selected($statusFilter===$s)>{{ ucfirst($s) }}</option>@endforeach</select></div>
  <div class="col-md-4 d-flex gap-2"><button class="btn btn-sm btn-cse flex-fill">Filter</button><a href="{{ route($routeBase.'.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a></div>
</form>
<div class="card border-0 shadow-sm"><div class="table-responsive">
  <table class="table table-hover align-middle mb-0">
    <thead><tr><th>{{ $module==='gallery' ? 'Photo' : 'Title' }}</th><th>Date</th><th>Status</th><th>Featured</th><th class="text-end">Actions</th></tr></thead>
    <tbody>
    @forelse($items as $c)
      <tr>
        <td>
          <div class="d-flex align-items-center gap-2">
            @if($c->image_path)<img src="{{ asset('storage/'.$c->image_path) }}" alt="" class="rounded object-fit-cover" style="width:44px;height:44px">@endif
            <div><span class="fw-medium">{{ Str::limit($c->title,60) }}</span>@if($module==='gallery' && $c->caption)<div class="small text-muted">{{ Str::limit($c->caption,50) }}</div>@endif</div>
          </div>
        </td>
        <td class="small">{{ ($dateField ? $c->{$dateField} : $c->created_at)?->format('d M Y') ?? '—' }}</td>
        <td><span class="badge bg-{{ ['draft'=>'secondary','pending'=>'warning text-dark','approved'=>'info text-dark','published'=>'success'][$c->status] ?? 'secondary' }}">{{ ucfirst($c->status) }}</span></td>
        <td>{!! $c->featured ? '<i class="bi bi-star-fill text-warning"></i>' : '<span class="text-muted small">—</span>' !!}</td>
        <td class="text-end">
          <a class="btn btn-sm btn-outline-primary" href="{{ route($routeBase.'.edit',$c) }}"><i class="bi bi-pencil"></i></a>
          @include('partials.confirm-form',['action'=>route($routeBase.'.destroy',$c),'method'=>'DELETE','btnClass'=>'btn-outline-danger','confirm'=>'Delete this record permanently?','slot'=>'<i class="bi bi-trash"></i>'])
        </td>
      </tr>
    @empty
      <tr><td colspan="5" class="text-center py-5 text-muted"><i class="bi bi-folder2 display-6 d-block mb-2"></i>Nothing here yet — create your first {{ strtolower($moduleLabel) }}.</td></tr>
    @endforelse
    </tbody>
  </table>
</div><div class="card-footer bg-white border-0">{{ $items->links() }}</div></div>
@endsection
