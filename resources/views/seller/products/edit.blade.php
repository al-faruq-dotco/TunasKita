@extends('seller.layouts.app')
@section('title','Edit Produk')
@section('heading','Edit Produk')
@section('content')<div class="mb-5"><a href="{{ route('seller.products.index') }}" class="text-sm font-semibold green">← Kembali ke produk</a><h1 class="mt-3 text-2xl font-extrabold green">Perbarui Produk</h1><p class="mt-1 text-sm muted">Perbarui detail produk dan stok toko Anda.</p></div><form method="POST" action="{{ route('seller.products.update',$product) }}" enctype="multipart/form-data" class="card p-5 md:p-7">@csrf @method('PUT') @include('seller.products._form')</form>@endsection
