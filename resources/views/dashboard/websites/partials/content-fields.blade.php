{{-- Fields of a content item form, generated from App\Support\ContentTypes. --}}
@php($isNew = ! $item?->exists)
<div class="grid gap-4 sm:grid-cols-2">
    @foreach ($type['fields'] as $field => $def)
        @php($value = $item?->{$field})
        @switch($def['type'])
            @case('textarea')
                <x-form.textarea :name="$field" :label="$def['label']" :value="$value" rows="3" class="sm:col-span-2" />
                @break
            @case('image')
                <x-form.image :name="$field" :label="$def['label']" :value="$item?->url($field)" :required="($type['image_required'] ?? false) && $isNew" class="sm:col-span-2" />
                @break
            @case('icon')
                <div x-data="{ icon: @js($value ?: 'briefcase') }">
                    <label class="form-label">{{ $def['label'] }}</label>
                    <input type="hidden" name="{{ $field }}" :value="icon">
                    <div class="flex max-h-32 flex-wrap gap-1.5 overflow-y-auto rounded-lg border border-slate-200 p-2">
                        @foreach (\App\Support\ContentTypes::icons() as $icon)
                            <button type="button" @click="icon = @js($icon)" class="inline-flex size-9 items-center justify-center rounded-lg" :class="icon === @js($icon) ? 'bg-brand-600 text-white' : 'text-slate-500 hover:bg-slate-100'" title="{{ $icon }}"><x-icon :name="$icon" class="size-5" /></button>
                        @endforeach
                    </div>
                </div>
                @break
            @case('rating')
                <x-form.select :name="$field" :label="$def['label']" :options="[5 => '★★★★★ (5)', 4 => '★★★★ (4)', 3 => '★★★ (3)', 2 => '★★ (2)', 1 => '★ (1)']" :value="$value ?? 5" />
                @break
            @default
                <x-form.input :name="$field" :label="$def['label']" :type="$def['type']" :value="$value" :required="in_array('required', $def['rules'])" step="{{ $def['type'] === 'number' ? 'any' : '' }}" />
        @endswitch
    @endforeach
</div>
