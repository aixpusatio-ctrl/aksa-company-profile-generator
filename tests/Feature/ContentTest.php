<?php

namespace Tests\Feature;

use App\Models\CompanyGalleryItem;
use App\Models\CompanyProduct;
use App\Models\CompanyProfile;
use App\Models\CompanyProject;
use App\Models\CompanyService;
use App\Models\CompanyTeamMember;
use App\Models\CompanyTestimonial;
use App\Models\Media;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ContentTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private CompanyProfile $company;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->user = $this->userWithPlan();
        $this->company = $this->companyFor($this->user);
    }

    /**
     * type => [model, valid payload, title column, updated payload]
     */
    public static function contentTypes(): array
    {
        return [
            'services' => ['services', CompanyService::class, ['title' => 'Konsultasi IT', 'icon' => 'cog', 'description' => 'Layanan konsultasi'], 'title', ['title' => 'Konsultasi Cloud']],
            'products' => ['products', CompanyProduct::class, ['name' => 'Produk A', 'category' => 'Alat', 'price' => '150000'], 'name', ['name' => 'Produk B']],
            'projects' => ['projects', CompanyProject::class, ['title' => 'Gedung X', 'client' => 'PT Klien', 'year' => 2023, 'url' => 'https://proyek.test'], 'title', ['title' => 'Gedung Y']],
            'team' => ['team', CompanyTeamMember::class, ['name' => 'Budi', 'position' => 'CEO', 'email' => 'budi@example.test', 'linkedin' => 'https://linkedin.com/in/budi'], 'name', ['name' => 'Budi S.']],
            'testimonials' => ['testimonials', CompanyTestimonial::class, ['customer_name' => 'Ani', 'company' => 'PT Ani', 'rating' => 5, 'testimonial' => 'Sangat puas'], 'customer_name', ['customer_name' => 'Ani W.', 'rating' => 4, 'testimonial' => 'Puas']],
            'gallery' => ['gallery', CompanyGalleryItem::class, ['title' => 'Kantor'], 'title', ['title' => 'Kantor Baru']],
        ];
    }

    private function url(string $type, string $suffix = ''): string
    {
        return "/dashboard/websites/{$this->company->id}/content/{$type}{$suffix}";
    }

    private function image(string $name = 'photo.jpg'): UploadedFile
    {
        return UploadedFile::fake()->image($name, 640, 480);
    }

    #[DataProvider('contentTypes')]
    public function test_content_items_can_be_created_updated_and_deleted(string $type, string $model, array $payload, string $titleField, array $update): void
    {
        if ($type === 'gallery') {
            $payload['image'] = $this->image();
        }

        $this->actingAs($this->user)->post($this->url($type), $payload)
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $item = $model::query()->where('company_profile_id', $this->company->id)->firstOrFail();
        $this->assertSame($payload[$titleField], $item->{$titleField});
        $this->assertSame(1, (int) $item->sort_order);

        $this->actingAs($this->user)->put($this->url($type, '/'.$item->id), $update)
            ->assertRedirect()
            ->assertSessionHasNoErrors();
        $this->assertSame($update[$titleField], $item->fresh()->{$titleField});

        if ($type === 'gallery') {
            $this->assertNotNull($item->fresh()->image, 'Gallery image is kept when no new file is uploaded.');
        }

        $this->actingAs($this->user)->delete($this->url($type, '/'.$item->id))->assertRedirect();
        $this->assertModelMissing($item);
    }

    #[DataProvider('contentTypes')]
    public function test_content_items_can_be_reordered(string $type, string $model, array $payload, string $titleField): void
    {
        $ids = [];
        foreach (['A', 'B', 'C'] as $suffix) {
            $attributes = $payload;
            $attributes[$titleField] = ($payload[$titleField] ?? 'Item').' '.$suffix;
            if ($type === 'gallery') {
                $attributes['image'] = 'media/fake/'.$suffix.'.jpg';
            }
            unset($attributes['url']);
            $ids[] = $this->company->{$type}()->create(array_intersect_key($attributes, array_flip((new $model)->getFillable())))->id;
        }

        $this->actingAs($this->user)
            ->postJson($this->url($type, '/reorder'), ['ids' => array_reverse($ids)])
            ->assertOk()
            ->assertJson(['saved' => true]);

        $this->assertSame(array_reverse($ids), $this->company->{$type}()->pluck('id')->all());
    }

    public function test_reorder_requires_an_array_of_ids(): void
    {
        $this->actingAs($this->user)
            ->postJson($this->url('services', '/reorder'), ['ids' => 'nope'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('ids');
    }

    public function test_content_validation_errors(): void
    {
        $this->actingAs($this->user)->post($this->url('services'), [])->assertSessionHasErrors('title');
        $this->actingAs($this->user)->post($this->url('products'), ['name' => 'X', 'price' => -5])->assertSessionHasErrors('price');
        $this->actingAs($this->user)->post($this->url('projects'), ['title' => 'X', 'url' => 'javascript:alert(1)', 'year' => 1800])
            ->assertSessionHasErrors(['url', 'year']);
        $this->actingAs($this->user)->post($this->url('team'), ['name' => 'X', 'email' => 'nope'])->assertSessionHasErrors('email');
        $this->actingAs($this->user)->post($this->url('testimonials'), ['customer_name' => 'X', 'rating' => 6])
            ->assertSessionHasErrors(['rating', 'testimonial']);
        $this->actingAs($this->user)->post($this->url('gallery'), ['title' => 'Tanpa gambar'])->assertSessionHasErrors('image');

        $this->assertSame(0, CompanyService::query()->count() + CompanyGalleryItem::query()->count());
    }

    public function test_image_upload_creates_media_record_and_stores_file(): void
    {
        $this->actingAs($this->user)->post($this->url('services'), [
            'title' => 'Dengan gambar',
            'image' => $this->image('service.jpg'),
        ])->assertSessionHasNoErrors();

        $service = CompanyService::query()->firstOrFail();
        $this->assertNotNull($service->image);
        Storage::disk('public')->assertExists($service->image);

        $media = Media::query()->where('path', $service->image)->firstOrFail();
        $this->assertSame($this->user->id, $media->user_id);
        $this->assertSame($this->company->id, $media->company_profile_id);
        $this->assertSame('images', $media->collection);
        $this->assertSame('service.jpg', $media->filename);
        $this->assertSame(640, (int) $media->width);
        $this->assertSame(480, (int) $media->height);
        $this->assertStringStartsWith('image/', $media->mime_type);
        $this->assertNotNull($service->url('image'));
    }

    public function test_team_photo_and_gallery_images_use_their_collections(): void
    {
        $this->actingAs($this->user)->post($this->url('team'), ['name' => 'Rina', 'photo' => $this->image()])->assertSessionHasNoErrors();
        $this->actingAs($this->user)->post($this->url('gallery'), ['image' => $this->image()])->assertSessionHasNoErrors();

        $team = CompanyTeamMember::query()->firstOrFail();
        $gallery = CompanyGalleryItem::query()->firstOrFail();

        $this->assertSame('images', Media::query()->where('path', $team->photo)->value('collection'));
        $this->assertSame('gallery', Media::query()->where('path', $gallery->image)->value('collection'));
    }

    public function test_image_can_be_removed_from_an_item(): void
    {
        $service = $this->company->services()->create(['title' => 'X', 'image' => 'media/old.jpg']);

        $this->actingAs($this->user)->put($this->url('services', '/'.$service->id), ['title' => 'X', 'image_remove' => '1'])
            ->assertSessionHasNoErrors();

        $this->assertNull($service->fresh()->image);
    }

    public function test_php_file_disguised_as_image_is_rejected(): void
    {
        $file = UploadedFile::fake()->createWithContent('shell.php', '<?php system($_GET["c"]); ?>');

        $this->actingAs($this->user)->post($this->url('services'), ['title' => 'Evil', 'image' => $file])
            ->assertSessionHasErrors('image');

        $this->assertSame(0, CompanyService::query()->count());
        $this->assertSame(0, Media::query()->count());
        $this->assertEmpty(Storage::disk('public')->allFiles());
    }

    public function test_non_image_files_are_rejected(): void
    {
        $pdf = UploadedFile::fake()->create('brochure.pdf', 100, 'application/pdf');
        $svg = UploadedFile::fake()->createWithContent('logo.svg', '<svg xmlns="http://www.w3.org/2000/svg" onload="alert(1)"></svg>');
        $jpgNamedPhp = UploadedFile::fake()->image('photo.php');

        foreach ([$pdf, $svg, $jpgNamedPhp] as $file) {
            $this->actingAs($this->user)->post($this->url('services'), ['title' => 'Evil', 'image' => $file])
                ->assertSessionHasErrors('image');
        }

        $this->assertSame(0, Media::query()->count());
    }

    public function test_oversized_or_too_small_images_are_rejected(): void
    {
        $tooBig = UploadedFile::fake()->image('big.jpg', 100, 100)->size(5000);
        $tooSmall = UploadedFile::fake()->image('tiny.jpg', 8, 8);

        $this->actingAs($this->user)->post($this->url('services'), ['title' => 'Big', 'image' => $tooBig])->assertSessionHasErrors('image');
        $this->actingAs($this->user)->post($this->url('services'), ['title' => 'Tiny', 'image' => $tooSmall])->assertSessionHasErrors('image');
    }

    public function test_unknown_content_type_returns_404(): void
    {
        $this->actingAs($this->user)->get($this->url('weapons'))->assertNotFound();
        $this->actingAs($this->user)->post($this->url('weapons'), ['title' => 'X'])->assertNotFound();
        $this->actingAs($this->user)->postJson($this->url('weapons', '/reorder'), ['ids' => [1]])->assertNotFound();
        $this->actingAs($this->user)->put($this->url('weapons', '/1'), ['title' => 'X'])->assertNotFound();
        $this->actingAs($this->user)->delete($this->url('weapons', '/1'))->assertNotFound();
    }

    public function test_unknown_item_id_returns_404(): void
    {
        $this->actingAs($this->user)->put($this->url('services', '/999999'), ['title' => 'X'])->assertNotFound();
        $this->actingAs($this->user)->delete($this->url('services', '/999999'))->assertNotFound();
    }

    public function test_media_library_upload_and_delete(): void
    {
        $this->actingAs($this->user)
            ->postJson('/dashboard/media', ['collection' => 'images', 'files' => [$this->image('a.jpg'), $this->image('b.png')]])
            ->assertOk()
            ->assertJsonCount(2, 'data');

        $media = Media::query()->where('user_id', $this->user->id)->get();
        $this->assertCount(2, $media);
        Storage::disk('public')->assertExists($media->first()->path);

        $this->actingAs($this->user)->deleteJson('/dashboard/media/'.$media->first()->id)->assertOk()->assertJson(['deleted' => true]);
        $this->assertModelMissing($media->first());
        Storage::disk('public')->assertMissing($media->first()->path);
    }

    public function test_media_library_documents_accept_pdf_but_images_do_not(): void
    {
        $this->actingAs($this->user)
            ->postJson('/dashboard/media', ['collection' => 'documents', 'files' => [UploadedFile::fake()->create('profil.pdf', 50, 'application/pdf')]])
            ->assertOk();

        $this->actingAs($this->user)
            ->postJson('/dashboard/media', ['collection' => 'images', 'files' => [UploadedFile::fake()->create('profil.pdf', 50, 'application/pdf')]])
            ->assertUnprocessable();

        $this->assertSame(1, Media::query()->count());
    }
}
