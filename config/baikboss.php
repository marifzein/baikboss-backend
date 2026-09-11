<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Identitas bisnis
    |--------------------------------------------------------------------------
    */
    'brand' => 'baikboss!',
    'wa_tim' => env('BAIKBOSS_WA_TIM', '6281234567890'), // nomor WA tim untuk tombol hubungi
    'email_tim' => env('BAIKBOSS_EMAIL_TIM', 'hello@baikboss.id'),

    /*
    |--------------------------------------------------------------------------
    | Jenis layanan pindahan
    |--------------------------------------------------------------------------
    */
    'services' => [
        ['code' => 'rumah', 'label' => 'Pindah Rumah', 'icon' => 'home'],
        ['code' => 'kos', 'label' => 'Pindah Kos', 'icon' => 'sofa'],
        ['code' => 'warung', 'label' => 'Pindah Warung', 'icon' => 'package'],
        ['code' => 'kantor', 'label' => 'Pindah Kantor', 'icon' => 'clipboard-list'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Katalog barang besar + biaya handling per unit (Rp)
    | Dipakai OrderService saat kalkulasi & disimpan ke order_items.
    |--------------------------------------------------------------------------
    */
    'items' => [
        ['code' => 'lemari', 'label' => 'Lemari', 'icon' => 'door-open', 'price' => 50000],
        ['code' => 'meja', 'label' => 'Meja', 'icon' => 'armchair', 'price' => 30000],
        ['code' => 'kursi', 'label' => 'Kursi', 'icon' => 'sofa', 'price' => 15000],
        ['code' => 'kulkas', 'label' => 'Kulkas', 'icon' => 'snowflake', 'price' => 40000],
        ['code' => 'mesin_cuci', 'label' => 'Mesin Cuci', 'icon' => 'washing-machine', 'price' => 40000],
        ['code' => 'tv', 'label' => 'TV', 'icon' => 'monitor', 'price' => 25000],
        ['code' => 'ac', 'label' => 'AC', 'icon' => 'snowflake', 'price' => 60000],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tarif luar Bojonegoro (flat, Rp) — fallback jika tujuan di luar kota
    |--------------------------------------------------------------------------
    */
    'tarif_luar_bojonegoro' => env('BAIKBOSS_TARIF_LUAR', 250000),

    /*
    |--------------------------------------------------------------------------
    | Rekening transfer untuk pembayaran
    |--------------------------------------------------------------------------
    */
    'rekening' => [
        ['bank' => 'BCA', 'nama' => 'Baikboss Multi Services', 'no' => '1234567890'],
        ['bank' => 'BRI', 'nama' => 'Baikboss Multi Services', 'no' => '098765432112345'],
    ],
];
