@extends('layouts.app')

@section('title', $club->name)
@section('hero_title', $club->name)
@section('hero_text', $club->sport->name.' · '.($club->location?->name ?? 'Tangerang'))

@section('content')
  <div class="site-section">
    <div class="container">
      <div class="row mb-5">
        <div class="col-lg-7 mb-4">
          <img src="{{ \App\Support\Placeholder::cover($club->id) }}" alt="{{ $club->name }}" class="img-fluid mb-4">
          <h3>Tentang klub
            @if ($club->is_verified)<span class="badge badge-success align-middle">Terverifikasi</span>@endif
          </h3>
          <p>{{ $club->description ?: 'Belum ada deskripsi.' }}</p>
        </div>

        <div class="col-lg-5">
          <div class="card-block">
            <ul class="ul-check list-unstyled success mb-4">
              <li>Olahraga: {{ $club->sport->name }}</li>
              <li>Lokasi: {{ $club->location?->name ?? 'Tangerang' }}@if ($club->address), {{ $club->address }}@endif</li>
              <li>Pengelola: {{ $club->owner->name }}</li>
              <li>{{ $club->members_count }} anggota</li>
            </ul>

            @can('update', $club)
              <a href="{{ route('organizer.clubs.members', $club->slug) }}" class="btn btn-outline-primary btn-block mb-3">Kelola Klub &amp; Anggota</a>
            @endcan

            @auth
              @if ($membership?->status?->value === 'approved')
                <p class="text-success mb-2">Anda anggota klub ini.</p>
              @elseif ($membership?->status?->value === 'pending')
                <p class="text-muted mb-2">Permintaan bergabung menunggu persetujuan.</p>
              @elseif ($membership?->status?->value === 'rejected')
                <p class="text-danger mb-2">Permintaan sebelumnya ditolak. Anda dapat mengajukan lagi.</p>
              @endif

              @if (! $membership || $membership->status->value === 'rejected')
                <form method="POST" action="{{ route('clubs.join', $club->slug) }}">@csrf
                  <button class="btn btn-primary btn-block py-3">Ajukan Bergabung</button>
                </form>
              @elseif ($membership->role->value !== 'owner')
                <form method="POST" action="{{ route('clubs.leave', $club->slug) }}">@csrf @method('DELETE')
                  <button class="btn btn-outline-danger btn-block"
                    onclick="return confirm('Yakin ingin keluar / membatalkan pengajuan?')">
                    {{ $membership->status->value === 'pending' ? 'Batalkan Pengajuan' : 'Keluar dari Klub' }}
                  </button>
                </form>
              @endif
            @else
              <a href="{{ route('login') }}" class="btn btn-primary btn-block py-3">Masuk untuk Bergabung</a>
            @endauth
          </div>
        </div>
      </div>

      <h3 class="mb-4">Aktivitas mendatang</h3>
      @if ($activities->isEmpty())
        <p class="text-muted">Belum ada aktivitas mendatang dari klub ini.</p>
      @else
        <div class="row">
          @foreach ($activities->chunk(3) as $chunk)
            <div class="col-lg-6">
              @foreach ($chunk as $activity)
                @include('partials.activity-item', ['activity' => $activity])
              @endforeach
            </div>
          @endforeach
        </div>
      @endif

      @if ($members->isNotEmpty())
        <h3 class="mt-5 mb-4">Anggota</h3>
        <div class="d-flex flex-wrap">
          @foreach ($members as $m)
            <span class="badge badge-light member-chip">{{ $m->user->name }}@if ($m->role->canManage()) <small>({{ $m->role->value }})</small>@endif</span>
          @endforeach
        </div>
      @endif
    </div>
  </div>
@endsection
