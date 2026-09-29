@extends('layouts.public')
@section('title', 'Account pending')
@section('content')
<div class="container py-5">
  <div class="row justify-content-center"><div class="col-md-7">
    <div class="card border-0 shadow-sm text-center">
      <div class="card-body p-5">
        <div class="display-6 mb-3"><i class="bi bi-hourglass-split text-warning"></i></div>
        <h1 class="h4">Your account is awaiting approval</h1>
        <p class="text-muted">A department administrator must approve your registration before you can access the portal. This usually takes 1–2 working days.</p>
        <a class="btn btn-outline-secondary mt-2" href="{{ route('logout') }}" onclick="event.preventDefault();this.closest('div').querySelector('#pendingLogout').submit();">Sign out</a>
        <form id="pendingLogout" method="POST" action="{{ route('logout') }}" class="d-none">@csrf</form>
      </div>
    </div>
  </div></div>
</div>
@endsection
