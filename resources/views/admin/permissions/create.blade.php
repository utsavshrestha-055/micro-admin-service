@extends('layouts.admin', ['title' => 'Create permission'])

@section('content')
    <div class="max-w-2xl"><a class="text-sm font-medium text-indigo-600 hover:text-indigo-800" href="{{ route('admin.permissions.index') }}">← Back to permissions</a>
        <section class="mt-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm shadow-slate-200/30 sm:p-8"><h2 class="text-xl font-semibold tracking-tight">Create a permission</h2><p class="mt-1 text-sm text-slate-500">Use a stable, namespaced key such as <code class="rounded bg-slate-100 px-1.5 py-0.5 text-xs">orders.read</code>.</p>
            <form method="POST" action="{{ route('admin.permissions.store') }}" class="mt-6 space-y-5">@csrf
                <div><label class="form-label" for="name">Permission name</label><input class="form-input font-mono" id="name" name="name" required maxlength="150" pattern="[a-z][a-z0-9_.-]*" value="{{ old('name') }}" placeholder="catalog.export"></div>
                <div><label class="form-label" for="description">Description</label><textarea class="form-input min-h-28 resize-y" id="description" name="description" maxlength="1000" placeholder="Describe which action this permission allows.">{{ old('description') }}</textarea></div>
                <div class="flex justify-end gap-3"><a class="secondary-button" href="{{ route('admin.permissions.index') }}">Cancel</a><button class="primary-button" type="submit">Create permission</button></div>
            </form>
        </section>
    </div>
@endsection
