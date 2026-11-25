<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    /** Allowed status changes. Completed and cancelled are final. */
    public const TRANSITIONS = [
        'pending'    => ['confirmed', 'cancelled'],
        'confirmed'  => ['processing', 'cancelled'],
        'processing' => ['shipped', 'cancelled'],
        'shipped'    => ['completed'],
        'completed'  => [],
        'cancelled'  => [],
    ];

    public function index(Request $request)
    {
        $orders = Order::query()
            ->with(['user:id,name', 'payment:id,order_id,status'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%' . $request->q . '%';
                $q->where(fn ($w) => $w
                    ->where('order_code', 'like', $term)
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', $term)));
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.product.primaryImage', 'shipment', 'payment']);

        return view('admin.orders.show', [
            'order'   => $order,
            'allowed' => self::TRANSITIONS[$order->status] ?? [],
        ]);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'status'          => ['required', Rule::in(array_keys(self::TRANSITIONS))],
            'courier'         => ['nullable', 'string', 'max:255'],
            'tracking_number' => ['nullable', 'string', 'max:255'],
        ]);

        $error = null;

        DB::transaction(function () use ($order, $data, &$error) {
            // Lock the row so a customer cancelling at the same moment can't race with us.
            $order = Order::with(['items', 'shipment', 'payment'])->lockForUpdate()->findOrFail($order->id);
            $new = $data['status'];

            if (! in_array($new, self::TRANSITIONS[$order->status] ?? [], true)) {
                $error = 'Status tidak bisa diubah dari ' . $order->status . ' ke ' . $new . '.';
                return;
            }

            if ($new === 'completed' && $order->payment && $order->payment->status !== 'paid') {
                if ($order->payment->status === 'pending' && strtolower($order->payment->payment_method) === 'cod') {
                    $order->payment->update(['status' => 'paid', 'paid_at' => now()]);
                } else {
                    $error = 'Pesanan belum bisa diselesaikan karena pembayaran belum lunas. Verifikasi pembayaran dulu.';
                    return;
                }
            }

            if ($new === 'cancelled') {
                foreach ($order->items as $item) {
                    Product::whereKey($item->product_id)->increment('stock', $item->quantity);
                }

                if ($order->payment) {
                    $paymentStatus = ['paid' => 'refunded', 'pending' => 'failed'][$order->payment->status]
                        ?? $order->payment->status;
                    $order->payment->update(['status' => $paymentStatus]);
                }
            }

            if ($order->shipment) {
                $shipmentUpdate = match ($new) {
                    'processing' => ['status' => 'processing'],
                    'shipped'    => ['status' => 'shipped', 'shipped_at' => now()],
                    'completed'  => ['status' => 'delivered', 'delivered_at' => now()],
                    'cancelled'  => ['status' => 'cancelled'],
                    default      => [],
                };

                if ($new === 'shipped') {
                    // array_filter keeps any courier the customer already entered when these are left blank
                    $shipmentUpdate += array_filter([
                        'courier'         => $data['courier'] ?? null,
                        'tracking_number' => $data['tracking_number'] ?? null,
                    ]);
                }

                if ($shipmentUpdate) {
                    $order->shipment->update($shipmentUpdate);

                    DB::table('shipment_tracking')->insert([
                        'shipment_id' => $order->shipment->id,
                        'status'      => $shipmentUpdate['status'],
                        'description' => $this->trackingText($new),
                        'tracked_at'  => now(),
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ]);
                }
            }

            $order->update(['status' => $new]);
        });

        if ($error) {
            return back()->with('error', $error);
        }

        return back()->with('success', 'Status pesanan diperbarui.');
    }

    private function trackingText(string $status): string
    {
        return match ($status) {
            'processing' => 'Pesanan sedang diproses penjual.',
            'shipped'    => 'Pesanan telah dikirim.',
            'completed'  => 'Pesanan telah diterima.',
            'cancelled'  => 'Pengiriman dibatalkan.',
            default      => '',
        };
    }
}