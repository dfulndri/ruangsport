@extends('layouts.app')

@section('title', 'Beranda')
@section('hero_title', 'Temukan teman dan kegiatan olahraga di sekitarmu')
@section('hero_text', 'Gabung klub, ikut aktivitas, dan bertanding di kompetisi.')

@section('content')
  <div class="site-section">
    <div class="container">
      <p class="text-center mb-5">
        @guest
          <a href="{{ route('register') }}" class="btn btn-primary py-3 px-5 mr-2">Daftar gratis</a>
          <a href="{{ route('activities.index') }}" class="btn btn-outline-primary py-3 px-5">Lihat aktivitas</a>
        @else
          <a href="{{ route('dashboard') }}" class="btn btn-primary py-3 px-5">Buka dashboard</a>
        @endguest
      </p>

      <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Aktivitas mendatang</h3>
        <a href="{{ route('activities.index') }}">Semua aktivitas</a>
      </div>
      @forelse ($activities as $activity)
        @include('partials.activity-item', ['activity' => $activity])
      @empty
        <p class="text-muted">Belum ada aktivitas mendatang.</p>
      @endforelse

      <div class="d-flex justify-content-between align-items-center mt-5 mb-4">
        <h3 class="mb-0">Klub</h3>
        <a href="{{ route('clubs.index') }}">Semua klub</a>
      </div>
      <div class="row">
        @forelse ($clubs as $club)
          <div class="col-md-4 mb-4">@include('partials.club-card', ['club' => $club])</div>
        @empty
          <div class="col-12"><p class="text-muted">Belum ada klub.</p></div>
        @endforelse
      </div>

      <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
        <h3 class="mb-0">Kompetisi</h3>
        <a href="{{ route('competitions.index') }}">Semua kompetisi</a>
      </div>
      <div class="row">
        @forelse ($competitions as $competition)
          <div class="col-md-4 mb-4">@include('partials.competition-card', ['competition' => $competition])</div>
        @empty
          <div class="col-12"><p class="text-muted">Belum ada kompetisi.</p></div>
        @endforelse
      </div>
    </div>
  </div>
@endsection
