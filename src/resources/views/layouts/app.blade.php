@php
  $assets = asset('assets/ruangsport');

  // Foto hero: pakai images/hero.(jpg|jpeg|webp|png) bila ada; kalau tidak, JPG terbesar dari template.
  $heroImage = null;
  foreach (['jpg', 'jpeg', 'webp', 'png'] as $ext) {
    if (file_exists(public_path("assets/ruangsport/images/hero.$ext"))) { $heroImage = "$assets/images/hero.$ext"; break; }
  }
  if (! $heroImage) {
    $pool = array_merge(glob(public_path('assets/ruangsport/images/*.jpg')) ?: [], glob(public_path('assets/ruangsport/images/*.jpeg')) ?: []);
    usort($pool, fn ($a, $b) => filesize($b) <=> filesize($a));
    if ($pool) { $heroImage = "$assets/images/".basename($pool[0]); }
  }
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="theme-color" content="#1d2b2a">
  <title>@yield('title', 'Beranda') &mdash; Ruangsport</title>
  <script>document.documentElement.classList.add('js');</script>

  <link href="https://fonts.googleapis.com/css?family=Muli:300,400,700,900" rel="stylesheet">
  @foreach (['fonts/icomoon/style.css', 'css/bootstrap.min.css', 'css/jquery-ui.css', 'css/owl.carousel.min.css', 'css/owl.theme.default.min.css', 'css/bootstrap-datepicker.css', 'fonts/flaticon/font/flaticon.css', 'css/aos.css', 'css/style.css'] as $css)
    <link rel="stylesheet" href="{{ $assets }}/{{ $css }}">
  @endforeach
  <link rel="stylesheet" href="{{ $assets }}/css/custom.css?v={{ @filemtime(public_path('assets/ruangsport/css/custom.css')) }}">
</head>
<body>
  <div id="scroll-progress" aria-hidden="true"></div>

  <div class="site-wrap">

    <div class="site-mobile-menu site-navbar-target">
      <div class="site-mobile-menu-header">
        <div class="site-mobile-menu-close mt-3"><span class="icon-close2 js-menu-toggle"></span></div>
      </div>
      <div class="site-mobile-menu-body"></div>
    </div>

    @include('partials.navbar')

    @hasSection('hero_title')
      <section class="page-hero @yield('hero_class')">
        <div class="page-hero__bg" data-parallax @if ($heroImage) style="background-image:url('{{ $heroImage }}')" @endif></div>
        <div class="container page-hero__inner">
          <h1>@yield('hero_title')</h1>
          @hasSection('hero_text')<p class="lead">@yield('hero_text')</p>@endif
          @hasSection('hero_actions')<div class="hero-actions">@yield('hero_actions')</div>@endif
        </div>
      </section>
    @endif

    @if (session()->hasAny(['success', 'error', 'info']))
      <div class="container pt-4">
        @foreach (['success' => 'success', 'error' => 'danger', 'info' => 'info'] as $key => $class)
          @if (session($key))<div class="alert alert-{{ $class }} mb-2">{{ session($key) }}</div>@endif
        @endforeach
      </div>
    @endif

    @yield('content')

    @include('partials.footer')

    <form id="logout-form" method="POST" action="{{ route('logout') }}" class="d-none">@csrf</form>
  </div>

  <button type="button" id="back-to-top" aria-label="Kembali ke atas">&uarr;</button>

  @include('partials.scripts')
</body>
</html>
