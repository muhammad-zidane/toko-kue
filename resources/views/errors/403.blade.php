@extends('errors.layout')

@section('title', 'Akses Ditolak')

@section('content')
    <div class="text-8xl font-display font-bold mb-4" style="color: var(--pink);">403</div>
    <h1 class="text-2xl font-bold mb-2" style="color: var(--brown-dark);">Akses Ditolak</h1>
    <p class="text-gray-500">{{ $exception->getMessage() ?: 'Kamu tidak memiliki izin untuk mengakses halaman ini.' }}</p>
@endsection
