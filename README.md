# E-Commerce MVP - Laravel 9

A complete e-commerce MVP built with Laravel 9, featuring a hierarchical product catalog, session-based cart management, Stripe payment integration, and admin panel.

## Features

### Client Features
- **Product Catalog**: Browse categories and subcategories with hierarchical navigation
- **Product Search**: Search products by name and description with category filtering
- **Shopping Cart**: Session-based cart that persists across visits and merges on login
- **User Authentication**: Registration, login, and profile management with Laravel Breeze
- **Checkout Process**: Secure checkout with address collection and order creation
- **Payment Processing**: Stripe integration with PaymentIntent and webhook handling
- **Order Management**: View order history and order details

### Admin Features
- **Category Management**: Create, update, and organize hierarchical categories
- **Product Management**: Full CRUD operations with image upload and stock management
- **Order Monitoring**: View and manage customer orders
- **Role-based Access**: Admin-only access to management features

### Technical Features
- **Clean Architecture**: Services, FormRequests, and proper separation of concerns
- **Money Handling**: Integer-based currency storage (minor units) with proper formatting
- **Image Management**: Laravel Filesystem with public disk storage
- **Testing**: Comprehensive feature tests for cart and checkout functionality
- **Database**: Optimized with proper indexes, foreign keys, and soft deletes
- **PSR-12 Compliant**: Clean, well-documented code following PHP standards

## Tech Stack

- **Framework**: Laravel 9.x (PHP ≥ 8.0)
- **Authentication**: Laravel Breeze (Blade)
- **Authorization**: Gates and Policies (role-based)
- **Payments**: Stripe PHP SDK with PaymentIntent
- **Frontend**: Blade templates with Tailwind CSS
- **Database**: MySQL/SQLite with Eloquent ORM
- **Storage**: Laravel Filesystem (public disk)
- **Testing**: PHPUnit with feature tests
- **Queue**: Sync driver (ready for Redis)

## Installation

### Prerequisites
- PHP 8.0 or higher
- Composer
- Node.js and NPM
- MySQL or SQLite
- Stripe account (for payment processing)

### Setup Steps

1. **Clone and Install Dependencies**
   ```bash
   git clone <repository-url>
   cd homify
   composer install
   npm install
   ```

2. **Environment Configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Database Setup**
   
   Configure your database in `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=homify
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

   Or for SQLite:
   ```env
   DB_CONNECTION=sqlite
   DB_DATABASE=/absolute/path/to/database.sqlite
   ```

4. **Install Laravel Breeze**
   ```bash
   php artisan breeze:install blade
   npm run dev
   ```

5. **Run Migrations and Seeders**
   ```bash
   php artisan migrate --seed
   ```

6. **Storage Setup**
   ```bash
   php artisan storage:link
   ```

7. **Stripe Configuration**
   
   Add your Stripe keys to `.env`:
   ```env
   STRIPE_KEY=pk_test_your_publishable_key
   STRIPE_SECRET=sk_test_your_secret_key
   STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret
   ```

8. **Start Development Server**
   ```bash
   php artisan serve
   ```

   Visit `http://localhost:8000` to access the application.

## Default Users

After running the seeder, you'll have:

- **Admin User**: 
  - Email: `admin@example.com`
  - Password: `password`
  - Access: Full admin panel access

- **Regular Users**: 5 client users with faker data

## Project Structure

### Models & Relationships
- **User**: Has orders, addresses, and carts
- **Category**: Self-referencing hierarchy with products
- **Product**: Belongs to category, has images, soft deletes
- **Cart & CartItem**: Session or user-based shopping cart
- **Order & OrderItem**: Complete order management
- **Address**: Billing and shipping addresses
- **Payment**: Stripe payment tracking

### Key Services
- **CartService**: Handles all cart operations, merging, and calculations
- **StripePaymentService**: Manages PaymentIntents and webhook processing

### Controllers
- **Client Controllers**: Catalog, Cart, Checkout, Payment, Order
- **Admin Controllers**: Category and Product management

### Routes Structure
```
/                           # Home page
/c/{slug}                   # Category pages
/p/{slug}                   # Product pages
/panier                     # Cart (French for shopping cart)
/checkout                   # Checkout process
/orders                     # Order history
/admin                      # Admin panel
/webhooks/stripe           # Stripe webhooks
```

## Database Schema

### Key Tables
- `users` - User accounts with role column
- `categories` - Hierarchical product categories
- `products` - Product catalog with pricing and inventory
- `product_images` - Product image management
- `carts` & `cart_items` - Shopping cart system
- `orders` & `order_items` - Order management
- `addresses` - Customer addresses
- `payments` - Payment tracking

### Money Storage
All monetary values are stored as integers in minor units (cents) with separate currency codes, following financial best practices.

## Testing

Run the test suite:
```bash
php artisan test
```

### Test Coverage
- Cart functionality (add, update, remove, merge)
- Checkout process (address validation, order creation)
- Admin operations (CRUD, permissions)
- Payment integration (mocked Stripe operations)

## Stripe Integration

### Setup Webhooks
1. Create webhook endpoint in Stripe Dashboard
2. Point to: `https://yourdomain.com/webhooks/stripe`
3. Listen for: `payment_intent.succeeded`, `payment_intent.payment_failed`
4. Copy webhook secret to `.env`

### Payment Flow
1. Customer completes checkout
2. PaymentIntent created with order metadata
3. Customer confirms payment with Stripe Elements
4. Webhook confirms payment success
5. Order status updated, stock decremented

## Deployment Considerations

### Production Setup
1. Set `APP_ENV=production` and `APP_DEBUG=false`
2. Configure proper database and Redis queue
3. Set up SSL certificate
4. Configure file storage (S3 recommended)
5. Set up proper logging and monitoring
6. Configure Stripe live keys

### Performance Optimization
- Enable Laravel caching (config, routes, views)
- Use Redis for sessions and cache
- Implement queue workers for email and notifications
- Optimize database queries with eager loading
- Use CDN for static assets

## Security Features

- CSRF protection on all forms
- SQL injection prevention via Eloquent
- XSS protection with Blade escaping
- Role-based authorization with Gates
- Input validation with FormRequests
- Secure password hashing
- Stripe webhook signature verification

## Contributing

1. Fork the repository
2. Create a feature branch
3. Follow PSR-12 coding standards
4. Write tests for new features
5. Submit a pull request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
