@props([
    'id' => 'dropdownMenu',
    'icon' => '',
    'notification' => null,
    'dropdownClass' => '',
])

<div {{ $attributes->merge(['class' => 'nav-item topbar-icon dropdown hidden-caret']) }}>
    <a class="nav-link dropdown-toggle" href="#" id="{{ $id }}" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="{{ $icon }}"></i>
        @if ($notification)
            <span class="notification">{{ $notification }}</span>
        @endif
    </a>
    <ul class="dropdown-menu {{ $dropdownClass }} animated fadeIn" aria-labelledby="{{ $id }}">
        {{ $slot }}
    </ul>
</div>
