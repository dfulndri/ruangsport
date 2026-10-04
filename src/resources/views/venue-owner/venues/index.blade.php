@extends('layouts.app')

@section('title', 'Venue Saya')
@section('hero_title', 'Pengelolaan Venue')
@section('hero_text', 'Kelola informasi tempat olahraga Anda.')

@section('content')
  <div class="site-section">
    <div class="container">
      @include('partials.panel-menu')

      <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Daftar venue</h3>
        <a href="{{ route('venue-owner.venues.create') }}" class="btn btn-primary">+ Tambah venue</a>
      </div>

      @if ($venues->isEmpty())
        <p class="text-muted">Belum ada venue.</p>
      @else
        <div class="table-responsive">
          <table class="table table-hover">
            <thead><tr><th>Venue</th><th>Lokasi</th><th>Olahraga</th><th>Verifikasi</th><th></th></tr></thead>
            <tbody>
              @foreach ($venues as $venue)
                <tr>
                  <td><a href="{{ route('venues.show', $venue->slug) }}">{{ $venue->name }}</a></td>
                  <td>{{ $venue->location?->name ?? '-' }}</td>
                  <td>{{ $venue->sports->pluck('name')->implode(', ') ?: '-' }}</td>
                  <td>
                    @if ($venue->is_verified)
                      <span class="badge badge-success">Terverifikasi</span>
                    @else
                      <form method="POST" action="{{ route('venue-owner.venues.verification', $venue->slug) }}">@csrf
                        <button class="btn btn-outline-secondary btn-sm">Ajukan</button>
                      </form>
                    @endif
                  </td>
                  <td class="text-right text-nowrap">
                    <a href="{{ route('venue-owner.venues.edit', $venue->slug) }}" class="btn btn-outline-secondary btn-sm">Ubah</a>
                    <form method="POST" action="{{ route('venue-owner.venues.destroy', $venue->slug) }}" class="d-inline">@csrf @method('DELETE')
                      <button class="btn btn-outline-danger btn-sm" onclick="return confirm('Hapus venue ini?')">Hapus</button>
                    </form>
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
