@extends('layouts.public')
@section('title', 'Student Directory')

@section('content')
@include('public._page-head', ['title' => 'Student Directory', 'subtitle' => 'Students who opted in to public display (privacy-consented)', 'icon' => 'bi-mortarboard'])
<div class="container pb-5">
    <form class="row g-2 mb-4" method="GET" action="{{ route('public.students') }}" role="search">
        <div class="col-md-6"><input class="form-control" type="search" name="q" value="{{ $search }}" placeholder="Search by roll number…" aria-label="Search students"></div>
        <div class="col-md-3"><button class="btn btn-cse w-100" type="submit"><i class="bi bi-search me-1"></i>Search</button></div>
        @if ($search)<div class="col-md-3"><a class="btn btn-outline-secondary w-100" href="{{ route('public.students') }}">Clear</a></div>@endif
    </form>

    @if ($items->isEmpty())
        <div class="empty-state py-5 text-center"><i class="bi bi-person-x fs-1 text-muted"></i><p class="mt-2 mb-0">No students have opted in to public display yet.</p></div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle bg-white shadow-sm rounded overflow-hidden">
                <thead><tr><th>Name</th><th>Roll Number</th><th>Programme</th><th>Batch</th></tr></thead>
                <tbody>
                    @foreach ($items as $s)
                        <tr>
                            <td class="fw-semibold">{{ $s->user?->name }}</td>
                            <td>{{ $s->roll_number }}</td>
                            <td class="small">{{ $s->program }}</td>
                            <td class="small">{{ $s->batch }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <p class="small text-muted"><i class="bi bi-shield-lock me-1"></i>Only students who enabled “public display consent” in their profile appear here. Contact details and academic records are never shown publicly.</p>
        <div class="mt-4 d-flex justify-content-center">{{ $items->links() }}</div>
    @endif
</div>
@endsection
