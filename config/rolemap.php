<?php

return [
    'dashboard'      => ['owner','kapus'],
    'loket'          => ['owner','admin','loket'],
    'pasien'         => ['loket'],                          // ← tambahan
    'laporan'        => ['owner','admin','loket','pelayanan'],
    'ruang_layanan'  => ['owner','admin','pelayanan'],
    'mal_sehat'      => ['owner','admin'],
    'laborat'        => ['laborat','pelayanan'],            // ← tambahkan pelayanan
];
