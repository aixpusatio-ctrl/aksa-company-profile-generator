<x-layouts.app :title="'Wizard · '.$company->name">
    @php($total = count($steps))
    <div class="mx-auto max-w-5xl">
        <div class="mb-6 flex items-center justify-between gap-4">
            <div class="min-w-0">
                <p class="text-sm font-medium text-slate-500">{{ $company->name }}</p>
                <h1 class="text-2xl font-bold tracking-tight">{{ $index + 1 }}. {{ $label }}</h1>
            </div>
            <a href="{{ route('websites.show', $company) }}" class="btn btn-ghost btn-sm">Simpan & keluar</a>
        </div>

        {{-- Progress --}}
        <div class="card mb-6 p-4">
            <div class="h-1.5 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full bg-gradient-to-r from-brand-500 to-violet-500 transition-all" style="width: {{ ($index + 1) / $total * 100 }}%"></div></div>
            <ol class="mt-4 flex gap-1 overflow-x-auto pb-1">
                @foreach ($steps as $key => [$stepLabel])
                    @php($position = $loop->index)
                    <li class="shrink-0">
                        <a href="{{ route('websites.wizard', [$company, $key]) }}" class="flex items-center gap-2 rounded-lg px-2.5 py-1.5 text-xs font-medium {{ $key === $step ? 'bg-brand-50 text-brand-700' : ($position < $company->wizard_step - 1 ? 'text-slate-700 hover:bg-slate-50' : 'text-slate-400 hover:bg-slate-50') }}">
                            <span class="inline-flex size-5 items-center justify-center rounded-full text-[10px] font-bold {{ $key === $step ? 'bg-brand-600 text-white' : ($position < $company->wizard_step - 1 ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-500') }}">
                                @if ($position < $company->wizard_step - 1 && $key !== $step) ✓ @else {{ $position + 1 }} @endif
                            </span>
                            {{ $stepLabel }}
                        </a>
                    </li>
                @endforeach
            </ol>
        </div>

        @if ($kind === 'content')
            @include('dashboard.websites.partials.content-manager', ['type' => $type, 'items' => $items])
            <form method="POST" action="{{ route('websites.wizard.save', [$company, $step]) }}" class="mt-6 flex items-center justify-between">
                @csrf
                <a href="{{ route('websites.wizard', [$company, $previous]) }}" class="btn btn-secondary"><x-icon name="arrow-left" class="size-4" /> Kembali</a>
                <button class="btn btn-primary">{{ $items->isEmpty() ? 'Lewati' : 'Lanjutkan' }} <x-icon name="arrow-right" class="size-4" /></button>
            </form>
        @elseif ($kind === 'preview')
            <div class="card overflow-hidden">
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                    <p class="text-sm text-slate-600">Periksa tampilan website Anda. Anda tetap bisa mengubah semuanya setelah publish.</p>
                    <a href="{{ route('websites.preview', $company) }}" target="_blank" class="btn btn-secondary btn-sm">Layar penuh <x-icon name="external" class="size-3.5" /></a>
                </div>
                <iframe src="{{ route('websites.preview.frame', $company) }}" title="Preview" class="h-[70vh] w-full border-0"></iframe>
            </div>
            <form method="POST" action="{{ route('websites.wizard.save', [$company, $step]) }}" class="mt-6 flex items-center justify-between">
                @csrf
                <a href="{{ route('websites.wizard', [$company, $previous]) }}" class="btn btn-secondary"><x-icon name="arrow-left" class="size-4" /> Kembali</a>
                <button class="btn btn-primary">Lanjutkan ke Publish <x-icon name="arrow-right" class="size-4" /></button>
            </form>
        @elseif ($kind === 'publish')
            <div class="card card-body text-center">
                <span class="mx-auto inline-flex size-16 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-500 to-violet-600 text-white shadow-lg"><x-icon name="rocket" class="size-8" /></span>
                <h2 class="mt-5 text-2xl font-bold">Siap dipublikasikan!</h2>
                <p class="mx-auto mt-2 max-w-md text-sm text-slate-600">Website Anda akan dapat diakses publik di alamat berikut:</p>
                <p class="mt-4 inline-flex items-center gap-2 rounded-xl bg-slate-100 px-4 py-2 font-mono text-sm font-semibold text-slate-900"><x-icon name="globe" class="size-4" /> {{ $company->subdomainHost() }}</p>
                <p class="mt-3 text-xs text-slate-500">Ingin domain sendiri (www.perusahaan.com)? Atur di menu Domain setelah publish.</p>
                <form method="POST" action="{{ route('websites.wizard.save', [$company, $step]) }}" class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
                    @csrf
                    <a href="{{ route('websites.wizard', [$company, $previous]) }}" class="btn btn-secondary"><x-icon name="arrow-left" class="size-4" /> Kembali</a>
                    <button class="btn btn-success btn-lg"><x-icon name="rocket" class="size-5" /> Publish Website</button>
                </form>
            </div>
        @else
            <form method="POST" action="{{ route('websites.wizard.save', [$company, $step]) }}" enctype="multipart/form-data" class="card"
                  @if ($kind === 'tab') x-data="autosave(@js(route('websites.autosave', $company)), @js($target))" @endif>
                @csrf
                <div class="card-body">
                    @if ($kind === 'template')
                        <p class="mb-5 text-sm text-slate-600">Template menentukan tampilan website. Anda bisa menggantinya kapan saja tanpa kehilangan data.</p>
                        @include('dashboard.websites.partials.template-picker', ['selected' => $company->template?->slug])
                    @else
                        <div class="mb-5 flex justify-end">@include('dashboard.websites.partials.autosave-status')</div>
                        @include('dashboard.websites.tabs.'.$target)
                    @endif
                </div>
                <div class="flex items-center justify-between border-t border-slate-100 bg-slate-50/60 px-6 py-4">
                    @if ($previous)
                        <button name="action" value="back" class="btn btn-secondary"><x-icon name="arrow-left" class="size-4" /> Kembali</button>
                    @else
                        <span></span>
                    @endif
                    <button class="btn btn-primary">Simpan & Lanjutkan <x-icon name="arrow-right" class="size-4" /></button>
                </div>
            </form>
        @endif
    </div>
</x-layouts.app>
