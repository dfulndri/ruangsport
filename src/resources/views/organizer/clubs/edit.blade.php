@extends('layouts.app')

@section('title', 'Ubah Klub')
@section('hero_title', 'Ubah Klub')
@section('hero_text', $club->name)

@section('content')
  <div class="site-section">
    <div class="container">
      @include('partials.panel-menu')
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <form method="POST" action="{{ route('organizer.clubs.update', $club->slug) }}" class="auth-card">
            @csrf @method('PUT')
            @include('organizer.clubs._form')
            <button class="btn btn-primary py-3 px-5">Simpan perubahan</button>
            <a href="{{ route('organizer.clubs.index') }}" class="btn btn-link">Batal</a>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
