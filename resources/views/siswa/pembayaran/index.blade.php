@extends('layouts.siswa')
@section('title', 'Pembayaran Daftar Ulang')

@section('content')
<style>
    .du-wrapper {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        padding-bottom: 2.5rem;
        animation: slideUp 0.5s cubic-bezier(0.16, 1, 0.3, 1);
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Hero Accepted Banner */
    .du-hero {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border-radius: 24px;
        padding: 2.25rem 2rem;
        color: white;
        position: relative;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(16, 185, 129, 0.2);
    }
    .du-hero::after {
        content: "";
        position: absolute;
        top: -60px;
        right: -60px;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.12);
        filter: blur(20px);
    }
    .du-badge-diterima {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.35);
        padding: .35rem 1rem;
        border-radius: 999px;
        font-size: .75rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        margin-bottom: .85rem;
    }
    .du-hero-title {
        font-size: 1.85rem;
        font-weight: 900;
        margin: 0 0 .5rem;
        letter-spacing: -0.02em;
        line-height: 1.2;
    }
    .du-hero-desc {
        margin: 0;
        font-size: .95rem;
        opacity: .95;
        max-width: 680px;
        line-height: 1.6;
    }

    /* Grid Layout */
    .du-grid {
        display: grid;
        grid-template-columns: 1.2fr 1fr;
        gap: 1.5rem;
    }

    @media (max-width: 900px) {
        .du-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Detail Card */
    .du-card {
        background: white;
        border-radius: 20px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        padding: 1.75rem;
        display: flex;
        flex-direction: column;
    }
    .du-card-title {
        font-size: 1.1rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 1.25rem;
        display: flex;
        align-items: center;
        gap: .6rem;
    }

    .du-info-table {
        width: 100%;
        border-collapse: collapse;
    }
    .du-info-table tr {
        border-bottom: 1px solid #f8fafc;
    }
    .du-info-table tr:last-child {
        border-bottom: none;
    }
    .du-info-table td {
        padding: .85rem 0;
        font-size: .9rem;
    }
    .du-info-label {
        color: #64748b;
        font-weight: 600;
        width: 42%;
    }
    .du-info-value {
        color: #0f172a;
        font-weight: 700;
        text-align: right;
    }

    /* Payment Box */
    .du-payment-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        text-align: center;
    }
    .du-amount-label {
        font-size: .8rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: .05em;
        margin-bottom: .35rem;
    }
    .du-amount-val {
        font-size: 2rem;
        font-weight: 900;
        color: #0f172a;
        letter-spacing: -0.02em;
    }
    .du-amount-empty {
        font-size: 1.25rem;
        font-weight: 800;
        color: #d97706;
    }

    .du-status-pill {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .35rem .9rem;
        border-radius: 999px;
        font-size: .8rem;
        font-weight: 800;
        margin-top: .75rem;
    }
    .du-status-belum_bayar {
        background: #fee2e2;
        color: #dc2626;
        border: 1px solid #fca5a5;
    }
    .du-status-pending {
        background: #fef3c7;
        color: #d97706;
        border: 1px solid #fde68a;
    }
    .du-status-lunas {
        background: #d1fae5;
        color: #059669;
        border: 1px solid #a7f3d0;
    }
    .du-status-gagal {
        background: #f1f5f9;
        color: #64748b;
        border: 1px solid #e2e8f0;
    }

    .btn-pay-now {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .6rem;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        border: none;
        padding: 1rem 2rem;
        border-radius: 14px;
        font-size: 1rem;
        font-weight: 800;
        cursor: pointer;
        width: 100%;
        transition: all .25s ease;
        box-shadow: 0 6px 20px rgba(37, 99, 235, 0.25);
    }
    .btn-pay-now:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(37, 99, 235, 0.35);
        color: white;
    }

    .btn-pay-disabled {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .6rem;
        background: #e2e8f0;
        color: #94a3b8;
        border: none;
        padding: 1rem 2rem;
        border-radius: 14px;
        font-size: 1rem;
        font-weight: 700;
        cursor: not-allowed;
        width: 100%;
    }

    .btn-pay-success {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .6rem;
        background: #10b981;
        color: white;
        border: none;
        padding: 1rem 2rem;
        border-radius: 14px;
        font-size: 1rem;
        font-weight: 800;
        cursor: default;
        width: 100%;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.2);
    }

    .du-notice-box {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 14px;
        padding: 1.15rem;
        font-size: .85rem;
        color: #1e40af;
        line-height: 1.55;
    }
</style>

<div class="du-wrapper">

    {{-- Hero Banner: Status Diterima --}}
    <div class="du-hero">
        <div class="du-badge-diterima">
            <i class="fa-solid fa-circle-check"></i> Pengumuman Kelulusan
        </div>
        <h1 class="du-hero-title">Selamat! Anda Dinyatakan Diterima</h1>
        <p class="du-hero-desc">
            Silakan melakukan pembayaran daftar ulang untuk menyelesaikan proses penerimaan dan konfirmasi sebagai peserta didik baru di {{ $settings['nama_sekolah'] ?? 'SMK Mitra Bintaro' }}.
        </p>
    </div>

    {{-- Grid: Informasi Siswa & Tagihan Pembayaran --}}
    <div class="du-grid">

        {{-- Card 1: Data Calon Siswa --}}
        <div class="du-card">
            <h3 class="du-card-title">
                <i class="fa-solid fa-id-card-clip" style="color:#3b82f6;"></i> Informasi Calon Siswa
            </h3>

            <table class="du-info-table">
                <tr>
                    <td class="du-info-label">Nomor Pendaftaran</td>
                    <td class="du-info-value" style="color:#2563eb;">{{ $pendaftaran->nomor_pendaftaran ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="du-info-label">Nama Lengkap</td>
                    <td class="du-info-value">{{ $pendaftaran->user->name ?? (Auth::user()->name ?? '-') }}</td>
                </tr>
                <tr>
                    <td class="du-info-label">Program Keahlian</td>
                    <td class="du-info-value">{{ $pendaftaran->jurusan->nama ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="du-info-label">NISN</td>
                    <td class="du-info-value">{{ $pendaftaran->nisn ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="du-info-label">Asal Sekolah</td>
                    <td class="du-info-value">{{ $pendaftaran->asal_sekolah ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="du-info-label">Tahun Ajaran</td>
                    <td class="du-info-value">{{ $settings['tahun_ajaran'] ?? '2026/2027' }}</td>
                </tr>
            </table>

            <div class="du-notice-box" style="margin-top: auto;">
                <i class="fa-solid fa-circle-info" style="margin-right: .3rem;"></i>
                Pastikan data pendaftaran Anda telah sesuai dengan berkas otentik saat daftar ulang.
            </div>
        </div>

        {{-- Card 2: Rincian Pembayaran Daftar Ulang --}}
        <div class="du-card">
            <h3 class="du-card-title">
                <i class="fa-solid fa-receipt" style="color:#10b981;"></i> Status Tagihan Daftar Ulang
            </h3>

            @php
                $status = $pembayaran ? $pembayaran->status : 'belum_bayar';
                $isLunas = $status === 'lunas';
                $isPending = $status === 'pending';
                $hasBiaya = ($biaya !== null && $biaya > 0);
            @endphp

            <div class="du-payment-box">
                <div class="du-amount-label">Biaya Daftar Ulang</div>
                @if($hasBiaya)
                    <div class="du-amount-val">
                        Rp {{ number_format($biaya, 0, ',', '.') }}
                    </div>
                @else
                    <div class="du-amount-empty">
                        Biaya belum ditentukan
                    </div>
                @endif

                {{-- Status Badge --}}
                @if($isLunas)
                    <div class="du-status-pill du-status-lunas">
                        <i class="fa-solid fa-circle-check"></i> Lunas
                    </div>
                @elseif($isPending)
                    <div class="du-status-pill du-status-pending">
                        <i class="fa-solid fa-clock"></i> Menunggu Konfirmasi
                    </div>
                @elseif($status === 'gagal' || $status === 'kedaluwarsa')
                    <div class="du-status-pill du-status-gagal">
                        <i class="fa-solid fa-triangle-exclamation"></i> {{ ucfirst($status) }}
                    </div>
                @else
                    <div class="du-status-pill du-status-belum_bayar">
                        <i class="fa-solid fa-circle-dot"></i> Belum Dibayar
                    </div>
                @endif
            </div>

            {{-- Tombol Aksi Pembayaran --}}
            <div style="margin-top: .5rem;">
                @if($isLunas)
                    <button type="button" class="btn-pay-success">
                        <i class="fa-solid fa-circle-check"></i> Pembayaran Daftar Ulang Lunas
                    </button>
                    @if($pembayaran && $pembayaran->paid_at)
                        <p style="text-align:center; font-size:.8rem; color:#64748b; margin:.5rem 0 0;">
                            Waktu Transaksi: {{ $pembayaran->paid_at->translatedFormat('d F Y - H:i') }} WIB
                        </p>
                    @endif
                @elseif($hasBiaya)
    <button type="button" class="btn-pay-now" id="btnBayarSekarang" onclick="bayarSekarang()">
        <i class="fa-solid fa-credit-card"></i> Bayar Sekarang
    </button>
                @else
                    <button type="button" class="btn-pay-disabled" disabled>
                        <i class="fa-solid fa-lock"></i> Bayar Sekarang
                    </button>
                    <p style="text-align:center; font-size:.8rem; color:#d97706; margin:.6rem 0 0; font-weight:600;">
                        <i class="fa-solid fa-circle-exclamation"></i> Tombol pembayaran akan aktif setelah nominal biaya daftar ulang ditentukan oleh pihak sekolah.
                    </p>
                @endif
            </div>

            {{-- Bantuan WhatsApp --}}
            @if(!empty($settings['link_wa']))
            <div style="margin-top: 1.25rem; text-align: center;">
                <a href="{{ $settings['link_wa'] }}" target="_blank" style="color: #16a34a; font-size: .85rem; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: .4rem;">
                    <i class="fa-brands fa-whatsapp"></i> Butuh bantuan panitia? Hubungi via WhatsApp
                </a>
            </div>
            @endif

        </div>

    </div>

</div>
@endsection

@section('scripts')

{{-- Midtrans Snap --}}
<script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('midtrans.client_key') }}"></script>

<script>
    function bayarSekarang() {
        const button = document.getElementById('btnBayarSekarang');

        button.disabled = true;
        button.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Memproses...';

        fetch("{{ route('siswa.pembayaran.bayar') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {

            if (!data.success) {
    throw new Error(
        data.error
            ? data.message + ' ' + data.error
            : data.message || 'Gagal membuat pembayaran.'
    );
}

            window.snap.pay(data.snap_token, {

                onSuccess: function(result) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Pembayaran Berhasil',
                        text: 'Pembayaran daftar ulang berhasil dilakukan.',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.reload();
                    });
                },

                onPending: function(result) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Pembayaran Menunggu',
                        text: 'Silakan selesaikan pembayaran sesuai instruksi Midtrans.',
                        confirmButtonText: 'OK'
                    });
                },

                onError: function(result) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Pembayaran Gagal',
                        text: 'Terjadi kesalahan saat pembayaran.',
                        confirmButtonText: 'OK'
                    });
                },

                onClose: function() {
                    button.disabled = false;
                    button.innerHTML = '<i class="fa-solid fa-credit-card"></i> Bayar Sekarang';
                }

            });

        })
        .catch(error => {

            console.error(error);

            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: error.message || 'Tidak dapat memproses pembayaran.',
                confirmButtonText: 'Mengerti'
            });

            button.disabled = false;
            button.innerHTML = '<i class="fa-solid fa-credit-card"></i> Bayar Sekarang';
        });
    }
</script>

@endsection