{{-- Menu navigasi panel (dashboard, organizer, venue, admin). Tampil sesuai role. --}}
@auth
  @php($u = auth()->user())
  <ul class="nav nav-pills mb-5 flex-nowrap flex-md-wrap" style="overflow-x:auto;">
    <li class="nav-item"><a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard saya</a></li>
    <li class="nav-item"><a class="nav-link {{ request()->routeIs('teams.*') ? 'active' : '' }}" href="{{ route('teams.index') }}">Tim saya</a></li>
    <li class="nav-item"><a class="nav-link {{ request()->routeIs('organizer.*') ? 'active' : '' }}" href="{{ route('organizer.dashboard') }}">Organizer</a></li>
    @if ($u->isVenueOwner() || $u->isAdmin())
      <li class="nav-item"><a class="nav-link {{ request()->routeIs('venue-owner.*') ? 'active' : '' }}" href="{{ route('venue-owner.venues.index') }}">Venue</a></li>
    @endif
    @if ($u->isAdmin())
      <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Admin</a></li>
    @endif
  </ul>

  @if (request()->routeIs('organizer.*'))
    <ul class="nav nav-tabs mb-4">
      <li class="nav-item"><a class="nav-link {{ request()->routeIs('organizer.dashboard') ? 'active' : '' }}" href="{{ route('organizer.dashboard') }}">Ringkasan</a></li>
      <li class="nav-item"><a class="nav-link {{ request()->routeIs('organizer.clubs.*') ? 'active' : '' }}" href="{{ route('organizer.clubs.index') }}">Klub</a></li>
      <li class="nav-item"><a class="nav-link {{ request()->routeIs('organizer.activities.*') ? 'active' : '' }}" href="{{ route('organizer.activities.index') }}">Aktivitas</a></li>
      <li class="nav-item"><a class="nav-link {{ request()->routeIs('organizer.competitions.*') ? 'active' : '' }}" href="{{ route('organizer.competitions.index') }}">Kompetisi</a></li>
    </ul>
  @elseif ($u->isAdmin() && request()->routeIs('admin.*'))
    <ul class="nav nav-tabs mb-4 flex-nowrap flex-md-wrap" style="overflow-x:auto;">
      <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Ringkasan</a></li>
      <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.verifications.*') ? 'active' : '' }}" href="{{ route('admin.verifications.index') }}">Verifikasi</a></li>
      <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">Pengguna</a></li>
      <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.clubs.*') ? 'active' : '' }}" href="{{ route('admin.clubs.index') }}">Klub</a></li>
      <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.posts.*') ? 'active' : '' }}" href="{{ route('admin.posts.index') }}">Postingan</a></li>
      <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.sports.*') ? 'active' : '' }}" href="{{ route('admin.sports.index') }}">Olahraga</a></li>
      <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.locations.*') ? 'active' : '' }}" href="{{ route('admin.locations.index') }}">Lokasi</a></li>
    </ul>
  @endif
@endauth
