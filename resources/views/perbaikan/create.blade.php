@extends('layouts.app')

@section('title', 'Tambah Laporan Perbaikan')
@section('page-title', 'Tambah Laporan Perbaikan')
@section('page-subtitle', 'Catat lokasi, waktu, dan foto perbaikan')

@section('content')
<div class="card p-6 max-w-3xl">
    <form method="POST" action="{{ route('perbaikan.store') }}" enctype="multipart/form-data">
        @csrf
        
        @include('perbaikan._form')
        <div class="flex gap-3 mt-6">
            <button type="submit" class="btn-primary">Simpan</button>
            <a href="{{ route('perbaikan.index') }}" class="btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
