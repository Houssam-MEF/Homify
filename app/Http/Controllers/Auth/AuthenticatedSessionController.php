<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use App\Services\Cart\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Get session ID BEFORE authentication and regeneration
        $oldSessionId = session()->getId();
        $guestCart = \App\Models\Cart::where('session_id', $oldSessionId)->first();
        $hadGuestCart = $guestCart && $guestCart->items->isNotEmpty();

        $request->authenticate();


        $request->session()->regenerate();
        
        // Merge guest cart into user cart after login
        $cartService = app(CartService::class);
        $cartService->mergeGuestCartIntoUserCart(auth()->user(), $oldSessionId);
        
        // Show success message if cart was merged
        if ($hadGuestCart) {
            session()->flash('success', 'Vos articles du panier ont été sauvegardés dans votre compte !');
            // Redirect to cart if items were merged
            return redirect(route('cart.show'));
        }

        // Redirect based on user role
        if (auth()->user()->isAdmin()) {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
