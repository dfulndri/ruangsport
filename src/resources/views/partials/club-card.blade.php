{{-- Variabel: $club (dengan sport, location, members_count) --}}
<div class="card-block h-100">
  <img src="{{ \App\Support\Placeholder::cover($club->id, 600, 260) }}" alt="{{ $club->name }}" class="img-fluid mb-3">
  <h5 class="mb-1"><a href="{{ route('clubs.show', $club->slug) }}">{{ $club->name }}</a>
    @if ($club->is_verified)<span class="badge badge-success align-middle">Terverifikasi</span>@endif
  </h5>
  <div class="text-muted small mb-2">{{ $club->sport->name }} &middot; {{ $club->location?->name ?? 'Tangerang' }} &middot; {{ $club->members_count }} anggota</div>
  <p class="mb-0">{{ \Illuminate\Support\Str::limit($club->description, 110) ?: 'Belum ada deskripsi.' }}</p>
</div>
