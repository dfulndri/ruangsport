@extends('layouts.app')

@section('title', 'Verifikasi')
@section('hero_title', 'Verifikasi Klub & Venue')

@section('content')
  <div class="site-section">
    <div class="container">
      @include('partials.panel-menu')

      <ul class="nav nav-pills mb-4">
        @foreach (['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak', 'all' => 'Semua'] as $key => $text)
          <li class="nav-item"><a class="nav-link {{ $status === $key ? 'active' : '' }}" href="{{ route('admin.verifications.index', ['status' => $key]) }}">{{ $text }}</a></li>
        @endforeach
      </ul>

      @if ($verifications->isEmpty())
        <p class="text-muted">Tidak ada pengajuan.</p>
      @else
        <div class="table-responsive">
          <table class="table table-hover">
            <thead><tr><th>Jenis</th><th>Nama</th><th>Diajukan oleh</th><th>Tanggal</th><th>Status</th><th style="min-width:260px;"></th></tr></thead>
            <tbody>
              @foreach ($verifications as $v)
                <tr>
                  <td>{{ $v->verifiable_type === \App\Models\Club::class ? 'Klub' : 'Venue' }}</td>
                  <td>{{ $v->verifiable?->name ?? '(sudah dihapus)' }}</td>
                  <td>{{ $v->requester->name }}</td>
                  <td>{{ $v->created_at->translatedFormat('d M Y') }}</td>
                  <td>{{ \App\Support\Label::badge('verification', $v->status) }}@if ($v->notes)<br><small class="text-muted">{{ $v->notes }}</small>@endif</td>
                  <td>
                    @if ($v->status->value === 'pending')
                      <form method="POST" action="{{ route('admin.verifications.update', $v->id) }}">@csrf @method('PATCH')
                        <input type="text" name="notes" class="form-control form-control-sm mb-2" placeholder="Catatan (opsional)">
                        <button name="decision" value="approve" class="btn btn-success btn-sm">Setujui</button>
                        <button name="decision" value="reject" class="btn btn-outline-danger btn-sm">Tolak</button>
                      </form>
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="d-flex justify-content-center">{{ $verifications->links() }}</div>
      @endif
    </div>
  </div>
@endsection
