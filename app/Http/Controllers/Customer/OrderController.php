<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('items.product', 'payment', 'shipment')->where('user_id', auth()->id())->latest()->paginate(10);

        return view('customer.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);
        $order->load('items.product', 'payment', 'shipment.tracking');

        return view('customer.orders.show', compact('order'));
    }

    public function cancel(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);
        $cancelled = DB::transaction(function () use ($order) {
            $locked = Order::with(['items', 'shipment', 'payment'])->lockForUpdate()->findOrFail($order->id);
            if ($locked->status !== 'pending') {
                return false;
            }foreach ($locked->items as $item) {
                $item->product()->lockForUpdate()->first()?->increment('stock', $item->quantity);
            } $locked->update(['status' => 'cancelled']);
            $locked->shipment?->update(['status' => 'cancelled']);
            if ($locked->payment) {
                $paymentStatus = match ($locked->payment->status) {
                    'paid' => 'refunded','pending' => 'failed',default => $locked->payment->status
                };
                $locked->payment->update(['status' => $paymentStatus]);
            }

return true;
        });
        if (! $cancelled) {
            return back()->with('error', 'Hanya pesanan berstatus menunggu yang dapat dibatalkan.');
        }

return back()->with('success','Pesanan dibatalkan.');
    }
}
