@extends('layouts.app')

@section('title', 'Pengguna')
@section('hero_title', 'Kelola Pengguna')

@section('content')
  <div class="site-section">
    <div class="container">
      @include('partials.panel-menu')

      <form method="GET" action="{{ route('admin.users.index') }}" class="filter-form mb-4">
        <div class="form-row">
          <div class="col-md-5 mb-2"><input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama atau email..."></div>
          <div class="col-md-3 mb-2">
            <select name="role" class="form-control">
              <option value="">Semua role</option>
              @foreach (['member', 'venue_owner', 'admin'] as $r)
                <option value="{{ $r }}" @selected(request('role') === $r)>{{ \App\Support\Label::text('user_role', $r) }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-2 mb-2"><button class="btn btn-primary btn-block">Cari</button></div>
        </div>
      </form>

      <div class="table-responsive">
        <table class="table table-hover">
          <thead><tr><th>Nama</th><th>Email</th><th style="min-width:340px;">Role & status</th></tr></thead>
          <tbody>
            @foreach ($users as $user)
              <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                  <form method="POST" action="{{ route('admin.users.update', $user->id) }}" class="form-inline">@csrf @method('PATCH')
                    <select name="role" class="form-control form-control-sm mr-2">
                      @foreach (['member', 'venue_owner', 'admin'] as $r)
                        <option value="{{ $r }}" @selected($user->role->value === $r)>{{ \App\Support\Label::text('user_role', $r) }}</option>
                      @endforeach
                    </select>
                    <select name="is_active" class="form-control form-control-sm mr-2">
                      <option value="1" @selected($user->is_active)>Aktif</option>
                      <option value="0" @selected(! $user->is_active)>Nonaktif</option>
                    </select>
                    <button class="btn btn-outline-primary btn-sm">Simpan</button>
                  </form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="d-flex justify-content-center">{{ $users->links() }}</div>
    </div>
  </div>
@endsection
