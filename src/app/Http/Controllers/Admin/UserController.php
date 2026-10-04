<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->filled('q'), fn ($q) => $q->where(
                fn ($w) => $w->where('name', 'like', '%'.$request->string('q').'%')
                    ->orWhere('email', 'like', '%'.$request->string('q').'%')
            ))
            ->when($request->filled('role'), fn ($q) => $q->where('role', $request->string('role')))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'role' => ['required', Rule::enum(UserRole::class)],
            'is_active' => ['required', 'boolean'],
        ]);

        // Cegah admin mengunci dirinya sendiri.
        if ($user->id === $request->user()->id
            && ($data['role'] !== UserRole::Admin->value || ! $data['is_active'])) {
            return back()->with('error', 'Anda tidak dapat menurunkan role atau menonaktifkan akun Anda sendiri.');
        }

        $user->update($data);

        return back()->with('success', 'Pengguna diperbarui.');
    }
}
