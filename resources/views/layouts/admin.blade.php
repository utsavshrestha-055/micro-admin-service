<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} · {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
    <div class="min-h-screen lg:flex">
        <aside id="admin-sidebar" class="fixed inset-y-0 left-0 z-40 hidden w-72 flex-col border-r border-slate-200 bg-white lg:flex">
            <div class="flex h-20 items-center gap-3 border-b border-slate-100 px-7">
                <span class="grid size-10 place-items-center rounded-xl bg-indigo-600 text-sm font-bold text-white">CA</span>
                <div>
                    <p class="font-semibold tracking-tight">Commerce Admin</p>
                    <p class="text-xs text-slate-500">Control center</p>
                </div>
            </div>
            <nav aria-label="Main navigation" class="flex-1 space-y-1 overflow-y-auto px-4 py-6">
                <p class="px-3 pb-2 text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">Workspace</p>
                @php($navigation = [
                    ['Dashboard', 'admin.dashboard', '⌂'], ['Users', 'admin.users.index', '♙'],
                    ['Roles', 'admin.roles.index', '◇'], ['Permissions', 'admin.permissions.index', '⌘'],
                ])
                @foreach ($navigation as [$label, $routeName, $icon])
                    <a href="{{ route($routeName) }}" @class(['nav-link', 'nav-link-active' => request()->routeIs($routeName.'*')])>
                        <span class="w-6 text-center text-lg" aria-hidden="true">{{ $icon }}</span><span>{{ $label }}</span>
                    </a>
                @endforeach
                <p class="px-3 pb-2 pt-7 text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">Commerce</p>
                @foreach ([['Products','products'],['Categories','categories'],['Merchants','merchants'],['Orders','orders'],['Customers','customers'],['Inventory','inventory'],['Reports','reports'],['Settings','settings'],['Audit logs','audit-logs']] as [$label, $path])
                    <a href="{{ route('admin.'.$path.'.index') }}" @class(['nav-link', 'nav-link-active' => request()->is('admin/'.$path)])>
                        <span class="w-6 text-center text-lg" aria-hidden="true">{{ ['Products'=>'□','Categories'=>'▦','Merchants'=>'◉','Orders'=>'▤','Customers'=>'♧','Inventory'=>'▥','Reports'=>'↗','Settings'=>'⚙','Audit logs'=>'◷'][$label] }}</span>
                        <span>{{ $label }}</span>
                        @unless (in_array($path, ['products', 'categories', 'orders'], true))<span class="ml-auto rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-medium text-slate-500">API</span>@endunless
                    </a>
                @endforeach
            </nav>
            <div class="border-t border-slate-100 p-5">
                <div class="flex items-center gap-3 rounded-xl bg-slate-50 p-3">
                    <span class="grid size-9 place-items-center rounded-full bg-indigo-100 text-sm font-semibold text-indigo-700">{{ strtoupper(substr(data_get(session('admin.user'), 'first_name', 'A'), 0, 1)) }}</span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-medium">{{ trim(data_get(session('admin.user'), 'first_name', '').' '.data_get(session('admin.user'), 'last_name', '')) ?: 'Administrator' }}</p>
                        <p class="truncate text-xs text-slate-500">{{ data_get(session('admin.user'), 'email') }}</p>
                    </div>
                </div>
            </div>
        </aside>

        <div class="min-w-0 flex-1 lg:pl-72">
            <header class="sticky top-0 z-30 flex h-20 items-center justify-between border-b border-slate-200 bg-white/95 px-5 backdrop-blur sm:px-8">
                <div class="flex items-center gap-3">
                    <button class="mobile-nav-toggle rounded-lg border border-slate-200 p-2 text-slate-600 lg:hidden" type="button" aria-label="Toggle navigation" aria-expanded="false">☰</button>
                    <div>
                        <p class="text-xs font-medium text-slate-400">Admin / <span class="text-slate-500">{{ $title ?? 'Dashboard' }}</span></p>
                        <h1 class="text-lg font-semibold tracking-tight sm:text-xl">{{ $title ?? 'Dashboard' }}</h1>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="hidden rounded-full border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600 sm:inline-flex">Superadmin workspace</span>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-50" type="submit">Sign out</button>
                    </form>
                </div>
            </header>

            <main class="mx-auto max-w-[1600px] space-y-6 p-5 sm:p-8">
                @if (session('status'))
                    <div role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
                @endif
                @if ($errors->any())
                    <div role="alert" class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                        <p class="font-semibold">Please review the issue below.</p>
                        <ul class="mt-1 list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
