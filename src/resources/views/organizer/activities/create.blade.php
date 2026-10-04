@extends('layouts.app')

@section('title', 'Buat Aktivitas')
@section('hero_title', 'Buat Aktivitas Baru')
@section('hero_text', 'Tentukan jadwal, lokasi, kuota, dan biaya kegiatan.')

@section('content')
  <div class="site-section">
    <div class="container">
      @include('partials.panel-menu')
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <form method="POST" action="{{ route('organizer.activities.store') }}" class="auth-card">
            @csrf
            @include('organizer.activities._form')
            <button class="btn btn-primary py-3 px-5">Simpan</button>
            <a href="{{ route('organizer.activities.index') }}" class="btn btn-link">Batal</a>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
