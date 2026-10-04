<?php

namespace App\Services\Competition;

use App\Enums\CompetitionStatus;
use App\Enums\MatchStatus;
use App\Models\Competition;
use App\Models\CompetitionMatch;
use DomainException;
use Illuminate\Support\Facades\DB;

/**
 * Mencatat skor, menentukan pemenang, dan meneruskannya ke babak berikutnya.
 */
class ScoreRecorder
{
    public function record(CompetitionMatch $match, int $scoreA, int $scoreB): void
    {
        DB::transaction(function () use ($match, $scoreA, $scoreB) {
            $match = CompetitionMatch::whereKey($match->id)->lockForUpdate()->firstOrFail();

            if ($match->is_bye) {
                throw new DomainException('Pertandingan BYE tidak perlu diberi skor.');
            }

            if (! $match->entry_a_id || ! $match->entry_b_id) {
                throw new DomainException('Kedua peserta belum lengkap. Selesaikan pertandingan babak sebelumnya.');
            }

            if ($scoreA === $scoreB) {
                throw new DomainException('Sistem gugur tidak boleh seri. Tentukan pemenang.');
            }

            $winnerId = $scoreA > $scoreB ? $match->entry_a_id : $match->entry_b_id;

            $next = $match->next_match_id
                ? CompetitionMatch::whereKey($match->next_match_id)->lockForUpdate()->first()
                : null;

            if ($next
                && $next->status === MatchStatus::Finished
                && $match->winner_entry_id
                && (int) $match->winner_entry_id !== (int) $winnerId) {
                throw new DomainException('Pemenang tidak bisa diubah karena pertandingan babak berikutnya sudah selesai.');
            }

            $match->update([
                'score_a' => $scoreA,
                'score_b' => $scoreB,
                'winner_entry_id' => $winnerId,
                'status' => MatchStatus::Finished,
            ]);

            if ($next) {
                CompetitionMatch::whereKey($next->id)->update([
                    $match->next_slot === 'a' ? 'entry_a_id' : 'entry_b_id' => $winnerId,
                ]);
            } else {
                // Pertandingan final selesai -> kompetisi selesai.
                Competition::whereKey($match->competition_id)->update([
                    'status' => CompetitionStatus::Finished->value,
                ]);
            }
        });
    }
}
