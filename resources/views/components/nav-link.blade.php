@props(['href', 'active' => false])

<li class="{{ $active ? 'active' : '' }}">
    <a class="nav-link" href="{{ $href }}" wire:navigate>
        <span class="sub-item">{{ $slot }}</span>
    </a>
</li>
