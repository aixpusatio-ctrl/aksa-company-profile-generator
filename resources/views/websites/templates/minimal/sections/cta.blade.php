{{-- Minimal CTA: an oversized typographic link. --}}
<section id="cta" class="mx-auto max-w-4xl px-6">
    <div class="border-t border-neutral-200 py-20 md:py-32">
        <p class="text-sm text-neutral-400">{{ $section->subtitle ?: 'Punya proyek atau pertanyaan?' }}</p>
        <a href="{{ $site->anchor('contact') }}" class="group mt-6 block font-heading text-5xl leading-[1.02] font-semibold tracking-tighter text-neutral-950 md:text-7xl">
            <span class="bg-[linear-gradient(currentColor,currentColor)] bg-[length:0%_3px] bg-left-bottom bg-no-repeat pb-1 transition-[background-size] duration-500 group-hover:bg-[length:100%_3px]">{{ $section->title ?: 'Mari bicara' }}</span>
            <x-icon name="arrow-right" class="inline size-10 align-middle transition group-hover:translate-x-2 md:size-14" />
        </a>
    </div>
</section>
