@extends('admin.layouts.app')

@section('title', 'Tambah Kategori')
@section('heading', 'Tambah Kategori')

@section('content')
    <div class="card max-w-2xl p-6">
        <form method="POST" action="{{ route('admin.categories.store') }}">
            @csrf
            @include('admin.categories._form', ['category' => null])
        </form>
    </div>
@endsection