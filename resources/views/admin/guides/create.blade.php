@extends('admin.layouts.app')

@section('title', 'Tambah Panduan')
@section('heading', 'Tambah Panduan Perawatan')

@section('content')
    <div class="card max-w-3xl p-6">
        <form method="POST" action="{{ route('admin.guides.store') }}" enctype="multipart/form-data">
            @csrf
            @include('admin.guides._form')
        </form>
    </div>
@endsection