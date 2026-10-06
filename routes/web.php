<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Dashboard;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\PublicTemplateController;
use App\Http\Controllers\Shop;
use App\Http\Controllers\Site\WebsiteController as SiteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tenant websites
|--------------------------------------------------------------------------
|
| Any host that is NOT a central domain (company.platform.test, a verified
| custom domain such as www.company.com, ...) is served by these routes.
| They are registered first so they win over the central routes below.
|
*/

$centralHosts = collect(config('platform.central_domains'))
    ->map(fn ($host) => preg_quote(strtolower($host), '#'))
    ->implode('|');

Route::domain('{tenant_host}')
    ->where(['tenant_host' => '(?!(?:'.$centralHosts.')$)[a-z0-9][a-z0-9.\-]*'])
    ->middleware(['tenant.domain', 'tenant'])
    ->name('site.')
    ->group(function () {
        Route::get('/', [SiteController::class, 'home'])->name('home');
        Route::get('/sitemap.xml', [SiteController::class, 'sitemap'])->name('sitemap');
        Route::get('/robots.txt', [SiteController::class, 'robots'])->name('robots');
        Route::post('/contact', [SiteController::class, 'contact'])->middleware('throttle:contact')->name('contact');

        // ------------------------------------------------------------ Online shop
        Route::middleware(['shop', 'throttle:shop'])->prefix('shop')->name('shop.')->group(function () {
            Route::get('/', [Shop\StorefrontController::class, 'home'])->name('home');
            Route::get('/products', [Shop\StorefrontController::class, 'catalog'])->name('catalog');
            Route::get('/category/{slug}', [Shop\StorefrontController::class, 'category'])->where('slug', '[a-z0-9\-]+')->name('category');
            Route::get('/search', [Shop\StorefrontController::class, 'search'])->name('search');
            Route::get('/product/{slug}', [Shop\StorefrontController::class, 'product'])->where('slug', '[a-z0-9\-]+')->name('product');
            Route::post('/product/{slug}/reviews', [Shop\ReviewController::class, 'store'])->middleware('throttle:checkout')->name('reviews.store');

            Route::get('/cart', [Shop\CartController::class, 'show'])->name('cart');
            Route::get('/cart/summary', [Shop\CartController::class, 'summary'])->name('cart.summary');
            Route::post('/cart', [Shop\CartController::class, 'add'])->name('cart.add');
            Route::patch('/cart/{item}', [Shop\CartController::class, 'update'])->whereNumber('item')->name('cart.update');
            Route::delete('/cart/{item}', [Shop\CartController::class, 'remove'])->whereNumber('item')->name('cart.remove');
            Route::post('/cart/coupon', [Shop\CartController::class, 'applyCoupon'])->middleware('throttle:checkout')->name('cart.coupon');
            Route::delete('/cart/coupon', [Shop\CartController::class, 'removeCoupon'])->name('cart.coupon.remove');

            Route::get('/wishlist', [Shop\WishlistController::class, 'index'])->name('wishlist');
            Route::post('/wishlist/{product}', [Shop\WishlistController::class, 'toggle'])->whereNumber('product')->name('wishlist.toggle');
            Route::post('/wishlist/{product}/cart', [Shop\WishlistController::class, 'moveToCart'])->whereNumber('product')->name('wishlist.cart');

            Route::get('/checkout', [Shop\CheckoutController::class, 'show'])->name('checkout');
            Route::post('/checkout/quote', [Shop\CheckoutController::class, 'quote'])->name('checkout.quote');
            Route::post('/checkout', [Shop\CheckoutController::class, 'store'])->middleware('throttle:checkout')->name('checkout.store');
            Route::post('/checkout/whatsapp', [Shop\CheckoutController::class, 'whatsapp'])->middleware('throttle:checkout')->name('checkout.whatsapp');

            Route::get('/order/track', [Shop\OrderController::class, 'track'])->name('order.track');
            Route::post('/order/track', [Shop\OrderController::class, 'lookup'])->middleware('throttle:checkout')->name('order.lookup');
            Route::get('/order/{number}', [Shop\OrderController::class, 'show'])->where('number', '[A-Za-z0-9\-]+')->name('order');
        });

        // ------------------------------------------------------------ Customer account
        Route::middleware(['shop', 'throttle:shop'])->prefix('account')->name('account.')->group(function () {
            Route::middleware('customer.guest')->group(function () {
                Route::get('/login', [Shop\AccountController::class, 'loginForm'])->name('login');
                Route::post('/login', [Shop\AccountController::class, 'login'])->middleware('throttle:customer-auth');
                Route::get('/register', [Shop\AccountController::class, 'registerForm'])->name('register');
                Route::post('/register', [Shop\AccountController::class, 'register'])->middleware('throttle:customer-auth');
            });

            Route::middleware('customer')->group(function () {
                Route::get('/', [Shop\AccountController::class, 'profile'])->name('profile');
                Route::put('/', [Shop\AccountController::class, 'updateProfile'])->name('profile.update');
                Route::post('/logout', [Shop\AccountController::class, 'logout'])->name('logout');
                Route::get('/orders', [Shop\AccountController::class, 'orders'])->name('orders');
                Route::get('/orders/{number}', [Shop\AccountController::class, 'order'])->where('number', '[A-Za-z0-9\-]+')->name('orders.show');
                Route::get('/wishlist', [Shop\AccountController::class, 'wishlist'])->name('wishlist');
                Route::get('/reviews', [Shop\AccountController::class, 'reviews'])->name('reviews');
                Route::get('/addresses', [Shop\AccountController::class, 'addresses'])->name('addresses');
                Route::post('/addresses', [Shop\AccountController::class, 'storeAddress'])->name('addresses.store');
                Route::put('/addresses/{address}', [Shop\AccountController::class, 'updateAddress'])->whereNumber('address')->name('addresses.update');
                Route::delete('/addresses/{address}', [Shop\AccountController::class, 'destroyAddress'])->whereNumber('address')->name('addresses.destroy');
            });
        });

        Route::get('/{slug}', [SiteController::class, 'page'])->where('slug', '[a-z0-9\-]+')->name('page');
    });

/*
|--------------------------------------------------------------------------
| Central application (landing page, auth, dashboard, admin)
|--------------------------------------------------------------------------
*/

Route::middleware('central')->group(function () {
    Route::get('/', [LandingController::class, 'index'])->name('home');
    Route::get('/robots.txt', [LandingController::class, 'robots'])->name('robots');
    Route::get('/sitemap.xml', [LandingController::class, 'sitemap'])->name('sitemap');

    Route::get('/templates', [PublicTemplateController::class, 'index'])->name('templates.index');
    Route::get('/templates/{template}', [PublicTemplateController::class, 'show'])->name('templates.show');
    Route::get('/templates/{template}/render', [PublicTemplateController::class, 'render'])->name('templates.render');

    // ------------------------------------------------------------------ Auth
    Route::middleware('guest')->group(function () {
        Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
        Route::post('/register', [RegisteredUserController::class, 'store'])->middleware('throttle:register');
        Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('/login', [AuthenticatedSessionController::class, 'store']);
        Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
        Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->middleware('throttle:6,1')->name('password.email');
        Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
        Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.store');
    });

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');

    // ------------------------------------------------------------------ User dashboard
    Route::middleware(['auth', 'active'])->prefix('dashboard')->group(function () {
        Route::get('/', [Dashboard\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/templates', [Dashboard\TemplateGalleryController::class, 'index'])->name('dashboard.templates');
        Route::get('/domains', [Dashboard\DomainController::class, 'overview'])->name('domains.index');
        Route::get('/shop', [Dashboard\Shop\ShopController::class, 'hub'])->name('shop.hub');

        Route::get('/media', [Dashboard\MediaController::class, 'index'])->name('media.index');
        Route::post('/media', [Dashboard\MediaController::class, 'store'])->middleware('throttle:uploads')->name('media.store');
        Route::delete('/media/{media}', [Dashboard\MediaController::class, 'destroy'])->name('media.destroy');

        Route::get('/notifications', [Dashboard\NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/read', [Dashboard\NotificationController::class, 'markAllRead'])->name('notifications.read');

        Route::get('/settings', [Dashboard\SettingsController::class, 'edit'])->name('settings.edit');
        Route::put('/settings/profile', [Dashboard\SettingsController::class, 'updateProfile'])->name('settings.profile');
        Route::put('/settings/password', [Dashboard\SettingsController::class, 'updatePassword'])->name('settings.password');

        // Websites (company profiles)
        Route::get('/websites', [Dashboard\WebsiteController::class, 'index'])->name('websites.index');
        Route::get('/websites/create', [Dashboard\WebsiteController::class, 'create'])->name('websites.create');
        Route::post('/websites', [Dashboard\WebsiteController::class, 'store'])->name('websites.store');

        Route::prefix('websites/{company}')->name('websites.')->scopeBindings()->group(function () {
            Route::get('/', [Dashboard\WebsiteController::class, 'show'])->name('show');
            Route::delete('/', [Dashboard\WebsiteController::class, 'destroy'])->name('destroy');
            Route::get('/preview', [Dashboard\PreviewController::class, 'show'])->name('preview');
            Route::get('/preview/frame', [Dashboard\PreviewController::class, 'frame'])->name('preview.frame');
            Route::post('/publish', [Dashboard\WebsiteController::class, 'publish'])->name('publish');
            Route::post('/unpublish', [Dashboard\WebsiteController::class, 'unpublish'])->name('unpublish');

            // Creation wizard
            Route::get('/wizard/{step?}', [Dashboard\WizardController::class, 'show'])->name('wizard');
            Route::post('/wizard/{step}', [Dashboard\WizardController::class, 'save'])->name('wizard.save');
            Route::post('/autosave', [Dashboard\WizardController::class, 'autosave'])->middleware('throttle:autosave')->name('autosave');

            // Profile editor tabs: info, about, contact, branding, seo
            Route::get('/edit/{tab}', [Dashboard\ProfileEditorController::class, 'edit'])->name('edit');
            Route::put('/edit/{tab}', [Dashboard\ProfileEditorController::class, 'update'])->name('update');

            Route::get('/template', [Dashboard\ProfileEditorController::class, 'template'])->name('template.edit');
            Route::put('/template', [Dashboard\ProfileEditorController::class, 'updateTemplate'])->name('template.update');

            // Repeatable content: services, products, projects, team, testimonials, gallery
            Route::get('/content/{type}', [Dashboard\ContentController::class, 'index'])->name('content.index');
            Route::post('/content/{type}', [Dashboard\ContentController::class, 'store'])->name('content.store');
            Route::post('/content/{type}/reorder', [Dashboard\ContentController::class, 'reorder'])->name('content.reorder');
            Route::put('/content/{type}/{id}', [Dashboard\ContentController::class, 'update'])->whereNumber('id')->name('content.update');
            Route::delete('/content/{type}/{id}', [Dashboard\ContentController::class, 'destroy'])->whereNumber('id')->name('content.destroy');

            Route::resource('pages', Dashboard\PageController::class)->except('show');

            Route::get('/menus', [Dashboard\MenuController::class, 'index'])->name('menus.index');
            Route::post('/menus', [Dashboard\MenuController::class, 'store'])->name('menus.store');
            Route::post('/menus/tree', [Dashboard\MenuController::class, 'tree'])->name('menus.tree');
            Route::put('/menus/{menu}', [Dashboard\MenuController::class, 'update'])->name('menus.update');
            Route::delete('/menus/{menu}', [Dashboard\MenuController::class, 'destroy'])->name('menus.destroy');

            Route::get('/sections', [Dashboard\SectionController::class, 'index'])->name('sections.index');
            Route::put('/sections', [Dashboard\SectionController::class, 'update'])->name('sections.update');

            Route::get('/domains', [Dashboard\DomainController::class, 'index'])->name('domains.index');
            Route::put('/domains/subdomain', [Dashboard\DomainController::class, 'updateSubdomain'])->name('domains.subdomain');
            Route::post('/domains', [Dashboard\DomainController::class, 'store'])->name('domains.store');
            Route::post('/domains/{domain}/verify', [Dashboard\DomainController::class, 'verify'])->name('domains.verify');
            Route::post('/domains/{domain}/primary', [Dashboard\DomainController::class, 'primary'])->name('domains.primary');
            Route::delete('/domains/{domain}', [Dashboard\DomainController::class, 'destroy'])->name('domains.destroy');

            // ---------------------------------------------------------- Online shop (seller)
            Route::prefix('shop')->name('shop.')->group(function () {
                Route::get('/', [Dashboard\Shop\ShopController::class, 'overview'])->name('overview');
                Route::post('/toggle', [Dashboard\Shop\ShopController::class, 'toggle'])->name('toggle');
                Route::get('/settings', [Dashboard\Shop\ShopController::class, 'settings'])->name('settings');
                Route::put('/settings', [Dashboard\Shop\ShopController::class, 'updateSettings'])->name('settings.update');
                Route::put('/sections', [Dashboard\Shop\ShopController::class, 'updateSections'])->name('sections.update');

                Route::resource('products', Dashboard\Shop\ProductController::class)->except('show')->parameters(['products' => 'shopProduct']);
                Route::post('products/{shopProduct}/duplicate', [Dashboard\Shop\ProductController::class, 'duplicate'])->name('products.duplicate');
                Route::post('products/{shopProduct}/images/reorder', [Dashboard\Shop\ProductController::class, 'reorderImages'])->name('products.images.reorder');
                Route::delete('products/{shopProduct}/images/{image}', [Dashboard\Shop\ProductController::class, 'destroyImage'])->name('products.images.destroy');

                Route::get('categories', [Dashboard\Shop\CategoryController::class, 'index'])->name('categories.index');
                Route::post('categories', [Dashboard\Shop\CategoryController::class, 'store'])->name('categories.store');
                Route::put('categories/{productCategory}', [Dashboard\Shop\CategoryController::class, 'update'])->name('categories.update');
                Route::delete('categories/{productCategory}', [Dashboard\Shop\CategoryController::class, 'destroy'])->name('categories.destroy');
                Route::post('tags', [Dashboard\Shop\CategoryController::class, 'storeTag'])->name('tags.store');
                Route::delete('tags/{productTag}', [Dashboard\Shop\CategoryController::class, 'destroyTag'])->name('tags.destroy');

                Route::get('orders', [Dashboard\Shop\OrderController::class, 'index'])->name('orders.index');
                Route::get('orders/{order}', [Dashboard\Shop\OrderController::class, 'show'])->name('orders.show');
                Route::get('orders/{order}/invoice', [Dashboard\Shop\OrderController::class, 'invoice'])->name('orders.invoice');
                Route::post('orders/{order}/status', [Dashboard\Shop\OrderController::class, 'updateStatus'])->name('orders.status');
                Route::post('orders/{order}/paid', [Dashboard\Shop\OrderController::class, 'markPaid'])->name('orders.paid');
                Route::post('orders/{order}/tracking', [Dashboard\Shop\OrderController::class, 'tracking'])->name('orders.tracking');

                Route::get('customers', [Dashboard\Shop\CustomerController::class, 'index'])->name('customers.index');
                Route::get('customers/{customer}', [Dashboard\Shop\CustomerController::class, 'show'])->name('customers.show');

                Route::resource('coupons', Dashboard\Shop\CouponController::class)->except(['show', 'create', 'edit']);

                Route::get('inventory', [Dashboard\Shop\InventoryController::class, 'index'])->name('inventory.index');
                Route::post('inventory/{shopProduct}/adjust', [Dashboard\Shop\InventoryController::class, 'adjust'])->name('inventory.adjust');

                Route::get('reviews', [Dashboard\Shop\ReviewController::class, 'index'])->name('reviews.index');
                Route::post('reviews/{productReview}/status', [Dashboard\Shop\ReviewController::class, 'status'])->name('reviews.status');
                Route::delete('reviews/{productReview}', [Dashboard\Shop\ReviewController::class, 'destroy'])->name('reviews.destroy');

                Route::get('shipping', [Dashboard\Shop\ShippingController::class, 'index'])->name('shipping.index');
                Route::post('shipping', [Dashboard\Shop\ShippingController::class, 'store'])->name('shipping.store');
                Route::put('shipping/{shippingMethod}', [Dashboard\Shop\ShippingController::class, 'update'])->name('shipping.update');
                Route::delete('shipping/{shippingMethod}', [Dashboard\Shop\ShippingController::class, 'destroy'])->name('shipping.destroy');

                Route::get('payments', [Dashboard\Shop\PaymentController::class, 'index'])->name('payments.index');
                Route::post('payments', [Dashboard\Shop\PaymentController::class, 'store'])->name('payments.store');
                Route::put('payments/{paymentMethod}', [Dashboard\Shop\PaymentController::class, 'update'])->name('payments.update');
                Route::delete('payments/{paymentMethod}', [Dashboard\Shop\PaymentController::class, 'destroy'])->name('payments.destroy');

                Route::get('discounts', [Dashboard\Shop\DiscountController::class, 'index'])->name('discounts.index');
                Route::put('discounts/{shopProduct}', [Dashboard\Shop\DiscountController::class, 'update'])->name('discounts.update');
                Route::post('taxes', [Dashboard\Shop\DiscountController::class, 'storeTax'])->name('taxes.store');
                Route::put('taxes/{tax}', [Dashboard\Shop\DiscountController::class, 'updateTax'])->name('taxes.update');
                Route::delete('taxes/{tax}', [Dashboard\Shop\DiscountController::class, 'destroyTax'])->name('taxes.destroy');
            });

            Route::get('/messages', [Dashboard\MessageController::class, 'index'])->name('messages.index');
            Route::patch('/messages/{message}', [Dashboard\MessageController::class, 'update'])->name('messages.update');
            Route::delete('/messages/{message}', [Dashboard\MessageController::class, 'destroy'])->name('messages.destroy');
        });
    });

    // ------------------------------------------------------------------ Admin panel
    Route::middleware(['auth', 'active', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

        Route::resource('users', Admin\UserController::class);
        Route::post('users/{user}/suspend', [Admin\UserController::class, 'suspend'])->name('users.suspend');
        Route::post('users/{user}/unsuspend', [Admin\UserController::class, 'unsuspend'])->name('users.unsuspend');
        Route::post('users/{user}/reset-password', [Admin\UserController::class, 'resetPassword'])->name('users.reset-password');

        Route::get('companies', [Admin\CompanyProfileController::class, 'index'])->name('companies.index');
        Route::get('companies/{company}', [Admin\CompanyProfileController::class, 'show'])->name('companies.show');
        Route::post('companies/{company}/status', [Admin\CompanyProfileController::class, 'toggleStatus'])->name('companies.status');
        Route::delete('companies/{company}', [Admin\CompanyProfileController::class, 'destroy'])->name('companies.destroy');

        Route::resource('templates', Admin\TemplateController::class)->except('show');
        Route::post('templates/{template}/duplicate', [Admin\TemplateController::class, 'duplicate'])->name('templates.duplicate');
        Route::post('templates/{template}/publish', [Admin\TemplateController::class, 'togglePublish'])->name('templates.publish');
        Route::post('templates/{template}/feature', [Admin\TemplateController::class, 'toggleFeatured'])->name('templates.feature');

        Route::resource('categories', Admin\TemplateCategoryController::class)->only(['index', 'store', 'update', 'destroy'])
            ->parameters(['categories' => 'category']);

        Route::get('pages', [Admin\PageController::class, 'index'])->name('pages.index');
        Route::post('pages/{page}/status', [Admin\PageController::class, 'toggleStatus'])->name('pages.status');
        Route::delete('pages/{page}', [Admin\PageController::class, 'destroy'])->name('pages.destroy');

        Route::get('domains', [Admin\DomainController::class, 'index'])->name('domains.index');
        Route::post('domains/{domain}/activate', [Admin\DomainController::class, 'activate'])->name('domains.activate');
        Route::post('domains/{domain}/verify', [Admin\DomainController::class, 'verify'])->name('domains.verify');
        Route::delete('domains/{domain}', [Admin\DomainController::class, 'destroy'])->name('domains.destroy');

        Route::get('subscriptions', [Admin\SubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::put('subscriptions/{user}', [Admin\SubscriptionController::class, 'update'])->name('subscriptions.update');

        Route::get('media', [Admin\MediaController::class, 'index'])->name('media.index');
        Route::delete('media/{media}', [Admin\MediaController::class, 'destroy'])->name('media.destroy');

        Route::get('settings', [Admin\SettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [Admin\SettingController::class, 'update'])->name('settings.update');

        Route::get('logs', [Admin\ActivityLogController::class, 'index'])->name('logs.index');

        Route::get('shop/shops', [Admin\ShopController::class, 'shops'])->name('shop.shops');
        Route::get('shop/products', [Admin\ShopController::class, 'products'])->name('shop.products');
        Route::get('shop/orders', [Admin\ShopController::class, 'orders'])->name('shop.orders');
        Route::get('shop/orders/{order:id}', [Admin\ShopController::class, 'order'])->name('shop.orders.show');
        Route::get('shop/customers', [Admin\ShopController::class, 'customers'])->name('shop.customers');
    });
});
