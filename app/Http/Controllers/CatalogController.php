<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    /**
     * Display the home page with featured categories and products.
     *
     * @return \Illuminate\View\View
     */
    public function home()
    {
        $featuredCategories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->with(['activeChildren', 'activeProducts'])
            ->take(16)
            ->get();

        $featuredProducts = Product::where('is_active', true)
            ->where('stock', '>', 0)
            ->with(['category', 'images'])
            ->inRandomOrder()
            ->take(8)
            ->get();

        return view('catalog.home', compact('featuredCategories', 'featuredProducts'));
    }

    /**
     * Display products for a specific category.
     *
     * @param string $slug
     * @return \Illuminate\View\View
     */
    public function category(string $slug)
    {
        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->with(['parent', 'activeChildren'])
            ->firstOrFail();

        $products = Product::where('category_id', $category->id)
            ->where('is_active', true)
            ->with(['images'])
            ->paginate(12);

        // Get breadcrumbs
        $breadcrumbs = $this->getCategoryBreadcrumbs($category);

        return view('catalog.category', compact('category', 'products', 'breadcrumbs'));
    }

    /**
     * Display a specific product.
     *
     * @param string $slug
     * @return \Illuminate\View\View
     */
    public function product(string $slug)
    {
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->with(['category.parent', 'images'])
            ->firstOrFail();

        // Get related products from the same category
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->with(['images'])
            ->take(4)
            ->get();

        // Get breadcrumbs
        $breadcrumbs = $this->getProductBreadcrumbs($product);

        return view('catalog.product', compact('product', 'relatedProducts', 'breadcrumbs'));
    }

    /**
     * Search for products.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $categoryId = $request->get('category');

        $products = Product::where('is_active', true)
            ->when($query, function ($queryBuilder) use ($query) {
                $queryBuilder->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%");
                });
            })
            ->when($categoryId, function ($queryBuilder) use ($categoryId) {
                $queryBuilder->where('category_id', $categoryId);
            })
            ->with(['category', 'images'])
            ->paginate(12)
            ->appends($request->query());

        $categories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->get();

        return view('catalog.search', compact('products', 'query', 'categoryId', 'categories'));
    }

    /**
     * Get breadcrumbs for a category.
     *
     * @param Category $category
     * @return array
     */
    protected function getCategoryBreadcrumbs(Category $category): array
    {
        $breadcrumbs = [];
        $current = $category;

        while ($current) {
            array_unshift($breadcrumbs, [
                'name' => $current->name,
                'slug' => $current->slug,
            ]);
            $current = $current->parent;
        }

        // Add home
        array_unshift($breadcrumbs, [
            'name' => 'Accueil',
            'slug' => null,
        ]);

        return $breadcrumbs;
    }

    /**
     * Get breadcrumbs for a product.
     *
     * @param Product $product
     * @return array
     */
    protected function getProductBreadcrumbs(Product $product): array
    {
        $breadcrumbs = $this->getCategoryBreadcrumbs($product->category);

        $breadcrumbs[] = [
            'name' => $product->name,
            'slug' => null, // Current page
        ];

        return $breadcrumbs;
    }
}

