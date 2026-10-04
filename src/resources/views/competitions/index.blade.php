@extends('layouts.app')

@section('title', 'Kompetisi')
@section('hero_title', 'Kompetisi')
@section('hero_text', 'Turnamen sistem gugur untuk peserta individu maupun tim.')

@section('content')
  <div class="site-section">
    <div class="container">
      <div class="row">
        @forelse ($competitions as $competition)
          <div class="col-md-6 col-lg-4 mb-4">@include('partials.competition-card', ['competition' => $competition])</div>
        @empty
          <div class="col-12"><p class="text-muted">Belum ada kompetisi.</p></div>
        @endforelse
      </div>
      <div class="d-flex justify-content-center">{{ $competitions->links() }}</div>
    </div>
  </div>
@endsection
