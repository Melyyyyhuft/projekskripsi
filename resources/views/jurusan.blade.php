@extends('layouts.app')

@section('title', 'Jurusan')

@section('content')
  <div class="text-center mb-5">
    <h1 class="fw-bold">Jurusan Kami</h1>
    <p class="lead">Pilihan jurusan yang dapat ditempuh siswa</p>
  </div>

  <div class="row">
    @php
      $jurusan = [
        ['nama' => 'Multimedia (MM)', 'deskripsi' => 'Desain grafis, editing, animasi', 'icon' => 'bi-camera-video'],
        ['nama' => 'Teknik Komputer Jaringan (TKJ)', 'deskripsi' => 'Jaringan komputer & IT Support', 'icon' => 'bi-wifi'],
        ['nama' => 'Keperawatan', 'deskripsi' => 'Perawatan & kesehatan dasar', 'icon' => 'bi-heart-pulse'],
        ['nama' => 'OTKP', 'deskripsi' => 'Administrasi perkantoran modern', 'icon' => 'bi-briefcase'],
        ['nama' => 'TBSM', 'deskripsi' => 'Teknik & servis sepeda motor', 'icon' => 'bi-gear'],
      ];
    @endphp

    @foreach ($jurusan as $j)
      <div class="col-md-4 mb-4">
        <div class="card h-100 text-center shadow-sm">
          <div class="card-body">
            <i class="bi {{ $j['icon'] }} fs-1 mb-3"></i>
            <h5 class="card-title">{{ $j['nama'] }}</h5>
            <p class="card-text">{{ $j['deskripsi'] }}</p>
          </div>
        </div>
      </div>
    @endforeach
  </div>
@endsection
