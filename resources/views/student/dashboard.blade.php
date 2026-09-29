@extends('layouts.portal')
@section('title','Student Dashboard')
@php $portal = 'student'; @endphp
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
  <div>
    <h1 class="h4 fw-bold mb-1">Welcome back, {{ auth()->user()->name }} 👋</h1>
    <p class="text-muted small mb-0">{{ auth()->user()->student?->roll_number }} · {{ auth()->user()->student?->program ?? 'B.Tech CSE' }} · Batch {{ auth()->user()->student?->batch }}</p>
  </div>
  <a href="{{ route('student.achievements.create') }}" class="btn btn-cse"><i class="bi bi-plus-lg me-1"></i>Submit achievement</a>
</div>

@php
  $missing = collect($profileFields)->filter(fn($ok) => !$ok)->count();
@endphp
@if ($missing > 0)
  <div class="alert alert-info d-flex align-items-center gap-2">
    <i class="bi bi-person-gear fs-4"></i>
    <div>Your profile is <strong>{{ round(100 - $missing*25) }}%</strong> complete.
      <a href="{{ route('student.profile') }}">Add the remaining details</a> to help reviewers verify your submissions.</div>
  </div>
@endif

<div class="row g-3 mb-4">
  @foreach([
    ['Total submissions', $counts->sum(), 'bi-inboxes', 'cse-stat-1'],
    ['Drafts', $counts['draft'] ?? 0, 'bi-pencil', 'cse-stat-2'],
    ['Pending review', $counts['pending'] ?? 0, 'bi-hourglass-split', 'cse-stat-3'],
    ['Rejected', $counts['rejected'] ?? 0, 'bi-x-circle', 'cse-stat-4'],
    ['Approved', $counts['approved'] ?? 0, 'bi-check-circle', 'cse-stat-5'],
    ['Published', $counts['published'] ?? 0, 'bi-broadcast', 'cse-stat-6'],
  ] as [$label, $value, $icon, $cls])
    <div class="col-6 col-md-4 col-xl-2">
      <div class="card stat-card border-0 shadow-sm {{ $cls }}"><div class="card-body text-center py-3">
        <i class="bi {{ $icon }} fs-3 opacity-75"></i>
        <div class="fs-2 fw-bold lh-1 mt-1">{{ $value }}</div>
        <div class="small text-muted">{{ $label }}</div>
      </div></div>
    </div>
  @endforeach
</div>

@if ($needsAttention->isNotEmpty())
<div class="card border-danger-subtle shadow-sm mb-4">
  <div class="card-header bg-white fw-semibold text-danger"><i class="bi bi-exclamation-triangle me-1"></i>Needs your attention</div>
  <ul class="list-group list-group-flush">
    @foreach ($needsAttention as $a)
      <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
          <a class="fw-semibold text-decoration-none" href="{{ route('student.achievements.show', $a) }}">{{ $a->title }}</a>
          <div class="small text-muted">Reviewer feedback: {{ Str::limit($a->rejection_feedback, 120) }}</div>
        </div>
        <a class="btn btn-sm btn-outline-danger" href="{{ route('student.achievements.edit', $a) }}">Fix &amp; resubmit</a>
      </li>
    @endforeach
  </ul>
</div>
@endif

<div class="card border-0 shadow-sm">
  <div class="card-header bg-white d-flex justify-content-between align-items-center">
    <span class="fw-semibold">Recent submissions</span>
    <a class="small" href="{{ route('student.achievements.index') }}">View all →</a>
  </div>
  @forelse ($recent as $a)
    <div class="list-group list-group-flush">
      <div class="list-group-item d-flex justify-content-between align-items-center gap-2 flex-wrap">
        <div>
          <a class="text-decoration-none fw-medium" href="{{ route('student.achievements.show', $a) }}">{{ $a->title }}</a>
          <div class="small text-muted">{{ $a->category?->name }} · updated {{ $a->updated_at->diffForHumans() }}</div>
        </div>
        @include('partials.status-badge', ['item' => $a])
      </div>
    </div>
  @empty
    <div class="card-body text-center py-5">
      <i class="bi bi-trophy display-5 text-muted"></i>
      <p class="mt-2 mb-1 fw-semibold">No achievements yet</p>
      <p class="text-muted small mb-3">Competitions, publications, certifications and internships all count. Submit your first one — it takes about two minutes.</p>
      <a href="{{ route('student.achievements.create') }}" class="btn btn-cse btn-sm">Submit your first achievement</a>
    </div>
  @endforelse
</div>
@endsection
