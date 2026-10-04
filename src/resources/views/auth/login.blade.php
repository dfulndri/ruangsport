@extends('layouts.app')

@section('title', 'Masuk')
@section('hero_title', 'Masuk')
@section('hero_text', 'Masuk untuk bergabung ke klub dan mendaftar kegiatan.')

@section('content')
  <div class="site-section">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-5">
          <form method="POST" action="{{ route('login.store') }}" class="auth-card">
            @csrf
            <div class="form-group">
              <label for="email">Email</label>
              <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required autofocus>
              @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
              <label for="password">Kata sandi</label>
              <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
              @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-check mb-4">
              <input type="checkbox" class="form-check-input" id="remember" name="remember" value="1">
              <label class="form-check-label" for="remember">Ingat saya</label>
            </div>
            <button class="btn btn-primary btn-block py-3">Masuk</button>
            <p class="text-center mt-3 mb-0">Belum punya akun? <a href="{{ route('register') }}">Daftar</a></p>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
