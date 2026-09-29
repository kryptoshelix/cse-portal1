@extends('layouts.public')
@section('title', $item->title)

@section('content')
@include('public._page-head', ['title' => $item->title, 'subtitle' => null, 'icon' => 'bi-diagram'])
<div class="container pb-5">
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm"><div class="card-body">
                <div class="d-flex flex-wrap gap-2 mb-3">
                    @if ($item->category)<span class="badge text-bg-cse">{{ $item->category }}</span>@endif
                    @if ($item->year)<span class="badge text-bg-light border">{{ $item->year }}</span>@endif
                    @if ($item->featured)<span class="badge text-bg-warning">Featured</span>@endif
                </div>
                @if ($item->description)
                    <div class="mb-3">{!! nl2br(e($item->description)) !!}</div>
                @endif
                <dl class="row small mb-0">
                    @if ($item->members)
                        <dt class="col-sm-4">Team members</dt><dd class="col-sm-8">{{ $item->members }}</dd>
                    @endif
                    @if ($item->guide?->user)
                        <dt class="col-sm-4">Guided by</dt><dd class="col-sm-8">{{ $item->guide->user->name }}@if($item->guide->designation) — {{ $item->guide->designation }}@endif</dd>
                    @endif
                    @if ($item->tech_stack)
                        <dt class="col-sm-4">Tech stack</dt><dd class="col-sm-8">{{ $item->tech_stack }}</dd>
                    @endif
                </dl>
            </div></div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm"><div class="card-body">
                <h2 class="h6">Explore more</h2>
                <a href="{{ route('public.projects') }}" class="btn btn-outline-cse btn-sm w-100 mb-2">All Projects</a>
                <a href="{{ route('public.achievements') }}" class="btn btn-outline-cse btn-sm w-100">Achievements</a>
            </div></div>
        </div>
    </div>
</div>
@endsection
