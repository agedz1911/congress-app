@props([
'type' => 'success',
'duration' => 5000,
'message' => null
])

@if($message || session('message'))
@php
$toastId = 'toast-message-' . uniqid();
@endphp
<div class="toast z-40 " id="{{ $toastId }}" data-duration="{{ $duration }}">
    <div class="alert alert-{{ $type }}">
        <span class="text-sm text-white">{{ $message ?? session('message') }}</span>
    </div>
</div>
<script>
    setTimeout(function() {
        const toast = document.getElementById('{{ $toastId }}');
        if (toast) {
            const duration = parseInt(toast.getAttribute('data-duration')) || 5000;
            setTimeout(function() {
                toast.style.transition = 'opacity 0.5s ease-out';
                toast.style.opacity = '0';
                setTimeout(function() {
                    toast.remove();
                }, 500);
            }, duration);
        }
    }, 100);
</script>
@endif