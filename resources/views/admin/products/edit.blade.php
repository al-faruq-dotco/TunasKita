@extends('admin.layouts.app')

@section('title', 'Edit Produk')
@section('heading', 'Edit Produk')

@section('content')
    <div class="card max-w-4xl p-6">
        <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.products._form')
        </form>
    </div>

    <div class="card mt-6 max-w-4xl p-6">
        <div class="font-bold green">Gambar saat ini</div>
        <p class="mb-4 text-xs muted">Perubahan di bagian ini langsung tersimpan.</p>

        @if ($product->images->isEmpty())
            <p class="text-sm muted">Belum ada gambar. Storefront memakai gambar cadangan.</p>
        @else
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
                @foreach ($product->images as $image)
                    <div class="rounded-xl border border-green-100 p-2">
                        <img src="{{ asset('storage/' . $image->image_path) }}" class="h-32 w-full rounded-lg object-cover" alt="">
                        <div class="mt-2 flex flex-col gap-1 text-xs">
                            @if ($image->is_primary)
                                <span class="rounded-full bg-green-100 px-2 py-1 text-center font-semibold text-green-800">Gambar utama</span>
                            @else
                                <form method="POST" action="{{ route('admin.products.images.primary', $image) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="w-full rounded-lg border border-green-100 px-2 py-1 hover:bg-green-50">Jadikan utama</button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('admin.products.images.destroy', $image) }}"
                                  onsubmit="return confirm('Hapus gambar ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="w-full rounded-lg border border-red-100 px-2 py-1 text-red-600 hover:bg-red-50">Hapus</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection