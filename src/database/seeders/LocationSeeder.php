<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Wilayah Tangerang Raya. Daftar kecamatan perlu dicek ulang
     * terhadap data resmi sebelum dipakai di produksi.
     */
    public function run(): void
    {
        $regions = [
            ['Kota Tangerang', 'kota', [
                'Batuceper', 'Benda', 'Cibodas', 'Ciledug', 'Cipondoh', 'Jatiuwung',
                'Karang Tengah', 'Karawaci', 'Larangan', 'Neglasari', 'Periuk',
                'Pinang', 'Tangerang',
            ]],
            ['Kota Tangerang Selatan', 'kota', [
                'Ciputat', 'Ciputat Timur', 'Pamulang', 'Pondok Aren',
                'Serpong', 'Serpong Utara', 'Setu',
            ]],
            ['Kabupaten Tangerang', 'kabupaten', [
                'Balaraja', 'Cikupa', 'Curug', 'Kelapa Dua', 'Kosambi', 'Legok',
                'Mauk', 'Pagedangan', 'Panongan', 'Pasar Kemis', 'Rajeg',
                'Sepatan', 'Teluknaga', 'Tigaraksa',
            ]],
        ];

        foreach ($regions as [$name, $type, $districts]) {
            $parent = Location::updateOrCreate(
                ['parent_id' => null, 'name' => $name],
                ['type' => $type],
            );

            foreach ($districts as $district) {
                Location::updateOrCreate(
                    ['parent_id' => $parent->id, 'name' => $district],
                    ['type' => 'kecamatan'],
                );
            }
        }
    }
}
