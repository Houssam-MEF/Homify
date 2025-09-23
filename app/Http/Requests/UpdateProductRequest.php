<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user() && auth()->user()->can('manage-catalog');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $productId = $this->route('product')->id ?? null;

        return [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('products', 'slug')->ignore($productId)],
            'description' => ['nullable', 'string'],
            'price_amount' => ['required', 'integer', 'min:0'],
            'price_currency' => ['required', 'string', 'size:3'],
            'stock' => ['required', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'remove_images' => ['nullable', 'array'],
            'remove_images.*' => ['integer', 'exists:product_images,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'category_id.required' => 'Category is required.',
            'category_id.exists' => 'The selected category does not exist.',
            'name.required' => 'Product name is required.',
            'name.max' => 'Product name cannot exceed 255 characters.',
            'slug.unique' => 'This slug is already taken.',
            'price_amount.required' => 'Price is required.',
            'price_amount.integer' => 'Price must be a number.',
            'price_amount.min' => 'Price cannot be negative.',
            'price_currency.required' => 'Currency is required.',
            'price_currency.size' => 'Currency must be a 3-letter code.',
            'stock.required' => 'Stock quantity is required.',
            'stock.integer' => 'Stock must be a number.',
            'stock.min' => 'Stock cannot be negative.',
            'images.*.image' => 'Each file must be an image.',
            'images.*.mimes' => 'Images must be of type: jpeg, png, jpg, gif.',
            'images.*.max' => 'Each image cannot exceed 2MB.',
            'remove_images.*.exists' => 'Invalid image selected for removal.',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // Convert price from dollars to cents if needed
        if ($this->has('price') && is_numeric($this->price)) {
            $this->merge([
                'price_amount' => (int) ($this->price * 100),
            ]);
        }

        // Set default currency if not provided
        if (!$this->has('price_currency')) {
            $this->merge([
                'price_currency' => 'USD',
            ]);
        }

        // Handle boolean conversion for is_active
        if ($this->has('is_active')) {
            $this->merge([
                'is_active' => (bool) $this->is_active,
            ]);
        }
    }
}

