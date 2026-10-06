<?php

namespace Tests\Unit;

use App\Support\Website\SiteContext;
use PHPUnit\Framework\TestCase;

class SiteContextTest extends TestCase
{
    public function test_live_context_builds_absolute_urls(): void
    {
        $site = SiteContext::live('http://acme.localhost/');

        $this->assertTrue($site->isLive());
        $this->assertFalse($site->isPreview());
        $this->assertFalse($site->isTemplatePreview());
        $this->assertSame('http://acme.localhost/', $site->home());
        $this->assertSame('http://acme.localhost/karir', $site->page('karir'));
        $this->assertSame('#services', $site->anchor('services'));
        $this->assertSame('http://acme.localhost/assets/x.png', $site->url('/assets/x.png'));
        $this->assertSame('http://acme.localhost/contact', $site->contactAction());
        $this->assertTrue($site->canSubmitForms());
    }

    public function test_live_context_on_a_sub_page_links_anchors_to_home(): void
    {
        $site = SiteContext::live('https://www.company.test', false, 'karir');

        $this->assertSame('https://www.company.test/#about', $site->anchor('about'));
        $this->assertTrue($site->isCurrentPage('karir'));
        $this->assertFalse($site->isCurrentPage('kontak'));
    }

    public function test_preview_context_uses_query_string_pages_and_disables_forms(): void
    {
        $base = 'http://localhost/dashboard/websites/5/preview/frame';
        $site = SiteContext::preview($base);

        $this->assertTrue($site->isPreview());
        $this->assertFalse($site->isLive());
        $this->assertFalse($site->isTemplatePreview());
        $this->assertSame($base, $site->home());
        $this->assertSame($base.'?page=karir', $site->page('karir'));
        $this->assertSame($base.'?page=a+b%26c', $site->page('a b&c'));
        $this->assertSame('#services', $site->anchor('services'));
        $this->assertSame($base, $site->url('/anything'));
        $this->assertSame('#contact', $site->contactAction());
        $this->assertFalse($site->canSubmitForms());

        $onPage = SiteContext::preview($base, false, 'karir');
        $this->assertSame($base.'#services', $onPage->anchor('services'));
    }

    public function test_preview_url_with_existing_query_string(): void
    {
        $site = SiteContext::preview('http://localhost/frame?token=1');

        $this->assertSame('http://localhost/frame?token=1&page=karir', $site->page('karir'));
    }

    public function test_template_context(): void
    {
        $base = 'http://localhost/templates/corporate-blue/render';
        $site = SiteContext::template($base);

        $this->assertTrue($site->isTemplatePreview());
        $this->assertTrue($site->isPreview());
        $this->assertFalse($site->isLive());
        $this->assertSame($base.'?page=tentang', $site->page('tentang'));
        $this->assertFalse($site->canSubmitForms());
        $this->assertSame(SiteContext::MODE_TEMPLATE, $site->mode);
    }
}
