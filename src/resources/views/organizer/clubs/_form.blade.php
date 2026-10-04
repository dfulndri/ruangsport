{{-- Variabel: $club (opsional), $sports, $locations --}}
<div class="form-group">
  <label for="name">Nama klub</label>
  <input type="text" id="name" name="name" value="{{ old('name', $club->name ?? '') }}" class="form-control @error('name') is-invalid @enderror" required>
  @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="form-row">
  <div class="form-group col-md-6">
    <label for="sport_id">Olahraga</label>
    <select id="sport_id" name="sport_id" class="form-control @error('sport_id') is-invalid @enderror" required>
      <option value="">Pilih olahraga</option>
      @foreach ($sports as $sport)
        <option value="{{ $sport->id }}" @selected((string) old('sport_id', $club->sport_id ?? '') === (string) $sport->id)>{{ $sport->name }}</option>
      @endforeach
    </select>
    @error('sport_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="form-group col-md-6">
    <label for="location_id">Lokasi</label>
    <select id="location_id" name="location_id" class="form-control @error('location_id') is-invalid @enderror">
      @include('partials.location-options', ['locations' => $locations, 'selected' => old('location_id', $club->location_id ?? '')])
    </select>
    @error('location_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
</div>

<div class="form-group">
  <label for="address">Alamat / titik kumpul</label>
  <input type="text" id="address" name="address" value="{{ old('address', $club->address ?? '') }}" class="form-control @error('address') is-invalid @enderror">
  @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="form-group">
  <label for="description">Deskripsi</label>
  <textarea id="description" name="description" rows="5" class="form-control @error('description') is-invalid @enderror">{{ old('description', $club->description ?? '') }}</textarea>
  @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
