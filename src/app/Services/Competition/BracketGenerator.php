<?php

namespace App\Services\Competition;

use App\Enums\CompetitionStatus;
use App\Enums\MatchStatus;
use App\Models\Competition;
use App\Models\CompetitionMatch;
use DomainException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Membuat bracket sistem gugur (single elimination).
 *
 * - Jumlah slot dibulatkan ke pangkat dua (4, 8, 16, ...); kekurangannya menjadi BYE.
 * - Peserta berunggulan tinggi mendapat BYE (penempatan seed standar).
 * - Semua pertandingan dibuat sekaligus dan saling tersambung lewat next_match_id.
 */
class BracketGenerator
{
    public function generate(Competition $competition, bool $reshuffle = false): void
    {
        DB::transaction(function () use ($competition, $reshuffle) {
            $competition = Competition::whereKey($competition->id)->lockForUpdate()->firstOrFail();

            if ($competition->status === CompetitionStatus::Draft) {
                throw new DomainException('Ubah status kompetisi menjadi "Pendaftaran dibuka" terlebih dahulu.');
            }

            if ($competition->status === CompetitionStatus::Finished) {
                throw new DomainException('Kompetisi sudah selesai.');
            }

            $hasResults = CompetitionMatch::where('competition_id', $competition->id)
                ->where('is_bye', false)
                ->where('status', MatchStatus::Finished->value)
                ->exists();

            if ($hasResults) {
                throw new DomainException('Bracket tidak dapat dibuat ulang karena sudah ada pertandingan yang selesai.');
            }

            $entries = $competition->verifiedEntries()->get();

            if ($entries->count() < 2) {
                throw new DomainException('Minimal 2 peserta terverifikasi untuk membuat bracket.');
            }

            CompetitionMatch::where('competition_id', $competition->id)->delete();

            $ordered = $this->orderEntries($entries, $reshuffle);

            foreach ($ordered as $i => $entry) {
                $entry->update(['seed' => $i + 1]);
            }

            $this->build($competition, $ordered);

            $competition->update(['status' => CompetitionStatus::Ongoing]);
        });
    }

    /** Peserta yang sudah punya seed lebih dulu (urut seed), sisanya diacak. */
    private function orderEntries(Collection $entries, bool $reshuffle): Collection
    {
        if ($reshuffle) {
            return $entries->shuffle()->values();
        }

        $seeded = $entries->filter(fn ($e) => $e->seed !== null)->sortBy('seed')->values();
        $unseeded = $entries->filter(fn ($e) => $e->seed === null)->shuffle()->values();

        return $seeded->concat($unseeded)->values();
    }

    private function build(Competition $competition, Collection $ordered): void
    {
        $n = $ordered->count();
        $size = 2;
        $rounds = 1;
        while ($size < $n) {
            $size *= 2;
            $rounds++;
        }

        // 1) Buat semua pertandingan.
        $byRound = [];
        for ($r = 1; $r <= $rounds; $r++) {
            $count = intdiv($size, 2 ** $r);
            for ($i = 0; $i < $count; $i++) {
                $byRound[$r][$i] = CompetitionMatch::create([
                    'competition_id' => $competition->id,
                    'round' => $r,
                    'match_number' => $i + 1,
                    'status' => MatchStatus::Scheduled,
                ]);
            }
        }

        // 2) Sambungkan ke pertandingan babak berikutnya.
        for ($r = 1; $r < $rounds; $r++) {
            foreach ($byRound[$r] as $i => $match) {
                $next = $byRound[$r + 1][intdiv($i, 2)];
                $match->update([
                    'next_match_id' => $next->id,
                    'next_slot' => $i % 2 === 0 ? 'a' : 'b',
                ]);
            }
        }

        // 3) Isi babak pertama sesuai penempatan seed; slot kosong = BYE.
        $order = $this->seedOrder($size);

        foreach ($byRound[1] as $i => $match) {
            $a = $ordered->get($order[2 * $i] - 1);
            $b = $ordered->get($order[2 * $i + 1] - 1);

            $match->update(['entry_a_id' => $a?->id, 'entry_b_id' => $b?->id]);

            if ($a === null || $b === null) {
                $winner = $a ?? $b;

                $match->update([
                    'is_bye' => true,
                    'winner_entry_id' => $winner->id,
                    'status' => MatchStatus::Finished,
                ]);

                if ($match->next_match_id) {
                    CompetitionMatch::whereKey($match->next_match_id)->update([
                        $match->next_slot === 'a' ? 'entry_a_id' : 'entry_b_id' => $winner->id,
                    ]);
                }
            }
        }
    }

    /**
     * Urutan seed standar untuk bracket: [1,8,4,5,2,7,3,6] untuk 8 slot.
     * Seed 1 dan 2 baru bertemu di final; seed teratas mendapat BYE.
     *
     * @return int[]
     */
    private function seedOrder(int $size): array
    {
        $order = [1];

        while (count($order) < $size) {
            $sum = count($order) * 2 + 1;
            $next = [];
            foreach ($order as $seed) {
                $next[] = $seed;
                $next[] = $sum - $seed;
            }
            $order = $next;
        }

        return $order;
    }
}
