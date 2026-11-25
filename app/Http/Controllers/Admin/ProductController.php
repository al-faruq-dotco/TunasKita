<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\ManagesProducts;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    use ManagesProducts;

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
        $data = $request->validate($this->rules(), $this->productMessages());
        $this->ensureImageQuota($request);

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
        $data = $request->validate($this->rules(), $this->productMessages());
        $this->ensureImageQuota($request, $product);

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

        $this->deleteProductWithImages($product);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus.');
    }

    public function destroyImage(ProductImage $image)
    {
        $this->removeImage($image);

        return back()->with('success', 'Gambar dihapus.');
    }

    public function setPrimary(ProductImage $image)
    {
        $this->makePrimary($image);

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
        return $this->baseProductRules() + [
            'seller_id' => ['required', Rule::exists('users', 'id')->where('role', 'seller')],
        ];
    }
}
