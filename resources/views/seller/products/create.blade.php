@extends('seller.layouts.app')
@section('title','Tambah Produk')
@section('heading','Tambah Produk')
@section('content')<div class="mb-5"><a href="{{ route('seller.products.index') }}" class="text-sm font-semibold green">← Kembali ke produk</a><h1 class="mt-3 text-2xl font-extrabold green">Tambah Produk Baru</h1><p class="mt-1 text-sm muted">Lengkapi informasi bibit anggur yang akan ditampilkan di katalog.</p></div><form method="POST" action="{{ route('seller.products.store') }}" enctype="multipart/form-data" class="card p-5 md:p-7">@csrf @include('seller.products._form')</form>@endsection
