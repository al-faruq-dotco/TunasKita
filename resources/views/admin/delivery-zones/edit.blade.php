@extends('admin.layouts.app')

@section('title', 'Edit Zona')
@section('heading', 'Edit Zona Pengiriman')

@section('content')
    <div class="card max-w-3xl p-6">
        <form method="POST" action="{{ route('admin.delivery-zones.update', $zone->id) }}">
            @csrf
            @method('PUT')
            @include('admin.delivery-zones._form')
        </form>
    </div>
@endsection