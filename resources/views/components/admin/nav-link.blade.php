@props([
    'route',
    'routePattern',
    'icon',
    'label',
])

<a href="{{ route($route) }}"
   class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-sm font-medium mb-0.5 transition-all {{ request()->routeIs($routePattern) ? 'bg-gradient-to-r from-primary to-primary-hover text-white font-semibold shadow-gold' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">
    <span class="w-5 text-center text-base shrink-0"><i class="fas {{ $icon }}"></i></span> {{ $label }}
</a>
