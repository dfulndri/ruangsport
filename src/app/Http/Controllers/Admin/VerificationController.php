<?php

namespace App\Http\Controllers\Admin;

use App\Enums\VerificationStatus;
use App\Http\Controllers\Controller;
use App\Models\Verification;
use App\Services\Verification\VerificationService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');

        $verifications = Verification::query()
            ->with(['verifiable', 'requester'])
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.verifications.index', compact('verifications', 'status'));
    }

    public function update(Request $request, Verification $verification, VerificationService $service): RedirectResponse
    {
        $data = $request->validate([
            'decision' => ['required', 'in:approve,reject'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $service->review($verification, $request->user(), $data['decision'] === 'approve', $data['notes'] ?? null);
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', $data['decision'] === 'approve' ? 'Pengajuan disetujui.' : 'Pengajuan ditolak.');
    }
}
