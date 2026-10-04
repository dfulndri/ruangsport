@extends('layouts.app')

@section('title', 'Venue')
@section('hero_title', 'Venue Olahraga')
@section('hero_text', 'Cari lapangan dan tempat berolahraga.')

@section('content')
  <div class="site-section">
    <div class="container">
      <form method="GET" action="{{ route('venues.index') }}" class="filter-form mb-4">
        <div class="form-row">
          <div class="col-md-5 mb-2"><input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama venue..."></div>
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
        @forelse ($venues as $venue)
          <div class="col-md-6 col-lg-4 mb-4">
            <div class="card-block h-100">
              <img src="{{ \App\Support\Placeholder::cover($venue->id + 3, 600, 260) }}" alt="{{ $venue->name }}" class="img-fluid mb-3">
              <h5 class="mb-1"><a href="{{ route('venues.show', $venue->slug) }}">{{ $venue->name }}</a>
                @if ($venue->is_verified)<span class="badge badge-success align-middle">Terverifikasi</span>@endif
              </h5>
              <div class="text-muted small mb-2">{{ $venue->location?->name ?? 'Tangerang' }}@if ($venue->sports->isNotEmpty()) &middot; {{ $venue->sports->pluck('name')->implode(', ') }}@endif</div>
              <p class="mb-0">{{ \Illuminate\Support\Str::limit($venue->description, 110) ?: 'Belum ada deskripsi.' }}</p>
            </div>
          </div>
        @empty
          <div class="col-12"><p class="text-muted">Tidak ada venue yang cocok.</p></div>
        @endforelse
      </div>
      <div class="d-flex justify-content-center">{{ $venues->links() }}</div>
    </div>
  </div>
@endsection
