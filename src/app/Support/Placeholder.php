<?php

namespace App\Support;

/** Gambar sampul sementara (SVG gradasi) supaya tidak bergantung pada file gambar. */
class Placeholder
{
    private const PALETTE = [
        ['#0f766e', '#2dd4bf'], ['#1d4ed8', '#60a5fa'], ['#b45309', '#fbbf24'],
        ['#6d28d9', '#c4b5fd'], ['#be123c', '#fb7185'], ['#166534', '#86efac'],
    ];

    public static function cover(int $seed, int $width = 800, int $height = 420): string
    {
        [$from, $to] = self::PALETTE[$seed % count(self::PALETTE)];

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="'.$width.'" height="'.$height.'" viewBox="0 0 '.$width.' '.$height.'">'
            .'<defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="'.$from.'"/><stop offset="1" stop-color="'.$to.'"/></linearGradient></defs>'
            .'<rect width="100%" height="100%" fill="url(#g)"/></svg>';

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }
}
