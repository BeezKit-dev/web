<span {{ $attributes->class('inline-flex items-center gap-3') }}>
    <svg viewBox="76.8 50 346.4 400" class="h-full w-auto dark:text-honey-500" role="img" aria-label="{{ config('app.name') }}">
        <defs>
            <mask id="logo-carve">
                <rect x="0" y="0" width="500" height="500" fill="#fff" />
                <circle cx="250" cy="250" r="32" fill="#000" />
                <g stroke="#000" stroke-width="20" stroke-linecap="round">
                    <line x1="250" y1="250" x2="423.2" y2="150" />
                    <line x1="250" y1="250" x2="76.8" y2="350" />
                    <line x1="250" y1="250" x2="250" y2="450" />
                </g>
            </mask>
        </defs>
        <polygon points="250,50 423.2,150 423.2,350 250,450 76.8,350 76.8,150" fill="currentColor" mask="url(#logo-carve)" />
    </svg>
    <span class="font-extrabold tracking-tight">Beez<span class="text-honey-500">Kit</span></span>
</span>
