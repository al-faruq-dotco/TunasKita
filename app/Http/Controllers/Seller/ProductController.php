<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with(['category', 'primaryImage'])
            ->where('seller_id', auth()->id())
            ->when($request->filled('q'), fn ($q) => $q->where('name', 'like', '%'.$request->q.'%'))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest('id')->paginate(10)->withQueryString();

        return view('seller.products.index', compact('products'));
    }

    public function create()
    {
        return view('seller.products.create', ['product' => null, 'categories' => Category::orderBy('name')->get(['id', 'name'])]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        DB::transaction(function () use ($data, $request) {
            $product = Product::create(collect($data)->except('images')->all() + [
                'seller_id' => auth()->id(),
                'slug' => $this->uniqueSlug($data['name']),
            ]);
            $this->saveImages($product, $request);
        });

        return redirect()->route('seller.products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $product = $this->ownedProduct($id)->load('images');

        return view('seller.products.edit', ['product' => $product, 'categories' => Category::orderBy('name')->get(['id', 'name'])]);
    }

    public function update(Request $request, int $id)
    {
        $product = $this->ownedProduct($id);
        $data = $request->validate($this->rules());
        DB::transaction(function () use ($data, $request, $product) {
            $product->update(collect($data)->except('images')->all());
            $this->saveImages($product, $request);
        });

        return redirect()->route('seller.products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $product = $this->ownedProduct($id);
        if (OrderItem::where('product_id', $product->id)->exists()) {
            return back()->with('error', 'Produk sudah pernah dipesan. Nonaktifkan produk agar riwayat transaksi tetap aman.');
        }
        $paths = $product->images()->pluck('image_path')->all();
        $product->delete();
        Storage::disk('public')->delete($paths);

        return back()->with('success', 'Produk berhasil dihapus.');
    }

    public function destroyImage(int $image)
    {
        $record = ProductImage::whereHas('product', fn ($q) => $q->where('seller_id', auth()->id()))->findOrFail($image);
        $productId = $record->product_id;
        $wasPrimary = $record->is_primary;
        Storage::disk('public')->delete($record->image_path);
        $record->delete();
        if ($wasPrimary) {
            ProductImage::where('product_id', $productId)->orderBy('id')->first()?->update(['is_primary' => true]);
        }

        return back()->with('success', 'Gambar produk dihapus.');
    }

    public function setPrimary(int $image)
    {
        $record = ProductImage::whereHas('product', fn ($q) => $q->where('seller_id', auth()->id()))->findOrFail($image);
        ProductImage::where('product_id', $record->product_id)->update(['is_primary' => false]);
        $record->update(['is_primary' => true]);

        return back()->with('success', 'Gambar utama diperbarui.');
    }

    private function ownedProduct(int $id): Product
    {
        return Product::where('seller_id', auth()->id())->findOrFail($id);
    }

    private function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0', 'max:9999999999'],
            'stock' => ['required', 'integer', 'min:0'],
            'plant_age' => ['nullable', 'string', 'max:80'],
            'plant_size' => ['nullable', 'string', 'max:80'],
            'availability' => ['required', Rule::in(['available', 'out_of_stock', 'preorder'])],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::limit(Str::slug($name), 170, '') ?: 'produk';
        $slug = $base;
        $i = 2;
        while (Product::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    private function saveImages(Product $product, Request $request): void
    {
        $hasPrimary = $product->images()->where('is_primary', true)->exists();
        foreach ($request->file('images', []) as $file) {
            $product->images()->create(['image_path' => $file->store('products', 'public'), 'is_primary' => ! $hasPrimary]);
            $hasPrimary = true;
        }
    }
}
