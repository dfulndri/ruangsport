@extends('layouts.app')

@section('title', 'Tambah Venue')
@section('hero_title', 'Tambah Venue')

@section('content')
  <div class="site-section">
    <div class="container">
      @include('partials.panel-menu')
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <form method="POST" action="{{ route('venue-owner.venues.store') }}" class="auth-card">
            @csrf
            @include('venue-owner.venues._form')
            <button class="btn btn-primary py-3 px-5">Simpan</button>
            <a href="{{ route('venue-owner.venues.index') }}" class="btn btn-link">Batal</a>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
