@extends('layouts.app')

@section('title', 'Panel Organizer')
@section('hero_title', 'Panel Organizer')
@section('hero_text', 'Kelola klub, aktivitas, dan kompetisi Anda.')

@section('content')
  <div class="site-section">
    <div class="container">
      @include('partials.panel-menu')

      <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Klub yang saya kelola</h3>
        <a href="{{ route('organizer.clubs.create') }}" class="btn btn-primary btn-sm">+ Buat klub</a>
      </div>
      @if ($clubs->isEmpty())
        <p class="text-muted mb-5">Anda belum mengelola klub. Buat klub untuk mulai membuat aktivitas dan kompetisi.</p>
      @else
        <div class="table-responsive mb-5">
          <table class="table table-hover">
            <thead><tr><th>Klub</th><th>Olahraga</th><th>Anggota</th><th>Menunggu</th><th></th></tr></thead>
            <tbody>
              @foreach ($clubs as $club)
                <tr>
                  <td><a href="{{ route('clubs.show', $club->slug) }}">{{ $club->name }}</a> @if ($club->is_verified)<span class="badge badge-success">Terverifikasi</span>@endif</td>
                  <td>{{ $club->sport->name }}</td>
                  <td>{{ $club->members_count }}</td>
                  <td>{{ $club->pending_count }}</td>
                  <td class="text-right"><a href="{{ route('organizer.clubs.members', $club->slug) }}" class="btn btn-outline-primary btn-sm">Anggota</a></td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif

      <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Aktivitas mendatang</h3>
        @can('create', \App\Models\Activity::class)
          <a href="{{ route('organizer.activities.create') }}" class="btn btn-primary btn-sm">+ Buat aktivitas</a>
        @endcan
      </div>
      @if ($activities->isEmpty())
        <p class="text-muted mb-5">Belum ada aktivitas mendatang.</p>
      @else
        <div class="table-responsive mb-5">
          <table class="table table-hover">
            <thead><tr><th>Kegiatan</th><th>Waktu</th><th>Status</th><th></th></tr></thead>
            <tbody>
              @foreach ($activities as $a)
                <tr>
                  <td>{{ $a->title }}</td>
                  <td>{{ $a->starts_at->translatedFormat('d M Y H:i') }}</td>
                  <td>{{ \App\Support\Label::badge('activity', $a->status) }}</td>
                  <td class="text-right"><a href="{{ route('organizer.activities.participants', $a->slug) }}" class="btn btn-outline-primary btn-sm">Peserta</a></td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif

      <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Kompetisi terbaru</h3>
        @can('create', \App\Models\Competition::class)
          <a href="{{ route('organizer.competitions.create') }}" class="btn btn-primary btn-sm">+ Buat kompetisi</a>
        @endcan
      </div>
      @if ($competitions->isEmpty())
        <p class="text-muted">Belum ada kompetisi.</p>
      @else
        <div class="table-responsive">
          <table class="table table-hover">
            <thead><tr><th>Kompetisi</th><th>Olahraga</th><th>Status</th><th></th></tr></thead>
            <tbody>
              @foreach ($competitions as $c)
                <tr>
                  <td>{{ $c->name }}</td>
                  <td>{{ $c->sport->name }}</td>
                  <td>{{ \App\Support\Label::badge('competition', $c->status) }}</td>
                  <td class="text-right"><a href="{{ route('organizer.competitions.manage', $c->slug) }}" class="btn btn-outline-primary btn-sm">Kelola</a></td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>
  </div>
@endsection
