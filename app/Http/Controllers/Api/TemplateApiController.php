<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CompanyProfile;
use App\Models\Template;
use App\Services\CompanyProfileService;
use App\Services\TemplateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TemplateApiController extends Controller
{
    public function index(TemplateService $templates): JsonResponse
    {
        return response()->json([
            'data' => $templates->published()->map(fn (Template $template) => [
                'name' => $template->name,
                'slug' => $template->slug,
                'description' => $template->description,
                'category' => $template->category?->name,
                'featured' => $template->is_featured,
                'preview_url' => route('templates.render', $template),
            ]),
        ]);
    }

    public function subdomainAvailability(Request $request): JsonResponse
    {
        $slug = Str::slug($request->string('slug')->toString());
        $valid = CompanyProfileService::isValidSlug($slug);
        $taken = $valid && CompanyProfile::query()->where('slug', $slug)->exists();

        return response()->json([
            'slug' => $slug,
            'valid' => $valid,
            'available' => $valid && ! $taken,
            'host' => $slug.'.'.config('platform.domain'),
        ]);
    }
}
