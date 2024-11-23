@props([
    'type' => 'button',
    'fullWidth' => false
])

<button {{ $attributes->merge([
    'type' => $type,
    'class' => 'btn btn-md btn-primary' . ($fullWidth ? ' w-100' : '')
]) }}>
    {{ $slot }}
</button>
