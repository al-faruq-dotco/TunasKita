<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    private function cart()
    {
        return Cart::firstOrCreate(['user_id' => auth()->id()]);
    }

    public function index()
    {
        $cart = $this->cart()->load('items.product.primaryImage');

        return view('customer.cart.index', compact('cart'));
    }

    public function store(Request $request, Product $product)
    {
        abort_unless($product->isAvailableForPurchase(), 422, 'Produk tidak tersedia.');
        $data = $request->validate(['quantity' => 'required|integer|min:1|max:99']);
        $cart = $this->cart();
        $item = $cart->items()->firstOrNew(['product_id' => $product->id]);
        $newQty = ($item->exists ? $item->quantity : 0) + $data['quantity'];
        if ($newQty > $product->stock) {
            return back()->withErrors(['quantity' => 'Jumlah melebihi stok tersedia.']);
        }
        $item->quantity = $newQty;
        $item->save();

        return redirect()->route('customer.cart.index')->with('success', 'Produk ditambahkan ke keranjang.');
    }

    public function update(Request $request, $item)
    {
        $data = $request->validate(['quantity' => 'required|integer|min:1|max:99']);
        $cart = $this->cart();
        $row = $cart->items()->with('product')->findOrFail($item);
        abort_unless($row->product->isAvailableForPurchase(), 422, 'Produk tidak lagi tersedia.');
        if ($data['quantity'] > $row->product->stock) {
            return back()->withErrors(['quantity' => 'Jumlah melebihi stok.']);
        }

        $row->update($data);

        return back()->with('success', 'Keranjang diperbarui.');
    }

    public function destroy($item)
    {
        $this->cart()->items()->findOrFail($item)->delete();

        return back()->with('success', 'Produk dihapus dari keranjang.');
    }
}
