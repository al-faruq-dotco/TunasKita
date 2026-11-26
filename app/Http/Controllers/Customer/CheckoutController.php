<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\DeliveryZone;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Shipment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $cart = Cart::with('items.product')->firstOrCreate(['user_id' => auth()->id()]);
        $cart->load('items.product');

        if ($cart->items->isEmpty()) {
            return redirect()->route('customer.cart.index')->with('error', 'Keranjang masih kosong.');
        }

        $deliveryZones = DeliveryZone::where('is_active', true)->orderBy('province')->orderBy('city')->get();

        return view('customer.checkout.index', compact('cart', 'deliveryZones'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'recipient_name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:25'],
            'address' => ['required', 'string'],
            'delivery_zone_id' => ['required', Rule::exists('delivery_zones', 'id')->where('is_active', true)],
            'courier' => ['nullable', 'string', 'max:80'],
            'payment_method' => ['required', Rule::in(['transfer_manual', 'cod'])],
            'notes' => ['nullable', 'string'],
        ]);

        try {
            $order = DB::transaction(function () use ($data): Order {
                $cart = Cart::with('items.product')->where('user_id', auth()->id())->firstOrFail();
                abort_if($cart->items->isEmpty(), 422, 'Keranjang kosong.');

                $deliveryZone = DeliveryZone::whereKey($data['delivery_zone_id'])
                    ->where('is_active', true)
                    ->lockForUpdate()
                    ->firstOrFail();
                $products = [];
                $subtotal = 0;

                foreach ($cart->items as $item) {
                    $product = $item->product()->lockForUpdate()->first();

                    if (! $product || ! $product->isAvailableForPurchase() || $product->stock < $item->quantity) {
                        throw new \RuntimeException('Stok atau ketersediaan '.($item->product?->name ?? 'produk').' berubah. Periksa kembali keranjang.');
                    }

                    $products[$item->id] = $product;
                    $subtotal += (float) $product->price * $item->quantity;
                }

                $shippingCost = (float) $deliveryZone->shipping_cost;
                $total = $subtotal + $shippingCost;
                $order = Order::create([
                    'user_id' => auth()->id(),
                    'order_code' => 'TK-'.now()->format('Ymd').'-'.Str::upper(Str::random(6)),
                    'total_price' => $total,
                    'status' => 'pending',
                    'notes' => $data['notes'] ?? null,
                ]);

                foreach ($cart->items as $item) {
                    $product = $products[$item->id];
                    $itemSubtotal = (float) $product->price * $item->quantity;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $item->quantity,
                        'price' => $product->price,
                        'subtotal' => $itemSubtotal,
                    ]);
                    $product->decrement('stock', $item->quantity);
                }

                Shipment::create([
                    'order_id' => $order->id,
                    'recipient_name' => $data['recipient_name'],
                    'phone' => $data['phone'],
                    'address' => $data['address'],
                    'courier' => $data['courier'] ?? null,
                    'shipping_cost' => $shippingCost,
                    'status' => 'pending',
                ]);

                Payment::create([
                    'order_id' => $order->id,
                    'payment_method' => $data['payment_method'],
                    'amount' => $total,
                    'status' => 'pending',
                ]);

                $cart->items()->delete();

                return $order;
            });
        } catch (\RuntimeException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        return redirect()->route('customer.orders.show', $order)->with('success', 'Pesanan berhasil dibuat.');
    }
}
