@extends('layouts.public')
@section('title', 'Patents')

@section('content')
@include('public._page-head', ['title' => 'Patents', 'subtitle' => 'Patent applications and grants by department faculty', 'icon' => 'bi-file-earmark-medical'])
<div class="container pb-5">
    <form class="row g-2 mb-4" method="GET" action="{{ route('public.patents') }}" role="search">
        <div class="col-md-6"><input class="form-control" type="search" name="q" value="{{ $search }}" placeholder="Search patents…" aria-label="Search patents"></div>
        <div class="col-md-3"><button class="btn btn-cse w-100" type="submit"><i class="bi bi-search me-1"></i>Search</button></div>
        @if ($search)<div class="col-md-3"><a class="btn btn-outline-secondary w-100" href="{{ route('public.patents') }}">Clear</a></div>@endif
    </form>

    <div class="table-responsive">
        <table class="table table-hover align-middle bg-white shadow-sm rounded overflow-hidden">
            <thead><tr><th>Title</th><th>Inventors</th><th>Patent No.</th><th>Filed</th><th>Status</th></tr></thead>
            <tbody>
                @forelse ($items as $pt)
                    <tr>
                        <td class="fw-semibold">{{ $pt->title }}</td>
                        <td class="small">{{ $pt->inventors }}@if($pt->faculty?->user)<div class="text-muted tiny">Dept. contact: {{ $pt->faculty->user->name }}</div>@endif</td>
                        <td class="small">{{ $pt->patent_number ?: '—' }}</td>
                        <td class="small">{{ $pt->filing_date?->format('d M Y') ?: '—' }}</td>
                        <td>
                            @if ($pt->filing_status === 'granted')
                                <span class="badge text-bg-success">Granted</span>
                            @else
                                <span class="badge text-bg-warning">Applied</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-5"><i class="bi bi-inboxes d-block fs-1 mb-2"></i>No patents have been published yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4 d-flex justify-content-center">{{ $items->links() }}</div>
</div>
@endsection
