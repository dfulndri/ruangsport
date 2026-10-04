@extends('layouts.app')

@section('title', 'Ubah Kompetisi')
@section('hero_title', 'Ubah Kompetisi')
@section('hero_text', $competition->name)

@section('content')
  <div class="site-section">
    <div class="container">
      @include('partials.panel-menu')
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <form method="POST" action="{{ route('organizer.competitions.update', $competition->slug) }}" class="auth-card">
            @csrf @method('PUT')
            @include('organizer.competitions._form')
            <button class="btn btn-primary py-3 px-5">Simpan perubahan</button>
            <a href="{{ route('organizer.competitions.manage', $competition->slug) }}" class="btn btn-link">Batal</a>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
