{{-- Services: Accordion — sticky heading & intro on the left, expandable service list (first open) with description and image on the right. --}}
<section id="services" class="{{ $ds->section($tone) }}">
    <div class="{{ $ds->container() }} grid gap-12 lg:grid-cols-12 lg:gap-16">
        <div class="lg:col-span-5">
            <div class="lg:sticky lg:top-28">
                @include('components.company.partials.heading', ['eyebrow' => 'Layanan', 'title' => $section->title ?: 'Layanan yang kami sediakan', 'subtitle' => $section->subtitle ?: 'Pilih layanan untuk melihat detail cakupan pekerjaan kami.', 'align' => 'left', 'number' => $index])
                <div class="mt-9 flex flex-wrap items-center gap-x-6 gap-y-4" {!! $ds->reveal(1) !!}>
                    @include('components.company.partials.button', ['href' => $site->anchor('contact'), 'label' => 'Konsultasi Gratis', 'kind' => 'primary'])
                    @if ($company->phone)
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $company->phone) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-ink hover:text-primary">
                            <x-icon name="phone" class="size-4 text-primary" /> {{ $company->phone }}
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="border-t border-line lg:col-span-7" x-data="{ active: 0 }">
            @foreach ($company->services as $service)
                @php($i = $loop->index)
                <div class="border-b border-line" {!! $ds->reveal($i) !!}>
                    <h3>
                        <button type="button" id="service-tab-{{ $i }}" class="group flex min-h-16 w-full items-center gap-5 py-6 text-left" @click="active = active === {{ $i }} ? null : {{ $i }}" :aria-expanded="(active === {{ $i }}).toString()" aria-controls="service-panel-{{ $i }}">
                            <span class="inline-flex size-11 shrink-0 items-center justify-center rounded-brand transition" :class="active === {{ $i }} ? 'bg-primary text-on-primary' : 'bg-primary/10 text-primary'">
                                <x-icon :name="$service->icon ?: 'briefcase'" class="size-5" />
                            </span>
                            <span class="heading flex-1 text-xl transition group-hover:text-primary sm:text-2xl">{{ $service->title }}</span>
                            <span class="inline-flex size-9 shrink-0 items-center justify-center rounded-full border transition duration-300" :class="active === {{ $i }} ? 'rotate-180 border-primary text-primary' : 'border-line text-muted'">
                                <x-icon name="chevron-down" class="size-4" />
                            </span>
                        </button>
                    </h3>
                    <div id="service-panel-{{ $i }}" role="region" aria-labelledby="service-tab-{{ $i }}" x-show="active === {{ $i }}" x-collapse @if (! $loop->first) x-cloak @endif>
                        <div class="grid gap-6 pb-8 sm:grid-cols-5 sm:pl-16">
                            <p class="text-muted {{ $service->url('image') ? 'sm:col-span-3' : 'sm:col-span-5' }}">{{ $service->description }}</p>
                            @if ($service->url('image'))
                                <x-site.img :src="$service->url('image')" :alt="$service->title" class="{{ $ds->img('aspect-[4/3] w-full object-cover sm:col-span-2') }}" />
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
