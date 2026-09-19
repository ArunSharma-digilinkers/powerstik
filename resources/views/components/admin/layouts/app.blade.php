@php
    use App\Models\User;

    // Sidebar: [route, label, roles allowed (admins always pass)], grouped.
    $nav = ['' => [
        ['admin.dashboard', 'Dashboard', []],
        ['admin.leads.index', 'Leads', [User::ROLE_SALES]],
    ]];
    foreach (config('admin.resources') as $slug => [, $group, $label, $roles]) {
        $nav[$group][] = ["admin.$slug.index", $label, $roles];
    }
    $nav['Administration'][] = ['admin.settings.edit', 'Settings', [User::ROLE_ADMIN]];
    $nav['Administration'][] = ['admin.system.index', 'System', [User::ROLE_ADMIN]];
    $user = auth()->user();
@endphp
<!DOCTYPE html>
<html lang="en" class="h-full bg-zinc-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title ?? 'Admin' }} · Powerstik</title>
    <link rel="icon" href="/favicon.ico" sizes="32x32">
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
</head>
<body class="h-full font-sans text-ink antialiased" x-data="{ nav: false }">
    <div class="flex min-h-full">
        {{-- Sidebar --}}
        <aside class="fixed inset-y-0 left-0 z-40 w-64 -translate-x-full border-r border-zinc-200 bg-white transition lg:translate-x-0"
               :class="nav && 'translate-x-0'">
            <div class="flex h-16 items-center border-b border-zinc-200 px-5">
                <a href="{{ route('admin.dashboard') }}"><img src="/images/brand/logo.png" alt="Powerstik" class="h-8 w-auto"></a>
            </div>
            <nav class="h-[calc(100%-4rem)] space-y-5 overflow-y-auto p-3">
                @foreach ($nav as $group => $items)
                    @php $items = array_filter($items, fn ($i) => ! $i[2] || $user->hasRole(...$i[2])); @endphp
                    @continue(! $items)
                    <div class="space-y-0.5">
                        @if ($group)
                            <p class="px-3 pb-1 text-xs font-semibold tracking-wide text-zinc-400 uppercase">{{ $group }}</p>
                        @endif
                        @foreach ($items as [$route, $label])
                            @php $active = request()->routeIs(\Illuminate\Support\Str::beforeLast($route, '.').'.*') || request()->routeIs($route); @endphp
                            <a href="{{ route($route) }}"
                               @class([
                                   'block rounded-md px-3 py-1.5 text-sm font-medium',
                                   'bg-brand-50 text-brand-700' => $active,
                                   'text-zinc-700 hover:bg-zinc-100' => ! $active,
                               ])>{{ $label }}</a>
                        @endforeach
                    </div>
                @endforeach
            </nav>
        </aside>
        <div x-show="nav" x-cloak @click="nav = false" class="fixed inset-0 z-30 bg-black/30 lg:hidden"></div>

        <div class="flex min-w-0 flex-1 flex-col lg:pl-64">
            <header class="sticky top-0 z-20 flex h-16 items-center gap-4 border-b border-zinc-200 bg-white/90 px-4 backdrop-blur sm:px-6">
                <button type="button" class="lg:hidden" @click="nav = true" aria-label="Open menu">
                    <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5"/></svg>
                </button>
                <h1 class="font-display text-lg font-semibold">{{ $title ?? 'Admin' }}</h1>
                <div class="ml-auto flex items-center gap-4 text-sm">
                    <a href="{{ route('home') }}" target="_blank" class="text-zinc-500 hover:text-ink">View site ↗</a>
                    <span class="hidden text-zinc-500 sm:inline">{{ $user->name }} · {{ User::ROLES[$user->role] ?? $user->role }}</span>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button class="font-medium text-zinc-700 hover:text-brand-600">Log out</button>
                    </form>
                </div>
            </header>

            <main class="flex-1 p-4 sm:p-6">
                @if (session('status'))
                    <div class="mb-6 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('status') }}</div>
                @endif
                @if ($errors->any())
                    <div class="mb-6 rounded-md border border-brand-200 bg-brand-50 px-4 py-3 text-sm text-brand-800">Please fix the highlighted fields.</div>
                @endif
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
