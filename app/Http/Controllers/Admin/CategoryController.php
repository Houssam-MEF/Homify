<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'can:manage-catalog']);
    }

    /**
     * Display a listing of the categories.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Category::with(['parent', 'children'])
            ->withCount(['products', 'children']);

        // Search functionality
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by parent category
        if ($request->filled('parent')) {
            if ($request->parent === 'root') {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id', $request->parent);
            }
        } else {
            // Par défaut, afficher seulement les catégories principales
            $query->whereNull('parent_id');
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $categories = $query->orderBy('name')->paginate(15)->appends($request->query());

        // Get parent categories for filter dropdown
        $parentCategories = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.categories.index', compact('categories', 'parentCategories'));
    }

    /**
     * Show the form for creating a new category.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $parentCategories = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.categories.create', compact('parentCategories'));
    }

    /**
     * Store a newly created category in storage.
     *
     * @param StoreCategoryRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreCategoryRequest $request)
    {
        try {
            $category = Category::create($request->validated());

            return redirect()
                ->route('admin.categories.index')
                ->with('success', "Category '{$category->name}' created successfully!");
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => 'Failed to create category: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified category.
     *
     * @param Category $category
     * @return \Illuminate\View\View
     */
    public function show(Category $category)
    {
        $category->load([
            'parent',
            'children' => function ($query) {
                $query->withCount('products');
            },
            'products' => function ($query) {
                $query->with('images')->take(10);
            }
        ]);

        return view('admin.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified category.
     *
     * @param Category $category
     * @return \Illuminate\View\View
     */
    public function edit(Category $category)
    {
        $parentCategories = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->where('id', '!=', $category->id)
            ->orderBy('name')
            ->get();

        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    /**
     * Update the specified category in storage.
     *
     * @param UpdateCategoryRequest $request
     * @param Category $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        try {
            $category->update($request->validated());

            return redirect()
                ->route('admin.categories.index')
                ->with('success', "Category '{$category->name}' updated successfully!");
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => 'Failed to update category: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified category from storage.
     *
     * @param Category $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Category $category)
    {
        try {
            // Check if category has products
            if ($category->products()->count() > 0) {
                return redirect()
                    ->route('admin.categories.index')
                    ->withErrors(['error' => 'Cannot delete category that contains products. Move or delete products first.']);
            }

            // Check if category has child categories
            if ($category->children()->count() > 0) {
                return redirect()
                    ->route('admin.categories.index')
                    ->withErrors(['error' => 'Cannot delete category that has child categories. Delete child categories first.']);
            }

            $categoryName = $category->name;
            $category->delete();

            return redirect()
                ->route('admin.categories.index')
                ->with('success', "Category '{$categoryName}' deleted successfully!");
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.categories.index')
                ->withErrors(['error' => 'Failed to delete category: ' . $e->getMessage()]);
        }
    }

    /**
     * Toggle category status (active/inactive).
     *
     * @param Category $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleStatus(Category $category)
    {
        try {
            $category->update(['is_active' => !$category->is_active]);

            $status = $category->is_active ? 'activated' : 'deactivated';

            return redirect()
                ->back()
                ->with('success', "Category '{$category->name}' {$status} successfully!");
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => 'Failed to update category status: ' . $e->getMessage()]);
        }
    }

    /**
     * Get categories as JSON for AJAX requests.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiIndex(Request $request)
    {
        $query = Category::where('is_active', true);

        if ($request->filled('parent_id')) {
            $query->where('parent_id', $request->parent_id);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $categories = $query->orderBy('name')->get(['id', 'name', 'parent_id']);

        return response()->json($categories);
    }
}

