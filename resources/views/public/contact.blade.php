@extends('layouts.public')
@section('title', 'Contact')

@section('content')
@include('public._page-head', ['title' => 'Contact Us', 'subtitle' => 'Reach the CSE department office', 'icon' => 'bi-envelope'])
<div class="container pb-5">
    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100"><div class="card-body">
                <h2 class="h5">Department Office</h2>
                <p class="mb-3 small text-muted">Sample University College, Academic Block C,<br>Main Campus Road, Sample City 000000.</p>
                <ul class="list-unstyled small mb-0">
                    <li class="mb-2"><i class="bi bi-envelope me-2 text-cse"></i>cse@sample-university.example</li>
                    <li class="mb-2"><i class="bi bi-telephone me-2 text-cse"></i>+1 555 010 0000</li>
                    <li><i class="bi bi-clock me-2 text-cse"></i>Mon–Fri, 9:00 AM – 5:00 PM</li>
                </ul>
            </div></div>
        </div>
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm"><div class="card-body">
                <h2 class="h5 mb-3">Send an Inquiry</h2>
                @include('partials.alerts')
                <form method="POST" action="{{ route('contact.submit') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="cname">Your Name *</label>
                            <input id="cname" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required maxlength="120">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="cemail">Email *</label>
                            <input id="cemail" type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required maxlength="190">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="csubject">Subject</label>
                            <input id="csubject" name="subject" value="{{ old('subject') }}" class="form-control @error('subject') is-invalid @enderror" maxlength="150">
                            @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="cmsg">Message *</label>
                            <textarea id="cmsg" name="message" rows="5" class="form-control @error('message') is-invalid @enderror" required>{{ old('message') }}</textarea>
                            @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <button class="btn btn-cse" type="submit"><i class="bi bi-send me-1"></i>Send Message</button>
                        </div>
                    </div>
                </form>
            </div></div>
        </div>
    </div>
</div>
@endsection
