{{-- Variabel: $competition (opsional), $sports, $clubs, $venues, $locked (opsional) --}}
@php($locked = $locked ?? false)

<div class="form-group">
  <label for="name">Nama kompetisi</label>
  <input type="text" id="name" name="name" value="{{ old('name', $competition->name ?? '') }}" class="form-control @error('name') is-invalid @enderror" required>
  @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="form-row">
  <div class="form-group col-md-6">
    <label for="sport_id">Olahraga</label>
    <select id="sport_id" name="sport_id" class="form-control @error('sport_id') is-invalid @enderror" @disabled($locked) required>
      <option value="">Pilih olahraga</option>
      @foreach ($sports as $sport)
        <option value="{{ $sport->id }}" @selected((string) old('sport_id', $competition->sport_id ?? '') === (string) $sport->id)>{{ $sport->name }} ({{ \App\Support\Label::text('participation', $sport->participation_type) }})</option>
      @endforeach
    </select>
    @if ($locked)<input type="hidden" name="sport_id" value="{{ $competition->sport_id }}">@endif
    @error('sport_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="form-group col-md-6">
    <label for="participant_type">Jenis peserta</label>
    <select id="participant_type" name="participant_type" class="form-control @error('participant_type') is-invalid @enderror" @disabled($locked) required>
      @foreach (['individual' => 'Individu (perorangan)', 'team' => 'Tim (beregu)'] as $value => $text)
        <option value="{{ $value }}" @selected(old('participant_type', isset($competition) ? $competition->participant_type->value : 'individual') === $value)>{{ $text }}</option>
      @endforeach
    </select>
    @if ($locked)<input type="hidden" name="participant_type" value="{{ $competition->participant_type->value }}">@endif
    @if ($locked)<small class="text-muted">Tidak dapat diubah karena sudah ada peserta.</small>@endif
    @error('participant_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
</div>

<div class="form-row">
  <div class="form-group col-md-6">
    <label for="club_id">Klub penyelenggara</label>
    <select id="club_id" name="club_id" class="form-control @error('club_id') is-invalid @enderror">
      <option value="">Tanpa klub</option>
      @foreach ($clubs as $club)
        <option value="{{ $club->id }}" @selected((string) old('club_id', $competition->club_id ?? '') === (string) $club->id)>{{ $club->name }}</option>
      @endforeach
    </select>
    @error('club_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="form-group col-md-6">
    <label for="venue_id">Venue (opsional)</label>
    <select id="venue_id" name="venue_id" class="form-control @error('venue_id') is-invalid @enderror">
      <option value="">Tidak memilih venue</option>
      @foreach ($venues as $venue)
        <option value="{{ $venue->id }}" @selected((string) old('venue_id', $competition->venue_id ?? '') === (string) $venue->id)>{{ $venue->name }}</option>
      @endforeach
    </select>
    @error('venue_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
</div>

<div class="form-row">
  <div class="form-group col-md-6">
    <label for="registration_open_at">Pendaftaran dibuka</label>
    <input type="datetime-local" id="registration_open_at" name="registration_open_at" value="{{ old('registration_open_at', isset($competition) && $competition->registration_open_at ? $competition->registration_open_at->format('Y-m-d\TH:i') : '') }}" class="form-control @error('registration_open_at') is-invalid @enderror">
    @error('registration_open_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="form-group col-md-6">
    <label for="registration_close_at">Pendaftaran ditutup</label>
    <input type="datetime-local" id="registration_close_at" name="registration_close_at" value="{{ old('registration_close_at', isset($competition) && $competition->registration_close_at ? $competition->registration_close_at->format('Y-m-d\TH:i') : '') }}" class="form-control @error('registration_close_at') is-invalid @enderror">
    @error('registration_close_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
</div>

<div class="form-row">
  <div class="form-group col-md-4">
    <label for="starts_at">Tanggal mulai</label>
    <input type="date" id="starts_at" name="starts_at" value="{{ old('starts_at', isset($competition) && $competition->starts_at ? $competition->starts_at->format('Y-m-d') : '') }}" class="form-control @error('starts_at') is-invalid @enderror">
    @error('starts_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="form-group col-md-4">
    <label for="ends_at">Tanggal selesai</label>
    <input type="date" id="ends_at" name="ends_at" value="{{ old('ends_at', isset($competition) && $competition->ends_at ? $competition->ends_at->format('Y-m-d') : '') }}" class="form-control @error('ends_at') is-invalid @enderror">
    @error('ends_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="form-group col-md-4">
    <label for="max_participants">Maksimal peserta</label>
    <input type="number" min="2" max="128" id="max_participants" name="max_participants" value="{{ old('max_participants', $competition->max_participants ?? '') }}" class="form-control @error('max_participants') is-invalid @enderror">
    @error('max_participants')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
</div>

<div class="form-group">
  <label for="status">Status</label>
  <select id="status" name="status" class="form-control @error('status') is-invalid @enderror" required>
    @foreach (['draft', 'registration_open', 'ongoing', 'finished'] as $s)
      <option value="{{ $s }}" @selected(old('status', isset($competition) ? $competition->status->value : 'draft') === $s)>{{ \App\Support\Label::text('competition', $s) }}</option>
    @endforeach
  </select>
  <small class="text-muted">Format kompetisi: sistem gugur (knockout). Status "Berlangsung" otomatis aktif saat bracket dibuat.</small>
  @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="form-group">
  <label for="description">Deskripsi</label>
  <textarea id="description" name="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description', $competition->description ?? '') }}</textarea>
  @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="form-group">
  <label for="rules">Peraturan</label>
  <textarea id="rules" name="rules" rows="4" class="form-control @error('rules') is-invalid @enderror">{{ old('rules', $competition->rules ?? '') }}</textarea>
  @error('rules')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
