<x-layouts.marketing title="Template Company Profile">
    <section class="border-b border-slate-100 bg-gradient-to-b from-brand-50/60 to-white">
        <div class="mx-auto max-w-7xl px-4 py-16 text-center sm:px-6">
            <h1 class="text-4xl font-extrabold tracking-tight sm:text-5xl">Template Company Profile</h1>
            <p class="mx-auto mt-4 max-w-2xl text-slate-600">Desain profesional untuk berbagai industri. Pratinjau penuh, lalu gunakan dengan satu klik.</p>
            <form method="GET" class="mx-auto mt-8 flex max-w-md gap-2">
                <input type="hidden" name="category" value="{{ $activeCategory }}">
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari template..." class="form-input">
                <button class="btn btn-primary">Cari</button>
            </form>
        </div>
    </section>
    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6">
        @include('templates.partials.grid', ['link' => fn ($t) => route('templates.show', $t)])
    </section>
</x-layouts.marketing>
