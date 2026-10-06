{{-- CTA: Contact — headline with three quick contact actions (WhatsApp, phone, email) as cards. --}}
@php
    $actions = collect([
        $company->whatsappUrl() ? ['whatsapp', 'WhatsApp', $company->whatsapp, $company->whatsappUrl(), 'Chat sekarang'] : null,
        $company->phone ? ['phone', 'Telepon', $company->phone, 'tel:'.preg_replace('/[^0-9+]/', '', $company->phone), 'Telepon kami'] : null,
        $company->email ? ['mail', 'Email', $company->email, 'mailto:'.$company->email, 'Kirim email'] : null,
    ])->filter()->values();
    $cols = [1 => 'sm:grid-cols-1', 2 => 'sm:grid-cols-2'][$actions->count()] ?? 'sm:grid-cols-3';
@endphp
<section id="cta" class="{{ $ds->section($tone) }}">
    <div class="{{ $ds->container() }} grid gap-10 lg:grid-cols-12 lg:items-center lg:gap-14">
        <div class="lg:col-span-5">
            @include('components.company.partials.heading', ['eyebrow' => 'Hubungi Kami', 'title' => $section->title ?: 'Butuh Bantuan? Kami Siap Melayani', 'subtitle' => $section->subtitle ?: 'Pilih cara yang paling nyaman untuk terhubung dengan tim kami.', 'align' => 'left', 'number' => $index])
            <div class="mt-8" {!! $ds->reveal(1) !!}>
                @include('components.company.partials.button', ['href' => $site->anchor('contact'), 'label' => 'Kirim Pesan', 'kind' => $actions->isEmpty() ? 'primary' : 'link'])
            </div>
        </div>
        @if ($actions->isNotEmpty())
            <div class="grid gap-4 lg:col-span-7 {{ $cols }}">
                @foreach ($actions as [$icon, $label, $value, $href, $action])
                    <a href="{{ $href }}" @if (str_starts_with($href, 'http')) target="_blank" rel="noopener noreferrer" @endif class="{{ $ds->card('group flex flex-col p-6 sm:p-7') }}" {!! $ds->reveal($loop->index + 1) !!}>
                        <span class="inline-flex size-12 items-center justify-center rounded-full bg-primary text-on-primary transition group-hover:scale-110"><x-icon :name="$icon" class="size-5" /></span>
                        <span class="mt-6 text-xs font-semibold tracking-[0.18em] text-muted uppercase">{{ $label }}</span>
                        <span class="mt-1 font-semibold break-words text-ink">{{ $value }}</span>
                        <span class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-primary">{{ $action }} <x-icon name="arrow-right" class="size-4 transition group-hover:translate-x-1" /></span>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</section>
