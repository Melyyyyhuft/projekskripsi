@extends('layouts.app')

@section('title', 'Hasil Seleksi')

@section('content')
  <div class="text-center mb-5">
    <h1 class="fw-bold">Pengumuman Hasil Seleksi</h1>
    <p class="lead">Masukkan nomor registrasi untuk melihat hasil</p>
  </div>

  <div class="row justify-content-center">
    <div class="col-md-6">
      <form>
        <div class="mb-3">
          <label class="form-label">Nomor Registrasi</label>
          <input type="text" class="form-control" placeholder="Contoh: PPDB12345">
        </div>
        <button type="submit" class="btn btn-info">Cek Hasil</button>
      </form>
    </div>
  </div>
@endsection
