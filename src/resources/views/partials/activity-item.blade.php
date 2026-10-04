{{-- Variabel: $activity (dengan sport; confirmed_count opsional) --}}
<div class="card-block mb-3">
  <div class="d-flex justify-content-between align-items-start">
    <h5 class="mb-1"><a href="{{ route('activities.show', $activity->slug) }}">{{ $activity->title }}</a></h5>
    {{ \App\Support\Label::badge('activity', $activity->status) }}
  </div>
  <div class="text-muted small">
    {{ $activity->sport->name }} &middot; {{ $activity->starts_at->translatedFormat('l, d M Y H:i') }}
    @if ($activity->location_text) &middot; {{ $activity->location_text }}@endif
  </div>
  <div class="small mt-2">
    @if (isset($activity->confirmed_count))
      {{ $activity->confirmed_count }}@if ($activity->quota) / {{ $activity->quota }}@endif peserta &middot;
    @endif
    {{ $activity->fee > 0 ? 'Rp '.number_format($activity->fee, 0, ',', '.') : 'Gratis' }}
  </div>
</div>
