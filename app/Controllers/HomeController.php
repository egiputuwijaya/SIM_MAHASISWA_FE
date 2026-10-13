<?php
require_once 'Controller.php';

/**
 * Controller halaman publik (tanpa login): Beranda, Fakultas & Prodi, About Dev.
 * Data institusi diambil dinamis dari endpoint API (/public/landing atau /universitas).
 */
class HomeController extends Controller {

    private const CACHE_TTL = 30; // 30 detik agar pembaruan profil cepat tampil

    /**
     * Lokasi file cache profil universitas sebagai jaminan ketersediaan data footer.
     */
    public static function getProfileCacheFile() {
        return __DIR__ . '/../../public/cache_profil_universitas.json';
    }

    /**
     * Simpan data profil institusi ke file cache lokal.
     */
    public static function saveProfileCache($uni) {
        if (!empty($uni) && is_array($uni)) {
            $file = self::getProfileCacheFile();
            @file_put_contents($file, json_encode($uni, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        }
    }

    /**
     * Ambil data profil institusi dari file cache lokal jika request API gagal/belum login.
     */
    public static function loadProfileCache() {
        $file = self::getProfileCacheFile();
        if (file_exists($file)) {
            $raw = @file_get_contents($file);
            $json = json_decode($raw, true);
            if (!empty($json) && is_array($json)) {
                return $json;
            }
        }
        return null;
    }

    /**
     * Ambil data landing dari API dengan prioritas keakuratan profil institusi.
     */
    private function getLandingData() {
        $cache = $_SESSION['landing_cache'] ?? null;
        if ($cache && (time() - ($cache['at'] ?? 0)) < self::CACHE_TTL && !isset($_GET['refresh'])) {
            return $cache['data'];
        }

        $default = [
            'universitas' => null,
            'stats' => [
                'totalFakultas'       => null,
                'totalProdi'          => null,
                'totalProdiAktif'     => null,
                'totalMahasiswa'      => null,
                'totalMahasiswaAktif' => null,
            ],
            'fakultas' => [],
        ];

        $token = $_SESSION['token'] ?? null;

        // 1. Coba endpoint publik /public/landing
        $res = ApiClient::get('/public/landing');
        if (!empty($res['success']) && !empty($res['data'])) {
            $data = array_replace_recursive($default, $res['data']);
            
            // Jika universitas belum disertakan atau kosong, panggil /universitas
            if (empty($data['universitas'])) {
                $uniRes = ApiClient::get('/universitas', ['limit' => 1], $token);
                if (!empty($uniRes['data'])) {
                    $data['universitas'] = isset($uniRes['data'][0]) ? $uniRes['data'][0] : $uniRes['data'];
                }
            }

            if (!empty($data['universitas'])) {
                self::saveProfileCache($data['universitas']);
            } else {
                $data['universitas'] = self::loadProfileCache();
            }

            $_SESSION['landing_cache'] = ['at' => time(), 'data' => $data];
            return $data;
        }

        // 2. Fallback: jika /public/landing belum aktif, ambil langsung via /universitas
        $data = $default;

        $uniRes = ApiClient::get('/universitas', ['limit' => 1], $token);
        if (!empty($uniRes['data'])) {
            $uni = isset($uniRes['data'][0]) ? $uniRes['data'][0] : $uniRes['data'];
            $data['universitas'] = $uni;
            self::saveProfileCache($uni);
        } else {
            $data['universitas'] = self::loadProfileCache();
        }

        // 3. Jika user memiliki token sesi, lengkapi daftar fakultas & statistik
        if ($token) {
            $fak = ApiClient::get('/fakultas', ['limit' => 100], $token);
            $prodi = ApiClient::get('/program-studi', ['limit' => 100], $token);
            $mhs = ApiClient::get('/mahasiswa', ['limit' => 1], $token);

            $fakList = $fak['data'] ?? [];
            $prodiList = $prodi['data'] ?? [];

            $data['stats']['totalFakultas']  = $fak['meta']['total'] ?? count($fakList);
            $data['stats']['totalProdi']     = $prodi['meta']['total'] ?? count($prodiList);
            $data['stats']['totalMahasiswa'] = $mhs['meta']['total'] ?? null;

            foreach ($fakList as $f) {
                $items = array_values(array_filter($prodiList, function ($p) use ($f) {
                    return (string)($p['idFakultas'] ?? '') === (string)($f['id'] ?? '');
                }));
                $data['fakultas'][] = [
                    'id'             => (string)($f['id'] ?? ''),
                    'kodeFakultas'   => $f['kodeFakultas'] ?? '',
                    'namaFakultas'   => $f['namaFakultas'] ?? '',
                    'totalProdi'     => count($items),
                    'totalMahasiswa' => null,
                    'programStudi'   => array_map(function ($p) {
                        return [
                            'kodeProgramStudi' => $p['kodeProgramStudi'] ?? '',
                            'namaProgramStudi' => $p['namaProgramStudi'] ?? '',
                            'jenjang'          => $p['jenjang'] ?? 'S1',
                            'statusAktif'      => $p['statusAktif'] ?? 'Aktif',
                            'totalMahasiswa'   => null,
                        ];
                    }, $items),
                ];
            }
        }

        if (!empty($data['universitas'])) {
            $_SESSION['landing_cache'] = ['at' => time(), 'data' => $data];
        }

        return $data;
    }

    public function index() {
        $landing = $this->getLandingData();
        $kodeUni = $landing['universitas']['kodeUniversitas'] ?? $landing['universitas']['kode_universitas'] ?? 'UMB';
        $this->view('home/index', [
            'landing'    => $landing,
            'title'      => "SIM Mahasiswa {$kodeUni} — Portal Informasi Akademik",
            'activePage' => 'home',
        ]);
    }

    public function fakultasProdi() {
        $landing = $this->getLandingData();
        $kodeUni = $landing['universitas']['kodeUniversitas'] ?? $landing['universitas']['kode_universitas'] ?? 'UMB';
        $this->view('home/fakultas-prodi', [
            'landing'    => $landing,
            'title'      => "Fakultas & Program Studi — SIM Mahasiswa {$kodeUni}",
            'activePage' => 'fakultas-prodi',
        ]);
    }

    public function aboutDev() {
        $landing = $this->getLandingData();
        $kodeUni = $landing['universitas']['kodeUniversitas'] ?? $landing['universitas']['kode_universitas'] ?? 'UMB';
        $this->view('home/about-dev', [
            'landing'    => $landing,
            'title'      => "About Dev — SIM Mahasiswa {$kodeUni}",
            'activePage' => 'about-dev',
        ]);
    }
}

