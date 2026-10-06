<?php

namespace App\Services;

use App\Models\CompanyProfile;
use App\Models\Media;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class MediaService
{
    public function disk(): string
    {
        return (string) setting('media_disk', config('platform.media.disk'));
    }

    /**
     * Validation rule for an uploaded image (type, MIME, size, dimensions).
     */
    public static function imageRule(?int $maxKb = null): File
    {
        $max = config('platform.media.max_image_dimension');

        return File::image()
            ->types(config('platform.media.image_mimes'))
            ->max($maxKb ?? (int) setting('max_upload_kb', config('platform.media.max_image_kb')))
            ->dimensions(Rule::dimensions()->maxWidth($max)->maxHeight($max)->minWidth(16)->minHeight(16));
    }

    public static function documentRule(): File
    {
        return File::types(config('platform.media.document_mimes'))
            ->max(config('platform.media.max_document_kb'));
    }

    /**
     * Store an uploaded file in the user's media library.
     */
    public function upload(UploadedFile $file, User $user, ?CompanyProfile $company = null, string $collection = 'images'): Media
    {
        $collection = in_array($collection, config('platform.media.collections'), true) ? $collection : 'images';
        $extension = strtolower($file->guessExtension() ?: $file->getClientOriginalExtension());
        $directory = 'media/'.$user->getKey().'/'.now()->format('Y/m');
        $name = Str::random(32).'.'.$extension;

        $path = $file->storeAs($directory, $name, ['disk' => $this->disk(), 'visibility' => 'public']);

        $width = $height = null;
        if (Str::startsWith((string) $file->getMimeType(), 'image/') && ($size = @getimagesize($file->getRealPath()))) {
            [$width, $height] = $size;
        }

        return Media::query()->create([
            'user_id' => $user->getKey(),
            'company_profile_id' => $company?->getKey(),
            'collection' => $collection,
            'disk' => $this->disk(),
            'path' => $path,
            'filename' => Str::limit(basename($file->getClientOriginalName()), 200, ''),
            'mime_type' => (string) $file->getMimeType(),
            'size' => (int) $file->getSize(),
            'width' => $width,
            'height' => $height,
        ]);
    }

    /**
     * Resolve the stored path for an image field from a request: a fresh
     * upload wins, then a path picked from the media library (must belong to
     * the user), otherwise the current value is kept (or removed).
     */
    public function resolveImageInput(array $input, string $field, User $user, ?CompanyProfile $company, ?string $current, string $collection = 'images'): ?string
    {
        $upload = $input[$field] ?? null;

        if ($upload instanceof UploadedFile) {
            return $this->upload($upload, $user, $company, $collection)->path;
        }

        $picked = $input[$field.'_media'] ?? null;
        if (filled($picked)) {
            $owned = Media::query()
                ->where('path', $picked)
                ->when(! $user->isAdmin(), fn ($q) => $q->where('user_id', $user->getKey()))
                ->exists();

            if ($owned) {
                return $picked;
            }
        }

        if (! empty($input[$field.'_remove'])) {
            return null;
        }

        return $current;
    }

    public function delete(Media $media): void
    {
        Storage::disk($media->disk)->delete($media->path);
        $media->delete();
    }
}
