@extends('layouts.app')

@section('title', 'Anggota '.$club->name)
@section('hero_title', 'Anggota Klub')
@section('hero_text', $club->name)

@section('content')
  <div class="site-section">
    <div class="container">
      @include('partials.panel-menu')

      <a href="{{ route('organizer.clubs.index') }}" class="d-inline-block mb-3">&larr; Kembali ke daftar klub</a>

      <div class="table-responsive">
        <table class="table table-hover">
          <thead><tr><th>Nama</th><th>Email</th><th>Peran</th><th>Status</th><th></th></tr></thead>
          <tbody>
            @foreach ($memberships as $m)
              <tr>
                <td>{{ $m->user->name }}</td>
                <td>{{ $m->user->email }}</td>
                <td>{{ \App\Support\Label::text('club_role', $m->role) }}</td>
                <td>{{ \App\Support\Label::badge('member', $m->status) }}</td>
                <td class="text-right text-nowrap">
                  @if ($m->role->value !== 'owner')
                    @if ($m->status->value !== 'approved')
                      <form method="POST" action="{{ route('organizer.clubs.members.update', [$club->slug, $m->id]) }}" class="d-inline">@csrf @method('PATCH')
                        <input type="hidden" name="status" value="approved">
                        <button class="btn btn-success btn-sm">Setujui</button>
                      </form>
                    @endif
                    @if ($m->status->value === 'pending')
                      <form method="POST" action="{{ route('organizer.clubs.members.update', [$club->slug, $m->id]) }}" class="d-inline">@csrf @method('PATCH')
                        <input type="hidden" name="status" value="rejected">
                        <button class="btn btn-outline-danger btn-sm">Tolak</button>
                      </form>
                    @endif
                    @if ($m->status->value === 'approved' && (auth()->id() === $club->owner_id || auth()->user()->isAdmin()))
                      <form method="POST" action="{{ route('organizer.clubs.members.update', [$club->slug, $m->id]) }}" class="d-inline">@csrf @method('PATCH')
                        <input type="hidden" name="role" value="{{ $m->role->value === 'manager' ? 'member' : 'manager' }}">
                        <button class="btn btn-outline-primary btn-sm">{{ $m->role->value === 'manager' ? 'Jadikan anggota' : 'Jadikan pengelola' }}</button>
                      </form>
                    @endif
                    <form method="POST" action="{{ route('organizer.clubs.members.destroy', [$club->slug, $m->id]) }}" class="d-inline">@csrf @method('DELETE')
                      <button class="btn btn-outline-danger btn-sm" onclick="return confirm('Keluarkan anggota ini?')">Keluarkan</button>
                    </form>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection
