@extends('layouts.app')

@section('title', $competition->name)
@section('hero_title', $competition->name)
@section('hero_text', $competition->sport->name.' · '.($competition->isIndividual() ? 'Peserta individu' : 'Peserta tim').' · Sistem gugur')

@section('content')
  @php($statusLabel = ['registration_open' => 'Pendaftaran dibuka', 'ongoing' => 'Berlangsung', 'finished' => 'Selesai'][$competition->status->value] ?? $competition->status->value)
  <div class="site-section">
    <div class="container">
      <div class="row mb-5">
        <div class="col-lg-7 mb-4">
          <h3>Tentang kompetisi</h3>
          <p>{{ $competition->description ?: 'Belum ada deskripsi.' }}</p>
          @if ($competition->rules)
            <h4 class="mt-4">Peraturan</h4>
            <p>{!! nl2br(e($competition->rules)) !!}</p>
          @endif
        </div>
        <div class="col-lg-5">
          <div class="card-block">
            <ul class="ul-check list-unstyled success mb-0">
              <li>Status: {{ $statusLabel }}</li>
              <li>Peserta: {{ $entries->count() }}@if ($competition->max_participants) / {{ $competition->max_participants }}@endif {{ $competition->isIndividual() ? 'orang' : 'tim' }}</li>
              @if ($competition->registration_close_at)<li>Pendaftaran ditutup: {{ $competition->registration_close_at->translatedFormat('d F Y') }}</li>@endif
              @if ($competition->starts_at)<li>Mulai: {{ $competition->starts_at->translatedFormat('d F Y') }}</li>@endif
              <li>Penyelenggara: {{ $competition->organizer->name }}</li>
              @if ($competition->venue)<li>Venue: {{ $competition->venue->name }}</li>@endif
            </ul>

            @if ($canManage)
              <a href="{{ route('organizer.competitions.manage', $competition->slug) }}" class="btn btn-outline-primary btn-block mt-4">Kelola Kompetisi</a>
            @endif

            @auth
              @if ($myEntries->isNotEmpty())
                <div class="mt-4">
                  @foreach ($myEntries as $my)
                    <p class="mb-2">
                      {{ $my->team ? $my->team->name : 'Anda' }}:
                      {{ \App\Support\Label::badge('entry', $my->status) }}
                    </p>
                    @if (in_array($my->status->value, ['pending', 'verified'], true) && $rounds->isEmpty())
                      <form method="POST" action="{{ route('competitions.withdraw', $competition->slug) }}">@csrf @method('DELETE')
                        @if ($my->team_id)<input type="hidden" name="team_id" value="{{ $my->team_id }}">@endif
                        <button class="btn btn-outline-danger btn-sm btn-block mb-2" onclick="return confirm('Batalkan pendaftaran?')">Batalkan pendaftaran{{ $my->team ? ' '.$my->team->name : '' }}</button>
                      </form>
                    @endif
                  @endforeach
                </div>
              @endif

              @if ($canRegister)
                @php
                  $activeStatuses = [\App\Enums\EntryStatus::Pending, \App\Enums\EntryStatus::Verified];
                  $registeredTeamIds = $myEntries->whereIn('status', $activeStatuses)->pluck('team_id')->all();
                @endphp
                @if ($competition->isIndividual())
                  @if ($myEntries->whereIn('status', $activeStatuses)->isEmpty())
                    <form method="POST" action="{{ route('competitions.register', $competition->slug) }}" class="mt-4">@csrf
                      <button class="btn btn-primary btn-block py-3">Daftar Kompetisi</button>
                    </form>
                  @endif
                @else
                  @php
                    $availableTeams = $myTeams->reject(fn ($t) => in_array($t->id, $registeredTeamIds, true));
                  @endphp
                  @if ($availableTeams->isNotEmpty())
                    <form method="POST" action="{{ route('competitions.register', $competition->slug) }}" class="mt-4">@csrf
                      <div class="form-group">
                        <label for="team_id">Daftarkan tim</label>
                        <select name="team_id" id="team_id" class="form-control" required>
                          @foreach ($availableTeams as $team)
                            <option value="{{ $team->id }}">{{ $team->name }}</option>
                          @endforeach
                        </select>
                      </div>
                      <button class="btn btn-primary btn-block py-3">Daftar Kompetisi</button>
                    </form>
                  @elseif ($myTeams->isEmpty())
                    <p class="small text-muted mt-4 mb-0">Kompetisi ini untuk tim. <a href="{{ route('teams.index') }}">Buat tim</a> {{ $competition->sport->name }} Anda terlebih dahulu.</p>
                  @endif
                @endif
              @elseif ($competition->status->value === 'registration_open')
                <p class="small text-muted mt-4 mb-0">Pendaftaran belum atau sudah tidak dibuka pada waktu ini.</p>
              @endif
            @else
              @if ($competition->status->value === 'registration_open')
                <a href="{{ route('login') }}" class="btn btn-primary btn-block py-3 mt-4">Masuk untuk Mendaftar</a>
              @endif
            @endauth
          </div>
        </div>
      </div>

      <h3 class="mb-4">Bracket</h3>
      @if ($rounds->isEmpty())
        <p class="text-muted">Bracket akan tampil setelah pendaftaran ditutup dan jadwal dibuat.</p>
      @else
        <div class="bracket">
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
                  @if ($m->scheduled_at)
                    <div class="bracket-meta">{{ $m->scheduled_at->translatedFormat('d M, H:i') }}@if ($m->court) &middot; {{ $m->court }}@endif</div>
                  @endif
                </div>
              @endforeach
            </div>
          @endforeach
        </div>
      @endif

      <h3 class="mt-5 mb-4">Daftar peserta terverifikasi</h3>
      @if ($entries->isEmpty())
        <p class="text-muted">Belum ada peserta terverifikasi.</p>
      @else
        <div class="d-flex flex-wrap">
          @foreach ($entries as $entry)
            <span class="badge badge-light member-chip">{{ $entry->display_name }}</span>
          @endforeach
        </div>
      @endif
    </div>
  </div>
@endsection
