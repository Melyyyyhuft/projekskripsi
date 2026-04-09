@extends('layouts.app')

@section('title', 'Ujian Online')

@section('content')
  <div class="text-center mb-5">
    <h1 class="fw-bold">Ujian Online</h1>
    <p class="lead">Kerjakan soal berikut dengan jujur</p>
  </div>

  <div class="row justify-content-center">
    <div class="col-md-8">
      <form>
        <div class="mb-3">
          <label class="form-label">1. Ibu kota Indonesia adalah?</label>
          <input type="text" class="form-control">
        </div>
        <div class="mb-3">
          <label class="form-label">2. 2 + 5 = ?</label>
          <input type="text" class="form-control">
        </div>
        <button type="submit" class="btn btn-success">Kirim Jawaban</button>
      </form>
    </div>
  </div>
@endsection
