@extends('layouts.app')

@section('title', 'Kompetisi Saya')
@section('hero_title', 'Kompetisi yang Saya Kelola')

@section('content')
  <div class="site-section">
    <div class="container">
      @include('partials.panel-menu')

      <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Daftar kompetisi</h3>
        @can('create', \App\Models\Competition::class)
          <a href="{{ route('organizer.competitions.create') }}" class="btn btn-primary">+ Buat kompetisi</a>
        @endcan
      </div>

      @if ($competitions->isEmpty())
        <p class="text-muted">Belum ada kompetisi. Buat klub terlebih dahulu bila Anda belum mengelola klub.</p>
      @else
        <div class="table-responsive">
          <table class="table table-hover">
            <thead><tr><th>Kompetisi</th><th>Peserta</th><th>Terverifikasi</th><th>Menunggu</th><th>Status</th><th></th></tr></thead>
            <tbody>
              @foreach ($competitions as $c)
                <tr>
                  <td><a href="{{ route('competitions.show', $c->slug) }}">{{ $c->name }}</a><br><small class="text-muted">{{ $c->sport->name }} · {{ \App\Support\Label::text('participation', $c->participant_type) }}</small></td>
                  <td>{{ $c->participant_type->value === 'team' ? 'Tim' : 'Individu' }}</td>
                  <td>{{ $c->verified_count }}@if ($c->max_participants) / {{ $c->max_participants }}@endif</td>
                  <td>{{ $c->pending_count }}</td>
                  <td>{{ \App\Support\Label::badge('competition', $c->status) }}</td>
                  <td class="text-right text-nowrap">
                    <a href="{{ route('organizer.competitions.manage', $c->slug) }}" class="btn btn-outline-primary btn-sm">Kelola</a>
                    <a href="{{ route('organizer.competitions.edit', $c->slug) }}" class="btn btn-outline-secondary btn-sm">Ubah</a>
                    <form method="POST" action="{{ route('organizer.competitions.destroy', $c->slug) }}" class="d-inline">@csrf @method('DELETE')
                      <button class="btn btn-outline-danger btn-sm" onclick="return confirm('Hapus kompetisi ini?')">Hapus</button>
                    </form>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="d-flex justify-content-center">{{ $competitions->links() }}</div>
      @endif
    </div>
  </div>
@endsection
