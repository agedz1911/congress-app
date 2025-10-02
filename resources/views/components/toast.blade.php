@props([
'type' => 'success',
'duration' => 500,
'message' => null
])

@php
$alertClasses = [
'success' => 'alert-success',
'error' => 'alert-error',
'info' => 'alert-info',
'warning' => 'alert-warning',
];
$variant = $alertClasses[$type] ?? $alertClasses['success'];
$text = $message ?? session('message');
@endphp

@if($text)
@php $toastId = 'toast-message-' . uniqid(); @endphp

<div class="toast z-40" id="{{ $toastId }}" data-duration="{{ (int) $duration }}">
    <div class="alert {{ $variant }}">
        <span class="text-sm">{{ $text }}</span>
    </div>
</div>

<script>
    (function() {
            // Pastikan script ini jalan setelah element tersedia
            const toastId = '{{ $toastId }}';
            const init = () => {
                const toast = document.getElementById(toastId);
                if (!toast) return;

                const duration = parseInt(toast.getAttribute('data-duration')) || 5000;

                // Tambahkan opacity awal supaya animasi terlihat
                toast.style.opacity = '1';

                setTimeout(() => {
                    toast.style.transition = 'opacity 0.5s ease-out';
                    toast.style.opacity = '0';
                    setTimeout(() => toast.remove(), 500);
                }, duration);
            };

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                init();
            }
        })();
</script>
@endif

{{-- @if($message || session('message'))
@php
$toastId = 'toast-message-' . uniqid();
@endphp
<div class="toast z-40 " id="{{ $toastId }}" data-duration="{{ $duration }}">
    <div class="alert alert-error">
        <span class="text-sm ">{{ $message ?? session('message') }}</span>
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
@endif --}}