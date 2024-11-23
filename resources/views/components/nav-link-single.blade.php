@props(['href', 'active' => false])

<li class="nav-item {{ $active ? 'active' : '' }}">
    <a class="nav-link" href="{{ $href }}">
        <i class="{{ $attributes->get('icon') }}"></i> <!-- Icon if needed -->
        <p>{{ $slot }}</p>
        @if($attributes->has('badge'))
            <span class="badge badge-success">{{ $attributes->get('badge') }}</span>
        @endif
    </a>
</li>
