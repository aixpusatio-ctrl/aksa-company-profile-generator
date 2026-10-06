<x-website-layout :company="$company" :title="$page->exists ? 'Edit Page' : 'New Page'">
    <form method="POST" action="{{ $page->exists ? route('websites.pages.update', [$company, $page]) : route('websites.pages.store', $company) }}" enctype="multipart/form-data"
          x-data="{ title: @js(old('title', $page->title) ?? ''), slug: @js(old('slug', $page->slug) ?? ''), touched: {{ $page->exists ? 'true' : 'false' }},
                   slugify(v) { return v.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '').replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '') } }"
          class="grid gap-6 xl:grid-cols-[1fr_300px]">
        @csrf
        @if ($page->exists) @method('PUT') @endif
        <div class="card card-body space-y-5">
            <div>
                <label class="form-label" for="title">Judul halaman</label>
                <input id="title" name="title" x-model="title" @input="if (!touched) slug = slugify(title)" required maxlength="150" class="form-input text-lg font-semibold">
                @error('title')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label" for="slug">Slug / URL</label>
                <div class="flex"><span class="inline-flex items-center rounded-l-lg border border-r-0 border-slate-300 bg-slate-50 px-3 text-sm text-slate-500">{{ $company->primaryHost() }}/</span>
                <input id="slug" name="slug" x-model="slug" @input="touched = true" class="form-input rounded-l-none font-mono"></div>
                @error('slug')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <x-form.rich name="content" label="Konten" :value="$page->content" />
        </div>
        <div class="space-y-6">
            <div class="card card-body space-y-4">
                <x-form.select name="status" label="Status" :options="['draft' => 'Draft', 'published' => 'Published']" :value="$page->status" />
                @unless ($page->exists)
                    <label class="flex items-center gap-2 text-sm text-slate-600"><input type="checkbox" name="add_to_menu" value="1" class="form-checkbox" checked> Tambahkan ke menu navigasi</label>
                @endunless
                <button class="btn btn-primary w-full">{{ $page->exists ? 'Simpan perubahan' : 'Buat halaman' }}</button>
                @if ($page->exists)
                    <a href="{{ route('websites.preview.frame', [$company, 'page' => $page->slug]) }}" target="_blank" class="btn btn-secondary w-full">Preview</a>
                @endif
            </div>
            <div class="card card-body space-y-4">
                <x-form.image name="featured_image" label="Featured image" :value="$page->url('featured_image')" />
            </div>
            <div class="card card-body space-y-4">
                <p class="text-sm font-semibold text-slate-900">SEO</p>
                <x-form.input name="seo_title" label="SEO title" :value="$page->seo_title" />
                <x-form.textarea name="seo_description" label="SEO description" :value="$page->seo_description" rows="3" />
            </div>
        </div>
    </form>
</x-website-layout>
