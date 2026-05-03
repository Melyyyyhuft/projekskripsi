@extends('layouts.admin')
@section('title', 'Bank Soal (CBT)')

@section('content')
<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem;">
    <!-- Form Tambah Soal -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <div class="glass-card" style="align-self: start;">
            <h3 style="margin-bottom: 1.5rem; color: var(--primary);">Tambah Soal Baru</h3>
            
            @if(session('success'))
                <div style="background: #d1fae5; color: #059669; padding: 1rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; font-weight: 500;">
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div style="background: #fee2e2; color: #dc2626; padding: 1rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; font-weight: 500;">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('admin.bank_soal.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="tahun_ajaran">Tahun Ajaran</label>
                    <input type="text" name="tahun_ajaran" id="tahun_ajaran" class="form-control" required value="{{ $filterTahun ?? '2024/2025' }}">
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="teks_soal">Pertanyaan</label>
                    <textarea name="teks_soal" id="teks_soal" class="form-control" required rows="3"></textarea>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label" for="opsi_a">Opsi A</label>
                        <input type="text" name="opsi_a" id="opsi_a" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="opsi_b">Opsi B</label>
                        <input type="text" name="opsi_b" id="opsi_b" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="opsi_c">Opsi C</label>
                        <input type="text" name="opsi_c" id="opsi_c" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="opsi_d">Opsi D</label>
                        <input type="text" name="opsi_d" id="opsi_d" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="jawaban_benar">Kunci Jawaban</label>
                    <select name="jawaban_benar" id="jawaban_benar" class="form-control" required>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                    </select>
                </div>
                
                <button type="submit" class="btn-primary" style="width: 100%;">Simpan Soal</button>
            </form>
        </div>

        <!-- Import Soal Massal -->
        <div class="glass-card" style="align-self: start;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="margin: 0; color: #d97706;">Import Soal Massal</h3>
                <a href="{{ route('admin.bank_soal.template') }}" style="font-size: 0.8rem; color: var(--primary); text-decoration: underline;">Unduh Template</a>
            </div>
            
            <form action="{{ route('admin.bank_soal.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="file_soal">Upload File (.csv / .txt)</label>
                    <input type="file" name="file_soal" id="file_soal" class="form-control" accept=".csv,.txt" required>
                    <small style="color: var(--gray-text); display: block; margin-top: 0.5rem;">Peringatan: File harus berisi minimal 50 soal yang valid sesuai template.</small>
                </div>
                <button type="submit" class="btn-primary" style="width: 100%; background: #d97706;">Upload & Proses</button>
            </form>
        </div>
    </div>

    <!-- Daftar Soal -->
    <div class="glass-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="margin: 0;">Daftar Soal</h3>
            
            <!-- Filter Tahun Ajaran -->
            <form action="{{ route('admin.bank_soal.index') }}" method="GET" style="display: flex; gap: 0.5rem;">
                <select name="tahun_ajaran" class="form-control" style="padding: 0.4rem 0.8rem; height: auto;" onchange="this.form.submit()">
                    @foreach($tahunAjarans as $ta)
                        <option value="{{ $ta }}" {{ $filterTahun == $ta ? 'selected' : '' }}>{{ $ta }}</option>
                    @endforeach
                    @if(!$tahunAjarans->contains($filterTahun))
                        <option value="{{ $filterTahun }}" selected>{{ $filterTahun }}</option>
                    @endif
                </select>
            </form>
        </div>

        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 50%;">Pertanyaan</th>
                        <th>Kunci</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($soals as $s)
                    <tr>
                        <td>
                            <strong>{{ \Illuminate\Support\Str::limit($s->teks_soal, 50) }}</strong><br>
                            <span style="font-size: 0.8rem; color: var(--gray-text);">
                                A: {{ $s->opsi_a }}, B: {{ $s->opsi_b }}, C: {{ $s->opsi_c }}, D: {{ $s->opsi_d }}
                            </span>
                        </td>
                        <td><span style="background: #e0f2fe; color: var(--primary); padding: 0.2rem 0.5rem; border-radius: 4px; font-weight: bold;">{{ $s->jawaban_benar }}</span></td>
                        <td>
                            <form action="{{ route('admin.bank_soal.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Hapus soal ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-primary" style="background: #ef4444; padding: 0.3rem 0.6rem; font-size: 0.8rem;">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" style="text-align: center; color: var(--gray-text); padding: 2rem;">Belum ada soal untuk tahun ajaran ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
