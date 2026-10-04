@extends('layouts.app')

@section('title', 'Ubah Aktivitas')
@section('hero_title', 'Ubah Aktivitas')
@section('hero_text', $activity->title)

@section('content')
  <div class="site-section">
    <div class="container">
      @include('partials.panel-menu')
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <form method="POST" action="{{ route('organizer.activities.update', $activity->slug) }}" class="auth-card">
            @csrf @method('PUT')
            @include('organizer.activities._form')
            <button class="btn btn-primary py-3 px-5">Simpan perubahan</button>
            <a href="{{ route('organizer.activities.index') }}" class="btn btn-link">Batal</a>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
