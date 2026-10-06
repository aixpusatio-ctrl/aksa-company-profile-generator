{{-- Services: Tabs — vertical tab list (horizontal pills on mobile) with a detail panel: image, title, description, CTA. --}}
<section id="services" class="{{ $ds->section($tone) }}">
    <div class="{{ $ds->container() }}">
        @include('components.company.partials.heading', ['eyebrow' => 'Layanan', 'title' => $section->title ?: 'Layanan kami', 'subtitle' => $section->subtitle ?: 'Jelajahi setiap layanan untuk melihat bagaimana kami dapat membantu.', 'number' => $index])

        <div class="mt-14 grid grid-cols-1 gap-8 lg:grid-cols-12 lg:gap-12" x-data="{ active: 0 }">
            <div class="-mx-5 min-w-0 overflow-x-auto px-5 [scrollbar-width:none] sm:-mx-8 sm:px-8 lg:col-span-4 lg:mx-0 lg:overflow-visible lg:px-0 [&::-webkit-scrollbar]:hidden">
                <div role="tablist" aria-label="Daftar layanan" class="flex w-max gap-2 lg:w-auto lg:flex-col lg:gap-1" {!! $ds->reveal(1) !!}>
                    @foreach ($company->services as $service)
                        <button type="button" role="tab" id="svc-tab-{{ $loop->index }}" aria-controls="svc-panel-{{ $loop->index }}"
                            :aria-selected="(active === {{ $loop->index }}).toString()" :tabindex="active === {{ $loop->index }} ? 0 : -1"
                            @click="active = {{ $loop->index }}"
                            @keydown.right.prevent="active = (active + 1) % {{ $loop->count }}; $nextTick(() => document.getElementById('svc-tab-' + active).focus())"
                            @keydown.down.prevent="active = (active + 1) % {{ $loop->count }}; $nextTick(() => document.getElementById('svc-tab-' + active).focus())"
                            @keydown.left.prevent="active = (active + {{ $loop->count - 1 }}) % {{ $loop->count }}; $nextTick(() => document.getElementById('svc-tab-' + active).focus())"
                            @keydown.up.prevent="active = (active + {{ $loop->count - 1 }}) % {{ $loop->count }}; $nextTick(() => document.getElementById('svc-tab-' + active).focus())"
                            class="group flex min-h-11 shrink-0 items-center gap-3 rounded-full border px-4 py-2.5 text-left text-sm font-semibold whitespace-nowrap transition lg:rounded-brand lg:border-0 lg:border-l-2 lg:px-5 lg:py-4 lg:text-base lg:whitespace-normal"
                            :class="active === {{ $loop->index }} ? 'border-primary bg-primary text-on-primary lg:bg-primary/10 lg:text-ink' : 'border-line text-muted hover:text-ink lg:border-transparent lg:hover:bg-ink/5'">
                            <x-icon :name="$service->icon ?: 'briefcase'" class="size-5 shrink-0 lg:text-primary" />
                            <span class="flex-1">{{ $service->title }}</span>
                            <span class="hidden shrink-0 transition lg:block" :class="active === {{ $loop->index }} ? 'opacity-100 text-primary' : 'opacity-0 -translate-x-2'"><x-icon name="arrow-right" class="size-4" /></span>
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="lg:col-span-8" {!! $ds->reveal(2) !!}>
                @foreach ($company->services as $service)
                    <div id="svc-panel-{{ $loop->index }}" role="tabpanel" aria-labelledby="svc-tab-{{ $loop->index }}" tabindex="0"
                        x-show="active === {{ $loop->index }}" x-transition:enter="transition duration-500 ease-out" x-transition:enter-start="opacity-0 translate-y-3" x-transition:enter-end="opacity-100 translate-y-0"
                        @if (! $loop->first) x-cloak @endif
                        class="{{ $ds->card('grid overflow-hidden md:grid-cols-2', false) }}">
                        <x-site.img :src="$service->url('image')" :alt="$service->title" :icon="$service->icon ?: 'briefcase'" class="aspect-[4/3] size-full object-cover md:aspect-auto md:min-h-[24rem]" />
                        <div class="flex flex-col justify-center p-7 sm:p-10">
                            <span class="font-mono text-xs text-muted">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }} / {{ str_pad($loop->count, 2, '0', STR_PAD_LEFT) }}</span>
                            <h3 class="heading mt-4 text-2xl sm:text-3xl">{{ $service->title }}</h3>
                            @if ($service->description)
                                <p class="mt-4 leading-relaxed text-muted">{{ $service->description }}</p>
                            @endif
                            <div class="mt-8">
                                @include('components.company.partials.button', ['href' => $site->anchor('contact'), 'label' => 'Tanyakan Layanan Ini', 'kind' => 'primary'])
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
