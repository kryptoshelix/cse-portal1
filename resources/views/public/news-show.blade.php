@extends('layouts.public')
@section('title', $item->heading)

@section('content')
@include('public._page-head', ['title' => $item->heading, 'subtitle' => null, 'icon' => 'bi-megaphone'])
<div class="container pb-5">
    <div class="row g-4 justify-content-center">
        <div class="col-lg-8">
            <article class="card border-0 shadow-sm"><div class="card-body">
                <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                    @if ($item->pinned)<span class="badge text-bg-warning"><i class="bi bi-pin-angle me-1"></i>Pinned</span>@endif
                    <span class="text-muted small"><i class="bi bi-calendar3 me-1"></i>Published {{ $item->published_at?->format('d M Y') }}</span>
                    @if ($item->author)<span class="text-muted small"><i class="bi bi-person me-1"></i>{{ $item->author->name }}</span>@endif
                </div>
                @if ($item->body)
                    <div>{!! nl2br(e($item->body)) !!}</div>
                @elseif ($item->excerpt)
                    <p>{{ $item->excerpt }}</p>
                @else
                    <p class="text-muted mb-0">Details to be announced.</p>
                @endif
            </div></article>
            <a href="{{ route('public.news') }}" class="btn btn-outline-cse btn-sm mt-3"><i class="bi bi-arrow-left me-1"></i>All News</a>
        </div>
    </div>
</div>
@endsection
