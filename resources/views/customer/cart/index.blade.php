@extends('customer.layouts.app') @section('title', 'Keranjang') @section('content')<h1 class="text-3xl font-bold green mb-6">
    Keranjang Belanja</h1>@php($total = 0)<div class="space-y-4">
    @forelse($cart->items as $item)
        @php($total += $item->product->price * $item->quantity)
        <div class="card p-4 flex flex-col md:flex-row md:items-center gap-4"><img
                class="w-24 h-24 rounded-xl object-cover" src="{{ $item->product->thumbnail_url }}">
            <div class="flex-1"><b>{{ $item->product->name }}</b>
                <p class="green font-bold">Rp {{ number_format($item->product->price, 0, ',', '.') }}</p>
            </div>
            <form method="POST" action="{{ route('customer.cart.update', $item) }}" class="flex gap-2">@csrf
                @method('PATCH')<input type="number" name="quantity" min="1" max="{{ $item->product->stock }}"
                    value="{{ $item->quantity }}" class="border rounded-lg p-2 w-20"><button
                    class="border rounded-lg px-3">Update</button></form>
            <form method="POST" action="{{ route('customer.cart.destroy', $item) }}">@csrf @method('DELETE')<button
                    class="text-red-600">Hapus</button></form><b>Rp
                {{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}</b>
    </div>@empty<div class="card p-10 text-center muted">Keranjang masih kosong. <a class="green underline"
                href="{{ route('customer.products.index') }}">Mulai belanja</a></div>
    @endforelse
</div>
@if ($cart->items->isNotEmpty())
    <div class="card p-6 mt-6 flex justify-between items-center">
        <div>
            <p class="muted">Total sementara</p><b class="text-2xl green">Rp
                {{ number_format($total, 0, ',', '.') }}</b>
        </div><a class="btn" href="{{ route('customer.checkout.index') }}">Lanjut Checkout →</a>
    </div>
@endif @endsection
