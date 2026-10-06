<?php

/*
|--------------------------------------------------------------------------
| Website Template Layouts
|--------------------------------------------------------------------------
|
| A "layout" is a Blade theme located in resources/views/websites/templates/{key}.
| Each layout ships its own header, navigation, hero, section designs and
| footer. Database templates (managed by admins) reference one of these
| layouts and can override its default branding & section order, so admins
| can create many template variants without writing code.
|
*/

return [

    'sections' => [
        'hero' => 'Hero',
        'about' => 'About',
        'services' => 'Services',
        'products' => 'Products',
        'projects' => 'Projects / Portfolio',
        'team' => 'Team',
        'testimonials' => 'Testimonials',
        'gallery' => 'Gallery',
        'cta' => 'Call To Action',
        'contact' => 'Contact',
    ],

    'fonts' => [
        'Inter', 'Plus Jakarta Sans', 'Poppins', 'Montserrat', 'DM Sans', 'Manrope',
        'Space Grotesk', 'IBM Plex Sans', 'Playfair Display', 'Lora', 'Cormorant Garamond',
        'Archivo', 'Outfit', 'Sora', 'Libre Baskerville', 'Oswald',
    ],

    'button_styles' => [
        'rounded' => 'Rounded',
        'pill' => 'Pill',
        'square' => 'Square',
    ],

    'radii' => [
        'none' => '0px',
        'sm' => '4px',
        'md' => '8px',
        'lg' => '14px',
        'xl' => '24px',
    ],

    'layouts' => [

        'corporate' => [
            'name' => 'Corporate',
            'category' => 'corporate',
            'description' => 'Tampilan korporat klasik dengan top-bar kontak, hero split, dan struktur yang tegas. Cocok untuk perusahaan mapan.',
            'defaults' => [
                'primary_color' => '#1d4ed8', 'secondary_color' => '#0f172a',
                'heading_font' => 'Plus Jakarta Sans', 'body_font' => 'Inter',
                'button_style' => 'rounded', 'border_radius' => 'md',
                'sections' => ['hero', 'about', 'services', 'products', 'projects', 'team', 'testimonials', 'gallery', 'cta', 'contact'],
            ],
        ],

        'modern-business' => [
            'name' => 'Modern Business',
            'category' => 'corporate',
            'description' => 'Hero full-bleed dengan gradien, navbar transparan, kartu dengan shadow lembut dan statistik menonjol.',
            'defaults' => [
                'primary_color' => '#7c3aed', 'secondary_color' => '#f97316',
                'heading_font' => 'Outfit', 'body_font' => 'DM Sans',
                'button_style' => 'pill', 'border_radius' => 'xl',
                'sections' => ['hero', 'services', 'about', 'projects', 'testimonials', 'products', 'team', 'gallery', 'cta', 'contact'],
            ],
        ],

        'technology' => [
            'name' => 'Technology',
            'category' => 'technology',
            'description' => 'Dark mode futuristik dengan grid, glow, tipografi mono dan bento layout untuk perusahaan teknologi.',
            'defaults' => [
                'primary_color' => '#22d3ee', 'secondary_color' => '#a855f7',
                'heading_font' => 'Space Grotesk', 'body_font' => 'Inter',
                'button_style' => 'rounded', 'border_radius' => 'lg',
                'sections' => ['hero', 'services', 'products', 'about', 'projects', 'team', 'testimonials', 'gallery', 'cta', 'contact'],
            ],
        ],

        'construction' => [
            'name' => 'Construction',
            'category' => 'construction',
            'description' => 'Tegas dan industrial: huruf kapital tebal, aksen kuning, angka besar, dan portofolio proyek bergaya masonry.',
            'defaults' => [
                'primary_color' => '#f59e0b', 'secondary_color' => '#1c1917',
                'heading_font' => 'Oswald', 'body_font' => 'Archivo',
                'button_style' => 'square', 'border_radius' => 'none',
                'sections' => ['hero', 'about', 'services', 'projects', 'gallery', 'team', 'testimonials', 'products', 'cta', 'contact'],
            ],
        ],

        'manufacturing' => [
            'name' => 'Manufacturing',
            'category' => 'manufacturing',
            'description' => 'Fokus pada produk dan kapasitas produksi: katalog produk, alur proses bernomor, dan sertifikasi.',
            'defaults' => [
                'primary_color' => '#0f766e', 'secondary_color' => '#334155',
                'heading_font' => 'IBM Plex Sans', 'body_font' => 'IBM Plex Sans',
                'button_style' => 'square', 'border_radius' => 'sm',
                'sections' => ['hero', 'products', 'about', 'services', 'projects', 'gallery', 'testimonials', 'team', 'cta', 'contact'],
            ],
        ],

        'consulting' => [
            'name' => 'Consulting',
            'category' => 'professional',
            'description' => 'Elegan dan tenang dengan serif klasik, banyak ruang putih, layanan bernomor dan kutipan testimonial besar.',
            'defaults' => [
                'primary_color' => '#14532d', 'secondary_color' => '#b45309',
                'heading_font' => 'Playfair Display', 'body_font' => 'Manrope',
                'button_style' => 'square', 'border_radius' => 'none',
                'sections' => ['hero', 'about', 'services', 'testimonials', 'team', 'projects', 'products', 'gallery', 'cta', 'contact'],
            ],
        ],

        'creative-agency' => [
            'name' => 'Creative Agency',
            'category' => 'creative',
            'description' => 'Berani dan playful: tipografi raksasa, marquee, warna kontras, dan portofolio grid asimetris.',
            'defaults' => [
                'primary_color' => '#ff4d2e', 'secondary_color' => '#111111',
                'heading_font' => 'Sora', 'body_font' => 'DM Sans',
                'button_style' => 'pill', 'border_radius' => 'xl',
                'sections' => ['hero', 'projects', 'services', 'about', 'team', 'testimonials', 'gallery', 'products', 'cta', 'contact'],
            ],
        ],

        'professional-services' => [
            'name' => 'Professional Services',
            'category' => 'professional',
            'description' => 'Untuk firma hukum, akuntan dan klinik: sidebar info, area praktik bertab, dan jadwal konsultasi.',
            'defaults' => [
                'primary_color' => '#1e3a8a', 'secondary_color' => '#c2a14d',
                'heading_font' => 'Libre Baskerville', 'body_font' => 'Inter',
                'button_style' => 'rounded', 'border_radius' => 'sm',
                'sections' => ['hero', 'services', 'about', 'team', 'testimonials', 'projects', 'products', 'gallery', 'cta', 'contact'],
            ],
        ],

        'minimal' => [
            'name' => 'Minimal',
            'category' => 'other',
            'description' => 'Hitam-putih, satu kolom, tipografi sebagai elemen utama dan daftar sederhana tanpa ornamen.',
            'defaults' => [
                'primary_color' => '#111827', 'secondary_color' => '#6b7280',
                'heading_font' => 'Manrope', 'body_font' => 'Manrope',
                'button_style' => 'square', 'border_radius' => 'none',
                'sections' => ['hero', 'about', 'services', 'projects', 'products', 'team', 'testimonials', 'gallery', 'cta', 'contact'],
            ],
        ],

        'executive' => [
            'name' => 'Executive',
            'category' => 'finance',
            'description' => 'Mewah dan premium: navy gelap dengan emas, serif Cormorant, garis tipis dan foto lebar sinematik.',
            'defaults' => [
                'primary_color' => '#c9a227', 'secondary_color' => '#0b1a2e',
                'heading_font' => 'Cormorant Garamond', 'body_font' => 'Montserrat',
                'button_style' => 'square', 'border_radius' => 'none',
                'sections' => ['hero', 'about', 'services', 'projects', 'team', 'testimonials', 'gallery', 'products', 'cta', 'contact'],
            ],
        ],

    ],

];
