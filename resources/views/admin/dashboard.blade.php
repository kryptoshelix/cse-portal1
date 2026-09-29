@extends('layouts.admin')
@section('title','Admin Dashboard')
@php $portal='admin'; @endphp
@section('content')
<h1 class="h4 fw-bold mb-3">Administration overview</h1>
<div class="row g-3 mb-4">
  @foreach([
    ['Pending reviews', $stats['pending'], 'bi-inboxes', 'text-warning'],
    ['Approved, not published', $stats['approved_unpublished'], 'bi-check2-circle', 'text-info'],
    ['Published achievements', $stats['published'], 'bi-broadcast', 'text-success'],
    ['Accounts awaiting approval', $stats['pending_accounts'], 'bi-person-question', 'text-danger'],
    ['Students', $stats['students'], 'bi-people', 'text-primary'],
    ['Faculty', $stats['faculty'], 'bi-person-video3', 'text-secondary'],
  ] as [$label,$value,$icon,$color])
    <div class="col-6 col-lg-2"><div class="card border-0 shadow-sm stat-card"><div class="card-body text-center py-3">
      <i class="bi {{ $icon }} fs-3 {{ $color }}"></i><div class="fs-2 fw-bold lh-1 mt-1">{{ $value }}</div><div class="small text-muted">{{ $label }}</div>
    </div></div></div>
  @endforeach
</div>

<div class="row g-3">
  <div class="col-lg-6">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-semibold"><i class="bi bi-hourglass-split me-1 text-warning"></i>Review queue</span>
        <a class="small" href="{{ route('admin.achievements.pending') }}">Open queue →</a>
      </div>
      <ul class="list-group list-group-flush">
        @forelse($recentPending as $a)
          <li class="list-group-item d-flex justify-content-between align-items-center gap-2">
            <div><a class="text-decoration-none fw-medium" href="{{ route('admin.achievements.show',$a) }}">{{ Str::limit($a->title,45) }}</a>
              <div class="small text-muted">{{ $a->owner?->name }} · submitted {{ $a->submitted_at?->diffForHumans() }}</div></div>
            <a class="btn btn-sm btn-outline-success" href="{{ route('admin.achievements.show',$a) }}#decision">Decide</a>
          </li>
        @empty
          <li class="list-group-item text-center text-muted py-4 small">Queue is empty 🎉</li>
        @endforelse
      </ul>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-semibold"><i class="bi bi-person-question me-1 text-danger"></i>Account approvals</span>
        <a class="small" href="{{ route('admin.accounts.pending') }}">All pending →</a>
      </div>
      <ul class="list-group list-group-flush">
        @forelse($recentPendingAccounts as $u)
          <li class="list-group-item d-flex justify-content-between align-items-center gap-2 flex-wrap">
            <div><a class="text-decoration-none fw-medium" href="{{ route('admin.accounts.show',$u) }}">{{ $u->name }}</a>
              <div class="small text-muted">{{ $u->email }} · {{ $u->role->label() }} · registered {{ $u->created_at->diffForHumans() }}</div></div>
            <div>
              <form method="POST" action="{{ route('admin.accounts.approve',$u) }}" class="d-inline">@csrf<button class="btn btn-sm btn-success">Approve</button></form>
            </div>
          </li>
        @empty
          <li class="list-group-item text-center text-muted py-4 small">No accounts waiting for approval.</li>
        @endforelse
      </ul>
    </div>
  </div>
</div>

<div class="row g-3 mt-1">
  <div class="col-lg-7">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-white d-flex justify-content-between align-items-center"><span class="fw-semibold"><i class="bi bi-clock-history me-1"></i>Recent audit activity</span><a class="small" href="{{ route('admin.audit.index') }}">Full log →</a></div>
      <ul class="list-group list-group-flush small">
        @forelse($recentAudit as $log)
          <li class="list-group-item d-flex justify-content-between gap-2">
            <span><span class="badge bg-light text-dark border me-1">{{ $log->action }}</span>{{ $log->description }} <span class="text-muted">— by {{ $log->user?->name ?? 'system' }}</span></span>
            <span class="text-muted text-nowrap">{{ $log->created_at->diffForHumans(null, true) }}</span>
          </li>
        @empty
          <li class="list-group-item text-muted text-center py-4">No recorded activity yet.</li>
        @endforelse
      </ul>
    </div>
  </div>
  <div class="col-lg-5">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-white fw-semibold"><i class="bi bi-megaphone me-1"></i>Publishing shortcuts</div>
      <div class="card-body d-grid gap-2">
        <a class="btn btn-outline-cse" href="{{ route('admin.activities.create') }}"><i class="bi bi-calendar-event me-1"></i>Add activity / event</a>
        <a class="btn btn-outline-cse" href="{{ route('admin.news.create') }}"><i class="bi bi-newspaper me-1"></i>Publish news item</a>
        <a class="btn btn-outline-cse" href="{{ route('admin.gallery.create') }}"><i class="bi bi-images me-1"></i>Add gallery photo</a>
        <a class="btn btn-outline-cse" href="{{ route('admin.categories.index') }}"><i class="bi bi-tags me-1"></i>Manage achievement categories</a>
      </div>
    </div>
  </div>
</div>
@endsection
