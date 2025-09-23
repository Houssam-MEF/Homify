<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutPaymentRequest extends FormRequest
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
            'payment_method' => ['required', 'string', 'in:card'],
            'card_number' => ['required', 'string', 'regex:/^[0-9\s]{13,19}$/'],
            'card_expiry' => ['required', 'string', 'regex:/^(0[1-9]|1[0-2])\/\d{2}$/'],
            'card_cvv' => ['required', 'string', 'regex:/^[0-9]{3,4}$/'],
            'cardholder_name' => ['required', 'string', 'max:255'],
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
            'payment_method.required' => 'La méthode de paiement est requise.',
            'payment_method.in' => 'Méthode de paiement non valide.',
            'card_number.required' => 'Le numéro de carte est requis.',
            'card_number.regex' => 'Le numéro de carte doit contenir entre 13 et 19 chiffres.',
            'card_expiry.required' => 'La date d\'expiration est requise.',
            'card_expiry.regex' => 'La date d\'expiration doit être au format MM/AA.',
            'card_cvv.required' => 'Le code CVV est requis.',
            'card_cvv.regex' => 'Le code CVV doit contenir 3 ou 4 chiffres.',
            'cardholder_name.required' => 'Le nom du titulaire est requis.',
            'cardholder_name.max' => 'Le nom du titulaire ne peut pas dépasser 255 caractères.',
        ];
    }
}
