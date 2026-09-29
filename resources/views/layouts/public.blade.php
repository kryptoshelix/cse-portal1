@extends('layouts.base')

@section('body')
<nav class="navbar navbar-expand-lg navbar-cse sticky-top" aria-label="Main navigation">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
            <span class="brand-mark">CSE</span>
            <span>Dept. Portal<small class="d-block fw-normal opacity-75" style="font-size:.72rem">Computer Science &amp; Engineering</small></span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#publicNav"
                aria-controls="publicNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="publicNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                @php
                    $links = [
                        ['Home', route('home'), request()->routeIs('home')],
                        ['About', route('about'), request()->routeIs('about')],
                        ['Faculty', route('public.faculty'), request()->routeIs('public.faculty')],
                        ['Students', route('public.students'), request()->routeIs('public.students')],
                        ['Achievements', route('public.achievements'), request()->routeIs('public.achievements*')],
                        ['Activities', route('public.activities'), request()->routeIs('public.activities*')],
                        ['Projects', route('public.projects'), request()->routeIs('public.projects*')],
                        ['Research', route('public.publications'), request()->routeIs('public.publications*', 'public.patents*')],
                        ['Patents', route('public.patents'), request()->routeIs('public.patents*')],
                        ['Gallery', route('public.gallery'), request()->routeIs('public.gallery')],
                        ['News', route('public.news'), request()->routeIs('public.news*')],
                        ['Contact', route('contact'), request()->routeIs('contact')],
                    ];
                @endphp
                @foreach ($links as [$label, $href, $active])
                    <li class="nav-item"><a class="nav-link {{ $active ? 'active' : '' }}" href="{{ $href }}">{{ $label }}</a></li>
                @endforeach
                <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
                    @auth
                        <a class="btn btn-sm btn-outline-light" href="{{ App\Enums\UserRole::dashboardRoute(auth()->user()) }}">
                            <i class="bi bi-speedometer2 me-1"></i>My Dashboard
                        </a>
                    @else
                        <a class="btn btn-sm btn-outline-light me-1" href="{{ route('login') }}">Login</a>
                        <a class="btn btn-sm btn-warning text-white" href="{{ route('register') }}">Register</a>
                    @endauth
                </li>
            </ul>
        </div>
    </div>
</nav>

<main id="main-content">
@yield('content')
</main>

<footer class="footer-cse mt-5 pt-5 pb-4">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-5">
                <h6 class="text-white fw-bold">Department of Computer Science &amp; Engineering</h6>
                <p class="mb-1 small">Sample University College — Academic Block C,<br>Main Campus Road, Sample City 000000.</p>
                <p class="small mb-0"><i class="bi bi-envelope me-1"></i>cse@sample-university.example<br>
                   <i class="bi bi-telephone me-1"></i>+1 555 010 0000</p>
            </div>
            <div class="col-6 col-lg-3">
                <h6 class="text-white fw-bold">Quick Links</h6>
                <ul class="list-unstyled small">
                    <li><a href="{{ route('about') }}">About the Department</a></li>
                    <li><a href="{{ route('public.faculty') }}">Faculty Directory</a></li>
                    <li><a href="{{ route('public.achievements') }}">Achievements</a></li>
                    <li><a href="{{ route('public.news') }}">News &amp; Announcements</a></li>
                    <li><a href="{{ route('contact') }}">Contact Us</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-4">
                <h6 class="text-white fw-bold">Portal Access</h6>
                <ul class="list-unstyled small">
                    <li><a href="{{ route('login') }}">Student / Faculty Login</a></li>
                    <li><a href="{{ route('register') }}">Create an Account</a></li>
                    <li><a href="{{ route('password.request') }}">Forgot Password</a></li>
                </ul>
                <p class="small mb-0 mt-2 opacity-75">All content shown in this development build is fictional sample data.</p>
            </div>
        </div>
        <hr class="border-secondary my-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center small">
            <span>&copy; {{ date('Y') }} CSE Department Portal (development build).</span>
            <span>Built with Laravel &amp; Bootstrap 5.</span>
        </div>
    </div>
</footer>
@endsection
