@extends('layouts.portal')
@section('title', $achievement->exists ? 'Edit Achievement' : 'New Achievement')
@php $portal = $portal ?? 'student'; @endphp
@section('content')
@php
  // SECURITY: the form never exposes status, owner, reviewer or privileged flags.
  $editable = !$achievement->exists || in_array($achievement->status, [\App\Enums\AchievementStatus::Draft, \App\Enums\AchievementStatus::Rejected], true);
@endphp
@if (!$editable)
  <div class="alert alert-warning"><i class="bi bi-lock me-1"></i>This submission is currently under review and cannot be edited.</div>
@endif
<h1 class="h4 fw-bold mb-3">{{ $achievement->exists ? 'Edit submission' : 'Submit an achievement' }}</h1>
<form method="POST" enctype="multipart/form-data"
      action="{{ $achievement->exists ? route($portal.'.achievements.update', $achievement) : route($portal.'.achievements.store') }}"
      class="card border-0 shadow-sm">
  @csrf
  @if($achievement->exists) @method('PUT') @endif
  <div class="card-body row g-3">
    <div class="col-md-8">
      <label for="title" class="form-label">Achievement title <span class="text-danger">*</span></label>
      <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $achievement->title) }}" required maxlength="200">
      @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
      <label for="achievement_category_id" class="form-label">Category <span class="text-danger">*</span></label>
      <select class="form-select @error('achievement_category_id') is-invalid @enderror" id="achievement_category_id" name="achievement_category_id" required>
        <option value="">Choose…</option>
        @foreach($categories as $c)
          <option value="{{ $c->id }}" @selected(old('achievement_category_id', $achievement->achievement_category_id) == $c->id)>{{ $c->name }}</option>
        @endforeach
      </select>
      @error('achievement_category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
      <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
      <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" required maxlength="5000">{{ old('description', $achievement->description) }}</textarea>
      <div class="form-text">What you achieved, when/where, and why it matters. Keep it factual — reviewers verify claims.</div>
      @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
      <label for="achievement_date" class="form-label">Date of achievement <span class="text-danger">*</span></label>
      <input type="date" class="form-control @error('achievement_date') is-invalid @enderror" id="achievement_date" name="achievement_date" value="{{ old('achievement_date', $achievement->achievement_date?->format('Y-m-d')) }}" required max="{{ date('Y-m-d') }}">
      @error('achievement_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
      <label for="issuing_organization" class="form-label">Issuing institution / organization <span class="text-danger">*</span></label>
      <input type="text" class="form-control @error('issuing_organization') is-invalid @enderror" id="issuing_organization" name="issuing_organization" value="{{ old('issuing_organization', $achievement->issuing_organization) }}" required maxlength="150">
      @error('issuing_organization')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
      <label for="level" class="form-label">Level</label>
      <select class="form-select" id="level" name="level">
        @foreach(['institute','regional','national','international'] as $l)
          <option value="{{ $l }}" @selected(old('level', $achievement->level ?? 'institute') === $l)>{{ ucfirst($l) }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-6">
      <label for="evidence" class="form-label">Supporting evidence (certificate / proof)</label>
      <input type="file" class="form-control @error('evidence') is-invalid @enderror" id="evidence" name="evidence" accept=".pdf,.jpg,.jpeg,.png" {{ $achievement->exists ? '' : 'required' }}>
      <div class="form-text">PDF, JPG or PNG · max 4 MB. Stored privately; only reviewers can open it.</div>
      @error('evidence')<div class="invalid-feedback">{{ $message }}</div>@enderror
      @if($achievement->exists && $achievement->documents->isNotEmpty())
        <ul class="small list-unstyled mt-1 mb-0">
          @foreach($achievement->documents as $d)
            <li><i class="bi bi-paperclip"></i> <a href="{{ route('documents.download', $d) }}">{{ $d->original_name }}</a></li>
          @endforeach
        </ul>
      @endif
    </div>
    <div class="col-md-6 d-flex align-items-end">
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="1" id="public_display_consent" name="public_display_consent" @checked(old('public_display_consent', $achievement->public_display_consent))>
        <label class="form-check-label" for="public_display_consent">I consent to this achievement being displayed publicly once approved and published.</label>
      </div>
    </div>
    @if($achievement->status === \App\Enums\AchievementStatus::Rejected)
      <div class="col-12"><div class="alert alert-danger mb-0"><strong>Reviewer feedback:</strong> {{ $achievement->rejection_feedback }}</div></div>
    @endif
  </div>
  <div class="card-footer bg-white d-flex flex-wrap gap-2 justify-content-end">
    <a href="{{ route($portal.'.achievements.index') }}" class="btn btn-outline-secondary me-auto">Cancel</a>
    <button type="submit" name="action" value="draft" class="btn btn-outline-cse" @disabled(!$editable)>Save as draft</button>
    <button type="submit" name="action" value="submit" class="btn btn-cse" @disabled(!$editable)>
      <i class="bi bi-send me-1"></i>{{ $achievement->status === \App\Enums\AchievementStatus::Rejected ? 'Resubmit for review' : 'Submit for review' }}
    </button>
  </div>
</form>
@endsection
