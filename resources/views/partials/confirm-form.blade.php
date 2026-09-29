{{-- $action, $method (POST/PUT/DELETE), $label, $btnClass, $confirm --}}
<form method="POST" action="{{ $action }}" class="d-inline" onsubmit="return confirm('{{ $confirm ?? 'Are you sure?' }}');">
    @csrf
    @if(($method ?? 'POST') !== 'POST') @method($method) @endif
    <button type="submit" class="btn btn-sm {{ $btnClass ?? 'btn-outline-secondary' }}">{{ $slot }}</button>
</form>
