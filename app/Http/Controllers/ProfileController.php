<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Order;
use App\Models\Address;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's dashboard with statistics and profile information.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        
        // Get user statistics
        $totalOrders = $user->orders()->count();
        $totalSpent = $user->orders()->where('status', 'completed')->sum('grand_total');
        $pendingOrders = $user->orders()->whereIn('status', ['pending', 'processing'])->count();
        $completedOrders = $user->orders()->where('status', 'completed')->count();
        
        // Get recent orders
        $recentOrders = $user->orders()
            ->with(['items.product'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Get monthly spending data for chart
        $monthlySpending = $user->orders()
            ->where('status', 'completed')
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('SUM(grand_total) as total')
            )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();
        
        // Get addresses
        $addresses = $user->addresses()->get();
        
        return view('profile.dashboard', compact(
            'user',
            'totalOrders',
            'totalSpent',
            'pendingOrders',
            'completedOrders',
            'recentOrders',
            'monthlySpending',
            'addresses'
        ));
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current-password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
