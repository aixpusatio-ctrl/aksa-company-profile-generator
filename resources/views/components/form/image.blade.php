{{--
    Image upload field with preview, "choose from media library" and remove.
    Sends: {name} (file), {name}_media (picked library path), {name}_remove (bool).
--}}
@props(['name', 'label' => null, 'value' => null, 'help' => null, 'aspect' => 'aspect-video', 'required' => false])
<div {{ $attributes->only('class') }} x-data="imageField(@js($value))">
    @if ($label)<label class="form-label">{{ $label }} @if ($required)<span class="text-rose-500">*</span>@endif</label>@endif
    <div class="flex items-start gap-4">
        <div class="relative w-36 shrink-0 overflow-hidden rounded-lg border border-slate-200 bg-slate-50 {{ $aspect }}">
            <template x-if="preview"><img :src="preview" alt="" class="absolute inset-0 size-full object-cover"></template>
            <div x-show="!preview" class="absolute inset-0 flex items-center justify-center text-slate-300"><x-icon name="photo" class="size-8" /></div>
        </div>
        <div class="min-w-0 flex-1 space-y-2">
            <input x-ref="file" type="file" name="{{ $name }}" accept="image/png,image/jpeg,image/webp,image/gif" @change="onFile" class="block w-full text-xs text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-brand-700 hover:file:bg-brand-100">
            <input type="hidden" name="{{ $name }}_media" :value="picked">
            <input type="hidden" name="{{ $name }}_remove" :value="removed ? 1 : 0">
            <div class="flex flex-wrap gap-2">
                <button type="button" class="btn btn-secondary btn-sm" @click="$dispatch('open-media-picker', { pick: (item) => pick(item) })"><x-icon name="folder" class="size-3.5" /> Media Library</button>
                <button type="button" class="btn btn-ghost btn-sm text-rose-600" x-show="preview" @click="remove()">Hapus</button>
            </div>
            <p class="form-help">{{ $help ?? 'JPG, PNG, WEBP atau GIF. Maks '.round(setting('max_upload_kb', 4096) / 1024, 1).' MB.' }}</p>
            @error($name)<p class="form-error">{{ $message }}</p>@enderror
        </div>
    </div>
</div>
