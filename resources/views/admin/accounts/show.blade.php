@extends('layouts.admin')
@section('title', $user->name)
@php use App\Enums\AccountStatus; @endphp
@section('content')
<nav aria-label="breadcrumb"><ol class="breadcrumb small">
  <li class="breadcrumb-item"><a class="text-decoration-none" href="{{ route('admin.accounts.index') }}">Accounts</a></li>
  <li class="breadcrumb-item active">{{ $user->name }}</li>
</ol></nav>
<div class="row g-3">
  <div class="col-lg-7">
    <div class="card border-0 shadow-sm"><div class="card-body">
      <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
          <h1 class="h5 fw-bold mb-1">{{ $user->name }}</h1>
          <div class="text-muted small">{{ $user->email }} · <span class="badge text-bg-light border text-capitalize">{{ $user->role->value }}</span>
            <span class="badge bg-{{ $user->status===AccountStatus::Active?'success':($user->status===AccountStatus::Pending?'warning text-dark':'danger') }}">{{ $user->status->label() }}</span></div>
        </div>
        <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.accounts.edit',$user) }}"><i class="bi bi-pencil me-1"></i>Edit role &amp; status</a>
      </div>
      <hr>
      <dl class="row small mb-0">
        <dt class="col-sm-4">Registered</dt><dd class="col-sm-8">{{ $user->created_at->format('d M Y, H:i') }}</dd>
        <dt class="col-sm-4">Last sign-in</dt><dd class="col-sm-8">{{ $user->last_login_at?->diffForHumans() ?? 'Never signed in' }}</dd>
        <dt class="col-sm-4">Phone</dt><dd class="col-sm-8">{{ $user->phone ?? '—' }}</dd>
        @if($user->student)
          <dt class="col-sm-4">Roll number</dt><dd class="col-sm-8">{{ $user->student->roll_number }}</dd>
          <dt class="col-sm-4">Program / Batch</dt><dd class="col-sm-8">{{ $user->student->program }} · {{ $user->student->batch }}</dd>
        @endif
        @if($user->facultyProfile)
          <dt class="col-sm-4">Employee ID</dt><dd class="col-sm-8">{{ $user->facultyProfile->employee_id }}</dd>
          <dt class="col-sm-4">Designation</dt><dd class="col-sm-8">{{ $user->facultyProfile->designation }} ({{ $user->facultyProfile->specialization }})</dd>
        @endif
        <dt class="col-sm-4">Achievements</dt><dd class="col-sm-8">{{ $stats->sum() }} total — {{ $stats['published'] ?? 0 }} published, {{ $stats['pending'] ?? 0 }} pending</dd>
      </dl>
    </div></div>
    <div class="card border-0 shadow-sm mt-3">
      <div class="card-header bg-white fw-semibold">Achievement submissions</div>
      <ul class="list-group list-group-flush">
        @forelse($achievements as $a)
          <li class="list-group-item d-flex justify-content-between align-items-center gap-2">
            <div><a class="text-decoration-none" href="{{ route('admin.achievements.show',$a) }}">{{ Str::limit($a->title,60) }}</a>
              <div class="small text-muted">{{ $a->category?->name }} · {{ $a->achievement_date?->format('M Y') }}</div></div>
            @include('partials.status-badge',['item'=>$a])
          </li>
        @empty
          <li class="list-group-item text-muted small text-center py-4">No achievement records for this person.</li>
        @endforelse
      </ul>
      <div class="card-footer bg-white border-0">{{ $achievements->links() }}</div>
    </div>
  </div>
  <div class="col-lg-5">
    <div class="card border-0 shadow-sm"><div class="card-body">
      <h2 class="h6 text-uppercase text-muted">Account actions</h2>
      <div class="d-grid gap-2">
        @if($user->status === AccountStatus::Pending)
          <form method="POST" action="{{ route('admin.accounts.approve',$user) }}" onsubmit="return confirm('Approve and notify by email?');">@csrf<button class="btn btn-success"><i class="bi bi-check-lg me-1"></i>Approve account</button></form>
          <form method="POST" action="{{ route('admin.accounts.decline',$user) }}" onsubmit="return confirm('Decline this registration?');">@csrf<button class="btn btn-outline-danger">Decline</button></form>
        @elseif($user->id !== auth()->id())
          @if($user->status === AccountStatus::Active)
            <form method="POST" action="{{ route('admin.accounts.suspend',$user) }}" onsubmit="return confirm('Suspend this account? They will be signed out of future sessions.');">@csrf<button class="btn btn-outline-danger"><i class="bi bi-pause-circle me-1"></i>Suspend account</button></form>
          @else
            <form method="POST" action="{{ route('admin.accounts.activate',$user) }}">@csrf<button class="btn btn-success"><i class="bi bi-play-circle me-1"></i>Reactivate account</button></form>
          @endif
        @else
          <div class="alert alert-info small mb-0">This is your own account — you cannot suspend yourself.</div>
        @endif
        <form method="POST" action="{{ route('admin.accounts.reset-password',$user) }}" onsubmit="return confirm('Send a fresh password reset link to this user?');">@csrf
          <button class="btn btn-outline-cse"><i class="bi bi-envelope-arrow-up me-1"></i>Send password reset link</button>
        </form>
      </div>
    </div></div>
    <div class="card border-0 shadow-sm mt-3">
      <div class="card-header bg-white fw-semibold small">Audit trail</div>
      <ul class="list-group list-group-flush small">
        @forelse($audit as $log)
          <li class="list-group-item py-2"><span class="badge bg-light text-dark border me-1">{{ $log->action }}</span>{{ $log->description }}<div class="text-muted">{{ $log->created_at->format('d M Y H:i') }} · by {{ $log->user?->name ?? 'system' }}</div></li>
        @empty
          <li class="list-group-item text-muted text-center py-3">No recorded actions reference this account.</li>
        @endforelse
      </ul>
    </div>
  </div>
</div>
@endsection
