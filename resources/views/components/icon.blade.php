@props(['name'])

<svg {{ $attributes->class('h-5 w-5 shrink-0')->merge(['viewBox' => '0 0 24 24', 'fill' => 'none', 'stroke' => 'currentColor', 'stroke-width' => '1.8', 'stroke-linecap' => 'round', 'stroke-linejoin' => 'round', 'aria-hidden' => 'true']) }}>
    @switch($name)
        @case('home')
            <path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z" />
            @break
        @case('meetings')
            <rect x="3" y="5" width="18" height="16" rx="2" /><path d="M16 3v4M8 3v4M3 10h18" />
            @break
        @case('notes')
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" /><path d="M14 2v6h6M8 13h8M8 17h8" />
            @break
        @case('tasks')
            <path d="m9 11 3 3L22 4M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" />
            @break
        @case('analytics')
            <path d="M3 3v18h18M18 17V9M13 17V5M8 17v-3" />
            @break
        @case('settings')
            <circle cx="12" cy="12" r="3" /><path d="m19.4 15 .1.1a1.7 1.7 0 0 1-2.4 2.4l-.1-.1a1.7 1.7 0 0 0-2.9 1.2v.2a1.7 1.7 0 0 1-3.4 0v-.2A1.7 1.7 0 0 0 8 17.4l-.1.1a1.7 1.7 0 1 1-2.4-2.4l.1-.1A1.7 1.7 0 0 0 4.4 12.1h-.2a1.7 1.7 0 0 1 0-3.4h.2A1.7 1.7 0 0 0 5.6 5.8l-.1-.1a1.7 1.7 0 1 1 2.4-2.4l.1.1A1.7 1.7 0 0 0 10.9 2.2V2a1.7 1.7 0 0 1 3.4 0v.2a1.7 1.7 0 0 0 2.9 1.2l.1-.1a1.7 1.7 0 1 1 2.4 2.4l-.1.1a1.7 1.7 0 0 0 1.2 2.9h.2a1.7 1.7 0 0 1 0 3.4h-.2a1.7 1.7 0 0 0-1.2 2.9z" />
            @break
        @case('search')
            <circle cx="11" cy="11" r="7" /><path d="m20 20-4-4" />
            @break
        @case('bell')
            <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4" />
            @break
        @case('upload')
            <path d="M12 16V4m-5 5 5-5 5 5M4 20h16" />
            @break
        @case('chevron')
            <path d="m9 18 6-6-6-6" />
            @break
        @case('menu')
            <path d="M4 6h16M4 12h16M4 18h16" />
            @break
        @case('logout')
            <path d="M10 17l5-5-5-5m5 5H3m9-9h7a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-7" />
            @break
        @case('user')
            <circle cx="12" cy="8" r="4" /><path d="M4 21a8 8 0 0 1 16 0" />
            @break
        @case('clock')
            <circle cx="12" cy="12" r="9" /><path d="M12 7v5l3 2" />
            @break
        @case('check')
            <path d="m5 12 4 4L19 6" />
            @break
        @case('alert')
            <path d="m10.3 3.9-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.7-3.1l-8-14a2 2 0 0 0-3.4 0ZM12 9v4m0 4h.01" />
            @break
        @case('copy')
            <rect x="8" y="8" width="13" height="13" rx="2" /><path d="M16 8V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h3" />
            @break
        @case('play')
            <path d="m8 5 12 7-12 7z" />
            @break
        @case('arrow')
            <path d="M7 17 17 7M7 7h10v10" />
            @break
        @case('plus')
            <path d="M12 5v14M5 12h14" />
            @break
        @case('filter')
            <path d="M4 6h16M7 12h10m-7 6h4" />
            @break
    @endswitch
</svg>
