@extends('layouts.app')

@section('title', 'Beranda')
@section('hero_class', 'page-hero--home')
@section('hero_title', 'Temukan teman dan kegiatan olahraga di sekitarmu')
@section('hero_text', 'Gabung klub, ikut aktivitas, dan bertanding di kompetisi, semuanya di satu tempat.')

@section('hero_actions')
  @guest
    <a href="{{ route('register') }}" class="btn btn-primary py-3 px-5">Daftar gratis</a>
    <a href="{{ route('activities.index') }}" class="btn btn-outline-light py-3 px-5">Lihat aktivitas</a>
  @else
    <a href="{{ route('dashboard') }}" class="btn btn-primary py-3 px-5">Buka dashboard</a>
    <a href="{{ route('activities.index') }}" class="btn btn-outline-light py-3 px-5">Cari aktivitas</a>
  @endguest
@endsection

@section('content')
  <div class="container stats-strip">
    <div class="stats-card">
      <div class="row text-center">
        @foreach ([['Anggota aktif', $stats['users']], ['Klub', $stats['clubs']], ['Aktivitas', $stats['activities']], ['Kompetisi', $stats['competitions']]] as [$label, $value])
          <div class="col-6 col-md-3 stat-item">
            <div class="stat-number" data-count="{{ $value }}">{{ $value }}</div>
            <div class="text-muted">{{ $label }}</div>
          </div>
        @endforeach
      </div>
    </div>
  </div>

  @if ($sports->isNotEmpty())
    <section class="rs-section pb-0">
      <div class="container">
        <div class="section-head"><h3>Pilih olahragamu</h3></div>
        @foreach ($sports as $sport)
          <a class="sport-chip" href="{{ route('activities.index', ['sport' => $sport->id]) }}">{{ $sport->name }}</a>
        @endforeach
      </div>
    </section>
  @endif

  <section class="rs-section">
    <div class="container">
      <div class="section-head">
        <h3>Aktivitas mendatang</h3>
        <a href="{{ route('activities.index') }}">Semua aktivitas</a>
      </div>
      @forelse ($activities as $activity)
        @include('partials.activity-item', ['activity' => $activity])
      @empty
        <p class="text-muted">Belum ada aktivitas mendatang. Organizer bisa membuatnya dari panel Organizer.</p>
      @endforelse
    </div>
  </section>

  <section class="rs-section rs-section--alt">
    <div class="container">
      <div class="section-head"><h3>Cara kerja</h3></div>
      <div class="row">
        @foreach ([['Buat akun', 'Daftar gratis sebagai member atau pemilik venue.'], ['Gabung atau daftar', 'Ajukan masuk klub, atau daftar ke aktivitas yang kuotanya masih ada.'], ['Main dan bertanding', 'Datang ke kegiatan, ikut kompetisi, dan pantau bagan pertandingan.']] as $i => [$title, $text])
          <div class="col-md-4 mb-4 step-item">
            <div class="step-number">{{ $i + 1 }}</div>
            <h5>{{ $title }}</h5>
            <p class="text-muted mb-0">{{ $text }}</p>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  <section class="rs-section">
    <div class="container">
      <div class="section-head">
        <h3>Klub</h3>
        <a href="{{ route('clubs.index') }}">Semua klub</a>
      </div>
      <div class="row">
        @forelse ($clubs as $club)
          <div class="col-md-6 col-lg-4 mb-4">@include('partials.club-card', ['club' => $club])</div>
        @empty
          <div class="col-12"><p class="text-muted">Belum ada klub.</p></div>
        @endforelse
      </div>
    </div>
  </section>

  <section class="rs-section rs-section--alt">
    <div class="container">
      <div class="section-head">
        <h3>Kompetisi</h3>
        <a href="{{ route('competitions.index') }}">Semua kompetisi</a>
      </div>
      <div class="row">
        @forelse ($competitions as $competition)
          <div class="col-md-6 col-lg-4 mb-4">@include('partials.competition-card', ['competition' => $competition])</div>
        @empty
          <div class="col-12"><p class="text-muted">Belum ada kompetisi.</p></div>
        @endforelse
      </div>
    </div>
  </section>

  <section class="cta-band">
    <div class="container">
      <h3>Punya lapangan atau tempat olahraga?</h3>
      <p>Daftarkan venue Anda agar mudah ditemukan komunitas olahraga di sekitar.</p>
      @guest
        <a href="{{ route('register') }}" class="btn btn-primary py-3 px-5">Daftar sebagai pemilik venue</a>
      @else
        <a href="{{ route('venues.index') }}" class="btn btn-primary py-3 px-5">Jelajahi venue</a>
      @endguest
    </div>
  </section>
@endsection
