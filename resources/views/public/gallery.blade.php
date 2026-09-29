@extends('layouts.public')
@section('title', 'Gallery')

@section('content')
@include('public._page-head', ['title' => 'Photo Gallery', 'subtitle' => 'Moments from departmental events and campus life', 'icon' => 'bi-images'])
<div class="container pb-5">
    @if ($items->isEmpty())
        <div class="empty-state py-5 text-center"><i class="bi bi-image fs-1 text-muted"></i><p class="mt-2 mb-0">The gallery is being prepared — check back soon.</p></div>
    @else
        <div class="row g-3">
            @foreach ($items as $g)
                <div class="col-6 col-md-4 col-lg-3">
                    <figure class="mb-0">
                        <div class="gallery-thumb">
                            @if ($g->image_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($g->image_path))
                                <img src="{{ asset('storage/'.$g->image_path) }}" alt="{{ $g->title }}" class="w-100 h-100 object-fit-cover" loading="lazy">
                            @else
                                <span class="d-flex align-items-center justify-content-center w-100 h-100"><i class="bi bi-image text-cse fs-1"></i></span>
                            @endif
                        </div>
                        <figcaption class="small mt-1">
                            <span class="fw-semibold d-block">{{ $g->title }}</span>
                            @if ($g->caption)<span class="text-muted">{{ Str::limit($g->caption, 70) }}</span>@endif
                            @if ($g->taken_on)<span class="text-muted tiny d-block">{{ $g->taken_on->format('d M Y') }}</span>@endif
                        </figcaption>
                    </figure>
                </div>
            @endforeach
        </div>
        <div class="mt-4 d-flex justify-content-center">{{ $items->links() }}</div>
    @endif
</div>
@endsection
