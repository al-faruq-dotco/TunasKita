@extends('admin.layouts.app')

@section('title', 'Edit ' . $label)
@section('heading', 'Edit ' . $label)

@section('content')
    <div class="card max-w-3xl p-6">
        <form method="POST" action="{{ route($routeBase . '.update', $account->id) }}">
            @csrf
            @method('PUT')
            @include('admin.accounts._form')
        </form>
    </div>
@endsection