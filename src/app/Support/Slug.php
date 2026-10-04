<?php

namespace App\Support;

use Illuminate\Support\Str;

class Slug
{
    /**
     * Buat slug unik untuk model ber-SoftDeletes (Club, Venue, Activity, Competition).
     *
     * @param  class-string  $modelClass
     */
    public static function unique(string $modelClass, string $source, ?int $ignoreId = null): string
    {
        $base = Str::slug($source) ?: 'item';
        $slug = $base;
        $i = 2;

        while ($modelClass::withTrashed()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
