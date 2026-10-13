@extends('layouts.admin', ['title' => 'Create role'])

@section('content')
    <div class="max-w-2xl"><a class="text-sm font-medium text-indigo-600 hover:text-indigo-800" href="{{ route('admin.roles.index') }}">← Back to roles</a>
        <section class="mt-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm shadow-slate-200/30 sm:p-8"><h2 class="text-xl font-semibold tracking-tight">Create a role</h2><p class="mt-1 text-sm text-slate-500">Role names become stable identifiers. Use lowercase letters, numbers, dashes, or underscores.</p>
            <form method="POST" action="{{ route('admin.roles.store') }}" class="mt-6 space-y-5">@csrf
                <div><label class="form-label" for="name">Name</label><input class="form-input" id="name" name="name" required maxlength="100" pattern="[a-z][a-z0-9_-]*" value="{{ old('name') }}" placeholder="support_agent"></div>
                <div><label class="form-label" for="description">Description</label><textarea class="form-input min-h-28 resize-y" id="description" name="description" maxlength="1000" placeholder="What can members of this role do?">{{ old('description') }}</textarea></div>
                <div class="flex justify-end gap-3"><a class="secondary-button" href="{{ route('admin.roles.index') }}">Cancel</a><button class="primary-button" type="submit">Create role</button></div>
            </form>
        </section>
    </div>
@endsection
