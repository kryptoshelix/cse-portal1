<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title','Admin') — CSE Admin</title>
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/portal.css') }}">
</head>
<body class="admin-body">
<a class="skip-link" href="#main-content">Skip to main content</a>
<nav class="navbar admin-topbar sticky-top" aria-label="Admin navigation">
  <div class="container-fluid px-3">
    <button class="btn btn-sm btn-outline-secondary d-xl-none me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminSidebar" aria-label="Open menu"><i class="bi bi-list"></i></button>
    <span class="fw-bold text-cse-dark"><i class="bi bi-gear-wide-connected me-1"></i>CSE Department — Administration</span>
    <div class="dropdown ms-auto">
      <a class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
        <span class="avatar-circle avatar-admin me-2">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</span>
        <span class="d-none d-sm-inline small">{{ auth()->user()->name }}</span>
      </a>
      <ul class="dropdown-menu dropdown-menu-end shadow">
        <li><h6 class="dropdown-header">{{ auth()->user()->role->label() }}</h6></li>
        <li><a class="dropdown-item" href="{{ route('home') }}"><i class="bi bi-globe me-2"></i>View public site</a></li>
        @if(auth()->user()->isStudent() || auth()->user()->isFaculty())
          <li><a class="dropdown-item" href="{{ route(auth()->user()->isFaculty() ? 'faculty.dashboard' : 'student.dashboard') }}"><i class="bi bi-house me-2"></i>My personal portal</a></li>
        @endif
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
<div class="admin-shell">
  <aside class="admin-sidebar offcanvas-xl offcanvas-start" tabindex="-1" id="adminSidebar" aria-label="Admin menu">
    <div class="offcanvas-header d-xl-none"><h5 class="offcanvas-title">Admin menu</h5><button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#adminSidebar" aria-label="Close"></button></div>
    <div class="offcanvas-body p-0 d-block">
      @php
        $sec = fn($label,$routes) => '<li class="sidebar-section">'.$label.'</li>';
        $link = fn($label,$href,$active,$icon) => '<li class="nav-item"><a class="nav-link'.($active?' active':'').'" href="'.$href.'"><i class="bi '.$icon.' me-2"></i>'.$label.'</a></li>';
        $r = fn($patterns) => collect((array)$patterns)->contains(fn($pt) => request()->routeIs($pt));
      @endphp
      <ul class="nav flex-column py-3">
        {!! $sec('OVERVIEW') !!}
        {!! $link('Dashboard', route('admin.dashboard'), $r(['admin.dashboard']), 'bi-speedometer2') !!}
        {!! $link('Pending Reviews', route('admin.achievements.pending'), $r(['admin.achievements.pending']), 'bi-inboxes') !!}
        {!! $link('Audit Log', route('admin.audit.index'), $r(['admin.audit.*']), 'bi-journal-text') !!}
        {!! $sec('REVIEW & PUBLISH') !!}
        {!! $link('Achievements', route('admin.achievements.index'), $r(['admin.achievements.index','admin.achievements.show','admin.achievements.edit','admin.achievements.create']), 'bi-trophy') !!}
        {!! $link('Categories', route('admin.categories.index'), $r(['admin.categories.*']), 'bi-tags') !!}
        {!! $sec('PEOPLE') !!}
        {!! $link('Students', route('admin.students.index'), $r(['admin.students.*']), 'bi-people') !!}
        {!! $link('Faculty', route('admin.faculty.index'), $r(['admin.faculty.*']), 'bi-person-video3') !!}
        {!! $link('Accounts &amp; Approvals', route('admin.accounts.index'), $r(['admin.accounts.*']), 'bi-person-check') !!}
        {!! $sec('CONTENT') !!}
        @foreach(['activities'=>'Activities & Events','projects'=>'Projects','publications'=>'Publications','patents'=>'Patents','gallery'=>'Gallery','news'=>'News'] as $mod=>$lbl)
          {!! $link($lbl, route('admin.'.$mod.'.index'), $r(['admin.'.$mod.'.*']), ['activities'=>'bi-calendar-event','projects'=>'bi-diagram-3','publications'=>'bi-journal-medical','patents'=>'bi-file-earmark-ruled','gallery'=>'bi-images','news'=>'bi-megaphone'][$mod]) !!}
        @endforeach
      </ul>
    </div>
  </aside>
  <main id="main-content" class="admin-main container-fluid px-3 px-lg-4 py-4">
    @include('partials.alerts')
    @yield('content')
  </main>
</div>
<script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
@stack('scripts')
</body>
</html>
