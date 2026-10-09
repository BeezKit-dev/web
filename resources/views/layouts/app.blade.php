<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scheme-light dark:scheme-dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ isset($title) ? $title.' · ' : '' }}{{ config('app.name') }}</title>
        <link rel="icon" href="{{ asset('images/logo.svg') }}" type="image/svg+xml">
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="min-h-screen bg-honey-50 font-sans text-ink-900 antialiased dark:bg-ink-900 dark:text-honey-50">
        <header class="border-b border-ink-900/15 bg-white dark:border-honey-50/15 dark:bg-ink-800">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6">
                <a href="{{ route('dashboard') }}" wire:navigate aria-label="{{ config('app.name') }}">
                    <x-logo class="h-9 text-2xl" />
                </a>

                @auth
                    <div
                        x-data="{ open: false }"
                        x-on:click.outside="open = false"
                        x-on:keydown.escape.window="open = false"
                        class="relative"
                    >
                        <button
                            type="button"
                            x-on:click="open = ! open"
                            x-bind:aria-expanded="open"
                            aria-haspopup="menu"
                            class="flex cursor-pointer items-center gap-3 rounded-lg border border-ink-900/20 py-1.5 pr-3 pl-1.5 hover:border-honey-500 dark:border-honey-50/20 dark:hover:border-honey-500"
                        >
                            <span class="flex size-8 items-center justify-center rounded-md bg-honey-500 text-sm font-extrabold text-ink-900">
                                {{ Str::upper(Str::substr(auth()->user()->name, 0, 1)) }}
                            </span>
                            <span class="hidden text-left sm:block">
                                <span class="block text-sm leading-tight font-semibold">{{ auth()->user()->name }}</span>
                                @if (auth()->user()->tenant)
                                    <span class="block text-xs leading-tight text-ink-700 dark:text-honey-50/70">{{ auth()->user()->tenant->name }}</span>
                                @endif
                            </span>
                            <svg class="size-4 text-ink-700 dark:text-honey-50/70" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/>
                            </svg>
                        </button>

                        <div
                            x-show="open"
                            x-cloak
                            x-transition.origin.top.right
                            role="menu"
                            class="absolute right-0 z-10 mt-2 w-64 rounded-xl border border-ink-900/20 bg-white p-1.5 shadow-lg dark:border-honey-50/20 dark:bg-ink-800"
                        >
                            <div class="px-3 py-2">
                                <p class="text-sm font-semibold">{{ auth()->user()->name }}</p>
                                @if (auth()->user()->tenant)
                                    <p class="text-xs text-ink-700 dark:text-honey-50/70">{{ auth()->user()->tenant->name }}</p>
                                @endif
                                <p class="mt-1 truncate text-xs text-ink-700 dark:text-honey-50/70">{{ auth()->user()->email }}</p>
                            </div>
                            <div class="my-1 border-t border-ink-900/10 dark:border-honey-50/10"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button
                                    type="submit"
                                    role="menuitem"
                                    class="w-full cursor-pointer rounded-lg px-3 py-2 text-left text-sm font-medium hover:bg-honey-100 dark:hover:bg-ink-700"
                                >
                                    {{ __('Log out') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @endauth
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6">
            {{ $slot }}
        </main>

        @livewireScripts
    </body>
</html>
