<?php

$appHost = parse_url((string) env('APP_URL', 'http://localhost'), PHP_URL_HOST) ?: 'localhost';

return [

    /*
    |--------------------------------------------------------------------------
    | Platform Domain
    |--------------------------------------------------------------------------
    |
    | Every company profile is reachable at "{slug}.{domain}". In local
    | development "localhost" works out of the box because modern browsers
    | resolve *.localhost to 127.0.0.1. Use "platform.test" with Valet/Herd or
    | "companyprofile.com" in production (with a wildcard DNS record).
    |
    */

    'domain' => env('PLATFORM_DOMAIN', $appHost),

    /*
    | Hosts that serve the SaaS application itself (landing page, dashboard,
    | admin panel). Requests for any other host are treated as tenant sites.
    */

    'central_domains' => array_values(array_unique(array_filter(array_map('trim', array_merge(
        [$appHost, env('PLATFORM_DOMAIN', $appHost), 'www.'.env('PLATFORM_DOMAIN', $appHost)],
        explode(',', (string) env('CENTRAL_DOMAINS', 'localhost,127.0.0.1'))
    ))))),

    'scheme' => env('PLATFORM_SCHEME', parse_url((string) env('APP_URL', 'http://localhost'), PHP_URL_SCHEME) ?: 'http'),

    'port' => env('PLATFORM_PORT', parse_url((string) env('APP_URL', 'http://localhost'), PHP_URL_PORT)),

    /*
    | Sub-domains that can never be claimed by a tenant.
    */

    'reserved_subdomains' => [
        'www', 'app', 'admin', 'api', 'mail', 'smtp', 'ftp', 'cdn', 'static', 'assets',
        'dashboard', 'login', 'register', 'support', 'help', 'docs', 'blog', 'status',
        'cname', 'ns1', 'ns2', 'dev', 'staging', 'test', 'platform', 'billing',
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Domains
    |--------------------------------------------------------------------------
    */

    'custom_domains' => [
        // Target customers point their CNAME record to.
        'cname_target' => env('PLATFORM_CNAME_TARGET', 'cname.'.env('PLATFORM_DOMAIN', 'companyprofile.com')),

        // IP address customers point apex (root) domains to.
        'server_ip' => env('PLATFORM_SERVER_IP', '203.0.113.10'),

        // TXT record prefix used to prove ownership.
        'txt_prefix' => '_cpg-verify',

        // "fake" verifies without real DNS lookups (development/demo),
        // "dns" performs real DNS queries.
        'verifier' => env('DOMAIN_VERIFIER', 'fake'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Demo Mode
    |--------------------------------------------------------------------------
    |
    | Demo credentials are displayed on the login page only when enabled and
    | the application is not running in production.
    |
    */

    'show_demo_credentials' => (bool) env('SHOW_DEMO_CREDENTIALS', true),

    'demo_accounts' => [
        ['role' => 'Admin', 'email' => 'admin@example.com', 'password' => 'password'],
        ['role' => 'User', 'email' => 'user@example.com', 'password' => 'password'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Subscription Plans
    |--------------------------------------------------------------------------
    */

    'trial_days' => 14,

    'plans' => [
        'free' => [
            'name' => 'Starter',
            'price' => 0,
            'description' => 'Untuk mencoba dan bisnis yang baru mulai.',
            'max_websites' => 1,
            'custom_domain' => false,
            'features' => ['1 website', 'Subdomain gratis', 'Semua template', 'Contact form'],
        ],
        'pro' => [
            'name' => 'Professional',
            'price' => 149000,
            'description' => 'Untuk perusahaan yang ingin tampil profesional.',
            'max_websites' => 5,
            'custom_domain' => true,
            'features' => ['5 website', 'Custom domain', 'Halaman & menu tanpa batas', 'SEO lengkap', 'Analytics'],
        ],
        'business' => [
            'name' => 'Business',
            'price' => 399000,
            'description' => 'Untuk grup usaha & agensi dengan banyak brand.',
            'max_websites' => null,
            'custom_domain' => true,
            'features' => ['Website tanpa batas', 'Custom domain tanpa batas', 'Prioritas support', 'Semua fitur Pro'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Media
    |--------------------------------------------------------------------------
    */

    'media' => [
        'disk' => env('MEDIA_DISK', 'public'),
        'max_image_kb' => 4096,
        'max_document_kb' => 10240,
        'image_mimes' => ['jpg', 'jpeg', 'png', 'webp', 'gif'],
        'document_mimes' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'],
        'max_image_dimension' => 6000,
        'collections' => ['images', 'logos', 'gallery', 'documents'],
    ],

];
