<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title','Dashboard') — {{ ucfirst($portal ?? 'Portal') }} Portal</title>
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/portal.css') }}">
</head>
<body class="portal-body">
<a class="skip-link" href="#main-content">Skip to main content</a>
<nav class="navbar navbar-cse-dark sticky-top" aria-label="Portal navigation">
  <div class="container-fluid px-3">
    <button class="btn btn-sm btn-outline-light d-lg-none me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#portalSidebar" aria-controls="portalSidebar" aria-label="Open menu"><i class="bi bi-list"></i></button>
    <a class="navbar-brand fw-bold" href="{{ route(($portal ?? 'student').'.dashboard') }}">
      <span class="brand-mark">CSE</span> {{ ucfirst($portal ?? 'user') }} Portal
    </a>
    <div class="dropdown ms-auto">
      <a class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
        <span class="avatar-circle me-2">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</span>
        <span class="d-none d-sm-inline">{{ auth()->user()->name }}</span>
      </a>
      <ul class="dropdown-menu dropdown-menu-end shadow">
        <li><h6 class="dropdown-header">{{ auth()->user()->role->label() }}</h6></li>
        <li><a class="dropdown-item" href="{{ route(($portal ?? 'student').'.profile') }}"><i class="bi bi-person me-2"></i>My profile</a></li>
        <li><a class="dropdown-item" href="{{ route('home') }}"><i class="bi bi-globe me-2"></i>Public website</a></li>
        <li><hr class="dropdown-divider"></li>
        <li>
          <form method="POST" action="{{ route('logout') }}">@csrf
            <button class="dropdown-item text-danger" type="submit"><i class="bi bi-box-arrow-right me-2"></i>Sign out</button>
          </form>
        </li>
      </ul>
    </div>
  </div>
</nav>
<div class="portal-shell">
  <aside class="portal-sidebar offcanvas-lg offcanvas-start" tabindex="-1" id="portalSidebar" aria-label="Portal menu">
    <div class="offcanvas-header d-lg-none"><h5 class="offcanvas-title">Menu</h5><button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#portalSidebar" aria-label="Close"></button></div>
    <div class="offcanvas-body p-0 d-block">
      @php
        $p = $portal ?? 'student';
        $menu = [
          ['Dashboard', route($p.'.dashboard'), 'bi-speedometer2', request()->routeIs($p.'.dashboard')],
          ['My Achievements', route($p.'.achievements.index'), 'bi-trophy', request()->routeIs($p.'.achievements.*')],
          ['Submit Achievement', route($p.'.achievements.create'), 'bi-plus-circle', request()->routeIs($p.'.achievements.create')],
          ['Profile &amp; Password', route($p.'.profile'), 'bi-person-gear', request()->routeIs($p.'.profile')],
        ];
      @endphp
      <ul class="nav flex-column py-3">
        @foreach ($menu as [$label, $href, $icon, $active])
          <li class="nav-item"><a class="nav-link {{ $active ? 'active' : '' }}" href="{{ $href }}"><i class="bi {{ $icon }} me-2"></i>{!! $label !!}</a></li>
        @endforeach
      </ul>
      <div class="sidebar-note mx-3 mt-2 p-3 rounded">
        <i class="bi bi-shield-check me-1"></i>You are signed in as <strong>{{ auth()->user()->role->label() }}</strong>. Admin tools are not part of this portal.
      </div>
    </div>
  </aside>
  <main id="main-content" class="portal-main container-fluid px-3 px-lg-4 py-4">
    @include('partials.alerts')
    @yield('content')
  </main>
</div>
<script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
@stack('scripts')
</body>
</html>
