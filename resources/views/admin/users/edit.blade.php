@extends('admin.layout')

@section('content')
    <div class="card">
        <h1>Edit User</h1>

        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <label>Nama</label>
            <input type="text" name="name" value="{{ $user->name }}" required>

            <label>Email</label>
            <input type="email" name="email" value="{{ $user->email }}" required>

            <label>No HP</label>
            <input type="text" name="phone" value="{{ $user->phone }}">

            <label>Role</label>
            <select name="role" required>
                <option value="customer" {{ $user->role == 'customer' ? 'selected' : '' }}>
                    Customer
                </option>
                <option value="mechanic" {{ $user->role == 'mechanic' ? 'selected' : '' }}>
                    Mechanic
                </option>
                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>
                    Admin
                </option>
            </select>

            <label>Password Baru</label>
            <input type="password" name="password" placeholder="Kosongkan jika tidak diganti">

            <button class="btn">Update</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
@endsection