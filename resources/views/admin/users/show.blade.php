@extends('layouts.admin')

@section('title', 'User Details')
@section('page-title', 'User Details')

@section('content')
<div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">User Details</h1>
                <p class="text-gray-600 mt-2">View and manage user information</p>
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('admin.users.edit', $user) }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                    ✏️ Edit User
                </a>
                <a href="{{ route('admin.users.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                    ← Back to Users
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- User Profile -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center space-x-4 mb-6">
                        <div class="flex-shrink-0 h-16 w-16">
                            <div class="h-16 w-16 rounded-full bg-{{ $user->is_admin ? 'blue' : 'gray' }}-500 flex items-center justify-center">
                                <span class="text-xl font-medium text-white">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h2>
                            <p class="text-gray-600">{{ $user->email }}</p>
                            @if($user->is_admin)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 mt-2">
                                    🔐 Administrator
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800 mt-2">
                                    👤 Regular User
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Account Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Account Information</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Member Since</dt>
                                    <dd class="text-sm text-gray-900">{{ $user->created_at->format('F d, Y') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                                    <dd class="text-sm text-gray-900">{{ $user->updated_at->format('F d, Y \a\t g:i A') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Email Verified</dt>
                                    <dd class="text-sm text-gray-900">
                                        @if($user->email_verified_at)
                                            <span class="text-green-600">✓ Verified on {{ $user->email_verified_at->format('M d, Y') }}</span>
                                        @else
                                            <span class="text-red-600">✗ Not verified</span>
                                        @endif
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Activity Summary</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Total Orders</dt>
                                    <dd class="text-sm text-gray-900">{{ $user->orders()->count() }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Addresses</dt>
                                    <dd class="text-sm text-gray-900">{{ $user->addresses()->count() }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Active Carts</dt>
                                    <dd class="text-sm text-gray-900">{{ $user->carts()->count() }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="bg-white rounded-lg shadow-md p-6 mt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Orders</h3>
                    @php
                        $recentOrders = $user->orders()->with('orderItems.product')->latest()->take(5)->get();
                    @endphp
                    
                    @if($recentOrders->count() > 0)
                        <div class="space-y-4">
                            @foreach($recentOrders as $order)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-medium text-gray-900">Order #{{ $order->id }}</p>
                                            <p class="text-sm text-gray-500">{{ $order->created_at->format('M d, Y \a\t g:i A') }}</p>
                                            <p class="text-sm text-gray-600">{{ $order->orderItems->count() }} item(s)</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-medium text-gray-900">{{ number_format($order->total_amount / 100, 2) }} {{ $order->currency }}</p>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($order->status === 'confirmed') bg-green-100 text-green-800
                                                @elseif($order->status === 'shipped') bg-blue-100 text-blue-800
                                                @elseif($order->status === 'delivered') bg-gray-100 text-gray-800
                                                @endif">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">No orders found for this user.</p>
                    @endif
                </div>
            </div>

            <!-- Actions Sidebar -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.toggle-admin', $user) }}">
                                @csrf
                                <button type="submit" 
                                        class="w-full text-left px-4 py-2 text-sm text-{{ $user->is_admin ? 'yellow' : 'green' }}-700 hover:bg-{{ $user->is_admin ? 'yellow' : 'green' }}-50 rounded-lg transition-colors">
                                    {{ $user->is_admin ? '🔓 Remove Admin Privileges' : '🔐 Grant Admin Privileges' }}
                                </button>
                            </form>
                        @endif

                        <button onclick="togglePasswordForm()" 
                                class="w-full text-left px-4 py-2 text-sm text-blue-700 hover:bg-blue-50 rounded-lg transition-colors">
                            🔑 Change Password
                        </button>

                        @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" 
                                  onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50 rounded-lg transition-colors">
                                    🗑️ Delete User
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- Password Change Form (Hidden by default) -->
                <div id="passwordForm" class="bg-white rounded-lg shadow-md p-6 hidden">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Change Password</h3>
                    <form method="POST" action="{{ route('admin.users.update-password', $user) }}">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                    New Password
                                </label>
                                <input type="password" 
                                       id="password" 
                                       name="password" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror"
                                       required>
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                    Confirm Password
                                </label>
                                <input type="password" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       required>
                            </div>
                            <div class="flex space-x-3">
                                <button type="submit" 
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                                    Update Password
                                </button>
                                <button type="button" 
                                        onclick="togglePasswordForm()"
                                        class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script>
function togglePasswordForm() {
    const form = document.getElementById('passwordForm');
    form.classList.toggle('hidden');
}
</script>
@endsection
