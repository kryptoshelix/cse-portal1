@extends('layouts.public')
@section('title', 'Projects')

@section('content')
@include('public._page-head', ['title' => 'Student & Faculty Projects', 'subtitle' => 'Published project work from the department', 'icon' => 'bi-diagram'])
<div class="container pb-5">
    <form class="row g-2 mb-4" method="GET" action="{{ route('public.projects') }}" role="search">
        <div class="col-md-6">
            <input class="form-control" type="search" name="q" value="{{ $search }}" placeholder="Search projects…" aria-label="Search projects">
        </div>
        <div class="col-md-3"><button class="btn btn-cse w-100" type="submit"><i class="bi bi-search me-1"></i>Search</button></div>
        @if ($search)<div class="col-md-3"><a class="btn btn-outline-secondary w-100" href="{{ route('public.projects') }}">Clear</a></div>@endif
    </form>

    @if ($items->isEmpty())
        <div class="empty-state py-5 text-center">
            <i class="bi bi-diagram-3 fs-1 text-muted"></i>
            <p class="mt-2 mb-0">No published projects yet.</p>
        </div>
    @else
        <div class="row g-3">
            @foreach ($items as $p)
                <div class="col-md-6 col-lg-4">
                    <article class="card h-100 border-0 shadow-sm">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                @if ($p->category)<span class="badge text-bg-cse">{{ $p->category }}</span>@endif
                                @if ($p->year)<span class="text-muted small">{{ $p->year }}</span>@endif
                            </div>
                            <h2 class="h6 mb-1"><a class="link-cse text-decoration-none stretched-link" href="{{ route('public.projects.show', $p->id) }}">{{ $p->title }}</a></h2>
                            @if ($p->description)<p class="small text-muted flex-grow-1">{{ Str::limit($p->description, 120) }}</p>@endif
                            <div class="mt-auto small text-muted">
                                @if ($p->guide?->user)<i class="bi bi-person-video3 me-1"></i>{{ $p->guide->user->name }}@endif
                                @if ($p->tech_stack)<div class="tiny mt-1"><i class="bi bi-braces me-1"></i>{{ Str::limit($p->tech_stack, 40) }}</div>@endif
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
