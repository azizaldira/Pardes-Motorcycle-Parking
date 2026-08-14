@extends('layouts.app')

@section('content')
<div style="padding: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2rem;">
        <div>
            <h1 class="page-title" style="margin-bottom: 0.5rem;">Manajemen Akun</h1>
            <p style="color: var(--text-muted);">Kelola akses dan keamanan akun sistem</p>
        </div>
        @if(auth()->user()->role === 'admin')
        <div>
            <button onclick="document.getElementById('createModal').style.display='flex'" class="btn btn-primary" style="padding: 0.6rem 1.2rem;">
                <i class="fas fa-plus" style="margin-right: 0.5rem;"></i> Tambah Akun
            </button>
        </div>
        @endif
    </div>

    @if(session('success'))
    <div style="background-color: #dcfce7; color: #166534; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-weight: 500;">
        <i class="fas fa-check-circle" style="margin-right: 0.5rem;"></i> {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div style="background-color: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-weight: 500;">
        <ul style="margin: 0; padding-left: 1.5rem;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if(auth()->user()->role === 'admin')
    <!-- TAMPILAN ADMIN: Tabel Seluruh Pengguna -->
    <div class="table-card" style="padding: 1.5rem;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama Pengguna</th>
                        <th>Email</th>
                        <th>Role / Jabatan</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $u)
                    <tr>
                        <td style="font-weight: 500;">
                            {{ $u->name }}
                            @if($u->id === auth()->id())
                            <span style="background-color: #e0e7ff; color: #4338ca; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.75rem; margin-left: 0.5rem;">Anda</span>
                            @endif
                        </td>
                        <td style="color: var(--text-muted);">{{ $u->email }}</td>
                        <td>
                            @if($u->role === 'admin')
                                <span style="background-color: #fef3c7; color: #92400e; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;">Admin</span>
                            @else
                                <span style="background-color: #dbeafe; color: #1e40af; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;">Staff</span>
                            @endif

                            @if(!$u->is_active)
                                <span style="background-color: #fee2e2; color: #991b1b; padding: 0.25rem 0.65rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; margin-left: 0.3rem;"><i class="fas fa-ban" style="margin-right: 0.2rem;"></i>Nonaktif</span>
                            @endif
                        </td>
                        <td style="text-align: right; white-space: nowrap;">
                            <button onclick="openEditModal({{ $u->id }}, '{{ $u->name }}', '{{ $u->email }}', '{{ $u->role }}')" class="btn" style="background-color: #f3f4f6; color: #374151; border: 1px solid #d1d5db; padding: 0.4rem 0.8rem; font-size: 0.875rem;">
                                <i class="fas fa-edit"></i> Edit
                            </button>

                            @if($u->role !== 'admin')
                            <form action="{{ route('users.toggle-status', $u->id) }}" method="POST" style="display: inline-block; margin-left: 0.5rem;" onsubmit="event.preventDefault(); showConfirmModal('{{ $u->is_active ? 'Apakah Anda yakin ingin menonaktifkan akun staff ini? Staff tidak akan dapat login kembali hingga diaktifkan.' : 'Aktifkan kembali akun staff ini agar dapat login ke sistem?' }}', () => this.submit(), '{{ $u->is_active ? 'Nonaktifkan' : 'Aktifkan' }}', '{{ $u->is_active ? 'Konfirmasi Penonaktifan' : 'Konfirmasi Pengaktifan' }}', '{{ $u->is_active ? '#ea580c' : '#10b981' }}');">
                                @csrf
                                @method('PATCH')
                                @if($u->is_active)
                                <button type="submit" class="btn" style="background-color: #fff7ed; color: #ea580c; border: 1px solid #fed7aa; padding: 0.4rem 0.8rem; font-size: 0.875rem;" title="Nonaktifkan Akun">
                                    <i class="fas fa-user-slash"></i> Nonaktifkan
                                </button>
                                @else
                                <button type="submit" class="btn" style="background-color: #ecfdf5; color: #10b981; border: 1px solid #a7f3d0; padding: 0.4rem 0.8rem; font-size: 0.875rem;" title="Aktifkan Akun">
                                    <i class="fas fa-user-check"></i> Aktifkan
                                </button>
                                @endif
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL CREATE -->
    <div id="createModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background-color: white; padding: 2rem; border-radius: 12px; width: 100%; max-width: 500px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="font-size: 1.25rem; font-weight: 600;">Tambah Akun Baru</h3>
                <i class="fas fa-times" style="cursor: pointer; color: var(--text-muted);" onclick="document.getElementById('createModal').style.display='none'"></i>
            </div>
            
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div style="margin-bottom: 1rem;">
                    <label class="form-label">Nama Lengkap <span style="color: #dc2626;">*</span></label>
                    <input type="text" name="name" class="form-input" style="width: 100%;" required>
                </div>
                <div style="margin-bottom: 1rem;">
                    <label class="form-label">Email <span style="color: #dc2626;">*</span></label>
                    <input type="email" name="email" class="form-input" style="width: 100%;" required>
                </div>
                <div style="margin-bottom: 1rem;">
                    <label class="form-label">Password <span style="color: #dc2626;">*</span></label>
                    <input type="password" name="password" class="form-input" style="width: 100%;" required minlength="6">
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label class="form-label">Role <span style="color: #dc2626;">*</span></label>
                    <select name="role" class="form-input" style="width: 100%;" required>
                        <option value="staff">Staff</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 1rem;">
                    <button type="button" class="btn" onclick="document.getElementById('createModal').style.display='none'" style="background-color: #f3f4f6;">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Akun</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT ADMIN -->
    <div id="editModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background-color: white; padding: 2rem; border-radius: 12px; width: 100%; max-width: 500px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="font-size: 1.25rem; font-weight: 600;">Edit Akun Pengguna</h3>
                <i class="fas fa-times" style="cursor: pointer; color: var(--text-muted);" onclick="document.getElementById('editModal').style.display='none'"></i>
            </div>
            
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div style="margin-bottom: 1rem;">
                    <label class="form-label">Nama Lengkap <span style="color: #dc2626;">*</span></label>
                    <input type="text" name="name" id="editName" class="form-input" style="width: 100%;" required>
                </div>
                <div style="margin-bottom: 1rem;">
                    <label class="form-label">Email <span style="color: #dc2626;">*</span></label>
                    <input type="email" name="email" id="editEmail" class="form-input" style="width: 100%;" required>
                </div>
                <div style="margin-bottom: 1rem;">
                    <label class="form-label">Password <span style="color: var(--text-muted); font-size: 0.75rem; font-weight: 400;">(Kosongkan jika tidak diubah)</span></label>
                    <input type="password" name="password" class="form-input" style="width: 100%;">
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label class="form-label">Role <span style="color: #dc2626;">*</span></label>
                    <select name="role" id="editRole" class="form-input" style="width: 100%;" required>
                        <option value="staff">Staff</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 1rem;">
                    <button type="button" class="btn" onclick="document.getElementById('editModal').style.display='none'" style="background-color: #f3f4f6;">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const authId = {{ auth()->id() }};
        function openEditModal(id, name, email, role) {
            document.getElementById('editForm').action = '/users/' + id;
            document.getElementById('editName').value = name;
            document.getElementById('editEmail').value = email;
            
            const roleSelect = document.getElementById('editRole');
            roleSelect.value = role;

            if (id === authId) {
                // Kunci form role jika edit diri sendiri
                roleSelect.disabled = true;
                // Add hidden input so role isn't missing in payload if someone removes disabled tag, 
                // but actually backend will ignore it anyway.
            } else {
                roleSelect.disabled = false;
            }

            document.getElementById('editModal').style.display = 'flex';
        }
    </script>

    @else
    <!-- TAMPILAN STAFF: Form Profil Saya Saja -->
    <div style="max-width: 600px; margin: 0 auto;">
        <div class="table-card" style="padding: 2rem; background-color: white;">
            <div style="text-align: center; margin-bottom: 2rem;">
                <div style="width: 80px; height: 80px; background-color: #eff6ff; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto;">
                    <i class="fas fa-user" style="font-size: 2.5rem; color: #3b82f6;"></i>
                </div>
                <h2 style="font-size: 1.5rem; font-weight: 600; color: var(--text-main);">Profil Anda</h2>
                <p style="color: var(--text-muted); font-size: 0.875rem;">Perbarui informasi akun dan kata sandi Anda</p>
            </div>

            <form action="{{ route('users.update', auth()->id()) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div style="margin-bottom: 1.5rem;">
                    <label class="form-label">Nama Lengkap <span style="color: #dc2626;">*</span></label>
                    <input type="text" name="name" value="{{ auth()->user()->name }}" class="form-input" style="width: 100%;" required>
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <label class="form-label">Email <span style="color: #dc2626;">*</span></label>
                    <input type="email" name="email" value="{{ auth()->user()->email }}" class="form-input" style="width: 100%;" required>
                </div>
                
                <div style="margin-bottom: 2rem;">
                    <label class="form-label" style="display: flex; justify-content: space-between;">
                        <span>Password Baru</span>
                        <span style="color: var(--text-muted); font-size: 0.75rem; font-weight: 400;">Biarkan kosong jika tidak ingin mengubah</span>
                    </label>
                    <input type="password" name="password" class="form-input" style="width: 100%;" placeholder="••••••••">
                </div>
                
                <div style="margin-bottom: 2rem;">
                    <label class="form-label">Jabatan (Role)</label>
                    <input type="text" value="{{ ucfirst(auth()->user()->role) }}" class="form-input" style="width: 100%; background-color: #f3f4f6; color: #9ca3af; cursor: not-allowed;" disabled>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem; font-size: 1rem;">
                    Simpan Perubahan
                </button>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection
