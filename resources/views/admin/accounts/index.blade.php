@extends('layouts.admin')
@section('title','Accounts')
@php $statuses = ['active'=>'Active','pending'=>'Pending approval','suspended'=>'Suspended']; @endphp
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
  <h1 class="h4 fw-bold mb-0">Portal accounts</h1>
  <a href="{{ route('admin.accounts.pending') }}" class="btn btn-warning"><i class="bi bi-person-question me-1"></i>Pending approvals</a>
</div>
<form method="GET" class="card card-body bg-white border-0 shadow-sm row g-2 align-items-end mb-3">
  <div class="col-md-5"><label for="q" class="small text-muted mb-1">Search name or email</label><input id="q" name="q" value="{{ $search }}" class="form-control form-control-sm"></div>
  <div class="col-md-3"><label for="role" class="small text-muted mb-1">Role</label>
    <select id="role" name="role" class="form-select form-select-sm"><option value="">All roles</option>
      @foreach(['student','faculty','admin'] as $r)<option value="{{ $r }}" @selected($role===$r)>{{ ucfirst($r) }}</option>@endforeach</select></div>
  <div class="col-md-2"><label for="status" class="small text-muted mb-1">Status</label>
    <select id="status" name="status" class="form-select form-select-sm"><option value="">All</option>
      @foreach($statuses as $k=>$v)<option value="{{ $k }}" @selected($statusFilter===$k)>{{ $v }}</option>@endforeach</select></div>
  <div class="col-md-2 d-flex gap-2"><button class="btn btn-sm btn-cse flex-fill">Filter</button><a href="{{ route('admin.accounts.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a></div>
</form>
<div class="card border-0 shadow-sm"><div class="table-responsive">
  <table class="table table-hover align-middle mb-0">
    <thead><tr><th>User</th><th>Role</th><th>Status</th><th>Last login</th><th>Created</th><th class="text-end">Actions</th></tr></thead>
    <tbody>
    @forelse($users as $u)
      <tr>
        <td><a class="text-decoration-none fw-medium" href="{{ route('admin.accounts.show',$u) }}">{{ $u->name }}</a><div class="small text-muted">{{ $u->email }}</div></td>
        <td><span class="badge text-bg-light border text-capitalize">{{ $u->role->value }}</span></td>
        <td><span class="badge bg-{{ $u->status->value==='active'?'success':($u->status->value==='pending'?'warning text-dark':'danger') }}">{{ $statuses[$u->status->value] ?? $u->status->label() }}</span></td>
        <td class="small text-muted">{{ $u->last_login_at?->diffForHumans() ?? 'never' }}</td>
        <td class="small text-muted">{{ $u->created_at->format('d M Y') }}</td>
        <td class="text-end">
          <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.accounts.show',$u) }}"><i class="bi bi-eye"></i></a>
          <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.accounts.edit',$u) }}"><i class="bi bi-pencil"></i></a>
        </td>
      </tr>
    @empty
      <tr><td colspan="6" class="text-center py-5 text-muted"><i class="bi bi-person display-6 d-block mb-2"></i>No accounts match the filters.</td></tr>
    @endforelse
    </tbody>
  </table>
</div><div class="card-footer bg-white border-0">{{ $users->links() }}</div></div>
@endsection
