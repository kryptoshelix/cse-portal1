@extends('layouts.admin')
@section('title','Review Achievement')
@section('content')
<nav aria-label="breadcrumb"><ol class="breadcrumb small">
  <li class="breadcrumb-item"><a class="text-decoration-none" href="{{ route('admin.achievements.index') }}">Achievements</a></li>
  <li class="breadcrumb-item active">Review #{{ $achievement->id }}</li>
</ol></nav>
<div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
  <h1 class="h4 fw-bold mb-0">{{ $achievement->title }} @include('partials.status-badge', ['item'=>$achievement])</h1>
  <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.achievements.edit', $achievement) }}"><i class="bi bi-pencil me-1"></i>Edit record</a>
</div>
<div class="row g-3">
  <div class="col-lg-7">
    <div class="card border-0 shadow-sm"><div class="card-body">
      <h2 class="h6 text-uppercase text-muted">Submission</h2>
      <p>{{ $achievement->description }}</p>
      <dl class="row small mb-0">
        <dt class="col-sm-4">Submitted by</dt><dd class="col-sm-8"><a href="{{ route('admin.accounts.show', $achievement->user_id) }}">{{ $achievement->owner?->name }}</a> ({{ $achievement->owner?->role->label() }}{{ $achievement->owner?->student?->roll_number ? ', '.$achievement->owner->student->roll_number : '' }})</dd>
        <dt class="col-sm-4">Category</dt><dd class="col-sm-8">{{ $achievement->category?->name }}</dd>
        <dt class="col-sm-4">Date / Level</dt><dd class="col-sm-8">{{ $achievement->achievement_date?->format('d M Y') }} · {{ ucfirst($achievement->level) }}</dd>
        <dt class="col-sm-4">Issuing org.</dt><dd class="col-sm-8">{{ $achievement->issuing_organization }}</dd>
        <dt class="col-sm-4">Consent for public display</dt><dd class="col-sm-8">{{ $achievement->public_display_consent ? 'Yes' : 'No — do not publish' }}</dd>
        <dt class="col-sm-4">Submitted at</dt><dd class="col-sm-8">{{ $achievement->submitted_at?->format('d M Y, H:i') ?? '—' }}</dd>
      </dl>
    </div></div>
    <div class="card border-0 shadow-sm mt-3"><div class="card-body">
      <h2 class="h6 text-uppercase text-muted"><i class="bi bi-paperclip me-1"></i>Evidence (private)</h2>
      @forelse($achievement->documents as $d)
        <a class="d-block py-2 border-bottom text-decoration-none" href="{{ route('documents.download', $d) }}"><i class="bi bi-file-earmark-text me-1 text-cse-dark"></i>{{ $d->original_name }} <span class="small text-muted">· {{ number_format($d->size/1024,1) }} KB · verified {{ $d->verified_at ? 'yes' : 'not yet' }}</span></a>
      @empty
        <p class="text-muted small mb-0">No evidence files attached.</p>
      @endforelse
    </div></div>
  </div>
  <div class="col-lg-5">
    @if($achievement->status === \App\Enums\AchievementStatus::Pending)
      <div id="decision" class="card border-0 shadow-sm"><div class="card-body">
        <h2 class="h6 text-uppercase text-muted">Review decision</h2>
        <form method="POST" action="{{ route('admin.achievements.approve', $achievement) }}" class="mb-4">
          @csrf
          <div class="mb-2"><label class="form-label small">Verification remarks (optional)</label>
            <textarea name="remarks" rows="2" class="form-control" placeholder="e.g. Certificate serial matched with issuer."></textarea></div>
          <button class="btn btn-success w-100"><i class="bi bi-check-lg me-1"></i>Approve</button>
        </form>
        <form method="POST" action="{{ route('admin.achievements.reject', $achievement) }}">
          @csrf
          <div class="mb-2"><label class="form-label small">Reason for rejection <span class="text-danger">*</span></label>
            <textarea name="reason" rows="2" class="form-control @error('reason') is-invalid @enderror" required placeholder="Explain what is wrong and how to fix it — the submitter sees this.">{{ old('reason') }}</textarea>
            @error('reason')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
          <button class="btn btn-danger w-100"><i class="bi bi-x-lg me-1"></i>Reject with feedback</button>
        </form>
      </div></div>
    @else
      <div class="card border-0 shadow-sm"><div class="card-body">
        <h2 class="h6 text-uppercase text-muted">Last decision</h2>
        <p class="small mb-1">@include('partials.status-badge', ['item'=>$achievement])</p>
        <p class="small text-muted">By {{ $achievement->reviewer?->name ?? '—' }} on {{ $achievement->reviewed_at?->format('d M Y, H:i') }}</p>
        @if($achievement->rejection_feedback)<div class="alert alert-danger small mb-0">{{ $achievement->rejection_feedback }}</div>@endif
        @if($achievement->verification_remarks)<div class="alert alert-success small mb-0">{{ $achievement->verification_remarks }}</div>@endif
      </div></div>
    @endif
    @if(in_array($achievement->status, [\App\Enums\AchievementStatus::Approved, \App\Enums\AchievementStatus::Published], true))
      <div class="card border-0 shadow-sm mt-3"><div class="card-body">
        <h2 class="h6 text-uppercase text-muted">Publishing</h2>
        @if($achievement->status === \App\Enums\AchievementStatus::Published)
          <p class="small">Published {{ $achievement->published_at?->format('d M Y') }}.
            <a href="{{ route('public.achievements.show', $achievement) }}">View on site →</a></p>
          @include('partials.confirm-form', ['action'=>route('admin.achievements.unpublish',$achievement),'btnClass'=>'btn-outline-secondary','confirm'=>'Remove from the public website? The record stays approved.','slot'=>'Unpublish'])
        @elseif(!$achievement->public_display_consent)
          <div class="alert alert-warning small mb-0"><i class="bi bi-exclamation-triangle me-1"></i>The owner has <strong>not consented</strong> to public display, so publishing is disabled.</div>
        @else
          <button class="btn btn-cse w-100" data-bs-toggle="modal" data-bs-target="#publishModal"><i class="bi bi-broadcast me-1"></i>Publish to website…</button>
        @endif
      </div></div>
    @endif
  </div>
</div>
@if($achievement->status === \App\Enums\AchievementStatus::Approved && $achievement->public_display_consent)
<div class="modal fade" id="publishModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content">
  <form method="POST" action="{{ route('admin.achievements.publish', $achievement) }}">
    @csrf
    <div class="modal-header"><h5 class="modal-title">Publish achievement</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
    <div class="modal-body">
      <div class="mb-3"><label class="form-label">Public headline</label><input name="headline" class="form-control" maxlength="150" placeholder="{{ $achievement->title }}"></div>
      <div class="mb-3"><label class="form-label">Public summary (optional)</label><textarea name="summary" rows="3" class="form-control" maxlength="500"></textarea></div>
      <div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="featured" value="1" id="featChk" @checked(old('featured'))><label class="form-check-label" for="featChk">Feature on the home page</label></div>
      <div class="form-check"><input class="form-check-input" type="checkbox" name="display_roll_number" value="1" id="rollChk" @checked(!old('_uncheck'))><label class="form-check-label" for="rollChk">Show roll number alongside the name</label></div>
    </div>
    <div class="modal-footer"><button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Cancel</button><button class="btn btn-cse">Publish now</button></div>
  </form>
</div></div></div>
@endif
@endsection
