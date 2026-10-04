@extends('layouts.app')

@section('title', 'Komunitas')
@section('hero_title', 'Komunitas')
@section('hero_text', 'Bagikan kabar dan berinteraksi dengan sesama pegiat olahraga.')

@section('content')
  <div class="site-section">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8">

          @auth
            <form method="POST" action="{{ route('posts.store') }}" class="card-block mb-5">
              @csrf
              <div class="form-group">
                <textarea name="body" rows="3" class="form-control @error('body') is-invalid @enderror" placeholder="Apa kabar olahragamu hari ini?" required>{{ old('body') }}</textarea>
                @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <button class="btn btn-primary">Bagikan</button>
            </form>
          @else
            <p class="text-center mb-5"><a href="{{ route('login') }}">Masuk</a> untuk ikut berbagi dan berkomentar.</p>
          @endauth

          @forelse ($posts as $post)
            <div class="card-block mb-4">
              <div class="d-flex justify-content-between">
                <strong>{{ $post->user->name }}</strong>
                <small class="text-muted">
                  {{ $post->created_at->diffForHumans() }}
                  @can('delete', $post)
                    <form method="POST" action="{{ route('posts.destroy', $post->id) }}" class="d-inline">@csrf @method('DELETE')
                      <button class="btn btn-link btn-sm p-0 ml-2 text-danger" onclick="return confirm('Hapus postingan ini?')">Hapus</button>
                    </form>
                  @endcan
                </small>
              </div>
              <p class="mt-2 mb-3">{!! nl2br(e($post->body)) !!}</p>

              @foreach ($post->comments as $comment)
                <div class="comment-item">
                  <strong>{{ $comment->user->name }}</strong>
                  <span>{{ $comment->body }}</span>
                  @can('delete', $comment)
                    <form method="POST" action="{{ route('comments.destroy', $comment->id) }}" class="d-inline">@csrf @method('DELETE')
                      <button class="btn btn-link btn-sm p-0 ml-2 text-danger" title="Hapus komentar" onclick="return confirm('Hapus komentar ini?')">&times;</button>
                    </form>
                  @endcan
                </div>
              @endforeach

              @auth
                <form method="POST" action="{{ route('comments.store', $post->id) }}" class="form-inline mt-3">
                  @csrf
                  <input type="text" name="body" class="form-control form-control-sm flex-fill mr-2" placeholder="Tulis komentar..." required maxlength="500">
                  <button class="btn btn-outline-primary btn-sm">Kirim</button>
                </form>
              @endauth
            </div>
          @empty
            <p class="text-center text-muted">Belum ada postingan.</p>
          @endforelse

          <div class="d-flex justify-content-center">{{ $posts->links() }}</div>
        </div>
      </div>
    </div>
  </div>
@endsection
