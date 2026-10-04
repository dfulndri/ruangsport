@extends('layouts.app')

@section('title', 'Aktivitas')
@section('hero_title', 'Aktivitas Olahraga')
@section('hero_text', 'Daftar kegiatan yang akan datang.')

@section('content')
  <div class="site-section">
    <div class="container">
      <form method="GET" action="{{ route('activities.index') }}" class="filter-form mb-4">
        <div class="form-row">
          <div class="col-md-5 mb-2"><input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari judul kegiatan..."></div>
          <div class="col-md-4 mb-2">
            <select name="sport" class="form-control">
              <option value="">Semua olahraga</option>
              @foreach ($sports as $sport)
                <option value="{{ $sport->id }}" @selected((string) request('sport') === (string) $sport->id)>{{ $sport->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3 mb-2"><button class="btn btn-primary btn-block">Cari</button></div>
        </div>
      </form>

      @forelse ($activities as $activity)
        @include('partials.activity-item', ['activity' => $activity])
      @empty
        <p class="text-muted">Tidak ada aktivitas mendatang yang cocok.</p>
      @endforelse
      <div class="d-flex justify-content-center">{{ $activities->links() }}</div>
    </div>
  </div>
@endsection
