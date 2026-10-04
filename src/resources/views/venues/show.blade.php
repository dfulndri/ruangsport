@extends('layouts.app')

@section('title', $venue->name)
@section('hero_title', $venue->name)
@section('hero_text', $venue->location?->name ?? 'Tangerang')

@section('content')
  <div class="site-section">
    <div class="container">
      <div class="row">
        <div class="col-lg-7 mb-4">
          <img src="{{ \App\Support\Placeholder::cover($venue->id + 3) }}" alt="{{ $venue->name }}" class="img-fluid mb-4">
          <h3>Tentang venue @if ($venue->is_verified)<span class="badge badge-success align-middle">Terverifikasi</span>@endif</h3>
          <p>{!! nl2br(e($venue->description ?: 'Belum ada deskripsi.')) !!}</p>

          @if ($venue->facilities->isNotEmpty())
            <h4 class="mt-4">Fasilitas</h4>
            <ul class="ul-check list-unstyled success">
              @foreach ($venue->facilities as $facility)<li>{{ $facility->name }}</li>@endforeach
            </ul>
          @endif
        </div>

        <div class="col-lg-5">
          <div class="card-block">
            <ul class="list-unstyled mb-0">
              @if ($venue->address)<li class="mb-2"><strong>Alamat</strong><br>{{ $venue->address }}</li>@endif
              @if ($venue->sports->isNotEmpty())<li class="mb-2"><strong>Olahraga</strong><br>{{ $venue->sports->pluck('name')->implode(', ') }}</li>@endif
              @if ($venue->owner)<li class="mb-2"><strong>Pengelola</strong><br>{{ $venue->owner->name }}</li>@endif
              @if (! empty($venue->opening_hours))
                <li class="mb-2"><strong>Jam operasional</strong>
                  @foreach ($venue->opening_hours as $day => $time)<br>{{ $day }}: {{ $time }}@endforeach
                </li>
              @endif
            </ul>
            @if ($venue->latitude && $venue->longitude)
              <a target="_blank" rel="noopener" class="btn btn-outline-primary btn-block mt-3" href="https://www.google.com/maps?q={{ $venue->latitude }},{{ $venue->longitude }}">Buka di Google Maps</a>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
