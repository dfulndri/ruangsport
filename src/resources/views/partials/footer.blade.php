<footer class="site-footer py-5">
  <div class="container">
    <div class="row">
      <div class="col-md-6 mb-3 mb-md-0">
        <strong>Ruangsport.</strong>
        <p class="mb-0 text-muted">Temukan klub, aktivitas, kompetisi, dan venue olahraga di sekitarmu.</p>
      </div>
      <div class="col-md-6 text-md-right">
        <a href="{{ route('clubs.index') }}" class="mr-3">Klub</a>
        <a href="{{ route('activities.index') }}" class="mr-3">Aktivitas</a>
        <a href="{{ route('competitions.index') }}" class="mr-3">Kompetisi</a>
        <a href="{{ route('venues.index') }}">Venue</a>
        <p class="small text-muted mt-3 mb-0">&copy; {{ date('Y') }} Ruangsport. Template dasar: Stamina by Free-Template.co</p>
      </div>
    </div>
  </div>
</footer>
