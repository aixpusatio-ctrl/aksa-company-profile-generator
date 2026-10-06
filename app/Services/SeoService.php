<?php

namespace App\Services;

use App\Models\CompanyPage;
use App\Models\CompanyProfile;
use App\Support\Website\SiteContext;
use Illuminate\Support\Str;

class SeoService
{
    public function meta(CompanyProfile $company, SiteContext $site, ?CompanyPage $page = null): array
    {
        $siteTitle = $company->seo_title ?: trim($company->name.($company->tagline ? ' — '.$company->tagline : ''));
        $description = $company->seo_description
            ?: Str::limit(strip_tags((string) ($company->description ?: $company->about)), 160);

        if ($page) {
            $title = ($page->seo_title ?: $page->title).' | '.$company->name;
            $description = $page->seo_description ?: Str::limit(strip_tags((string) $page->content), 160) ?: $description;
            $canonical = $site->isLive() ? $this->canonicalBase($company).'/'.$page->slug : null;
            $image = $page->url('featured_image') ?: $company->url('og_image');
        } else {
            $title = $siteTitle;
            $canonical = $site->isLive() ? $this->canonicalBase($company).'/' : null;
            $image = $company->url('og_image') ?: $company->url('hero_image');
        }

        return [
            'title' => $title,
            'description' => $description,
            'keywords' => $company->seo_keywords,
            'canonical' => $canonical,
            'og_title' => $page ? $title : ($company->og_title ?: $title),
            'og_description' => $page ? $description : ($company->og_description ?: $description),
            'og_image' => $image,
            'og_type' => $page ? 'article' : 'website',
            'site_name' => $company->name,
            'favicon' => $company->url('favicon') ?: $company->url('logo'),
            'robots' => $site->isLive() ? 'index,follow' : 'noindex,nofollow',
        ];
    }

    /**
     * Canonical host: primary custom domain when active, else the sub domain.
     */
    public function canonicalBase(CompanyProfile $company): string
    {
        return rtrim($company->publicUrl(), '/');
    }

    public function sitemap(CompanyProfile $company): string
    {
        $base = $this->canonicalBase($company);
        $urls = collect([['loc' => $base.'/', 'lastmod' => $company->updated_at, 'priority' => '1.0']]);

        $company->pages()->published()->get()->each(function (CompanyPage $page) use ($urls, $base) {
            $urls->push(['loc' => $base.'/'.$page->slug, 'lastmod' => $page->updated_at, 'priority' => '0.8']);
        });

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
        foreach ($urls as $url) {
            $xml .= '  <url><loc>'.e($url['loc']).'</loc>'
                .($url['lastmod'] ? '<lastmod>'.$url['lastmod']->toAtomString().'</lastmod>' : '')
                .'<priority>'.$url['priority'].'</priority></url>'."\n";
        }

        return $xml.'</urlset>'."\n";
    }

    public function robots(CompanyProfile $company): string
    {
        return "User-agent: *\nAllow: /\n\nSitemap: ".$this->canonicalBase($company)."/sitemap.xml\n";
    }
}
