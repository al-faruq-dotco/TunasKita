<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

trait ManagesProducts
{
    protected function baseProductRules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0', 'max:9999999999'],
            'stock' => ['required', 'integer', 'min:0'],
            'plant_age' => ['nullable', 'string', 'max:80'],
            'plant_size' => ['nullable', 'string', 'max:80'],
            'availability' => ['required', 'in:available,out_of_stock,preorder'],
            'status' => ['required', 'in:active,inactive'],
            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    protected function productMessages(): array
    {
        return [
            'category_id.required' => 'Kategori produk wajib dipilih.',
            'category_id.exists' => 'Kategori tidak ditemukan.',
            'name.required' => 'Nama produk wajib diisi.',
            'price.required' => 'Harga produk wajib diisi.',
            'price.numeric' => 'Harga harus berupa angka.',
            'stock.required' => 'Stok produk wajib diisi.',
            'images.max' => 'Maksimal 5 gambar produk.',
            'images.*.image' => 'File harus berupa gambar.',
            'images.*.mimes' => 'Format gambar harus JPG, JPEG, PNG, atau WEBP.',
            'images.*.max' => 'Ukuran gambar maksimal 2 MB.',
        ];
    }

    protected function ensureImageQuota(
        Request $request,
        ?Product $product = null
    ): void {
        $existing = $product
            ? $product->images()->count()
            : 0;

        $newImages = count($request->file('images', []));

        if ($existing + $newImages > 5) {
            throw ValidationException::withMessages([
                'images' => 'Maksimal 5 gambar untuk setiap produk.',
            ]);
        }
    }

    protected function uniqueSlug(string $name): string
    {
        $base = Str::limit(Str::slug($name), 170, '') ?: 'produk';

        $slug = $base;
        $i = 2;

        while (Product::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

    protected function saveImages(
        Product $product,
        Request $request
    ): void {
        $hasPrimary = $product->images()
            ->where('is_primary', true)
            ->exists();

        foreach ($request->file('images', []) as $file) {
            $path = $file->store('products', 'public');

            $product->images()->create([
                'image_path' => $path,
                'is_primary' => !$hasPrimary,
            ]);

            $hasPrimary = true;
        }
    }

    protected function deleteProductWithImages(Product $product): void
    {
        $paths = $product->images()
            ->pluck('image_path')
            ->all();

        $product->delete();

        Storage::disk('public')->delete($paths);
    }

    protected function removeImage(ProductImage $image): void
    {
        $product = $image->product;
        $wasPrimary = $image->is_primary;

        Storage::disk('public')->delete($image->image_path);

        $image->delete();

        if ($wasPrimary) {
            $nextImage = $product->images()
                ->orderBy('id')
                ->first();

            $nextImage?->update(['is_primary' => true]);
        }
    }

    protected function makePrimary(ProductImage $image): void
    {
        ProductImage::where('product_id', $image->product_id)
            ->update(['is_primary' => false]);

        $image->update(['is_primary' => true]);
    }
}
