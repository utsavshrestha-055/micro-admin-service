<?php

namespace App\Http\Controllers\Admin;

use App\Grpc\GrpcRequestException;
use App\Http\Controllers\Controller;
use App\Services\Commerce\CommerceManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class CategoryController extends Controller
{
    public function index(Request $request, CommerceManagementService $commerce): View
    {
        try {
            $pageToken = (string) $request->query('page_token', '');
            $response = $commerce->listCategories(25, $pageToken);
            $categories = $response['categories'] ?? [];
        } catch (RuntimeException) {
            abort(503, 'The catalog service is temporarily unavailable.');
        }

        return view('admin.categories.index', ['categories' => $categories, 'nextPageToken' => $response['next_page_token'] ?? '']);
    }

    public function create(CommerceManagementService $commerce): View
    {
        return view('admin.categories.form', ['category' => null, 'categories' => $this->categories($commerce)]);
    }

    public function store(Request $request, CommerceManagementService $commerce): RedirectResponse
    {
        $validated = $this->validated($request);

        try {
            $commerce->createCategory($this->payload($validated));
        } catch (RuntimeException) {
            return back()->withErrors(['category' => 'The category could not be created. Please try again.'])->withInput();
        }

        return redirect()->route('admin.categories.index')->with('status', 'Category created.');
    }

    public function edit(string $categoryId, CommerceManagementService $commerce): View
    {
        try {
            $category = $commerce->getCategory($categoryId)['category'] ?? null;
        } catch (GrpcRequestException $exception) {
            if ($exception->grpcStatus === 'NOT_FOUND') {
                abort(404);
            }

            abort(503, 'The catalog service is temporarily unavailable.');
        } catch (RuntimeException) {
            abort(503, 'The catalog service is temporarily unavailable.');
        }

        abort_unless(is_array($category), 404);

        return view('admin.categories.form', ['category' => $category, 'categories' => $this->categories($commerce)]);
    }

    public function update(Request $request, string $categoryId, CommerceManagementService $commerce): RedirectResponse
    {
        $validated = $this->validated($request);

        try {
            $commerce->updateCategory($this->payload($validated) + ['id' => $categoryId]);
        } catch (RuntimeException) {
            return back()->withErrors(['category' => 'The category could not be updated. Please try again.'])->withInput();
        }

        return redirect()->route('admin.categories.index')->with('status', 'Category updated.');
    }

    public function destroy(string $categoryId, CommerceManagementService $commerce): RedirectResponse
    {
        try {
            $commerce->deleteCategory($categoryId);
        } catch (RuntimeException) {
            return back()->withErrors(['category' => 'The category could not be deleted. It may still contain active products or child categories.']);
        }

        return redirect()->route('admin.categories.index')->with('status', 'Category deleted.');
    }

    /** @return array<string, mixed> */
    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['required', 'string', 'max:150', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'description' => ['nullable', 'string', 'max:2000'],
            'parent_id' => ['nullable', 'uuid'],
            'is_active' => ['required', 'boolean'],
        ]);
    }

    /** @param array<string, mixed> $validated @return array<string, mixed> */
    private function payload(array $validated): array
    {
        return [
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? '',
            'parentId' => $validated['parent_id'] ?? '',
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
