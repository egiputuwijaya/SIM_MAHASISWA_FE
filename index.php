<?php
/**
 * SIM Mahasiswa - Entry Point Bridge untuk Hosting (InfinityFree / cPanel)
 * 
 * File ini bertindak sebagai jembatan langsung ketika proyek diunggah ke folder htdocs
 * pada hosting yang tidak mendukung pengaturan DocumentRoot secara kustom.
 */

// Arahkan eksekusi langsung ke kernel publik
require_once __DIR__ . '/public/index.php';
