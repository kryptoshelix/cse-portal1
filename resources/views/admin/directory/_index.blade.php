@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
  <h1 class="h4 fw-bold mb-0">{{ $back['label'] }}</h1>
  <a href="{{ $createRoute }}" class="btn btn-cse"><i class="bi bi-person-plus me-1"></i>{{ $newLabel }}</a>
</div>
<form method="GET" class="card card-body bg-white border-0 shadow-sm row g-2 align-items-end mb-3">
  <div class="col-md-8"><label for="q" class="small text-muted mb-1">Search name, email or roll number</label>
    <input id="q" name="q" value="{{ $search }}" class="form-control form-control-sm"></div>
  <div class="col-md-4 d-flex gap-2"><button class="btn btn-sm btn-cse flex-fill">Search</button><a href="{{ route($back['route']) }}" class="btn btn-sm btn-outline-secondary">Reset</a></div>
</form>
<div class="card border-0 shadow-sm"><div class="table-responsive">
  <table class="table table-hover align-middle mb-0">
    <thead><tr><th>Name</th><th>Roll no. / Emp ID</th><th>Program / Designation</th><th>Batch</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
    <tbody>
    @forelse($people as $u)
      <tr>
        <td><a class="text-decoration-none fw-medium" href="{{ route('admin.accounts.show',$u) }}">{{ $u->name }}</a><div class="small text-muted">{{ $u->email }}</div></td>
        <td class="small">{{ $u->student?->roll_number ?? $u->facultyProfile?->employee_id ?? '—' }}</td>
        <td class="small">{{ $u->student?->program ?? $u->facultyProfile?->designation ?? '—' }}</td>
        <td class="small">{{ $u->student?->batch ?? '—' }}</td>
        <td><span class="badge bg-{{ $u->status->value==='active'?'success':($u->status->value==='pending'?'warning text-dark':'danger') }}">{{ $u->status->label() }}</span></td>
        <td class="text-end">
          <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.accounts.show',$u) }}"><i class="bi bi-eye"></i></a>
          <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.'.$backType.'.edit',$u) }}"><i class="bi bi-pencil"></i></a>
          @if($u->id !== auth()->id())
            @include('partials.confirm-form',['action'=>route('admin.'.$backType.'.destroy',$u),'method'=>'DELETE','btnClass'=>'btn-outline-danger','confirm'=>'Soft-delete this person record? Their login account is kept for audit purposes.','slot'=>'<i class="bi bi-trash"></i>'])
          @endif
        </td>
      </tr>
    @empty
      <tr><td colspan="6" class="text-center py-5 text-muted"><i class="bi bi-people display-6 d-block mb-2"></i>No records found. Use “{{ $newLabel }}” to add the first one.</td></tr>
    @endforelse
    </tbody>
  </table>
</div><div class="card-footer bg-white border-0">{{ $people->links() }}</div></div>
@endsection
