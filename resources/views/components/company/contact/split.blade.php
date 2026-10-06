{{-- Contact: Split — heading & contact details left, form card right, map below. --}}
<section id="contact" class="{{ $ds->section($tone) }}">
    <div class="{{ $ds->container() }} grid gap-12 lg:grid-cols-12">
        <div class="lg:col-span-5">
            @include('components.company.partials.heading', ['eyebrow' => 'Kontak', 'title' => $section->title ?: 'Mari Berdiskusi', 'subtitle' => $section->subtitle ?: 'Tim kami siap membantu. Kami akan merespons dalam 1x24 jam kerja.', 'align' => 'left', 'number' => $index])
            @include('components.company.partials.contact-details', ['class' => 'mt-10'])
        </div>
        <div class="lg:col-span-7" {!! $ds->reveal(1) !!}>
            <div class="{{ $ds->card('p-6 sm:p-10', false) }}">
                @include('components.company.partials.form')
            </div>
        </div>
    </div>
    @if ($company->mapEmbedUrl())
        <div class="{{ $ds->container() }} mt-12" {!! $ds->reveal(2) !!}>
            @include('websites.partials.map', ['mapClass' => $ds->img('h-80 w-full border-0')])
        </div>
    @endif
</section>
