@extends('layouts.admin', ['title' => 'Permissions'])

@section('content')
    <section class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end"><div><p class="text-sm text-slate-500">Access control</p><h2 class="mt-1 text-2xl font-semibold tracking-tight">Permissions</h2><p class="mt-1 text-sm text-slate-500">Capabilities are stored and evaluated by the authorization service.</p></div><div class="flex flex-wrap gap-3"><form method="POST" action="{{ route('admin.permissions.seed') }}">@csrf<button class="secondary-button" type="submit">Sync application permissions</button></form><a class="primary-button" href="{{ route('admin.permissions.create') }}"><span aria-hidden="true">＋</span> Create permission</a></div></section>
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm shadow-slate-200/30">
        <div class="border-b border-slate-100 px-5 py-4"><h3 class="text-sm font-semibold">Permission catalog <span class="ml-1 rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-500">{{ count($permissions) }}</span></h3></div>
        <div class="divide-y divide-slate-100">
            @forelse ($permissions as $permission)
                <details class="group px-5 py-4"><summary class="flex cursor-pointer list-none items-center gap-4"><span class="min-w-0 flex-1"><span class="block font-mono text-sm font-medium text-slate-800">{{ $permission['name'] }}</span><span class="mt-1 block text-xs text-slate-500">{{ $permission['description'] ?: 'No description' }}</span></span><span class="text-xs font-medium text-indigo-600 group-open:hidden">Edit</span><span class="hidden text-xs font-medium text-slate-500 group-open:inline">Close</span></summary>
                    <div class="mt-4 rounded-xl bg-slate-50 p-4"><form method="POST" action="{{ route('admin.permissions.update', $permission['id']) }}" class="grid gap-3 sm:grid-cols-[1fr_1.5fr_auto]">@csrf @method('PUT')<div><label class="form-label" for="name-{{ $permission['id'] }}">Name</label><input id="name-{{ $permission['id'] }}" class="form-input py-2 font-mono text-xs" name="name" required maxlength="150" value="{{ $permission['name'] }}"></div><div><label class="form-label" for="description-{{ $permission['id'] }}">Description</label><input id="description-{{ $permission['id'] }}" class="form-input py-2 text-sm" name="description" maxlength="1000" value="{{ $permission['description'] ?? '' }}"></div><div class="flex items-end"><button class="secondary-button w-full" type="submit">Save</button></div></form>
                        <form method="POST" action="{{ route('admin.permissions.destroy', $permission['id']) }}" class="mt-3 text-right" onsubmit="return confirm('Delete {{ $permission['name'] }}? This may change access granted by roles.')">@csrf @method('DELETE')<button class="text-xs font-medium text-rose-600 hover:text-rose-800" type="submit">Delete permission</button></form>
                    </div>
                </details>
            @empty
                <p class="px-5 py-14 text-center text-sm text-slate-500">No permissions are registered yet.</p>
            @endforelse
        </div>
    </section>
@endsection
