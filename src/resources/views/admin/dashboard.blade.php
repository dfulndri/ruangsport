@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('hero_title', 'Dashboard Admin')
@section('hero_text', 'Ringkasan ekosistem Ruangsport.')

@section('content')
  <div class="site-section">
    <div class="container">
      @include('partials.panel-menu')

      <div class="row">
        @foreach ([
          ['Pengguna', $stats['users']],
          ['Klub', $stats['clubs']],
          ['Klub terverifikasi', $stats['verified_clubs']],
          ['Organizer', $stats['organizers']],
          ['Aktivitas', $stats['activities']],
          ['Kompetisi', $stats['competitions']],
          ['Total peserta kegiatan', $stats['participants']],
          ['Verifikasi menunggu', $stats['pending_verifications']],
        ] as [$label, $value])
          <div class="col-md-3 col-6 mb-4">
            <div class="card-block text-center h-100">
              <div class="stat-number">{{ number_format($value, 0, ',', '.') }}</div>
              <div class="text-muted">{{ $label }}</div>
            </div>
          </div>
        @endforeach
      </div>

      <div class="row mt-4">
        <div class="col-lg-6 mb-4">
          <h4 class="mb-3">Olahraga paling banyak diminati</h4>
          @if ($topSports->isEmpty())
            <p class="text-muted">Belum ada data.</p>
          @else
            <table class="table">
              <thead><tr><th>Olahraga</th><th class="text-right">Jumlah aktivitas</th></tr></thead>
              <tbody>
                @foreach ($topSports as $row)
                  <tr><td>{{ $row->sport->name }}</td><td class="text-right">{{ $row->total }}</td></tr>
                @endforeach
              </tbody>
            </table>
          @endif
        </div>
        <div class="col-lg-6 mb-4">
          <h4 class="mb-3">Aktivitas per bulan</h4>
          <table class="table">
            <thead><tr><th>Bulan</th><th class="text-right">Jumlah</th></tr></thead>
            <tbody>
              @foreach ($months as $m)
                <tr><td>{{ $m['label'] }}</td><td class="text-right">{{ $m['total'] }}</td></tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
@endsection
