@extends('layouts.portal')
@section('title','My Achievements')
@php $portal = $portal ?? 'student'; @endphp
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
  <h1 class="h4 fw-bold mb-0">My achievement submissions</h1>
  <a href="{{ route($portal.'.achievements.create') }}" class="btn btn-cse"><i class="bi bi-plus-lg me-1"></i>New submission</a>
</div>

<form method="GET" class="card card-body bg-white border-0 shadow-sm row g-2 align-items-end mb-3">
  <div class="col-md-5">
    <label for="q" class="form-label small text-muted mb-1">Search title</label>
    <input type="text" id="q" name="q" value="{{ $search }}" class="form-control form-control-sm" placeholder="e.g. hackathon">
  </div>
  <div class="col-md-4">
    <label for="status" class="form-label small text-muted mb-1">Status</label>
    <select id="status" name="status" class="form-select form-select-sm">
      <option value="">All statuses</option>
      @foreach(['draft','pending','rejected','approved','published'] as $s)
        <option value="{{ $s }}" @selected($statusFilter===$s)>{{ ucfirst($s) }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-3 d-flex gap-2">
    <button class="btn btn-sm btn-cse flex-fill">Filter</button>
    <a href="{{ route($portal.'.achievements.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
  </div>
</form>

<div class="card border-0 shadow-sm">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead><tr><th>Title</th><th>Category</th><th>Date</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
      <tbody>
      @forelse ($items as $a)
        <tr>
          <td><a class="text-decoration-none fw-medium" href="{{ route($portal.'.achievements.show', $a) }}">{{ $a->title }}</a></td>
          <td class="small text-muted">{{ $a->category?->name ?? '—' }}</td>
          <td class="small">{{ $a->achievement_date?->format('d M Y') ?? '—' }}</td>
          <td>@include('partials.status-badge', ['item' => $a])</td>
          <td class="text-end">
            <a class="btn btn-sm btn-outline-secondary" href="{{ route($portal.'.achievements.show', $a) }}"><i class="bi bi-eye"></i></a>
            @can('update', $a)
              <a class="btn btn-sm btn-outline-primary" href="{{ route($portal.'.achievements.edit', $a) }}"><i class="bi bi-pencil"></i></a>
            @endcan
            @can('delete', $a)
              @include('partials.confirm-form', ['action'=>route($portal.'.achievements.destroy',$a),'method'=>'DELETE','btnClass'=>'btn-outline-danger','confirm'=>'Delete this draft permanently?','slot'=>'<i class=\'bi bi-trash\'></i>'])
            @endcan
          </td>
        </tr>
      @empty
        <tr><td colspan="5" class="text-center py-5 text-muted">
          <i class="bi bi-inbox display-6 d-block mb-2"></i>
          No submissions match. <a href="{{ route($portal.'.achievements.create') }}">Submit your first achievement</a>.
        </td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-footer bg-white border-0">{{ $items->links() }}</div>
</div>
@endsection
