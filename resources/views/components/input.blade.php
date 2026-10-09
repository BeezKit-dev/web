@props(['label', 'name', 'clears' => [], 'sharesErrorsWith' => []])

<div x-data="{
    clear() {
        @js([$name, ...$clears]).forEach((field) => {
            document.getElementById(field)?.removeAttribute('data-invalid');
            document.querySelector(`[data-error-for='${field}']`)?.setAttribute('hidden', '');
        });
    },
}">
    <label for="{{ $name }}" class="mb-1.5 block text-sm font-semibold">{{ $label }}</label>
    <input
        id="{{ $name }}"
        name="{{ $name }}"
        x-on:input="clear()"
        @if ($errors->hasAny([$name, ...$sharesErrorsWith])) data-invalid @endif
        {{ $attributes->class([
            'block w-full rounded-lg border bg-white transition-[border-color,box-shadow] duration-75 px-3.5 py-2.5 text-base placeholder:text-ink-700/40 focus:outline-none focus:ring-4 focus:ring-honey-500/40 dark:bg-ink-900 dark:placeholder:text-honey-50/30',
            'border-ink-900/20 not-data-invalid:hover:border-honey-500 not-data-invalid:focus:border-ink-900',
            'dark:border-honey-50/20 dark:not-data-invalid:hover:border-honey-500 dark:not-data-invalid:focus:border-honey-500',
            'data-invalid:border-red-600 dark:data-invalid:border-red-400',
        ]) }}
    >
    @error($name)
        <p data-error-for="{{ $name }}" class="mt-1.5 text-sm font-medium text-red-700 dark:text-red-400">{{ $message }}</p>
    @enderror
</div>
