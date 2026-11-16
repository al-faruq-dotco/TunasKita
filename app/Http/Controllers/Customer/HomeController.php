<?php
namespace App\Http\Controllers\Customer;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
class HomeController extends Controller {
 public function index(){
  $categories=Category::withCount(['products'=>fn($q)=>$q->where('status','active')])->orderBy('name')->get();
  $products=Product::with(['primaryImage','category'])->where('status','active')->where('availability','!=','out_of_stock')->latest()->take(8)->get();
  return view('customer.home',compact('categories','products'));
 }
}
