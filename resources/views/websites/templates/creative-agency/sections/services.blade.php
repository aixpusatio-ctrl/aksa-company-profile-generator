{{-- Creative services: big hover rows with arrow on a near-black block. --}}
<section id="services" class="px-3 sm:px-5">
    <div class="mx-auto max-w-[90rem] rounded-[2rem] bg-secondary px-6 py-20 text-on-secondary sm:px-10 lg:py-28">
        <div class="flex flex-col justify-between gap-6 md:flex-row md:items-end">
            <h2 class="font-heading text-5xl leading-[0.9] font-extrabold tracking-tighter md:text-7xl lg:text-8xl">{{ $section->title ?: 'Apa yang kami lakukan' }}<span class="text-primary">*</span></h2>
            <p class="max-w-sm text-on-secondary/60">{{ $section->subtitle ?: '*dan kami melakukannya dengan sangat serius — tapi tetap menyenangkan.' }}</p>
        </div>
        <ul class="mt-16 border-t border-white/15">
            @foreach ($company->services as $service)
                <li>
                    <a href="{{ $site->anchor('contact') }}" class="group relative flex items-center gap-6 overflow-hidden border-b border-white/15 px-2 py-8 md:gap-10 md:px-6 md:py-10">
                        <span class="absolute inset-0 origin-bottom scale-y-0 bg-primary transition-transform duration-500 ease-out group-hover:scale-y-100"></span>
                        <span class="relative w-10 shrink-0 font-heading text-sm font-bold text-on-secondary/40 transition group-hover:text-on-primary md:w-16">({{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }})</span>
                        <span class="relative flex-1">
                            <span class="block font-heading text-3xl font-extrabold tracking-tight transition duration-500 group-hover:translate-x-4 group-hover:text-on-primary md:text-6xl">{{ $service->title }}</span>
                            <span class="mt-3 block max-w-2xl text-sm text-on-secondary/60 transition group-hover:translate-x-4 group-hover:text-on-primary/85 md:text-base">{{ $service->description }}</span>
                        </span>
                        <span class="relative hidden size-12 shrink-0 items-center justify-center rounded-full border border-white/20 transition duration-500 group-hover:rotate-45 group-hover:border-transparent group-hover:bg-white group-hover:text-neutral-950 sm:inline-flex md:size-20">
                            <x-icon name="arrow-up-right" class="size-5 md:size-8" />
                        </span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</section>
