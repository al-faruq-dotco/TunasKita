@extends('admin.layouts.app')

@section('title', 'Edit Panduan')
@section('heading', 'Edit Panduan Perawatan')

@section('content')
    <div class="card max-w-3xl p-6">
        <form method="POST" action="{{ route('admin.guides.update', $guide->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.guides._form')
        </form>
    </div>
@endsection