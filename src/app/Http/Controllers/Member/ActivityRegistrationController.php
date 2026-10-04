<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Services\Activity\ActivityRegistrationService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ActivityRegistrationController extends Controller
{
    public function __construct(private ActivityRegistrationService $service)
    {
    }

    public function store(Request $request, Activity $activity): RedirectResponse
    {
        try {
            $participant = $this->service->register($activity, $request->user());
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with(
            $participant->status->value === 'waitlisted' ? 'info' : 'success',
            $participant->status->value === 'waitlisted'
                ? 'Kuota penuh. Anda masuk daftar tunggu dan akan naik otomatis bila ada yang membatalkan.'
                : 'Pendaftaran berhasil. Sampai jumpa di kegiatan!'
        );
    }

    public function destroy(Request $request, Activity $activity): RedirectResponse
    {
        try {
            $this->service->cancel($activity, $request->user());
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Pendaftaran dibatalkan.');
    }
}
