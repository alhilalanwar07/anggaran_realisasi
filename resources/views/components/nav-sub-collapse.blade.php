@props(['title', 'href', 'active' => false])

<li class="nav-item {{ $active ? 'active border-start' : '' }}">
    <a data-bs-toggle="collapse" href="{{ $href }}" aria-expanded="{{ $active ? 'true' : 'false' }}">
        <span class="sub-item">{{ $title }}</span>
        <span class="caret"></span>
    </a>
    <div class="collapse {{ $active ? 'show border-start' : '' }}" id="{{ str_replace('#', '', $href) }}">
        <ul class="nav nav-collapse subnav">
            {{ $slot }}
        </ul>
    </div>
</li>
