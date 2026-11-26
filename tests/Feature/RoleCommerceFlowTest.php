<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Category;
use App\Models\DeliveryZone;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RoleCommerceFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_redirects_each_role_to_its_own_dashboard(): void
    {
        foreach ([
            'admin' => 'admin.dashboard',
            'seller' => 'seller.dashboard',
            'customer' => 'customer.home',
        ] as $role => $route) {
            auth()->logout();
            $user = $this->makeUser($role);

            $this->post(route('login.process'), [
                'email' => $user->email,
                'password' => 'test-password',
            ])->assertRedirect(route($route));
        }
    }

    public function test_each_role_is_forbidden_from_the_other_role_dashboards(): void
    {
        $dashboardRoutes = [
            'admin' => route('admin.categories.index'),
            'seller' => route('seller.dashboard'),
            'customer' => route('customer.home'),
        ];

        foreach ($dashboardRoutes as $role => $ownRoute) {
            $this->actingAs($this->makeUser($role));

            foreach ($dashboardRoutes as $otherRole => $route) {
                if ($otherRole !== $role) {
                    $this->get($route)->assertForbidden();
                }
            }

            $this->get($ownRoute)->assertSuccessful();
        }
    }

    public function test_customer_registration_cannot_assign_another_role(): void
    {
        $this->post(route('register.process'), [
            'name' => 'New Customer',
            'email' => 'new-customer@example.test',
            'password' => 'secret12',
            'password_confirmation' => 'secret12',
            'role' => 'admin',
        ])->assertRedirect(route('login'));

        $this->assertDatabaseHas('users', [
            'email' => 'new-customer@example.test',
            'role' => 'customer',
        ]);
    }

    public function test_authentication_views_render_the_responsive_forms(): void
    {
        $this->get(route('login'))
            ->assertSuccessful()
            ->assertSee('Masuk ke akun')
            ->assertSee('data-password-toggle="password"', false);

        $this->get(route('register'))
            ->assertSuccessful()
            ->assertSee('Buat akun Customer')
            ->assertSee('password_confirmation');
    }

    public function test_logout_ends_the_session_and_protected_pages_redirect_guests(): void
    {
        foreach (['admin', 'seller', 'customer'] as $role) {
            $this->actingAs($this->makeUser($role))
                ->post(route('logout'))
                ->assertRedirect(route('login'));

            $this->get(route('customer.home'))->assertRedirect(route('login'));
        }
    }

    public function test_admin_can_create_update_and_delete_categories(): void
    {
        $this->actingAs($this->makeUser('admin'));

        $this->post(route('admin.categories.store'), ['name' => 'Anggur Impor'])
            ->assertRedirect(route('admin.categories.index'));

        $category = Category::where('name', 'Anggur Impor')->firstOrFail();

        $this->put(route('admin.categories.update', $category), ['name' => 'Anggur Premium'])
            ->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'Anggur Premium']);

        $this->delete(route('admin.categories.destroy', $category))
            ->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_admin_can_create_and_update_products_without_changing_their_public_slug(): void
    {
        $admin = $this->makeUser('admin');
        $seller = $this->makeUser('seller');
        $category = Category::create(['name' => 'Anggur Impor', 'description' => null]);

        $this->actingAs($admin)
            ->post(route('admin.products.store'), [
                'category_id' => $category->id,
                'seller_id' => $seller->id,
                'name' => 'Bibit Jupiter',
                'price' => 85000,
                'stock' => 10,
                'availability' => 'available',
                'status' => 'active',
            ])->assertRedirect(route('admin.products.index'));

        $product = Product::where('name', 'Bibit Jupiter')->firstOrFail();
        $slug = $product->slug;

        $this->put(route('admin.products.update', $product), [
            'category_id' => $category->id,
            'seller_id' => $seller->id,
            'name' => 'Bibit Jupiter Pilihan',
            'price' => 90000,
            'stock' => 8,
            'availability' => 'available',
            'status' => 'active',
        ])->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Bibit Jupiter Pilihan',
            'slug' => $slug,
            'price' => '90000.00',
        ]);

        $this->delete(route('admin.products.destroy', $product))
            ->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_customer_cannot_view_another_customers_order(): void
    {
        $customer = $this->makeUser('customer');
        $anotherCustomer = $this->makeUser('customer');
        $order = Order::create([
            'user_id' => $anotherCustomer->id,
            'order_code' => 'TK-PRIVATE-ORDER',
            'total_price' => 10000,
            'status' => 'pending',
        ]);

        $this->actingAs($customer)
            ->get(route('customer.orders.show', $order))
            ->assertForbidden();
    }

    public function test_customer_cancellation_returns_stock_and_marks_pending_payment_failed(): void
    {
        $customer = $this->makeUser('customer');
        $seller = $this->makeUser('seller');
        $category = Category::create(['name' => 'Anggur', 'description' => null]);
        $product = $this->makeProduct($seller, $category, ['stock' => 0]);
        $order = Order::create([
            'user_id' => $customer->id,
            'order_code' => 'TK-CUSTOMER-CANCEL',
            'total_price' => 50000,
            'status' => 'pending',
        ]);
        OrderItem::create(['order_id' => $order->id, 'product_id' => $product->id, 'quantity' => 2, 'price' => 25000, 'subtotal' => 50000]);
        Shipment::create([
            'order_id' => $order->id,
            'recipient_name' => 'Customer',
            'phone' => '08000000000',
            'address' => 'Pekanbaru',
            'shipping_cost' => 0,
            'status' => 'pending',
        ]);
        Payment::create(['order_id' => $order->id, 'payment_method' => 'transfer_manual', 'amount' => 50000, 'status' => 'pending']);

        $this->actingAs($customer)
            ->patch(route('customer.orders.cancel', $order))
            ->assertRedirect();

        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 2]);
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'cancelled']);
        $this->assertDatabaseHas('payments', ['order_id' => $order->id, 'status' => 'failed']);
        $this->assertDatabaseHas('shipments', ['order_id' => $order->id, 'status' => 'cancelled']);
    }

    public function test_admin_shipping_update_records_tracking_visible_to_the_customer(): void
    {
        $admin = $this->makeUser('admin');
        $customer = $this->makeUser('customer');
        $order = Order::create([
            'user_id' => $customer->id,
            'order_code' => 'TK-SHIP-TEST',
            'total_price' => 10000,
            'status' => 'processing',
        ]);
        Shipment::create([
            'order_id' => $order->id,
            'recipient_name' => 'Customer',
            'phone' => '08000000000',
            'address' => 'Pekanbaru',
            'shipping_cost' => 0,
            'status' => 'processing',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.orders.status', $order), [
                'status' => 'shipped',
                'courier' => 'Kurir Uji',
                'tracking_number' => 'RESI-123',
            ])->assertRedirect();

        $this->assertDatabaseHas('shipment_tracking', [
            'shipment_id' => $order->shipment->id,
            'status' => 'shipped',
        ]);

        $this->actingAs($customer)
            ->get(route('customer.orders.show', $order))
            ->assertSuccessful()
            ->assertSee('Riwayat pengiriman')
            ->assertSee('RESI-123');
    }

    public function test_seller_cannot_edit_another_sellers_product_and_new_products_are_owned(): void
    {
        $seller = $this->makeUser('seller');
        $otherSeller = $this->makeUser('seller');
        $category = Category::create(['name' => 'Anggur', 'description' => null]);
        $foreignProduct = $this->makeProduct($otherSeller, $category);

        $this->actingAs($seller)
            ->get(route('seller.products.edit', $foreignProduct))
            ->assertNotFound();

        $this->post(route('seller.products.store'), [
            'category_id' => $category->id,
            'name' => 'Bibit Seller',
            'description' => 'Bibit uji',
            'price' => 50000,
            'stock' => 3,
            'availability' => 'available',
            'status' => 'active',
        ])->assertRedirect(route('seller.products.index'));

        $this->assertDatabaseHas('products', [
            'name' => 'Bibit Seller',
            'seller_id' => $seller->id,
        ]);

        $product = Product::where('name', 'Bibit Seller')->firstOrFail();

        $this->put(route('seller.products.update', $product), [
            'category_id' => $category->id,
            'name' => 'Bibit Seller Diperbarui',
            'price' => 55000,
            'stock' => 4,
            'availability' => 'available',
            'status' => 'active',
        ])->assertRedirect(route('seller.products.index'));
        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Bibit Seller Diperbarui']);

        $this->from(route('seller.products.index'))
            ->delete(route('seller.products.destroy', $product))
            ->assertRedirect(route('seller.products.index'));
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_customer_catalog_hides_inactive_and_unavailable_products(): void
    {
        $customer = $this->makeUser('customer');
        $seller = $this->makeUser('seller');
        $category = Category::create(['name' => 'Anggur', 'description' => null]);
        $this->makeProduct($seller, $category, ['name' => 'Produk Siap', 'slug' => 'produk-siap']);
        $this->makeProduct($seller, $category, ['name' => 'Produk Habis', 'slug' => 'produk-habis', 'stock' => 0]);
        $this->makeProduct($seller, $category, ['name' => 'Produk Nonaktif', 'slug' => 'produk-nonaktif', 'status' => 'inactive']);

        $this->actingAs($customer)
            ->get(route('customer.products.index'))
            ->assertSuccessful()
            ->assertSee('Produk Siap')
            ->assertDontSee('Produk Habis')
            ->assertDontSee('Produk Nonaktif');
    }

    public function test_cart_and_wishlist_reject_products_with_zero_stock(): void
    {
        $customer = $this->makeUser('customer');
        $seller = $this->makeUser('seller');
        $category = Category::create(['name' => 'Anggur', 'description' => null]);
        $product = $this->makeProduct($seller, $category, ['stock' => 0]);

        $this->actingAs($customer)
            ->post(route('customer.cart.store', $product), ['quantity' => 1])
            ->assertStatus(422);

        $this->post(route('customer.wishlist.store', $product))->assertStatus(422);

        $this->assertDatabaseCount('cart_items', 0);
        $this->assertDatabaseCount('wishlist_items', 0);
    }

    public function test_customer_can_manage_cart_and_wishlist_for_a_valid_product(): void
    {
        $customer = $this->makeUser('customer');
        $seller = $this->makeUser('seller');
        $category = Category::create(['name' => 'Anggur', 'description' => null]);
        $product = $this->makeProduct($seller, $category, ['stock' => 4]);

        $this->actingAs($customer)
            ->post(route('customer.cart.store', $product), ['quantity' => 2])
            ->assertRedirect(route('customer.cart.index'));

        $cart = Cart::where('user_id', $customer->id)->firstOrFail();
        $item = $cart->items()->firstOrFail();

        $this->from(route('customer.cart.index'))
            ->patch(route('customer.cart.update', $item), ['quantity' => 3])
            ->assertRedirect(route('customer.cart.index'));
        $this->assertDatabaseHas('cart_items', ['id' => $item->id, 'quantity' => 3]);

        $this->from(route('customer.cart.index'))
            ->patch(route('customer.cart.update', $item), ['quantity' => 5])
            ->assertSessionHasErrors('quantity');
        $this->assertDatabaseHas('cart_items', ['id' => $item->id, 'quantity' => 3]);

        $this->post(route('customer.wishlist.store', $product))->assertRedirect();
        $this->assertDatabaseHas('wishlist_items', ['user_id' => $customer->id, 'product_id' => $product->id]);

        $this->delete(route('customer.cart.destroy', $item))->assertRedirect();
        $this->assertDatabaseMissing('cart_items', ['id' => $item->id]);
    }

    public function test_checkout_rejects_unknown_payment_method_without_creating_an_order(): void
    {
        $customer = $this->makeUser('customer');
        [$cart, $product] = $this->makeCartWithProduct($customer);
        $zone = DeliveryZone::create(['province' => 'Riau', 'city' => 'Pekanbaru', 'shipping_cost' => 25000, 'is_active' => true]);

        $this->actingAs($customer)
            ->from(route('customer.checkout.index'))
            ->post(route('customer.checkout.store'), $this->checkoutPayload($zone, 'unknown'))
            ->assertRedirect(route('customer.checkout.index'))
            ->assertSessionHasErrors('payment_method');

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 5]);
        $this->assertDatabaseHas('cart_items', ['cart_id' => $cart->id, 'product_id' => $product->id]);
    }

    public function test_checkout_uses_current_product_price_and_configured_shipping_cost(): void
    {
        $customer = $this->makeUser('customer');
        [$cart, $product] = $this->makeCartWithProduct($customer);
        $product->update(['price' => 120.50]);
        $zone = DeliveryZone::create(['province' => 'Riau', 'city' => 'Pekanbaru', 'shipping_cost' => 25, 'is_active' => true]);

        $this->actingAs($customer)
            ->post(route('customer.checkout.store'), $this->checkoutPayload($zone))
            ->assertRedirect(route('customer.orders.show', Order::first()));

        $order = Order::firstOrFail();

        $this->assertSame('266.00', $order->total_price);
        $this->assertDatabaseHas('order_items', ['order_id' => $order->id, 'price' => '120.50', 'quantity' => 2, 'subtotal' => '241.00']);
        $this->assertDatabaseHas('shipments', ['order_id' => $order->id, 'shipping_cost' => '25.00']);
        $this->assertDatabaseHas('payments', ['order_id' => $order->id, 'amount' => '266.00', 'payment_method' => 'transfer_manual']);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 3]);
        $this->assertDatabaseCount('cart_items', 0);
    }

    public function test_admin_can_confirm_payment_and_cancel_order_with_stock_return_and_refund(): void
    {
        $admin = $this->makeUser('admin');
        $customer = $this->makeUser('customer');
        $seller = $this->makeUser('seller');
        $category = Category::create(['name' => 'Anggur', 'description' => null]);
        $product = $this->makeProduct($seller, $category, ['stock' => 0]);
        $order = Order::create([
            'user_id' => $customer->id,
            'order_code' => 'TK-TEST-1',
            'total_price' => 50000,
            'status' => 'pending',
        ]);
        OrderItem::create(['order_id' => $order->id, 'product_id' => $product->id, 'quantity' => 2, 'price' => 25000, 'subtotal' => 50000]);
        Shipment::create([
            'order_id' => $order->id,
            'recipient_name' => 'Customer',
            'phone' => '08000000000',
            'address' => 'Pekanbaru',
            'shipping_cost' => 0,
            'status' => 'pending',
        ]);
        $payment = Payment::create([
            'order_id' => $order->id,
            'payment_method' => 'transfer_manual',
            'amount' => 50000,
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.payments.status', $payment), ['status' => 'paid'])
            ->assertRedirect();

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'confirmed']);

        $this->patch(route('admin.orders.status', $order), ['status' => 'cancelled'])
            ->assertRedirect();

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'cancelled']);
        $this->assertDatabaseHas('payments', ['id' => $payment->id, 'status' => 'refunded']);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 2]);
        $this->assertDatabaseHas('shipments', ['order_id' => $order->id, 'status' => 'cancelled']);
    }

    public function test_seller_cannot_change_status_of_a_multi_seller_order(): void
    {
        $seller = $this->makeUser('seller');
        $otherSeller = $this->makeUser('seller');
        $customer = $this->makeUser('customer');
        $category = Category::create(['name' => 'Anggur', 'description' => null]);
        $firstProduct = $this->makeProduct($seller, $category, ['slug' => 'seller-one']);
        $secondProduct = $this->makeProduct($otherSeller, $category, ['slug' => 'seller-two']);
        $order = Order::create([
            'user_id' => $customer->id,
            'order_code' => 'TK-TEST-2',
            'total_price' => 50000,
            'status' => 'pending',
        ]);
        OrderItem::create(['order_id' => $order->id, 'product_id' => $firstProduct->id, 'quantity' => 1, 'price' => 25000, 'subtotal' => 25000]);
        OrderItem::create(['order_id' => $order->id, 'product_id' => $secondProduct->id, 'quantity' => 1, 'price' => 25000, 'subtotal' => 25000]);

        $this->actingAs($seller)
            ->patch(route('seller.orders.status', $order), ['status' => 'confirmed'])
            ->assertSessionHas('error');

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'pending']);
    }

    private function makeUser(string $role): User
    {
        return User::factory()->create([
            'role' => $role,
            'password' => Hash::make('test-password'),
        ]);
    }

    private function makeProduct(User $seller, Category $category, array $attributes = []): Product
    {
        return Product::create(array_merge([
            'category_id' => $category->id,
            'seller_id' => $seller->id,
            'name' => 'Bibit Anggur',
            'slug' => 'test-product-'.(Product::count() + 1),
            'description' => 'Bibit untuk pengujian.',
            'price' => 25000,
            'stock' => 5,
            'availability' => 'available',
            'status' => 'active',
        ], $attributes));
    }

    private function makeCartWithProduct(User $customer): array
    {
        $seller = $this->makeUser('seller');
        $category = Category::create(['name' => 'Anggur', 'description' => null]);
        $product = $this->makeProduct($seller, $category);
        $cart = Cart::create(['user_id' => $customer->id]);
        $cart->items()->create(['product_id' => $product->id, 'quantity' => 2]);

        return [$cart, $product];
    }

    private function checkoutPayload(DeliveryZone $zone, string $paymentMethod = 'transfer_manual'): array
    {
        return [
            'recipient_name' => 'Customer',
            'phone' => '08000000000',
            'address' => 'Pekanbaru',
            'delivery_zone_id' => $zone->id,
            'payment_method' => $paymentMethod,
        ];
    }
}
