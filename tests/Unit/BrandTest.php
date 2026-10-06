<?php

namespace Tests\Unit;

use App\Support\Website\Brand;
use Tests\TestCase;

class BrandTest extends TestCase
{
    private function vars(array $brand): array
    {
        return collect(explode(';', Brand::cssVariables($brand)))
            ->mapWithKeys(fn ($pair) => [strstr($pair, ':', true) => substr(strstr($pair, ':'), 1)])
            ->all();
    }

    public function test_css_variables_with_defaults(): void
    {
        $vars = $this->vars([]);

        $this->assertSame('#1d4ed8', $vars['--brand-primary']);
        $this->assertSame('#0f172a', $vars['--brand-secondary']);
        $this->assertSame('#ffffff', $vars['--brand-on-primary']);
        $this->assertSame('8px', $vars['--brand-radius']);
        $this->assertSame('8px', $vars['--brand-btn-radius']);
        $this->assertStringStartsWith("'Inter'", $vars['--brand-font-heading']);
        $this->assertCount(8, $vars);
    }

    public function test_css_variables_with_custom_brand(): void
    {
        $vars = $this->vars([
            'primary_color' => '#FACC15',
            'secondary_color' => '#111827',
            'heading_font' => 'Playfair Display',
            'body_font' => 'Inter',
            'button_style' => 'pill',
            'border_radius' => 'lg',
        ]);

        $this->assertSame('#facc15', $vars['--brand-primary']);
        $this->assertSame('#111111', $vars['--brand-on-primary'], 'Dark text on a yellow button.');
        $this->assertSame('#ffffff', $vars['--brand-on-secondary']);
        $this->assertSame('14px', $vars['--brand-radius']);
        $this->assertSame('9999px', $vars['--brand-btn-radius']);
        $this->assertSame("'Playfair Display',Georgia,serif", $vars['--brand-font-heading']);
    }

    public function test_button_radius_variants(): void
    {
        $this->assertSame('0px', $this->vars(['button_style' => 'square', 'border_radius' => 'xl'])['--brand-btn-radius']);
        $this->assertSame('6px', $this->vars(['button_style' => 'rounded', 'border_radius' => 'none'])['--brand-btn-radius']);
        $this->assertSame('0px', $this->vars(['border_radius' => 'none'])['--brand-radius']);
    }

    public function test_invalid_values_cannot_inject_css(): void
    {
        $css = Brand::cssVariables([
            'primary_color' => 'red;}body{display:none',
            'secondary_color' => 'url(javascript:alert(1))',
            'heading_font' => "Inter';}</style><script>alert(1)</script>",
            'border_radius' => '1px;}',
        ]);

        $this->assertStringNotContainsString('display:none', $css);
        $this->assertStringNotContainsString('javascript', $css);
        $this->assertStringNotContainsString('<script', $css);
        $this->assertStringNotContainsString('</style', $css);
        $this->assertStringContainsString('--brand-primary:#1d4ed8', $css);
        $this->assertStringContainsString('--brand-secondary:#1d4ed8', $css);
        $this->assertStringContainsString("--brand-font-heading:'Inter'", $css);
        $this->assertStringContainsString('--brand-radius:8px', $css);
    }

    public function test_color_normalization(): void
    {
        $this->assertSame('#abcdef', Brand::color('#ABCDEF'));
        $this->assertSame('#1d4ed8', Brand::color('#abc'));
        $this->assertSame('#1d4ed8', Brand::color(null));
        $this->assertSame('#1d4ed8', Brand::color('blue'));
    }

    public function test_contrast_picks_readable_text_color(): void
    {
        $this->assertSame('#111111', Brand::contrast('#ffffff'));
        $this->assertSame('#111111', Brand::contrast('#f59e0b'));
        $this->assertSame('#111111', Brand::contrast('#22d3ee'));
        $this->assertSame('#ffffff', Brand::contrast('#000000'));
        $this->assertSame('#ffffff', Brand::contrast('#1d4ed8'));
        $this->assertSame('#ffffff', Brand::contrast('#14532d'));
    }

    public function test_fonts_url_only_includes_whitelisted_fonts(): void
    {
        $this->assertNull(Brand::fontsUrl([]));
        $this->assertNull(Brand::fontsUrl(['heading_font' => 'Evil Font', 'body_font' => '"><script>']));

        $url = Brand::fontsUrl(['heading_font' => 'Plus Jakarta Sans', 'body_font' => 'Plus Jakarta Sans']);
        $this->assertStringStartsWith('https://fonts.googleapis.com/css2?', $url);
        $this->assertSame(1, substr_count($url, 'family='), 'Duplicate fonts are loaded once.');
        $this->assertStringContainsString('family=Plus+Jakarta+Sans', $url);
        $this->assertStringEndsWith('&display=swap', $url);

        $two = Brand::fontsUrl(['heading_font' => 'Oswald', 'body_font' => 'Inter']);
        $this->assertSame(2, substr_count($two, 'family='));
    }
}
