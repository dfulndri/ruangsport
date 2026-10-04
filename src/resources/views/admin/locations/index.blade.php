@extends('layouts.app')

@section('title', 'Data Lokasi')
@section('hero_title', 'Data Lokasi')

@section('content')
  <div class="site-section">
    <div class="container">
      @include('partials.panel-menu')

      <div class="row">
        <div class="col-lg-4 mb-4">
          <form method="POST" action="{{ route('admin.locations.store') }}" class="auth-card">@csrf
            <h5 class="mb-3">Tambah lokasi</h5>
            <div class="form-group">
              <label for="name">Nama</label>
              <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
              @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
              <label for="type">Jenis</label>
              <select id="type" name="type" class="form-control">
                @foreach (['kota' => 'Kota', 'kabupaten' => 'Kabupaten', 'kecamatan' => 'Kecamatan'] as $v => $t)
                  <option value="{{ $v }}" @selected(old('type', 'kecamatan') === $v)>{{ $t }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="parent_id">Berada di (untuk kecamatan)</label>
              <select id="parent_id" name="parent_id" class="form-control">
                <option value="">— Tingkat atas (kota/kabupaten) —</option>
                @foreach ($cities as $city)
                  <option value="{{ $city->id }}" @selected((string) old('parent_id') === (string) $city->id)>{{ $city->name }}</option>
                @endforeach
              </select>
            </div>
            <button class="btn btn-primary btn-block">Tambah</button>
          </form>
        </div>
        <div class="col-lg-8">
          @foreach ($cities as $city)
            <div class="card-block mb-3">
              <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ $city->name }} <small class="text-muted">({{ $city->children->count() }} kecamatan)</small></h5>
                <form method="POST" action="{{ route('admin.locations.destroy', $city->id) }}">@csrf @method('DELETE')
                  <button class="btn btn-outline-danger btn-sm" onclick="return confirm('Hapus {{ $city->name }}?')">Hapus</button>
                </form>
              </div>
              <div class="d-flex flex-wrap mt-3">
                @foreach ($city->children->sortBy('name') as $district)
                  <span class="badge badge-light member-chip">
                    {{ $district->name }}
                    <form method="POST" action="{{ route('admin.locations.destroy', $district->id) }}" class="d-inline">@csrf @method('DELETE')
                      <button class="btn btn-link btn-sm p-0 ml-1 text-danger" title="Hapus" onclick="return confirm('Hapus {{ $district->name }}?')">&times;</button>
                    </form>
                  </span>
                @endforeach
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
@endsection
