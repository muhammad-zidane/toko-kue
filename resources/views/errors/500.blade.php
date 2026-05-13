@extends('errors.layout')

@section('title', 'Terjadi Kesalahan')

@section('content')
    <div class="text-8xl font-display font-bold mb-4" style="color: var(--pink);">500</div>
    <h1 class="text-2xl font-bold mb-2" style="color: var(--brown-dark);">Terjadi Kesalahan Server</h1>
    <p class="text-gray-500">Maaf, terjadi kesalahan pada server kami. Tim kami sedang bekerja untuk memperbaikinya.</p>
@endsection
