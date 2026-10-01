@extends('admin.layout')

@section('content')
    <div class="card">
        <h1>Edit Mekanik</h1>

        <form action="{{ route('admin.mechanics.update', $mechanic->id) }}" method="POST">
            @csrf
            @method('PUT')

            <label>Nama Mekanik</label>
            <input type="text" name="name" value="{{ $mechanic->name }}" required>

            <label>Email</label>
            <input type="email" name="email" value="{{ $mechanic->email }}" required>

            <label>No HP</label>
            <input type="text" name="phone" value="{{ $mechanic->phone }}">

            <label>Password Baru</label>
            <input type="password" name="password" placeholder="Kosongkan jika tidak diganti">

            <label>Bengkel</label>
            <select name="workshop_id" required>
                @foreach($workshops as $workshop)
                    <option value="{{ $workshop->id }}" {{ $mechanic->workshop_id == $workshop->id ? 'selected' : '' }}>
                        {{ $workshop->name }}
                    </option>
                @endforeach
            </select>

            <label>Status</label>
            <select name="status" class="form-control" required>
                <option value="open" {{ $mechanic->status == 'open' ? 'selected' : '' }}>Open</option>
                <option value="close" {{ $mechanic->status == 'close' ? 'selected' : '' }}>Close</option>
            </select>

            <label>Latitude</label>
            <input type="text" name="latitude" value="{{ $mechanic->latitude }}" required>

            <label>Longitude</label>
            <input type="text" name="longitude" value="{{ $mechanic->longitude }}" required>

            <button class="btn">Update</button>
            <a href="{{ route('admin.mechanics.index') }}" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
@endsection