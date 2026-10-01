@extends('admin.layout')

@section('title', 'Daftar Mekanik')

@section('actions')
    <button type="button" onclick="openModal('modal-create')" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> Tambah Mekanik
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
        .badge-success { background: rgba(16, 185, 129, 0.1); color: #059669; }
        .badge-danger { background: rgba(239, 68, 68, 0.1); color: #dc2626; }
    </style>

        <div style="margin-bottom: 24px;">
            <p style="margin: 0; color: var(--text-muted); font-size: 15px;">
                <i class="fa-solid fa-wrench" style="margin-right: 6px; color: var(--accent-primary);"></i>
                Manajemen data mekanik dan posisi lokasinya.
            </p>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>No HP</th>
                        <th>Bengkel</th>
                        <th>Status</th>
                        <th>Latitude</th>
                        <th>Longitude</th>
                        <th style="text-align: right;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mechanics as $mechanic)
                        <tr>
                            <td style="font-weight: 600; color: var(--text-main);">{{ $mechanic->name }}</td>
                            <td>{{ $mechanic->email }}</td>
                            <td>{{ $mechanic->phone ?? '-' }}</td>
                            <td>{{ $mechanic->workshop_name ?? '-' }}</td>
                            <td>
                                @if(strtolower($mechanic->status) == 'open')
                                    <span class="badge badge-success">Open</span>
                                @else
                                    <span class="badge badge-danger">Close</span>
                                @endif
                            </td>
                            <td style="font-family: monospace; color: var(--text-muted);">{{ $mechanic->latitude }}</td>
                            <td style="font-family: monospace; color: var(--text-muted);">{{ $mechanic->longitude }}</td>
                            <td>
                                <div class="actions" style="justify-content: flex-end;">
                                    <button type="button" onclick="openModal('modal-edit-{{ $mechanic->id }}')" class="btn btn-secondary btn-sm" style="padding: 6px 12px;">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </button>
                                    <button type="button" onclick="openModal('modal-delete-{{ $mechanic->id }}')" class="btn btn-danger btn-sm" style="padding: 6px 12px;">
                                        <i class="fa-solid fa-trash"></i> Hapus
                                    </button>
                                </div>

                        <!-- Modal Edit -->
                        <div id="modal-edit-{{ $mechanic->id }}" class="modal-overlay">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h2>Edit Mekanik</h2>
                                    <button type="button" class="modal-close" onclick="closeModal('modal-edit-{{ $mechanic->id }}')">&times;</button>
                                </div>
                                <form action="{{ route('admin.mechanics.update', $mechanic->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="form-group">
                                        <label>Nama Mekanik</label>
                                        <input type="text" name="name" value="{{ $mechanic->name }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" name="email" value="{{ $mechanic->email }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label>No HP</label>
                                        <input type="text" name="phone" value="{{ $mechanic->phone }}">
                                    </div>
                                    <div class="form-group">
                                        <label>Password Baru</label>
                                        <input type="password" name="password" placeholder="Kosongkan jika tidak diganti">
                                    </div>
                                    <div class="form-group">
                                        <label>Bengkel</label>
                                        <select name="workshop_id" required>
                                            @foreach($workshops as $workshop)
                                                <option value="{{ $workshop->id }}" {{ $mechanic->workshop_id == $workshop->id ? 'selected' : '' }}>
                                                    {{ $workshop->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select name="status" class="form-control" required>
                                            <option value="open" {{ $mechanic->status == 'open' ? 'selected' : '' }}>Open</option>
                                            <option value="close" {{ $mechanic->status == 'close' ? 'selected' : '' }}>Close</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Latitude</label>
                                        <input type="text" name="latitude" value="{{ $mechanic->latitude }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Longitude</label>
                                        <input type="text" name="longitude" value="{{ $mechanic->longitude }}" required>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" onclick="closeModal('modal-edit-{{ $mechanic->id }}')">Batal</button>
                                        <button type="submit" class="btn btn-primary">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Modal Delete -->
                        <div id="modal-delete-{{ $mechanic->id }}" class="modal-overlay">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h2>Konfirmasi Hapus</h2>
                                    <button type="button" class="modal-close" onclick="closeModal('modal-delete-{{ $mechanic->id }}')">&times;</button>
                                </div>
                                <p>Apakah Anda yakin ingin menghapus mekanik <strong>{{ $mechanic->name }}</strong>?</p>
                                <div class="modal-footer">
                                    <form action="{{ route('admin.mechanics.destroy', $mechanic->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-secondary" onclick="closeModal('modal-delete-{{ $mechanic->id }}')">Batal</button>
                                        <button type="submit" class="btn btn-danger">Hapus</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    </div>
    </div>

    <!-- Modal Create -->
    <div id="modal-create" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Tambah Mekanik</h2>
                <button type="button" class="modal-close" onclick="closeModal('modal-create')">&times;</button>
            </div>
            <form action="{{ route('admin.mechanics.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Nama Mekanik</label>
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
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group">
                    <label>Bengkel</label>
                    <select name="workshop_id" required>
                        @foreach($workshops as $workshop)
                            <option value="{{ $workshop->id }}">{{ $workshop->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control" required>
                        <option value="open">Open</option>
                        <option value="close">Close</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Latitude</label>
                    <input type="text" name="latitude" placeholder="-7.1150" required>
                </div>
                <div class="form-group">
                    <label>Longitude</label>
                    <input type="text" name="longitude" placeholder="112.4190" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modal-create')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
