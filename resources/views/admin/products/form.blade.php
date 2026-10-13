@extends('layouts.admin', ['title' => $product ? 'Edit product' : 'Create product'])

@section('content')
    <section class="max-w-3xl rounded-2xl border border-slate-200 bg-white p-6 sm:p-8"><h2 class="text-xl font-semibold">{{ $product ? 'Edit product' : 'Create product' }}</h2><form class="mt-6 grid gap-5 sm:grid-cols-2" method="POST" action="{{ $product ? route('admin.products.update', $product['id']) : route('admin.products.store') }}">@csrf @if ($product) @method('PUT') @endif
        <div class="sm:col-span-2"><label class="form-label" for="name">Name</label><input class="form-input" id="name" name="name" required maxlength="200" value="{{ old('name', $product['name'] ?? '') }}"></div>
        <div><label class="form-label" for="slug">Slug</label><input class="form-input" id="slug" name="slug" required maxlength="200" value="{{ old('slug', $product['slug'] ?? '') }}"></div>
        <div><label class="form-label" for="sku">SKU</label><input class="form-input" id="sku" name="sku" required maxlength="100" value="{{ old('sku', $product['sku'] ?? '') }}"></div>
        <div><label class="form-label" for="category_id">Category</label><select class="form-input" id="category_id" name="category_id" required><option value="">Choose category</option>@foreach ($categories as $category)<option value="{{ $category['id'] }}" @selected(old('category_id', $product['category_id'] ?? '') === $category['id'])>{{ $category['name'] }}</option>@endforeach</select></div>
        <div><label class="form-label" for="currency">Currency</label><input class="form-input" id="currency" name="currency" required maxlength="3" pattern="[A-Z]{3}" value="{{ old('currency', $product['currency'] ?? 'USD') }}"></div>
        <div><label class="form-label" for="price_minor">Price (minor units)</label><input class="form-input" id="price_minor" name="price_minor" type="number" min="0" step="1" required value="{{ old('price_minor', $product['price_minor'] ?? 0) }}"></div>
        <div><label class="form-label" for="stock_quantity">Stock quantity</label><input class="form-input" id="stock_quantity" name="stock_quantity" type="number" min="0" step="1" required value="{{ old('stock_quantity', $product['stock_quantity'] ?? 0) }}"></div>
        <div class="sm:col-span-2"><label class="form-label" for="short_description">Short description</label><textarea class="form-input min-h-24" id="short_description" name="short_description" maxlength="1000">{{ old('short_description', $product['short_description'] ?? '') }}</textarea></div>
        <div><label class="form-label" for="is_active">Status</label><select class="form-input" id="is_active" name="is_active"><option value="1" @selected((string) old('is_active', (int) ($product['is_active'] ?? true)) === '1')>Active</option><option value="0" @selected((string) old('is_active', (int) ($product['is_active'] ?? true)) === '0')>Inactive</option></select></div>
        <div class="flex items-end gap-3"><button class="primary-button" type="submit">{{ $product ? 'Save changes' : 'Create product' }}</button><a class="secondary-button" href="{{ route('admin.products.index') }}">Cancel</a></div>
    </form>
    @if ($product)
        <section class="mt-8 border-t border-slate-100 pt-8"><h3 class="text-lg font-semibold">Product details</h3><form class="mt-4 grid gap-4 sm:grid-cols-2" method="POST" action="{{ route('admin.products.details.update', $product['id']) }}">@csrf @method('PUT')
            <div class="sm:col-span-2"><label class="form-label" for="long_description">Long description</label><textarea class="form-input min-h-28" id="long_description" name="long_description" maxlength="10000">{{ old('long_description', $details['long_description'] ?? '') }}</textarea></div>
            <div><label class="form-label" for="brand">Brand</label><input class="form-input" id="brand" name="brand" maxlength="150" value="{{ old('brand', $details['brand'] ?? '') }}"></div>
            <div><label class="form-label" for="weight">Weight</label><input class="form-input" id="weight" name="weight" maxlength="100" value="{{ old('weight', $details['weight'] ?? '') }}"></div>
            <div><label class="form-label" for="specs_json">Specifications (JSON)</label><textarea class="form-input min-h-24 font-mono text-xs" id="specs_json" name="specs_json">{{ old('specs_json', $details['specs_json'] ?? '') }}</textarea></div>
            <div><label class="form-label" for="dimensions_json">Dimensions (JSON)</label><textarea class="form-input min-h-24 font-mono text-xs" id="dimensions_json" name="dimensions_json">{{ old('dimensions_json', $details['dimensions_json'] ?? '') }}</textarea></div>
            <div><label class="form-label" for="seo_title">SEO title</label><input class="form-input" id="seo_title" name="seo_title" maxlength="200" value="{{ old('seo_title', $details['seo_title'] ?? '') }}"></div>
            <div><label class="form-label" for="meta_description">Meta description</label><input class="form-input" id="meta_description" name="meta_description" maxlength="1000" value="{{ old('meta_description', $details['meta_description'] ?? '') }}"></div>
            <div class="sm:col-span-2"><label class="form-label" for="tags">Tags (comma or line separated)</label><textarea class="form-input min-h-20" id="tags" name="tags">{{ old('tags', implode(', ', $details['tags'] ?? [])) }}</textarea></div>
            <div class="flex items-end gap-3"><button class="primary-button" type="submit">Save details</button></div>
        </form>
        @if ($details !== [])<form class="mt-3" method="POST" action="{{ route('admin.products.details.destroy', $product['id']) }}">@csrf @method('DELETE')<button class="text-sm text-rose-600" type="submit">Delete product details</button></form>@endif
        </section>
        <section class="mt-8 border-t border-slate-100 pt-8"><h3 class="text-lg font-semibold">Product images</h3><div class="mt-4 space-y-4">
            @foreach ($images as $image)
                <div class="grid gap-3 rounded-xl border border-slate-200 p-4 sm:grid-cols-2"><form class="contents" method="POST" enctype="multipart/form-data" action="{{ route('admin.products.images.update', [$product['id'], $image['id']]) }}">@csrf @method('PUT')
                    <div class="sm:col-span-2"><img class="mb-3 h-32 w-32 rounded-lg border border-slate-200 object-cover" src="{{ ($image['url'] ?? '') ?: route('product-images.show', $image['id']) }}" alt="{{ $image['alt_text'] ?? '' }}"><label class="form-label" for="url-{{ $image['id'] }}">Image URL (optional)</label><input class="form-input" id="url-{{ $image['id'] }}" name="url" type="url" maxlength="2000" value="{{ old('url', $image['url'] ?? '') }}"></div>
                    <div class="sm:col-span-2"><label class="form-label" for="file-{{ $image['id'] }}">Or upload a replacement</label><input class="form-input" id="file-{{ $image['id'] }}" name="image_file" type="file" accept="image/jpeg,image/png,image/webp"><p class="form-hint">JPEG, PNG, or WebP; max 5 MB.</p></div>
                    <div><label class="form-label" for="alt-{{ $image['id'] }}">Alt text</label><input class="form-input" id="alt-{{ $image['id'] }}" name="alt_text" maxlength="500" value="{{ old('alt_text', $image['alt_text'] ?? '') }}"></div>
                    <div><label class="form-label" for="sort-{{ $image['id'] }}">Sort order</label><input class="form-input" id="sort-{{ $image['id'] }}" name="sort_order" type="number" min="0" required value="{{ old('sort_order', $image['sort_order'] ?? 0) }}"></div>
                    <div><label class="form-label" for="primary-{{ $image['id'] }}">Primary image</label><select class="form-input" id="primary-{{ $image['id'] }}" name="is_primary"><option value="1" @selected((bool) ($image['is_primary'] ?? false))>Yes</option><option value="0" @selected(! (bool) ($image['is_primary'] ?? false))>No</option></select></div>
                    <div class="flex items-end gap-3"><button class="secondary-button" type="submit">Save image</button></div></form><form class="self-end" method="POST" action="{{ route('admin.products.images.destroy', [$product['id'], $image['id']]) }}">@csrf @method('DELETE')<button class="text-sm text-rose-600" type="submit">Delete</button></form></div>
            @endforeach
        </div>
        <form class="mt-6 grid gap-3 rounded-xl border border-dashed border-slate-300 p-4 sm:grid-cols-2" method="POST" enctype="multipart/form-data" action="{{ route('admin.products.images.store', $product['id']) }}">@csrf<h4 class="font-medium sm:col-span-2">Add image</h4>
            <div class="sm:col-span-2"><label class="form-label" for="new-image-url">Image URL (optional when uploading)</label><input class="form-input" id="new-image-url" name="url" type="url" maxlength="2000" value="{{ old('url') }}"></div>
            <div class="sm:col-span-2"><label class="form-label" for="new-image-file">Upload image</label><input class="form-input" id="new-image-file" name="image_file" type="file" accept="image/jpeg,image/png,image/webp"><p class="form-hint">JPEG, PNG, or WebP; max 5 MB. Uploaded images are stored in the product service.</p></div>
            <div><label class="form-label" for="new-image-alt">Alt text</label><input class="form-input" id="new-image-alt" name="alt_text" maxlength="500" value="{{ old('alt_text') }}"></div>
            <div><label class="form-label" for="new-image-sort">Sort order</label><input class="form-input" id="new-image-sort" name="sort_order" type="number" min="0" required value="{{ old('sort_order', 0) }}"></div>
            <div><label class="form-label" for="new-image-primary">Primary image</label><select class="form-input" id="new-image-primary" name="is_primary"><option value="0">No</option><option value="1">Yes</option></select></div>
            <div class="flex items-end"><button class="primary-button" type="submit">Add image</button></div>
        </form></section>
    @endif
    </section>
@endsection
