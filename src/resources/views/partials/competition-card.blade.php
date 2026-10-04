{{-- Variabel: $competition (dengan sport, verified_count) --}}
<div class="card-block h-100">
  <div class="d-flex justify-content-between align-items-start">
    <h5 class="mb-1"><a href="{{ route('competitions.show', $competition->slug) }}">{{ $competition->name }}</a></h5>
    {{ \App\Support\Label::badge('competition', $competition->status) }}
  </div>
  <div class="text-muted small mb-2">{{ $competition->sport->name }} &middot; {{ \App\Support\Label::text('participation', $competition->participant_type) }} &middot; Sistem gugur</div>
  <p class="mb-1">{{ $competition->verified_count }}@if ($competition->max_participants) / {{ $competition->max_participants }}@endif peserta terverifikasi</p>
  @if ($competition->starts_at)<p class="small text-muted mb-0">Mulai {{ $competition->starts_at->translatedFormat('d F Y') }}</p>@endif
</div>
