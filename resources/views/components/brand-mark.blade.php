@props(['class' => 'h-10 w-10'])

<svg {{ $attributes->class($class)->merge(['viewBox' => '0 0 40 40', 'fill' => 'none', 'role' => 'img', 'aria-label' => 'Syncora']) }}>
    <rect x="1" y="1" width="38" height="38" rx="12" fill="#0F766E" />
    <path d="M11 16.25a5.25 5.25 0 0 1 5.25-5.25h7.5A5.25 5.25 0 0 1 29 16.25v.5A5.25 5.25 0 0 1 23.75 22H21" stroke="white" stroke-width="2.2" stroke-linecap="round" />
    <path d="M29 23.75A5.25 5.25 0 0 1 23.75 29h-7.5A5.25 5.25 0 0 1 11 23.75v-.5A5.25 5.25 0 0 1 16.25 18H19" stroke="white" stroke-width="2.2" stroke-linecap="round" />
    <circle cx="20" cy="20" r="2" fill="#A7F3D0" />
</svg>
