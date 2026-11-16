<?php
namespace App\Http\Controllers\Customer;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Shipment;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class CheckoutController extends Controller {
 public function index(){ $cart=Cart::with('items.product')->firstOrCreate(['user_id'=>auth()->id()]);$cart->load('items.product');if($cart->items->isEmpty())return redirect()->route('customer.cart.index')->with('error','Keranjang masih kosong.');return view('customer.checkout.index',compact('cart')); }
 public function store(Request $request){
  $data=$request->validate(['recipient_name'=>'required|string|max:150','phone'=>'required|string|max:25','address'=>'required|string','courier'=>'nullable|string|max:80','payment_method'=>'required|string|max:50','notes'=>'nullable|string']);
  try { $order=DB::transaction(function() use($data){
   $cart=Cart::with('items.product')->where('user_id',auth()->id())->firstOrFail();abort_if($cart->items->isEmpty(),422,'Keranjang kosong.');$total=0;
   foreach($cart->items as $item){$p=$item->product()->lockForUpdate()->first();if(!$p || $p->status!=='active' || $p->stock<$item->quantity)throw new \RuntimeException('Stok '.$item->product->name.' tidak mencukupi.');$total += $p->price*$item->quantity;}
   $order=Order::create(['user_id'=>auth()->id(),'order_code'=>'TK-'.now()->format('Ymd').'-'.Str::upper(Str::random(6)),'total_price'=>$total,'status'=>'pending','notes'=>$data['notes']??null]);
   foreach($cart->items as $item){$p=$item->product()->lockForUpdate()->first();OrderItem::create(['order_id'=>$order->id,'product_id'=>$p->id,'quantity'=>$item->quantity,'price'=>$p->price,'subtotal'=>$p->price*$item->quantity]);$p->decrement('stock',$item->quantity);}
   Shipment::create(['order_id'=>$order->id,'recipient_name'=>$data['recipient_name'],'phone'=>$data['phone'],'address'=>$data['address'],'courier'=>$data['courier']??null,'shipping_cost'=>0,'status'=>'pending']);
   Payment::create(['order_id'=>$order->id,'payment_method'=>$data['payment_method'],'amount'=>$total,'status'=>'pending']);$cart->items()->delete();return $order;
  }); } catch (\RuntimeException $e) { return back()->withInput()->with('error',$e->getMessage()); }
  return redirect()->route('customer.orders.show',$order)->with('success','Pesanan berhasil dibuat.');
 }
}
