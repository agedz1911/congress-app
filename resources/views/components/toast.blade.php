@props([
'type' => 'success',
'duration' => 3000,
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

<div class="toast z-40" id="{{ $toastId }}" data-duration="{{ (int) $duration }}" x-data="{ show: true }" x-show="show"
    x-init="setTimeout(() => show = false, 3000)">
    <button class="btn btn-circle btn-xs float-end right-0 bottom-10 absolute" onclick="this.parentElement.remove()">
        <i class="fa fa-x text-xs"></i>
    </button>
    <div class="alert {{ $variant }}">
        <span class="text-sm">{{ $text }}</span>
    </div>
</div>

@endif