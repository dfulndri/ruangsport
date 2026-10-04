@extends('layouts.app')

@section('title', $activity->title)
@section('hero_title', $activity->title)
@section('hero_text', $activity->sport->name.' · '.$activity->starts_at->translatedFormat('l, d F Y H:i'))

@section('content')
  @php($mineStatus = $mine?->status?->value)
  <div class="site-section">
    <div class="container">
      <div class="row">
        <div class="col-lg-7 mb-4">
          <h3>Tentang kegiatan {{ \App\Support\Label::badge('activity', $activity->status) }}</h3>
          <p>{!! nl2br(e($activity->description ?: 'Belum ada deskripsi.')) !!}</p>

          <h4 class="mt-5">Peserta terdaftar ({{ $confirmedCount }})</h4>
          @if ($participants->isEmpty())
            <p class="text-muted">Belum ada peserta.</p>
          @else
            <div class="d-flex flex-wrap">
              @foreach ($participants as $p)<span class="badge badge-light member-chip">{{ $p->user->name }}</span>@endforeach
            </div>
          @endif
        </div>

        <div class="col-lg-5">
          <div class="card-block">
            <ul class="ul-check list-unstyled success mb-4">
              <li>Waktu: {{ $activity->starts_at->translatedFormat('d M Y H:i') }}@if ($activity->ends_at) &ndash; {{ $activity->ends_at->translatedFormat('H:i') }}@endif</li>
              <li>Lokasi: {{ $activity->venue?->name ?? $activity->location_text ?? 'Akan diinformasikan' }}</li>
              <li>Kuota: {{ $confirmedCount }}@if ($activity->quota) / {{ $activity->quota }}@else (tanpa batas)@endif</li>
              <li>Biaya: {{ $activity->fee > 0 ? 'Rp '.number_format($activity->fee, 0, ',', '.') : 'Gratis' }}</li>
              <li>Penyelenggara: {{ $activity->club?->name ?? $activity->organizer->name }}</li>
            </ul>

            @auth
              @if (in_array($mineStatus, ['registered', 'waitlisted', 'attended', 'no_show'], true))
                <p class="mb-2">Status Anda: {{ \App\Support\Label::badge('participant', $mine->status) }}</p>
                @if (in_array($mineStatus, ['registered', 'waitlisted'], true))
                  <form method="POST" action="{{ route('activities.cancel', $activity->slug) }}">@csrf @method('DELETE')
                    <button class="btn btn-outline-danger btn-block" onclick="return confirm('Batalkan pendaftaran?')">Batalkan pendaftaran</button>
                  </form>
                @endif
              @elseif ($activity->status->value === 'open')
                <form method="POST" action="{{ route('activities.rsvp', $activity->slug) }}">@csrf
                  <button class="btn btn-primary btn-block py-3">
                    {{ $activity->quota && $confirmedCount >= $activity->quota ? 'Masuk daftar tunggu' : 'Daftar kegiatan' }}
                  </button>
                </form>
              @else
                <p class="text-muted mb-0">Pendaftaran tidak dibuka.</p>
              @endif
            @else
              <a href="{{ route('login') }}" class="btn btn-primary btn-block py-3">Masuk untuk mendaftar</a>
            @endauth

            @can('manageParticipants', $activity)
              <a href="{{ route('organizer.activities.participants', $activity->slug) }}" class="btn btn-outline-primary btn-block mt-3">Kelola peserta</a>
            @endcan
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
