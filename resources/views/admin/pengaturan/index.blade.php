@extends('layouts.admin')
@section('title', 'Pengaturan Sistem')

@section('content')

@if (session('success'))
    <div style="background-color: #d4edda; color: #155724; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid #c3e6cb;">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div style="background-color: #f8d7da; color: #721c24; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid #f5c6cb;">
        {{ session('error') }}
    </div>
@endif

@if ($errors->any())
    <div style="background-color: #f8d7da; color: #721c24; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid #f5c6cb;">
        <ul style="margin: 0; padding-left: 1.5rem;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<style>
    .tabs-container { margin-bottom: 2rem; }
    .tab-buttons { display: flex; gap: 0.5rem; border-bottom: 2px solid #e2e8f0; margin-bottom: 1.5rem; }
    .tab-btn { padding: 0.75rem 1.5rem; background: none; border: none; font-weight: 600; color: var(--gray-text); cursor: pointer; border-bottom: 2px solid transparent; margin-bottom: -2px; }
    .tab-btn.active { color: var(--primary); border-bottom-color: var(--primary); }
    .tab-btn:hover:not(.active) { color: var(--dark); border-bottom-color: var(--gray-light); }
    .tab-pane { display: none; }
    .tab-pane.active { display: block; animation: fadeIn 0.3s ease; }
</style>

<div class="glass-card tabs-container">
    <div class="tab-buttons">
        <button class="tab-btn active" onclick="openTab(event, 'tab-umum')">Umum</button>
        <button class="tab-btn" onclick="openTab(event, 'tab-periode')">Periode & Bobot Seleksi</button>
        <button class="tab-btn" onclick="openTab(event, 'tab-jurusan')">Kuota Jurusan</button>
    </div>

    <!-- Tab Umum -->
    <div id="tab-umum" class="tab-pane active">
        <form action="{{ route('admin.pengaturan.umum') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">Nama Sekolah</label>
                    <input type="text" name="nama_sekolah" class="form-control" value="{{ $settings['nama_sekolah'] ?? 'SMK Negeri 1 Default' }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Tahun Ajaran Aktif</label>
                    <input type="text" name="tahun_ajaran" class="form-control" value="{{ $settings['tahun_ajaran'] ?? '2026/2027' }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Status PPDB</label>
                    <select name="status_ppdb" class="form-control" required>
                        <option value="buka" {{ ($settings['status_ppdb'] ?? '') == 'buka' ? 'selected' : '' }}>Buka</option>
                        <option value="tutup" {{ ($settings['status_ppdb'] ?? '') == 'tutup' ? 'selected' : '' }}>Tutup</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Logo Sekolah (Opsional, Biarkan kosong jika tidak diubah)</label>
                    <input type="file" name="logo_sekolah" class="form-control" accept=".jpg,.jpeg,.png">
                    @if(isset($settings['logo_sekolah']))
                        <div style="margin-top: 0.5rem;">
                            <img src="{{ asset('storage/' . $settings['logo_sekolah']) }}" alt="Logo" style="height: 50px;">
                        </div>
                    @endif
                </div>
            </div>
            <button type="submit" class="btn-primary">Simpan Pengaturan Umum</button>
        </form>
    </div>

    <!-- Tab Periode & Bobot -->
    <div id="tab-periode" class="tab-pane">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
            <!-- Form Periode -->
            <div>
                <h4 style="margin-top: 0; margin-bottom: 1rem; color: var(--dark);">Periode Pendaftaran</h4>
                <form action="{{ route('admin.pengaturan.periode') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Tanggal Buka Pendaftaran</label>
                        <input type="date" name="tgl_buka" class="form-control" value="{{ $settings['tgl_buka'] ?? '' }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal Tutup Pendaftaran</label>
                        <input type="date" name="tgl_tutup" class="form-control" value="{{ $settings['tgl_tutup'] ?? '' }}" required>
                        <small style="color: var(--gray-text);">Sistem akan otomatis menutup PPDB jika melewati tanggal ini.</small>
                    </div>
                    <button type="submit" class="btn-primary">Simpan Periode</button>
                </form>
            </div>

            <!-- Form Bobot Seleksi -->
            <div>
                <h4 style="margin-top: 0; margin-bottom: 1rem; color: var(--dark);">Bobot Nilai Seleksi (%)</h4>
                <form action="{{ route('admin.pengaturan.bobot') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Bobot Ujian Online (%)</label>
                        <input type="number" name="bobot_ujian" class="form-control" value="{{ $settings['bobot_ujian'] ?? 60 }}" required min="0" max="100">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Bobot Nilai Rapor (%)</label>
                        <input type="number" name="bobot_rapor" class="form-control" value="{{ $settings['bobot_rapor'] ?? 40 }}" required min="0" max="100">
                        <small style="color: var(--gray-text);">Total bobot harus 100%.</small>
                    </div>
                    <button type="submit" class="btn-primary">Simpan Bobot</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Tab Jurusan -->
    <div id="tab-jurusan" class="tab-pane">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h4 style="margin: 0; color: var(--dark);">Manajemen Kuota Jurusan</h4>
            <button type="button" class="btn-primary" onclick="showAddJurusanForm()">+ Tambah Jurusan</button>
        </div>

        <!-- Form Tambah (Hidden by default) -->
        <div id="form-tambah-jurusan" style="display: none; background: #f8fafc; padding: 1.5rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; border: 1px solid #e2e8f0;">
            <form action="{{ route('admin.jurusan-setting.store') }}" method="POST">
                @csrf
                <div style="display: flex; gap: 1rem; align-items: flex-end;">
                    <div class="form-group" style="margin-bottom: 0; flex: 2;">
                        <label class="form-label">Nama Jurusan</label>
                        <input type="text" name="nama" class="form-control" placeholder="Contoh: Rekayasa Perangkat Lunak" required>
                    </div>
                    <div class="form-group" style="margin-bottom: 0; flex: 1;">
                        <label class="form-label">Kuota</label>
                        <input type="number" name="kuota" class="form-control" placeholder="Contoh: 100" required min="1">
                    </div>
                    <button type="submit" class="btn-primary">Simpan</button>
                    <button type="button" class="btn-outline" onclick="hideAddJurusanForm()">Batal</button>
                </div>
            </form>
        </div>

        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Jurusan</th>
                        <th style="text-align: center;">Kuota Total</th>
                        <th style="text-align: center;">Diterima</th>
                        <th style="text-align: center;">Sisa Kuota</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jurusans as $j)
                    <tr>
                        <td>{{ $j->id }}</td>
                        <td>
                            <!-- Edit Form Inline -->
                            <form action="{{ route('admin.jurusan-setting.update', $j->id) }}" method="POST" id="edit-form-{{ $j->id }}" style="display: none;">
                                @csrf
                                @method('PUT')
                                <div style="display: flex; gap: 0.5rem;">
                                    <input type="text" name="nama" value="{{ $j->nama }}" class="form-control" style="padding: 0.5rem;" required>
                                    <input type="number" name="kuota" value="{{ $j->kuota }}" class="form-control" style="padding: 0.5rem; width: 80px;" required>
                                    <button type="submit" style="background: #10b981; color: white; padding: 0.5rem 1rem; border-radius: 4px; font-weight: bold; font-size: 0.8rem;">Simpan</button>
                                    <button type="button" onclick="toggleEdit({{ $j->id }})" style="background: #94a3b8; color: white; padding: 0.5rem 1rem; border-radius: 4px; font-weight: bold; font-size: 0.8rem;">Batal</button>
                                </div>
                            </form>
                            <span id="display-nama-{{ $j->id }}"><strong>{{ $j->nama }}</strong></span>
                        </td>
                        <td style="text-align: center;"><span id="display-kuota-{{ $j->id }}">{{ $j->kuota }}</span></td>
                        <td style="text-align: center;">
                            <span style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 0.25rem 0.75rem; border-radius: 999px; font-weight: 600;">
                                {{ $j->diterima_count }}
                            </span>
                        </td>
                        <td style="text-align: center;">
                            @if($j->sisa_kuota <= 0)
                                <span style="color: #ef4444; font-weight: 700;">Penuh (0)</span>
                            @else
                                <span style="color: var(--primary); font-weight: 700;">{{ $j->sisa_kuota }}</span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <button type="button" onclick="toggleEdit({{ $j->id }})" style="color: var(--primary); margin-right: 0.5rem; text-decoration: underline;">Edit</button>
                            <form action="{{ route('admin.jurusan-setting.destroy', $j->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Yakin ingin menghapus jurusan ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="color: #ef4444; text-decoration: underline;">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function openTab(evt, tabName) {
        var i, tabContent, tabBtns;
        tabContent = document.getElementsByClassName("tab-pane");
        for (i = 0; i < tabContent.length; i++) {
            tabContent[i].style.display = "none";
            tabContent[i].classList.remove("active");
        }
        tabBtns = document.getElementsByClassName("tab-btn");
        for (i = 0; i < tabBtns.length; i++) {
            tabBtns[i].className = tabBtns[i].className.replace(" active", "");
        }
        document.getElementById(tabName).style.display = "block";
        document.getElementById(tabName).classList.add("active");
        evt.currentTarget.className += " active";
    }

    function showAddJurusanForm() {
        document.getElementById('form-tambah-jurusan').style.display = 'block';
    }

    function hideAddJurusanForm() {
        document.getElementById('form-tambah-jurusan').style.display = 'none';
    }

    function toggleEdit(id) {
        var form = document.getElementById('edit-form-' + id);
        var dispNama = document.getElementById('display-nama-' + id);
        var dispKuota = document.getElementById('display-kuota-' + id);
        
        if (form.style.display === 'none') {
            form.style.display = 'block';
            dispNama.style.display = 'none';
            dispKuota.style.display = 'none';
        } else {
            form.style.display = 'none';
            dispNama.style.display = 'inline';
            dispKuota.style.display = 'inline';
        }
    }
</script>

@endsection
