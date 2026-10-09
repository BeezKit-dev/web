<x-layouts::app :title="__('Dashboard')">
    <h1 class="text-2xl font-extrabold tracking-tight">{{ __('Welcome, :name', ['name' => auth()->user()->name]) }}</h1>
    <p class="mt-1 text-sm text-ink-700 dark:text-honey-50/70">{{ __('The dashboard is coming soon.') }}</p>
</x-layouts::app>
