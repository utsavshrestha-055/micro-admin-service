@extends('layouts.admin', ['title' => 'Categories'])

@section('content')
    <section class="flex items-end justify-between gap-4"><div><p class="text-sm text-slate-500">Catalog organization</p><h2 class="mt-1 text-2xl font-semibold">Categories</h2></div><a class="primary-button" href="{{ route('admin.categories.create') }}">Create category</a></section>
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white"><div class="overflow-x-auto"><table class="min-w-full divide-y divide-slate-100 text-left text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-5 py-3">Name</th><th class="px-5 py-3">Slug</th><th class="px-5 py-3">Status</th><th class="px-5 py-3 text-right">Actions</th></tr></thead><tbody class="divide-y divide-slate-100">
        @forelse ($categories as $category)
            <tr><td class="px-5 py-4 font-medium">{{ $category['name'] }}</td><td class="px-5 py-4 font-mono text-xs">{{ $category['slug'] }}</td><td class="px-5 py-4">{{ ($category['is_active'] ?? false) ? 'Active' : 'Inactive' }}</td><td class="px-5 py-4"><div class="flex justify-end gap-3"><a class="text-indigo-600" href="{{ route('admin.categories.edit', $category['id']) }}">Edit</a><form method="POST" action="{{ route('admin.categories.destroy', $category['id']) }}" onsubmit="return confirm('Delete this category?')">@csrf @method('DELETE')<button class="text-rose-600" type="submit">Delete</button></form></div></td></tr>
        @empty
            <tr><td class="px-5 py-8 text-center text-slate-500" colspan="4">No categories found.</td></tr>
        @endforelse
    </tbody></table></div>@if ($nextPageToken !== '')<form class="border-t border-slate-100 p-4 text-right" method="GET" action="{{ route('admin.categories.index') }}"><input type="hidden" name="page_token" value="{{ $nextPageToken }}"><button class="secondary-button" type="submit">Next page →</button></form>@endif</section>
@endsection
