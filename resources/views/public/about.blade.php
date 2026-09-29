@extends('layouts.public')
@section('title', 'About the Department')

@section('content')
@include('public._page-head', ['title' => 'About the Department', 'subtitle' => 'Computer Science & Engineering — Sample University College', 'icon' => 'bi-info-circle'])
<div class="container pb-5">
    <div class="row g-5">
        <div class="col-lg-8">
            <h2 class="h4 section-title">Our Mission</h2>
            <p>The Department of Computer Science &amp; Engineering is committed to providing quality education, fostering research culture, and developing industry-ready professionals in computing disciplines. This portal documents departmental activities, faculty profiles, student achievements, projects, publications, patents, and announcements transparently.</p>
            <p class="text-muted small"><em>Note: this development build contains clearly fictional sample content only. Official institutional information must be supplied by the department before production use.</em></p>

            <h2 class="h4 section-title mt-4">Programmes</h2>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-cse-subtle"><tr><th>Programme</th><th>Intake</th><th>Degree</th></tr></thead>
                    <tbody>
                        <tr><td>B.Tech — Computer Science &amp; Engineering</td><td>120</td><td>Undergraduate</td></tr>
                        <tr><td>M.Tech — Artificial Intelligence &amp; Data Engineering</td><td>36</td><td>Postgraduate</td></tr>
                    </tbody>
                </table>
            </div>

            <h2 class="h4 section-title mt-4">What this portal offers</h2>
            <div class="row g-3">
                @foreach ([
                    ['bi-trophy','Achievements','Students submit achievements for review; approved items are published here after display consent.'],
                    ['bi-people','Directories','Public faculty directory and an opt-in student listing.'],
                    ['bi-journal-text','Research','Published papers and patents filed or granted by faculty.'],
                    ['bi-images','Gallery & News','Photographs, events and official announcements.'],
                ] as [$ic,$t,$d])
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100"><div class="card-body">
                            <i class="bi {{ $ic }} fs-3 text-cse"></i>
                            <h3 class="h6 mt-2">{{ $t }}</h3><p class="small text-muted mb-0">{{ $d }}</p>
                        </div></div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-cse text-white fw-semibold"><i class="bi bi-mortarboard me-2"></i>Department at a glance</div>
                <ul class="list-group list-group-flush small">
                    <li class="list-group-item d-flex justify-content-between"><span>Established</span><strong>Sample year (fictional)</strong></li>
                    <li class="list-group-item d-flex justify-content-between"><span>Faculty members</span><strong>{{ \App\Models\Faculty::count() }}</strong></li>
                    <li class="list-group-item d-flex justify-content-between"><span>Registered students</span><strong>{{ \App\Models\Student::count() }}</strong></li>
                    <li class="list-group-item d-flex justify-content-between"><span>Published achievements</span><strong>{{ \App\Models\Achievement::published()->count() }}</strong></li>
                </ul>
            </div>
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body">
                    <h2 class="h6">Head of Department</h2>
                    <div class="avatar-circle mb-2">H</div>
                    <p class="mb-0 small">Prof. Fictional HOD<br><span class="text-muted">Professor &amp; Head, CSE Department</span></p>
                </div>
            </div>
            <a href="{{ route('contact') }}" class="btn btn-cse w-100 mt-3"><i class="bi bi-envelope me-1"></i>Contact the Department</a>
        </div>
    </div>
</div>
@endsection
