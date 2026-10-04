@php($assets = asset('assets/ruangsport'))
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Beranda') &mdash; Ruangsport</title>

  <link href="https://fonts.googleapis.com/css?family=Muli:300,400,700,900" rel="stylesheet">
  @foreach (['fonts/icomoon/style.css', 'css/bootstrap.min.css', 'css/jquery-ui.css', 'css/owl.carousel.min.css', 'css/owl.theme.default.min.css', 'css/bootstrap-datepicker.css', 'fonts/flaticon/font/flaticon.css', 'css/aos.css', 'css/style.css', 'css/custom.css'] as $css)
    <link rel="stylesheet" href="{{ $assets }}/{{ $css }}">
  @endforeach
</head>
<body>
  <div class="site-wrap">

    <div class="site-mobile-menu site-navbar-target">
      <div class="site-mobile-menu-header">
        <div class="site-mobile-menu-close mt-3"><span class="icon-close2 js-menu-toggle"></span></div>
      </div>
      <div class="site-mobile-menu-body"></div>
    </div>

    @include('partials.navbar')

    @hasSection('hero_title')
      <div class="page-hero">
        <div class="container">
          <h1>@yield('hero_title')</h1>
          @hasSection('hero_text')<p class="lead mb-0">@yield('hero_text')</p>@endif
        </div>
      </div>
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

  @include('partials.scripts')
</body>
</html>
