<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class MasterDataController extends Controller
{
    /** GET /api/layanan — daftar jenis pindahan */
    public function layanan(): JsonResponse
    {
        return response()->json([
            'data' => config('baikboss.services'),
        ]);
    }

    /** GET /api/barang — daftar barang + harga handling satuan */
    public function barang(): JsonResponse
    {
        return response()->json([
            'data' => config('baikboss.items'),
        ]);
    }

    /** GET /api/tarif — tabel tarif per kecamatan Bojonegoro */
    public function tarif(): JsonResponse
    {
        $data = Cache::remember('tarif_kecamatan', 300, function () {
            return \App\Models\TarifKecamatan::where('aktif', true)
                ->orderBy('kecamatan')
                ->get(['id', 'kecamatan', 'kota', 'provinsi', 'tarif']);
        });

        return response()->json(['data' => $data]);
    }

    /** GET /api/wilayah/provinsi — daftar provinsi (luar Bojonegoro) */
    public function provinsi(): JsonResponse
    {
        $data = \App\Models\Wilayah::where('level', 'provinsi')
            ->orderBy('nama')
            ->get(['id', 'nama']);

        return response()->json(['data' => $data]);
    }

    /** GET /api/wilayah/kota/{provinsiId} */
    public function kota(int $provinsiId): JsonResponse
    {
        $data = \App\Models\Wilayah::where('level', 'kota')
            ->where('parent_id', $provinsiId)
            ->orderBy('nama')
            ->get(['id', 'nama']);

        return response()->json(['data' => $data]);
    }

    /** GET /api/wilayah/kecamatan/{kotaId} */
    public function kecamatan(int $kotaId): JsonResponse
    {
        $data = \App\Models\Wilayah::where('level', 'kecamatan')
            ->where('parent_id', $kotaId)
            ->orderBy('nama')
            ->get(['id', 'nama']);

        return response()->json(['data' => $data]);
    }

    /** GET /api/rekening — info transfer untuk pembayaran */
    public function rekening(): JsonResponse
    {
        return response()->json([
            'data' => config('baikboss.rekening'),
            'wa_tim' => config('baikboss.wa_tim'),
        ]);
    }

    /** GET /api/faq — pertanyaan yang sering diajukan */
    public function faq(): JsonResponse
    {
        return response()->json([
            'data' => [
                [
                    'q' => 'Apa saja jenis layanan pindahan yang disediakan?',
                    'a' => 'Kami menyediakan layanan pindahan rumah, kos, warung, dan kantor sesuai kebutuhan Anda.',
                ],
                [
                    'q' => 'Bagaimana cara saya memesan jasa pindahan?',
                    'a' => 'Anda bisa memesan melalui website kami, pilih opsi layanan, isi detail, dan ikuti langkah pembayaran.',
                ],
                [
                    'q' => 'Apakah saya bisa menentukan jadwal pindahan sendiri?',
                    'a' => 'Ya, Anda bisa memilih tanggal dan jam sesuai keinginan, kami akan mengonfirmasi ketersediaan tim.',
                ],
                [
                    'q' => 'Apakah tarif pindahan sudah termasuk packing dan bongkar-muat?',
                    'a' => 'Tarif pindahan sudah termasuk jasa bongkar-muat, namun jika ada barang khusus, bisa dibicarakan lebih lanjut.',
                ],
                [
                    'q' => 'Apakah saya bisa membatalkan pesanan setelah konfirmasi?',
                    'a' => 'Ya, pesanan bisa dibatalkan sebelum tim berangkat, mohon hubungi kami secepatnya.',
                ],
                [
                    'q' => 'Berapa lama proses pemesanan hingga pindahan selesai?',
                    'a' => 'Proses pemesanan biasanya butuh waktu satu hari kerja, tergantung jadwal dan ketersediaan.',
                ],
                [
                    'q' => 'Apakah tim dibekali perlengkapan keselamatan saat pindahan?',
                    'a' => 'Ya, tim kami dilengkapi perlengkapan keselamatan, seperti sarung tangan dan alat bantu angkat.',
                ],
                [
                    'q' => 'Apakah saya perlu menyiapkan barang yang akan dipindah?',
                    'a' => 'Anda diharapkan sudah mengemas barang-barang kecil, tim akan membantu untuk barang besar.',
                ],
                [
                    'q' => 'Apakah saya bisa mengubah alamat tujuan setelah pemesanan?',
                    'a' => 'Anda bisa menghubungi tim kami setelah pemesanan lewat tombol WhatsApp yang kami sediakan, atau melalui email yang tertera di konfirmasi.',
                ],
                [
                    'q' => 'Bagaimana cara saya menghubungi tim setelah pemesanan?',
                    'a' => 'Anda bisa menghubungi tim kami setelah pemesanan lewat tombol WhatsApp yang kami sediakan, atau melalui email yang tertera di konfirmasi.',
                ],
            ],
        ]);
    }
}
