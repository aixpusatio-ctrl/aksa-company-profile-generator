<?php

namespace App\Support;

/**
 * Realistic demo content (fictional Indonesian companies, Bahasa Indonesia copy)
 * used to fill template previews and the database seeder.
 *
 * Every website layout gets its own company in a matching industry so each
 * template preview feels authentic. All images are deterministic picsum URLs.
 */
final class DemoContent
{
    private const LAYOUTS = [
        'corporate' => 'corporate',
        'modern-business' => 'modernBusiness',
        'technology' => 'technology',
        'construction' => 'construction',
        'manufacturing' => 'manufacturing',
        'consulting' => 'consulting',
        'creative-agency' => 'creativeAgency',
        'professional-services' => 'professionalServices',
        'minimal' => 'minimal',
        'executive' => 'executive',
    ];

    /**
     * Layout keys that have dedicated demo content.
     */
    public static function layouts(): array
    {
        return array_keys(self::LAYOUTS);
    }

    /**
     * Complete demo data set for a layout; unknown layouts fall back to corporate.
     */
    public static function forLayout(string $layout): array
    {
        $method = self::LAYOUTS[$layout] ?? self::LAYOUTS['corporate'];

        return self::$method();
    }

    /**
     * Demo data set for a template: a data file in app/Support/Demo/data/{key}.php
     * when present, otherwise the dataset of the hand-crafted layout.
     */
    public static function for(string $key): array
    {
        $file = __DIR__.'/Demo/data/'.basename($key).'.php';

        return is_file($file) ? self::fromData(require $file) : self::forLayout($key);
    }

    /** Keys of all data-file datasets. */
    public static function dataKeys(): array
    {
        return array_map(fn ($file) => basename($file, '.php'), glob(__DIR__.'/Demo/data/*.php') ?: []);
    }

    /**
     * Expand a compact data file (see app/Support/Demo/data/README.md) into
     * the full structure used by previews and the seeder.
     */
    public static function fromData(array $d): array
    {
        $prefix = $d['prefix'];
        $company = $d['company'];

        foreach (['about', 'history'] as $field) {
            if (is_array($company[$field] ?? null)) {
                $company[$field] = self::paragraphs($company[$field]);
            }
        }
        if (is_array($company['mission'] ?? null)) {
            $company['mission'] = self::bullets($company['mission']);
        }
        if (is_array($company['company_values'] ?? null)) {
            $company['company_values'] = self::values($company['company_values']);
        }
        if (is_array($company['social_links'] ?? null) && array_is_list($company['social_links'])) {
            $company['social_links'] = self::socials($d['handle'] ?? $prefix, $company['social_links']);
        }
        if (isset($company['highlights'])) {
            $company['highlights'] = array_map(fn ($row) => ['value' => (string) $row[0], 'label' => $row[1]], $company['highlights']);
        }
        $company['hero_image'] ??= self::img("{$prefix}-hero", 1600, 1000);
        $company['country'] ??= 'Indonesia';

        $page = $d['page'];

        return [
            'company' => $company,
            'services' => self::services($prefix, $d['services']),
            'products' => self::products($prefix, $d['products']),
            'projects' => self::projects($prefix, $d['projects']),
            'team' => self::team($prefix, $d['domain'], $d['team']),
            'testimonials' => self::testimonials($prefix, $d['testimonials']),
            'gallery' => self::gallery($prefix, $d['gallery']),
            'pages' => [
                self::page($prefix, $page[0], $page[1], $page[2], $page[3], $page[4], $page[5], $page[6]),
                self::careerPage($prefix, $company['name'], $company['email'], $d['roles']),
                self::privacyPage($prefix, $company['name'], $company['email']),
            ],
        ];
    }

    /**
     * Nested demo navigation used by template previews.
     */
    public static function menus(): array
    {
        $item = fn (string $title, string $type, string $url, int $order, array $children = []) => [
            'title' => $title,
            'type' => $type,
            'url' => $url,
            'status' => 'active',
            'sort_order' => $order,
            'children' => $children,
        ];

        $child = function (string $title, string $type, string $url, int $order) use ($item): array {
            $data = $item($title, $type, $url, $order);
            unset($data['children']);

            return $data;
        };

        return [
            $item('Home', 'anchor', 'hero', 1),
            $item('Perusahaan', 'group', '#', 2, [
                $child('Tentang Kami', 'anchor', 'about', 1),
                $child('Tim', 'anchor', 'team', 2),
                $child('Karir', 'page', 'karir', 3),
            ]),
            $item('Layanan', 'anchor', 'services', 3),
            $item('Produk', 'anchor', 'products', 4),
            $item('Portofolio', 'anchor', 'projects', 5),
            $item('Kontak', 'anchor', 'contact', 6),
        ];
    }

    // ------------------------------------------------------------------ Industries

    /**
     * Corporate: diversified holding & logistics group.
     */
    private static function corporate(): array
    {
        $p = 'corp';
        $name = 'PT Arunika Samudra Nusantara';
        $domain = 'arunikagroup.co.id';

        return [
            'company' => [
                'name' => $name,
                'tagline' => 'Menghubungkan Nusantara melalui logistik, energi, dan properti terintegrasi',
                'description' => 'Arunika Group adalah perusahaan induk terdiversifikasi yang bergerak di bidang logistik, energi, dan properti komersial. Dengan jaringan di 24 provinsi, kami mendukung rantai pasok ribuan pelaku usaha di seluruh Indonesia.',
                'established_year' => 1998,
                'phone' => '(021) 2930 5500',
                'email' => 'info@'.$domain,
                'whatsapp' => '081234567890',
                'address' => 'Arunika Tower Lt. 18, Jl. Jend. Sudirman Kav. 52-53',
                'city' => 'Jakarta Selatan',
                'province' => 'DKI Jakarta',
                'country' => 'Indonesia',
                'postal_code' => '12190',
                'latitude' => -6.2253,
                'longitude' => 106.8089,
                'working_hours' => 'Senin - Jumat, 08.00 - 17.00',
                'website' => 'https://www.'.$domain,
                'social_links' => self::socials('arunikagroup', ['facebook', 'instagram', 'linkedin', 'youtube', 'x']),
                'about' => self::paragraphs([
                    'Didirikan pada tahun 1998 sebagai perusahaan ekspedisi antarpulau, Arunika Group kini tumbuh menjadi grup usaha dengan tiga pilar bisnis utama: logistik terintegrasi, distribusi energi, dan pengembangan properti komersial.',
                    'Didukung lebih dari 4.500 karyawan, armada 1.200 unit, serta 38 pusat distribusi, kami berkomitmen menghadirkan nilai jangka panjang bagi pelanggan, mitra, pemegang saham, dan masyarakat.',
                ]),
                'vision' => 'Menjadi grup usaha terintegrasi terdepan di Indonesia yang menggerakkan pertumbuhan ekonomi nasional secara berkelanjutan.',
                'mission' => self::bullets([
                    'Menyediakan solusi logistik dan rantai pasok yang andal, efisien, dan terjangkau di seluruh Nusantara.',
                    'Mengembangkan portofolio bisnis yang sehat dengan tata kelola perusahaan yang baik.',
                    'Membangun sumber daya manusia yang kompeten, berintegritas, dan berdaya saing global.',
                    'Memberikan dampak positif bagi lingkungan dan masyarakat di setiap wilayah operasional.',
                ]),
                'history' => self::paragraphs([
                    'Perjalanan Arunika dimulai dari sebuah gudang kecil di Tanjung Priok dengan lima unit truk. Berkat kepercayaan pelanggan, perusahaan berkembang pesat dan membuka jalur pelayaran antarpulau pertama pada tahun 2004.',
                    'Memasuki dekade 2010-an, Arunika melakukan diversifikasi ke sektor distribusi energi dan properti komersial. Pada 2021, grup ini menyelesaikan transformasi digital menyeluruh untuk seluruh lini operasional.',
                ]),
                'company_values' => self::values([
                    'Integritas' => 'jujur, transparan, dan bertanggung jawab dalam setiap keputusan bisnis.',
                    'Keunggulan' => 'menetapkan standar tinggi dan terus melakukan perbaikan berkelanjutan.',
                    'Kolaborasi' => 'bersinergi lintas unit usaha untuk menciptakan nilai yang lebih besar.',
                    'Kepedulian' => 'menempatkan keselamatan, karyawan, dan masyarakat sebagai prioritas.',
                    'Inovasi' => 'berani beradaptasi dengan teknologi dan kebutuhan pasar yang berubah.',
                ]),
                'hero_image' => self::img("$p-hero", 1600, 900),
                'logo' => null,
                'seo_title' => 'Arunika Group - Holding Logistik, Energi & Properti Indonesia',
                'seo_description' => 'Arunika Group adalah perusahaan induk terdiversifikasi di bidang logistik terintegrasi, distribusi energi, dan properti komersial dengan jaringan di 24 provinsi.',
                'seo_keywords' => 'holding company, logistik indonesia, distribusi energi, properti komersial, arunika group',
            ],
            'services' => self::services($p, [
                ['Logistik Terintegrasi', 'Layanan end-to-end mulai dari pergudangan, transportasi darat, hingga pengiriman antarpulau dengan pelacakan real-time.', 'truck'],
                ['Manajemen Rantai Pasok', 'Perencanaan dan optimasi rantai pasok untuk menekan biaya operasional dan mempercepat waktu pengiriman.', 'chart'],
                ['Distribusi Energi', 'Distribusi BBM industri dan LPG ke sektor manufaktur, pertambangan, dan perkebunan di seluruh Indonesia.', 'bolt'],
                ['Properti Komersial', 'Pengembangan dan pengelolaan gedung perkantoran, kawasan pergudangan, serta pusat distribusi modern.', 'building'],
                ['Freight Forwarding', 'Pengurusan ekspor-impor, kepabeanan, dan konsolidasi kargo internasional melalui jaringan mitra global.', 'globe'],
                ['Layanan Korporat', 'Dukungan keuangan, SDM, dan teknologi bersama untuk seluruh anak perusahaan dan mitra strategis.', 'briefcase'],
            ]),
            'products' => self::products($p, [
                ['Arunika Express Cargo', 'Pengiriman kargo darat reguler dan ekspres ke lebih dari 400 kota dengan jaminan waktu tiba.', null, 'Logistik'],
                ['Arunika Smart Warehouse', 'Sewa ruang gudang berstandar internasional lengkap dengan sistem WMS dan keamanan 24 jam.', 85000, 'Pergudangan'],
                ['Arunika Fleet Rental', 'Penyewaan truk dan trailer beserta pengemudi tersertifikasi untuk kebutuhan proyek jangka panjang.', 2750000, 'Transportasi'],
                ['Arunika Energi Industri', 'Pasokan solar industri dan pelumas dengan jadwal pengiriman terjamin langsung ke lokasi pelanggan.', null, 'Energi'],
                ['Arunika Business Park', 'Kawasan perkantoran dan pergudangan terpadu di koridor industri Cikarang dan Gresik.', null, 'Properti'],
            ]),
            'projects' => self::projects($p, [
                ['Pusat Distribusi Regional Cikarang', 'Pembangunan dan operasional pusat distribusi seluas 45.000 m² untuk jaringan ritel nasional.', 'PT Sumber Ritel Nusantara', 'Bekasi, Jawa Barat', 2023, 'Logistik', null],
                ['Logistik Proyek Smelter Morowali', 'Pengiriman 18.000 ton material konstruksi dan alat berat ke kawasan industri Morowali.', 'PT Sulawesi Nikel Industri', 'Morowali, Sulawesi Tengah', 2022, 'Project Cargo', null],
                ['Digitalisasi Armada Nasional', 'Implementasi telematika dan sistem manajemen armada untuk 1.200 unit kendaraan operasional.', 'Internal Arunika Group', 'Jakarta', 2021, 'Transformasi Digital', null],
                ['Distribusi BBM Perkebunan Kalimantan', 'Kontrak distribusi solar industri untuk 32 perkebunan kelapa sawit di Kalimantan Tengah.', 'Borneo Agro Lestari', 'Palangkaraya, Kalimantan Tengah', 2024, 'Energi', null],
                ['Arunika Tower Sudirman', 'Pengembangan gedung perkantoran grade A setinggi 28 lantai bersertifikat Green Building.', 'Arunika Properti', 'Jakarta Selatan', 2019, 'Properti', 'https://www.arunikagroup.co.id/properti'],
                ['Cold Chain Indonesia Timur', 'Pembangunan jaringan rantai dingin untuk distribusi produk perikanan dari Maluku ke Jawa.', 'Koperasi Nelayan Maluku Sejahtera', 'Ambon, Maluku', 2025, 'Logistik', null],
            ]),
            'team' => self::team($p, $domain, [
                ['Hendra Wijayakusuma', 'Presiden Direktur', 'Memimpin Arunika Group selama lebih dari 15 tahun dengan fokus pada ekspansi dan tata kelola perusahaan.', 'hendra-wijayakusuma', 'hendra.w'],
                ['Ratna Dewi Kartika', 'Direktur Keuangan', 'Berpengalaman 20 tahun di perbankan korporat dan pasar modal sebelum bergabung dengan Arunika.', 'ratna-dewi-kartika', 'ratna.kartika'],
                ['Bambang Setiadi', 'Direktur Operasional Logistik', 'Ahli rantai pasok yang membangun jaringan 38 pusat distribusi Arunika di seluruh Indonesia.', 'bambang-setiadi', 'bambang.setiadi'],
                ['Maya Anggraini', 'Direktur Sumber Daya Manusia', 'Menggerakkan program pengembangan talenta dan budaya kerja di seluruh unit usaha grup.', 'maya-anggraini', 'maya.anggraini'],
            ]),
            'testimonials' => self::testimonials($p, [
                ['Andi Prasetyo', 'PT Sumber Ritel Nusantara', 'Arunika menjadi mitra logistik kami selama delapan tahun dengan tingkat ketepatan pengiriman di atas 98%. Tim mereka sangat responsif bahkan saat puncak musim belanja.', 5],
                ['Siti Rahmawati', 'Borneo Agro Lestari', 'Pasokan energi untuk perkebunan kami tidak pernah terlambat sejak bekerja sama dengan Arunika. Pelaporan digitalnya juga memudahkan pengawasan biaya.', 5],
                ['Rudi Hartono', 'PT Sulawesi Nikel Industri', 'Pengiriman alat berat ke lokasi terpencil terlaksana aman dan sesuai jadwal. Koordinasi proyek dilakukan dengan sangat profesional.', 4],
            ]),
            'gallery' => self::gallery($p, [
                ['Pusat Distribusi Cikarang', 'Fasilitas pergudangan modern dengan sistem rak otomatis.', 'Fasilitas'],
                ['Armada Arunika Express', 'Armada truk yang dilengkapi GPS dan sensor suhu.', 'Armada'],
                ['Pelabuhan Tanjung Priok', 'Aktivitas bongkar muat kargo antarpulau.', 'Operasional'],
                ['Arunika Tower', 'Kantor pusat Arunika Group di kawasan Sudirman.', 'Kantor'],
                ['Program Arunika Peduli', 'Kegiatan donor darah dan bakti sosial karyawan.', 'CSR'],
                ['Rapat Umum Pemegang Saham', 'RUPS tahunan Arunika Group 2024.', 'Korporat'],
            ]),
            'pages' => [
                self::careerPage($p, 'Arunika Group', 'karir@'.$domain, ['Supply Chain Analyst', 'Fleet Operations Supervisor', 'Corporate Finance Officer', 'Management Trainee Program']),
                self::privacyPage($p, $name, 'privasi@'.$domain),
                self::page($p, 'Tata Kelola Perusahaan', 'tata-kelola', [
                    'Arunika Group meyakini bahwa tata kelola perusahaan yang baik (Good Corporate Governance) merupakan fondasi pertumbuhan yang berkelanjutan. Seluruh unit usaha kami menerapkan prinsip transparansi, akuntabilitas, responsibilitas, independensi, dan kewajaran.',
                    'Dewan Komisaris dan Direksi menjalankan fungsi pengawasan serta pengurusan secara terpisah, didukung oleh Komite Audit, Komite Nominasi dan Remunerasi, serta Komite Manajemen Risiko.',
                ], 'Pilar Tata Kelola', [
                    'Kode Etik dan Pedoman Perilaku untuk seluruh insan Arunika',
                    'Sistem pelaporan pelanggaran (whistleblowing system) yang independen',
                    'Audit internal berbasis risiko secara berkala',
                    'Kebijakan anti-suap dan anti-korupsi bersertifikat ISO 37001',
                ], 'Laporan tata kelola tahunan kami dapat diunduh oleh pemegang saham dan pemangku kepentingan melalui halaman Hubungan Investor.', 'Komitmen Arunika Group dalam menerapkan Good Corporate Governance di seluruh unit usaha.'),
            ],
        ];
    }

    /**
     * Modern business: HR & payroll fintech startup.
     */
    private static function modernBusiness(): array
    {
        $p = 'mb';
        $name = 'PT Gajiku Teknologi Indonesia';
        $domain = 'gajiku.id';

        return [
            'company' => [
                'name' => $name,
                'tagline' => 'Payroll, HR, dan benefit karyawan dalam satu aplikasi',
                'description' => 'Gajiku adalah platform HR dan payroll berbasis cloud yang membantu bisnis mengelola penggajian, absensi, dan benefit karyawan secara otomatis. Lebih dari 3.000 perusahaan di Indonesia telah mempercayakan pengelolaan SDM mereka kepada Gajiku.',
                'established_year' => 2019,
                'phone' => '(021) 5089 1122',
                'email' => 'halo@'.$domain,
                'whatsapp' => '081290001234',
                'address' => 'Green Office Park 9, Jl. BSD Grand Boulevard',
                'city' => 'Tangerang Selatan',
                'province' => 'Banten',
                'country' => 'Indonesia',
                'postal_code' => '15345',
                'latitude' => -6.3012,
                'longitude' => 106.6522,
                'working_hours' => 'Senin - Jumat, 08.00 - 17.00',
                'website' => 'https://www.'.$domain,
                'social_links' => self::socials('gajiku.id', ['facebook', 'instagram', 'linkedin', 'youtube', 'tiktok', 'x']),
                'about' => self::paragraphs([
                    'Gajiku lahir dari pengalaman para pendirinya yang melihat betapa rumitnya proses penggajian di perusahaan Indonesia: perhitungan PPh 21, BPJS, lembur, hingga reimbursement yang masih dikerjakan manual di spreadsheet.',
                    'Kami membangun platform yang sederhana namun lengkap, sehingga tim HR dapat menghemat hingga 80% waktu administrasi dan fokus pada hal yang lebih penting: mengembangkan karyawan.',
                ]),
                'vision' => 'Menjadi platform pengelolaan SDM paling dipercaya oleh bisnis di Asia Tenggara.',
                'mission' => self::bullets([
                    'Mengotomatiskan proses payroll dan administrasi SDM agar akurat dan patuh regulasi.',
                    'Memberikan akses benefit finansial yang adil bagi setiap karyawan.',
                    'Menghadirkan data SDM real-time untuk pengambilan keputusan yang lebih baik.',
                    'Menjaga keamanan data pelanggan dengan standar keamanan kelas dunia.',
                ]),
                'history' => self::paragraphs([
                    'Gajiku didirikan pada 2019 di sebuah co-working space di BSD dengan sepuluh pelanggan pertama dari kalangan UMKM. Setahun kemudian, pandemi mendorong percepatan adopsi kerja jarak jauh dan fitur absensi berbasis lokasi kami digunakan oleh ratusan perusahaan.',
                    'Pada 2022 Gajiku memperoleh pendanaan Seri A dan izin sebagai penyelenggara layanan keuangan digital. Kini kami melayani lebih dari 250.000 karyawan setiap bulannya.',
                ]),
                'company_values' => self::values([
                    'Customer Obsessed' => 'setiap fitur berangkat dari masalah nyata pelanggan.',
                    'Move Fast, Stay Safe' => 'bergerak cepat tanpa mengorbankan keamanan dan kepatuhan.',
                    'Ownership' => 'bertanggung jawab penuh atas hasil kerja dari awal hingga akhir.',
                    'Transparansi' => 'terbuka dalam berkomunikasi, termasuk saat menyampaikan kabar buruk.',
                ]),
                'hero_image' => self::img("$p-hero", 1600, 900),
                'logo' => null,
                'seo_title' => 'Gajiku - Aplikasi Payroll & HR Online untuk Bisnis Indonesia',
                'seo_description' => 'Kelola payroll, PPh 21, BPJS, absensi, dan benefit karyawan secara otomatis dengan Gajiku. Dipercaya 3.000+ perusahaan di Indonesia.',
                'seo_keywords' => 'aplikasi payroll, software hr, aplikasi absensi, hitung pph 21, gajiku',
            ],
            'services' => self::services($p, [
                ['Payroll Otomatis', 'Hitung gaji, lembur, PPh 21, dan BPJS secara otomatis lalu transfer ke ratusan rekening dalam sekali klik.', 'banknotes'],
                ['Absensi Online', 'Absensi berbasis GPS dan pengenalan wajah dari aplikasi mobile, cocok untuk tim lapangan maupun hybrid.', 'phone'],
                ['Manajemen Cuti & Lembur', 'Pengajuan dan persetujuan cuti, izin, serta lembur secara digital dengan alur persetujuan fleksibel.', 'clipboard'],
                ['Earned Wage Access', 'Karyawan dapat menarik sebagian gaji yang sudah dihasilkan sebelum hari gajian tanpa bunga.', 'heart'],
                ['Analitik SDM', 'Dashboard real-time untuk memantau biaya tenaga kerja, turnover, dan produktivitas tim.', 'chart'],
                ['Keamanan Data', 'Data terenkripsi end-to-end dan tersimpan di pusat data Indonesia sesuai regulasi PDP.', 'shield'],
            ]),
            'products' => self::products($p, [
                ['Gajiku Starter', 'Paket payroll dan absensi untuk bisnis hingga 25 karyawan. Harga per bulan.', 299000, 'Paket Langganan'],
                ['Gajiku Growth', 'Payroll, absensi, cuti, dan reimbursement untuk bisnis yang sedang bertumbuh. Harga per karyawan per bulan.', 25000, 'Paket Langganan'],
                ['Gajiku Enterprise', 'Solusi kustom dengan integrasi ERP, SSO, dan manajer akun khusus.', null, 'Paket Langganan'],
                ['Gajiku Flex', 'Fitur earned wage access sebagai benefit karyawan tanpa biaya bagi perusahaan.', null, 'Benefit'],
                ['Gajiku Mobile', 'Aplikasi karyawan untuk absensi, slip gaji, dan pengajuan cuti di Android dan iOS.', 0, 'Aplikasi'],
            ]),
            'projects' => self::projects($p, [
                ['Digitalisasi Payroll 120 Cabang', 'Migrasi payroll manual ke Gajiku untuk 4.800 karyawan jaringan ritel di 120 cabang.', 'Toko Sentosa Group', 'Jakarta', 2023, 'Ritel', null],
                ['Absensi Tim Lapangan Distribusi', 'Implementasi absensi GPS untuk 900 sales dan pengemudi di 14 kota.', 'PT Distribusi Pangan Makmur', 'Surabaya, Jawa Timur', 2022, 'Distribusi', null],
                ['Benefit EWA untuk Pabrik Garmen', 'Peluncuran earned wage access yang menurunkan turnover operator produksi hingga 22%.', 'PT Busana Prima Tekstil', 'Semarang, Jawa Tengah', 2024, 'Manufaktur', null],
                ['Integrasi HRIS dan ERP', 'Integrasi dua arah antara Gajiku dan sistem ERP untuk konsolidasi biaya tenaga kerja.', 'Kopi Senja Nusantara', 'Bandung, Jawa Barat', 2024, 'F&B', null],
                ['Payroll Rumah Sakit Multi-Shift', 'Konfigurasi aturan shift dan tunjangan kompleks untuk tenaga medis di 6 rumah sakit.', 'RS Medika Sejahtera', 'Medan, Sumatera Utara', 2025, 'Kesehatan', null],
                ['Program Gajiku untuk UMKM', 'Program literasi HR dan akses gratis Gajiku untuk 1.000 UMKM binaan.', 'Kemitraan UMKM Digital', 'Nasional', 2021, 'Sosial', 'https://www.gajiku.id/umkm'],
            ]),
            'team' => self::team($p, $domain, [
                ['Kevin Halim', 'Co-Founder & CEO', 'Mantan konsultan strategi yang memimpin visi dan pertumbuhan bisnis Gajiku.', 'kevinhalim', 'kevin'],
                ['Nadia Putri Lestari', 'Co-Founder & CTO', 'Insinyur perangkat lunak yang membangun arsitektur platform Gajiku sejak hari pertama.', 'nadiaputrilestari', 'nadia'],
                ['Fajar Ramadhan', 'VP of Product', 'Memastikan setiap fitur Gajiku menjawab kebutuhan nyata tim HR dan karyawan.', 'fajarramadhan', 'fajar'],
                ['Clarissa Tanoto', 'Head of Customer Success', 'Memimpin tim yang mendampingi lebih dari 3.000 pelanggan dalam proses implementasi.', 'clarissatanoto', 'clarissa'],
            ]),
            'testimonials' => self::testimonials($p, [
                ['Dian Permatasari', 'Toko Sentosa Group', 'Proses payroll yang dulu memakan lima hari kini selesai dalam beberapa jam. Tim HR kami akhirnya punya waktu untuk program pengembangan karyawan.', 5],
                ['Agus Salim', 'PT Distribusi Pangan Makmur', 'Absensi GPS Gajiku menyelesaikan masalah kehadiran tim lapangan kami. Laporannya akurat dan langsung terhubung ke perhitungan gaji.', 5],
                ['Melati Sukma', 'Kopi Senja Nusantara', 'Implementasinya cepat dan tim customer success sangat membantu. Integrasi dengan ERP kami berjalan mulus tanpa kendala berarti.', 4],
            ]),
            'gallery' => self::gallery($p, [
                ['Kantor Gajiku BSD', 'Ruang kerja terbuka tim Gajiku di Green Office Park.', 'Kantor'],
                ['Hackathon Internal 2024', 'Tim engineering mengembangkan prototipe fitur baru dalam 48 jam.', 'Budaya'],
                ['Gajiku HR Summit', 'Forum tahunan bersama 500 praktisi HR Indonesia.', 'Acara'],
                ['Sesi Onboarding Pelanggan', 'Pelatihan penggunaan platform untuk tim HR pelanggan.', 'Pelanggan'],
                ['Town Hall Bulanan', 'Seluruh tim berbagi pencapaian dan rencana kuartal.', 'Budaya'],
                ['Aplikasi Gajiku Mobile', 'Tampilan aplikasi karyawan untuk absensi dan slip gaji.', 'Produk'],
            ]),
            'pages' => [
                self::careerPage($p, 'Gajiku', 'talent@'.$domain, ['Senior Backend Engineer (Go)', 'Product Designer', 'Account Executive B2B', 'Payroll Implementation Specialist']),
                self::privacyPage($p, $name, 'privacy@'.$domain),
                self::page($p, 'Keamanan Data', 'keamanan-data', [
                    'Data gaji dan informasi pribadi karyawan adalah data yang sangat sensitif. Karena itu, keamanan merupakan fondasi dari setiap baris kode yang kami tulis di Gajiku.',
                    'Seluruh data pelanggan disimpan di pusat data bersertifikat Tier III di Indonesia dan dienkripsi baik saat transit maupun saat tersimpan.',
                ], 'Standar Keamanan Kami', [
                    'Sertifikasi ISO/IEC 27001:2022 untuk sistem manajemen keamanan informasi',
                    'Enkripsi AES-256 dan TLS 1.3 untuk seluruh data',
                    'Autentikasi dua faktor dan single sign-on (SSO) untuk akun admin',
                    'Penetration test oleh pihak ketiga independen setiap tahun',
                ], 'Jika Anda menemukan celah keamanan, silakan laporkan melalui program responsible disclosure kami di security@gajiku.id.', 'Pelajari bagaimana Gajiku melindungi data payroll dan informasi pribadi karyawan Anda.'),
            ],
        ];
    }

    /**
     * Technology: software house.
     */
    private static function technology(): array
    {
        $p = 'tech';
        $name = 'PT Nusantara Digital Teknologi';
        $domain = 'nusantaradigital.co.id';

        return [
            'company' => [
                'name' => $name,
                'tagline' => 'Membangun perangkat lunak yang menggerakkan bisnis Indonesia',
                'description' => 'Nusantara Digital adalah software house yang mengembangkan aplikasi web, mobile, dan sistem enterprise untuk perusahaan dan instansi pemerintah. Kami menggabungkan rekayasa perangkat lunak yang solid dengan pemahaman bisnis yang mendalam.',
                'established_year' => 2014,
                'phone' => '(022) 8602 7788',
                'email' => 'hello@'.$domain,
                'whatsapp' => '081321456789',
                'address' => 'Jl. Ir. H. Juanda No. 128, Dago',
                'city' => 'Bandung',
                'province' => 'Jawa Barat',
                'country' => 'Indonesia',
                'postal_code' => '40132',
                'latitude' => -6.8915,
                'longitude' => 107.6107,
                'working_hours' => 'Senin - Jumat, 08.00 - 17.00',
                'website' => 'https://www.'.$domain,
                'social_links' => self::socials('nusantaradigital', ['instagram', 'linkedin', 'youtube', 'x', 'facebook']),
                'about' => self::paragraphs([
                    'Sejak 2014, Nusantara Digital telah menyelesaikan lebih dari 300 proyek perangkat lunak untuk perbankan, ritel, logistik, kesehatan, dan sektor publik. Kami percaya teknologi terbaik adalah teknologi yang benar-benar digunakan dan memberikan dampak bisnis.',
                    'Tim kami terdiri dari 120 engineer, desainer, dan konsultan produk yang bekerja dengan metodologi agile, praktik DevOps modern, serta standar keamanan informasi ISO 27001.',
                ]),
                'vision' => 'Menjadi mitra transformasi digital terpercaya yang melahirkan produk teknologi kelas dunia dari Indonesia.',
                'mission' => self::bullets([
                    'Menghadirkan solusi perangkat lunak yang andal, aman, dan mudah dikembangkan.',
                    'Mendampingi klien dari tahap ide, pengembangan, hingga operasional produk digital.',
                    'Mengembangkan talenta digital lokal melalui program akademi dan mentoring.',
                    'Menerapkan praktik rekayasa terbaik dan terus mengadopsi teknologi terkini.',
                ]),
                'history' => self::paragraphs([
                    'Nusantara Digital berawal dari empat alumni teknik informatika di Bandung yang mengerjakan proyek website untuk UMKM. Kepercayaan klien membawa kami menangani sistem informasi rumah sakit pertama pada 2016.',
                    'Pada 2020 kami membuka kantor di Jakarta dan Yogyakarta, serta meluncurkan Nusantara Digital Academy. Kini kami juga mengembangkan produk SaaS sendiri untuk sektor logistik dan kesehatan.',
                ]),
                'company_values' => self::values([
                    'Kualitas' => 'kode yang bersih, teruji, dan terdokumentasi dengan baik.',
                    'Kemitraan' => 'kami sukses ketika produk klien berhasil di pasar.',
                    'Rasa Ingin Tahu' => 'terus belajar dan bereksperimen dengan teknologi baru.',
                    'Integritas' => 'transparan dalam estimasi, progres, dan risiko proyek.',
                ]),
                'hero_image' => self::img("$p-hero", 1600, 900),
                'logo' => null,
                'seo_title' => 'Nusantara Digital - Software House & Jasa Pembuatan Aplikasi',
                'seo_description' => 'Software house di Bandung yang mengembangkan aplikasi web, mobile, cloud, dan sistem enterprise untuk perusahaan dan instansi di Indonesia.',
                'seo_keywords' => 'software house bandung, jasa pembuatan aplikasi, pengembangan web, aplikasi mobile, sistem enterprise',
            ],
            'services' => self::services($p, [
                ['Pengembangan Aplikasi Web', 'Aplikasi web berperforma tinggi dengan arsitektur modern, dari portal pelanggan hingga dashboard internal.', 'code'],
                ['Aplikasi Mobile', 'Aplikasi Android dan iOS native maupun cross-platform dengan pengalaman pengguna yang intuitif.', 'phone'],
                ['Cloud & DevOps', 'Migrasi ke cloud, otomatisasi CI/CD, dan pengelolaan infrastruktur yang skalabel dan efisien.', 'cpu'],
                ['Sistem Enterprise', 'ERP, sistem informasi rumah sakit, dan integrasi sistem warisan dengan layanan modern.', 'cube'],
                ['UI/UX Design', 'Riset pengguna, desain antarmuka, dan prototipe interaktif yang tervalidasi sebelum pengembangan.', 'paint'],
                ['Keamanan Siber', 'Audit keamanan aplikasi, penetration testing, dan penerapan standar keamanan informasi.', 'shield'],
            ]),
            'products' => self::products($p, [
                ['NDT Fleet', 'Platform SaaS manajemen armada dan pelacakan pengiriman secara real-time. Harga per kendaraan per bulan.', 75000, 'SaaS'],
                ['NDT Klinik', 'Sistem informasi klinik terintegrasi SATUSEHAT dan BPJS Kesehatan. Harga per bulan.', 1500000, 'SaaS'],
                ['NDT Kasir', 'Aplikasi point of sale untuk ritel dan F&B dengan laporan penjualan multi-cabang.', 250000, 'SaaS'],
                ['Paket Website Korporat', 'Website perusahaan profesional lengkap dengan CMS, SEO dasar, dan hosting satu tahun.', 15000000, 'Jasa'],
                ['Dedicated Development Team', 'Tim engineer khusus yang bekerja sebagai perpanjangan tim produk Anda.', null, 'Jasa'],
            ]),
            'projects' => self::projects($p, [
                ['Aplikasi Mobile Banking Syariah', 'Pengembangan aplikasi mobile banking dengan fitur pembukaan rekening online dan e-KYC.', 'Bank Syariah Amanah', 'Jakarta', 2023, 'Fintech', null],
                ['Sistem Informasi Rumah Sakit', 'SIMRS terintegrasi untuk 8 rumah sakit dengan modul rawat jalan, rawat inap, dan farmasi.', 'RS Harapan Bunda Group', 'Bandung, Jawa Barat', 2021, 'Kesehatan', null],
                ['Marketplace B2B Bahan Bangunan', 'Platform e-commerce B2B yang menghubungkan 1.500 toko bangunan dengan distributor.', 'BangunMart', 'Surabaya, Jawa Timur', 2022, 'E-commerce', 'https://bangunmart.co.id'],
                ['Portal Layanan Publik Terpadu', 'Portal perizinan online yang memangkas waktu layanan dari 14 hari menjadi 3 hari.', 'Pemerintah Kota Cimahi', 'Cimahi, Jawa Barat', 2020, 'GovTech', null],
                ['Migrasi Cloud Platform Logistik', 'Migrasi sistem on-premise ke cloud dengan penghematan biaya infrastruktur 40%.', 'PT Kirim Cepat Indonesia', 'Jakarta', 2024, 'Cloud', null],
                ['Aplikasi Loyalitas Ritel', 'Aplikasi member dan poin loyalitas untuk 2 juta pelanggan jaringan supermarket.', 'Segar Mart', 'Semarang, Jawa Tengah', 2025, 'Ritel', null],
            ]),
            'team' => self::team($p, $domain, [
                ['Raditya Pratama', 'Chief Executive Officer', 'Co-founder yang memimpin strategi bisnis dan kemitraan Nusantara Digital.', 'radityapratama', 'raditya'],
                ['Intan Maharani', 'Chief Technology Officer', 'Arsitek perangkat lunak dengan pengalaman 15 tahun di sistem berskala besar.', 'intanmaharani', 'intan'],
                ['Yosua Sihombing', 'Head of Engineering', 'Memimpin 90 engineer dan memastikan standar kualitas kode di setiap proyek.', 'yosuasihombing', 'yosua'],
                ['Aulia Rahman', 'Lead Product Designer', 'Desainer produk yang berfokus pada riset pengguna dan sistem desain.', 'auliarahman', 'aulia'],
            ]),
            'testimonials' => self::testimonials($p, [
                ['Ir. Herman Susanto', 'RS Harapan Bunda Group', 'SIMRS dari Nusantara Digital membuat pelayanan pasien kami jauh lebih cepat dan terintegrasi. Dukungan teknisnya sangat responsif selama 24 jam.', 5],
                ['Lia Kurniawati', 'Bank Syariah Amanah', 'Tim Nusantara Digital memahami regulasi perbankan dengan baik dan mampu menyelesaikan aplikasi tepat waktu. Kualitas kodenya lolos audit keamanan tanpa temuan kritis.', 5],
                ['Daniel Wibowo', 'BangunMart', 'Mereka bukan sekadar vendor, tetapi mitra produk yang aktif memberi masukan. Marketplace kami kini memproses ribuan transaksi setiap hari.', 4],
            ]),
            'gallery' => self::gallery($p, [
                ['Kantor Dago', 'Ruang kerja kolaboratif tim engineering di Bandung.', 'Kantor'],
                ['Sprint Planning', 'Sesi perencanaan sprint bersama tim produk klien.', 'Proses Kerja'],
                ['Nusantara Digital Academy', 'Bootcamp pemrograman untuk 60 talenta muda.', 'Komunitas'],
                ['Tech Talk Bulanan', 'Berbagi pengetahuan seputar arsitektur dan cloud.', 'Komunitas'],
                ['Design Workshop', 'Workshop riset pengguna dan prototyping.', 'Proses Kerja'],
                ['Peluncuran NDT Fleet', 'Acara peluncuran produk SaaS manajemen armada.', 'Acara'],
            ]),
            'pages' => [
                self::careerPage($p, 'Nusantara Digital', 'careers@'.$domain, ['Backend Engineer (Laravel)', 'Mobile Engineer (Flutter)', 'DevOps Engineer', 'UI/UX Designer']),
                self::privacyPage($p, $name, 'privacy@'.$domain),
                self::page($p, 'Berita & Insight', 'berita', [
                    'Ikuti kabar terbaru dari Nusantara Digital, mulai dari peluncuran produk, pencapaian proyek, hingga wawasan teknologi dari para engineer kami.',
                    'Kami rutin membagikan studi kasus dan praktik terbaik pengembangan perangkat lunak agar ekosistem teknologi Indonesia tumbuh bersama.',
                ], 'Kabar Terbaru', [
                    'Nusantara Digital meraih sertifikasi ISO/IEC 27001:2022',
                    'NDT Klinik resmi terintegrasi dengan platform SATUSEHAT',
                    'Nusantara Digital Academy meluluskan angkatan ke-8',
                    'Studi kasus: menghemat 40% biaya cloud melalui optimasi arsitektur',
                ], 'Ingin berkolaborasi atau meliput kegiatan kami? Hubungi tim komunikasi melalui media@nusantaradigital.co.id.', 'Berita terbaru, studi kasus, dan wawasan teknologi dari Nusantara Digital.'),
            ],
        ];
    }

    /**
     * Construction: general contractor.
     */
    private static function construction(): array
    {
        $p = 'cons';
        $name = 'PT Bangun Karya Persada';
        $domain = 'bangunkarya.co.id';

        return [
            'company' => [
                'name' => $name,
                'tagline' => 'Kontraktor umum terpercaya untuk gedung, infrastruktur, dan kawasan industri',
                'description' => 'Bangun Karya Persada adalah kontraktor umum berkualifikasi besar yang mengerjakan proyek gedung, infrastruktur, dan kawasan industri di seluruh Indonesia. Kami mengutamakan mutu, ketepatan waktu, dan keselamatan kerja di setiap proyek.',
                'established_year' => 2003,
                'phone' => '(031) 5678 9012',
                'email' => 'info@'.$domain,
                'whatsapp' => '081331234567',
                'address' => 'Jl. Raya Darmo No. 88',
                'city' => 'Surabaya',
                'province' => 'Jawa Timur',
                'country' => 'Indonesia',
                'postal_code' => '60265',
                'latitude' => -7.2876,
                'longitude' => 112.7390,
                'working_hours' => 'Senin - Jumat, 08.00 - 17.00',
                'website' => 'https://www.'.$domain,
                'social_links' => self::socials('bangunkaryapersada', ['facebook', 'instagram', 'linkedin', 'youtube']),
                'about' => self::paragraphs([
                    'Bangun Karya Persada telah menyelesaikan lebih dari 250 proyek konstruksi dengan nilai kumulatif di atas Rp 6 triliun, mulai dari gedung bertingkat, pabrik, jembatan, hingga jalan kawasan industri.',
                    'Didukung tenaga ahli bersertifikat, peralatan berat milik sendiri, serta sistem manajemen mutu ISO 9001, ISO 45001, dan ISO 14001, kami memastikan setiap proyek selesai sesuai spesifikasi, anggaran, dan jadwal.',
                ]),
                'vision' => 'Menjadi perusahaan konstruksi nasional terkemuka yang dikenal karena mutu, keselamatan, dan integritas.',
                'mission' => self::bullets([
                    'Menghasilkan bangunan dan infrastruktur berkualitas tinggi sesuai standar teknis.',
                    'Menerapkan budaya keselamatan kerja dengan target nihil kecelakaan.',
                    'Mengoptimalkan teknologi konstruksi modern termasuk BIM untuk efisiensi proyek.',
                    'Membangun kemitraan jangka panjang dengan klien, subkontraktor, dan pemasok.',
                ]),
                'history' => self::paragraphs([
                    'Didirikan oleh Ir. Soetrisno Hadi pada 2003, perusahaan memulai langkah dengan proyek renovasi gedung sekolah dan ruko di Surabaya. Dalam lima tahun, Bangun Karya Persada dipercaya membangun pabrik pertama di kawasan industri Rungkut.',
                    'Sejak 2015 kami memperluas operasi ke luar Jawa dengan proyek infrastruktur di Kalimantan dan Sulawesi. Pada 2022 perusahaan menerapkan Building Information Modeling (BIM) di seluruh proyek strategis.',
                ]),
                'company_values' => self::values([
                    'Keselamatan' => 'tidak ada pekerjaan yang terlalu penting untuk dikerjakan secara tidak aman.',
                    'Mutu' => 'setiap detail dikerjakan sesuai spesifikasi dan standar teknis.',
                    'Integritas' => 'jujur dan bertanggung jawab kepada klien dan pemangku kepentingan.',
                    'Ketepatan Waktu' => 'perencanaan matang untuk menyelesaikan proyek sesuai jadwal.',
                    'Kerja Sama Tim' => 'kolaborasi solid antara kantor pusat, lapangan, dan mitra.',
                ]),
                'hero_image' => self::img("$p-hero", 1600, 900),
                'logo' => null,
                'seo_title' => 'Bangun Karya Persada - Kontraktor Umum Gedung & Infrastruktur',
                'seo_description' => 'Kontraktor umum di Surabaya untuk proyek gedung, pabrik, infrastruktur, dan kawasan industri. Bersertifikat ISO 9001, ISO 45001, dan ISO 14001.',
                'seo_keywords' => 'kontraktor surabaya, kontraktor gedung, jasa konstruksi, kontraktor pabrik, design and build',
            ],
            'services' => self::services($p, [
                ['Konstruksi Gedung', 'Pembangunan gedung perkantoran, hotel, rumah sakit, dan fasilitas pendidikan bertingkat.', 'building'],
                ['Bangunan Industri', 'Konstruksi pabrik, gudang, dan fasilitas pendukung kawasan industri dengan struktur baja maupun beton.', 'cog'],
                ['Infrastruktur Sipil', 'Pekerjaan jalan, jembatan, drainase, dan pematangan lahan untuk kawasan dan pemerintah daerah.', 'truck'],
                ['Design & Build', 'Layanan terpadu mulai dari perencanaan, desain, perizinan, hingga serah terima bangunan.', 'clipboard'],
                ['Mekanikal & Elektrikal', 'Instalasi MEP, sistem kelistrikan, HVAC, dan proteksi kebakaran sesuai standar SNI.', 'bolt'],
                ['Renovasi & Perawatan', 'Renovasi, perkuatan struktur, dan perawatan berkala untuk menjaga nilai aset bangunan.', 'wrench'],
            ]),
            'products' => self::products($p, [
                ['Paket Konstruksi Gudang Baja', 'Gudang struktur baja bentang lebar siap pakai. Harga per m², belum termasuk pondasi khusus.', 2850000, 'Bangunan Industri'],
                ['Paket Ruko 3 Lantai', 'Pembangunan ruko standar dengan spesifikasi menengah. Harga per m².', 4200000, 'Bangunan Komersial'],
                ['Jasa Perencanaan & BIM', 'Perencanaan struktur, arsitektur, dan pemodelan BIM untuk koordinasi desain.', null, 'Konsultansi'],
                ['Sewa Alat Berat', 'Penyewaan excavator, crane, dan vibro roller beserta operator bersertifikat. Harga per hari.', 3500000, 'Peralatan'],
                ['Pemeliharaan Gedung Tahunan', 'Kontrak perawatan sipil dan MEP untuk gedung perkantoran dan pabrik.', null, 'Perawatan'],
            ]),
            'projects' => self::projects($p, [
                ['Gedung Kantor Graha Samudera', 'Gedung perkantoran 15 lantai dengan dua basement dan fasad kaca hemat energi.', 'PT Samudera Properti', 'Surabaya, Jawa Timur', 2022, 'Gedung', null],
                ['Pabrik Pengolahan Makanan', 'Konstruksi pabrik seluas 18.000 m² beserta utilitas dan instalasi pengolahan air limbah.', 'PT Pangan Lestari Jaya', 'Pasuruan, Jawa Timur', 2023, 'Industri', null],
                ['Jembatan Sungai Mahakam II', 'Jembatan rangka baja sepanjang 240 meter untuk akses kawasan industri.', 'Pemerintah Provinsi Kalimantan Timur', 'Samarinda, Kalimantan Timur', 2021, 'Infrastruktur', null],
                ['Rumah Sakit Ibu dan Anak', 'Pembangunan rumah sakit 6 lantai dengan kapasitas 150 tempat tidur.', 'Yayasan Kasih Bunda', 'Malang, Jawa Timur', 2024, 'Gedung', null],
                ['Jalan Kawasan Industri Gresik', 'Pekerjaan jalan beton sepanjang 7,5 km beserta saluran drainase.', 'Kawasan Industri Gresik Baru', 'Gresik, Jawa Timur', 2020, 'Infrastruktur', null],
                ['Hotel Resor Pantai Senggigi', 'Resor 120 kamar dengan struktur tahan gempa dan konsep arsitektur tropis.', 'PT Lombok Wisata Indah', 'Lombok Barat, NTB', 2025, 'Hospitality', null],
            ]),
            'team' => self::team($p, $domain, [
                ['Ir. Soetrisno Hadi, M.T.', 'Direktur Utama', 'Pendiri perusahaan dengan pengalaman lebih dari 30 tahun di industri konstruksi.', 'soetrisno-hadi', 'soetrisno'],
                ['Ir. Wahyu Nugroho', 'Direktur Teknik', 'Ahli struktur bersertifikat utama yang memimpin seluruh aspek teknis proyek.', 'wahyu-nugroho', 'wahyu'],
                ['Linda Kusumawati, S.E.', 'Direktur Keuangan', 'Mengelola keuangan dan pengendalian biaya proyek dengan disiplin tinggi.', 'linda-kusumawati', 'linda'],
                ['Arif Budiman, S.T.', 'Manajer HSE', 'Memastikan penerapan K3 dan lingkungan di seluruh lokasi proyek.', 'arif-budiman-hse', 'arif'],
            ]),
            'testimonials' => self::testimonials($p, [
                ['Hartono Gunawan', 'PT Samudera Properti', 'Gedung kami selesai dua minggu lebih cepat dari jadwal dengan kualitas pekerjaan yang sangat rapi. Pelaporan progres mingguannya transparan dan mudah dipahami.', 5],
                ['Dr. Ratih Puspita', 'Yayasan Kasih Bunda', 'Bangun Karya Persada memahami kebutuhan khusus bangunan rumah sakit. Koordinasi dengan tim medis kami selama pembangunan berjalan sangat baik.', 5],
                ['Bayu Saputra', 'PT Pangan Lestari Jaya', 'Standar keselamatan kerjanya patut diacungi jempol, tanpa satu pun kecelakaan kerja selama proyek. Kami akan kembali bekerja sama untuk ekspansi berikutnya.', 4],
            ]),
            'gallery' => self::gallery($p, [
                ['Pengecoran Pelat Lantai', 'Pengecoran lantai 12 proyek Graha Samudera.', 'Proyek'],
                ['Erection Rangka Baja', 'Pemasangan struktur baja pabrik di Pasuruan.', 'Proyek'],
                ['Safety Briefing Pagi', 'Toolbox meeting harian sebelum pekerjaan dimulai.', 'K3'],
                ['Armada Alat Berat', 'Excavator dan crane milik perusahaan.', 'Peralatan'],
                ['Koordinasi BIM', 'Rapat koordinasi desain menggunakan model 3D.', 'Teknologi'],
                ['Serah Terima Proyek', 'Serah terima gedung rumah sakit kepada klien.', 'Acara'],
            ]),
            'pages' => [
                self::careerPage($p, 'Bangun Karya Persada', 'hrd@'.$domain, ['Site Manager', 'Quantity Surveyor', 'Ahli K3 Konstruksi', 'Drafter BIM']),
                self::privacyPage($p, $name, 'legal@'.$domain),
                self::page($p, 'Keselamatan Kerja (K3)', 'keselamatan-kerja', [
                    'Keselamatan dan Kesehatan Kerja (K3) adalah nilai utama di Bangun Karya Persada. Kami meyakini bahwa setiap kecelakaan dapat dicegah melalui perencanaan, pelatihan, dan pengawasan yang konsisten.',
                    'Sistem Manajemen Keselamatan Konstruksi (SMKK) kami telah tersertifikasi dan diaudit secara berkala oleh lembaga independen.',
                ], 'Program K3 Kami', [
                    'Safety induction wajib bagi seluruh pekerja dan tamu proyek',
                    'Toolbox meeting harian dan safety patrol mingguan',
                    'Izin kerja (permit to work) untuk pekerjaan berisiko tinggi',
                    'Sertifikasi ISO 45001:2018 dan pencapaian 5 juta jam kerja tanpa kecelakaan',
                ], 'Kami mengundang seluruh mitra dan subkontraktor untuk bersama-sama menjaga budaya keselamatan di setiap lokasi proyek.', 'Komitmen Bangun Karya Persada terhadap Keselamatan dan Kesehatan Kerja di setiap proyek konstruksi.'),
            ],
        ];
    }

    /**
     * Manufacturing: plastic packaging factory.
     */
    private static function manufacturing(): array
    {
        $p = 'mfg';
        $name = 'PT Plastindo Kemas Utama';
        $domain = 'plastindokemas.co.id';

        return [
            'company' => [
                'name' => $name,
                'tagline' => 'Produsen kemasan plastik berkualitas untuk industri makanan, minuman, dan farmasi',
                'description' => 'Plastindo Kemas Utama memproduksi botol, preform, cup, dan kemasan fleksibel untuk ratusan merek nasional. Pabrik kami di Cikarang beroperasi 24 jam dengan kapasitas produksi lebih dari 60.000 ton per tahun.',
                'established_year' => 1995,
                'phone' => '(021) 8990 3456',
                'email' => 'sales@'.$domain,
                'whatsapp' => '081211223344',
                'address' => 'Kawasan Industri Jababeka II, Jl. Industri Selatan 5 Blok PP No. 7',
                'city' => 'Kabupaten Bekasi',
                'province' => 'Jawa Barat',
                'country' => 'Indonesia',
                'postal_code' => '17530',
                'latitude' => -6.3109,
                'longitude' => 107.1557,
                'working_hours' => 'Senin - Jumat, 08.00 - 17.00',
                'website' => 'https://www.'.$domain,
                'social_links' => self::socials('plastindokemas', ['facebook', 'instagram', 'linkedin', 'youtube']),
                'about' => self::paragraphs([
                    'Plastindo Kemas Utama adalah produsen kemasan plastik terintegrasi dengan teknologi injection, blow molding, thermoforming, dan ekstrusi film. Kami melayani industri air minum dalam kemasan, makanan, kosmetik, hingga farmasi.',
                    'Seluruh produk kami diproduksi di ruang bersih bersertifikat dan telah memenuhi standar FSSC 22000, ISO 9001, serta sertifikasi halal, sehingga aman untuk kontak langsung dengan pangan.',
                ]),
                'vision' => 'Menjadi produsen kemasan plastik terdepan di Asia Tenggara yang inovatif dan ramah lingkungan.',
                'mission' => self::bullets([
                    'Memproduksi kemasan berkualitas tinggi yang aman, konsisten, dan tepat waktu.',
                    'Mengembangkan desain kemasan inovatif bersama pelanggan.',
                    'Meningkatkan penggunaan material daur ulang dan efisiensi energi produksi.',
                    'Membangun lingkungan kerja yang aman dan sejahtera bagi seluruh karyawan.',
                ]),
                'history' => self::paragraphs([
                    'Berawal dari pabrik kecil dengan tiga mesin injeksi di Bekasi pada 1995, Plastindo tumbuh seiring berkembangnya industri air minum dalam kemasan di Indonesia.',
                    'Pada 2012 kami pindah ke fasilitas seluas 8 hektar di Jababeka dan menambah lini kemasan fleksibel. Sejak 2020 Plastindo mengoperasikan lini daur ulang rPET food grade pertama milik perusahaan.',
                ]),
                'company_values' => self::values([
                    'Mutu Tanpa Kompromi' => 'setiap batch diuji sebelum dikirim ke pelanggan.',
                    'Keamanan Pangan' => 'higienitas dan keamanan produk menjadi prioritas utama.',
                    'Efisiensi' => 'produksi ramping untuk harga yang kompetitif.',
                    'Keberlanjutan' => 'mengurangi jejak karbon dan sampah plastik.',
                ]),
                'hero_image' => self::img("$p-hero", 1600, 900),
                'logo' => null,
                'seo_title' => 'Plastindo Kemas Utama - Produsen Kemasan Plastik & Botol PET',
                'seo_description' => 'Pabrik kemasan plastik di Cikarang: botol PET, preform, cup, tutup, dan kemasan fleksibel food grade bersertifikat FSSC 22000 dan halal.',
                'seo_keywords' => 'pabrik kemasan plastik, botol pet, preform, cup plastik, kemasan food grade',
            ],
            'services' => self::services($p, [
                ['Injection Molding', 'Produksi preform, tutup botol, dan komponen plastik presisi dengan mesin injeksi modern.', 'cog'],
                ['Blow Molding', 'Pembuatan botol PET dan HDPE berbagai ukuran untuk minuman, kosmetik, dan farmasi.', 'cube'],
                ['Thermoforming', 'Produksi cup, tray, dan clamshell food grade untuk industri makanan dan minuman.', 'sparkles'],
                ['Desain & Pengembangan Mold', 'Perancangan bentuk kemasan dan pembuatan cetakan sesuai identitas merek pelanggan.', 'light-bulb'],
                ['Quality Control Laboratorium', 'Pengujian dimensi, kekuatan, migrasi, dan kebocoran di laboratorium terakreditasi.', 'clipboard'],
                ['Daur Ulang rPET', 'Pengolahan botol bekas menjadi resin rPET food grade untuk kemasan berkelanjutan.', 'leaf'],
            ]),
            'products' => self::products($p, [
                ['Botol PET 600 ml', 'Botol air minum ringan dengan neck 28 mm, tersedia bening dan biru muda. Harga per 1.000 pcs.', 950000, 'Botol'],
                ['Preform PET 18,5 gram', 'Preform untuk botol 330-600 ml dengan bobot konsisten. Harga per 1.000 pcs.', 720000, 'Preform'],
                ['Cup PP 16 oz', 'Gelas plastik untuk minuman dingin dengan sealing lid. Harga per 1.000 pcs.', 410000, 'Cup & Tray'],
                ['Tutup Botol 28 mm', 'Tutup ulir dengan tamper evident ring, tersedia berbagai warna. Harga per 1.000 pcs.', 180000, 'Tutup'],
                ['Jerigen HDPE 5 Liter', 'Jerigen kuat untuk minyak goreng dan bahan kimia rumah tangga.', null, 'Botol'],
                ['Kemasan Fleksibel Standing Pouch', 'Standing pouch multilayer dengan zipper dan cetak hingga 8 warna.', null, 'Kemasan Fleksibel'],
            ]),
            'projects' => self::projects($p, [
                ['Kemasan Botol Merek AMDK Nasional', 'Pasokan 40 juta botol per bulan untuk merek air minum dalam kemasan nasional.', 'PT Tirta Alam Segar', 'Sukabumi, Jawa Barat', 2022, 'Minuman', null],
                ['Desain Ulang Botol Ringan', 'Pengurangan bobot botol 12% tanpa mengurangi kekuatan, menghemat 1.200 ton resin per tahun.', 'PT Tirta Alam Segar', 'Cikarang, Jawa Barat', 2023, 'R&D', null],
                ['Kemasan Botol Farmasi Sirup', 'Botol PET amber 60 ml dengan tutup child-resistant untuk produk sirup obat.', 'PT Medifarma Sehat', 'Jakarta Timur', 2021, 'Farmasi', null],
                ['Cup Minuman Jaringan Kopi', 'Produksi cup dan lid kustom untuk 600 gerai kopi di seluruh Indonesia.', 'Kopi Kenangan Senja', 'Jakarta', 2024, 'F&B', null],
                ['Lini Daur Ulang rPET', 'Commissioning lini daur ulang botol berkapasitas 1.000 ton per bulan.', 'Internal Plastindo', 'Cikarang, Jawa Barat', 2020, 'Keberlanjutan', null],
                ['Kemasan Kosmetik Premium', 'Botol PETG dan jar kosmetik dengan finishing doff untuk merek kecantikan lokal.', 'Ayu Beauty Indonesia', 'Bandung, Jawa Barat', 2025, 'Kosmetik', 'https://www.plastindokemas.co.id/studi-kasus'],
            ]),
            'team' => self::team($p, $domain, [
                ['Teddy Gunawan', 'Presiden Direktur', 'Generasi kedua pendiri yang memimpin modernisasi pabrik dan ekspansi ekspor.', 'teddy-gunawan', 'teddy'],
                ['Ir. Sri Wahyuni', 'Direktur Produksi', 'Ahli teknik polimer dengan pengalaman 25 tahun di industri kemasan.', 'sri-wahyuni-polimer', 'sri.wahyuni'],
                ['Michael Santoso', 'Direktur Penjualan', 'Membangun kemitraan dengan lebih dari 300 merek nasional dan multinasional.', 'michael-santoso', 'michael'],
                ['Dewi Anjani, M.Si.', 'Manajer Quality Assurance', 'Memastikan seluruh produk memenuhi standar keamanan pangan dan farmasi.', 'dewi-anjani', 'dewi.anjani'],
            ]),
            'testimonials' => self::testimonials($p, [
                ['Yudi Hermawan', 'PT Tirta Alam Segar', 'Kapasitas dan konsistensi kualitas Plastindo membuat lini produksi kami tidak pernah berhenti karena kekurangan botol. Tim R&D mereka juga membantu kami menurunkan biaya kemasan.', 5],
                ['apt. Rina Marlina', 'PT Medifarma Sehat', 'Dokumentasi mutu dan sertifikat analisis selalu lengkap untuk setiap pengiriman. Sangat membantu proses audit BPOM kami.', 5],
                ['Stefani Lim', 'Ayu Beauty Indonesia', 'Kemasan kosmetik yang dihasilkan sangat premium dan sesuai desain. Proses dari sampel hingga produksi massal berjalan cepat.', 4],
            ]),
            'gallery' => self::gallery($p, [
                ['Lini Blow Molding', 'Mesin blow molding otomatis berkapasitas 20.000 botol per jam.', 'Produksi'],
                ['Ruang Injeksi Preform', 'Area injeksi preform dengan sistem pendingin terpusat.', 'Produksi'],
                ['Laboratorium QC', 'Pengujian kekuatan tekan dan kebocoran botol.', 'Quality Control'],
                ['Gudang Barang Jadi', 'Gudang otomatis dengan kapasitas 15.000 palet.', 'Fasilitas'],
                ['Lini Daur Ulang rPET', 'Pengolahan botol bekas menjadi flake dan resin food grade.', 'Keberlanjutan'],
                ['Pelatihan Operator', 'Program peningkatan kompetensi operator mesin.', 'SDM'],
            ]),
            'pages' => [
                self::careerPage($p, 'Plastindo Kemas Utama', 'rekrutmen@'.$domain, ['Supervisor Produksi', 'Mold Technician', 'QC Analyst', 'Sales Engineer']),
                self::privacyPage($p, $name, 'legal@'.$domain),
                self::page($p, 'Sertifikasi & Standar Mutu', 'sertifikasi', [
                    'Sebagai produsen kemasan yang bersentuhan langsung dengan pangan dan obat, Plastindo menerapkan sistem manajemen mutu dan keamanan pangan yang ketat di seluruh rantai produksi.',
                    'Sertifikasi kami diaudit ulang setiap tahun oleh lembaga sertifikasi internasional dan dapat diverifikasi oleh pelanggan kapan saja.',
                ], 'Sertifikasi yang Kami Miliki', [
                    'FSSC 22000 versi 6 untuk keamanan kemasan pangan',
                    'ISO 9001:2015 Sistem Manajemen Mutu',
                    'ISO 14001:2015 Sistem Manajemen Lingkungan',
                    'Sertifikat Halal BPJPH dan SNI untuk produk kemasan',
                ], 'Salinan sertifikat dan hasil uji migrasi dapat diminta melalui tim penjualan kami.', 'Daftar sertifikasi mutu dan keamanan pangan PT Plastindo Kemas Utama.'),
            ],
        ];
    }

    /**
     * Consulting: management consulting firm.
     */
    private static function consulting(): array
    {
        $p = 'consult';
        $name = 'PT Cakrawala Strategi Konsultan';
        $domain = 'cakrawalastrategi.com';

        return [
            'company' => [
                'name' => $name,
                'tagline' => 'Strategi yang tajam, eksekusi yang terukur',
                'description' => 'Cakrawala Strategi adalah firma konsultan manajemen yang membantu perusahaan merumuskan strategi, meningkatkan kinerja operasional, dan memimpin transformasi organisasi. Kami bekerja berdampingan dengan manajemen hingga hasilnya benar-benar terwujud.',
                'established_year' => 2010,
                'phone' => '(021) 3192 4400',
                'email' => 'contact@'.$domain,
                'whatsapp' => '081388776655',
                'address' => 'Menara Cakrawala Lt. 21, Jl. M.H. Thamrin No. 9',
                'city' => 'Jakarta Pusat',
                'province' => 'DKI Jakarta',
                'country' => 'Indonesia',
                'postal_code' => '10350',
                'latitude' => -6.1862,
                'longitude' => 106.8227,
                'working_hours' => 'Senin - Jumat, 08.00 - 17.00',
                'website' => 'https://www.'.$domain,
                'social_links' => self::socials('cakrawalastrategi', ['linkedin', 'instagram', 'youtube', 'x']),
                'about' => self::paragraphs([
                    'Cakrawala Strategi didirikan oleh para konsultan senior yang sebelumnya berkarier di firma konsultan global. Kami menggabungkan metodologi kelas dunia dengan pemahaman mendalam tentang konteks bisnis dan regulasi Indonesia.',
                    'Selama lebih dari satu dekade, kami telah mendampingi lebih dari 180 klien dari BUMN, grup usaha keluarga, hingga perusahaan multinasional dalam menghadapi tantangan paling strategis mereka.',
                ]),
                'vision' => 'Menjadi mitra strategis pilihan utama bagi para pemimpin bisnis di Indonesia.',
                'mission' => self::bullets([
                    'Memberikan rekomendasi strategis berbasis data dan wawasan industri yang mendalam.',
                    'Mendampingi implementasi hingga menghasilkan dampak finansial yang terukur.',
                    'Membangun kapabilitas internal klien agar perubahan dapat berkelanjutan.',
                    'Menjunjung tinggi independensi, objektivitas, dan kerahasiaan klien.',
                ]),
                'history' => self::paragraphs([
                    'Cakrawala Strategi berdiri pada 2010 dengan tim berjumlah tujuh orang dan klien pertama sebuah bank daerah yang tengah melakukan restrukturisasi.',
                    'Kini tim kami terdiri dari 85 konsultan dengan praktik khusus di sektor keuangan, energi, consumer goods, dan sektor publik, serta kantor perwakilan di Surabaya dan Singapura.',
                ]),
                'company_values' => self::values([
                    'Dampak Nyata' => 'keberhasilan diukur dari hasil yang dicapai klien.',
                    'Objektivitas' => 'menyampaikan kebenaran meskipun tidak selalu nyaman didengar.',
                    'Kerahasiaan' => 'menjaga informasi klien dengan standar etika tertinggi.',
                    'Kolaborasi' => 'bekerja sebagai satu tim bersama manajemen klien.',
                ]),
                'hero_image' => self::img("$p-hero", 1600, 900),
                'logo' => null,
                'seo_title' => 'Cakrawala Strategi - Konsultan Manajemen & Strategi Bisnis',
                'seo_description' => 'Firma konsultan manajemen di Jakarta untuk strategi korporat, transformasi organisasi, peningkatan kinerja operasional, dan transformasi digital.',
                'seo_keywords' => 'konsultan manajemen, konsultan strategi, transformasi organisasi, konsultan bisnis jakarta',
            ],
            'services' => self::services($p, [
                ['Strategi Korporat', 'Perumusan strategi pertumbuhan, portofolio bisnis, dan rencana jangka panjang perusahaan.', 'light-bulb'],
                ['Transformasi Organisasi', 'Desain struktur organisasi, tata kelola, dan manajemen perubahan yang efektif.', 'users'],
                ['Kinerja Operasional', 'Peningkatan produktivitas dan efisiensi biaya melalui lean management dan perbaikan proses.', 'cog'],
                ['Transformasi Digital', 'Peta jalan digital, analitik data, dan otomasi proses untuk keunggulan kompetitif.', 'cpu'],
                ['Merger & Akuisisi', 'Due diligence komersial, valuasi, dan integrasi pascamerger.', 'briefcase'],
                ['Pengembangan Kepemimpinan', 'Program pengembangan pemimpin dan asesmen talenta tingkat eksekutif.', 'academic'],
            ]),
            'products' => self::products($p, [
                ['Strategic Diagnostic', 'Asesmen cepat 4 minggu atas posisi strategis dan peluang pertumbuhan perusahaan.', 185000000, 'Asesmen'],
                ['Operational Excellence Program', 'Program 6 bulan untuk menurunkan biaya operasional dan meningkatkan produktivitas.', null, 'Program'],
                ['Leadership Accelerator', 'Program kepemimpinan 12 sesi untuk manajer madya dan senior. Harga per peserta.', 18500000, 'Pelatihan'],
                ['Digital Maturity Assessment', 'Pengukuran kematangan digital dan penyusunan peta jalan prioritas.', 95000000, 'Asesmen'],
                ['Cakrawala Industry Report', 'Laporan riset tahunan tren industri dan outlook ekonomi Indonesia.', 2500000, 'Riset'],
            ]),
            'projects' => self::projects($p, [
                ['Transformasi Bank Pembangunan Daerah', 'Restrukturisasi model bisnis yang meningkatkan laba bersih 35% dalam dua tahun.', 'Bank Daerah Nusantara', 'Surabaya, Jawa Timur', 2021, 'Jasa Keuangan', null],
                ['Strategi Ekspansi Regional FMCG', 'Penyusunan strategi masuk pasar Vietnam dan Filipina untuk produsen makanan ringan.', 'PT Rasa Nusantara Food', 'Jakarta', 2023, 'Consumer Goods', null],
                ['Efisiensi Operasional Tambang', 'Program lean yang menghemat biaya operasional Rp 210 miliar per tahun.', 'PT Batu Bara Kencana', 'Balikpapan, Kalimantan Timur', 2022, 'Energi & Tambang', null],
                ['Reformasi Birokrasi Kementerian', 'Redesain proses layanan dan struktur organisasi unit pelayanan publik.', 'Kementerian (Rahasia)', 'Jakarta', 2020, 'Sektor Publik', null],
                ['Integrasi Pascamerger Asuransi', 'Integrasi dua perusahaan asuransi jiwa termasuk budaya, sistem, dan produk.', 'Asuransi Jiwa Sentosa', 'Jakarta', 2024, 'Merger & Akuisisi', null],
                ['Peta Jalan Digital Rumah Sakit', 'Penyusunan peta jalan transformasi digital untuk jaringan 12 rumah sakit.', 'Sehat Sentosa Hospital Group', 'Medan, Sumatera Utara', 2025, 'Kesehatan', null],
            ]),
            'team' => self::team($p, $domain, [
                ['Dr. Adrian Siregar', 'Managing Partner', 'Mantan partner firma konsultan global dengan spesialisasi strategi korporat.', 'adrian-siregar', 'adrian.siregar'],
                ['Kartika Sari Dewi', 'Partner, Financial Services', 'Mendampingi transformasi lebih dari 20 bank dan perusahaan asuransi.', 'kartika-sari-dewi', 'kartika.dewi'],
                ['Bima Aditya', 'Partner, Operations', 'Ahli lean management dan rantai pasok di sektor manufaktur dan energi.', 'bima-aditya', 'bima.aditya'],
                ['Putri Handayani', 'Principal, Digital & Analytics', 'Memimpin praktik transformasi digital dan analitik data Cakrawala.', 'putri-handayani', 'putri.handayani'],
            ]),
            'testimonials' => self::testimonials($p, [
                ['Ir. Darmawan Hidayat', 'Bank Daerah Nusantara', 'Cakrawala tidak hanya memberikan rekomendasi, tetapi juga mendampingi tim kami hingga perubahan benar-benar berjalan. Hasilnya terlihat jelas pada kinerja keuangan kami.', 5],
                ['Susanti Wijaya', 'PT Rasa Nusantara Food', 'Analisis pasarnya sangat tajam dan praktis untuk dieksekusi. Strategi ekspansi yang disusun menjadi pegangan direksi kami hingga hari ini.', 5],
                ['Robert Simanjuntak', 'PT Batu Bara Kencana', 'Program efisiensi yang dijalankan bersama Cakrawala memberikan penghematan melebihi target. Tim konsultannya sangat memahami kondisi lapangan.', 4],
            ]),
            'gallery' => self::gallery($p, [
                ['Workshop Strategi Direksi', 'Sesi perumusan strategi bersama jajaran direksi klien.', 'Engagement'],
                ['Kantor Thamrin', 'Ruang kerja kolaboratif di Menara Cakrawala.', 'Kantor'],
                ['Cakrawala CEO Forum', 'Diskusi tahunan bersama 100 CEO Indonesia.', 'Acara'],
                ['Leadership Accelerator', 'Sesi pelatihan kepemimpinan untuk manajer senior.', 'Pelatihan'],
                ['Kunjungan Lapangan', 'Observasi proses operasional di lokasi klien.', 'Engagement'],
                ['Peluncuran Industry Report', 'Peluncuran laporan riset outlook ekonomi 2025.', 'Riset'],
            ]),
            'pages' => [
                self::careerPage($p, 'Cakrawala Strategi', 'recruiting@'.$domain, ['Business Analyst', 'Associate Consultant', 'Senior Consultant, Digital', 'Research Analyst']),
                self::privacyPage($p, $name, 'privacy@'.$domain),
                self::page($p, 'Insight & Publikasi', 'insight', [
                    'Tim riset Cakrawala Strategi secara rutin menerbitkan analisis tentang tren ekonomi, industri, dan praktik manajemen terbaik yang relevan bagi para pemimpin bisnis di Indonesia.',
                    'Publikasi kami disusun berdasarkan data primer, wawancara dengan eksekutif, dan pengalaman langsung dari ratusan proyek konsultansi.',
                ], 'Publikasi Terbaru', [
                    'Outlook Ekonomi & Industri Indonesia 2025',
                    'Lima Prioritas Transformasi Digital untuk Bank Daerah',
                    'Membangun Organisasi yang Gesit di Perusahaan Keluarga',
                    'Strategi Dekarbonisasi untuk Sektor Energi dan Tambang',
                ], 'Berlangganan newsletter kami untuk mendapatkan insight terbaru langsung di kotak masuk Anda.', 'Riset, artikel, dan laporan industri dari tim Cakrawala Strategi.'),
            ],
        ];
    }

    /**
     * Creative agency: branding & design studio.
     */
    private static function creativeAgency(): array
    {
        $p = 'agency';
        $name = 'Rupa Kreatif Studio';
        $domain = 'rupakreatif.id';

        return [
            'company' => [
                'name' => $name,
                'tagline' => 'Kami membangun merek yang diingat dan dicintai',
                'description' => 'Rupa Kreatif adalah studio branding dan desain yang membantu merek Indonesia menemukan suara, rupa, dan cerita terbaiknya. Dari identitas visual hingga kampanye digital, kami merancang pengalaman merek yang konsisten di setiap titik sentuh.',
                'established_year' => 2016,
                'phone' => '(0274) 556 789',
                'email' => 'halo@'.$domain,
                'whatsapp' => '081227654321',
                'address' => 'Jl. Prawirotaman II No. 21, Mergangsan',
                'city' => 'Yogyakarta',
                'province' => 'DI Yogyakarta',
                'country' => 'Indonesia',
                'postal_code' => '55153',
                'latitude' => -7.8186,
                'longitude' => 110.3677,
                'working_hours' => 'Senin - Jumat, 08.00 - 17.00',
                'website' => 'https://www.'.$domain,
                'social_links' => self::socials('rupakreatif', ['instagram', 'tiktok', 'linkedin', 'youtube', 'facebook']),
                'about' => self::paragraphs([
                    'Kami adalah tim desainer, penulis, strategist, dan animator yang percaya bahwa merek yang kuat lahir dari pemahaman mendalam tentang manusia. Setiap proyek kami mulai dengan mendengar, meneliti, lalu menerjemahkannya menjadi ide visual yang berani.',
                    'Dari studio kami di Yogyakarta, Rupa Kreatif telah bekerja sama dengan lebih dari 150 merek, mulai dari startup, UMKM ekspor, hingga perusahaan publik dan lembaga budaya.',
                ]),
                'vision' => 'Menjadi studio kreatif yang membawa merek-merek Indonesia sejajar dengan merek terbaik dunia.',
                'mission' => self::bullets([
                    'Merancang identitas merek yang strategis, otentik, dan mudah dikenali.',
                    'Menggabungkan riset, kreativitas, dan teknologi dalam setiap karya.',
                    'Mengangkat kekayaan budaya Indonesia ke dalam bahasa desain kontemporer.',
                    'Menumbuhkan ekosistem kreatif melalui kolaborasi dan berbagi pengetahuan.',
                ]),
                'history' => self::paragraphs([
                    'Rupa Kreatif dimulai pada 2016 sebagai kolektif tiga desainer grafis yang mengerjakan identitas kedai kopi dan toko kerajinan di Yogyakarta.',
                    'Karya rebranding batik tulis Giriloyo pada 2018 membawa kami meraih penghargaan desain nasional pertama. Kini Rupa Kreatif memiliki 35 kreator dan melayani klien di Indonesia, Singapura, dan Australia.',
                ]),
                'company_values' => self::values([
                    'Berani' => 'tidak takut mencoba ide yang belum pernah ada.',
                    'Empati' => 'memahami audiens sebelum mulai mendesain.',
                    'Detail' => 'kualitas lahir dari ketelitian pada hal-hal kecil.',
                    'Bercerita' => 'setiap desain harus memiliki cerita yang bermakna.',
                    'Bersenang-senang' => 'karya terbaik lahir dari tim yang menikmati prosesnya.',
                ]),
                'hero_image' => self::img("$p-hero", 1600, 900),
                'logo' => null,
                'seo_title' => 'Rupa Kreatif Studio - Branding, Desain & Kampanye Kreatif',
                'seo_description' => 'Studio branding di Yogyakarta untuk identitas visual, strategi merek, desain kemasan, website, dan kampanye digital.',
                'seo_keywords' => 'studio branding, jasa desain logo, identitas visual, desain kemasan, creative agency yogyakarta',
            ],
            'services' => self::services($p, [
                ['Strategi Merek', 'Riset audiens, positioning, arsitektur merek, dan penyusunan brand story yang kuat.', 'light-bulb'],
                ['Identitas Visual', 'Logo, sistem warna, tipografi, dan pedoman merek yang konsisten di semua media.', 'paint'],
                ['Desain Kemasan', 'Kemasan yang menonjol di rak, fungsional, dan mencerminkan karakter produk.', 'cube'],
                ['Kampanye Digital', 'Konsep kreatif, konten media sosial, dan kampanye berbayar yang terukur.', 'megaphone'],
                ['Fotografi & Video', 'Produksi foto produk, video merek, dan konten pendek untuk berbagai platform.', 'camera'],
                ['Website & Digital Experience', 'Desain dan pengembangan website yang indah, cepat, dan mudah dikelola.', 'globe'],
            ]),
            'products' => self::products($p, [
                ['Paket Brand Starter', 'Logo, palet warna, tipografi, dan mini brand guideline untuk usaha baru.', 12500000, 'Paket Branding'],
                ['Paket Brand Identity Lengkap', 'Strategi merek, identitas visual lengkap, dan brand guideline 60 halaman.', 45000000, 'Paket Branding'],
                ['Desain Kemasan per SKU', 'Desain kemasan siap cetak termasuk mockup dan pendampingan percetakan.', 7500000, 'Kemasan'],
                ['Retainer Konten Sosial Media', 'Perencanaan dan produksi 20 konten per bulan untuk Instagram dan TikTok.', 15000000, 'Retainer'],
                ['Brand Workshop', 'Workshop setengah hari untuk menemukan nilai dan kepribadian merek bersama tim Anda.', null, 'Workshop'],
            ]),
            'projects' => self::projects($p, [
                ['Rebranding Batik Giriloyo', 'Identitas baru yang menghadirkan batik tulis tradisional untuk pasar urban dan ekspor.', 'Koperasi Batik Giriloyo', 'Bantul, DI Yogyakarta', 2019, 'Branding', null],
                ['Kemasan Kopi Lereng Merapi', 'Seri kemasan kopi spesialti dengan ilustrasi lanskap lereng Merapi.', 'Lereng Merapi Coffee', 'Sleman, DI Yogyakarta', 2022, 'Kemasan', null],
                ['Kampanye #JalanJalanLokal', 'Kampanye digital pariwisata yang meraih 18 juta tayangan dalam sebulan.', 'Dinas Pariwisata DIY', 'Yogyakarta', 2023, 'Kampanye', null],
                ['Identitas Startup Edutech', 'Sistem identitas dan ilustrasi untuk platform belajar online anak.', 'Pintar Ceria', 'Jakarta', 2024, 'Branding', 'https://pintarceria.id'],
                ['Website Festival Seni', 'Website interaktif dan identitas visual festival seni kontemporer tahunan.', 'Festival Seni Nusa', 'Yogyakarta', 2021, 'Digital', null],
                ['Rebranding Jaringan Hotel Butik', 'Identitas merek, signage, dan seragam untuk 9 hotel butik di Bali dan Lombok.', 'Svarga Boutique Hotels', 'Denpasar, Bali', 2025, 'Hospitality', null],
            ]),
            'team' => self::team($p, $domain, [
                ['Ayu Larasati', 'Founder & Creative Director', 'Desainer peraih penghargaan yang memimpin arah kreatif seluruh proyek studio.', 'ayularasati', 'ayu'],
                ['Gilang Saputra', 'Brand Strategist', 'Menerjemahkan riset dan wawasan pasar menjadi strategi merek yang tajam.', 'gilangsaputra', 'gilang'],
                ['Nindya Paramitha', 'Lead Designer', 'Spesialis identitas visual dan tipografi dengan sentuhan budaya lokal.', 'nindyaparamitha', 'nindya'],
                ['Reza Mahendra', 'Head of Motion & Video', 'Sutradara dan animator di balik kampanye video Rupa Kreatif.', 'rezamahendra', 'reza'],
            ]),
            'testimonials' => self::testimonials($p, [
                ['Sri Lestari', 'Koperasi Batik Giriloyo', 'Rupa Kreatif berhasil membuat batik kami terasa relevan bagi generasi muda tanpa kehilangan akar tradisinya. Penjualan online kami naik tiga kali lipat setelah rebranding.', 5],
                ['Andreas Kusuma', 'Lereng Merapi Coffee', 'Kemasan baru kami sering difoto dan dibagikan pelanggan di media sosial. Prosesnya menyenangkan dan timnya sangat mendengarkan masukan kami.', 5],
                ['Made Wirawan', 'Svarga Boutique Hotels', 'Identitas merek yang dihasilkan terasa mewah sekaligus hangat. Detail eksekusinya, dari signage hingga kartu kamar, sangat konsisten.', 5],
            ]),
            'gallery' => self::gallery($p, [
                ['Studio Prawirotaman', 'Ruang kerja kreatif kami di jantung Yogyakarta.', 'Studio'],
                ['Sesi Moodboard', 'Eksplorasi arah visual bersama klien.', 'Proses'],
                ['Photoshoot Produk', 'Produksi foto kemasan kopi spesialti.', 'Produksi'],
                ['Brand Workshop', 'Workshop penemuan nilai merek bersama tim klien.', 'Proses'],
                ['Pameran Desain', 'Karya Rupa Kreatif dalam pameran desain nasional.', 'Acara'],
                ['Signage Hotel Butik', 'Penerapan identitas visual pada signage hotel.', 'Karya'],
            ]),
            'pages' => [
                self::careerPage($p, 'Rupa Kreatif', 'join@'.$domain, ['Graphic Designer', 'Copywriter', 'Motion Designer', 'Social Media Strategist']),
                self::privacyPage($p, 'PT Rupa Warna Kreasi (Rupa Kreatif Studio)', 'halo@'.$domain),
                self::page($p, 'Proses Kreatif Kami', 'proses-kreatif', [
                    'Karya yang hebat tidak lahir dari kebetulan. Di Rupa Kreatif, setiap proyek melewati proses yang terstruktur namun tetap memberi ruang bagi ide-ide yang tidak terduga.',
                    'Kami melibatkan klien di setiap tahap sehingga hasil akhir terasa sebagai karya bersama, bukan sekadar pesanan desain.',
                ], 'Empat Tahap Proses', [
                    'Discover - riset audiens, kompetitor, dan wawancara pemangku kepentingan',
                    'Define - merumuskan positioning, brand story, dan arah kreatif',
                    'Design - eksplorasi konsep, iterasi, dan penyempurnaan visual',
                    'Deliver - penerapan ke seluruh media dan penyusunan brand guideline',
                ], 'Ingin memulai proyek bersama kami? Ceritakan merek Anda melalui halo@rupakreatif.id.', 'Kenali proses kreatif Rupa Kreatif Studio dari riset hingga peluncuran merek.'),
            ],
        ];
    }

    /**
     * Professional services: law firm.
     */
    private static function professionalServices(): array
    {
        $p = 'law';
        $name = 'Wiratama & Rekan Law Firm';
        $domain = 'wiratamalaw.com';

        return [
            'company' => [
                'name' => $name,
                'tagline' => 'Nasihat hukum yang jernih untuk keputusan bisnis yang tepat',
                'description' => 'Wiratama & Rekan adalah kantor hukum full-service yang menangani hukum korporasi, litigasi, ketenagakerjaan, dan penanaman modal. Kami memberikan nasihat hukum yang praktis dan berorientasi solusi bagi klien korporasi maupun perorangan.',
                'established_year' => 2005,
                'phone' => '(021) 5290 8877',
                'email' => 'office@'.$domain,
                'whatsapp' => '081298765432',
                'address' => 'Gedung Menara Kuningan Lt. 12, Jl. H.R. Rasuna Said Blok X-7 Kav. 5',
                'city' => 'Jakarta Selatan',
                'province' => 'DKI Jakarta',
                'country' => 'Indonesia',
                'postal_code' => '12940',
                'latitude' => -6.2297,
                'longitude' => 106.8306,
                'working_hours' => 'Senin - Jumat, 08.00 - 17.00',
                'website' => 'https://www.'.$domain,
                'social_links' => self::socials('wiratamalaw', ['linkedin', 'instagram', 'facebook', 'x']),
                'about' => self::paragraphs([
                    'Wiratama & Rekan didirikan dengan keyakinan bahwa nasihat hukum terbaik adalah nasihat yang mudah dipahami dan dapat langsung ditindaklanjuti. Kami memadukan ketelitian analisis hukum dengan pemahaman bisnis yang kuat.',
                    'Didukung 40 advokat dan konsultan hukum, kami mewakili perusahaan nasional, investor asing, BUMN, dan individu dalam transaksi bisnis maupun penyelesaian sengketa di seluruh Indonesia.',
                ]),
                'vision' => 'Menjadi kantor hukum Indonesia yang paling dipercaya karena integritas, keahlian, dan dedikasi kepada klien.',
                'mission' => self::bullets([
                    'Memberikan layanan hukum berkualitas tinggi yang tepat waktu dan berorientasi solusi.',
                    'Melindungi kepentingan klien dengan menjunjung tinggi kode etik profesi advokat.',
                    'Mengembangkan advokat muda yang unggul dan berintegritas.',
                    'Berkontribusi pada pembangunan hukum melalui layanan pro bono dan publikasi.',
                ]),
                'history' => self::paragraphs([
                    'Kantor hukum ini didirikan pada 2005 oleh Dr. Bagus Wiratama, S.H., LL.M. dengan fokus awal pada hukum perusahaan dan penanaman modal asing.',
                    'Seiring meningkatnya kebutuhan klien, kami membuka praktik litigasi dan arbitrase pada 2011, serta praktik ketenagakerjaan dan perlindungan data pribadi pada 2020.',
                ]),
                'company_values' => self::values([
                    'Integritas' => 'menjunjung tinggi kejujuran dan kode etik advokat.',
                    'Kerahasiaan' => 'setiap informasi klien dijaga dengan ketat.',
                    'Keunggulan' => 'analisis hukum yang mendalam dan cermat.',
                    'Responsif' => 'hadir cepat saat klien membutuhkan.',
                ]),
                'hero_image' => self::img("$p-hero", 1600, 900),
                'logo' => null,
                'seo_title' => 'Wiratama & Rekan - Kantor Hukum Korporasi & Litigasi Jakarta',
                'seo_description' => 'Kantor hukum di Jakarta untuk hukum korporasi, penanaman modal, litigasi, arbitrase, ketenagakerjaan, dan perlindungan data pribadi.',
                'seo_keywords' => 'law firm jakarta, kantor hukum, pengacara korporasi, konsultan hukum, advokat litigasi',
            ],
            'services' => self::services($p, [
                ['Hukum Korporasi', 'Pendirian badan usaha, tata kelola, restrukturisasi, serta aksi korporasi perusahaan.', 'building'],
                ['Litigasi & Arbitrase', 'Pendampingan sengketa perdata, niaga, dan arbitrase di BANI maupun forum internasional.', 'scale'],
                ['Penanaman Modal', 'Perizinan PMA, struktur investasi, dan kepatuhan regulasi sektoral bagi investor.', 'banknotes'],
                ['Ketenagakerjaan', 'Penyusunan peraturan perusahaan, PKB, serta penyelesaian perselisihan hubungan industrial.', 'users'],
                ['Perlindungan Data Pribadi', 'Audit kepatuhan UU PDP, penyusunan kebijakan privasi, dan penanganan insiden data.', 'shield'],
                ['Uji Tuntas Hukum', 'Legal due diligence untuk transaksi merger, akuisisi, dan pendanaan.', 'clipboard'],
            ]),
            'products' => self::products($p, [
                ['Paket Pendirian PT', 'Pendirian PT lengkap dengan akta notaris, SK Kemenkumham, NIB, dan NPWP badan.', 8500000, 'Paket Korporasi'],
                ['Legal Retainer Bulanan', 'Konsultasi hukum tanpa batas dan review hingga 10 kontrak per bulan.', 15000000, 'Retainer'],
                ['Review & Drafting Kontrak', 'Penyusunan atau peninjauan perjanjian bisnis dalam bahasa Indonesia dan Inggris.', 3500000, 'Dokumen Hukum'],
                ['Audit Kepatuhan UU PDP', 'Penilaian kesiapan perusahaan terhadap UU Perlindungan Data Pribadi.', null, 'Kepatuhan'],
                ['Konsultasi Hukum Awal', 'Sesi konsultasi 60 menit dengan advokat senior, tatap muka atau daring.', 1500000, 'Konsultasi'],
            ]),
            'projects' => self::projects($p, [
                ['Akuisisi Perusahaan Distribusi Farmasi', 'Penasihat hukum pembeli dalam akuisisi senilai USD 120 juta termasuk uji tuntas.', 'Investor Strategis Asia', 'Jakarta', 2023, 'Merger & Akuisisi', null],
                ['Sengketa Konstruksi di BANI', 'Mewakili kontraktor dalam sengketa klaim pekerjaan tambah senilai Rp 340 miliar.', 'Kontraktor BUMN', 'Jakarta', 2022, 'Arbitrase', null],
                ['Struktur Investasi Pembangkit EBT', 'Penasihat struktur PMA dan perizinan proyek pembangkit listrik tenaga surya.', 'Konsorsium Energi Terbarukan', 'Nusa Tenggara Timur', 2024, 'Penanaman Modal', null],
                ['Kepatuhan PDP Perusahaan Fintech', 'Audit dan implementasi program kepatuhan UU PDP untuk platform pinjaman digital.', 'Platform Fintech Nasional', 'Jakarta', 2025, 'Perlindungan Data', null],
                ['Restrukturisasi Utang Grup Ritel', 'Pendampingan PKPU dan negosiasi rencana perdamaian dengan 200 kreditur.', 'Grup Ritel Nasional', 'Jakarta Pusat', 2020, 'Restrukturisasi', null],
                ['Perjanjian Kerja Bersama Manufaktur', 'Negosiasi dan penyusunan PKB untuk 6.000 pekerja pabrik otomotif.', 'Produsen Komponen Otomotif', 'Karawang, Jawa Barat', 2021, 'Ketenagakerjaan', null],
            ]),
            'team' => self::team($p, $domain, [
                ['Dr. Bagus Wiratama, S.H., LL.M.', 'Managing Partner', 'Advokat senior dengan pengalaman 25 tahun di bidang korporasi dan penanaman modal.', 'bagus-wiratama', 'bagus.wiratama'],
                ['Fransiska Halim, S.H., M.H.', 'Partner, Litigasi & Arbitrase', 'Telah menangani lebih dari 150 perkara niaga dan arbitrase.', 'fransiska-halim', 'fransiska.halim'],
                ['Rizky Firmansyah, S.H., LL.M.', 'Partner, Ketenagakerjaan', 'Spesialis hubungan industrial dan penyelesaian perselisihan ketenagakerjaan.', 'rizky-firmansyah', 'rizky.firmansyah'],
                ['Anindita Rahayu, S.H.', 'Senior Associate, Data Privacy', 'Konsultan hukum bersertifikat CIPP/E yang menangani kepatuhan perlindungan data.', 'anindita-rahayu', 'anindita.rahayu'],
            ]),
            'testimonials' => self::testimonials($p, [
                ['Johan Prasetya', 'PT Medika Distribusi Utama', 'Tim Wiratama & Rekan menangani transaksi akuisisi kami dengan sangat cermat dan tepat waktu. Penjelasan risikonya selalu lugas dan mudah dipahami direksi.', 5],
                ['Ir. Haryanto', 'Kontraktor BUMN', 'Strategi arbitrase yang disusun sangat solid dan didukung bukti yang kuat. Kami memperoleh putusan yang sangat menguntungkan.', 5],
                ['Vania Christina', 'Platform Fintech Nasional', 'Program kepatuhan PDP yang mereka rancang praktis dan bisa langsung diterapkan oleh tim kami. Sangat direkomendasikan untuk perusahaan teknologi.', 4],
            ]),
            'gallery' => self::gallery($p, [
                ['Ruang Rapat Utama', 'Ruang pertemuan klien di Menara Kuningan.', 'Kantor'],
                ['Perpustakaan Hukum', 'Koleksi literatur dan yurisprudensi kantor.', 'Kantor'],
                ['Seminar UU PDP', 'Seminar kepatuhan perlindungan data bersama 200 peserta.', 'Acara'],
                ['Klinik Hukum Pro Bono', 'Layanan konsultasi hukum gratis bagi masyarakat.', 'Pro Bono'],
                ['Penandatanganan Transaksi', 'Closing transaksi akuisisi lintas negara.', 'Transaksi'],
                ['Pelatihan Advokat Muda', 'Program pengembangan associate tahunan.', 'SDM'],
            ]),
            'pages' => [
                self::careerPage($p, 'Wiratama & Rekan', 'recruitment@'.$domain, ['Associate - Corporate & M&A', 'Associate - Litigation', 'Legal Intern', 'Paralegal']),
                self::privacyPage($p, $name, 'privacy@'.$domain),
                self::page($p, 'Publikasi Hukum', 'publikasi', [
                    'Wiratama & Rekan secara berkala menerbitkan legal update dan artikel analisis mengenai perkembangan peraturan perundang-undangan yang berdampak pada dunia usaha.',
                    'Publikasi ini disusun untuk tujuan informasi umum dan tidak dimaksudkan sebagai nasihat hukum atas suatu permasalahan tertentu.',
                ], 'Legal Update Terbaru', [
                    'Kewajiban Pejabat Pelindungan Data Pribadi berdasarkan UU PDP',
                    'Perubahan Ketentuan Perizinan Berusaha Berbasis Risiko',
                    'Pokok-Pokok Pengaturan Alih Daya dan PKWT Terbaru',
                    'Penegakan Putusan Arbitrase Asing di Indonesia',
                ], 'Untuk berlangganan legal update, kirimkan email ke publikasi@wiratamalaw.com.', 'Legal update dan artikel analisis hukum dari Wiratama & Rekan Law Firm.'),
            ],
        ];
    }

    /**
     * Minimal: architecture studio.
     */
    private static function minimal(): array
    {
        $p = 'arch';
        $name = 'Studio Ruang Lestari';
        $domain = 'ruanglestari.studio';

        return [
            'company' => [
                'name' => $name,
                'tagline' => 'Arsitektur tropis yang tenang, jujur, dan berkelanjutan',
                'description' => 'Studio Ruang Lestari adalah studio arsitektur dan interior yang merancang hunian, ruang hospitality, dan bangunan publik dengan pendekatan tropis modern. Kami percaya ruang yang baik lahir dari kesederhanaan, cahaya alami, dan material lokal.',
                'established_year' => 2012,
                'phone' => '(0361) 4712 300',
                'email' => 'studio@'.$domain,
                'whatsapp' => '081337001122',
                'address' => 'Jl. Tukad Badung No. 45, Renon',
                'city' => 'Denpasar',
                'province' => 'Bali',
                'country' => 'Indonesia',
                'postal_code' => '80226',
                'latitude' => -8.6705,
                'longitude' => 115.2126,
                'working_hours' => 'Senin - Jumat, 08.00 - 17.00',
                'website' => 'https://www.'.$domain,
                'social_links' => self::socials('ruanglestari.studio', ['instagram', 'linkedin', 'facebook', 'youtube']),
                'about' => self::paragraphs([
                    'Kami merancang ruang yang menyatu dengan iklim, lanskap, dan budaya setempat. Setiap proyek dimulai dari pengamatan cermat terhadap tapak: arah matahari, angin, vegetasi, dan cara orang akan hidup di dalamnya.',
                    'Studio kecil kami terdiri dari 18 arsitek dan desainer interior yang terlibat langsung dari sketsa pertama hingga pengawasan pembangunan.',
                ]),
                'vision' => 'Menghadirkan arsitektur yang membuat manusia hidup lebih baik dan bumi tetap lestari.',
                'mission' => self::bullets([
                    'Merancang bangunan yang responsif terhadap iklim tropis dan hemat energi.',
                    'Mengutamakan material lokal dan keterampilan perajin setempat.',
                    'Menghadirkan detail yang sederhana, presisi, dan tahan lama.',
                    'Mendampingi klien secara personal dari konsep hingga bangunan selesai.',
                ]),
                'history' => self::paragraphs([
                    'Studio Ruang Lestari didirikan pada 2012 oleh arsitek Made Arya Wibisana setelah satu dekade berpraktik di Singapura dan Tokyo.',
                    'Proyek rumah bambu di Ubud pada 2015 membawa studio meraih penghargaan arsitektur nasional. Sejak itu kami berkarya di Bali, Jawa, Nusa Tenggara, dan beberapa kota di Asia.',
                ]),
                'company_values' => self::values([
                    'Kesederhanaan' => 'menghilangkan yang tidak perlu agar yang esensial menonjol.',
                    'Kontekstual' => 'setiap desain berangkat dari tapak dan budayanya.',
                    'Keberlanjutan' => 'memilih solusi pasif dan material ramah lingkungan.',
                    'Keahlian' => 'menghargai kerja tangan perajin dan ketelitian detail.',
                ]),
                'hero_image' => self::img("$p-hero", 1600, 900),
                'logo' => null,
                'seo_title' => 'Studio Ruang Lestari - Arsitek & Desain Interior Bali',
                'seo_description' => 'Studio arsitektur dan interior di Bali untuk rumah tinggal, villa, hospitality, dan bangunan publik dengan pendekatan tropis modern berkelanjutan.',
                'seo_keywords' => 'arsitek bali, studio arsitektur, desain villa, arsitektur tropis, desain interior',
            ],
            'services' => self::services($p, [
                ['Desain Arsitektur', 'Perancangan rumah tinggal, villa, dan bangunan komersial dari konsep hingga gambar kerja.', 'home'],
                ['Desain Interior', 'Interior yang hangat dan fungsional dengan furnitur kustom karya perajin lokal.', 'paint'],
                ['Perencanaan Tapak', 'Masterplan kawasan resor dan hunian dengan analisis lanskap dan lingkungan.', 'globe'],
                ['Desain Berkelanjutan', 'Strategi desain pasif, efisiensi energi, dan pendampingan sertifikasi bangunan hijau.', 'leaf'],
                ['Pengawasan Berkala', 'Pengawasan desain di lapangan untuk memastikan kualitas pelaksanaan.', 'clipboard'],
                ['Konsultasi Renovasi', 'Kajian dan desain ulang bangunan lama agar lebih nyaman dan bernilai.', 'wrench'],
            ]),
            'products' => self::products($p, [
                ['Konsultasi Desain Awal', 'Kunjungan tapak dan sesi konsultasi konsep selama dua jam.', 2500000, 'Konsultasi'],
                ['Paket Desain Rumah Tinggal', 'Desain arsitektur lengkap hingga gambar kerja. Harga per m² luas bangunan.', 350000, 'Desain'],
                ['Paket Desain Interior', 'Desain interior lengkap termasuk furnitur kustom. Harga per m².', 450000, 'Desain'],
                ['Masterplan Kawasan', 'Perencanaan kawasan resor dan hunian skala besar.', null, 'Perencanaan'],
            ]),
            'projects' => self::projects($p, [
                ['Rumah Bambu Sayan', 'Rumah keluarga berstruktur bambu dengan ventilasi silang dan atap lebar.', 'Keluarga Hartanto', 'Ubud, Bali', 2019, 'Rumah Tinggal', null],
                ['Villa Tebing Uluwatu', 'Villa empat kamar yang mengikuti kontur tebing dengan material batu paras lokal.', 'Klien Privat', 'Uluwatu, Bali', 2022, 'Villa', null],
                ['Perpustakaan Desa Tenganan', 'Perpustakaan komunitas dari kayu daur ulang dan atap alang-alang.', 'Yayasan Baca Nusantara', 'Karangasem, Bali', 2021, 'Bangunan Publik', null],
                ['Kafe Teras Sawah', 'Kafe terbuka di tengah sawah dengan struktur beton ekspos dan kayu ulin bekas.', 'Teras Sawah Coffee', 'Tabanan, Bali', 2023, 'Hospitality', null],
                ['Rumah Kota Kebayoran', 'Renovasi rumah tahun 70-an menjadi hunian tropis modern dengan inner courtyard.', 'Keluarga Santoso', 'Jakarta Selatan', 2024, 'Renovasi', null],
                ['Resor Eko Labuan Bajo', 'Masterplan dan desain 24 unit cottage resor berkelanjutan.', 'Komodo Eco Retreat', 'Labuan Bajo, NTT', 2025, 'Hospitality', 'https://www.ruanglestari.studio/proyek/labuan-bajo'],
            ]),
            'team' => self::team($p, $domain, [
                ['Made Arya Wibisana, IAI', 'Prinsipal Arsitek', 'Pendiri studio dengan pengalaman berpraktik di Singapura dan Tokyo.', 'madearyawibisana', 'arya'],
                ['Laras Ayuningtyas', 'Associate Architect', 'Memimpin proyek hospitality dan perencanaan kawasan studio.', 'larasayuningtyas', 'laras'],
                ['Komang Adi Putra', 'Kepala Desain Interior', 'Merancang furnitur dan interior bersama jaringan perajin lokal Bali.', 'komangadiputra', 'komang'],
                ['Sekar Wulandari', 'Konsultan Keberlanjutan', 'Ahli fisika bangunan dan sertifikasi bangunan hijau.', 'sekarwulandari', 'sekar'],
            ]),
            'testimonials' => self::testimonials($p, [
                ['Liana Hartanto', 'Pemilik Rumah Bambu Sayan', 'Rumah kami terasa sejuk sepanjang hari tanpa perlu pendingin ruangan. Tim Ruang Lestari sangat sabar mendengarkan kebutuhan keluarga kami.', 5],
                ['Putu Sudarsana', 'Teras Sawah Coffee', 'Desainnya sederhana tetapi sangat kuat karakternya. Kafe kami kini menjadi destinasi yang sering difoto pengunjung.', 5],
                ['Jessica Moreau', 'Komodo Eco Retreat', 'Pendekatan keberlanjutan studio ini sangat serius dan terukur. Mereka memahami lanskap Flores dengan sangat baik.', 4],
            ]),
            'gallery' => self::gallery($p, [
                ['Cahaya Pagi', 'Cahaya alami masuk melalui kisi kayu Rumah Bambu Sayan.', 'Arsitektur'],
                ['Courtyard', 'Taman dalam rumah kota Kebayoran.', 'Arsitektur'],
                ['Detail Material', 'Pertemuan batu paras, kayu, dan beton ekspos.', 'Detail'],
                ['Ruang Baca', 'Interior perpustakaan desa Tenganan.', 'Interior'],
                ['Maket Studi', 'Maket kayu untuk studi massa bangunan.', 'Proses'],
                ['Studio Renon', 'Meja kerja dan perpustakaan material studio.', 'Studio'],
            ]),
            'pages' => [
                self::careerPage($p, 'Studio Ruang Lestari', 'karir@'.$domain, ['Arsitek Junior', 'Desainer Interior', 'Drafter 3D / Visualizer', 'Magang Arsitektur']),
                self::privacyPage($p, 'PT Ruang Lestari Arsitektur', 'studio@'.$domain),
                self::page($p, 'Desain Berkelanjutan', 'sustainability', [
                    'Bangunan menyumbang hampir 40% emisi karbon global. Sebagai arsitek, kami memiliki tanggung jawab untuk merancang dengan lebih bijak terhadap bumi.',
                    'Di setiap proyek, kami mengutamakan strategi pasif terlebih dahulu sebelum menambahkan teknologi aktif, sehingga bangunan nyaman secara alami dan hemat energi sepanjang usianya.',
                ], 'Prinsip Desain Kami', [
                    'Orientasi bangunan dan ventilasi silang untuk mengurangi kebutuhan pendingin',
                    'Penggunaan material lokal, terbarukan, dan material bekas pakai',
                    'Pengelolaan air hujan dan lanskap dengan vegetasi asli',
                    'Panel surya dan pencahayaan alami untuk efisiensi energi',
                ], 'Kami dengan senang hati mendiskusikan strategi keberlanjutan untuk proyek Anda sejak tahap konsep.', 'Pendekatan desain berkelanjutan Studio Ruang Lestari untuk arsitektur tropis.'),
            ],
        ];
    }

    /**
     * Executive: investment & asset management firm.
     */
    private static function executive(): array
    {
        $p = 'exec';
        $name = 'PT Mandala Investama Asset Management';
        $domain = 'mandalainvestama.co.id';

        return [
            'company' => [
                'name' => $name,
                'tagline' => 'Mengelola kekayaan dengan disiplin, integritas, dan visi jangka panjang',
                'description' => 'Mandala Investama adalah perusahaan manajer investasi berizin OJK yang mengelola dana institusi, keluarga, dan investor individu. Kami menghadirkan strategi investasi berbasis riset untuk pertumbuhan nilai yang berkelanjutan.',
                'established_year' => 2008,
                'phone' => '(021) 2788 6600',
                'email' => 'client.service@'.$domain,
                'whatsapp' => '081222334455',
                'address' => 'Sequis Tower Lt. 30, Jl. Jend. Sudirman Kav. 71',
                'city' => 'Jakarta Selatan',
                'province' => 'DKI Jakarta',
                'country' => 'Indonesia',
                'postal_code' => '12190',
                'latitude' => -6.2146,
                'longitude' => 106.8166,
                'working_hours' => 'Senin - Jumat, 08.00 - 17.00',
                'website' => 'https://www.'.$domain,
                'social_links' => self::socials('mandalainvestama', ['linkedin', 'instagram', 'youtube', 'x']),
                'about' => self::paragraphs([
                    'Mandala Investama mengelola dana kelolaan lebih dari Rp 28 triliun melalui reksa dana, kontrak pengelolaan dana, dan solusi investasi khusus bagi dana pensiun, asuransi, yayasan, serta keluarga.',
                    'Tim investasi kami yang terdiri dari analis dan manajer portofolio berpengalaman menerapkan proses investasi yang disiplin, didukung manajemen risiko yang ketat dan prinsip ESG.',
                ]),
                'vision' => 'Menjadi manajer investasi terpercaya yang menjaga dan menumbuhkan kesejahteraan lintas generasi.',
                'mission' => self::bullets([
                    'Memberikan imbal hasil yang konsisten melalui proses investasi berbasis riset.',
                    'Menempatkan kepentingan investor di atas segalanya dengan tata kelola yang kuat.',
                    'Menerapkan manajemen risiko yang prudent dan kepatuhan penuh terhadap regulasi.',
                    'Mendorong investasi bertanggung jawab yang mempertimbangkan aspek ESG.',
                ]),
                'history' => self::paragraphs([
                    'Mandala Investama memperoleh izin usaha manajer investasi pada 2008 dan meluncurkan reksa dana saham pertamanya di tengah krisis keuangan global, dengan keyakinan pada prospek jangka panjang Indonesia.',
                    'Disiplin investasi tersebut membuahkan kepercayaan investor. Pada 2019 kami meluncurkan layanan private wealth management dan pada 2023 menjadi penandatangan Principles for Responsible Investment.',
                ]),
                'company_values' => self::values([
                    'Amanah' => 'mengelola dana investor dengan penuh tanggung jawab.',
                    'Disiplin' => 'setia pada proses investasi dalam kondisi pasar apa pun.',
                    'Transparansi' => 'pelaporan kinerja yang jelas dan berkala.',
                    'Prudent' => 'mengutamakan perlindungan modal dan pengelolaan risiko.',
                    'Jangka Panjang' => 'fokus pada penciptaan nilai yang berkelanjutan.',
                ]),
                'hero_image' => self::img("$p-hero", 1600, 900),
                'logo' => null,
                'seo_title' => 'Mandala Investama - Manajer Investasi & Wealth Management',
                'seo_description' => 'Manajer investasi berizin OJK yang mengelola reksa dana, discretionary fund, dan private wealth untuk institusi, keluarga, dan investor individu.',
                'seo_keywords' => 'manajer investasi, reksa dana, asset management indonesia, wealth management, investasi institusi',
            ],
            'services' => self::services($p, [
                ['Reksa Dana', 'Beragam reksa dana saham, pendapatan tetap, campuran, dan pasar uang sesuai profil risiko.', 'chart'],
                ['Discretionary Fund', 'Kontrak pengelolaan dana khusus untuk dana pensiun, asuransi, dan yayasan.', 'briefcase'],
                ['Private Wealth Management', 'Perencanaan dan pengelolaan kekayaan keluarga secara personal dan menyeluruh.', 'banknotes'],
                ['Investasi Berkelanjutan', 'Portofolio berbasis kriteria ESG untuk investor yang peduli dampak.', 'leaf'],
                ['Riset & Advisory', 'Riset makroekonomi dan rekomendasi alokasi aset bagi investor institusi.', 'light-bulb'],
                ['Manajemen Risiko', 'Pengukuran dan pengendalian risiko portofolio dengan standar internasional.', 'shield'],
            ]),
            'products' => self::products($p, [
                ['Mandala Ekuitas Prima', 'Reksa dana saham yang berinvestasi pada perusahaan berfundamental kuat. Minimum pembelian.', 100000, 'Reksa Dana Saham'],
                ['Mandala Obligasi Optima', 'Reksa dana pendapatan tetap dengan fokus pada obligasi pemerintah dan korporasi berperingkat tinggi.', 100000, 'Reksa Dana Pendapatan Tetap'],
                ['Mandala Kas Likuid', 'Reksa dana pasar uang untuk pengelolaan dana jangka pendek yang likuid.', 10000, 'Reksa Dana Pasar Uang'],
                ['Mandala Syariah Berimbang', 'Reksa dana campuran berbasis efek syariah untuk pertumbuhan yang seimbang.', 100000, 'Reksa Dana Syariah'],
                ['Mandala ESG Leaders', 'Reksa dana saham yang memilih emiten dengan praktik ESG terbaik.', 100000, 'Reksa Dana Saham'],
                ['Private Mandate', 'Portofolio kustom untuk investor dengan dana kelolaan di atas Rp 10 miliar.', null, 'Wealth Management'],
            ]),
            'projects' => self::projects($p, [
                ['Pengelolaan Dana Pensiun BUMN', 'Mandat pengelolaan portofolio pendapatan tetap senilai Rp 3,2 triliun.', 'Dana Pensiun BUMN', 'Jakarta', 2021, 'Institusi', null],
                ['Peluncuran Mandala ESG Leaders', 'Reksa dana ESG pertama perusahaan dengan dana kelolaan Rp 1 triliun dalam setahun.', 'Investor Ritel & Institusi', 'Jakarta', 2023, 'Produk', 'https://www.mandalainvestama.co.id/esg'],
                ['Struktur Investasi Family Office', 'Perancangan alokasi aset dan tata kelola investasi keluarga lintas generasi.', 'Family Office Privat', 'Surabaya, Jawa Timur', 2022, 'Wealth Management', null],
                ['Mandat Endowment Universitas', 'Pengelolaan dana abadi universitas dengan target imbal hasil riil jangka panjang.', 'Yayasan Pendidikan Nasional', 'Yogyakarta', 2020, 'Institusi', null],
                ['Kemitraan Distribusi Digital', 'Distribusi reksa dana melalui tiga platform investasi digital terkemuka.', 'Agen Penjual Reksa Dana Digital', 'Jakarta', 2024, 'Kemitraan', null],
                ['Portofolio Syariah Asuransi', 'Pengelolaan dana tabarru dan investasi syariah perusahaan asuransi.', 'Asuransi Syariah Nasional', 'Jakarta', 2025, 'Institusi', null],
            ]),
            'team' => self::team($p, $domain, [
                ['Hadi Gunawan Tjahjadi, CFA', 'Presiden Direktur', 'Lebih dari 25 tahun memimpin pengelolaan investasi di pasar modal Indonesia.', 'hadi-tjahjadi', 'hadi.tjahjadi'],
                ['Dra. Maria Christina Sitompul', 'Direktur Investasi', 'Chief Investment Officer yang mengawasi seluruh strategi dan portofolio investasi.', 'maria-sitompul', 'maria.sitompul'],
                ['Arya Wicaksono, CFA', 'Kepala Riset', 'Memimpin tim riset ekuitas dan makroekonomi Mandala Investama.', 'arya-wicaksono', 'arya.wicaksono'],
                ['Shinta Larasati, FRM', 'Direktur Kepatuhan & Risiko', 'Memastikan tata kelola, kepatuhan regulasi, dan manajemen risiko yang ketat.', 'shinta-larasati', 'shinta.larasati'],
            ]),
            'testimonials' => self::testimonials($p, [
                ['Drs. Bambang Riyanto', 'Dana Pensiun BUMN', 'Mandala Investama konsisten memberikan kinerja di atas tolok ukur dengan risiko yang terkendali. Pelaporan bulanannya sangat lengkap dan transparan.', 5],
                ['Prof. Endang Sulistyowati', 'Yayasan Pendidikan Nasional', 'Tim Mandala memahami kebutuhan dana abadi yang harus terjaga nilainya lintas generasi. Pendampingan komite investasi kami sangat profesional.', 5],
                ['Hendrik Wijaya', 'Nasabah Private Wealth', 'Saya merasa didengar dan dipahami, bukan sekadar ditawari produk. Portofolio keluarga kami kini lebih terstruktur dan terdiversifikasi.', 4],
            ]),
            'gallery' => self::gallery($p, [
                ['Ruang Investasi', 'Trading room dan ruang kerja tim manajer portofolio.', 'Kantor'],
                ['Market Outlook 2025', 'Paparan outlook pasar kepada investor institusi.', 'Acara'],
                ['Komite Investasi', 'Rapat komite investasi mingguan.', 'Tata Kelola'],
                ['Lounge Private Wealth', 'Ruang pertemuan eksklusif untuk nasabah.', 'Kantor'],
                ['Edukasi Investor', 'Program literasi pasar modal bersama Bursa Efek.', 'Edukasi'],
                ['Penghargaan Manajer Investasi', 'Penghargaan Manajer Investasi Terbaik 2024.', 'Penghargaan'],
            ]),
            'pages' => [
                self::careerPage($p, 'Mandala Investama', 'hr@'.$domain, ['Equity Research Analyst', 'Fixed Income Portfolio Manager', 'Relationship Manager Private Wealth', 'Risk Management Officer']),
                self::privacyPage($p, $name, 'compliance@'.$domain),
                self::page($p, 'Kepatuhan & Manajemen Risiko', 'kepatuhan', [
                    'Mandala Investama terdaftar dan diawasi oleh Otoritas Jasa Keuangan (OJK). Kami menempatkan kepatuhan terhadap regulasi dan perlindungan investor sebagai prinsip utama dalam setiap aktivitas usaha.',
                    'Fungsi kepatuhan dan manajemen risiko berdiri independen dari fungsi investasi dan melapor langsung kepada Direksi serta Dewan Komisaris.',
                ], 'Kerangka Kepatuhan Kami', [
                    'Penerapan program APU-PPT (Anti Pencucian Uang dan Pencegahan Pendanaan Terorisme)',
                    'Pemisahan aset investor melalui Bank Kustodian independen',
                    'Pengukuran risiko pasar, likuiditas, dan kredit secara harian',
                    'Kode etik dan kebijakan transaksi pribadi bagi seluruh karyawan',
                ], 'Investasi melalui reksa dana mengandung risiko. Calon investor wajib membaca dan memahami prospektus sebelum memutuskan untuk berinvestasi. Kinerja masa lalu tidak mencerminkan kinerja masa datang.', 'Komitmen Mandala Investama terhadap kepatuhan regulasi OJK dan manajemen risiko.'),
            ],
        ];
    }

    // ------------------------------------------------------------------ Builders

    private static function img(string $seed, int $width, int $height): string
    {
        return "https://picsum.photos/seed/{$seed}/{$width}/{$height}";
    }

    private static function paragraphs(array $paragraphs): string
    {
        return implode('', array_map(fn (string $text) => "<p>{$text}</p>", $paragraphs));
    }

    private static function bullets(array $items): string
    {
        return '<ul>'.implode('', array_map(fn (string $text) => "<li>{$text}</li>", $items)).'</ul>';
    }

    /**
     * @param  array<string, string>  $values  label => short explanation
     */
    private static function values(array $values): string
    {
        $items = [];
        foreach ($values as $label => $text) {
            $items[] = "<li><strong>{$label}</strong> — {$text}</li>";
        }

        return '<ul>'.implode('', $items).'</ul>';
    }

    private static function socials(string $handle, array $networks): array
    {
        $bases = [
            'facebook' => 'https://www.facebook.com/',
            'instagram' => 'https://www.instagram.com/',
            'linkedin' => 'https://www.linkedin.com/company/',
            'youtube' => 'https://www.youtube.com/@',
            'tiktok' => 'https://www.tiktok.com/@',
            'x' => 'https://x.com/',
        ];

        $links = [];
        foreach ($networks as $network) {
            $links[$network] = $bases[$network].$handle;
        }

        return $links;
    }

    /**
     * @param  array<int, array{0: string, 1: string, 2: string}>  $rows  [title, description, icon]
     */
    private static function services(string $prefix, array $rows): array
    {
        return array_map(fn (array $row, int $i) => [
            'title' => $row[0],
            'description' => $row[1],
            'icon' => $row[2],
            'image' => self::img("{$prefix}-service-".($i + 1), 800, 600),
        ], $rows, array_keys($rows));
    }

    /**
     * @param  array<int, array{0: string, 1: string, 2: int|float|null, 3: string}>  $rows  [name, description, price, category]
     */
    private static function products(string $prefix, array $rows): array
    {
        return array_map(fn (array $row, int $i) => [
            'name' => $row[0],
            'description' => $row[1],
            'image' => self::img("{$prefix}-product-".($i + 1), 800, 600),
            'price' => $row[2],
            'category' => $row[3],
        ], $rows, array_keys($rows));
    }

    /**
     * @param  array<int, array>  $rows  [title, description, client, location, year, category, url]
     */
    private static function projects(string $prefix, array $rows): array
    {
        return array_map(fn (array $row, int $i) => [
            'title' => $row[0],
            'description' => $row[1],
            'image' => self::img("{$prefix}-project-".($i + 1), 1200, 800),
            'client' => $row[2],
            'location' => $row[3],
            'year' => $row[4],
            'category' => $row[5],
            'url' => $row[6],
        ], $rows, array_keys($rows));
    }

    /**
     * @param  array<int, array{0: string, 1: string, 2: string, 3: string, 4: string}>  $rows  [name, position, bio, linkedin handle, email local part]
     */
    private static function team(string $prefix, string $domain, array $rows): array
    {
        return array_map(fn (array $row, int $i) => [
            'name' => $row[0],
            'position' => $row[1],
            'photo' => self::img("{$prefix}-team-".($i + 1), 600, 700),
            'bio' => $row[2],
            'linkedin' => 'https://www.linkedin.com/in/'.$row[3],
            'email' => $row[4].'@'.$domain,
        ], $rows, array_keys($rows));
    }

    /**
     * @param  array<int, array{0: string, 1: string, 2: string, 3: int}>  $rows  [customer name, company, testimonial, rating]
     */
    private static function testimonials(string $prefix, array $rows): array
    {
        return array_map(fn (array $row, int $i) => [
            'customer_name' => $row[0],
            'company' => $row[1],
            'photo' => self::img("{$prefix}-testimonial-".($i + 1), 200, 200),
            'testimonial' => $row[2],
            'rating' => $row[3],
        ], $rows, array_keys($rows));
    }

    /**
     * @param  array<int, array{0: string, 1: string, 2: string}>  $rows  [title, description, category]
     */
    private static function gallery(string $prefix, array $rows): array
    {
        return array_map(fn (array $row, int $i) => [
            'image' => self::img("{$prefix}-gallery-".($i + 1), 1000, 750),
            'title' => $row[0],
            'description' => $row[1],
            'category' => $row[2],
        ], $rows, array_keys($rows));
    }

    /**
     * Generic published page: intro paragraphs, an <h2> with a list, and a closing paragraph.
     */
    private static function page(
        string $prefix,
        string $title,
        string $slug,
        array $intro,
        string $heading,
        array $items,
        string $closing,
        string $seoDescription,
    ): array {
        return [
            'title' => $title,
            'slug' => $slug,
            'content' => self::paragraphs($intro)
                ."<h2>{$heading}</h2>"
                .self::bullets($items)
                .self::paragraphs([$closing]),
            'status' => 'published',
            'seo_title' => $title,
            'seo_description' => $seoDescription,
            'featured_image' => self::img("{$prefix}-page-{$slug}", 1600, 900),
        ];
    }

    private static function careerPage(string $prefix, string $brand, string $email, array $roles): array
    {
        return self::page(
            $prefix,
            'Karir',
            'karir',
            [
                "Bergabunglah bersama {$brand} dan tumbuh bersama tim yang berdedikasi, kolaboratif, dan bersemangat menghadirkan karya terbaik bagi pelanggan.",
                'Kami menyediakan lingkungan kerja yang suportif, jenjang karier yang jelas, program pelatihan berkelanjutan, serta paket kompensasi dan benefit yang kompetitif.',
            ],
            'Posisi yang Sedang Dibuka',
            $roles,
            "Kirimkan CV dan portofolio terbaru Anda ke {$email} dengan subjek sesuai posisi yang dilamar. Kami tidak memungut biaya apa pun dalam proses rekrutmen.",
            "Lowongan kerja dan informasi karier di {$brand}. Temukan posisi yang sesuai dan bergabung bersama kami.",
        );
    }

    private static function privacyPage(string $prefix, string $company, string $email): array
    {
        return self::page(
            $prefix,
            'Kebijakan Privasi',
            'kebijakan-privasi',
            [
                "{$company} menghormati dan melindungi privasi setiap pengunjung situs web serta pelanggan kami. Kebijakan ini menjelaskan bagaimana kami mengumpulkan, menggunakan, dan melindungi data pribadi Anda sesuai Undang-Undang Nomor 27 Tahun 2022 tentang Pelindungan Data Pribadi.",
                'Data pribadi yang kami kumpulkan dapat meliputi nama, alamat email, nomor telepon, nama perusahaan, serta informasi lain yang Anda berikan secara sukarela melalui formulir kontak atau komunikasi dengan kami.',
            ],
            'Penggunaan Data Pribadi',
            [
                'Menanggapi pertanyaan, permintaan penawaran, dan kebutuhan layanan Anda',
                'Mengirimkan informasi terkait produk, layanan, dan kegiatan perusahaan atas persetujuan Anda',
                'Meningkatkan kualitas situs web dan pengalaman pengguna',
                'Memenuhi kewajiban hukum dan peraturan yang berlaku',
            ],
            "Kami tidak menjual atau menyewakan data pribadi Anda kepada pihak ketiga. Untuk mengakses, memperbarui, atau menghapus data pribadi Anda, silakan hubungi kami melalui {$email}.",
            "Kebijakan privasi {$company} mengenai pengumpulan, penggunaan, dan perlindungan data pribadi.",
        );
    }
}
