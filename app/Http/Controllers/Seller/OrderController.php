<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    private const TRANSITIONS = [
        'pending' => ['confirmed'],
        'confirmed' => ['processing'],
        'processing' => ['shipped'],
        'shipped' => ['completed'],
        'completed' => [],
        'cancelled' => [],
    ];

    public function index(Request $request)
    {
        $orders = $this->sellerOrders()
            ->with(['user:id,name,email', 'payment:id,order_id,status', 'items' => fn ($q) => $q->whereHas('product', fn ($p) => $p->where('seller_id', auth()->id()))->with('product:id,name,seller_id')])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('q'), fn ($q) => $q->where('order_code', 'like', '%'.$request->q.'%'))
            ->latest()->paginate(10)->withQueryString();

        return view('seller.orders.index', compact('orders'));
    }

    public function show(int $id)
    {
        $order = $this->sellerOrders()->with(['user', 'payment', 'shipment', 'items' => fn ($q) => $q->whereHas('product', fn ($p) => $p->where('seller_id', auth()->id()))->with('product')])->findOrFail($id);
        $allowed = self::TRANSITIONS[$order->status] ?? [];
        $sellerSubtotal = (float) $order->items->sum('subtotal');

        return view('seller.orders.show', compact('order', 'allowed', 'sellerSubtotal'));
    }

    public function updateStatus(Request $request, int $id)
    {
        $order = $this->sellerOrders()->with(['items.product', 'shipment', 'payment'])->findOrFail($id);
        $foreignItems = $order->items()->whereHas('product', fn ($q) => $q->where('seller_id', '!=', auth()->id()))->count();
        if ($foreignItems > 0) {
            return back()->with('error', 'Pesanan ini berisi produk dari lebih dari satu penjual. Perubahan status harus dilakukan oleh administrator.');
        }
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(self::TRANSITIONS))],
            'courier' => ['nullable', 'string', 'max:255'],
            'tracking_number' => ['nullable', 'string', 'max:255'],
        ]);

        if (! in_array($data['status'], self::TRANSITIONS[$order->status] ?? [], true)) {
            return back()->with('error', 'Perubahan status pesanan tidak diperbolehkan.');
        }
        if ($data['status'] === 'shipped' && empty($data['courier'])) {
            return back()->withErrors(['courier' => 'Nama kurir wajib diisi saat pesanan dikirim.']);
        }
        if ($data['status'] === 'completed' && (! $order->payment || ($order->payment->status !== 'paid'
            && strtolower($order->payment->payment_method) !== 'cod'))) {
            return back()->with('error', 'Pesanan belum dapat diselesaikan karena pembayaran belum lunas.');
        }

        DB::transaction(function () use ($order, $data) {
            $order->update(['status' => $data['status']]);
            if ($order->shipment) {
                $shipmentData = match ($data['status']) {
                    'processing' => ['status' => 'processing'],
                    'shipped' => ['status' => 'shipped', 'courier' => $data['courier'] ?? null, 'tracking_number' => $data['tracking_number'] ?? null, 'shipped_at' => now()],
                    'completed' => ['status' => 'delivered', 'delivered_at' => now()],
                    default => [],
                };
                if ($shipmentData) {
                    $order->shipment->update($shipmentData);
                    DB::table('shipment_tracking')->insert([
                        'shipment_id' => $order->shipment->id,
                        'status' => $shipmentData['status'],
                        'description' => match ($data['status']) {
                            'processing' => 'Pesanan sedang diproses penjual.',
                            'shipped' => 'Pesanan telah dikirim oleh penjual.',
                            'completed' => 'Pesanan telah diterima.',
                        },
                        'tracked_at' => now(), 'created_at' => now(), 'updated_at' => now(),
                    ]);
                }
            }
            if ($data['status'] === 'completed' && $order->payment && strtolower($order->payment->payment_method) === 'cod' && $order->payment->status === 'pending') {
                $order->payment->update(['status' => 'paid', 'paid_at' => now()]);
            }
        });

        return redirect()->route('seller.orders.show', $order)->with('success', 'Status pesanan berhasil diperbarui.');
    }

    private function sellerOrders()
    {
        // Seller hanya melihat pesanan yang memuat setidaknya satu produk miliknya.
        return Order::whereHas('items.product', fn ($q) => $q->where('seller_id', auth()->id()));
    }
}
