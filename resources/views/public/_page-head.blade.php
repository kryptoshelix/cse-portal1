@props(['title', 'subtitle' => null, 'icon' => 'bi-collection'])
<div class="bg-cse-dark text-white py-5 mb-4">
    <div class="container">
        <h1 class="h2 mb-1"><i class="bi {{ $icon }} me-2 opacity-75"></i>{{ $title }}</h1>
        @if ($subtitle)<p class="lead opacity-75 mb-0">{{ $subtitle }}</p>@endif
    </div>
</div>
