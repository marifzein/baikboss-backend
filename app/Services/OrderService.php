<?php

namespace App\Services;

use App\Models\Order;
use App\Models\TarifKecamatan;
use App\Models\Wilayah;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    /**
     * Hitung estimasi biaya dari payload yang sama dengan create().
     * Tidak menyentuh DB selain membaca tarif/wilayah.
     */
    public function quote(array $data): array
    {
        [$tarifAmount, $tarifSource] = $this->resolveTarif($data);

        $items = $this->resolveItems($data['items'] ?? []);

        $itemsAmount = array_sum(array_column($items, 'subtotal'));

        return [
            'tarif_amount' => $tarifAmount,
            'tarif_source' => $tarifSource,   // info kecamatan/luar untuk UI
            'items_amount' => $itemsAmount,
            'total_amount' => $tarifAmount + $itemsAmount,
            'items' => $items,
        ];
    }

    /**
     * Buat pesanan lengkap: user, order, order_items, payment(pending).
     * Semua dalam satu transaksi DB.
     */
    public function create(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $user = \App\Models\User::updateOrCreate(
                ['phone' => $data['phone']],
                ['name' => $data['name']],
            );

            $quote = $this->quote($data);

            $order = Order::create([
                'order_code' => $this->generateCode(),
                'user_id' => $user->id,
                'wa_number' => $data['phone'],
                'service_type' => $data['service_type'],

                'origin_label' => $data['origin_label'],
                'origin_lat' => $data['origin_lat'] ?? null,
                'origin_lng' => $data['origin_lng'] ?? null,
                'origin_kecamatan_id' => $data['origin_kecamatan_id'] ?? null,

                'dest_type' => $data['dest_type'],
                'dest_tarif_id' => $data['dest_tarif_id'] ?? null,
                'dest_wilayah_id' => $data['dest_wilayah_id'] ?? null,
                'dest_label' => $data['dest_label'],
                'dest_lat' => $data['dest_lat'] ?? null,
                'dest_lng' => $data['dest_lng'] ?? null,

                'schedule_date' => $data['schedule_date'],
                'schedule_time' => $data['schedule_time'],

                'tarif_amount' => $quote['tarif_amount'],
                'items_amount' => $quote['items_amount'],
                'total_amount' => $quote['total_amount'],

                'notes' => $data['notes'] ?? null,
                'status' => 'pending',
            ]);

            foreach ($quote['items'] as $item) {
                $order->items()->create([
                    'item_name' => $item['item_name'],
                    'qty' => $item['qty'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            $order->payment()->create([
                'amount' => $quote['total_amount'],
                'method' => 'transfer_bank',
                'status' => 'pending',
            ]);

            return $order->fresh(['items', 'payment']);
        });
    }

    /**
     * Tentukan tarif dasar berdasar tujuan.
     * - Tujuan Bojonegoro: tarif baris tarif_kecamatan (dest_tarif_id)
     * - Luar Bojonegoro: flat config tarif_luar_bojonegoro
     */
    private function resolveTarif(array $data): array
    {
        if (($data['dest_type'] ?? '') === 'bojonegoro') {
            $tarif = TarifKecamatan::where('aktif', true)->find($data['dest_tarif_id'] ?? null);

            if (! $tarif) {
                throw ValidationException::withMessages([
                    'dest_tarif_id' => 'Kecamatan tujuan tidak ditemukan / tidak aktif.',
                ]);
            }

            return [$tarif->tarif, [
                'type' => 'bojonegoro',
                'kecamatan' => $tarif->kecamatan,
                'tarif' => $tarif->tarif,
            ]];
        }

        if (($data['dest_type'] ?? '') === 'luar') {
            $wilayah = Wilayah::where('level', 'kecamatan')->find($data['dest_wilayah_id'] ?? null);

            if (! $wilayah) {
                throw ValidationException::withMessages([
                    'dest_wilayah_id' => 'Kecamatan tujuan (luar Bojonegoro) tidak ditemukan.',
                ]);
            }

            $flat = (int) config('baikboss.tarif_luar_bojonegoro');

            return [$flat, [
                'type' => 'luar',
                'kecamatan' => $wilayah->nama,
                'tarif' => $flat,
            ]];
        }

        throw ValidationException::withMessages([
            'dest_type' => 'Tipe tujuan harus bojonegoro atau luar.',
        ]);
    }

    /**
     * Validasi & hitung items: [{item_name, qty}] -> harga dari config.
     */
    private function resolveItems(array $rawItems): array
    {
        $catalog = collect(config('baikboss.items'))->keyBy('code');
        $items = [];

        foreach ($rawItems as $raw) {
            $code = $raw['item_name'] ?? null;
            $qty = (int) ($raw['qty'] ?? 0);

            if ($qty <= 0) {
                continue;
            }

            $product = $catalog->get($code);

            if (! $product) {
                throw ValidationException::withMessages([
                    "items" => "Barang tidak dikenal: {$code}",
                ]);
            }

            $items[] = [
                'item_name' => $code,
                'qty' => $qty,
                'unit_price' => $product['price'],
                'subtotal' => $qty * $product['price'],
            ];
        }

        if (empty($items)) {
            throw ValidationException::withMessages([
                'items' => 'Minimal pilih satu barang yang akan dipindah.',
            ]);
        }

        return $items;
    }

    private function generateCode(): string
    {
        do {
            $code = 'BB-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        } while (Order::where('order_code', $code)->exists());

        return $code;
    }
}
