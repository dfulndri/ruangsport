@extends('layouts.app')

@section('title', 'Moderasi Postingan')
@section('hero_title', 'Moderasi Postingan')

@section('content')
  <div class="site-section">
    <div class="container">
      @include('partials.panel-menu')

      @forelse ($posts as $post)
        <div class="card-block mb-3">
          <div class="d-flex justify-content-between">
            <div>
              <strong>{{ $post->user->name }}</strong>
              <small class="text-muted">· {{ $post->created_at->diffForHumans() }} · {{ $post->club?->name ?? 'Feed umum' }} · {{ $post->comments_count }} komentar</small>
            </div>
            <form method="POST" action="{{ route('admin.posts.destroy', $post->id) }}">@csrf @method('DELETE')
              <button class="btn btn-outline-danger btn-sm" onclick="return confirm('Hapus postingan ini beserta komentarnya?')">Hapus</button>
            </form>
          </div>
          <p class="mt-2 mb-0">{!! nl2br(e($post->body)) !!}</p>
        </div>
      @empty
        <p class="text-muted">Belum ada postingan.</p>
      @endforelse

      <div class="d-flex justify-content-center">{{ $posts->links() }}</div>
    </div>
  </div>
@endsection
