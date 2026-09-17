<?php

return [
    'brand' => 'baikboss!',

    /* Nomor WA tim (kontak di halaman ringkasan BossMove) */
    'wa_tim' => env('BAIKBOSS_WA_TIM', '6281234567890'),
    'wa_bossmove' => env('BAIKBOSS_WA_BOSSMOVE', '6281234567891'), // WA tenaga pindahan BossMove
    'email_tim' => env('BAIKBOSS_EMAIL_TIM', 'hello@baikboss.id'),

    /* Jenis layanan pindahan */
    'services' => [
        ['code' => 'rumah', 'label' => 'Pindah Rumah', 'icon' => 'home'],
        ['code' => 'kos', 'label' => 'Pindah Kos', 'icon' => 'sofa'],
        ['code' => 'warung', 'label' => 'Pindah Warung', 'icon' => 'package'],
        ['code' => 'kantor', 'label' => 'Pindah Kantor', 'icon' => 'clipboard-list'],
    ],

    /* Katalog barang besar + biaya handling per unit (Rp) */
    'items' => [
        ['code' => 'lemari', 'label' => 'Lemari', 'icon' => 'door-open', 'price' => 50000],
        ['code' => 'meja', 'label' => 'Meja', 'icon' => 'armchair', 'price' => 30000],
        ['code' => 'kursi', 'label' => 'Kursi', 'icon' => 'sofa', 'price' => 15000],
        ['code' => 'kulkas', 'label' => 'Kulkas', 'icon' => 'snowflake', 'price' => 40000],
        ['code' => 'mesin_cuci', 'label' => 'Mesin Cuci', 'icon' => 'washing-machine', 'price' => 40000],
        ['code' => 'tv', 'label' => 'TV', 'icon' => 'monitor', 'price' => 25000],
        ['code' => 'ac', 'label' => 'AC', 'icon' => 'snowflake', 'price' => 60000],
    ],

    /* Tarif luar Bojonegoro (flat, Rp) */
    'tarif_luar_bojonegoro' => env('BAIKBOSS_TARIF_LUAR', 250000),

    /* Ongkir default layanan BossGlow (massage/facial) */
    'ongkir_default' => env('BAIKBOSS_ONGKIR', 10000),

    /*
    | Katalog layanan BossGlow — dipakai GlowController & wizard frontend
    | gender: gender terapis yang melayani
    */
    'glow' => [
        [
            'key' => 'massage_male',
            'label' => 'Massage Pria',
            'category' => 'massage',
            'gender' => 'male',
            'gender_label' => 'pria',
            'duration_hours' => 1,
            'price' => 75000,
            'packages' => [
                ['duration_hours' => 1, 'price' => 75000, 'label' => 'Massage Pria 1 Jam'],
                ['duration_hours' => 2, 'price' => 150000, 'label' => 'Massage Pria 2 Jam'],
            ],
        ],
        [
            'key' => 'massage_female',
            'label' => 'Massage Wanita',
            'category' => 'massage',
            'gender' => 'female',
            'gender_label' => 'wanita',
            'duration_hours' => 1,
            'price' => 75000,
            'packages' => [
                ['duration_hours' => 1, 'price' => 75000, 'label' => 'Massage Wanita 1 Jam'],
                ['duration_hours' => 2, 'price' => 150000, 'label' => 'Massage Wanita 2 Jam'],
            ],
        ],
        [
            'key' => 'facial',
            'label' => 'Facial Wanita',
            'category' => 'facial',
            'gender' => 'female',
            'gender_label' => 'wanita',
            'duration_hours' => 1,
            'price' => 120000,
            'packages' => [
                ['duration_hours' => 1, 'price' => 120000, 'label' => 'Facial Wanita 1 Jam'],
                ['duration_hours' => 1, 'price' => 155000, 'label' => 'Facial + Totok Wajah'],
            ],
        ],
    ],

    /* Rekening transfer untuk pembayaran */
    'rekening' => [
        ['bank' => 'BCA', 'nama' => 'Baikboss Multi Services', 'no' => '1234567890'],
        ['bank' => 'BRI', 'nama' => 'Baikboss Multi Services', 'no' => '098765432112345'],
    ],
];
