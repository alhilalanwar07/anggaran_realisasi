<!-- resources/views/components/dropdown-link.blade.php -->
<a {{ $attributes->merge(['class' => 'dropdown-item']) }} href="{{ $href }}">
    <div class="notif-content">
        @isset($image)
            <div class="notif-img">
                <img src="{{ $image }}" alt="Img Profile" />
            </div>
        @endisset
        <span class="block">{{ $slot }}</span>
        @isset($time)
            <span class="time">{{ $time }}</span>
        @endisset
    </div>
</a>
