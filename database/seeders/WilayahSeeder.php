<?php

namespace Database\Seeders;

use App\Models\Wilayah;
use Illuminate\Database\Seeder;

/**
 * Wilayah "luar Bojonegoro": provinsi -> kota/kabupaten -> kecamatan.
 * Dataset contoh yang siap dipakai; struktur tabel mendukung dataload
 * dataset lengkap (mis. kode Kemendagri) tanpa perubahan skema.
 */
class WilayahSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Jawa Timur' => [
                'Kota Surabaya' => ['Gubeng', 'Wonokromo', 'Rungkut', 'Sukolilo', 'Tandes'],
                'Kota Malang' => ['Klojen', 'Lowokwaru', 'Kedungkandang', 'Sukun'],
                'Kota Madiun' => ['Kartoharjo', 'Manguharjo', 'Taman'],
                'Kab. Madiun' => ['Dolopo', 'Jiwan', 'Wungu'],
                'Kab. Mojokerto' => ['Mojokerto', 'Pacet', 'Trawas'],
                'Kab. Jombang' => ['Jombang', 'Diwek', 'Peterongan'],
                'Kab. Nganjuk' => ['Nganjuk', 'Wilangan', 'Sawahan'],
                'Kab. Tuban' => ['Tuban', 'Senori', 'Jenu'],
                'Kab. Lamongan' => ['Lamongan', 'Sugio', 'Babat'],
                'Kab. Gresik' => ['Gresik', 'Manyar', 'Kebomas'],
                'Kab. Sidoarjo' => ['Sidoarjo', 'Waru', 'Candi'],
                'Kab. Ngawi' => ['Ngawi', 'Kwadungan', 'Geneng'],
                'Kab. Madiun (Kota)' => ['Kadipaten', 'Wungu'],
            ],
            'Jawa Tengah' => [
                'Kota Semarang' => ['Semarang Tengah', 'Banyumanik', 'Tembalang'],
                'Kab. Grobogan' => ['Purwodadi', 'Gabus', 'Kradenan'],
                'Kab. Sragen' => ['Sragen', 'Masaran', 'Gemolong'],
                'Kab. Rembang' => ['Rembang', 'Lasem', 'Pamotan'],
            ],
            'DI Yogyakarta' => [
                'Kota Yogyakarta' => ['Gondokusuman', 'Mergangsan', 'Umbulharjo'],
                'Kab. Sleman' => ['Depok', 'Ngaglik', 'Kalasan'],
                'Kab. Bantul' => ['Bantul', 'Kasihan', 'Sewon'],
            ],
            'Jawa Barat' => [
                'Kota Bandung' => ['Coblong', 'Sukajadi', 'Cibeunying Kaler'],
                'Kab. Bekasi' => ['Cikarang', 'Tambun Selatan', 'Setu'],
                'Kota Bekasi' => ['Bekasi Timur', 'Bekasi Barat', 'Rawalumbu'],
            ],
            'DKI Jakarta' => [
                'Jakarta Pusat' => ['Tanah Abang', 'Menteng', 'Senen'],
                'Jakarta Timur' => ['Matraman', 'Cakung', 'Duren Sawit'],
            ],
        ];

        foreach ($data as $provinsi => $kotas) {
            $prov = Wilayah::create(['level' => 'provinsi', 'nama' => $provinsi]);

            foreach ($kotas as $kota => $kecamatans) {
                $kot = Wilayah::create([
                    'level' => 'kota',
                    'nama' => $kota,
                    'parent_id' => $prov->id,
                ]);

                foreach ($kecamatans as $kec) {
                    Wilayah::create([
                        'level' => 'kecamatan',
                        'nama' => $kec,
                        'parent_id' => $kot->id,
                    ]);
                }
            }
        }
    }
}
