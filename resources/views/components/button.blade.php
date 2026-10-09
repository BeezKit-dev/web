<button {{ $attributes->merge(['type' => 'submit'])->class('w-full cursor-pointer rounded-lg border border-ink-900 bg-honey-500 px-4 py-3 text-base font-bold text-ink-900 shadow-[3px_3px_0_0_var(--color-ink-900)] transition duration-75 hover:bg-honey-400 active:translate-x-[3px] active:translate-y-[3px] active:shadow-none disabled:opacity-60 dark:border-honey-500 dark:shadow-[3px_3px_0_0_rgb(255_201_7/0.3)]') }}>
    {{ $slot }}
</button>
