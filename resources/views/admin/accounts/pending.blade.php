@extends('layouts.admin')
@section('title','Pending Approvals')
@section('content')
<h1 class="h4 fw-bold mb-1">Registrations awaiting approval</h1>
<p class="text-muted small">Approving activates the account and emails the user a link to set their password — no credentials are shared in plain text.</p>
<div class="card border-0 shadow-sm">
  <ul class="list-group list-group-flush">
    @forelse($users as $u)
      <li class="list-group-item d-flex justify-content-between align-items-center gap-3 flex-wrap py-3">
        <div>
          <a class="fw-semibold text-decoration-none" href="{{ route('admin.accounts.show',$u) }}">{{ $u->name }}</a>
          <span class="badge text-bg-light border ms-1 text-capitalize">{{ $u->role->value }}</span>
          <div class="small text-muted">{{ $u->email }} · {{ $u->student?->roll_number ?? $u->facultyProfile?->employee_id ?? '' }} · registered {{ $u->created_at->format('d M Y') }}</div>
        </div>
        <div class="d-flex gap-2">
          <form method="POST" action="{{ route('admin.accounts.approve',$u) }}" onsubmit="return confirm('Approve this account and send password setup link?');">@csrf<button class="btn btn-sm btn-success"><i class="bi bi-check-lg me-1"></i>Approve</button></form>
          <form method="POST" action="{{ route('admin.accounts.decline',$u) }}" onsubmit="return confirm('Decline this registration? The account will be suspended.');">@csrf<button class="btn btn-sm btn-outline-danger"><i class="bi bi-x-lg me-1"></i>Decline</button></form>
        </div>
      </li>
    @empty
      <li class="list-group-item text-center py-5 text-muted"><i class="bi bi-check2-all display-6 d-block mb-2 text-success"></i>Nice work — there is nothing waiting for approval.</li>
    @endforelse
  </ul>
  <div class="card-footer bg-white border-0">{{ $users->links() }}</div>
</div>
@endsection
