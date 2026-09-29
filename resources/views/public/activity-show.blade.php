@extends('layouts.public')
@section('title', $item->title)

@section('content')
@include('public._page-head', ['title' => $item->title, 'subtitle' => null, 'icon' => 'bi-calendar-event'])
<div class="container pb-5">
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm"><div class="card-body">
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="badge text-bg-cse text-capitalize">{{ str_replace('_', ' ', $item->type) }}</span>
                    <span class="badge text-bg-light border"><i class="bi bi-calendar3 me-1"></i>{{ $item->start_date?->format('d M Y') }}@if($item->end_date && $item->end_date->ne($item->start_date)) → {{ $item->end_date->format('d M Y') }}@endif</span>
                    @if ($item->venue)<span class="badge text-bg-light border"><i class="bi bi-geo-alt me-1"></i>{{ $item->venue }}</span>@endif
                </div>
                @if ($item->description)
                    <div>{!! nl2br(e($item->description)) !!}</div>
                @else
                    <p class="text-muted mb-0">Details for this event will be announced soon.</p>
                @endif
                @if ($item->organizer)
                    <hr><p class="small text-muted mb-0"><i class="bi bi-person-badge me-1"></i>Organized by {{ $item->organizer }}</p>
                @endif
            </div></div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm"><div class="card-body">
                <h2 class="h6">Related</h2>
                <p class="small text-muted">See more departmental events and updates:</p>
                <a href="{{ route('public.activities') }}" class="btn btn-outline-cse btn-sm w-100 mb-2">All Activities</a>
                <a href="{{ route('public.news') }}" class="btn btn-outline-cse btn-sm w-100">News &amp; Announcements</a>
            </div></div>
        </div>
    </div>
</div>
@endsection
