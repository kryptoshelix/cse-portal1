@extends('layouts.public')
@section('title', $item->title)

@section('content')
@include('public._page-head', ['title' => $item->title, 'subtitle' => null, 'icon' => 'bi-trophy'])
<div class="container pb-5">
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm"><div class="card-body">
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="badge text-bg-cse">{{ $item->category?->name }}</span>
                    <span class="badge text-bg-light border">{{ $item->level }}</span>
                    <span class="badge text-bg-light border"><i class="bi bi-calendar3 me-1"></i>{{ $item->achievement_date?->format('d M Y') }}</span>
                </div>
                @if ($item->description)
                    <div class="mb-3">{!! nl2br(e($item->description)) !!}</div>
                @endif
                <dl class="row small mb-0">
                    <dt class="col-sm-4">Achieved by</dt><dd class="col-sm-8">{{ $item->owner?->name }}@if($item->student?->roll_number) ({{ $item->student->roll_number }})@endif</dd>
                    @if ($item->issuing_organization)
                        <dt class="col-sm-4">Issuing organization</dt><dd class="col-sm-8">{{ $item->issuing_organization }}</dd>
                    @endif
                    @if ($item->participants->isNotEmpty())
                        <dt class="col-sm-4">Participants</dt>
                        <dd class="col-sm-8">{{ $item->participants->pluck('name')->filter()->implode(', ') }}</dd>
                    @endif
                    <dt class="col-sm-4">Verified by</dt>
                    <dd class="col-sm-8">Department review — published {{ $item->published_at?->format('d M Y') }}</dd>
                </dl>
            </div></div>

            @if ($item->documents->isNotEmpty())
                <div class="card border-0 shadow-sm mt-4"><div class="card-body">
                    <h2 class="h6"><i class="bi bi-paperclip me-2"></i>Public supporting documents</h2>
                    <ul class="list-unstyled mb-0 small">
                        @foreach ($item->documents as $doc)
                            <li class="py-1 border-bottom"><i class="bi bi-file-earmark me-2 text-cse"></i>{{ $doc->original_name ?: $doc->file_name }}</li>
                        @endforeach
                    </ul>
                    <p class="small text-muted mt-2 mb-0">Certificates and other private evidence are visible only to the owner and reviewers.</p>
                </div></div>
            @endif
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm"><div class="card-body">
                <h2 class="h6">Want to get featured?</h2>
                <p class="small text-muted">Students submit achievements through their portal. After verification by the department (and with display consent), they appear on this page.</p>
                <a href="{{ route('register') }}" class="btn btn-cse btn-sm w-100 mb-2">Create a student account</a>
                <a href="{{ route('public.achievements') }}" class="btn btn-outline-secondary btn-sm w-100">Browse all achievements</a>
            </div></div>
        </div>
    </div>
</div>
@endsection
