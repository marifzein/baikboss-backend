<?php

namespace Database\Seeders;

use App\Models\TarifKecamatan;
use Illuminate\Database\Seeder;

/**
 * Tarif dasar per kecamatan di Kabupaten Bojonegoro (28 kecamatan).
 * Tarif = biaya angkut dasar sekali jalan (Rp), belum termasuk handling barang.
 * Tier: kota pusat < dekat < menengah < jauh — mudah diubah tim operasional.
 */
class TarifKecamatanSeeder extends Seeder
{
    public function run(): void
    {
        $tarif = [
            // Pusat kota
            'Bojonegoro'  => 100_000,

            // Tier dekat (ring 1)
            'Kapas'       => 120_000,
            'Dander'      => 120_000,
            'Kalitidu'    => 130_000,
            'Malo'        => 130_000,
            'Temayang'    => 140_000,
            'Bubulan'     => 140_000,
            'Gondang'     => 140_000,

            // Tier menengah (ring 2)
            'Baureno'     => 150_000,
            'Kanor'       => 160_000,
            'Sumberejo'   => 160_000,
            'Kedungadem'  => 160_000,
            'Kepohbaru'   => 160_000,
            'Trucuk'      => 150_000,
            'Ngasem'      => 160_000,
            'Kasiman'     => 170_000,
            'Sukosewu'    => 170_000,
            'Sekar'       => 170_000,
            'Sugihwaras'  => 170_000,

            // Tier jauh (ring 3)
            'Balen'       => 190_000,
            'Gayam'       => 190_000,
            'Ngraho'      => 200_000,
            'Padangan'    => 200_000,
            'Purwosari'   => 190_000,
            'Kedewan'     => 190_000,
            'Margomulyo'  => 200_000,
            'Ngambon'     => 200_000,
            'Tambakrejo'  => 200_000,
        ];

        foreach ($tarif as $kecamatan => $harga) {
            TarifKecamatan::updateOrCreate(
                ['kecamatan' => $kecamatan],
                [
                    'kota'     => 'Bojonegoro',
                    'provinsi' => 'Jawa Timur',
                    'tarif'    => $harga,
                    'aktif'    => true,
                ],
            );
        }
    }
}
