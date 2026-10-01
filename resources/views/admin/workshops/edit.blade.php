@extends('admin.layout')

@section('content')
    <div class="card">
        <h1>Edit Bengkel</h1>

        <form action="{{ route('admin.workshops.update', $workshop->id) }}" method="POST">
            @csrf
            @method('PUT')

            <label>Nama Bengkel</label>
            <input type="text" name="name" value="{{ $workshop->name }}" required>

            <label>Alamat</label>
            <textarea name="address" required>{{ $workshop->address }}</textarea>

            <label>Rating</label>
            <input type="number" step="0.1" min="0" max="5" name="rating" value="{{ $workshop->rating }}" required>

            <label>Status</label>
            <select name="is_open" required>
                <option value="1" {{ $workshop->is_open ? 'selected' : '' }}>Buka</option>
                <option value="0" {{ !$workshop->is_open ? 'selected' : '' }}>Tutup</option>
            </select>

            <label>Latitude</label>
            <input type="text" name="latitude" value="{{ $workshop->latitude }}" required>

            <label>Longitude</label>
            <input type="text" name="longitude" value="{{ $workshop->longitude }}" required>

            <button class="btn">Update</button>
            <a href="{{ route('admin.workshops.index') }}" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
@endsection