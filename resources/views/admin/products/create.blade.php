@extends('admin.layouts.app')

@section('title', 'Tambah Produk')
@section('heading', 'Tambah Produk')

@section('content')
    <div class="card max-w-4xl p-6">
        <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
            @csrf
            @include('admin.products._form')
        </form>
    </div>
@endsection