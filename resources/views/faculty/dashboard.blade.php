@extends('layouts.portal')
@section('title','Faculty Dashboard')
@php $portal = 'faculty'; @endphp
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
  <div>
    <h1 class="h4 fw-bold mb-1">{{ auth()->user()->name }}</h1>
    <p class="text-muted small mb-0">{{ auth()->user()->facultyProfile?->designation }} · {{ auth()->user()->facultyProfile?->specialization }}</p>
  </div>
  <a href="{{ route('faculty.achievements.create') }}" class="btn btn-cse"><i class="bi bi-plus-lg me-1"></i>New submission</a>
</div>

<div class="row g-3 mb-4">
  @if(auth()->user()->canReviewAchievements())
    <div class="col-6 col-md-3">
      <div class="card stat-card cse-stat-3 border-0 shadow-sm"><div class="card-body text-center py-3">
        <i class="bi bi-clipboard-check fs-3"></i><div class="fs-2 fw-bold">{{ $reviewQueue }}</div>
        <div class="small text-muted">Department review queue<br>(you are an authorized reviewer)</div>
      </div></div>
    </div>
  @endif
  @foreach([
    ['My submissions', $counts->sum(), 'bi-folder2-open'],
    ['Drafts', $counts['draft'] ?? 0, 'bi-pencil'],
    ['Pending', $counts['pending'] ?? 0, 'bi-hourglass-split'],
    ['Rejected', $counts['rejected'] ?? 0, 'bi-x-circle'],
    ['Approved', $counts['approved'] ?? 0, 'bi-check-circle'],
    ['Published', $counts['published'] ?? 0, 'bi-broadcast'],
  ] as [$label, $value, $icon])
    <div class="col-6 col-md-3 col-xl-2">
      <div class="card stat-card border-0 shadow-sm"><div class="card-body text-center py-3">
        <i class="bi {{ $icon }} fs-3 opacity-75"></i><div class="fs-2 fw-bold lh-1 mt-1">{{ $value }}</div>
        <div class="small text-muted">{{ $label }}</div>
      </div></div>
    </div>
  @endforeach
</div>

<div class="card border-0 shadow-sm">
  <div class="card-header bg-white d-flex justify-content-between align-items-center">
    <span class="fw-semibold">My recent submissions</span>
    <a class="small" href="{{ route('faculty.achievements.index') }}">View all →</a>
  </div>
  @forelse ($recent as $a)
    <div class="list-group list-group-flush">
      <div class="list-group-item d-flex justify-content-between align-items-center gap-2 flex-wrap">
        <div>
          <a class="text-decoration-none fw-medium" href="{{ route('faculty.achievements.show', $a) }}">{{ $a->title }}</a>
          <div class="small text-muted">{{ $a->category?->name }} · updated {{ $a->updated_at->diffForHumans() }}</div>
        </div>
        @include('partials.status-badge', ['item' => $a])
      </div>
    </div>
  @empty
    <div class="card-body text-center py-5">
      <i class="bi bi-folder2-open display-5 text-muted"></i>
      <p class="mt-2 mb-1 fw-semibold">You have no submissions yet</p>
      <p class="text-muted small mb-3">Record your own publications-in-practice, mentorship awards, FDP completions and similar achievements here.</p>
      <a href="{{ route('faculty.achievements.create') }}" class="btn btn-cse btn-sm">Create your first submission</a>
    </div>
  @endforelse
</div>
@endsection
