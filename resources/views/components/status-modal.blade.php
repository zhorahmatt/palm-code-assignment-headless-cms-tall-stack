@props([
    'show' => false,
    'title' => 'Change Status',
    'message' => 'Are you sure you want to change the status?',
    'itemName' => '',
    'newStatus' => '',
    'statusMethod' => 'changeStatus',
    'closeMethod' => 'closeStatusModal',
    'statusConfig' => []
])

@php
$defaultConfig = [
    'active' => ['color' => 'green', 'icon' => 'check', 'action' => 'Activate'],
    'inactive' => ['color' => 'red', 'icon' => 'x', 'action' => 'Deactivate'],
    'published' => ['color' => 'green', 'icon' => 'check', 'action' => 'Publish'],
    'draft' => ['color' => 'yellow', 'icon' => 'exclamation', 'action' => 'Unpublish']
];
$config = array_merge($defaultConfig, $statusConfig);
$currentConfig = $config[$newStatus] ?? $config['active'];
@endphp

@if($show)
    <div class="fixed inset-0 z-50 overflow-y-auto"
        x-data="{ show: @entangle('showStatusModal') }"
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">

        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="$wire.{{ $closeMethod }}()"></div>

        <!-- Modal panel -->
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <!-- Status icon -->
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-{{ $currentConfig['color'] }}-100 sm:mx-0 sm:h-10 sm:w-10">
                            @if($currentConfig['icon'] === 'check')
                                <svg class="h-6 w-6 text-{{ $currentConfig['color'] }}-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @elseif($currentConfig['icon'] === 'x')
                                <svg class="h-6 w-6 text-{{ $currentConfig['color'] }}-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728" />
                                </svg>
                            @else
                                <svg class="h-6 w-6 text-{{ $currentConfig['color'] }}-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                </svg>
                            @endif
                        </div>

                        <!-- Modal content -->
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 class="text-base font-semibold leading-6 text-gray-900">{{ $title }}</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    {{ $message }}
                                    @if($itemName)
                                        <strong>"{{ $itemName }}"</strong>?
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal actions -->
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button type="button"
                            wire:click="{{ $statusMethod }}"
                            wire:loading.attr="disabled"
                            wire:target="{{ $statusMethod }}"
                            class="inline-flex w-full justify-center rounded-md bg-{{ $currentConfig['color'] }}-600 hover:bg-{{ $currentConfig['color'] }}-500 px-3 py-2 text-sm font-semibold text-white shadow-sm disabled:opacity-50 sm:ml-3 sm:w-auto">
                        <span wire:loading.remove wire:target="{{ $statusMethod }}">{{ $currentConfig['action'] }}</span>
                        <span wire:loading wire:target="{{ $statusMethod }}" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ $currentConfig['action'] }}ing...
                        </span>
                    </button>
                    <button type="button"
                            wire:click="{{ $closeMethod }}"
                            class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
