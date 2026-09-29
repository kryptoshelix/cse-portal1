@extends('layouts.public')
@section('title', 'Activities & Events')

@section('content')
@include('public._page-head', ['title' => 'Activities & Events', 'subtitle' => 'Workshops, seminars, guest lectures and departmental events', 'icon' => 'bi-calendar-event'])
<div class="container pb-5">
    <form class="row g-2 mb-4" method="GET" action="{{ route('public.activities') }}" role="search">
        <div class="col-md-6">
            <input class="form-control" type="search" name="q" value="{{ $search }}" placeholder="Search events…" aria-label="Search activities">
        </div>
        <div class="col-md-3">
            <button class="btn btn-cse w-100" type="submit"><i class="bi bi-search me-1"></i>Search</button>
        </div>
        @if ($search)
            <div class="col-md-3"><a class="btn btn-outline-secondary w-100" href="{{ route('public.activities') }}">Clear</a></div>
        @endif
    </form>

    @forelse ($items as $ac)
        <article class="card border-0 shadow-sm mb-3">
            <div class="card-body d-flex gap-3 align-items-start">
                <div class="date-chip flex-shrink-0 text-center">
                    <span class="d-block fw-bold">{{ $ac->start_date?->format('d') }}</span>
                    <span class="d-block small text-uppercase">{{ $ac->start_date?->format('M') }}</span>
                </div>
                <div>
                    <div class="d-flex flex-wrap gap-2 mb-1">
                        <span class="badge text-bg-cse text-capitalize">{{ str_replace('_', ' ', $ac->type) }}</span>
                        @if ($ac->featured)<span class="badge text-bg-warning">Featured</span>@endif
                    </div>
                    <h2 class="h5 mb-1"><a class="link-cse text-decoration-none" href="{{ route('public.activities.show', $ac->slug) }}">{{ $ac->title }}</a></h2>
                    <p class="small text-muted mb-1">
                        <i class="bi bi-geo-alt me-1"></i>{{ $ac->venue ?: 'Venue TBA' }}
                        @if ($ac->end_date && $ac->end_date->ne($ac->start_date)) — {{ $ac->end_date->format('d M Y') }}@endif
                        @if ($ac->organizer)<br><i class="bi bi-person-badge me-1"></i>Organizer: {{ $ac->organizer }}@endif
                    </p>
                    @if ($ac->description)<p class="mb-0 small">{{ Str::limit($ac->description, 220) }}</p>@endif
                </div>
            </div>
        </article>
    @empty
        <div class="empty-state py-5 text-center">
            <i class="bi bi-calendar-x fs-1 text-muted"></i>
            <p class="mt-2 mb-0">No published activities yet.</p>
        </div>
    @endforelse

    <div class="mt-4 d-flex justify-content-center">{{ $items->links() }}</div>
</div>
@endsection
