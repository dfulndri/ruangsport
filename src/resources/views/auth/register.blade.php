@extends('layouts.app')

@section('title', 'Daftar')
@section('hero_title', 'Buat akun Ruangsport')
@section('hero_text', 'Gratis. Cukup beberapa detik.')

@section('content')
  <div class="site-section">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-5">
          <form method="POST" action="{{ route('register.store') }}" class="auth-card">
            @csrf
            <div class="form-group">
              <label for="name">Nama</label>
              <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required autofocus>
              @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
              <label for="email">Email</label>
              <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required>
              @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
              <label for="role">Saya mendaftar sebagai</label>
              <select id="role" name="role" class="form-control @error('role') is-invalid @enderror">
                <option value="member" @selected(old('role', 'member') === 'member')>Member (pegiat olahraga)</option>
                <option value="venue_owner" @selected(old('role') === 'venue_owner')>Pemilik venue</option>
              </select>
              @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
              <label for="password">Kata sandi (minimal 8 karakter)</label>
              <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
              @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
              <label for="password_confirmation">Ulangi kata sandi</label>
              <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
            </div>
            <button class="btn btn-primary btn-block py-3">Daftar</button>
            <p class="text-center mt-3 mb-0">Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></p>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
