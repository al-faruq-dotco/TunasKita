@extends('admin.layouts.app')

@section('title', 'Edit Kategori Panduan')
@section('heading', 'Edit Kategori Panduan')

@section('content')
    <div class="card max-w-2xl p-6">
        <form method="POST" action="{{ route('admin.guide-categories.update', $category->id) }}">
            @csrf
            @method('PUT')
            @include('admin.guide-categories._form')
        </form>
    </div>
@endsection