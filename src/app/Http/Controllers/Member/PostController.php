<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Post::class);

        $data = $request->validate(
            ['body' => ['required', 'string', 'max:1000']],
            ['body.required' => 'Isi postingan wajib diisi.', 'body.max' => 'Postingan maksimal 1000 karakter.']
        );

        Post::create($data + ['user_id' => $request->user()->id]);

        return back()->with('success', 'Postingan dibagikan.');
    }
}
