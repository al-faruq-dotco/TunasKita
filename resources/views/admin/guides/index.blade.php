@extends('admin.layouts.app')

@section('title', 'Panduan Perawatan')
@section('heading', 'Panduan Perawatan')

@section('content')
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <form method="GET" class="flex flex-wrap gap-2 text-sm">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari judul..."
                   class="rounded-xl border border-green-100 px-3 py-2 outline-none focus:border-[#315d40]">
            <select name="category" class="rounded-xl border border-green-100 px-3 py-2">
                <option value="">Semua kategori</option>
                @foreach ($categories as $c)
                    <option value="{{ $c->id }}" @selected(request('category') == $c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
            <button class="btn text-sm">Filter</button>
        </form>

        <a href="{{ route('admin.guides.create') }}" class="btn text-sm">+ Tambah Panduan</a>
    </div>

    <div class="card overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-green-50 text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-5 py-3">Panduan</th>
                    <th class="px-5 py-3">Kategori</th>
                    <th class="px-5 py-3">Penulis</th>
                    <th class="px-5 py-3">Dibuat</th>
                    <th class="px-5 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($guides as $guide)
                    <tr class="border-t border-green-50">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                @if ($guide->image)
                                    <img src="{{ asset('storage/' . $guide->image) }}" class="h-12 w-12 rounded-lg object-cover" alt="">
                                @else
                                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-50">🌿</div>
                                @endif
                                <div class="font-semibold green">{{ $guide->title }}</div>
                            </div>
                        </td>
                        <td class="px-5 py-3">{{ $guide->category->name ?? 'Tanpa kategori' }}</td>
                        <td class="px-5 py-3">{{ $guide->author->name ?? '-' }}</td>
                        <td class="px-5 py-3 muted">{{ $guide->created_at?->format('d M Y') }}</td>
                        <td class="px-5 py-3">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.guides.edit', $guide->id) }}"
                                   class="rounded-lg border border-green-100 px-3 py-1.5 hover:bg-green-50">Edit</a>
                                <form method="POST" action="{{ route('admin.guides.destroy', $guide->id) }}"
                                      onsubmit="return confirm('Hapus panduan {{ addslashes($guide->title) }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="rounded-lg border border-red-100 px-3 py-1.5 text-red-600 hover:bg-red-50">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center muted">Belum ada panduan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $guides->links() }}</div>
@endsection