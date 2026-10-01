@extends('admin.layout')

@section('content')
    <div class="card">
        <h1>Tambah Mekanik</h1>

        <form action="{{ route('admin.mechanics.store') }}" method="POST">
            @csrf

            <label>Nama Mekanik</label>
            <input type="text" name="name" required>

            <label>Email</label>
            <input type="email" name="email" required>

            <label>No HP</label>
            <input type="text" name="phone" placeholder="081234567890">

            <label>Password</label>
            <input type="password" name="password" required>

            <label>Bengkel</label>
            <select name="workshop_id" required>
                @foreach($workshops as $workshop)
                    <option value="{{ $workshop->id }}">{{ $workshop->name }}</option>
                @endforeach
            </select>
            <label>Status</label>
            <select name="status" class="form-control" required>
                <option value="open">Open</option>
                <option value="close">Close</option>
            </select>
            <label>Latitude</label>
            <input type="text" name="latitude" placeholder="-7.1150" required>

            <label>Longitude</label>
            <input type="text" name="longitude" placeholder="112.4190" required>

            <button class="btn">Simpan</button>
            <a href="{{ route('admin.mechanics.index') }}" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
@endsection