<aside
    x-data="{ collapsed: false }"
    :class="collapsed ? 'w-20' : 'w-64'"
    class="hidden lg:flex lg:flex-col shrink-0 border-r border-app-border bg-sidebar-bg shadow-[1px_0_0_0_rgba(15,23,42,0.02)] transition-all duration-200"
>
    <div class="flex items-center justify-between px-4 py-5">
        <a href="{{ route('client.dashboard') }}" class="flex min-w-0 items-center overflow-hidden rounded-lg bg-white p-1.5 shadow-sm" :class="collapsed && 'justify-center'">
            <img x-show="!collapsed" x-cloak src="{{ asset('assets/images/logo.webp') }}" alt="{{ config('app.name', 'Client Portal') }}" class="h-8 w-auto max-w-[165px] shrink-0 object-contain">
            <img x-show="collapsed" x-cloak src="{{ asset('assets/images/logo-mark.png') }}" alt="{{ config('app.name', 'Client Portal') }}" class="h-9 w-9 shrink-0 rounded object-cover">
        </a>
        <button @click="collapsed = !collapsed" x-show="!collapsed" x-cloak class="rounded-md p-1 text-sidebar-text hover:bg-white">
            <x-icon name="chevron-left" class="w-4 h-4" />
        </button>
    </div>

    <div class="px-4 pb-2" :class="collapsed && 'px-0 flex justify-center'">
        <button @click="collapsed = !collapsed" x-show="collapsed" x-cloak class="rounded-md p-1 text-sidebar-text hover:bg-white">
            <x-icon name="chevron-right" class="w-4 h-4" />
        </button>
    </div>

    <nav class="flex-1 space-y-6 overflow-y-auto px-3 pb-6">
        <div>
            <div class="space-y-1">
                <x-sidebar-link :href="route('client.dashboard')" :active="request()->routeIs('client.dashboard')" icon="grid">
                    <span x-show="!collapsed" x-cloak>Dashboard</span>
                </x-sidebar-link>
                <x-sidebar-link :href="route('client.onboarding.index')" :active="request()->routeIs('client.onboarding.*')" icon="plus">
                    <span x-show="!collapsed" x-cloak>Onboarding</span>
                </x-sidebar-link>
                <x-sidebar-link :href="route('client.projects.index')" :active="request()->routeIs('client.projects.*')" icon="folder">
                    <span x-show="!collapsed" x-cloak>Projects</span>
                </x-sidebar-link>
                <x-sidebar-link :href="route('client.lms.index')" :active="request()->routeIs('client.lms.*')" icon="book-open">
                    <span x-show="!collapsed" x-cloak>Documentation</span>
                </x-sidebar-link>
                <x-sidebar-link :href="route('client.courses.index')" :active="request()->routeIs('client.courses.*') || request()->routeIs('client.course-*')" icon="video">
                    <span x-show="!collapsed" x-cloak>Courses</span>
                </x-sidebar-link>
                <x-sidebar-link :href="route('client.documents.index')" :active="request()->routeIs('client.documents.*')" icon="file-text">
                    <span x-show="!collapsed" x-cloak>Documents</span>
                </x-sidebar-link>
                <x-sidebar-link :href="route('client.customization-requests.index')" :active="request()->routeIs('client.customization-requests.*')" icon="settings">
                    <span x-show="!collapsed" x-cloak>Customization</span>
                </x-sidebar-link>
                <x-sidebar-link :href="route('client.support.index')" :active="request()->routeIs('client.support.*')" icon="life-buoy">
                    <span x-show="!collapsed" x-cloak>Support</span>
                </x-sidebar-link>
            </div>
        </div>
    </nav>

    <div class="border-t border-app-border p-3">
        <div class="flex items-center gap-3 px-1 pb-3" :class="collapsed && 'justify-center'">
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary text-sm font-semibold text-white">
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
                class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-sidebar-text hover:bg-danger-light hover:text-danger"
                :class="collapsed && 'justify-center'"
                title="Log out"
            >
                <x-icon name="log-out" class="w-[18px] h-[18px] shrink-0" />
                <span x-show="!collapsed" x-cloak>Logout</span>
            </button>
        </form>
    </div>
</aside>
