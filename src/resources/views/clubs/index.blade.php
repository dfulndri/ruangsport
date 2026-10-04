@extends('layouts.app')

@section('title', 'Klub')
@section('hero_title', 'Klub Olahraga')
@section('hero_text', 'Temukan komunitas yang cocok dengan olahragamu.')

@section('content')
  <div class="site-section">
    <div class="container">
      <form method="GET" action="{{ route('clubs.index') }}" class="filter-form mb-4">
        <div class="form-row">
          <div class="col-md-5 mb-2"><input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama klub..."></div>
          <div class="col-md-4 mb-2">
            <select name="sport" class="form-control">
              <option value="">Semua olahraga</option>
              @foreach ($sports as $sport)
                <option value="{{ $sport->id }}" @selected((string) request('sport') === (string) $sport->id)>{{ $sport->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3 mb-2"><button class="btn btn-primary btn-block">Cari</button></div>
        </div>
      </form>

      <div class="row">
        @forelse ($clubs as $club)
          <div class="col-md-6 col-lg-4 mb-4">@include('partials.club-card', ['club' => $club])</div>
        @empty
          <div class="col-12"><p class="text-muted">Tidak ada klub yang cocok. Coba ubah pencarian.</p></div>
        @endforelse
      </div>
      <div class="d-flex justify-content-center">{{ $clubs->links() }}</div>
    </div>
  </div>
@endsection
