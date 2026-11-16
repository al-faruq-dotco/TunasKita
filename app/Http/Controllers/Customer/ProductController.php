<?php
namespace App\Http\Controllers\Customer;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
class ProductController extends Controller {
 public function index(Request $request){
  $query=Product::with(['primaryImage','category'])->where('status','active');
  if($request->filled('q')) $query->where('name','like','%'.$request->q.'%');
  if($request->filled('category')) $query->where('category_id',$request->category);
  $products=$query->latest()->paginate(12)->withQueryString();
  $categories=Category::orderBy('name')->get();
  return view('customer.products.index',compact('products','categories'));
 }
 public function show(Product $product){abort_unless($product->status==='active',404);$product->load(['images','category']);return view('customer.products.show',compact('product'));}
}
