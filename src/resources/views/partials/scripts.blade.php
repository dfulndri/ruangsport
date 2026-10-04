@php
  // Muat semua JS template; jQuery dan Bootstrap lebih dulu, main.js terakhir.
  $dir = public_path('assets/ruangsport/js');
  $files = collect(glob($dir.'/*.js') ?: [])->map(fn ($p) => basename($p));
  $first = collect(['jquery-3.3.1.min.js', 'jquery-ui.js', 'popper.min.js', 'bootstrap.min.js'])->filter(fn ($f) => $files->contains($f));
  $last = $files->contains('main.js') ? collect(['main.js']) : collect();
  $ordered = $first->merge($files->reject(fn ($f) => $first->contains($f) || $f === 'main.js'))->merge($last);
@endphp
@foreach ($ordered as $file)
  <script src="{{ asset('assets/ruangsport/js/'.$file) }}"></script>
@endforeach
