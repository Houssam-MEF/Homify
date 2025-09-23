<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
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
        $categoryId = $this->route('category')->id ?? null;

        return [
            'parent_id' => ['nullable', 'exists:categories,id', 'not_in:' . $categoryId],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('categories', 'slug')->ignore($categoryId)],
            'is_active' => ['boolean'],
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
            'parent_id.exists' => 'The selected parent category does not exist.',
            'parent_id.not_in' => 'A category cannot be its own parent.',
            'name.required' => 'Category name is required.',
            'name.max' => 'Category name cannot exceed 255 characters.',
            'slug.unique' => 'This slug is already taken.',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $categoryId = $this->route('category')->id ?? null;
            
            // Prevent circular references
            if ($this->parent_id && $categoryId) {
                $parentCategory = \App\Models\Category::find($this->parent_id);
                if ($parentCategory && $this->wouldCreateCircularReference($parentCategory, $categoryId)) {
                    $validator->errors()->add('parent_id', 'Selecting this parent would create a circular reference.');
                }
            }
        });
    }

    /**
     * Check if selecting the parent would create a circular reference.
     *
     * @param  \App\Models\Category  $parentCategory
     * @param  int  $categoryId
     * @return bool
     */
    private function wouldCreateCircularReference($parentCategory, $categoryId)
    {
        $current = $parentCategory;
        
        while ($current) {
            if ($current->id === $categoryId) {
                return true;
            }
            $current = $current->parent;
        }
        
        return false;
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // Handle boolean conversion for is_active
        if ($this->has('is_active')) {
            $this->merge([
                'is_active' => (bool) $this->is_active,
            ]);
        }
    }
}

