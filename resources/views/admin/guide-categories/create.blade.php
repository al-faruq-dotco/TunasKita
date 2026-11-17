@extends('admin.layouts.app')

@section('title', 'Tambah Kategori Panduan')
@section('heading', 'Tambah Kategori Panduan')

@section('content')
    <div class="card max-w-2xl p-6">
        <form method="POST" action="{{ route('admin.guide-categories.store') }}">
            @csrf
            @include('admin.guide-categories._form')
        </form>
    </div>
@endsection