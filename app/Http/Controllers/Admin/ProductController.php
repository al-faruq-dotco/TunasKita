<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::query()
            ->with(['category', 'primaryImage'])
            ->when($request->filled('q'), fn ($q) => $q->where('name', 'like', '%' . $request->q . '%'))
            ->when($request->filled('category'), fn ($q) => $q->where('category_id', $request->category))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.products.index', [
            'products'   => $products,
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'sellers'    => User::pluck('name', 'id'),
        ]);
    }

    public function create()
    {
        return view('admin.products.create', $this->formData());
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());

        DB::transaction(function () use ($data, $request) {
            $product = Product::create(
                collect($data)->except('images')->all() + ['slug' => $this->uniqueSlug($data['name'])]
            );
            $this->saveImages($product, $request);
        });

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        $product->load('images');

        return view('admin.products.edit', $this->formData($product));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate($this->rules());

        DB::transaction(function () use ($data, $request, $product) {
            // The slug is intentionally left alone so storefront URLs stay stable.
            $product->update(collect($data)->except('images')->all());
            $this->saveImages($product, $request);
        });

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        if (OrderItem::where('product_id', $product->id)->exists()) {
            return back()->with('error', 'Produk sudah pernah dipesan dan tidak bisa dihapus. Ubah statusnya menjadi nonaktif saja.');
        }

        $paths = $product->images()->pluck('image_path')->all();
        $product->delete();
        Storage::disk('public')->delete($paths);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus.');
    }

    public function destroyImage(ProductImage $image)
    {
        $productId  = $image->product_id;
        $wasPrimary = $image->is_primary;

        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        if ($wasPrimary) {
            ProductImage::where('product_id', $productId)->orderBy('id')->first()?->update(['is_primary' => true]);
        }

        return back()->with('success', 'Gambar dihapus.');
    }

    public function setPrimary(ProductImage $image)
    {
        ProductImage::where('product_id', $image->product_id)->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);

        return back()->with('success', 'Gambar utama diubah.');
    }

    private function formData(?Product $product = null): array
    {
        return [
            'product'    => $product,
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'sellers'    => User::where('role', 'seller')->orderBy('name')->get(['id', 'name']),
        ];
    }

    private function rules(): array
    {
        return [
            'category_id'  => ['required', 'exists:categories,id'],
            'seller_id'    => ['required', Rule::exists('users', 'id')->where('role', 'seller')],
            'name'         => ['required', 'string', 'max:150'],
            'description'  => ['nullable', 'string'],
            'price'        => ['required', 'numeric', 'min:0', 'max:9999999999'],
            'stock'        => ['required', 'integer', 'min:0'],
            'plant_age'    => ['nullable', 'string', 'max:80'],
            'plant_size'   => ['nullable', 'string', 'max:80'],
            'availability' => ['required', Rule::in(['available', 'out_of_stock', 'preorder'])],
            'status'       => ['required', Rule::in(['active', 'inactive'])],
            'images'       => ['nullable', 'array', 'max:5'],
            'images.*'     => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::limit(Str::slug($name), 170, '') ?: 'produk';
        $slug = $base;
        $i = 2;

        while (Product::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

    private function saveImages(Product $product, Request $request): void
    {
        $hasPrimary = $product->images()->where('is_primary', true)->exists();

        foreach ($request->file('images', []) as $file) {
            $product->images()->create([
                'image_path' => $file->store('products', 'public'),
                'is_primary' => ! $hasPrimary,
            ]);
            $hasPrimary = true;
        }
    }
}