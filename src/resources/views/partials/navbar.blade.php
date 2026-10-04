<header class="site-navbar py-4 js-sticky-header site-navbar-target" role="banner">
  <div class="container-fluid">
    <div class="d-flex align-items-center">
      <div class="site-logo"><a href="{{ route('home') }}">Ruangsport<span>.</span></a></div>
      <div class="ml-auto">
        <nav class="site-navigation position-relative text-right" role="navigation">
          <ul class="site-menu main-menu js-clone-nav mr-auto d-none d-lg-block">
            <li><a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Beranda</a></li>
            <li><a href="{{ route('clubs.index') }}" class="nav-link {{ request()->routeIs('clubs.*') ? 'active' : '' }}">Klub</a></li>
            <li><a href="{{ route('activities.index') }}" class="nav-link {{ request()->routeIs('activities.*') ? 'active' : '' }}">Aktivitas</a></li>
            <li><a href="{{ route('competitions.index') }}" class="nav-link {{ request()->routeIs('competitions.*') ? 'active' : '' }}">Kompetisi</a></li>
            <li><a href="{{ route('venues.index') }}" class="nav-link {{ request()->routeIs('venues.*') ? 'active' : '' }}">Venue</a></li>
            <li><a href="{{ route('community.index') }}" class="nav-link {{ request()->routeIs('community.*') ? 'active' : '' }}">Komunitas</a></li>
            @guest
              <li><a href="{{ route('login') }}" class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}">Masuk</a></li>
              <li><a href="{{ route('register') }}" class="nav-link {{ request()->routeIs('register') ? 'active' : '' }}">Daftar</a></li>
            @else
              <li><a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard', 'admin.*', 'organizer.*', 'venue-owner.*', 'teams.*') ? 'active' : '' }}">Dashboard</a></li>
              <li><a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Keluar</a></li>
            @endguest
          </ul>
        </nav>
        <a href="#" class="d-inline-block d-lg-none site-menu-toggle js-menu-toggle float-right"><span class="icon-menu h3"></span></a>
      </div>
    </div>
  </div>
</header>
