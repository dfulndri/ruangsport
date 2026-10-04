@extends('layouts.app')

@section('title', 'Aktivitas Saya')
@section('hero_title', 'Aktivitas yang Saya Kelola')

@section('content')
  <div class="site-section">
    <div class="container">
      @include('partials.panel-menu')

      <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Daftar aktivitas</h3>
        @can('create', \App\Models\Activity::class)
          <a href="{{ route('organizer.activities.create') }}" class="btn btn-primary">+ Buat aktivitas</a>
        @endcan
      </div>

      @if ($activities->isEmpty())
        <p class="text-muted">Belum ada aktivitas. Buat klub terlebih dahulu bila Anda belum mengelola klub.</p>
      @else
        <div class="table-responsive">
          <table class="table table-hover">
            <thead><tr><th>Kegiatan</th><th>Klub</th><th>Waktu</th><th>Peserta</th><th>Status</th><th></th></tr></thead>
            <tbody>
              @foreach ($activities as $a)
                <tr>
                  <td><a href="{{ route('activities.show', $a->slug) }}">{{ $a->title }}</a><br><small class="text-muted">{{ $a->sport->name }}</small></td>
                  <td>{{ $a->club?->name ?? '-' }}</td>
                  <td>{{ $a->starts_at->translatedFormat('d M Y H:i') }}</td>
                  <td>{{ $a->confirmed_count }}@if ($a->quota) / {{ $a->quota }}@endif</td>
                  <td>{{ \App\Support\Label::badge('activity', $a->status) }}</td>
                  <td class="text-right text-nowrap">
                    <a href="{{ route('organizer.activities.participants', $a->slug) }}" class="btn btn-outline-primary btn-sm">Peserta</a>
                    <a href="{{ route('organizer.activities.edit', $a->slug) }}" class="btn btn-outline-secondary btn-sm">Ubah</a>
                    <form method="POST" action="{{ route('organizer.activities.destroy', $a->slug) }}" class="d-inline">@csrf @method('DELETE')
                      <button class="btn btn-outline-danger btn-sm" onclick="return confirm('Hapus aktivitas ini?')">Hapus</button>
                    </form>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="d-flex justify-content-center">{{ $activities->links() }}</div>
      @endif
    </div>
  </div>
@endsection
