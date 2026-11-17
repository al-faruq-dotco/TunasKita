@extends('admin.layouts.app')

@section('title', 'Zona Pengiriman')
@section('heading', 'Zona Pengiriman')

@section('content')
    @php $rp = fn ($n) => 'Rp ' . number_format($n, 0, ',', '.'); @endphp

    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <form method="GET" class="flex flex-wrap gap-2 text-sm">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari provinsi atau kota..."
                   class="rounded-xl border border-green-100 px-3 py-2 outline-none focus:border-[#315d40]">
            <select name="status" class="rounded-xl border border-green-100 px-3 py-2">
                <option value="">Semua status</option>
                <option value="active" @selected(request('status') === 'active')>Aktif</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
            </select>
            <button class="btn text-sm">Filter</button>
        </form>

        <a href="{{ route('admin.delivery-zones.create') }}" class="btn text-sm">+ Tambah Zona</a>
    </div>

    <div class="card overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-green-50 text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-5 py-3">Provinsi</th>
                    <th class="px-5 py-3">Kota</th>
                    <th class="px-5 py-3">Ongkir</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($zones as $zone)
                    <tr class="border-t border-green-50">
                        <td class="px-5 py-3">{{ $zone->province }}</td>
                        <td class="px-5 py-3 font-semibold green">{{ $zone->city }}</td>
                        <td class="px-5 py-3">{{ $rp($zone->shipping_cost) }}</td>
                        <td class="px-5 py-3">
                            <span class="rounded-full px-3 py-1 text-xs font-semibold
                                {{ $zone->is_active ? 'bg-green-100 text-green-800' : 'bg-slate-100 text-slate-600' }}">
                                {{ $zone->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex justify-end gap-2">
                                <form method="POST" action="{{ route('admin.delivery-zones.toggle', $zone->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="rounded-lg border border-green-100 px-3 py-1.5 hover:bg-green-50">
                                        {{ $zone->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                                <a href="{{ route('admin.delivery-zones.edit', $zone->id) }}"
                                   class="rounded-lg border border-green-100 px-3 py-1.5 hover:bg-green-50">Edit</a>
                                <form method="POST" action="{{ route('admin.delivery-zones.destroy', $zone->id) }}"
                                      onsubmit="return confirm('Hapus zona {{ addslashes($zone->city) }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="rounded-lg border border-red-100 px-3 py-1.5 text-red-600 hover:bg-red-50">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center muted">Belum ada zona pengiriman.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $zones->links() }}</div>
@endsection