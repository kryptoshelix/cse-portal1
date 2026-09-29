@extends('layouts.portal')
@section('title', $achievement->title)
@php $portal = $portal ?? 'student'; @endphp
@section('content')
<nav aria-label="breadcrumb"><ol class="breadcrumb small">
  <li class="breadcrumb-item"><a class="text-decoration-none" href="{{ route($portal.'.achievements.index') }}">My achievements</a></li>
  <li class="breadcrumb-item active">{{ Str::limit($achievement->title, 40) }}</li>
</ol></nav>
<div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
  <h1 class="h4 fw-bold mb-0">{{ $achievement->title }} @include('partials.status-badge', ['item' => $achievement])</h1>
  <div class="d-flex gap-2">
    @can('update', $achievement)
      <a class="btn btn-sm btn-outline-primary" href="{{ route($portal.'.achievements.edit', $achievement) }}"><i class="bi bi-pencil me-1"></i>Edit</a>
    @endcan
    @can('submit', $achievement)
      @include('partials.confirm-form', ['action'=>route($portal.'.achievements.submit',$achievement),'btnClass'=>'btn-cse','confirm'=>'Submit this draft for administrator review?','slot'=>'<i class="bi bi-send me-1"></i>Submit for review'])
    @endcan
  </div>
</div>

@if($achievement->status === \App\Enums\AchievementStatus::Rejected && $achievement->rejection_feedback)
  <div class="alert alert-danger"><strong><i class="bi bi-x-circle me-1"></i>Review outcome — rejected ({{ $achievement->reviewed_at?->format('d M Y') }}):</strong><br>{{ $achievement->rejection_feedback }}
    @can('update', $achievement)<div class="mt-2"><a class="btn btn-sm btn-danger" href="{{ route($portal.'.achievements.edit', $achievement) }}">Fix &amp; resubmit</a></div>@endcan
  </div>
@endif
@if($achievement->status === \App\Enums\AchievementStatus::Published)
  <div class="alert alert-success"><i class="bi bi-broadcast me-1"></i>This achievement is live on the public department website. <a href="{{ route('public.achievements.show', $achievement) }}">View public page →</a></div>
@endif

<div class="row g-3">
  <div class="col-lg-8">
    <div class="card border-0 shadow-sm h-100"><div class="card-body">
      <h2 class="h6 text-uppercase text-muted">Description</h2>
      <p class="mb-4">{{ $achievement->description }}</p>
      <dl class="row mb-0 small">
        <dt class="col-sm-4">Category</dt><dd class="col-sm-8">{{ $achievement->category?->name ?? '—' }}</dd>
        <dt class="col-sm-4">Achieved on</dt><dd class="col-sm-8">{{ $achievement->achievement_date?->format('d F Y') ?? '—' }}</dd>
        <dt class="col-sm-4">Issued by</dt><dd class="col-sm-8">{{ $achievement->issuing_organization }}</dd>
        <dt class="col-sm-4">Level</dt><dd class="col-sm-8 text-capitalize">{{ $achievement->level }}</dd>
        <dt class="col-sm-4">Public display consent</dt><dd class="col-sm-8">{{ $achievement->public_display_consent ? 'Given' : 'Not given' }}</dd>
        <dt class="col-sm-4">Submitted</dt><dd class="col-sm-8">{{ $achievement->submitted_at?->format('d M Y, H:i') ?? 'Not yet submitted' }}</dd>
        @if($achievement->reviewer)
          <dt class="col-sm-4">Last reviewed by</dt><dd class="col-sm-8">{{ $achievement->reviewer->name }} · {{ $achievement->reviewed_at?->format('d M Y, H:i') }}</dd>
        @endif
      </dl>
    </div></div>
  </div>
  <div class="col-lg-4">
    <div class="card border-0 shadow-sm"><div class="card-body">
      <h2 class="h6 text-uppercase text-muted"><i class="bi bi-paperclip me-1"></i>Evidence documents</h2>
      @forelse($achievement->documents as $d)
        <a class="d-block text-decoration-none py-2 border-bottom" href="{{ route('documents.download', $d) }}">
          <i class="bi bi-file-earmark-text me-1 text-cse-dark"></i>{{ $d->original_name }}
          <span class="d-block small text-muted">{{ number_format($d->size/1024, 1) }} KB · private download</span>
        </a>
      @empty
        <p class="text-muted small mb-0">No files attached.</p>
      @endforelse
    </div></div>
    <div class="card border-0 shadow-sm mt-3"><div class="card-body timeline-card">
      <h2 class="h6 text-uppercase text-muted">Workflow</h2>
      <ol class="workflow-steps small mb-0">
        <li class="{{ $achievement->created_at ? 'done' : '' }}">Created {{ $achievement->created_at?->format('d M Y') }}</li>
        <li class="{{ $achievement->submitted_at ? 'done' : '' }}">Submitted for review {{ $achievement->submitted_at?->format('d M Y') }}</li>
        <li class="{{ in_array($achievement->status, [\App\Enums\AchievementStatus::Approved, \App\Enums\AchievementStatus::Published]) ? 'done' : '' }}">Approved {{ $achievement->approved_at?->format('d M Y') }}</li>
        <li class="{{ $achievement->status === \App\Enums\AchievementStatus::Published ? 'done' : '' }}">Published on public site</li>
      </ol>
    </div></div>
  </div>
</div>
@endsection
