{{-- Radio grid of templates with live preview links. Field name: template --}}
<div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
    @foreach ($templates as $template)
        <label class="group relative cursor-pointer">
            <input type="radio" name="template" value="{{ $template->slug }}" class="peer sr-only" @checked(old('template', $selected) === $template->slug) required>
            <div class="overflow-hidden rounded-2xl border-2 border-transparent bg-white shadow-sm ring-1 ring-slate-200 transition peer-checked:border-brand-500 peer-checked:ring-4 peer-checked:ring-brand-500/15 hover:shadow-lg">
                <x-template-thumb :template="$template" />
                <div class="flex items-center justify-between gap-2 p-4">
                    <div class="min-w-0">
                        <p class="truncate font-semibold text-slate-900">{{ $template->name }}</p>
                        <p class="text-xs text-slate-500">{{ $template->category?->name }}</p>
                    </div>
                    <a href="{{ route('templates.show', $template) }}" target="_blank" class="btn btn-ghost btn-sm" @click.stop>Preview <x-icon name="external" class="size-3.5" /></a>
                </div>
            </div>
            <span class="absolute top-3 right-3 hidden size-7 items-center justify-center rounded-full bg-brand-600 text-white shadow-lg peer-checked:inline-flex"><x-icon name="check" class="size-4" /></span>
        </label>
    @endforeach
</div>
