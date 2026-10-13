@extends('layouts.admin', ['title' => 'Role details'])

@section('content')
    <div class="flex items-center gap-2 text-sm text-slate-500"><a class="hover:text-indigo-600" href="{{ route('admin.roles.index') }}">Roles</a><span>/</span><span class="text-slate-800">{{ $role['name'] }}</span></div>
    <div class="grid gap-6 xl:grid-cols-[0.9fr_1.3fr]">
        <section class="h-fit rounded-2xl border border-slate-200 bg-white p-6 shadow-sm shadow-slate-200/30">
            <h2 class="text-xl font-semibold tracking-tight">Role settings</h2><p class="mt-1 text-sm text-slate-500">Changes are sent to the authorization service.</p>
            <form method="POST" action="{{ route('admin.roles.update', $role['id']) }}" class="mt-6 space-y-5">@csrf @method('PUT')
                <div><label class="form-label" for="name">Name</label><input class="form-input" id="name" name="name" required maxlength="100" pattern="[a-z][a-z0-9_-]*" value="{{ old('name', $role['name']) }}"></div>
                <div><label class="form-label" for="description">Description</label><textarea class="form-input min-h-28 resize-y" id="description" name="description" maxlength="1000">{{ old('description', $role['description'] ?? '') }}</textarea></div>
                <input type="hidden" name="is_active" value="0">
                <label class="flex items-center gap-3 rounded-xl bg-slate-50 px-4 py-3"><input class="size-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" name="is_active" type="checkbox" value="1" @checked(old('is_active', $role['is_active'] ?? false))><span><span class="block text-sm font-medium">Role is active</span><span class="block text-xs text-slate-500">Inactive roles do not grant permissions.</span></span></label>
                <button class="primary-button w-full" type="submit">Save role</button>
            </form>
            <form method="POST" action="{{ route('admin.roles.destroy', $role['id']) }}" class="mt-3" onsubmit="return confirm('Delete the {{ $role['name'] }} role? User assignments and permissions for this role will be removed by the service.')">@csrf @method('DELETE')<button class="danger-button w-full" type="submit">Delete role</button></form>
        </section>
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm shadow-slate-200/30">
            <div class="border-b border-slate-100 px-5 py-4"><h2 class="font-semibold tracking-tight">Role permissions</h2><p class="mt-1 text-sm text-slate-500">{{ count($assignedPermissionIds) }} assigned · Manage access with care.</p></div>
            <div class="divide-y divide-slate-100">
                @forelse ($permissions as $permission)
                    @php($assigned = in_array($permission['id'], $assignedPermissionIds, true))
                    <div class="flex flex-col gap-3 px-5 py-4 sm:flex-row sm:items-center"><div class="min-w-0 flex-1"><p class="font-mono text-sm font-medium text-slate-800">{{ $permission['name'] }}</p><p class="mt-1 text-xs text-slate-500">{{ $permission['description'] ?: 'No description' }}</p></div>
                        @if ($assigned)
                            <form method="POST" action="{{ route('admin.roles.permissions.revoke', $role['id']) }}" onsubmit="return confirm('Revoke {{ $permission['name'] }} from this role?')">@csrf @method('DELETE')<input type="hidden" name="permission_id" value="{{ $permission['id'] }}"><button class="secondary-button text-rose-700" type="submit">Revoke</button></form>
                        @else
                            <form method="POST" action="{{ route('admin.roles.permissions.grant', $role['id']) }}">@csrf<input type="hidden" name="permission_id" value="{{ $permission['id'] }}"><button class="secondary-button" type="submit">Grant</button></form>
                        @endif
                    </div>
                @empty
                    <p class="px-5 py-12 text-center text-sm text-slate-500">No permissions are registered.</p>
                @endforelse
            </div>
        </section>
    </div>
@endsection
