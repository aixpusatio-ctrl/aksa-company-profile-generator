<?php

/*
| Google Fonts available for branding: name => [weights, kind].
| kind: sans | serif | mono | display (affects the CSS fallback stack).
*/
$fontRegistry = [
    'Inter' => ['300;400;500;600;700;800', 'sans'],
    'Inter Tight' => ['300;400;500;600;700;800', 'sans'],
    'Plus Jakarta Sans' => ['300;400;500;600;700;800', 'sans'],
    'Poppins' => ['300;400;500;600;700;800', 'sans'],
    'Montserrat' => ['300;400;500;600;700;800', 'sans'],
    'DM Sans' => ['300;400;500;600;700;800', 'sans'],
    'Manrope' => ['300;400;500;600;700;800', 'sans'],
    'Space Grotesk' => ['300;400;500;600;700', 'sans'],
    'IBM Plex Sans' => ['300;400;500;600;700', 'sans'],
    'Archivo' => ['300;400;500;600;700;800', 'sans'],
    'Outfit' => ['300;400;500;600;700;800', 'sans'],
    'Sora' => ['300;400;500;600;700;800', 'sans'],
    'Oswald' => ['300;400;500;600;700', 'display'],
    'Syne' => ['400;500;600;700;800', 'display'],
    'Bricolage Grotesque' => ['300;400;500;600;700;800', 'sans'],
    'Figtree' => ['300;400;500;600;700;800', 'sans'],
    'Urbanist' => ['300;400;500;600;700;800', 'sans'],
    'Red Hat Display' => ['300;400;500;600;700;800', 'sans'],
    'Lexend' => ['300;400;500;600;700;800', 'sans'],
    'Nunito Sans' => ['300;400;500;600;700;800', 'sans'],
    'Work Sans' => ['300;400;500;600;700;800', 'sans'],
    'Public Sans' => ['300;400;500;600;700;800', 'sans'],
    'Rubik' => ['300;400;500;600;700;800', 'sans'],
    'Barlow' => ['300;400;500;600;700;800', 'sans'],
    'Barlow Condensed' => ['300;400;500;600;700;800', 'display'],
    'Libre Franklin' => ['300;400;500;600;700;800', 'sans'],
    'Jost' => ['300;400;500;600;700;800', 'sans'],
    'Epilogue' => ['300;400;500;600;700;800', 'sans'],
    'Schibsted Grotesk' => ['400;500;600;700;800', 'sans'],
    'Geist' => ['300;400;500;600;700;800', 'sans'],
    'Hanken Grotesk' => ['300;400;500;600;700;800', 'sans'],
    'Albert Sans' => ['300;400;500;600;700;800', 'sans'],
    'Onest' => ['300;400;500;600;700;800', 'sans'],
    'Chivo' => ['300;400;500;600;700;800', 'sans'],
    'Unbounded' => ['300;400;500;600;700;800', 'display'],
    'Big Shoulders Display' => ['300;400;500;600;700;800', 'display'],
    'Bebas Neue' => ['400', 'display'],
    'Anton' => ['400', 'display'],
    'Tenor Sans' => ['400', 'sans'],
    'Playfair Display' => ['400;500;600;700;800', 'serif'],
    'Lora' => ['400;500;600;700', 'serif'],
    'Cormorant Garamond' => ['300;400;500;600;700', 'serif'],
    'Libre Baskerville' => ['400;700', 'serif'],
    'Fraunces' => ['300;400;500;600;700;800', 'serif'],
    'Instrument Serif' => ['400', 'serif'],
    'Source Serif 4' => ['300;400;500;600;700;800', 'serif'],
    'Merriweather' => ['300;400;700', 'serif'],
    'Bodoni Moda' => ['400;500;600;700;800', 'serif'],
    'EB Garamond' => ['400;500;600;700;800', 'serif'],
    'Spectral' => ['300;400;500;600;700;800', 'serif'],
    'Newsreader' => ['300;400;500;600;700;800', 'serif'],
    'DM Serif Display' => ['400', 'serif'],
    'Young Serif' => ['400', 'serif'],
    'Cinzel' => ['400;500;600;700;800', 'serif'],
    'Marcellus' => ['400', 'serif'],
    'Prata' => ['400', 'serif'],
    'Italiana' => ['400', 'serif'],
    'JetBrains Mono' => ['300;400;500;600;700;800', 'mono'],
    'Space Mono' => ['400;700', 'mono'],
];

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
        'stats' => 'Statistics',
        'clients' => 'Clients / Partners',
    ],

    'font_registry' => $fontRegistry,

    'fonts' => array_keys($fontRegistry),

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

    /*
    | Layout themes. "composer" builds a website from the component library
    | (resources/views/components/company/*) and a design system stored in
    | the template's config; the others are hand-crafted Blade themes.
    */
    'layouts' => [

        'composer' => [
            'name' => 'Component Composer',
            'category' => 'corporate',
            'description' => 'Website disusun dari library komponen (navbar, hero, about, services, ...) dengan design system per template.',
            'defaults' => [
                'primary_color' => '#2563eb', 'secondary_color' => '#0f172a',
                'heading_font' => 'Inter Tight', 'body_font' => 'Inter',
                'button_style' => 'rounded', 'border_radius' => 'lg',
                'sections' => ['hero', 'clients', 'about', 'services', 'stats', 'products', 'projects', 'team', 'testimonials', 'gallery', 'cta', 'contact'],
            ],
        ],

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
