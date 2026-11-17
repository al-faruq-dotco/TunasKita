@extends('admin.layouts.app')

@section('title', 'Kategori Panduan')
@section('heading', 'Kategori Panduan')

@section('content')
    <div class="mb-4 flex items-center justify-between">
        <p class="text-sm muted">Kelompokkan panduan perawatan berdasarkan topik.</p>
        <a href="{{ route('admin.guide-categories.create') }}" class="btn text-sm">+ Tambah Kategori</a>
    </div>

    <div class="card overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-green-50 text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-5 py-3">Nama</th>
                    <th class="px-5 py-3">Deskripsi</th>
                    <th class="px-5 py-3">Panduan</th>
                    <th class="px-5 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $category)
                    <tr class="border-t border-green-50">
                        <td class="px-5 py-3 font-semibold green">{{ $category->name }}</td>
                        <td class="px-5 py-3 muted">{{ \Illuminate\Support\Str::limit($category->description, 70) ?: '-' }}</td>
                        <td class="px-5 py-3">{{ $category->guides_count }}</td>
                        <td class="px-5 py-3">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.guide-categories.edit', $category->id) }}"
                                   class="rounded-lg border border-green-100 px-3 py-1.5 hover:bg-green-50">Edit</a>
                                <form method="POST" action="{{ route('admin.guide-categories.destroy', $category->id) }}"
                                      onsubmit="return confirm('Hapus kategori {{ addslashes($category->name) }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="rounded-lg border border-red-100 px-3 py-1.5 text-red-600 hover:bg-red-50">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-10 text-center muted">Belum ada kategori panduan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $categories->links() }}</div>
@endsection