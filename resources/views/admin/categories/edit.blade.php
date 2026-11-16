@extends('admin.layouts.app')

@section('title', 'Edit Kategori')
@section('heading', 'Edit Kategori')

@section('content')
    <div class="card max-w-2xl p-6">
        <form method="POST" action="{{ route('admin.categories.update', $category) }}">
            @csrf
            @method('PUT')
            @include('admin.categories._form', ['category' => $category])
        </form>
    </div>
@endsection