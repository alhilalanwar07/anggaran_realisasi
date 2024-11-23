@props(['title', 'icon', 'href', 'active' => false])

<li class="nav-item {{ $active ? 'active' : '' }}">
    <a data-bs-toggle="collapse" href="{{ $href }}" aria-expanded="{{ $active ? 'true' : 'false' }}" >
        <i class="{{ $icon }}"></i>
        <p>{{ $title }}</p>
        <span class="caret"></span>
    </a>
    <div class="collapse {{ $active ? 'show' : '' }}" id="{{ str_replace('#', '', $href) }}">
        <ul class="nav nav-collapse">
            {{ $slot }}
        </ul>
    </div>
</li>
