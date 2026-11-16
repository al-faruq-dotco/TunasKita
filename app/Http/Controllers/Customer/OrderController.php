<?php
namespace App\Http\Controllers\Customer;
use App\Http\Controllers\Controller;
use App\Models\Order;
class OrderController extends Controller {
 public function index(){$orders=Order::with('items.product','payment','shipment')->where('user_id',auth()->id())->latest()->paginate(10);return view('customer.orders.index',compact('orders'));}
 public function show(Order $order){abort_unless($order->user_id===auth()->id(),403);$order->load('items.product','payment','shipment');return view('customer.orders.show',compact('order'));}
 public function cancel(Order $order){abort_unless($order->user_id===auth()->id(),403);if($order->status!=='pending')return back()->with('error','Hanya pesanan berstatus menunggu yang dapat dibatalkan.');\Illuminate\Support\Facades\DB::transaction(function()use($order){$order->load('items');foreach($order->items as $item){$item->product()->increment('stock',$item->quantity);} $order->update(['status'=>'cancelled']);$order->shipment?->update(['status'=>'cancelled']);$order->payment?->update(['status'=>'failed']);});return back()->with('success','Pesanan dibatalkan.');}
}
