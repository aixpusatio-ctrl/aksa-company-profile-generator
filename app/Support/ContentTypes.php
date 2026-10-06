<?php

namespace App\Support;

use App\Models\CompanyGalleryItem;
use App\Models\CompanyProduct;
use App\Models\CompanyProject;
use App\Models\CompanyService;
use App\Models\CompanyTeamMember;
use App\Models\CompanyTestimonial;
use Illuminate\Support\Arr;

/**
 * Registry describing the repeatable content blocks of a company profile.
 * One generic controller + view drive the CRUD (add, edit, delete, reorder)
 * for all of them, based on these definitions.
 */
class ContentTypes
{
    public static function all(): array
    {
        return [
            'services' => [
                'model' => CompanyService::class,
                'relation' => 'services',
                'singular' => 'Service',
                'plural' => 'Services',
                'title_field' => 'title',
                'subtitle_field' => 'description',
                'image_field' => 'image',
                'description' => 'Layanan yang ditawarkan perusahaan Anda.',
                'fields' => [
                    'title' => ['label' => 'Judul layanan', 'type' => 'text', 'rules' => ['required', 'string', 'max:150']],
                    'icon' => ['label' => 'Ikon', 'type' => 'icon', 'rules' => ['nullable', 'string', 'max:50']],
                    'description' => ['label' => 'Deskripsi', 'type' => 'textarea', 'rules' => ['nullable', 'string', 'max:2000']],
                    'image' => ['label' => 'Gambar', 'type' => 'image', 'rules' => []],
                ],
            ],
            'products' => [
                'model' => CompanyProduct::class,
                'relation' => 'products',
                'singular' => 'Product',
                'plural' => 'Products',
                'title_field' => 'name',
                'subtitle_field' => 'category',
                'image_field' => 'image',
                'description' => 'Katalog produk yang ingin Anda tampilkan.',
                'fields' => [
                    'name' => ['label' => 'Nama produk', 'type' => 'text', 'rules' => ['required', 'string', 'max:150']],
                    'category' => ['label' => 'Kategori', 'type' => 'text', 'rules' => ['nullable', 'string', 'max:100']],
                    'price' => ['label' => 'Harga (opsional)', 'type' => 'number', 'rules' => ['nullable', 'numeric', 'min:0', 'max:999999999999']],
                    'description' => ['label' => 'Deskripsi', 'type' => 'textarea', 'rules' => ['nullable', 'string', 'max:2000']],
                    'image' => ['label' => 'Gambar', 'type' => 'image', 'rules' => []],
                ],
            ],
            'projects' => [
                'model' => CompanyProject::class,
                'relation' => 'projects',
                'singular' => 'Project',
                'plural' => 'Projects',
                'title_field' => 'title',
                'subtitle_field' => 'client',
                'image_field' => 'image',
                'description' => 'Portofolio proyek yang pernah dikerjakan.',
                'fields' => [
                    'title' => ['label' => 'Judul proyek', 'type' => 'text', 'rules' => ['required', 'string', 'max:150']],
                    'client' => ['label' => 'Klien', 'type' => 'text', 'rules' => ['nullable', 'string', 'max:150']],
                    'location' => ['label' => 'Lokasi', 'type' => 'text', 'rules' => ['nullable', 'string', 'max:150']],
                    'year' => ['label' => 'Tahun', 'type' => 'number', 'rules' => ['nullable', 'integer', 'min:1900', 'max:2100']],
                    'category' => ['label' => 'Kategori', 'type' => 'text', 'rules' => ['nullable', 'string', 'max:100']],
                    'url' => ['label' => 'URL', 'type' => 'url', 'rules' => ['nullable', 'url:http,https', 'max:255']],
                    'description' => ['label' => 'Deskripsi', 'type' => 'textarea', 'rules' => ['nullable', 'string', 'max:3000']],
                    'image' => ['label' => 'Gambar', 'type' => 'image', 'rules' => []],
                ],
            ],
            'team' => [
                'model' => CompanyTeamMember::class,
                'relation' => 'team',
                'singular' => 'Team Member',
                'plural' => 'Team',
                'title_field' => 'name',
                'subtitle_field' => 'position',
                'image_field' => 'photo',
                'description' => 'Perkenalkan orang-orang di balik perusahaan.',
                'fields' => [
                    'name' => ['label' => 'Nama', 'type' => 'text', 'rules' => ['required', 'string', 'max:150']],
                    'position' => ['label' => 'Jabatan', 'type' => 'text', 'rules' => ['nullable', 'string', 'max:150']],
                    'email' => ['label' => 'Email', 'type' => 'email', 'rules' => ['nullable', 'email', 'max:150']],
                    'linkedin' => ['label' => 'LinkedIn URL', 'type' => 'url', 'rules' => ['nullable', 'url:http,https', 'max:255']],
                    'bio' => ['label' => 'Bio singkat', 'type' => 'textarea', 'rules' => ['nullable', 'string', 'max:2000']],
                    'photo' => ['label' => 'Foto', 'type' => 'image', 'rules' => []],
                ],
            ],
            'testimonials' => [
                'model' => CompanyTestimonial::class,
                'relation' => 'testimonials',
                'singular' => 'Testimonial',
                'plural' => 'Testimonials',
                'title_field' => 'customer_name',
                'subtitle_field' => 'company',
                'image_field' => 'photo',
                'description' => 'Apa kata pelanggan tentang perusahaan Anda.',
                'fields' => [
                    'customer_name' => ['label' => 'Nama pelanggan', 'type' => 'text', 'rules' => ['required', 'string', 'max:150']],
                    'company' => ['label' => 'Perusahaan', 'type' => 'text', 'rules' => ['nullable', 'string', 'max:150']],
                    'rating' => ['label' => 'Rating', 'type' => 'rating', 'rules' => ['required', 'integer', 'min:1', 'max:5']],
                    'testimonial' => ['label' => 'Testimoni', 'type' => 'textarea', 'rules' => ['required', 'string', 'max:2000']],
                    'photo' => ['label' => 'Foto', 'type' => 'image', 'rules' => []],
                ],
            ],
            'gallery' => [
                'model' => CompanyGalleryItem::class,
                'relation' => 'gallery',
                'singular' => 'Gallery Image',
                'plural' => 'Gallery',
                'title_field' => 'title',
                'subtitle_field' => 'category',
                'image_field' => 'image',
                'image_required' => true,
                'description' => 'Foto kantor, kegiatan, fasilitas dan lainnya.',
                'fields' => [
                    'image' => ['label' => 'Gambar', 'type' => 'image', 'rules' => []],
                    'title' => ['label' => 'Judul', 'type' => 'text', 'rules' => ['nullable', 'string', 'max:150']],
                    'category' => ['label' => 'Kategori', 'type' => 'text', 'rules' => ['nullable', 'string', 'max:100']],
                    'description' => ['label' => 'Deskripsi', 'type' => 'textarea', 'rules' => ['nullable', 'string', 'max:1000']],
                ],
            ],
        ];
    }

    public static function keys(): array
    {
        return array_keys(self::all());
    }

    public static function get(string $type): array
    {
        $definition = self::all()[$type] ?? null;
        abort_if($definition === null, 404);

        return $definition + ['key' => $type];
    }

    /**
     * Validation rules for the non-image fields of a type.
     */
    public static function rules(string $type): array
    {
        return collect(self::get($type)['fields'])
            ->reject(fn ($field) => $field['type'] === 'image')
            ->map(fn ($field) => $field['rules'])
            ->all();
    }

    public static function imageField(string $type): ?string
    {
        return Arr::get(self::get($type), 'image_field');
    }

    public static function icons(): array
    {
        return [
            'briefcase', 'building', 'chart', 'code', 'cog', 'cube', 'globe', 'heart', 'light-bulb',
            'megaphone', 'phone', 'rocket', 'shield', 'sparkles', 'truck', 'users', 'wrench', 'academic',
            'camera', 'cpu', 'leaf', 'scale', 'banknotes', 'home', 'bolt', 'clipboard', 'paint',
        ];
    }
}
