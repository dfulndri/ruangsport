@extends('layouts.app')

@section('title', 'Buat Kompetisi')
@section('hero_title', 'Buat Kompetisi Baru')
@section('hero_text', 'Format sistem gugur (knockout) untuk peserta individu atau tim.')

@section('content')
  <div class="site-section">
    <div class="container">
      @include('partials.panel-menu')
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <form method="POST" action="{{ route('organizer.competitions.store') }}" class="auth-card">
            @csrf
            @include('organizer.competitions._form')
            <button class="btn btn-primary py-3 px-5">Simpan</button>
            <a href="{{ route('organizer.competitions.index') }}" class="btn btn-link">Batal</a>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
