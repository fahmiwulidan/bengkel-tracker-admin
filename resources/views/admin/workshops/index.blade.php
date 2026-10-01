@extends('admin.layout')

@section('title', 'Daftar Bengkel')

@section('actions')
    <button type="button" onclick="openModal('modal-create')" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> Tambah Bengkel
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
            <i class="fa-solid fa-store" style="margin-right: 6px; color: var(--accent-primary);"></i>
            Manajemen data bengkel, rating, dan titik lokasi peta.
        </p>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>Rating</th>
                    <th>Status</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                    <th style="text-align: right;"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($workshops as $workshop)
                    <tr>
                        <td style="font-weight: 600; color: var(--text-main);">{{ $workshop->name }}</td>
                        <td>{{ $workshop->address }}</td>
                        <td style="font-weight: 600;">
                            <i class="fa-solid fa-star" style="color: #f59e0b; margin-right: 4px;"></i>{{ $workshop->rating }}
                        </td>
                        <td>
                            @if($workshop->is_open)
                                <span class="badge badge-success">Buka</span>
                            @else
                                <span class="badge badge-danger">Tutup</span>
                            @endif
                        </td>
                        <td style="font-family: monospace; color: var(--text-muted);">{{ $workshop->latitude }}</td>
                        <td style="font-family: monospace; color: var(--text-muted);">{{ $workshop->longitude }}</td>
                        <td>
                            <div class="actions" style="justify-content: flex-end;">
                                <button type="button" onclick="openModal('modal-edit-{{ $workshop->id }}')" class="btn btn-secondary btn-sm" style="padding: 6px 12px;">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                </button>
                                <button type="button" onclick="openModal('modal-delete-{{ $workshop->id }}')" class="btn btn-danger btn-sm" style="padding: 6px 12px;">
                                    <i class="fa-solid fa-trash"></i> Hapus
                                </button>
                            </div>

                        <!-- Modal Edit -->
                        <div id="modal-edit-{{ $workshop->id }}" class="modal-overlay">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h2>Edit Bengkel</h2>
                                    <button type="button" class="modal-close" onclick="closeModal('modal-edit-{{ $workshop->id }}')">&times;</button>
                                </div>
                                <form action="{{ route('admin.workshops.update', $workshop->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="form-group">
                                        <label>Nama Bengkel</label>
                                        <input type="text" name="name" value="{{ $workshop->name }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Alamat</label>
                                        <textarea name="address" required>{{ $workshop->address }}</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Rating</label>
                                        <input type="number" step="0.1" min="0" max="5" name="rating" value="{{ $workshop->rating }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select name="is_open" required>
                                            <option value="1" {{ $workshop->is_open ? 'selected' : '' }}>Buka</option>
                                            <option value="0" {{ !$workshop->is_open ? 'selected' : '' }}>Tutup</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Latitude</label>
                                        <input type="text" name="latitude" value="{{ $workshop->latitude }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Longitude</label>
                                        <input type="text" name="longitude" value="{{ $workshop->longitude }}" required>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" onclick="closeModal('modal-edit-{{ $workshop->id }}')">Batal</button>
                                        <button type="submit" class="btn btn-primary">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Modal Delete -->
                        <div id="modal-delete-{{ $workshop->id }}" class="modal-overlay">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h2>Konfirmasi Hapus</h2>
                                    <button type="button" class="modal-close" onclick="closeModal('modal-delete-{{ $workshop->id }}')">&times;</button>
                                </div>
                                <p>Apakah Anda yakin ingin menghapus bengkel <strong>{{ $workshop->name }}</strong>?</p>
                                <div class="modal-footer">
                                    <form action="{{ route('admin.workshops.destroy', $workshop->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-secondary" onclick="closeModal('modal-delete-{{ $workshop->id }}')">Batal</button>
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

    <!-- Modal Create -->
    <div id="modal-create" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Tambah Bengkel</h2>
                <button type="button" class="modal-close" onclick="closeModal('modal-create')">&times;</button>
            </div>
            <form action="{{ route('admin.workshops.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Nama Bengkel</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Alamat</label>
                    <textarea name="address" required></textarea>
                </div>
                <div class="form-group">
                    <label>Rating</label>
                    <input type="number" step="0.1" min="0" max="5" name="rating" value="4.5" required>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="is_open" required>
                        <option value="1">Buka</option>
                        <option value="0">Tutup</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Latitude</label>
                    <input type="text" name="latitude" placeholder="-7.1132" required>
                </div>
                <div class="form-group">
                    <label>Longitude</label>
                    <input type="text" name="longitude" placeholder="112.4173" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modal-create')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
