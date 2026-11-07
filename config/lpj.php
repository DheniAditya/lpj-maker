<?php

return [
    /*
    | Nomor WhatsApp admin untuk pengguna yang lupa password (format internasional, tanpa +).
    | Bisa diganti lewat .env: ADMIN_WHATSAPP=6285198215605
    */
    'admin_whatsapp' => env('ADMIN_WHATSAPP', '6285198215605'),

    /*
    | Kop PDF LPJ: nama instansi, alamat, dan logo (path relatif ke folder public).
    */
    'pdf' => [
        'org' => env('LPJ_PDF_ORG', 'IDN Boarding School Solo'),
        'address' => env('LPJ_PDF_ADDRESS', 'Dusun Gondanglegi, Berjo, Kec. Ngargoyoso, Kabupaten Karanganyar, Jawa Tengah'),
        'logo' => env('LPJ_PDF_LOGO', 'images/logo-idn-solo.png'),
    ],
];
