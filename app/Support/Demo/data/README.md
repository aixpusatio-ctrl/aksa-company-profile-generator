# Demo data files

One PHP file per composed template (`{template-slug}.php`), loaded by
`App\Support\DemoContent::for($slug)` for template previews and the seeder.
Each file returns a compact array; `DemoContent::fromData()` expands it.

```php
<?php

return [
    'prefix' => 'arunika-digital',      // unique seed prefix for picsum images
    'handle' => 'arunikadigital',       // social media handle
    'domain' => 'arunika.digital',      // used for team e-mails
    'company' => [
        'name' => 'Arunika Digital',
        'tagline' => 'Building Digital Infrastructure for Tomorrow',
        'description' => '2 sentences.',
        'established_year' => 2014,
        'phone' => '(022) 8765 4321',
        'email' => 'halo@arunika.digital',
        'whatsapp' => '081234567890',
        'address' => 'Jl. ...',
        'city' => 'Bandung', 'province' => 'Jawa Barat', 'postal_code' => '40115',
        'latitude' => -6.9147, 'longitude' => 107.6098,
        'working_hours' => 'Senin - Jumat, 09.00 - 18.00',
        'website' => 'https://arunika.digital',
        'social_links' => ['instagram', 'linkedin', 'youtube', 'x'],   // networks (URLs built from handle)
        'about' => ['paragraph 1', 'paragraph 2'],
        'vision' => 'One sentence.',
        'mission' => ['item', 'item', 'item', 'item'],
        'history' => ['paragraph 1', 'paragraph 2'],
        'company_values' => ['Integritas' => 'short explanation', '...' => '...'],
        'highlights' => [['250+', 'Klien enterprise'], ['12', 'Kota'], ['99,9%', 'Uptime'], ['2014', 'Tahun berdiri']],
        'seo_title' => '...', 'seo_description' => '...', 'seo_keywords' => 'a, b, c',
    ],
    'services' => [['Title', 'Description 1-2 sentences.', 'icon-name'], /* 6 */],
    'products' => [['Name', 'Description.', 1500000 /* or null */, 'Category'], /* 4-6 */],
    'projects' => [['Title', 'Description.', 'Client', 'Location', 2024, 'Category', null /* or https url */], /* 6 */],
    'team' => [['Full Name', 'Position', 'Bio sentence.', 'linkedin-handle', 'email-local'], /* 4 */],
    'testimonials' => [['Name', 'Company', 'Testimonial 2 sentences.', 5], /* 3 */],
    'gallery' => [['Title', 'Description.', 'Category'], /* 6 */],
    'roles' => ['Open position 1', 'Open position 2', 'Open position 3'],
    'page' => ['Page Title', 'page-slug', ['intro p1', 'intro p2'], 'H2 heading', ['item', 'item', 'item', 'item'], 'closing paragraph', 'SEO description'],
];
```

Icons must be names from `App\Support\ContentTypes::icons()`.
