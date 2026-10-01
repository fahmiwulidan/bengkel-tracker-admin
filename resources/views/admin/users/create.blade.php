@extends('admin.layout')

@section('content')
    <div class="card">
        <h1>Tambah User</h1>

        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf

            <label>Nama</label>
            <input type="text" name="name" required>

            <label>Email</label>
            <input type="email" name="email" required>

            <label>No HP</label>
            <input type="text" name="phone" placeholder="081234567890">

            <label>Role</label>
            <select name="role" required>
                <option value="customer">Customer</option>
                <option value="mechanic">Mechanic</option>
                <option value="admin">Admin</option>
            </select>

            <label>Password</label>
            <input type="password" name="password" required>

            <button class="btn">Simpan</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
@endsection