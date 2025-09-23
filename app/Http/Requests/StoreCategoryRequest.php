<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
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
        return [
            'parent_id' => ['nullable', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:categories,slug'],
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
            // Prevent circular references
            if ($this->parent_id) {
                $parentCategory = \App\Models\Category::find($this->parent_id);
                if ($parentCategory && $this->wouldCreateCircularReference($parentCategory)) {
                    $validator->errors()->add('parent_id', 'Selecting this parent would create a circular reference.');
                }
            }
        });
    }

    /**
     * Check if selecting the parent would create a circular reference.
     *
     * @param  \App\Models\Category  $parentCategory
     * @return bool
     */
    private function wouldCreateCircularReference($parentCategory)
    {
        // For new categories, no circular reference is possible
        return false;
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // Set default active status
        if (!$this->has('is_active')) {
            $this->merge([
                'is_active' => true,
            ]);
        }
    }
}

