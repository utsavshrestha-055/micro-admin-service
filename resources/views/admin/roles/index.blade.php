@extends('layouts.admin', ['title' => 'Roles'])

@section('content')
    <section class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end"><div><p class="text-sm text-slate-500">Access control</p><h2 class="mt-1 text-2xl font-semibold tracking-tight">Roles</h2><p class="mt-1 text-sm text-slate-500">Role records are managed by the authentication service.</p></div><div class="flex flex-wrap gap-3"><form method="POST" action="{{ route('admin.roles.sync') }}">@csrf<button class="secondary-button" type="submit">Sync application roles</button></form><a class="primary-button" href="{{ route('admin.roles.create') }}"><span aria-hidden="true">＋</span> Create role</a></div></section>
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm shadow-slate-200/30">
        <div class="border-b border-slate-100 px-5 py-4"><h3 class="text-sm font-semibold">Role directory <span class="ml-1 rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-500">{{ count($roles) }}</span></h3></div>
        <div class="overflow-x-auto"><table class="min-w-full divide-y divide-slate-100 text-left text-sm"><thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500"><tr><th class="px-5 py-3 font-semibold">Role</th><th class="px-5 py-3 font-semibold">Description</th><th class="px-5 py-3 font-semibold">Status</th><th class="px-5 py-3 text-right font-semibold">Action</th></tr></thead><tbody class="divide-y divide-slate-100">
            @forelse ($roles as $role)
                <tr class="hover:bg-slate-50/70"><td class="px-5 py-4 font-medium text-slate-800">{{ $role['name'] }}</td><td class="max-w-lg px-5 py-4 text-slate-500">{{ $role['description'] ?: '—' }}</td><td class="px-5 py-4"><span @class(['status-badge', 'status-active' => ($role['is_active'] ?? false), 'status-inactive' => ! ($role['is_active'] ?? false)])>{{ ($role['is_active'] ?? false) ? 'Active' : 'Inactive' }}</span></td><td class="px-5 py-4 text-right"><a href="{{ route('admin.roles.show', $role['id']) }}" class="font-medium text-indigo-600 hover:text-indigo-800">Manage <span aria-hidden="true">→</span></a></td></tr>
            @empty
                <tr><td colspan="4" class="px-5 py-14 text-center"><p class="font-medium text-slate-700">No roles available</p><p class="mt-1 text-sm text-slate-500">Create a role to organize permissions for your team.</p></td></tr>
            @endforelse
        </tbody></table></div>
    </section>
@endsection
