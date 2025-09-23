<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Services\Cart\CartService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Merge guest cart into user cart after registration
        $cartService = app(CartService::class);
        
        // Check if there's a guest cart before merging
        $sessionId = session()->getId();
        $guestCart = \App\Models\Cart::where('session_id', $sessionId)->first();
        $hadGuestCart = $guestCart && $guestCart->items->isNotEmpty();
        
        $cartService->mergeGuestCartIntoUserCart($user, $sessionId);
        
        // Show success message if cart was merged
        if ($hadGuestCart) {
            session()->flash('success', 'Vos articles du panier ont été sauvegardés dans votre compte !');
            // Redirect to cart if items were merged
            return redirect(route('cart.show'));
        }

        return redirect(RouteServiceProvider::HOME);
    }
}
