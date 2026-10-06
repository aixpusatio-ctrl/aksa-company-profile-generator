{{-- Professional services: practice areas as side-list tabs with a detail panel, plus an FAQ accordion. --}}
@php($services = $company->services)
<section id="services" class="py-20 lg:py-28" x-data="{ active: 0 }">
    <div class="mx-auto max-w-7xl px-6">
        <div class="grid gap-6 border-b border-slate-200 pb-10 lg:grid-cols-12 lg:items-end">
            <div class="lg:col-span-7">
                <p class="flex items-center gap-3 text-xs font-semibold tracking-[0.2em] text-primary uppercase"><span class="h-px w-10 bg-secondary"></span> Area Praktik</p>
                <h2 class="mt-4 font-heading text-3xl leading-tight text-slate-900 md:text-4xl">{{ $section->title ?: 'Bidang Keahlian Kami' }}</h2>
            </div>
            <p class="text-slate-600 lg:col-span-5">{{ $section->subtitle ?: 'Setiap perkara dan kebutuhan ditangani oleh spesialis yang berpengalaman di bidangnya, dengan pendekatan yang cermat dan terukur.' }}</p>
        </div>

        @if ($services->isNotEmpty())
            <div class="mt-12 grid grid-cols-1 gap-8 lg:grid-cols-12">
                {{-- Side list --}}
                <div class="min-w-0 lg:col-span-4">
                    <div class="-mx-6 flex gap-2 overflow-x-auto px-6 pb-2 lg:mx-0 lg:flex-col lg:gap-0 lg:overflow-visible lg:px-0 lg:border-l lg:border-slate-200 lg:pb-0" role="tablist">
                        @foreach ($services as $service)
                            <button type="button" role="tab" @click="active = {{ $loop->index }}" :aria-selected="active === {{ $loop->index }}"
                                class="group flex shrink-0 items-center gap-4 rounded-brand border px-4 py-3 text-left transition lg:-ml-px lg:rounded-none lg:border-0 lg:border-l-2 lg:px-6 lg:py-4"
                                :class="active === {{ $loop->index }} ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 text-slate-600 hover:text-slate-900 lg:border-transparent'">
                                <x-icon :name="$service->icon ?: 'briefcase'" class="size-5 shrink-0" />
                                <span class="text-sm font-semibold whitespace-nowrap lg:whitespace-normal">{{ $service->title }}</span>
                                <x-icon name="chevron-right" class="ml-auto hidden size-4 opacity-0 transition lg:block" ::class="active === {{ $loop->index }} && 'opacity-100'" />
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Detail panel --}}
                <div class="min-w-0 lg:col-span-8">
                    @foreach ($services as $service)
                        <div x-show="active === {{ $loop->index }}" @if (! $loop->first) x-cloak @endif x-transition.opacity.duration.300ms role="tabpanel" class="grid overflow-hidden rounded-brand border border-slate-200 bg-white md:grid-cols-5">
                            <div class="relative md:col-span-2">
                                <x-site.img :src="$service->url('image')" :alt="$service->title" :icon="$service->icon ?: 'briefcase'" class="h-56 w-full object-cover md:h-full md:min-h-80" />
                            </div>
                            <div class="flex flex-col p-8 md:col-span-3 md:p-10">
                                <span class="font-heading text-sm text-secondary">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }} / {{ str_pad($services->count(), 2, '0', STR_PAD_LEFT) }}</span>
                                <h3 class="mt-3 font-heading text-2xl text-slate-900">{{ $service->title }}</h3>
                                <p class="mt-4 leading-relaxed text-slate-600">{{ $service->description }}</p>
                                <ul class="mt-6 space-y-2.5 text-sm text-slate-700">
                                    @foreach (['Analisis awal & pemetaan kebutuhan', 'Rekomendasi tertulis yang jelas', 'Pendampingan oleh tenaga ahli'] as $point)
                                        <li class="flex items-center gap-3"><x-icon name="check-circle" class="size-4 shrink-0 text-primary" /> {{ $point }}</li>
                                    @endforeach
                                </ul>
                                <div class="mt-auto flex flex-wrap items-center gap-4 pt-8">
                                    <a href="{{ $site->anchor('contact') }}" class="inline-flex items-center gap-2 rounded-btn bg-primary px-5 py-2.5 text-sm font-semibold text-on-primary transition hover:opacity-90">Konsultasikan <x-icon name="arrow-right" class="size-4" /></a>
                                    @if ($company->whatsappUrl())
                                        <a href="{{ $company->whatsappUrl() }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700 hover:text-primary"><x-icon name="whatsapp" class="size-4" /> Tanya via WhatsApp</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- FAQ accordion built from the practice areas --}}
            <div class="mt-20 grid gap-10 lg:grid-cols-12">
                <div class="lg:col-span-4">
                    <p class="flex items-center gap-3 text-xs font-semibold tracking-[0.2em] text-primary uppercase"><span class="h-px w-10 bg-secondary"></span> Tanya Jawab</p>
                    <h3 class="mt-4 font-heading text-2xl text-slate-900">Pertanyaan yang sering diajukan</h3>
                    <p class="mt-3 text-sm text-slate-600">Belum menemukan jawaban? Tim kami siap membantu menjelaskan secara langsung.</p>
                    <a href="{{ $site->anchor('contact') }}" class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-primary">Ajukan pertanyaan <x-icon name="arrow-right" class="size-4" /></a>
                </div>
                <div class="divide-y divide-slate-200 border-y border-slate-200 lg:col-span-8" x-data="{ faq: 0 }">
                    @foreach ($services->take(5) as $service)
                        <div>
                            <button type="button" @click="faq = faq === {{ $loop->index }} ? null : {{ $loop->index }}" class="flex w-full items-center justify-between gap-6 py-5 text-left">
                                <span class="font-heading text-lg text-slate-900">Apa cakupan layanan {{ $service->title }}?</span>
                                <span class="inline-flex size-8 shrink-0 items-center justify-center rounded-full border border-slate-300 text-primary transition" :class="faq === {{ $loop->index }} && 'rotate-45 border-primary bg-primary text-on-primary'"><x-icon name="plus" class="size-4" /></span>
                            </button>
                            <div x-show="faq === {{ $loop->index }}" x-collapse @if (! $loop->first) x-cloak @endif>
                                <p class="max-w-2xl pb-6 text-sm leading-relaxed text-slate-600">{{ $service->description }} Hubungi kami untuk konsultasi awal dan penjelasan rinci mengenai tahapan serta estimasi biaya.</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>
