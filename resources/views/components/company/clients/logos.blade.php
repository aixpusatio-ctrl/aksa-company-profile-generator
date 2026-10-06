{{-- Clients: Logos — client names as typographic wordmarks in a row. --}}
<section id="clients" class="{{ $ds->section($tone, '!py-12') }}">
    <div class="{{ $ds->container() }}">
        <p class="text-center text-xs font-semibold tracking-[0.25em] text-muted uppercase" {!! $ds->reveal() !!}>{{ $section->title ?: 'Dipercaya oleh perusahaan terkemuka' }}</p>
        <ul class="mt-8 flex flex-wrap items-center justify-center gap-x-12 gap-y-6">
            @foreach (array_slice($company->clients(), 0, 8) as $client)
                <li class="heading text-lg text-ink/50 transition hover:text-ink sm:text-xl" {!! $ds->reveal($loop->index + 1) !!}>{{ $client }}</li>
            @endforeach
        </ul>
    </div>
</section>
