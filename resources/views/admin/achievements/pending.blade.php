@extends('layouts.admin')
@section('title','Pending Reviews')
@section('content')
<h1 class="h4 fw-bold mb-1">Review queue</h1>
<p class="text-muted small">Submissions awaiting a decision. Open each one, verify the evidence file, then approve or reject with feedback.</p>
<div class="card border-0 shadow-sm">
  <div class="list-group list-group-flush">
    @forelse($items as $a)
      <div class="list-group-item d-flex justify-content-between align-items-center gap-3 flex-wrap py-3">
        <div>
          <a class="fw-semibold text-decoration-none" href="{{ route('admin.achievements.show', $a) }}">{{ $a->title }}</a>
          <div class="small text-muted">{{ $a->owner?->name }} ({{ $a->owner?->student?->roll_number ?? 'faculty' }}) · {{ $a->category?->name }} · level {{ $a->level }} · submitted {{ $a->submitted_at?->diffForHumans() }}</div>
        </div>
        <div class="d-flex gap-2">
          <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.achievements.show', $a) }}">Review</a>
          <a class="btn btn-sm btn-success" href="{{ route('admin.achievements.show', $a) }}#decision">Decide</a>
        </div>
      </div>
    @empty
      <div class="list-group-item text-center py-5 text-muted"><i class="bi bi-check2-circle display-6 d-block mb-2 text-success"></i>The queue is empty — every submission has been reviewed.</div>
    @endforelse
  </div>
  <div class="card-footer bg-white border-0">{{ $items->links() }}</div>
</div>
@endsection
