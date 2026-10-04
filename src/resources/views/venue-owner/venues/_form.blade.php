{{-- Variabel: $venue (opsional), $sports, $locations, $facilitiesText, $hoursText (opsional) --}}
<div class="form-group">
  <label for="name">Nama venue</label>
  <input type="text" id="name" name="name" value="{{ old('name', $venue->name ?? '') }}" class="form-control @error('name') is-invalid @enderror" required>
  @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="form-row">
  <div class="form-group col-md-6">
    <label for="location_id">Lokasi</label>
    <select id="location_id" name="location_id" class="form-control @error('location_id') is-invalid @enderror">
      @include('partials.location-options', ['locations' => $locations, 'selected' => old('location_id', $venue->location_id ?? '')])
    </select>
    @error('location_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="form-group col-md-6">
    <label for="address">Alamat</label>
    <input type="text" id="address" name="address" value="{{ old('address', $venue->address ?? '') }}" class="form-control @error('address') is-invalid @enderror">
    @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
</div>

<div class="form-row">
  <div class="form-group col-md-6">
    <label for="latitude">Latitude (opsional)</label>
    <input type="text" id="latitude" name="latitude" value="{{ old('latitude', $venue->latitude ?? '') }}" class="form-control @error('latitude') is-invalid @enderror" placeholder="-6.1783">
    @error('latitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="form-group col-md-6">
    <label for="longitude">Longitude (opsional)</label>
    <input type="text" id="longitude" name="longitude" value="{{ old('longitude', $venue->longitude ?? '') }}" class="form-control @error('longitude') is-invalid @enderror" placeholder="106.6319">
    @error('longitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
</div>

<div class="form-group">
  <label>Jenis olahraga</label>
  @php($selectedSports = collect(old('sports', isset($venue) ? $venue->sports->pluck('id')->all() : []))->map(fn ($id) => (int) $id))
  <div class="d-flex flex-wrap">
    @foreach ($sports as $sport)
      <div class="form-check mr-4">
        <input class="form-check-input" type="checkbox" name="sports[]" id="sport-{{ $sport->id }}" value="{{ $sport->id }}" @checked($selectedSports->contains($sport->id))>
        <label class="form-check-label" for="sport-{{ $sport->id }}">{{ $sport->name }}</label>
      </div>
    @endforeach
  </div>
  @error('sports')<div class="text-danger small">{{ $message }}</div>@enderror
</div>

<div class="form-row">
  <div class="form-group col-md-6">
    <label for="facilities">Fasilitas (satu per baris)</label>
    <textarea id="facilities" name="facilities" rows="5" class="form-control @error('facilities') is-invalid @enderror" placeholder="Parkir luas&#10;Kantin&#10;Toilet">{{ old('facilities', $facilitiesText ?? '') }}</textarea>
    @error('facilities')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="form-group col-md-6">
    <label for="opening_hours">Jam operasional (satu hari per baris)</label>
    <textarea id="opening_hours" name="opening_hours" rows="5" class="form-control @error('opening_hours') is-invalid @enderror" placeholder="Senin - Jumat: 08.00 - 22.00&#10;Sabtu - Minggu: 07.00 - 23.00">{{ old('opening_hours', $hoursText ?? '') }}</textarea>
    <small class="text-muted">Format: <code>Hari: jam</code></small>
    @error('opening_hours')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
</div>

<div class="form-group">
  <label for="description">Deskripsi</label>
  <textarea id="description" name="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description', $venue->description ?? '') }}</textarea>
  @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
