@php
    $portalClient = Auth::user()->client;
    $portalName = $portalClient?->company_name ?? 'Client Portal';
@endphp

<aside
    x-data="{ collapsed: false }"
    :class="collapsed ? 'w-20' : 'w-72'"
    class="hidden lg:flex lg:flex-col shrink-0 border-r border-app-border bg-sidebar-bg transition-all duration-200"
>
    <div class="flex items-center justify-between px-4 py-5">
        <a href="{{ route('client.dashboard') }}" class="flex items-center gap-2 overflow-hidden" :class="collapsed && 'justify-center w-full'">
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary text-white font-bold">
                {{ Str::substr($portalName, 0, 1) }}
            </span>
            <span x-show="!collapsed" x-cloak class="truncate text-lg font-semibold text-secondary-dark" title="{{ $portalName }}">
                {{ $portalName }}
            </span>
        </a>
        <button @click="collapsed = !collapsed" x-show="!collapsed" x-cloak class="rounded-md p-1 text-secondary hover:bg-surface-alt">
            <x-icon name="chevron-left" class="w-4 h-4" />
        </button>
    </div>

    <div class="px-4 pb-2" :class="collapsed && 'px-0 flex justify-center'">
        <button @click="collapsed = !collapsed" x-show="collapsed" x-cloak class="rounded-md p-1 text-secondary hover:bg-surface-alt">
            <x-icon name="chevron-right" class="w-4 h-4" />
        </button>
    </div>

    <nav class="flex-1 space-y-6 overflow-y-auto px-3 pb-6">
        <div>
            <p x-show="!collapsed" x-cloak class="mb-2 px-3 text-xs font-semibold uppercase tracking-wide text-secondary">Main</p>
            <div class="space-y-1">
                <x-sidebar-link :href="route('client.dashboard')" :active="request()->routeIs('client.dashboard')" icon="grid">
                    <span x-show="!collapsed" x-cloak>Dashboard</span>
                </x-sidebar-link>
                <x-sidebar-link :href="route('client.projects.index')" :active="request()->routeIs('client.projects.*')" icon="folder">
                    <span x-show="!collapsed" x-cloak>Projects</span>
                </x-sidebar-link>
                <x-sidebar-link :href="route('client.training.index')" :active="request()->routeIs('client.training.*')" icon="play-circle">
                    <span x-show="!collapsed" x-cloak>Training</span>
                </x-sidebar-link>
            </div>
        </div>
    </nav>

    <div class="border-t border-app-border p-3">
        <div class="flex items-center gap-3 px-1 pb-3" :class="collapsed && 'justify-center'">
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-secondary-dark text-sm font-semibold text-white">
                {{ Str::substr(Auth::user()->name, 0, 1) }}
            </span>
            <span x-show="!collapsed" x-cloak class="min-w-0 flex-1">
                <span class="block truncate text-sm font-medium text-secondary-dark">{{ Auth::user()->name }}</span>
                <span class="block truncate text-xs text-secondary">{{ Auth::user()->email }}</span>
            </span>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-secondary-dark hover:bg-danger-light hover:text-danger"
                :class="collapsed && 'justify-center'"
                title="Log out"
            >
                <x-icon name="log-out" class="w-[18px] h-[18px] shrink-0" />
                <span x-show="!collapsed" x-cloak>Logout</span>
            </button>
        </form>
    </div>
</aside>
