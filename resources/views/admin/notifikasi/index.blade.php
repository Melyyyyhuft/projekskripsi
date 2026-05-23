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
            @php
                $type = $notif->data['type'] ?? 'baru';
                $isRead = !is_null($notif->read_at);
                
                $bgColor = $isRead ? '#f9fafb' : ($type == 'revisi' ? '#fffbeb' : '#f0fdf4');
                $accentColor = $type == 'revisi' ? '#f59e0b' : '#10b981';
                $icon = $type == 'revisi' ? '🔄' : '🔔';
                $title = $type == 'revisi' ? 'Revisi Masuk' : 'Pendaftaran Baru';
            @endphp
            <div style="padding: 1rem; border: 1px solid rgba(0,0,0,0.05); border-radius: var(--radius-md); background: {{ $bgColor }}; border-left: 4px solid {{ $isRead ? '#e5e7eb' : $accentColor }}; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="font-size: 0.75rem; font-weight: 800; color: {{ $accentColor }}; text-transform: uppercase; margin-bottom: 0.25rem;">{{ $title }}</div>
                    <strong style="color: var(--dark); font-size: 1.1rem;">{{ $icon }} {{ $notif->data['pesan'] ?? 'Notifikasi' }}</strong>
                    <div style="color: var(--gray-text); font-size: 0.875rem; margin-top: 0.25rem;">
                        Waktu: {{ $notif->created_at->diffForHumans() }}
                    </div>
                </div>
                
                <div style="display: flex; gap: 0.5rem; align-items: center;">
                    @if(!$notif->read_at)
                        <a href="{{ route('admin.notifikasi.read', $notif->id) }}" class="btn-primary" style="background: {{ $accentColor }}; padding: 0.4rem 0.8rem; font-size: 0.875rem;">Lihat Detail</a>
                    @else
                        <a href="{{ route('admin.pendaftaran.show', $notif->data['pendaftaran_id']) }}" class="btn-outline" style="padding: 0.4rem 0.8rem; font-size: 0.875rem;">Buka Detail</a>
                        <span style="color: var(--gray-text); font-size: 0.875rem; font-weight: 600; margin-left: 0.5rem;">Dibaca</span>
                    @endif
                </div>
            </div>
        @empty
            <div style="text-align: center; color: var(--gray-text); padding: 3rem;">
                Tidak ada notifikasi di kategori ini.
            </div>
        @endforelse
    </div>
</div>
@endsection
