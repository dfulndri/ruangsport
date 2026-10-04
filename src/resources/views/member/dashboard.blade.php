@extends('layouts.app')

@section('title', 'Dashboard')
@section('hero_title', 'Halo, '.$user->name)
@section('hero_text', 'Ringkasan klub dan kegiatan olahragamu.')

@section('content')
  <div class="site-section">
    <div class="container">
      @include('partials.panel-menu')

      <h3 class="mb-4">Klub saya</h3>
      @if ($clubMemberships->isEmpty())
        <p class="text-muted">Kamu belum bergabung ke klub mana pun. <a href="{{ route('clubs.index') }}">Cari klub</a></p>
      @else
        <div class="table-responsive mb-5">
          <table class="table table-hover">
            <thead><tr><th>Klub</th><th>Olahraga</th><th>Peran</th><th>Status</th></tr></thead>
            <tbody>
              @foreach ($clubMemberships as $m)
                <tr>
                  <td><a href="{{ route('clubs.show', $m->club->slug) }}">{{ $m->club->name }}</a></td>
                  <td>{{ $m->club->sport->name }}</td>
                  <td>{{ ucfirst($m->role->value) }}</td>
                  <td>
                    @php($badge = ['approved' => 'success', 'pending' => 'warning', 'rejected' => 'danger'][$m->status->value])
                    <span class="badge badge-{{ $badge }}">{{ ['approved' => 'Disetujui', 'pending' => 'Menunggu', 'rejected' => 'Ditolak'][$m->status->value] }}</span>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif

      <h3 class="mb-4">Kegiatan mendatang</h3>
      @if ($upcoming->isEmpty())
        <p class="text-muted">Belum ada RSVP. <a href="{{ route('activities.index') }}">Lihat aktivitas</a></p>
      @else
        <div class="table-responsive mb-5">
          <table class="table table-hover">
            <thead><tr><th>Kegiatan</th><th>Waktu</th><th>Status</th></tr></thead>
            <tbody>
              @foreach ($upcoming as $p)
                <tr>
                  <td><a href="{{ route('activities.show', $p->activity->slug) }}">{{ $p->activity->title }}</a></td>
                  <td>{{ $p->activity->starts_at->translatedFormat('l, d M Y H:i') }}</td>
                  <td><span class="badge badge-{{ $p->status->value === 'registered' ? 'success' : 'warning' }}">{{ $p->status->value === 'registered' ? 'Terdaftar' : 'Daftar tunggu' }}</span></td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif

      <h3 class="mb-4">Riwayat kegiatan</h3>
      @if ($history->isEmpty())
        <p class="text-muted">Belum ada riwayat.</p>
      @else
        <div class="table-responsive">
          <table class="table table-hover">
            <thead><tr><th>Kegiatan</th><th>Tanggal</th><th>Status</th></tr></thead>
            <tbody>
              @foreach ($history as $p)
                <tr>
                  <td><a href="{{ route('activities.show', $p->activity->slug) }}">{{ $p->activity->title }}</a></td>
                  <td>{{ $p->activity->starts_at->translatedFormat('d M Y') }}</td>
                  <td>{{ ['registered' => 'Terdaftar', 'waitlisted' => 'Daftar tunggu', 'cancelled' => 'Dibatalkan', 'attended' => 'Hadir', 'no_show' => 'Tidak hadir'][$p->status->value] }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>
  </div>
@endsection
