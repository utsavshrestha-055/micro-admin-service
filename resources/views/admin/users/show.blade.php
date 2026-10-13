@extends('layouts.admin', ['title' => 'User profile'])

@section('content')
    <div class="flex items-center gap-2 text-sm text-slate-500"><a class="hover:text-indigo-600" href="{{ route('admin.users.index') }}">Users</a><span>/</span><span class="text-slate-800">Profile</span></div>
    <div class="grid gap-6 xl:grid-cols-[1.4fr_0.8fr]">
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm shadow-slate-200/30">
            <div class="flex items-center gap-4 border-b border-slate-100 pb-5"><span class="grid size-14 place-items-center rounded-2xl bg-indigo-50 text-lg font-semibold text-indigo-700">{{ strtoupper(substr($user['first_name'] ?? $user['email'] ?? 'U', 0, 1)) }}</span><div><h2 class="text-xl font-semibold tracking-tight">{{ trim(($user['first_name'] ?? '').' '.($user['last_name'] ?? '')) ?: 'Unnamed user' }}</h2><p class="mt-1 text-sm text-slate-500">{{ $user['email'] }}</p></div><span @class(['status-badge ml-auto', 'status-active' => ($user['is_active'] ?? false), 'status-inactive' => ! ($user['is_active'] ?? false)])>{{ ($user['is_active'] ?? false) ? 'Active' : 'Inactive' }}</span></div>
            <form method="POST" action="{{ route('admin.users.update', $user['id']) }}" class="mt-6 space-y-5">
                @csrf @method('PUT')
                <div class="grid gap-4 sm:grid-cols-2">
                    <div><label class="form-label" for="first_name">First name</label><input class="form-input" id="first_name" name="first_name" value="{{ old('first_name', $user['first_name'] ?? '') }}" maxlength="100"></div>
                    <div><label class="form-label" for="last_name">Last name</label><input class="form-input" id="last_name" name="last_name" value="{{ old('last_name', $user['last_name'] ?? '') }}" maxlength="100"></div>
                </div>
                <div><label class="form-label" for="email">Email address</label><input class="form-input" id="email" name="email" type="email" required value="{{ old('email', $user['email']) }}" maxlength="255"></div>
                <div><label class="form-label" for="password">New password <span class="font-normal text-slate-400">(leave blank to keep current)</span></label><input class="form-input" id="password" name="password" type="password" autocomplete="new-password" minlength="12"><p class="form-hint">Use at least 12 characters. The service revokes the user’s sessions after a password change.</p></div>
                <input type="hidden" name="is_active" value="0">
                <label class="flex items-center gap-3 rounded-xl bg-slate-50 px-4 py-3"><input class="size-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" name="is_active" type="checkbox" value="1" @checked(old('is_active', $user['is_active'] ?? false))><span><span class="block text-sm font-medium">Account is active</span><span class="block text-xs text-slate-500">Inactive users cannot authenticate.</span></span></label>
                <div class="flex justify-end"><button class="primary-button" type="submit">Save changes</button></div>
            </form>
        </section>

        <section class="space-y-6">
            <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm shadow-slate-200/30">
                <h3 class="font-semibold tracking-tight">Assigned roles</h3><p class="mt-1 text-sm text-slate-500">Roles are read from the authorization service.</p>
                <div class="mt-4 space-y-2">
                    @forelse ($roles as $role)
                        <div class="flex items-center justify-between gap-3 rounded-xl border border-slate-100 px-3 py-2.5"><span class="text-sm font-medium text-slate-700">{{ $role }}</span><form method="POST" action="{{ route('admin.users.roles.unassign', $user['id']) }}" onsubmit="return confirm('Remove the {{ $role }} role from this user?')">@csrf @method('DELETE')<input type="hidden" name="role" value="{{ $role }}"><button class="text-xs font-medium text-rose-600 hover:text-rose-800" type="submit">Remove</button></form></div>
                    @empty
                        <p class="rounded-xl bg-slate-50 px-4 py-5 text-center text-sm text-slate-500">No roles assigned.</p>
                    @endforelse
                </div>
                <form method="POST" action="{{ route('admin.users.roles.assign', $user['id']) }}" class="mt-5 border-t border-slate-100 pt-5">
                    @csrf
                    <label class="form-label" for="role">Assign a role</label>
                    <div class="flex gap-2"><select class="form-input min-w-0" id="role" name="role" required><option value="">Choose a role</option>@foreach ($availableRoles as $role)<option value="{{ $role['name'] }}">{{ $role['name'] }}</option>@endforeach</select><button class="secondary-button shrink-0" type="submit">Assign</button></div>
                </form>
            </article>
            <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm shadow-slate-200/30">
                <h3 class="font-semibold tracking-tight">Effective permissions</h3><p class="mt-1 text-sm text-slate-500">Combined from this user’s assigned roles.</p>
                <div class="mt-4 flex flex-wrap gap-2">
                    @forelse ($effectivePermissions as $permission)
                        <span class="rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1.5 font-mono text-[11px] text-slate-600">{{ $permission['name'] }}</span>
                    @empty
                        <p class="w-full rounded-xl bg-slate-50 px-4 py-5 text-center text-sm text-slate-500">No permissions found for assigned roles.</p>
                    @endforelse
                </div>
            </article>
            <article class="rounded-2xl border border-rose-200 bg-white p-6 shadow-sm shadow-slate-200/30">
                <h3 class="font-semibold text-rose-800">Delete account</h3><p class="mt-1 text-sm leading-6 text-slate-500">This permanently removes the user through the authentication service.</p>
                <form method="POST" action="{{ route('admin.users.destroy', $user['id']) }}" class="mt-4" onsubmit="return confirm('Permanently delete this user? This action cannot be undone.')">@csrf @method('DELETE')<button class="danger-button" type="submit">Delete user</button></form>
            </article>
        </section>
    </div>
@endsection
