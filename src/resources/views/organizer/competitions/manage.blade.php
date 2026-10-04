@extends('layouts.app')

@section('title', 'Kelola '.$competition->name)
@section('hero_title', 'Kelola Kompetisi')
@section('hero_text', $competition->name)

@section('content')
  <div class="site-section">
    <div class="container">
      @include('partials.panel-menu')

      <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
          <a href="{{ route('organizer.competitions.index') }}">&larr; Daftar kompetisi</a>
          <span class="mx-2">|</span>
          {{ \App\Support\Label::badge('competition', $competition->status) }}
          <span class="text-muted ml-2">{{ $competition->sport->name }} · {{ $competition->participant_type->value === 'team' ? 'Peserta tim' : 'Peserta individu' }}</span>
        </div>
        <div>
          <a href="{{ route('competitions.show', $competition->slug) }}" class="btn btn-outline-secondary btn-sm">Lihat halaman publik</a>
          <a href="{{ route('organizer.competitions.edit', $competition->slug) }}" class="btn btn-outline-primary btn-sm">Ubah kompetisi</a>
        </div>
      </div>

      {{-- ============ PESERTA ============ --}}
      <h3 class="mb-3">Peserta</h3>
      @if ($entries->isEmpty())
        <p class="text-muted mb-5">Belum ada pendaftar. Pastikan status kompetisi "Pendaftaran dibuka".</p>
      @else
        <div class="table-responsive mb-5">
          <table class="table table-hover">
            <thead><tr><th>#</th><th>Peserta</th><th>Seed</th><th>Status</th><th></th></tr></thead>
            <tbody>
              @foreach ($entries as $i => $entry)
                <tr>
                  <td>{{ $i + 1 }}</td>
                  <td>
                    {{ $entry->display_name }}
                    @if ($entry->team)<br><small class="text-muted">{{ $entry->team->members->count() }} anggota</small>@endif
                  </td>
                  <td>{{ $entry->seed ?? '-' }}</td>
                  <td>{{ \App\Support\Label::badge('entry', $entry->status) }}</td>
                  <td class="text-right text-nowrap">
                    @if ($rounds->isEmpty() && in_array($entry->status->value, ['pending', 'verified', 'rejected'], true))
                      @if ($entry->status->value !== 'verified')
                        <form method="POST" action="{{ route('organizer.competitions.entries.update', [$competition->slug, $entry->id]) }}" class="d-inline">@csrf @method('PATCH')
                          <input type="hidden" name="status" value="verified">
                          <button class="btn btn-success btn-sm">Verifikasi</button>
                        </form>
                      @endif
                      @if ($entry->status->value !== 'rejected')
                        <form method="POST" action="{{ route('organizer.competitions.entries.update', [$competition->slug, $entry->id]) }}" class="d-inline">@csrf @method('PATCH')
                          <input type="hidden" name="status" value="rejected">
                          <button class="btn btn-outline-danger btn-sm">Tolak</button>
                        </form>
                      @endif
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif

      {{-- ============ BRACKET ============ --}}
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Bracket &amp; pertandingan</h3>
        @if ($competition->status->value !== 'finished' && $competition->status->value !== 'draft')
          <form method="POST" action="{{ route('organizer.competitions.bracket', $competition->slug) }}" class="form-inline">@csrf
            <div class="form-check mr-3">
              <input type="checkbox" class="form-check-input" id="reshuffle" name="reshuffle" value="1">
              <label class="form-check-label" for="reshuffle">Acak ulang unggulan</label>
            </div>
            <button class="btn btn-primary btn-sm" onclick="return confirm('{{ $rounds->isEmpty() ? 'Buat bracket dari peserta terverifikasi? Pendaftaran akan ditutup.' : 'Bracket lama akan dibuat ulang. Lanjutkan?' }}')">
              {{ $rounds->isEmpty() ? 'Buat bracket' : 'Buat ulang bracket' }}
            </button>
          </form>
        @endif
      </div>

      @if ($rounds->isEmpty())
        <p class="text-muted">Bracket belum dibuat. Verifikasi minimal 2 peserta, lalu klik "Buat bracket".</p>
      @else
        <div class="bracket mb-5">
          @foreach ($rounds as $round => $matches)
            <div class="bracket-round">
              <h5 class="bracket-title">{{ $roundNames[$round] }}</h5>
              @foreach ($matches as $m)
                <div class="bracket-match">
                  @foreach (['a' => $m->entryA, 'b' => $m->entryB] as $slot => $entry)
                    @php($entryId = $slot === 'a' ? $m->entry_a_id : $m->entry_b_id)
                    <div class="bracket-slot {{ $m->winner_entry_id && $m->winner_entry_id === $entryId ? 'winner' : '' }}">
                      <span>{{ $entry?->display_name ?? ($m->is_bye ? 'BYE' : 'Menunggu') }}</span>
                      <strong>{{ $slot === 'a' ? ($m->score_a ?? '-') : ($m->score_b ?? '-') }}</strong>
                    </div>
                  @endforeach
                </div>
              @endforeach
            </div>
          @endforeach
        </div>

        <h5 class="mb-3">Input skor &amp; jadwal</h5>
        <div class="table-responsive">
          <table class="table table-sm">
            <thead><tr><th>Babak</th><th>#</th><th>Pertandingan</th><th>Skor</th><th>Jadwal</th><th>Lapangan</th><th></th></tr></thead>
            <tbody>
              @foreach ($rounds as $round => $matches)
                @foreach ($matches as $m)
                  @continue($m->is_bye)
                  <tr>
                    <td>
                      {{ $roundNames[$round] }}
                      <form id="match-{{ $m->id }}" method="POST" action="{{ route('organizer.competitions.matches.update', [$competition->slug, $m->id]) }}">@csrf @method('PATCH')</form>
                    </td>
                    <td>{{ $m->match_number }}</td>
                    <td>
                      {{ $m->entryA?->display_name ?? 'Menunggu' }}
                      <span class="text-muted">vs</span>
                      {{ $m->entryB?->display_name ?? 'Menunggu' }}
                    </td>
                    <td class="text-nowrap">
                      @if ($m->entry_a_id && $m->entry_b_id)
                        <input form="match-{{ $m->id }}" type="number" min="0" max="999" name="score_a" value="{{ $m->score_a }}" class="form-control form-control-sm d-inline-block" style="width:64px;">
                        &ndash;
                        <input form="match-{{ $m->id }}" type="number" min="0" max="999" name="score_b" value="{{ $m->score_b }}" class="form-control form-control-sm d-inline-block" style="width:64px;">
                      @else
                        <span class="text-muted">Menunggu peserta</span>
                      @endif
                    </td>
                    <td><input form="match-{{ $m->id }}" type="datetime-local" name="scheduled_at" value="{{ $m->scheduled_at?->format('Y-m-d\TH:i') }}" class="form-control form-control-sm"></td>
                    <td><input form="match-{{ $m->id }}" type="text" name="court" value="{{ $m->court }}" class="form-control form-control-sm" style="min-width:90px;" placeholder="Lap. 1"></td>
                    <td><button form="match-{{ $m->id }}" class="btn btn-outline-primary btn-sm">Simpan</button></td>
                  </tr>
                @endforeach
              @endforeach
            </tbody>
          </table>
        </div>
        <p class="small text-muted">Sistem gugur tidak mengenal seri. Pemenang otomatis masuk ke babak berikutnya; pertandingan final yang selesai menandai kompetisi selesai.</p>
      @endif
    </div>
  </div>
@endsection
