@extends('layouts.app')

@section('title', 'Beranda')

@section('content')

{{-- HERO ATAS --}}
<div class="p-5 bg-primary text-white text-center rounded">
    <h1>Selamat Datang di PPDB Sekolah</h1>
    <p>Masa depan cerah dimulai di sini!</p>
    <a href="{{ url('/ppdb/daftar') }}" class="btn btn-light mt-3">
        Daftar Sekarang
    </a>
</div>

{{-- SECTION GAMBAR SEKOLAH --}}
<section style="background:#1d4ed8; padding:60px; color:white; text-align:center; margin-top:30px;">
    <h2>Profil Sekolah</h2>
    <p>Mengenal lingkungan dan fasilitas sekolah kami</p>

    <img src="/images/sekolah.jpg" alt="Sekolah"
         style="width:300px; margin-top:20px; border-radius:10px;">
</section>

{{-- DIV BARU DI BAWAHNYA --}}
<div class="container mt-5 text-center">
    <h2>Tentang Sekolah</h2>

    <img src="/images/gedung.jpg"
         alt="Gedung Sekolah"
         style="width:300px; margin:20px 0; border-radius:10px;">

    <p>
        Sekolah kami berkomitmen mencetak generasi unggul,
        berkarakter, dan siap menghadapi tantangan masa depan
        melalui pendidikan berkualitas dan lingkungan yang nyaman.
    </p>
</div>

@endsection