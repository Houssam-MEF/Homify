<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'can:manage-catalog']);
    }

    /**
     * Display the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        $stats = [
            'total_products' => Product::count(),
            'total_categories' => Category::count(),
            'total_orders' => \App\Models\Order::count(),
            'total_users' => \App\Models\User::count(),
            'revenue' => \App\Models\Order::where('status', 'completed')->sum('total_amount') / 100, // Convert from cents
        ];

        return view('admin.dashboard', compact('stats'));
    }

    /**
     * Display a listing of the products.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'images'])
            ->withCount('images');

        // Search functionality
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Filter by stock
        if ($request->filled('stock')) {
            if ($request->stock === 'in_stock') {
                $query->where('stock', '>', 0);
            } elseif ($request->stock === 'out_of_stock') {
                $query->where('stock', '<=', 0);
            } elseif ($request->stock === 'low_stock') {
                $query->where('stock', '>', 0)->where('stock', '<=', 10);
            }
        }

        $products = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->appends($request->query());

        // Get categories for filter dropdown
        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new product.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created product in storage.
     *
     * @param StoreProductRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreProductRequest $request)
    {
        try {
            $product = Product::create($request->validated());

            // Handle image uploads
            if ($request->hasFile('images')) {
                $this->handleImageUploads($product, $request->file('images'));
            }

            return redirect()
                ->route('admin.products.index')
                ->with('success', "Product '{$product->name}' created successfully!");
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => 'Failed to create product: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified product.
     *
     * @param Product $product
     * @return \Illuminate\View\View
     */
    public function show(Product $product)
    {
        $product->load(['category', 'images' => function ($query) {
            $query->orderBy('sort');
        }]);

        return view('admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     *
     * @param Product $product
     * @return \Illuminate\View\View
     */
    public function edit(Product $product)
    {
        $product->load(['images' => function ($query) {
            $query->orderBy('sort');
        }]);

        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product in storage.
     *
     * @param UpdateProductRequest $request
     * @param Product $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        try {
            $product->update($request->validated());

            // Handle image removals
            if ($request->has('remove_images')) {
                $this->handleImageRemovals($request->remove_images);
            }

            // Handle new image uploads
            if ($request->hasFile('images')) {
                $this->handleImageUploads($product, $request->file('images'));
            }

            return redirect()
                ->route('admin.products.index')
                ->with('success', "Product '{$product->name}' updated successfully!");
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => 'Failed to update product: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified product from storage.
     *
     * @param Product $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Product $product)
    {
        try {
            // Check if product has associated orders
            if ($product->orderItems()->count() > 0) {
                return redirect()
                    ->route('admin.products.index')
                    ->withErrors(['error' => 'Cannot delete product that has been ordered. Consider deactivating instead.']);
            }

            $productName = $product->name;

            // Delete associated images from storage
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->path);
                $image->delete();
            }

            // Soft delete the product
            $product->delete();

            return redirect()
                ->route('admin.products.index')
                ->with('success', "Product '{$productName}' deleted successfully!");
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.products.index')
                ->withErrors(['error' => 'Failed to delete product: ' . $e->getMessage()]);
        }
    }

    /**
     * Restore a soft-deleted product.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore($id)
    {
        try {
            $product = Product::withTrashed()->findOrFail($id);
            $product->restore();

            return redirect()
                ->route('admin.products.index')
                ->with('success', "Product '{$product->name}' restored successfully!");
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.products.index')
                ->withErrors(['error' => 'Failed to restore product: ' . $e->getMessage()]);
        }
    }

    /**
     * Toggle product status (active/inactive).
     *
     * @param Product $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleStatus(Product $product)
    {
        try {
            $product->update(['is_active' => !$product->is_active]);

            $status = $product->is_active ? 'activated' : 'deactivated';

            return redirect()
                ->back()
                ->with('success', "Product '{$product->name}' {$status} successfully!");
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => 'Failed to update product status: ' . $e->getMessage()]);
        }
    }

    /**
     * Bulk update stock for multiple products.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bulkUpdateStock(Request $request)
    {
        $request->validate([
            'products' => ['required', 'array'],
            'products.*.id' => ['required', 'exists:products,id'],
            'products.*.stock' => ['required', 'integer', 'min:0'],
        ]);

        try {
            $updatedCount = 0;

            foreach ($request->products as $productData) {
                $product = Product::find($productData['id']);
                if ($product) {
                    $product->update(['stock' => $productData['stock']]);
                    $updatedCount++;
                }
            }

            return redirect()
                ->back()
                ->with('success', "Updated stock for {$updatedCount} products successfully!");
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => 'Failed to update stock: ' . $e->getMessage()]);
        }
    }

    /**
     * Handle image uploads for a product.
     *
     * @param Product $product
     * @param array $images
     * @return void
     */
    protected function handleImageUploads(Product $product, array $images): void
    {
        $maxSort = $product->images()->max('sort') ?? -1;

        foreach ($images as $index => $image) {
            $path = $image->store('products', 'public');

            ProductImage::create([
                'product_id' => $product->id,
                'path' => $path,
                'alt' => $product->name . ' image',
                'sort' => $maxSort + $index + 1,
            ]);
        }
    }

    /**
     * Handle image removals.
     *
     * @param array $imageIds
     * @return void
     */
    protected function handleImageRemovals(array $imageIds): void
    {
        $images = ProductImage::whereIn('id', $imageIds)->get();

        foreach ($images as $image) {
            Storage::disk('public')->delete($image->path);
            $image->delete();
        }
    }

    /**
     * Delete a specific product image.
     *
     * @param ProductImage $image
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteImage(ProductImage $image)
    {
        try {
            Storage::disk('public')->delete($image->path);
            $image->delete();

            return redirect()
                ->back()
                ->with('success', 'Image deleted successfully!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => 'Failed to delete image: ' . $e->getMessage()]);
        }
    }

    /**
     * Update image sort order.
     *
     * @param Request $request
     * @param Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateImageOrder(Request $request, Product $product)
    {
        $request->validate([
            'images' => ['required', 'array'],
            'images.*.id' => ['required', 'exists:product_images,id'],
            'images.*.sort' => ['required', 'integer', 'min:0'],
        ]);

        try {
            foreach ($request->images as $imageData) {
                ProductImage::where('id', $imageData['id'])
                    ->where('product_id', $product->id)
                    ->update(['sort' => $imageData['sort']]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
