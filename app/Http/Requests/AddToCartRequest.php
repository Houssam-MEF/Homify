<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class AddToCartRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
            'qty' => ['required', 'integer', 'min:1'],
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
            $product = Product::find($this->product_id);
            
            if ($product && !$product->is_active) {
                $validator->errors()->add('product_id', 'This product is not available.');
            }

            if ($product && !$product->inStock($this->qty)) {
                $validator->errors()->add('qty', 'Not enough stock available. Available: ' . $product->stock);
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'product_id.required' => 'Product selection is required.',
            'product_id.exists' => 'The selected product does not exist.',
            'qty.required' => 'Quantity is required.',
            'qty.integer' => 'Quantity must be a number.',
            'qty.min' => 'Quantity must be at least 1.',
        ];
    }
}

