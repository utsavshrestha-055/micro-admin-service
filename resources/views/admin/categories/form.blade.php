@extends('layouts.admin', ['title' => $category ? 'Edit category' : 'Create category'])

@section('content')
    <section class="max-w-3xl rounded-2xl border border-slate-200 bg-white p-6 sm:p-8"><h2 class="text-xl font-semibold">{{ $category ? 'Edit category' : 'Create category' }}</h2><form class="mt-6 space-y-5" method="POST" action="{{ $category ? route('admin.categories.update', $category['id']) : route('admin.categories.store') }}">@csrf @if ($category) @method('PUT') @endif
        <div><label class="form-label" for="name">Name</label><input class="form-input" id="name" name="name" required maxlength="150" value="{{ old('name', $category['name'] ?? '') }}"></div>
        <div><label class="form-label" for="slug">Slug</label><input class="form-input" id="slug" name="slug" required maxlength="150" pattern="[a-z0-9]+(-[a-z0-9]+)*" value="{{ old('slug', $category['slug'] ?? '') }}"></div>
        <div><label class="form-label" for="description">Description</label><textarea class="form-input min-h-24" id="description" name="description" maxlength="2000">{{ old('description', $category['description'] ?? '') }}</textarea></div>
        <div><label class="form-label" for="parent_id">Parent category</label><select class="form-input" id="parent_id" name="parent_id"><option value="">None</option>@foreach ($categories as $option) @if (($option['id'] ?? '') !== ($category['id'] ?? ''))<option value="{{ $option['id'] }}" @selected(old('parent_id', $category['parent_id'] ?? '') === $option['id'])>{{ $option['name'] }}</option>@endif @endforeach</select></div>
        <div><label class="form-label" for="is_active">Status</label><select class="form-input" id="is_active" name="is_active"><option value="1" @selected((string) old('is_active', (int) ($category['is_active'] ?? true)) === '1')>Active</option><option value="0" @selected((string) old('is_active', (int) ($category['is_active'] ?? true)) === '0')>Inactive</option></select></div>
        <div class="flex gap-3"><button class="primary-button" type="submit">{{ $category ? 'Save changes' : 'Create category' }}</button><a class="secondary-button" href="{{ route('admin.categories.index') }}">Cancel</a></div>
    </form></section>
@endsection
