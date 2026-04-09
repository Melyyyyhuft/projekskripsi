@extends('layouts.app')

@section('title', 'Pendaftaran PPDB')

@section('content')
  <div class="text-center mb-5">
    <h1 class="fw-bold">Formulir Pendaftaran</h1>
    <p class="lead">Silakan isi data berikut untuk mendaftar</p>
  </div>

  <div class="row justify-content-center">
    <div class="col-md-6">
      <form>
        <div class="mb-3">
          <label class="form-label">Nama Lengkap</label>
          <input type="text" class="form-control" placeholder="Masukkan nama lengkap">
        </div>
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" class="form-control" placeholder="contoh@email.com">
        </div>
        <div class="mb-3">
          <label class="form-label">No. HP</label>
          <input type="text" class="form-control" placeholder="08xxxxxxxxxx">
        </div>
        <div class="mb-3">
          <label class="form-label">Jurusan Pilihan</label>
          <select class="form-select">
            <option>Pilih Jurusan</option>
            <option>Multimedia</option>
            <option>TKJ</option>
            <option>Keperawatan</option>
            <option>OTKP</option>
            <option>TBSM</option>
          </select>
        </div>
        <button type="submit" class="btn btn-primary">Daftar</button>
      </form>
    </div>
  </div>
@endsection
