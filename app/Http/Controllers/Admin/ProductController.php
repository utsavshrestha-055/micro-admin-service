<?php

namespace App\Http\Controllers\Admin;

use App\Grpc\GrpcRequestException;
use App\Http\Controllers\Controller;
use App\Services\Commerce\CommerceManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use RuntimeException;

class ProductController extends Controller
{
    public function index(Request $request, CommerceManagementService $commerce): View
    {
        $filters = [
            'pageSize' => 25,
            'pageToken' => (string) $request->query('page_token', ''),
            'nameSearch' => (string) $request->query('q', ''),
        ];

        if ($request->filled('category_id')) {
            $filters['categoryId'] = $request->query('category_id');
        }

        if ($request->query->has('is_active') && in_array($request->query('is_active'), ['0', '1'], true)) {
            $filters['isActive'] = $request->boolean('is_active');
        }

        try {
            $response = $commerce->listProducts($filters);
            $products = $response['products'] ?? [];
        } catch (RuntimeException) {
            abort(503, 'The catalog service is temporarily unavailable.');
        }

        return view('admin.products.index', [
            'products' => $products,
            'categories' => $this->categories($commerce),
            'nextPageToken' => $response['next_page_token'] ?? '',
        ]);
    }

    public function create(CommerceManagementService $commerce): View
    {
        return view('admin.products.form', ['product' => null, 'categories' => $this->categories($commerce)]);
    }

    public function store(Request $request, CommerceManagementService $commerce): RedirectResponse
    {
        $validated = $this->validated($request);

        try {
            $commerce->createProduct($this->payload($validated));
        } catch (RuntimeException) {
            return back()->withErrors(['product' => 'The product could not be created. Please try again.'])->withInput();
        }

        return redirect()->route('admin.products.index')->with('status', 'Product created.');
    }

    public function edit(string $productId, CommerceManagementService $commerce): View
    {
        try {
            $page = $commerce->getProductPage($productId);
            $product = $page['product'] ?? null;
        } catch (GrpcRequestException $exception) {
            if ($exception->grpcStatus === 'NOT_FOUND') {
                abort(404);
            }

            abort(503, 'The catalog service is temporarily unavailable.');
        } catch (RuntimeException) {
            abort(503, 'The catalog service is temporarily unavailable.');
        }

        abort_unless(is_array($product), 404);

        return view('admin.products.form', [
            'product' => $product,
            'details' => $page['details'] ?? [],
            'images' => $page['images'] ?? [],
            'categories' => $this->categories($commerce),
        ]);
    }

    public function update(Request $request, string $productId, CommerceManagementService $commerce): RedirectResponse
    {
        $validated = $this->validated($request);

        try {
            $commerce->updateProduct($this->payload($validated) + ['id' => $productId]);
        } catch (RuntimeException) {
            return back()->withErrors(['product' => 'The product could not be updated. Please try again.'])->withInput();
        }

        return redirect()->route('admin.products.index')->with('status', 'Product updated.');
    }

    public function destroy(string $productId, CommerceManagementService $commerce): RedirectResponse
    {
        try {
            $commerce->deleteProduct($productId);
        } catch (RuntimeException) {
            return back()->withErrors(['product' => 'The product could not be deleted. Please try again.']);
        }

        return redirect()->route('admin.products.index')->with('status', 'Product deleted.');
    }

    public function saveDetails(Request $request, string $productId, CommerceManagementService $commerce): RedirectResponse
    {
        $validated = $request->validate([
            'long_description' => ['nullable', 'string', 'max:10000'],
            'specs_json' => ['nullable', 'json'],
            'brand' => ['nullable', 'string', 'max:150'],
            'weight' => ['nullable', 'string', 'max:100'],
            'dimensions_json' => ['nullable', 'json'],
            'seo_title' => ['nullable', 'string', 'max:200'],
            'meta_description' => ['nullable', 'string', 'max:1000'],
            'tags' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            $page = $commerce->getProductPage($productId);
            $commerce->saveProductDetails([
                'productId' => $productId,
                'longDescription' => $validated['long_description'] ?? '',
                'specsJson' => $validated['specs_json'] ?? '',
                'brand' => $validated['brand'] ?? '',
                'weight' => $validated['weight'] ?? '',
                'dimensionsJson' => $validated['dimensions_json'] ?? '',
                'seoTitle' => $validated['seo_title'] ?? '',
                'metaDescription' => $validated['meta_description'] ?? '',
                'tags' => array_values(array_filter(array_map('trim', preg_split('/[\r\n,]+/', $validated['tags'] ?? '') ?: []))),
            ], is_array($page['details'] ?? null));
        } catch (RuntimeException) {
            return back()->withErrors(['details' => 'Product details could not be saved. Please try again.'])->withInput();
        }

        return back()->with('status', 'Product details saved.');
    }

    public function deleteDetails(string $productId, CommerceManagementService $commerce): RedirectResponse
    {
        try {
            $commerce->deleteProductDetails($productId);
        } catch (RuntimeException) {
            return back()->withErrors(['details' => 'Product details could not be deleted. Please try again.']);
        }

        return back()->with('status', 'Product details deleted.');
    }

    public function storeImage(Request $request, string $productId, CommerceManagementService $commerce): RedirectResponse
    {
        $validated = $this->validatedImage($request);

        try {
            $commerce->createProductImage($this->imagePayload($request, $validated, $productId));
        } catch (RuntimeException) {
            return back()->withErrors(['image' => 'The product image could not be created. Please check the image or URL and try again.'])->withInput();
        }

        return back()->with('status', 'Product image added.');
    }

    public function updateImage(Request $request, string $productId, string $imageId, CommerceManagementService $commerce): RedirectResponse
    {
        $validated = $this->validatedImage($request, true);

        try {
            $commerce->updateProductImage($this->imagePayload($request, $validated, $productId) + ['id' => $imageId]);
        } catch (RuntimeException) {
            return back()->withErrors(['image' => 'The product image could not be updated. Please try again.'])->withInput();
        }

        return back()->with('status', 'Product image updated.');
    }

    public function showImage(string $imageId, CommerceManagementService $commerce): Response
    {
        try {
            $image = $commerce->getProductImageData($imageId);
        } catch (GrpcRequestException $exception) {
            abort($exception->grpcStatus === 'NOT_FOUND' ? 404 : 503);
        } catch (RuntimeException) {
            abort(503, 'The catalog service is temporarily unavailable.');
        }

        $contentType = $image['content_type'] ?? '';
        $imageData = $image['image_data'] ?? '';

        abort_unless(in_array($contentType, ['image/jpeg', 'image/png', 'image/webp'], true), 404);

        $decodedImage = is_string($imageData) ? base64_decode($imageData, true) : false;
        abort_unless(is_string($decodedImage), 404);

        return response($decodedImage, 200, [
            'Content-Type' => $contentType,
            'Cache-Control' => 'public, max-age=3600',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function deleteImage(string $productId, string $imageId, CommerceManagementService $commerce): RedirectResponse
    {
        try {
            $commerce->deleteProductImage($imageId);
        } catch (RuntimeException) {
            return back()->withErrors(['image' => 'The product image could not be deleted. A product must retain a primary image.']);
        }

        return back()->with('status', 'Product image deleted.');
    }

    /** @return array<string, mixed> */
    private function validatedImage(Request $request, bool $allowNoReplacement = false): array
    {
        return $request->validate([
            'url' => ['nullable', 'url', 'max:2000', ...($allowNoReplacement ? [] : ['required_without:image_file'])],
            'image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'dimensions:max_width=8000,max_height=8000', 'max:5120', ...($allowNoReplacement ? [] : ['required_without:url'])],
            'alt_text' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_primary' => ['required', 'boolean'],
        ]);
    }

    /** @param array<string, mixed> $validated */
    /** @param array<string, mixed> $validated @return array<string, mixed> */
    private function imagePayload(Request $request, array $validated, string $productId): array
    {
        $uploadedImage = $request->file('image_file');

        return [
            'productId' => $productId,
            'url' => $uploadedImage !== null ? '' : ($validated['url'] ?? ''),
            'altText' => $validated['alt_text'] ?? '',
            'sortOrder' => (int) $validated['sort_order'],
            'isPrimary' => (bool) $validated['is_primary'],
            'imageData' => $uploadedImage?->get(),
            'contentType' => $uploadedImage?->getMimeType() ?? '',
        ];
    }

    /** @return array<string, mixed> */
    private function validated(Request $request): array
    {
        return $request->validate([
            'category_id' => ['required', 'uuid'],
            'name' => ['required', 'string', 'max:200'],
            'slug' => ['required', 'string', 'max:200', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'sku' => ['required', 'string', 'max:100'],
            'short_description' => ['nullable', 'string', 'max:1000'],
            'price_minor' => ['required', 'integer', 'min:0'],
            'currency' => ['required', 'string', 'size:3', 'regex:/^[A-Z]{3}$/'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'is_active' => ['required', 'boolean'],
        ]);
    }

    /** @param array<string, mixed> $validated @return array<string, mixed> */
    private function payload(array $validated): array
    {
        return [
            'categoryId' => $validated['category_id'],
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'sku' => $validated['sku'],
            'shortDescription' => $validated['short_description'] ?? '',
            'priceMinor' => (int) $validated['price_minor'],
            'currency' => $validated['currency'],
            'stockQuantity' => (int) $validated['stock_quantity'],
            'isActive' => (bool) $validated['is_active'],
        ];
    }

    /** @return array<int, array<string, mixed>> */
    private function categories(CommerceManagementService $commerce): array
    {
        try {
            return $commerce->listCategories()['categories'] ?? [];
        } catch (RuntimeException) {
            abort(503, 'The catalog service is temporarily unavailable.');
        }
    }
}
