<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scheme-light dark:scheme-dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ isset($title) ? $title.' · ' : '' }}{{ config('app.name') }}</title>
        <link rel="icon" href="{{ asset('images/logo.svg') }}" type="image/svg+xml">
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-honey-50 font-sans text-ink-900 antialiased dark:bg-ink-900 dark:text-honey-50">
        <div class="relative flex min-h-screen flex-col items-center justify-center overflow-hidden px-4 py-10">
            <svg class="pointer-events-none absolute inset-0 size-full text-honey-500/20 dark:text-honey-500/10" aria-hidden="true">
                <defs>
                    <pattern id="honeycomb" width="56" height="97" patternUnits="userSpaceOnUse" patternTransform="scale(1.2)">
                        <path d="M28 66L0 50V16L28 0l28 16v34L28 66zm0 0v31M0 50L-28 66m84 0L56 50" fill="none" stroke="currentColor" stroke-width="0.5"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#honeycomb)"/>
            </svg>

            <main class="relative w-full max-w-md">
                <a href="{{ url('/') }}" class="flex justify-center" wire:navigate>
                    <x-logo class="h-20 text-5xl" />
                </a>

                <div class="mt-8 rounded-2xl border border-ink-900 bg-white p-6 shadow-[6px_6px_0_0_var(--color-ink-900)] sm:p-8 dark:border-honey-500 dark:bg-ink-800 dark:shadow-[6px_6px_0_0_rgb(255_201_7/0.3)]">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </body>
</html>
