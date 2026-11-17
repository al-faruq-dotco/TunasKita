@extends('admin.layouts.app')

@section('title', 'Data ' . $label)
@section('heading', 'Data ' . $label)

@section('content')
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <form method="GET" class="flex gap-2 text-sm">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama atau email..."
                   class="rounded-xl border border-green-100 px-3 py-2 outline-none focus:border-[#315d40]">
            <button class="btn text-sm">Cari</button>
        </form>

        <a href="{{ route($routeBase . '.create') }}" class="btn text-sm">+ Tambah {{ $label }}</a>
    </div>

    <div class="card overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-green-50 text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-5 py-3">Nama</th>
                    <th class="px-5 py-3">Email</th>
                    <th class="px-5 py-3">Telepon</th>
                    <th class="px-5 py-3">{{ $relatedLabel }}</th>
                    <th class="px-5 py-3">Terdaftar</th>
                    <th class="px-5 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($accounts as $account)
                    <tr class="border-t border-green-50">
                        <td class="px-5 py-3 font-semibold green">{{ $account->name }}</td>
                        <td class="px-5 py-3">{{ $account->email }}</td>
                        <td class="px-5 py-3">{{ $account->phone ?: '-' }}</td>
                        <td class="px-5 py-3">{{ $account->related_count }}</td>
                        <td class="px-5 py-3 muted">{{ $account->created_at?->format('d M Y') ?? '-' }}</td>
                        <td class="px-5 py-3">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route($routeBase . '.edit', $account->id) }}"
                                   class="rounded-lg border border-green-100 px-3 py-1.5 hover:bg-green-50">Edit</a>
                                <form method="POST" action="{{ route($routeBase . '.destroy', $account->id) }}"
                                      onsubmit="return confirm('Hapus akun {{ addslashes($account->name) }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="rounded-lg border border-red-100 px-3 py-1.5 text-red-600 hover:bg-red-50">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center muted">Belum ada data.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $accounts->links() }}</div>
@endsection