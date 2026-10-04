@extends('layouts.app')

@section('title', 'Tim Saya')
@section('hero_title', 'Tim Saya')
@section('hero_text', 'Buat tim untuk mengikuti kompetisi beregu.')

@section('content')
  <div class="site-section">
    <div class="container">
      @include('partials.panel-menu')

      <div class="row">
        <div class="col-lg-4 mb-5">
          <h4 class="mb-3">Buat tim baru</h4>
          <form method="POST" action="{{ route('teams.store') }}" class="auth-card">
            @csrf
            <div class="form-group">
              <label for="name">Nama tim</label>
              <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
              @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
              <label for="sport_id">Olahraga</label>
              <select id="sport_id" name="sport_id" class="form-control @error('sport_id') is-invalid @enderror" required>
                <option value="">Pilih olahraga</option>
                @foreach ($sports as $sport)
                  <option value="{{ $sport->id }}" @selected((string) old('sport_id') === (string) $sport->id)>{{ $sport->name }}</option>
                @endforeach
              </select>
              @error('sport_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
              <label for="club_id">Klub (opsional)</label>
              <select id="club_id" name="club_id" class="form-control">
                <option value="">Tanpa klub</option>
                @foreach ($clubs as $club)
                  <option value="{{ $club->id }}" @selected((string) old('club_id') === (string) $club->id)>{{ $club->name }}</option>
                @endforeach
              </select>
            </div>
            <button class="btn btn-primary btn-block py-3">Buat tim</button>
          </form>
        </div>

        <div class="col-lg-8">
          <h4 class="mb-3">Tim yang saya ikuti</h4>
          @forelse ($teams as $team)
            @php
              $canManage = $team->created_by === auth()->id()
                || $team->members->contains(fn ($m) => $m->id === auth()->id() && $m->pivot->role === 'captain');
            @endphp
            <div class="card-block mb-4">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <h5 class="mb-1">{{ $team->name }}</h5>
                  <small class="text-muted">{{ $team->sport->name }}@if ($team->club) · {{ $team->club->name }}@endif</small>
                </div>
                @can('delete', $team)
                  <form method="POST" action="{{ route('teams.destroy', $team->id) }}">@csrf @method('DELETE')
                    <button class="btn btn-outline-danger btn-sm" onclick="return confirm('Hapus tim ini?')">Hapus tim</button>
                  </form>
                @endcan
              </div>

              <div class="d-flex flex-wrap mt-3">
                @foreach ($team->members as $member)
                  <span class="badge badge-light member-chip">
                    {{ $member->name }}@if ($member->pivot->role === 'captain') <small>(kapten)</small>@endif
                    @if ($canManage && $member->id !== $team->created_by)
                      <form method="POST" action="{{ route('teams.members.destroy', [$team->id, $member->id]) }}" class="d-inline">@csrf @method('DELETE')
                        <button class="btn btn-link btn-sm p-0 ml-1 text-danger" title="Keluarkan" onclick="return confirm('Keluarkan anggota ini?')">&times;</button>
                      </form>
                    @endif
                  </span>
                @endforeach
              </div>

              @if ($canManage)
                <form method="POST" action="{{ route('teams.members.store', $team->id) }}" class="form-inline mt-3">@csrf
                  <input type="email" name="email" class="form-control form-control-sm flex-fill mr-2" placeholder="Email anggota (akun Ruangsport)" required>
                  <button class="btn btn-outline-primary btn-sm">Tambah anggota</button>
                </form>
              @endif
            </div>
          @empty
            <p class="text-muted">Anda belum memiliki tim.</p>
          @endforelse
        </div>
      </div>
    </div>
  </div>
@endsection
