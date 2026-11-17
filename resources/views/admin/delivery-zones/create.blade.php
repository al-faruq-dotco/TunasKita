@extends('admin.layouts.app')

@section('title', 'Tambah Zona')
@section('heading', 'Tambah Zona Pengiriman')

@section('content')
    <div class="card max-w-3xl p-6">
        <form method="POST" action="{{ route('admin.delivery-zones.store') }}">
            @csrf
            @include('admin.delivery-zones._form')
        </form>
    </div>
@endsection