@extends('layouts.app')

@section('title', 'Peserta '.$activity->title)
@section('hero_title', 'Peserta & Kehadiran')
@section('hero_text', $activity->title.' · '.$activity->starts_at->translatedFormat('d F Y H:i'))

@section('content')
  <div class="site-section">
    <div class="container">
      @include('partials.panel-menu')

      <a href="{{ route('organizer.activities.index') }}" class="d-inline-block mb-3">&larr; Kembali ke daftar aktivitas</a>

      @if ($participants->isEmpty())
        <p class="text-muted">Belum ada peserta.</p>
      @else
        <div class="table-responsive">
          <table class="table table-hover">
            <thead><tr><th>#</th><th>Nama</th><th>Mendaftar</th><th>Status</th><th>Check-in</th><th></th></tr></thead>
            <tbody>
              @foreach ($participants as $i => $p)
                <tr>
                  <td>{{ $i + 1 }}</td>
                  <td>{{ $p->user->name }}<br><small class="text-muted">{{ $p->user->email }}</small></td>
                  <td>{{ $p->registered_at->translatedFormat('d M H:i') }}</td>
                  <td>{{ \App\Support\Label::badge('participant', $p->status) }}</td>
                  <td>{{ $p->checked_in_at?->format('H:i') ?? '-' }}</td>
                  <td class="text-right text-nowrap">
                    @if ($p->status->value !== 'waitlisted')
                      @foreach (['attended' => ['Hadir', 'success'], 'no_show' => ['Tidak hadir', 'outline-danger'], 'registered' => ['Reset', 'outline-secondary']] as $value => [$text, $color])
                        @if ($p->status->value !== $value)
                          <form method="POST" action="{{ route('organizer.activities.participants.update', [$activity->slug, $p->id]) }}" class="d-inline">@csrf @method('PATCH')
                            <input type="hidden" name="status" value="{{ $value }}">
                            <button class="btn btn-{{ $color }} btn-sm">{{ $text }}</button>
                          </form>
                        @endif
                      @endforeach
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>
  </div>
@endsection
