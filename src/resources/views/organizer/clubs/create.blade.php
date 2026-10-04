@extends('layouts.app')

@section('title', 'Buat Klub')
@section('hero_title', 'Buat Klub Baru')
@section('hero_text', 'Anda otomatis menjadi pemilik klub dan dapat membuat aktivitas serta kompetisi.')

@section('content')
  <div class="site-section">
    <div class="container">
      @include('partials.panel-menu')
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <form method="POST" action="{{ route('organizer.clubs.store') }}" class="auth-card">
            @csrf
            @include('organizer.clubs._form')
            <button class="btn btn-primary py-3 px-5">Simpan</button>
            <a href="{{ route('organizer.clubs.index') }}" class="btn btn-link">Batal</a>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
