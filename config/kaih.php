<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Batas Pengisian
    |--------------------------------------------------------------------------
    |
    | Jumlah maksimal kuesioner yang boleh diisi oleh satu pengguna.
    |
    */

    'max_submissions' => (int) env('KAIH_MAX_SUBMISSIONS', 7),

    /*
    |--------------------------------------------------------------------------
    | Akun Google yang diizinkan
    |--------------------------------------------------------------------------
    |
    | Domain email yang boleh login. Kosongkan untuk mengizinkan semua akun
    | Google (misalnya gmail.com dan belajar.id).
    |
    */

    'allowed_email_domains' => array_values(array_filter(array_map(
        fn (string $domain): string => strtolower(trim($domain)),
        explode(',', (string) env('KAIH_ALLOWED_EMAIL_DOMAINS', 'gmail.com')),
    ))),

    /*
    |--------------------------------------------------------------------------
    | Super Admin awal
    |--------------------------------------------------------------------------
    |
    | Email di daftar ini otomatis menjadi Super Admin saat login.
    |
    */

    'super_admin_emails' => array_values(array_filter(array_map(
        fn (string $email): string => strtolower(trim($email)),
        explode(',', (string) env('KAIH_SUPER_ADMIN_EMAILS', '')),
    ))),

    /*
    |--------------------------------------------------------------------------
    | Login pengembangan
    |--------------------------------------------------------------------------
    |
    | Hanya aktif saat APP_ENV=local. Memungkinkan uji coba tanpa kredensial
    | Google OAuth. JANGAN aktifkan di server produksi.
    |
    */

    'dev_login' => (bool) env('KAIH_DEV_LOGIN', false),

    /*
    |--------------------------------------------------------------------------
    | Skala Jawaban
    |--------------------------------------------------------------------------
    */

    'scale' => [
        1 => ['label' => 'Tidak Pernah', 'hint' => '0 hari', 'emoji' => '😴'],
        2 => ['label' => 'Kadang-kadang', 'hint' => '1–2 hari', 'emoji' => '🙂'],
        3 => ['label' => 'Sering', 'hint' => '3–4 hari', 'emoji' => '😄'],
        4 => ['label' => 'Selalu', 'hint' => 'setiap hari', 'emoji' => '🤩'],
    ],

    'period_hint' => 'Jawab berdasarkan apa yang kamu lakukan sejak pengisian terakhir (± 3–4 hari terakhir).',

    /*
    |--------------------------------------------------------------------------
    | Kategori Skor (rata-rata 1–4)
    |--------------------------------------------------------------------------
    */

    'levels' => [
        ['min' => 3.5, 'label' => 'Membudaya', 'emoji' => '🏆', 'color' => '#059669', 'classes' => 'bg-emerald-100 text-emerald-700'],
        ['min' => 2.75, 'label' => 'Berkembang Baik', 'emoji' => '🌟', 'color' => '#0284c7', 'classes' => 'bg-sky-100 text-sky-700'],
        ['min' => 2.0, 'label' => 'Mulai Berkembang', 'emoji' => '🌱', 'color' => '#d97706', 'classes' => 'bg-amber-100 text-amber-700'],
        ['min' => 0, 'label' => 'Perlu Pendampingan', 'emoji' => '🤗', 'color' => '#e11d48', 'classes' => 'bg-rose-100 text-rose-700'],
    ],

];
