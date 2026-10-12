@extends('layouts.app')

@section('title', 'Edit Laporan Perbaikan')
@section('page-title', 'Edit Laporan Perbaikan')
@section('page-subtitle', 'Catat lokasi, waktu, dan foto perbaikan')

@section('content')
<div class="card p-6 max-w-3xl">
    <form method="POST" action="{{ route('perbaikan.update', $perbaikan) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('perbaikan._form')
        <div class="flex gap-3 mt-6">
            <button type="submit" class="btn-primary">Simpan</button>
            <a href="{{ route('perbaikan.index') }}" class="btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
