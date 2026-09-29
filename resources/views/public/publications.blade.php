@extends('layouts.public')
@section('title', 'Publications')

@section('content')
@include('public._page-head', ['title' => 'Research Publications', 'subtitle' => 'Peer-reviewed papers published by department faculty', 'icon' => 'bi-journal-text'])
<div class="container pb-5">
    <form class="row g-2 mb-4" method="GET" action="{{ route('public.publications') }}" role="search">
        <div class="col-md-6"><input class="form-control" type="search" name="q" value="{{ $search }}" placeholder="Search publications…" aria-label="Search publications"></div>
        <div class="col-md-3"><button class="btn btn-cse w-100" type="submit"><i class="bi bi-search me-1"></i>Search</button></div>
        @if ($search)<div class="col-md-3"><a class="btn btn-outline-secondary w-100" href="{{ route('public.publications') }}">Clear</a></div>@endif
    </form>

    @forelse ($items as $pub)
        <article class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <h2 class="h6 mb-1">{{ $pub->title }}</h2>
                <p class="small text-muted mb-1"><i class="bi bi-people me-1"></i>{{ $pub->authors }}</p>
                <div class="small text-muted d-flex flex-wrap gap-3">
                    @if ($pub->journal_or_conference)<span><i class="bi bi-journal me-1"></i><em>{{ $pub->journal_or_conference }}</em>@if($pub->volume_issue), {{ $pub->volume_issue }}@endif</span>@endif
                    @if ($pub->publication_date)<span><i class="bi bi-calendar3 me-1"></i>{{ $pub->publication_date->format('M Y') }}</span>@endif
                    @if ($pub->doi)<span><i class="bi bi-link-45deg me-1"></i>DOI: {{ $pub->doi }}</span>@endif
                    @if ($pub->url)<a href="{{ $pub->url }}" rel="noopener nofollow" target="_blank"><i class="bi bi-box-arrow-up-right me-1"></i>View paper</a>@endif
                    @if ($pub->faculty?->user)<span><i class="bi bi-person-video3 me-1"></i>Corresponding: {{ $pub->faculty->user->name }}</span>@endif
                </div>
                @if ($pub->abstract)
                    <details class="mt-2 small"><summary class="text-cse">Abstract</summary><p class="text-muted mt-1 mb-0">{{ $pub->abstract }}</p></details>
                @endif
            </div>
        </article>
    @empty
        <div class="empty-state py-5 text-center"><i class="bi bi-journal-x fs-1 text-muted"></i><p class="mt-2 mb-0">No publications have been listed yet.</p></div>
    @endforelse

    <div class="mt-4 d-flex justify-content-center">{{ $items->links() }}</div>
</div>
@endsection
