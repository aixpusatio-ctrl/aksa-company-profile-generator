<x-layouts.marketing title="Template Company Profile">
    <section class="relative overflow-hidden border-b border-slate-100 bg-gradient-to-b from-brand-50/70 to-white">
        <div class="absolute inset-0 -z-0 bg-[linear-gradient(rgba(15,23,42,.035)_1px,transparent_1px),linear-gradient(90deg,rgba(15,23,42,.035)_1px,transparent_1px)] bg-[size:56px_56px] [mask-image:radial-gradient(ellipse_at_top,black,transparent_70%)]"></div>
        <div class="relative mx-auto max-w-7xl px-4 py-16 text-center sm:px-6 sm:py-20">
            <span class="inline-flex items-center gap-2 rounded-full border border-brand-200 bg-white px-4 py-1.5 text-xs font-semibold text-brand-700 shadow-sm">{{ $templates->count() }} template premium · {{ $categories->count() }} kategori</span>
            <h1 class="mx-auto mt-6 max-w-3xl text-4xl font-extrabold tracking-tight sm:text-6xl">Template website yang benar-benar berbeda</h1>
            <p class="mx-auto mt-5 max-w-2xl text-lg text-slate-600">Setiap template memiliki design system sendiri — layout, navigasi, hero, tipografi, kartu dan footer. Pratinjau penuh dengan data demo, lalu gunakan dengan satu klik.</p>
        </div>
    </section>
    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6">
        @include('templates.partials.grid', ['link' => fn ($t) => route('templates.show', $t)])
    </section>
</x-layouts.marketing>
