@extends('layouts.public')
@section('title', 'News & Announcements')

@section('content')
@include('public._page-head', ['title' => 'News & Announcements', 'subtitle' => 'Official departmental updates', 'icon' => 'bi-megaphone'])
<div class="container pb-5">
    @forelse ($items as $n)
        <article class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                    @if ($n->pinned)<span class="badge text-bg-warning"><i class="bi bi-pin-angle me-1"></i>Pinned</span>@endif
                    <span class="text-muted small"><i class="bi bi-calendar3 me-1"></i>{{ $n->published_at?->format('d M Y') }}</span>
                </div>
                <h2 class="h5 mb-1"><a class="link-cse text-decoration-none stretched-link" href="{{ route('public.news.show', $n->slug) }}">{{ $n->heading }}</a></h2>
                @if ($n->excerpt)<p class="text-muted mb-0">{{ Str::limit($n->excerpt, 200) }}</p>@endif
            </div>
        </article>
    @empty
        <div class="empty-state py-5 text-center"><i class="bi bi-megaphone fs-1 text-muted"></i><p class="mt-2 mb-0">No announcements published yet.</p></div>
    @endforelse
    <div class="mt-4 d-flex justify-content-center">{{ $items->links() }}</div>
</div>
@endsection
