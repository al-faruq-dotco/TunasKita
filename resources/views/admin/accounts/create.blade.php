@extends('admin.layouts.app')

@section('title', 'Tambah ' . $label)
@section('heading', 'Tambah ' . $label)

@section('content')
    <div class="card max-w-3xl p-6">
        <form method="POST" action="{{ route($routeBase . '.store') }}">
            @csrf
            @include('admin.accounts._form')
        </form>
    </div>
@endsection