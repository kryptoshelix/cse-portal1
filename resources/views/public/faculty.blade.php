@extends('layouts.public')
@section('title', 'Faculty Directory')

@section('content')
@include('public._page-head', ['title' => 'Faculty Directory', 'subtitle' => 'Meet the teachers and researchers of the CSE department', 'icon' => 'bi-person-video3'])
<div class="container pb-5">
    <form class="row g-2 mb-4" method="GET" action="{{ route('public.faculty') }}" role="search">
        <div class="col-md-6"><input class="form-control" type="search" name="q" value="{{ $search }}" placeholder="Search by name…" aria-label="Search faculty"></div>
        <div class="col-md-3"><button class="btn btn-cse w-100" type="submit"><i class="bi bi-search me-1"></i>Search</button></div>
        @if ($search)<div class="col-md-3"><a class="btn btn-outline-secondary w-100" href="{{ route('public.faculty') }}">Clear</a></div>@endif
    </form>

    @if ($items->isEmpty())
        <div class="empty-state py-5 text-center"><i class="bi bi-people fs-1 text-muted"></i><p class="mt-2 mb-0">Faculty profiles are being added.</p></div>
    @else
        <div class="row g-3">
            @foreach ($items as $f)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body d-flex gap-3">
                            <div class="avatar-circle flex-shrink-0">{{ Str::of($f->user?->name ?? '?')->substr(0,1)->upper() }}</div>
                            <div>
                                <h2 class="h6 mb-0">{{ $f->user?->name }}</h2>
                                <div class="text-cse small fw-semibold">{{ $f->designation }}</div>
                                @if ($f->employee_id)<div class="tiny text-muted">ID: {{ $f->employee_id }}</div>@endif
                                @if ($f->specialization)<div class="small mt-1"><i class="bi bi-cpu me-1 text-cse"></i>{{ $f->specialization }}</div>@endif
                                @if ($f->qualification)<div class="tiny text-muted">{{ $f->qualification }}</div>@endif
                                @if ($f->bio)<p class="small text-muted mt-2 mb-0">{{ Str::limit($f->bio, 110) }}</p>@endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4 d-flex justify-content-center">{{ $items->links() }}</div>
    @endif
</div>
@endsection
