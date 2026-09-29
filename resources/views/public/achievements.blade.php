@extends('layouts.public')
@section('title', 'Achievements')

@section('content')
@include('public._page-head', ['title' => 'Achievements', 'subtitle' => 'Published, verified achievements of our students and faculty', 'icon' => 'bi-trophy'])
<div class="container pb-5">
    <form class="row g-2 mb-4" method="GET" action="{{ route('public.achievements') }}" role="search">
        <div class="col-md-5">
            <input class="form-control" type="search" name="q" value="{{ $search }}" placeholder="Search title or organization…" aria-label="Search achievements">
        </div>
        <div class="col-md-4">
            <select class="form-select" name="category" aria-label="Filter by category">
                <option value="">All categories</option>
                @foreach ($categories as $c)
                    <option value="{{ $c->slug }}" @selected(request('category') === $c->slug)>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button class="btn btn-cse flex-grow-1" type="submit"><i class="bi bi-search me-1"></i>Filter</button>
            @if ($search || request('category'))<a class="btn btn-outline-secondary" href="{{ route('public.achievements') }}">Clear</a>@endif
        </div>
    </form>

    @if ($items->isEmpty())
        <div class="empty-state py-5 text-center">
            <i class="bi bi-inboxes fs-1 text-muted"></i>
            <p class="mt-2 mb-0">No published achievements match your search.</p>
        </div>
    @else
        <div class="row g-3">
            @foreach ($items as $a)
                <div class="col-md-6 col-lg-4">
                    <article class="card h-100 border-0 shadow-sm achievement-card">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge text-bg-cse">{{ $a->category?->name }}</span>
                                <span class="text-muted small">{{ $a->achievement_date?->format('M Y') }}</span>
                            </div>
                            <h2 class="h6 mb-1"><a class="link-cse text-decoration-none stretched-link" href="{{ route('public.achievements.show', $a->id) }}">{{ $a->title }}</a></h2>
                            <p class="small text-muted mb-1">{{ $a->owner?->name }}</p>
                            <div class="mt-auto d-flex flex-wrap gap-2 small text-muted">
                                <span><i class="bi bi-award me-1"></i>{{ $a->level }}</span>
                                @if ($a->issuing_organization)<span><i class="bi bi-building me-1"></i>{{ Str::limit($a->issuing_organization, 24) }}</span>@endif
                            </div>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
        <div class="mt-4 d-flex justify-content-center">{{ $items->links() }}</div>
    @endif
</div>
@endsection
