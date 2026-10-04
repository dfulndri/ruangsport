{{-- Variabel: $activity (opsional), $sports, $clubs, $venues --}}
<div class="form-group">
  <label for="title">Judul kegiatan</label>
  <input type="text" id="title" name="title" value="{{ old('title', $activity->title ?? '') }}" class="form-control @error('title') is-invalid @enderror" required>
  @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="form-row">
  <div class="form-group col-md-6">
    <label for="sport_id">Olahraga</label>
    <select id="sport_id" name="sport_id" class="form-control @error('sport_id') is-invalid @enderror" required>
      <option value="">Pilih olahraga</option>
      @foreach ($sports as $sport)
        <option value="{{ $sport->id }}" @selected((string) old('sport_id', $activity->sport_id ?? '') === (string) $sport->id)>{{ $sport->name }}</option>
      @endforeach
    </select>
    @error('sport_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="form-group col-md-6">
    <label for="club_id">Klub penyelenggara</label>
    <select id="club_id" name="club_id" class="form-control @error('club_id') is-invalid @enderror">
      <option value="">Tanpa klub</option>
      @foreach ($clubs as $club)
        <option value="{{ $club->id }}" @selected((string) old('club_id', $activity->club_id ?? '') === (string) $club->id)>{{ $club->name }}</option>
      @endforeach
    </select>
    @error('club_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
</div>

<div class="form-row">
  <div class="form-group col-md-6">
    <label for="venue_id">Venue (opsional)</label>
    <select id="venue_id" name="venue_id" class="form-control @error('venue_id') is-invalid @enderror">
      <option value="">Tidak memilih venue</option>
      @foreach ($venues as $venue)
        <option value="{{ $venue->id }}" @selected((string) old('venue_id', $activity->venue_id ?? '') === (string) $venue->id)>{{ $venue->name }}</option>
      @endforeach
    </select>
    @error('venue_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="form-group col-md-6">
    <label for="location_text">Lokasi (teks bebas)</label>
    <input type="text" id="location_text" name="location_text" value="{{ old('location_text', $activity->location_text ?? '') }}" class="form-control @error('location_text') is-invalid @enderror" placeholder="Mis. Gerbang utama taman">
    @error('location_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
</div>

<div class="form-row">
  <div class="form-group col-md-6">
    <label for="starts_at">Waktu mulai</label>
    <input type="datetime-local" id="starts_at" name="starts_at" value="{{ old('starts_at', isset($activity) ? $activity->starts_at->format('Y-m-d\TH:i') : '') }}" class="form-control @error('starts_at') is-invalid @enderror" required>
    @error('starts_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="form-group col-md-6">
    <label for="ends_at">Waktu selesai (opsional)</label>
    <input type="datetime-local" id="ends_at" name="ends_at" value="{{ old('ends_at', isset($activity) && $activity->ends_at ? $activity->ends_at->format('Y-m-d\TH:i') : '') }}" class="form-control @error('ends_at') is-invalid @enderror">
    @error('ends_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
</div>

<div class="form-row">
  <div class="form-group col-md-4">
    <label for="quota">Kuota (kosong = tanpa batas)</label>
    <input type="number" min="1" id="quota" name="quota" value="{{ old('quota', $activity->quota ?? '') }}" class="form-control @error('quota') is-invalid @enderror">
    @error('quota')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="form-group col-md-4">
    <label for="fee">Biaya (Rp, 0 = gratis)</label>
    <input type="number" min="0" id="fee" name="fee" value="{{ old('fee', $activity->fee ?? 0) }}" class="form-control @error('fee') is-invalid @enderror">
    @error('fee')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="form-group col-md-4">
    <label for="status">Status</label>
    <select id="status" name="status" class="form-control @error('status') is-invalid @enderror" required>
      @foreach (['draft', 'open', 'closed', 'finished', 'cancelled'] as $s)
        <option value="{{ $s }}" @selected(old('status', isset($activity) ? $activity->status->value : 'open') === $s)>{{ \App\Support\Label::text('activity', $s) }}</option>
      @endforeach
    </select>
    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
</div>

<div class="form-group">
  <label for="description">Deskripsi</label>
  <textarea id="description" name="description" rows="5" class="form-control @error('description') is-invalid @enderror">{{ old('description', $activity->description ?? '') }}</textarea>
  @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
