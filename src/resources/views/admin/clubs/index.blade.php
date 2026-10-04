@extends('layouts.app')

@section('title', 'Moderasi Klub')
@section('hero_title', 'Moderasi Klub')

@section('content')
  <div class="site-section">
    <div class="container">
      @include('partials.panel-menu')

      <form method="GET" action="{{ route('admin.clubs.index') }}" class="filter-form mb-4">
        <div class="form-row">
          <div class="col-md-6 mb-2"><input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama klub..."></div>
          <div class="col-md-2 mb-2"><button class="btn btn-primary btn-block">Cari</button></div>
        </div>
      </form>

      <div class="table-responsive">
        <table class="table table-hover">
          <thead><tr><th>Klub</th><th>Olahraga</th><th>Pemilik</th><th>Anggota</th><th>Verifikasi</th><th></th></tr></thead>
          <tbody>
            @foreach ($clubs as $club)
              <tr>
                <td><a href="{{ route('clubs.show', $club->slug) }}">{{ $club->name }}</a><br><small class="text-muted">{{ $club->location?->name ?? 'Tangerang' }}</small></td>
                <td>{{ $club->sport->name }}</td>
                <td>{{ $club->owner->name }}</td>
                <td>{{ $club->members_count }}</td>
                <td>@if ($club->is_verified)<span class="badge badge-success">Terverifikasi</span>@else<span class="badge badge-secondary">Belum</span>@endif</td>
                <td class="text-right text-nowrap">
                  <form method="POST" action="{{ route('admin.clubs.verify', $club->id) }}" class="d-inline">@csrf @method('PATCH')
                    <button class="btn btn-outline-primary btn-sm">{{ $club->is_verified ? 'Cabut verifikasi' : 'Verifikasi' }}</button>
                  </form>
                  <form method="POST" action="{{ route('admin.clubs.destroy', $club->id) }}" class="d-inline">@csrf @method('DELETE')
                    <button class="btn btn-outline-danger btn-sm" onclick="return confirm('Hapus klub ini?')">Hapus</button>
                  </form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="d-flex justify-content-center">{{ $clubs->links() }}</div>
    </div>
  </div>
@endsection
