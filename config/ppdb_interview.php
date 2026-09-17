<?php

// Nilai akhir = jumlah poin / poin maksimal * 100 + bonus.
$favorite = ['A' => 'Ustadz manhaj salaf terkenal', 'B' => 'Guru mereka', 'C' => 'Ustadz non-salaf (sesuai penilaian pewawancara)'];
$attendance = ['A' => 'Hadir', 'B' => 'Tidak hadir: izin syar’i / meninggal / cerai', 'C' => 'Tidak hadir karena kesibukan / tanpa alasan'];
$memorization = ['A' => 'Tahfiz / punya hafalan', 'B' => 'Masih ikut tahsin', 'C' => 'Belum ikut tahsin'];
$music = ['A' => 'Tidak suka', 'B' => 'Kadang-kadang', 'C' => 'Suka / sering'];
$support = ['A' => 'Sangat mendukung', 'B' => 'Kurang mendukung', 'C' => 'Tidak mendukung'];
$payment = ['A' => 'Sanggup', 'B' => 'Diusahakan', 'C' => 'Tidak sanggup'];
$difficulty = ['A' => 'Berusaha semaksimal mungkin untuk membayar', 'B' => 'Meminta keringanan ma’had', 'C' => 'Memindahkan anak ke sekolah lain'];

return [
    'weights' => ['A' => 3, 'B' => 2, 'C' => 1],
    'threshold' => 70,
    'sections' => [
        'child' => ['label' => 'Anak', 'questions' => [
            'motivation' => ['label' => 'Siapa yang menyuruh masuk ke Darussalam?', 'options' => ['A' => 'Keinginan orang tua dan sendiri', 'B' => 'Keinginan sendiri', 'C' => 'Keinginan orang tua']],
            'sport' => ['label' => 'Apakah suka olahraga dan apa jenisnya?', 'options' => ['A' => 'Bola / bulu tangkis / basket / bela diri', 'B' => 'Joging / fitness', 'C' => 'Tidak suka olahraga']],
            'favorite' => ['label' => 'Siapa ustadz favorit?', 'options' => $favorite, 'reject_c' => true],
            'manhaj' => ['label' => 'Apa manhaj salaf?', 'options' => ['A' => 'Bisa menjelaskan', 'B' => 'Kurang bisa menjelaskan', 'C' => 'Tidak bisa menjelaskan']],
            'memorization' => ['label' => 'Berapa jumlah hafalan?', 'options' => ['A' => '4 juz ke atas', 'B' => '1–3 juz', 'C' => 'Kurang dari 1 juz']],
            'hitting' => ['label' => 'Pernah memukul teman?', 'options' => ['A' => 'Tidak pernah', 'C' => 'Pernah']],
            'bullying' => ['label' => 'Apa tindakan ketika teman dirundung?', 'options' => ['A' => 'Menolong', 'B' => 'Membiarkan', 'C' => 'Ikut merundung / mengejek']],
            'mocking' => ['label' => 'Pernah mengejek dengan nama binatang atau orang tua?', 'options' => ['A' => 'Tidak pernah', 'B' => 'Jarang', 'C' => 'Sering']],
            'gaming' => ['label' => 'Apakah suka bermain game?', 'options' => ['A' => 'Tidak pernah dan tidak main HP', 'B' => 'Kadang-kadang', 'C' => 'Sering / punya HP sendiri']],
            'relative' => ['label' => 'Apakah ada saudara di Darussalam?', 'options' => ['A' => 'Saudara kandung (kakak / abang)', 'B' => 'Saudara dekat / keluarga', 'C' => 'Tidak ada']],
        ]],
        'father' => ['label' => 'Ayah', 'questions' => [
            'attendance' => ['label' => 'Apakah ayah hadir mendampingi?', 'options' => $attendance, 'reject_c' => true],
            'appearance' => ['label' => 'Penampilan ayah', 'options' => ['A' => 'Jubah / gamis', 'B' => 'Kemeja / batik', 'C' => 'Kaos / jeans']],
            'beard' => ['label' => 'Apakah ayah berjenggot?', 'options' => ['A' => 'Berjenggot', 'B' => 'Dipotong / dirapikan', 'C' => 'Dicukur habis']],
            'prayer' => ['label' => 'Apakah ayah salat berjamaah di masjid?', 'options' => ['A' => 'Insyaallah selalu', 'B' => 'Kadang-kadang', 'C' => 'Jarang / tidak pernah']],
            'favorite' => ['label' => 'Siapa ustadz favorit ayah?', 'options' => $favorite, 'reject_c' => true],
            'smoking' => ['label' => 'Apakah ayah merokok atau vape?', 'options' => ['A' => 'Tidak', 'C' => 'Iya']],
            'memorization' => ['label' => 'Hafalan ayah', 'options' => $memorization],
            'music' => ['label' => 'Apakah ayah suka musik?', 'options' => $music],
            'support' => ['label' => 'Apakah ayah mendukung ananda mondok?', 'options' => $support],
            'payment' => ['label' => 'Kesanggupan membayar SPP tanggal 10 dan biaya lain tepat waktu', 'options' => $payment],
            'difficulty' => ['label' => 'Apa yang dilakukan jika mengalami kesulitan ekonomi?', 'options' => $difficulty],
        ]],
        'mother' => ['label' => 'Ibu', 'questions' => [
            'attendance' => ['label' => 'Apakah ibu hadir mendampingi?', 'options' => $attendance, 'reject_c' => true],
            'appearance' => ['label' => 'Penampilan ibu', 'options' => ['A' => 'Gamis dan hijab syar’i', 'B' => 'Pakaian longgar dan hijab', 'C' => 'Pakaian ketat / tidak berhijab']],
            'prayer' => ['label' => 'Apakah ibu salat tepat waktu?', 'options' => ['A' => 'Insyaallah selalu', 'B' => 'Kadang-kadang', 'C' => 'Jarang / tidak pernah']],
            'favorite' => ['label' => 'Siapa ustadz favorit ibu?', 'options' => $favorite, 'reject_c' => true],
            'memorization' => ['label' => 'Hafalan ibu', 'options' => $memorization],
            'music' => ['label' => 'Apakah ibu suka musik?', 'options' => $music],
            'support' => ['label' => 'Apakah ibu mendukung ananda mondok?', 'options' => $support],
            'payment' => ['label' => 'Kesanggupan membayar SPP tanggal 10 dan biaya lain tepat waktu', 'options' => $payment],
            'difficulty' => ['label' => 'Apa yang dilakukan jika mengalami kesulitan ekonomi?', 'options' => $difficulty],
        ]],
    ],
];
