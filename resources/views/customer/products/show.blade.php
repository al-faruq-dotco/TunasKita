@extends('customer.layouts.app')

@section('title', $product->name)

@section('content')
    <div class="grid gap-8 md:grid-cols-2">

        {{-- GALERI GAMBAR PRODUK --}}
        <div class="overflow-hidden rounded-3xl border border-green-100 bg-white">

            @php
                $gallery = $product->images->sortByDesc('is_primary')->values();
            @endphp

            <img
                id="main-image"
                class="h-96 w-full bg-green-50 object-cover"
                src="{{ $product->thumbnail_url }}"
                alt="{{ $product->name }}"
            >

            @if ($gallery->count() > 1)
                <div class="flex gap-2 overflow-x-auto p-3">
                    @foreach ($gallery as $img)
                        <button
                            type="button"
                            data-src="{{ $img->url }}"
                            onclick="document.getElementById('main-image').src = this.dataset.src"
                            class="shrink-0"
                        >
                            <img
                                class="h-16 w-16 rounded-lg border border-green-100 object-cover"
                                src="{{ $img->url }}"
                                alt="Foto {{ $loop->iteration }} {{ $product->name }}"
                            >
                        </button>
                    @endforeach
                </div>
            @endif

        </div>

        {{-- INFORMASI PRODUK --}}
        <div>

            <p class="text-sm text-[#718071]">
                {{ $product->category?->name }}
            </p>

            <h1 class="mt-2 text-3xl font-extrabold text-[#244d35]">
                {{ $product->name }}
            </h1>

            <p class="mt-4 text-2xl font-bold text-[#244d35]">
                Rp {{ number_format($product->price, 0, ',', '.') }}
            </p>

            <p class="mt-4">
                {{ $product->description }}
            </p>

            {{-- DETAIL PRODUK --}}
            <div class="my-5 text-sm leading-7 text-[#718071]">
                Usia tanaman: {{ $product->plant_age ?? '-' }}<br>
                Ukuran: {{ $product->plant_size ?? '-' }}<br>
                Stok: {{ $product->stock }}<br>

                Ketersediaan:
                {{ $product->availability === 'preorder'
                    ? 'Preorder'
                    : ($product->isAvailableForPurchase() ? 'Tersedia' : 'Tidak tersedia') }}
            </div>

            {{-- KERANJANG DAN WISHLIST --}}
            @if ($product->isAvailableForPurchase())

                <form
                    method="POST"
                    action="{{ route('customer.cart.store', $product) }}"
                    class="flex gap-3"
                >
                    @csrf

                    <input
                        type="number"
                        name="quantity"
                        min="1"
                        max="{{ $product->stock }}"
                        value="1"
                        class="w-24 rounded-xl border border-green-100 p-3"
                    >

                    <button class="rounded-full bg-[#315d40] px-5 py-3 font-semibold text-white">
                        Tambah ke Keranjang
                    </button>
                </form>

                <form
                    method="POST"
                    action="{{ route('customer.wishlist.store', $product) }}"
                    class="mt-3"
                >
                    @csrf

                    <button class="rounded-full border border-green-700 px-5 py-2 text-[#244d35]">
                        ♡ Simpan Favorit
                    </button>
                </form>

            @else

                <p class="rounded-xl bg-amber-50 p-4 text-sm text-amber-900">
                    Produk ini sedang tidak tersedia untuk dibeli.
                </p>

            @endif

        </div>
    </div>
@endsection