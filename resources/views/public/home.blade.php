@extends('layouts.public')
@section('title', 'Home')

@section('content')
{{-- Hero --}}
<section class="hero-cse text-white">
    <div class="container py-5">
        <div class="row align-items-center gy-4">
            <div class="col-lg-8">
                <p class="text-uppercase small fw-bold letter-spacing-1 mb-2 text-warning">Department of Computer Science &amp; Engineering</p>
                <h1 class="display-5 fw-bold mb-3">Excellence in Computing Education&nbsp;&amp; Research</h1>
                <p class="lead opacity-90 mb-4">The CSE Department at Sample University College nurtures skilled engineers, supports faculty research, and celebrates student achievement — all documented transparently through this portal.</p>
                <a class="btn btn-warning text-white btn-lg me-2" href="{{ route('public.achievements') }}"><i class="bi bi-trophy me-1"></i>Student Achievements</a>
                <a class="btn btn-outline-light btn-lg" href="{{ route('about') }}">About the Department</a>
            </div>
            <div class="col-lg-4 d-none d-lg-block">
                <div class="hero-card p-4 rounded-3">
                    <h2 class="h5 text-white fw-bold mb-3"><i class="bi bi-megaphone me-2 text-warning"></i>Latest Notice</h2>
                    @forelse ($news->take(2) as $n)
                        <a href="{{ route('public.news.show', $n->slug) }}" class="d-block text-white text-decoration-none border-bottom border-secondary pb-2 mb-2">
                            <span class="small text-warning">{{ $n->published_at?->format('d M Y') }}</span>
                            <span class="d-block">{{ Str::limit($n->heading, 60) }}</span>
                        </a>
                    @empty
                        <p class="small opacity-75 mb-0">No announcements yet.</p>
                    @endforelse
                    <a href="{{ route('public.news') }}" class="small text-warning">All news &rarr;</a>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container">
    {{-- Stats --}}
    <div class="row g-3 text-center my-4">
        @foreach ([
            ['bi-person-video3', $stats['faculty'], 'Faculty Members'],
            ['bi-mortarboard', $stats['students'], 'Registered Students'],
            ['bi-trophy', $stats['achievements'], 'Published Achievements'],
            ['bi-journal-text', $stats['publications'], 'Published Research Papers'],
        ] as [$ic, $val, $label])
            <div class="col-6 col-md-3">
                <div class="card stat-card border-0 shadow-sm h-100">
                    <div class="card-body py-4">
                        <i class="bi {{ $ic }} fs-2 text-cse"></i>
                        <div class="fs-3 fw-bold mt-2">{{ number_format($val) }}</div>
                        <div class="text-muted small">{{ $label }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Announcements --}}
    <section class="mb-5" aria-labelledby="sec-news">
        <div class="d-flex justify-content-between align-items-end mb-3">
            <h2 id="sec-news" class="h4 section-title mb-0">News &amp; Announcements</h2>
            <a href="{{ route('public.news') }}" class="small">View all <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="row g-3">
            @forelse ($news as $n)
                <div class="col-md-6">
                    <article class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex gap-2 mb-2">
                                @if ($n->pinned)<span class="badge text-bg-warning">Pinned</span>@endif
                                <span class="text-muted small"><i class="bi bi-calendar3 me-1"></i>{{ $n->published_at?->format('d M Y') }}</span>
                            </div>
                            <h3 class="h6"><a class="link-cse stretched-link text-decoration-none" href="{{ route('public.news.show', $n->slug) }}">{{ $n->heading }}</a></h3>
                            @if ($n->excerpt)<p class="text-muted small mb-0">{{ Str::limit($n->excerpt, 140) }}</p>@endif
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12"><p class="text-muted">No published announcements yet.</p></div>
            @endforelse
        </div>
    </section>

    {{-- Achievements --}}
    <section class="mb-5" aria-labelledby="sec-ach">
        <div class="d-flex justify-content-between align-items-end mb-3">
            <h2 id="sec-ach" class="h4 section-title mb-0">Recent Student Achievements</h2>
            <a href="{{ route('public.achievements') }}" class="small">View all <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="row g-3">
            @forelse ($achievements as $a)
                <div class="col-md-6 col-lg-4">
                    <article class="card h-100 border-0 shadow-sm achievement-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge text-bg-cse">{{ $a->category?->name }}</span>
                                <span class="text-muted small">{{ $a->achievement_date?->format('M Y') }}</span>
                            </div>
                            <h3 class="h6 mb-1"><a class="link-cse text-decoration-none stretched-link" href="{{ route('public.achievements.show', $a->id) }}">{{ $a->title }}</a></h3>
                            <p class="small text-muted mb-0">
                                {{ $a->owner?->name }}@if($a->issuing_organization) · {{ $a->issuing_organization }}@endif
                            </p>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12"><p class="text-muted">No achievements have been published yet.</p></div>
            @endforelse
        </div>
    </section>

    <div class="row g-4 mb-5">
        {{-- Projects --}}
        <div class="col-lg-6">
            <h2 class="h5 section-title mb-3">Featured Projects</h2>
            @forelse ($projects as $p)
                <article class="card border-0 shadow-sm mb-3">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between">
                            <h3 class="h6 mb-1"><a class="link-cse text-decoration-none" href="{{ route('public.projects.show', $p->id) }}">{{ $p->title }}</a></h3>
                            @if ($p->year)<span class="text-muted small">{{ $p->year }}</span>@endif
                        </div>
                        @if ($p->tech_stack)<p class="small text-muted mb-0">{{ $p->tech_stack }}</p>@endif
                    </div>
                </article>
            @empty
                <p class="text-muted small">No featured projects yet.</p>
            @endforelse
            <a href="{{ route('public.projects') }}" class="small">All projects <i class="bi bi-arrow-right"></i></a>
        </div>
        {{-- Activities --}}
        <div class="col-lg-6">
            <h2 class="h5 section-title mb-3">Upcoming &amp; Recent Activities</h2>
            @forelse ($activities as $ac)
                <article class="d-flex gap-3 mb-3">
                    <div class="date-chip flex-shrink-0 text-center">
                        <span class="d-block fw-bold">{{ $ac->start_date?->format('d') }}</span>
                        <span class="d-block small text-uppercase">{{ $ac->start_date?->format('M') }}</span>
                    </div>
                    <div>
                        <h3 class="h6 mb-1"><a class="link-cse text-decoration-none" href="{{ route('public.activities.show', $ac->slug) }}">{{ $ac->title }}</a></h3>
                        <span class="small text-muted"><i class="bi bi-geo-alt me-1"></i>{{ $ac->venue ?: 'TBA' }} · <span class="text-capitalize">{{ str_replace('_', ' ', $ac->type) }}</span></span>
                    </div>
                </article>
            @empty
                <p class="text-muted small">No activities published yet.</p>
            @endforelse
            <a href="{{ route('public.activities') }}" class="small">All activities <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>

    {{-- Faculty highlight + publications --}}
    <div class="row g-4 mb-5">
        <div class="col-lg-6">
            <h2 class="h5 section-title mb-3">Faculty Highlights</h2>
            <div class="row g-3">
                @forelse ($faculty as $f)
                    <div class="col-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center py-3">
                                <div class="avatar-circle mx-auto mb-2">{{ Str::of($f->user?->name ?? '?')->substr(0,1)->upper() }}</div>
                                <div class="fw-semibold small">{{ $f->user?->name }}</div>
                                <div class="text-muted small">{{ $f->designation }}</div>
                                @if ($f->specialization)<div class="text-muted tiny">{{ Str::limit($f->specialization, 40) }}</div>@endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12"><p class="text-muted small">Faculty profiles are being added.</p></div>
                @endforelse
            </div>
            <a href="{{ route('public.faculty') }}" class="small mt-2 d-inline-block">Full directory <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="col-lg-6">
            <h2 class="h5 section-title mb-3">Research Highlights</h2>
            @forelse ($publications as $pub)
                <article class="card border-0 shadow-sm mb-3">
                    <div class="card-body py-3">
                        <h3 class="h6 mb-1">{{ $pub->title }}</h3>
                        <p class="small text-muted mb-0">{{ $pub->authors }}@if($pub->journal_or_conference) — <em>{{ $pub->journal_or_conference }}</em>@endif</p>
                    </div>
                </article>
            @empty
                <p class="text-muted small">No publications listed yet.</p>
            @endforelse
            <a href="{{ route('public.publications') }}" class="small">All publications <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>

    {{-- Gallery preview --}}
    @if ($gallery->isNotEmpty())
        <section class="mb-5" aria-labelledby="sec-gal">
            <div class="d-flex justify-content-between align-items-end mb-3">
                <h2 id="sec-gal" class="h4 section-title mb-0">Gallery</h2>
                <a href="{{ route('public.gallery') }}" class="small">View gallery <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="row g-2">
                @foreach ($gallery as $g)
                    <div class="col-4 col-md-2">
                        <div class="gallery-thumb" title="{{ $g->title }}">
                            @if ($g->image_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($g->image_path))
                                <img src="{{ asset('storage/'.$g->image_path) }}" alt="{{ $g->title }}" class="w-100 h-100 object-fit-cover" loading="lazy">
                            @else
                                <span class="d-flex align-items-center justify-content-center w-100 h-100"><i class="bi bi-image text-cse fs-3"></i></span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
