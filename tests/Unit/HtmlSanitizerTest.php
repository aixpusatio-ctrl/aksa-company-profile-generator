<?php

namespace Tests\Unit;

use App\Support\HtmlSanitizer;
use Tests\TestCase;

class HtmlSanitizerTest extends TestCase
{
    public function test_blank_input_returns_null(): void
    {
        $this->assertNull(HtmlSanitizer::clean(null));
        $this->assertNull(HtmlSanitizer::clean(''));
        $this->assertNull(HtmlSanitizer::clean('   '));
    }

    public function test_script_tags_are_removed(): void
    {
        $clean = HtmlSanitizer::clean('<p>Halo</p><script>alert("xss")</script>');

        $this->assertSame('<p>Halo</p>', $clean);
    }

    public function test_only_script_content_results_in_null(): void
    {
        $this->assertNull(HtmlSanitizer::clean('<script>alert(1)</script>'));
    }

    public function test_event_handler_attributes_are_removed(): void
    {
        $clean = HtmlSanitizer::clean('<p onclick="steal()" onmouseover="x()">Teks</p><img src="https://img.test/a.png" onerror="alert(1)" alt="a">');

        $this->assertStringNotContainsString('onclick', $clean);
        $this->assertStringNotContainsString('onmouseover', $clean);
        $this->assertStringNotContainsString('onerror', $clean);
        $this->assertStringContainsString('<p>Teks</p>', $clean);
        $this->assertStringContainsString('src="https://img.test/a.png"', $clean);
    }

    public function test_javascript_links_are_removed(): void
    {
        $clean = HtmlSanitizer::clean('<a href="javascript:alert(1)">klik</a> <a href="JaVaScRiPt:alert(2)">lagi</a> <a href="data:text/html;base64,PHNjcmlwdD4=">data</a>');

        $this->assertStringNotContainsStringIgnoringCase('javascript:', $clean);
        $this->assertStringNotContainsString('data:text/html', $clean);
        $this->assertStringContainsString('klik', $clean);
    }

    public function test_dangerous_elements_are_removed(): void
    {
        $clean = HtmlSanitizer::clean('<p>A</p><iframe src="https://evil.test"></iframe><object data="x"></object><style>body{display:none}</style><form action="/x"><input name="a"></form>');

        foreach (['<iframe', '<object', '<style', '<form', '<input', 'display:none'] as $needle) {
            $this->assertStringNotContainsString($needle, $clean);
        }
    }

    public function test_allowed_formatting_is_kept(): void
    {
        $html = '<h2>Judul</h2><p><strong>Tebal</strong> <em>miring</em> <u>garis</u></p><ul><li>Satu</li></ul><ol><li>Dua</li></ol><blockquote>Kutipan</blockquote><a href="https://example.test" title="t">tautan</a> <a href="mailto:a@example.test">email</a> <a href="tel:+62812">telp</a>';
        $clean = HtmlSanitizer::clean($html);

        foreach (['<h2>Judul</h2>', '<strong>Tebal</strong>', '<em>miring</em>', '<u>garis</u>', '<li>Satu</li>', '<ol>', '<blockquote>Kutipan</blockquote>', 'href="https://example.test"', 'href="mailto:a@example.test"', 'href="tel:+62812"'] as $needle) {
            $this->assertStringContainsString($needle, $clean);
        }
    }

    public function test_target_blank_links_get_noopener(): void
    {
        $clean = HtmlSanitizer::clean('<a href="https://example.test" target="_blank">x</a>');

        $this->assertStringContainsString('target="_blank"', $clean);
        $this->assertStringContainsString('noopener', $clean);
    }

    public function test_figures_from_the_editor_are_kept(): void
    {
        // Trix editor wraps attachments in <figure>/<figcaption>.
        $clean = HtmlSanitizer::clean('<figure><img src="https://img.test/a.png" alt="a"><figcaption>Keterangan</figcaption></figure>');

        $this->assertStringContainsString('<img', $clean);
        $this->assertStringContainsString('Keterangan', $clean);
    }

    public function test_sanitizer_can_be_called_repeatedly(): void
    {
        $this->assertSame('<p>Satu</p>', HtmlSanitizer::clean('<p>Satu</p>'));
        $this->assertSame('<p>Dua</p>', HtmlSanitizer::clean('<p>Dua</p><script>x</script>'));
    }
}
