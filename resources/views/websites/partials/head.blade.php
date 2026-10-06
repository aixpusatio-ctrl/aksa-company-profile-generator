{{--
    <head> contents shared by every website template:
    SEO meta, canonical URL, Open Graph, Twitter Card, favicon, fonts,
    compiled site assets and the company's brand CSS variables.
--}}
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $seo['title'] }}</title>
<meta name="description" content="{{ $seo['description'] }}">
@if ($seo['keywords'])
    <meta name="keywords" content="{{ $seo['keywords'] }}">
@endif
<meta name="robots" content="{{ $seo['robots'] }}">
@if ($seo['canonical'])
    <link rel="canonical" href="{{ $seo['canonical'] }}">
@endif

<meta property="og:type" content="{{ $seo['og_type'] }}">
<meta property="og:site_name" content="{{ $seo['site_name'] }}">
<meta property="og:title" content="{{ $seo['og_title'] }}">
<meta property="og:description" content="{{ $seo['og_description'] }}">
@if ($seo['canonical'])
    <meta property="og:url" content="{{ $seo['canonical'] }}">
@endif
@if ($seo['og_image'])
    <meta property="og:image" content="{{ $seo['og_image'] }}">
@endif
<meta name="twitter:card" content="{{ $seo['og_image'] ? 'summary_large_image' : 'summary' }}">
<meta name="twitter:title" content="{{ $seo['og_title'] }}">
<meta name="twitter:description" content="{{ $seo['og_description'] }}">
@if ($seo['og_image'])
    <meta name="twitter:image" content="{{ $seo['og_image'] }}">
@endif

@if ($seo['favicon'])
    <link rel="icon" href="{{ $seo['favicon'] }}">
@endif
<meta name="theme-color" content="{{ $brand['primary_color'] ?? '#1d4ed8' }}">

@if ($fontsUrl)
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="{{ $fontsUrl }}">
@endif

@vite(['resources/css/site.css', 'resources/js/site.js'])

{{-- Values are validated (hex colors, whitelisted fonts & radii) before reaching this point. --}}
<style>:root{ {!! $brandCss !!} }</style>

<script type="application/ld+json">{!! json_encode(array_filter([
    '@context' => 'https://schema.org',
    '@type' => 'Organization',
    'name' => $company->name,
    'description' => $seo['description'],
    'url' => $seo['canonical'],
    'logo' => $company->url('logo'),
    'email' => $company->email,
    'telephone' => $company->phone,
    'foundingDate' => $company->established_year ? (string) $company->established_year : null,
    'address' => $company->fullAddress() ? ['@type' => 'PostalAddress', 'streetAddress' => $company->address, 'addressLocality' => $company->city, 'addressRegion' => $company->province, 'postalCode' => $company->postal_code, 'addressCountry' => $company->country] : null,
    'sameAs' => array_values($company->socialLinks()) ?: null,
]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
