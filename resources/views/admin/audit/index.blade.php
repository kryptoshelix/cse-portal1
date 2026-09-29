@extends('layouts.admin')
@section('title','Audit Log')
@section('content')
<h1 class="h4 fw-bold mb-3">Audit trail</h1>
<p class="text-muted small">Every review decision, publication and account change is recorded here. Entries are read-only.</p>
<form method="GET" class="card card-body bg-white border-0 shadow-sm row g-2 align-items-end mb-3">
  <div class="col-md-5"><label for="q" class="small text-muted mb-1">Search description or action</label><input id="q" name="q" value="{{ $search }}" class="form-control form-control-sm"></div>
  <div class="col-md-3"><label for="user_id" class="small text-muted mb-1">Performed by</label>
    <select id="user_id" name="user_id" class="form-select form-select-sm"><option value="">Anyone</option>
      @foreach($users as $u)<option value="{{ $u->id }}" @selected((string)$userId===(string)$u->id)>{{ $u->name }}</option>@endforeach</select></div>
  <div class="col-md-4 d-flex gap-2"><button class="btn btn-sm btn-cse flex-fill">Filter</button><a href="{{ route('admin.audit.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a></div>
</form>
<div class="card border-0 shadow-sm"><div class="table-responsive">
  <table class="table table-hover align-middle mb-0">
    <thead><tr><th>When</th><th>Who</th><th>Action</th><th>Details</th><th>IP</th></tr></thead>
    <tbody>
    @forelse($logs as $log)
      <tr>
        <td class="small text-nowrap">{{ $log->created_at->format('d M Y H:i') }}</td>
        <td class="small">{{ $log->user?->name ?? 'System' }}</td>
        <td><span class="badge text-bg-light border">{{ $log->action }}</span></td>
        <td class="small">{{ $log->description }}
          @if($log->new_values)<details><summary class="small text-primary d-inline">changes</summary><pre class="small bg-light p-2 rounded mt-1 mb-0">{{ json_encode($log->new_values, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}</pre></details>@endif</td>
        <td class="small text-muted">{{ $log->ip_address ?? '—' }}</td>
      </tr>
    @empty
      <tr><td colspan="5" class="text-center py-5 text-muted">No audit entries match.</td></tr>
    @endforelse
    </tbody>
  </table>
</div><div class="card-footer bg-white border-0">{{ $logs->links() }}</div></div>
@endsection
