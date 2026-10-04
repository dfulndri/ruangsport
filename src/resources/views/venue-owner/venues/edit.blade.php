@extends('layouts.app')

@section('title', 'Ubah Venue')
@section('hero_title', 'Ubah Venue')
@section('hero_text', $venue->name)

@section('content')
  <div class="site-section">
    <div class="container">
      @include('partials.panel-menu')
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <form method="POST" action="{{ route('venue-owner.venues.update', $venue->slug) }}" class="auth-card">
            @csrf @method('PUT')
            @include('venue-owner.venues._form')
            <button class="btn btn-primary py-3 px-5">Simpan perubahan</button>
            <a href="{{ route('venue-owner.venues.index') }}" class="btn btn-link">Batal</a>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
