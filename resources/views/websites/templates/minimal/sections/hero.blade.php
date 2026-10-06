{{-- Minimal hero: one huge statement and a short paragraph. Nothing else. --}}
<section id="hero" class="mx-auto max-w-4xl px-6 pt-20 pb-20 md:pt-36 md:pb-32">
    <p class="text-sm text-neutral-400">
        {{ $company->name }}@if ($company->city) — {{ $company->city }}@endif @if ($company->established_year) — Sejak {{ $company->established_year }}@endif
    </p>
    <h1 class="mt-8 font-heading text-[2.75rem] leading-[1.02] font-semibold tracking-tighter text-neutral-950 sm:text-6xl md:text-7xl lg:text-[5.5rem]">
        {{ $section->title ?: ($company->tagline ?: $company->name) }}
    </h1>
    <div class="mt-12 grid gap-8 md:grid-cols-4">
        <p class="max-w-xl text-lg leading-relaxed text-neutral-500 md:col-span-3 md:col-start-2">
            {{ $section->subtitle ?: $company->description }}
        </p>
    </div>
    <div class="mt-10 grid md:grid-cols-4">
        <a href="{{ $site->anchor('contact') }}" class="group inline-flex items-center gap-2 justify-self-start text-sm font-medium text-neutral-950 underline decoration-neutral-300 underline-offset-[6px] transition hover:decoration-neutral-950 md:col-start-2">
            Mulai percakapan <x-icon name="arrow-right" class="size-4 transition group-hover:translate-x-1" />
        </a>
    </div>
</section>
