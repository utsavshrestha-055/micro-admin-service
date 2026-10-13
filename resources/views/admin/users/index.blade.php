@extends('layouts.admin', ['title' => 'Users'])

@section('content')
    <section class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div><p class="text-sm text-slate-500">Identity and access</p><h2 class="mt-1 text-2xl font-semibold tracking-tight">Users</h2></div>
        <div class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs text-slate-500">Directory provided by Auth Service</div>
    </section>
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm shadow-slate-200/30">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col gap-3 border-b border-slate-100 p-4 sm:flex-row sm:items-center sm:justify-between">
            <div><h3 class="text-sm font-semibold">User directory</h3><p class="mt-1 text-xs text-slate-500">Search filters the current service page. The service API supports offset pagination.</p></div>
            <div class="flex flex-col gap-2 sm:flex-row">
                <label class="relative block sm:w-64"><span class="sr-only">Search current page</span><input class="form-input py-2 pl-9 text-sm" type="search" name="q" value="{{ request('q') }}" placeholder="Filter this page"><span class="absolute left-3 top-2.5 text-slate-400" aria-hidden="true">⌕</span></label>
                <label><span class="sr-only">Filter by status</span><select class="form-input py-2 text-sm sm:w-36" name="status" onchange="this.form.requestSubmit()"><option value="">Any status</option><option value="active" @selected(request('status') === 'active')>Active</option><option value="inactive" @selected(request('status') === 'inactive')>Inactive</option></select></label>
                <button class="secondary-button" type="submit">Apply</button>
            </div>
        </form>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500"><tr><th class="px-5 py-3 font-semibold">User</th><th class="px-5 py-3 font-semibold">Status</th><th class="px-5 py-3 font-semibold">Created</th><th class="px-5 py-3 text-right font-semibold">Action</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($users as $user)
                        <tr class="transition hover:bg-slate-50/70">
                            <td class="whitespace-nowrap px-5 py-4"><div class="flex items-center gap-3"><span class="grid size-9 place-items-center rounded-full bg-indigo-50 text-xs font-semibold text-indigo-700">{{ strtoupper(substr($user['first_name'] ?? $user['email'] ?? 'U', 0, 1)) }}</span><div><p class="font-medium text-slate-800">{{ trim(($user['first_name'] ?? '').' '.($user['last_name'] ?? '')) ?: 'Unnamed user' }}</p><p class="mt-0.5 text-xs text-slate-500">{{ $user['email'] ?? '' }}</p></div></div></td>
                            <td class="whitespace-nowrap px-5 py-4"><span @class(['status-badge', 'status-active' => ($user['is_active'] ?? false), 'status-inactive' => ! ($user['is_active'] ?? false)])>{{ ($user['is_active'] ?? false) ? 'Active' : 'Inactive' }}</span></td>
                            <td class="whitespace-nowrap px-5 py-4 text-slate-500">{{ filled($user['created_at'] ?? null) ? \Illuminate\Support\Carbon::parse($user['created_at'])->format('M j, Y') : '—' }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-right"><a class="text-sm font-medium text-indigo-600 hover:text-indigo-800" href="{{ route('admin.users.show', $user['id']) }}">View profile <span aria-hidden="true">→</span></a></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-14 text-center"><span class="mx-auto grid size-11 place-items-center rounded-full bg-slate-100 text-lg text-slate-400">♙</span><p class="mt-3 text-sm font-semibold text-slate-700">No users found</p><p class="mt-1 text-sm text-slate-500">Try another search or check back when users are available.</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="flex items-center justify-between border-t border-slate-100 px-5 py-4">
            <p class="text-xs text-slate-500">Page {{ $page }} · {{ $users->count() }} shown</p>
            <div class="flex gap-2">
                @if ($page > 1)<a class="secondary-button" href="{{ route('admin.users.index', ['page' => $page - 1, 'q' => request('q'), 'status' => request('status')]) }}">Previous</a>@endif
                @if ($hasNextPage)<a class="secondary-button" href="{{ route('admin.users.index', ['page' => $page + 1, 'q' => request('q'), 'status' => request('status')]) }}">Next</a>@endif
            </div>
        </div>
    </section>
@endsection
