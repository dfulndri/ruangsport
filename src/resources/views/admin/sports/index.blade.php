@extends('layouts.app')

@section('title', 'Data Olahraga')
@section('hero_title', 'Data Olahraga')

@section('content')
  <div class="site-section">
    <div class="container">
      @include('partials.panel-menu')

      <div class="row">
        <div class="col-lg-4 mb-4">
          <form method="POST" action="{{ route('admin.sports.store') }}" class="auth-card">@csrf
            <h5 class="mb-3">Tambah olahraga</h5>
            <div class="form-group">
              <label for="name">Nama</label>
              <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
              @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
              <label for="participation_type">Jenis peserta</label>
              <select id="participation_type" name="participation_type" class="form-control @error('participation_type') is-invalid @enderror">
                @foreach (['individual' => 'Individu', 'team' => 'Tim', 'both' => 'Individu & tim'] as $v => $t)
                  <option value="{{ $v }}" @selected(old('participation_type', 'both') === $v)>{{ $t }}</option>
                @endforeach
              </select>
              @error('participation_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <button class="btn btn-primary btn-block">Tambah</button>
          </form>
        </div>
        <div class="col-lg-8">
          <div class="table-responsive">
            <table class="table table-hover">
              <thead><tr><th>Olahraga</th><th>Jenis peserta</th><th></th></tr></thead>
              <tbody>
                @foreach ($sports as $sport)
                  <tr>
                    <td>{{ $sport->name }}</td>
                    <td>{{ \App\Support\Label::text('participation', $sport->participation_type) }}</td>
                    <td class="text-right">
                      <form method="POST" action="{{ route('admin.sports.destroy', $sport->id) }}">@csrf @method('DELETE')
                        <button class="btn btn-outline-danger btn-sm" onclick="return confirm('Hapus olahraga ini?')">Hapus</button>
                      </form>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
