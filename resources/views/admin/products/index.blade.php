@extends('admin.layouts.app')

@section('title', 'Data Produk')
@section('heading', 'Data Produk')

@section('content')
    @php
        $rp = fn ($n) => 'Rp ' . number_format($n, 0, ',', '.');
        $availability = ['available' => 'Tersedia', 'out_of_stock' => 'Habis', 'preorder' => 'Pre-order'];
    @endphp

    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <form method="GET" class="flex flex-wrap gap-2 text-sm">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari produk..."
                   class="rounded-xl border border-green-100 px-3 py-2 outline-none focus:border-[#315d40]">
            <select name="category" class="rounded-xl border border-green-100 px-3 py-2">
                <option value="">Semua kategori</option>
                @foreach ($categories as $c)
                    <option value="{{ $c->id }}" @selected(request('category') == $c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
            <select name="status" class="rounded-xl border border-green-100 px-3 py-2">
                <option value="">Semua status</option>
                <option value="active" @selected(request('status') === 'active')>Aktif</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
            </select>
            <button class="btn text-sm">Filter</button>
        </form>

        <a href="{{ route('admin.products.create') }}" class="btn text-sm">+ Tambah Produk</a>
    </div>

    <div class="card overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-green-50 text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-5 py-3">Produk</th>
                    <th class="px-5 py-3">Kategori</th>
                    <th class="px-5 py-3">Penjual</th>
                    <th class="px-5 py-3">Harga</th>
                    <th class="px-5 py-3">Stok</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr class="border-t border-green-50">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                @if ($product->primaryImage)
                                    <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}"
                                         class="h-12 w-12 rounded-lg object-cover" alt="">
                                @else
                                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-50">🍇</div>
                                @endif
                                <div>
                                    <div class="font-semibold green">{{ $product->name }}</div>
                                    <div class="text-xs muted">{{ $availability[$product->availability] ?? $product->availability }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3">{{ $product->category->name ?? '-' }}</td>
                        <td class="px-5 py-3">{{ $sellers[$product->seller_id] ?? 'Tidak diketahui (#' . $product->seller_id . ')' }}</td>
                        <td class="px-5 py-3">{{ $rp($product->price) }}</td>
                        <td class="px-5 py-3">{{ $product->stock }}</td>
                        <td class="px-5 py-3">
                            <span class="rounded-full px-3 py-1 text-xs font-semibold
                                {{ $product->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-slate-100 text-slate-600' }}">
                                {{ $product->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.products.edit', $product) }}"
                                   class="rounded-lg border border-green-100 px-3 py-1.5 hover:bg-green-50">Edit</a>
                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}"
                                      onsubmit="return confirm('Hapus produk {{ addslashes($product->name) }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="rounded-lg border border-red-100 px-3 py-1.5 text-red-600 hover:bg-red-50">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center muted">Produk tidak ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $products->links() }}</div>
@endsection