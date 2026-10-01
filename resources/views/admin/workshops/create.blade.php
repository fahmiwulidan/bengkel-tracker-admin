@extends('admin.layout')

@section('content')
    <div class="card">
        <h1>Tambah Bengkel</h1>

        <form action="{{ route('admin.workshops.store') }}" method="POST">
            @csrf

            <label>Nama Bengkel</label>
            <input type="text" name="name" required>

            <label>Alamat</label>
            <textarea name="address" required></textarea>

            <label>Rating</label>
            <input type="number" step="0.1" min="0" max="5" name="rating" value="4.5" required>

            <label>Status</label>
            <select name="is_open" required>
                <option value="1">Buka</option>
                <option value="0">Tutup</option>
            </select>

            <label>Latitude</label>
            <input type="text" name="latitude" placeholder="-7.1132" required>

            <label>Longitude</label>
            <input type="text" name="longitude" placeholder="112.4173" required>

            <button class="btn">Simpan</button>
            <a href="{{ route('admin.workshops.index') }}" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
@endsection