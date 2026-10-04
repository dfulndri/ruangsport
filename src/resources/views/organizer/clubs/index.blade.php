@extends('layouts.app')

@section('title', 'Klub Saya')
@section('hero_title', 'Klub yang Saya Kelola')

@section('content')
  <div class="site-section">
    <div class="container">
      @include('partials.panel-menu')

      <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Daftar klub</h3>
        <a href="{{ route('organizer.clubs.create') }}" class="btn btn-primary">+ Buat klub</a>
      </div>

      @if ($clubs->isEmpty())
        <p class="text-muted">Anda belum mengelola klub.</p>
      @else
        <div class="table-responsive">
          <table class="table table-hover">
            <thead><tr><th>Klub</th><th>Olahraga</th><th>Anggota</th><th>Menunggu</th><th>Verifikasi</th><th></th></tr></thead>
            <tbody>
              @foreach ($clubs as $club)
                <tr>
                  <td><a href="{{ route('clubs.show', $club->slug) }}">{{ $club->name }}</a><br><small class="text-muted">{{ $club->location?->name ?? 'Tangerang' }}</small></td>
                  <td>{{ $club->sport->name }}</td>
                  <td>{{ $club->members_count }}</td>
                  <td>{{ $club->pending_count }}</td>
                  <td>
                    @if ($club->is_verified)
                      <span class="badge badge-success">Terverifikasi</span>
                    @else
                      <form method="POST" action="{{ route('organizer.clubs.verification', $club->slug) }}">@csrf
                        <button class="btn btn-outline-secondary btn-sm">Ajukan</button>
                      </form>
                    @endif
                  </td>
                  <td class="text-right text-nowrap">
                    <a href="{{ route('organizer.clubs.members', $club->slug) }}" class="btn btn-outline-primary btn-sm">Anggota</a>
                    <a href="{{ route('organizer.clubs.edit', $club->slug) }}" class="btn btn-outline-secondary btn-sm">Ubah</a>
                    @can('delete', $club)
                      <form method="POST" action="{{ route('organizer.clubs.destroy', $club->slug) }}" class="d-inline">@csrf @method('DELETE')
                        <button class="btn btn-outline-danger btn-sm" onclick="return confirm('Hapus klub ini beserta datanya?')">Hapus</button>
                      </form>
                    @endcan
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>
  </div>
@endsection
