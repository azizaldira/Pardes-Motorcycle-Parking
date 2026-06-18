@extends('layouts.app')

@section('content')
<div style="padding: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2rem;">
        <div>
            <h1 class="page-title" style="margin-bottom: 0.5rem;">Pengaturan Bisnis</h1>
            <p style="color: var(--text-muted);">Kelola identitas utama dari sistem parkir ini</p>
        </div>
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

    <div class="table-card" style="padding: 2rem; max-width: 800px;">
        <form action="{{ route('pengaturan-bisnis.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div style="margin-bottom: 2rem;">
                <label class="form-label" style="font-size: 1rem; color: var(--text-main); font-weight: 600;">Nama Usaha <span style="color: #dc2626;">*</span></label>
                <p style="color: var(--text-muted); font-size: 0.875rem; margin-bottom: 0.5rem;">Akan ditampilkan pada Kop Surat Laporan PDF dan Dashboard.</p>
                <input type="text" name="business_name" value="{{ old('business_name', $setting->business_name ?? '') }}" class="form-input" style="width: 100%; font-size: 1rem;" required maxlength="100" placeholder="Misal: Penitipan Motor Parde's">
            </div>

            <div style="margin-bottom: 2.5rem;">
                <label class="form-label" style="font-size: 1rem; color: var(--text-main); font-weight: 600;">Logo Bisnis</label>
                <p style="color: var(--text-muted); font-size: 0.875rem; margin-bottom: 1rem;">Format yang diizinkan: JPG, JPEG, PNG, WebP. Ukuran Maksimal: 2 MB.</p>
                
                <div style="display: flex; gap: 2rem; align-items: flex-start; flex-wrap: wrap;">
                    
                    <!-- Preview Area -->
                    <div style="background-color: #f9fafb; border: 2px dashed #d1d5db; border-radius: 12px; width: 200px; height: 200px; display: flex; align-items: center; justify-content: center; flex-direction: column; overflow: hidden; position: relative;" id="logoPreviewContainer">
                        @if($setting && $setting->business_logo)
                            <img src="{{ asset('storage/' . $setting->business_logo) }}" alt="Logo Bisnis" style="max-width: 100%; max-height: 100%; object-fit: contain;" id="currentLogo">
                        @else
                            <i class="fas fa-motorcycle" style="font-size: 4rem; color: #9ca3af; margin-bottom: 1rem;" id="defaultIcon"></i>
                            <span style="color: #6b7280; font-size: 0.875rem; font-weight: 500;" id="defaultText">Belum ada logo</span>
                        @endif
                    </div>

                    <!-- Upload Controls -->
                    <div style="flex: 1;">
                        <input type="file" name="business_logo" id="business_logo" accept="image/jpeg, image/png, image/jpg, image/webp" style="display: none;" onchange="previewImage(this)">
                        
                        <label for="business_logo" class="btn btn-primary" style="display: inline-block; cursor: pointer; margin-bottom: 1rem;">
                            <i class="fas fa-upload" style="margin-right: 0.5rem;"></i> Unggah Gambar Baru
                        </label>
                        <br>

                        @if($setting && $setting->business_logo)
                        <!-- Tombol Hapus Berdiri Terpisah dengan Form agar tak tersubmit campur -->
                        <button type="button" class="btn" style="background-color: #fee2e2; color: #dc2626; border: 1px solid #fca5a5; padding: 0.5rem 1rem;" onclick="if(confirm('Yakin ingin menghapus logo permanen?')) document.getElementById('deleteLogoForm').submit();">
                            <i class="fas fa-trash-alt" style="margin-right: 0.5rem;"></i> Hapus Logo Saat Ini
                        </button>
                        @endif

                        <div id="fileInfo" style="margin-top: 1rem; color: var(--text-muted); font-size: 0.875rem;"></div>
                    </div>
                </div>
            </div>

            <div style="border-top: 1px solid var(--border-color); padding-top: 1.5rem; display: flex; justify-content: flex-end;">
                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem; font-size: 1rem;">
                    <i class="fas fa-save" style="margin-right: 0.5rem;"></i> Simpan Pengaturan
                </button>
            </div>
        </form>
        
        <!-- Hidden Form for Deletion -->
        @if($setting && $setting->business_logo)
        <form id="deleteLogoForm" action="{{ route('pengaturan-bisnis.logo.destroy') }}" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>
        @endif
    </div>
</div>

<script>
    function previewImage(input) {
        const previewContainer = document.getElementById('logoPreviewContainer');
        const fileInfo = document.getElementById('fileInfo');
        
        if (input.files && input.files[0]) {
            const file = input.files[0];
            
            // Cek ukuran
            if(file.size > 2048 * 1024) {
                alert("Ukuran file melebihi 2 MB!");
                input.value = "";
                return;
            }

            const reader = new FileReader();
            
            reader.onload = function(e) {
                previewContainer.innerHTML = `<img src="${e.target.result}" style="max-width: 100%; max-height: 100%; object-fit: contain; border-radius: 8px;">`;
                fileInfo.innerHTML = `<span style="color: #16a34a;"><i class="fas fa-check-circle"></i> File terpilih: ${file.name}</span>`;
            }
            
            reader.readAsDataURL(file);
        }
    }
</script>
@endsection
