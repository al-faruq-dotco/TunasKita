@extends('customer.layouts.app')
@section('title', 'Checkout')
@section('content')
    <h1 class="mb-6 text-3xl font-bold text-[#24472f]">Checkout</h1>
    @if ($deliveryZones->isEmpty())
        <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 p-5 text-sm text-amber-900">
            Zona pengiriman aktif belum tersedia. Admin perlu mengaktifkan zona dan mengisi tarif sebelum checkout dapat digunakan.
        </div>
    @endif
    <div class="grid gap-6 md:grid-cols-3">
        <form method="POST" action="{{ route('customer.checkout.store') }}" class="rounded-3xl border border-[#e6eadf] bg-white p-6 shadow-sm md:col-span-2">
            @csrf
            <h2 class="mb-4 text-xl font-bold text-[#24472f]">Informasi Pengiriman</h2>
            <label class="mb-1 block text-sm font-semibold" for="recipient_name">Nama penerima</label>
            <input id="recipient_name" name="recipient_name" required value="{{ old('recipient_name', auth()->user()->name) }}" class="mb-4 w-full rounded-xl border border-[#dce4d8] p-3">
            <label class="mb-1 block text-sm font-semibold" for="phone">Nomor telepon</label>
            <input id="phone" name="phone" required value="{{ old('phone', auth()->user()->phone) }}" class="mb-4 w-full rounded-xl border border-[#dce4d8] p-3">
            <label class="mb-1 block text-sm font-semibold" for="address">Alamat lengkap</label>
            <textarea id="address" name="address" required class="mb-4 w-full rounded-xl border border-[#dce4d8] p-3">{{ old('address', auth()->user()->address) }}</textarea>
            <label class="mb-1 block text-sm font-semibold" for="delivery_zone_id">Zona pengiriman</label>
            <select id="delivery_zone_id" name="delivery_zone_id" required @disabled($deliveryZones->isEmpty()) class="mb-4 w-full rounded-xl border border-[#dce4d8] bg-white p-3">
                <option value="">Pilih kota/kabupaten</option>
                @foreach ($deliveryZones as $zone)
                    <option value="{{ $zone->id }}" data-cost="{{ $zone->shipping_cost }}" @selected(old('delivery_zone_id') == $zone->id)>
                        {{ $zone->city }}, {{ $zone->province }} — Rp {{ number_format($zone->shipping_cost, 0, ',', '.') }}
                    </option>
                @endforeach
            </select>
            <label class="mb-1 block text-sm font-semibold" for="courier">Kurir (opsional)</label>
            <input id="courier" name="courier" value="{{ old('courier') }}" class="mb-4 w-full rounded-xl border border-[#dce4d8] p-3" placeholder="Contoh: JNE">
            <label class="mb-1 block text-sm font-semibold" for="payment_method">Metode pembayaran</label>
            <select id="payment_method" name="payment_method" required class="mb-4 w-full rounded-xl border border-[#dce4d8] bg-white p-3">
                <option value="transfer_manual" @selected(old('payment_method', 'transfer_manual') === 'transfer_manual')>Transfer manual</option>
                <option value="cod" @selected(old('payment_method') === 'cod')>COD (jika tersedia)</option>
            </select>
            <label class="mb-1 block text-sm font-semibold" for="notes">Catatan (opsional)</label>
            <textarea id="notes" name="notes" class="mb-5 w-full rounded-xl border border-[#dce4d8] p-3">{{ old('notes') }}</textarea>
            <button @disabled($deliveryZones->isEmpty()) class="rounded-full bg-[#315b3c] px-6 py-3 font-bold text-white transition hover:bg-[#24472f] disabled:cursor-not-allowed disabled:opacity-50">Buat Pesanan</button>
        </form>
        <aside class="h-fit rounded-3xl border border-[#e6eadf] bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-xl font-bold text-[#24472f]">Ringkasan</h2>
            @php($subtotal = 0)
            @foreach ($cart->items as $item)
                @php($lineTotal = (float) $item->product->price * $item->quantity)
                @php($subtotal += $lineTotal)
                <div class="flex justify-between gap-3 py-2 text-sm">
                    <span>{{ $item->product->name }} × {{ $item->quantity }}</span>
                    <b>Rp {{ number_format($lineTotal, 0, ',', '.') }}</b>
                </div>
            @endforeach
            <hr class="my-3 border-[#e6eadf]">
            <div class="flex justify-between text-sm"><span>Subtotal produk</span><span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span></div>
            <div class="mt-2 flex justify-between text-sm"><span>Ongkos kirim</span><span id="shipping-cost">Pilih zona</span></div>
            <p class="mt-4 text-xs text-[#68776a]">Tarif dihitung dari zona aktif yang dikelola Admin. Harga dan stok akan diperiksa kembali saat pesanan dibuat.</p>
        </aside>
    </div>
    <script>
        const zoneSelect = document.getElementById('delivery_zone_id');
        const shippingCost = document.getElementById('shipping-cost');
        const updateShippingCost = () => {
            const cost = zoneSelect?.selectedOptions[0]?.dataset.cost;
            shippingCost.textContent = cost === undefined
                ? 'Pilih zona'
                : 'Rp ' + new Intl.NumberFormat('id-ID').format(Number(cost));
        };
        zoneSelect?.addEventListener('change', updateShippingCost);
        updateShippingCost();
    </script>
@endsection
