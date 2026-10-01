@extends('admin.layout')

@section('title', 'Daftar User')

@section('actions')
    <button type="button" onclick="openModal('modal-create')" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> Tambah User
    </button>
@endsection

@section('content')
    <style>
        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .badge-info { background: rgba(59, 130, 246, 0.1); color: #2563eb; }
        .badge-success { background: rgba(16, 185, 129, 0.1); color: #059669; }
        .badge-primary { background: rgba(255, 107, 0, 0.1); color: var(--accent-primary); }
    </style>

        <div style="margin-bottom: 24px;">
            <p style="margin: 0; color: var(--text-muted); font-size: 15px;">
                <i class="fa-solid fa-users" style="margin-right: 6px; color: var(--accent-primary);"></i>
                Manajemen akun Customer, Mekanik, dan Administrator.
            </p>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>No HP</th>
                        <th>Role</th>
                        <th>Dibuat</th>
                        <th style="text-align: right;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td style="font-weight: 600; color: var(--text-main);">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone ?? '-' }}</td>
                            <td>
                                @if($user->role == 'admin')
                                    <span class="badge badge-success">Admin</span>
                                @elseif($user->role == 'mechanic')
                                    <span class="badge badge-primary">Mechanic</span>
                                @else
                                    <span class="badge badge-info">Customer</span>
                                @endif
                            </td>
                            <td style="color: var(--text-muted); font-size: 14px;">{{ \Carbon\Carbon::parse($user->created_at)->format('d M Y, H:i') }}</td>
                            <td>
                                <div class="actions" style="justify-content: flex-end;">
                                    <button type="button" onclick="openModal('modal-edit-{{ $user->id }}')" class="btn btn-secondary btn-sm" style="padding: 6px 12px;">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </button>
                                    <button type="button" onclick="openModal('modal-delete-{{ $user->id }}')" class="btn btn-danger btn-sm" style="padding: 6px 12px;">
                                        <i class="fa-solid fa-trash"></i> Hapus
                                    </button>
                                </div>

                        <!-- Modal Edit -->
                        <div id="modal-edit-{{ $user->id }}" class="modal-overlay">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h2>Edit User</h2>
                                    <button type="button" class="modal-close" onclick="closeModal('modal-edit-{{ $user->id }}')">&times;</button>
                                </div>
                                <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="form-group">
                                        <label>Nama</label>
                                        <input type="text" name="name" value="{{ $user->name }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" name="email" value="{{ $user->email }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label>No HP</label>
                                        <input type="text" name="phone" value="{{ $user->phone }}">
                                    </div>
                                    <div class="form-group">
                                        <label>Role</label>
                                        <select name="role" required>
                                            <option value="customer" {{ $user->role == 'customer' ? 'selected' : '' }}>Customer</option>
                                            <option value="mechanic" {{ $user->role == 'mechanic' ? 'selected' : '' }}>Mechanic</option>
                                            <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Password Baru</label>
                                        <input type="password" name="password" placeholder="Kosongkan jika tidak diganti">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" onclick="closeModal('modal-edit-{{ $user->id }}')">Batal</button>
                                        <button type="submit" class="btn btn-primary">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Modal Delete -->
                        <div id="modal-delete-{{ $user->id }}" class="modal-overlay">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h2>Konfirmasi Hapus</h2>
                                    <button type="button" class="modal-close" onclick="closeModal('modal-delete-{{ $user->id }}')">&times;</button>
                                </div>
                                <p>Apakah Anda yakin ingin menghapus user <strong>{{ $user->name }}</strong>?</p>
                                <div class="modal-footer">
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-secondary" onclick="closeModal('modal-delete-{{ $user->id }}')">Batal</button>
                                        <button type="submit" class="btn btn-danger">Hapus</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 32px; color: var(--text-muted);">Belum ada data user.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
    </div>

    <!-- Modal Create -->
    <div id="modal-create" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Tambah User</h2>
                <button type="button" class="modal-close" onclick="closeModal('modal-create')">&times;</button>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>No HP</label>
                    <input type="text" name="phone" placeholder="081234567890">
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <select name="role" required>
                        <option value="customer">Customer</option>
                        <option value="mechanic">Mechanic</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modal-create')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
