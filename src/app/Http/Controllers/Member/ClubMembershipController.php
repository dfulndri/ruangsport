<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\ClubMember;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ClubMembershipController extends Controller
{
    /** Ajukan bergabung (menunggu persetujuan pengelola klub). */
    public function store(Request $request, Club $club): RedirectResponse
    {
        $membership = ClubMember::where('club_id', $club->id)->where('user_id', $request->user()->id)->first();

        if ($membership && $membership->status->value !== 'rejected') {
            return back()->with('info', 'Anda sudah terdaftar atau pengajuan Anda masih diproses.');
        }

        if ($membership) {
            $membership->update(['status' => 'pending']);
        } else {
            ClubMember::create([
                'club_id' => $club->id,
                'user_id' => $request->user()->id,
                'role' => 'member',
                'status' => 'pending',
            ]);
        }

        return back()->with('success', 'Permintaan bergabung dikirim. Menunggu persetujuan pengelola klub.');
    }

    /** Keluar dari klub atau batalkan pengajuan. */
    public function destroy(Request $request, Club $club): RedirectResponse
    {
        $membership = ClubMember::where('club_id', $club->id)->where('user_id', $request->user()->id)->first();

        if (! $membership) {
            return back()->with('info', 'Anda bukan anggota klub ini.');
        }

        if ($membership->role->value === 'owner') {
            return back()->with('error', 'Pemilik klub tidak dapat keluar dari klubnya sendiri.');
        }

        $membership->delete();

        return back()->with('success', 'Anda telah keluar dari klub.');
    }
}
