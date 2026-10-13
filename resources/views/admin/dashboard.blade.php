@extends('layouts.admin', ['title' => 'Dashboard'])

@section('content')
    <section class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <p class="text-sm text-slate-500">Your workspace at a glance</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight">Good to see you, {{ data_get($user, 'first_name', 'Admin') }}</h2>
        </div>
        <div class="inline-flex items-center gap-2 self-start rounded-full border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-600 sm:self-auto">
            <span @class(['size-2 rounded-full', 'bg-emerald-500' => $serviceStatus === 'ok', 'bg-amber-500' => $serviceStatus !== 'ok'])></span>
            Auth service {{ $serviceStatus === 'ok' ? 'connected' : 'unavailable' }}
        </div>
    </section>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Platform overview">
        @foreach ([['Orders','Orders API required','↗'],['Products','Catalog API required','□'],['Merchants','Merchant API required','◉'],['Inventory','Inventory API required','▥']] as [$label, $detail, $icon])
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm shadow-slate-200/30">
                <div class="flex items-start justify-between">
                    <span class="grid size-10 place-items-center rounded-xl bg-slate-100 text-lg text-slate-600">{{ $icon }}</span>
                    <span class="rounded-full bg-amber-50 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide text-amber-700">Not connected</span>
                </div>
                <h3 class="mt-5 text-sm font-medium text-slate-500">{{ $label }}</h3>
                <p class="mt-1 text-sm font-semibold text-slate-800">{{ $detail }}</p>
            </article>
        @endforeach
    </section>

    <section class="grid gap-6 xl:grid-cols-[1.5fr_1fr]">
        <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm shadow-slate-200/30">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h3 class="font-semibold tracking-tight">Commerce overview</h3>
                    <p class="mt-1 text-sm text-slate-500">Order trends and product summaries will appear when commerce APIs are connected.</p>
                </div>
                <span class="rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-500">Awaiting data</span>
            </div>
            <div class="mt-7 grid h-52 place-items-center rounded-xl border border-dashed border-slate-200 bg-slate-50 px-6 text-center">
                <div>
                    <span class="mx-auto grid size-11 place-items-center rounded-full bg-white text-xl text-slate-400 shadow-sm">⌁</span>
                    <p class="mt-3 text-sm font-medium text-slate-700">No commerce data connected</p>
                    <p class="mt-1 max-w-sm text-xs leading-5 text-slate-500">This panel does not invent order or product metrics. Connect a commerce API to populate the dashboard.</p>
                </div>
            </div>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm shadow-slate-200/30">
            <h3 class="font-semibold tracking-tight">Quick actions</h3>
            <p class="mt-1 text-sm text-slate-500">Common access management tasks</p>
            <div class="mt-5 space-y-3">
                <a href="{{ route('admin.users.index') }}" class="quick-action"><span class="quick-action-icon">♙</span><span class="flex-1"><span class="block text-sm font-medium">Review users</span><span class="block text-xs text-slate-500">Inspect accounts and assigned roles</span></span><span aria-hidden="true">→</span></a>
                <a href="{{ route('admin.roles.index') }}" class="quick-action"><span class="quick-action-icon">◇</span><span class="flex-1"><span class="block text-sm font-medium">Manage roles</span><span class="block text-xs text-slate-500">Maintain access groups</span></span><span aria-hidden="true">→</span></a>
                <a href="{{ route('admin.permissions.index') }}" class="quick-action"><span class="quick-action-icon">⌘</span><span class="flex-1"><span class="block text-sm font-medium">Review permissions</span><span class="block text-xs text-slate-500">Define granular capabilities</span></span><span aria-hidden="true">→</span></a>
            </div>
        </article>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm shadow-slate-200/30">
        <div class="flex items-center justify-between">
            <div><h3 class="font-semibold tracking-tight">Recent activity</h3><p class="mt-1 text-sm text-slate-500">Activity history requires an audit log service.</p></div>
            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs text-slate-500">No audit source</span>
        </div>
        <div class="mt-5 rounded-xl bg-slate-50 px-5 py-8 text-center text-sm text-slate-500">No activity is shown until an audit API is connected.</div>
    </section>
@endsection
