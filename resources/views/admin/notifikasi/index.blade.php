@extends('layouts.admin')
@section('title', 'Pusat Notifikasi')

@section('content')
<div class="glass-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h3 style="color: var(--primary); margin: 0;">Notifikasi Pendaftaran</h3>
        
        @if($tab == 'unread' && $notifications->count() > 0)
        <form action="{{ route('admin.notifikasi.read_all') }}" method="POST">
            @csrf
            <button type="submit" class="btn-primary" style="padding: 0.5rem 1rem; font-size: 0.875rem;">Tandai Semua Dibaca</button>
        </form>
        @endif
    </div>

    <!-- Tabs -->
    <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem; border-bottom: 1px solid rgba(0,0,0,0.1); padding-bottom: 0.5rem;">
        <a href="{{ route('admin.notifikasi.index', ['tab' => 'unread']) }}" style="font-weight: 600; padding: 0.5rem 1rem; border-radius: var(--radius-sm); {{ $tab == 'unread' ? 'background: var(--primary); color: white;' : 'color: var(--gray-text); text-decoration: none;' }}">
            Belum Dibaca
        </a>
        <a href="{{ route('admin.notifikasi.index', ['tab' => 'read']) }}" style="font-weight: 600; padding: 0.5rem 1rem; border-radius: var(--radius-sm); {{ $tab == 'read' ? 'background: var(--primary); color: white;' : 'color: var(--gray-text); text-decoration: none;' }}">
            Arsip (Sudah Dibaca)
        </a>
    </div>

    <div style="display: flex; flex-direction: column; gap: 1rem;">
        @forelse($notifications as $notif)
            <div style="padding: 1rem; border: 1px solid rgba(0,0,0,0.05); border-radius: var(--radius-md); background: {{ $notif->read_at ? '#f9fafb' : '#f0fdf4' }}; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <strong style="color: var(--dark); font-size: 1.1rem;">🔔 {{ $notif->data['pesan'] ?? 'Notifikasi' }}</strong>
                    <div style="color: var(--gray-text); font-size: 0.875rem; margin-top: 0.25rem;">
                        Waktu: {{ $notif->created_at->diffForHumans() }}
                    </div>
                </div>
                
                @if(!$notif->read_at)
                    <a href="{{ route('admin.notifikasi.read', $notif->id) }}" class="btn-primary" style="background: #10b981; padding: 0.4rem 0.8rem; font-size: 0.875rem;">Buka & Tandai Dibaca</a>
                @else
                    <span style="color: var(--gray-text); font-size: 0.875rem; font-weight: 600;">Sudah Dibaca</span>
                @endif
            </div>
        @empty
            <div style="text-align: center; color: var(--gray-text); padding: 3rem;">
                Tidak ada notifikasi di kategori ini.
            </div>
        @endforelse
    </div>
</div>
@endsection
