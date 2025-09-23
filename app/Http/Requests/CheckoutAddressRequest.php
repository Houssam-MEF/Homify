<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutAddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            // Delivery Address
            'shipping_first_name' => ['required', 'string', 'max:255'],
            'shipping_last_name' => ['required', 'string', 'max:255'],
            'shipping_line1' => ['required', 'string', 'max:255'],
            'shipping_line2' => ['nullable', 'string', 'max:255'],
            'shipping_city' => ['required', 'string', 'max:255'],
            'shipping_region' => ['nullable', 'string', 'max:255'],
            'shipping_postal_code' => ['required', 'string', 'max:20'],
            'shipping_country' => ['required', 'string', 'max:2'],
            'shipping_phone' => ['required', 'string', 'max:20'],
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
            'shipping_first_name.required' => 'Le prénom est requis.',
            'shipping_last_name.required' => 'Le nom est requis.',
            'shipping_line1.required' => 'L\'adresse est requise.',
            'shipping_city.required' => 'La ville est requise.',
            'shipping_postal_code.required' => 'Le code postal est requis.',
            'shipping_country.required' => 'Le pays est requis.',
            'shipping_phone.required' => 'Le téléphone est requis.',
        ];
    }

    /**
     * Get the delivery address data.
     *
     * @return array
     */
    public function getDeliveryAddressData()
    {
        return [
            'type' => 'shipping',
            'first_name' => $this->shipping_first_name,
            'last_name' => $this->shipping_last_name,
            'name' => $this->shipping_first_name . ' ' . $this->shipping_last_name,
            'line1' => $this->shipping_line1,
            'line2' => $this->shipping_line2,
            'city' => $this->shipping_city,
            'region' => $this->shipping_region,
            'postal_code' => $this->shipping_postal_code,
            'country' => $this->shipping_country,
            'phone' => $this->shipping_phone,
        ];
    }
}

