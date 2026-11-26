<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\WishlistItem;

class WishlistController extends Controller
{
    public function index()
    {
        $items = WishlistItem::with('product.primaryImage')->where('user_id', auth()->id())->latest()->get();

        return view('customer.wishlist.index', compact('items'));
    }

    public function store(Product $product)
    {
        abort_unless($product->isAvailableForPurchase(), 422, 'Produk tidak tersedia untuk wishlist.');
        WishlistItem::firstOrCreate(['user_id' => auth()->id(), 'product_id' => $product->id]);

        return back()->with('success', 'Produk disimpan ke wishlist.');
    }

    public function destroy($item)
    {
        WishlistItem::where('user_id', auth()->id())->findOrFail($item)->delete();

        return back()->with('success', 'Produk dihapus dari wishlist.');
    }
}
