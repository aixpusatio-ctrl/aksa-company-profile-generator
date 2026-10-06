<x-website-layout :company="$company" :title="$tabs[$tab]['label']">
    <form method="POST" action="{{ route('websites.update', [$company, $tab]) }}" enctype="multipart/form-data"
          class="card" x-data="autosave(@js(route('websites.autosave', $company)), @js($tab))">
        @csrf
        @method('PUT')
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
            <div>
                <h2 class="text-base font-semibold text-slate-900">{{ $tabs[$tab]['label'] }}</h2>
                <p class="text-xs text-slate-500">Perubahan teks tersimpan otomatis.</p>
            </div>
            @include('dashboard.websites.partials.autosave-status')
        </div>
        <div class="card-body">
            @include('dashboard.websites.tabs.'.$tab)
        </div>
        <div class="flex items-center justify-between gap-3 border-t border-slate-100 bg-slate-50/60 px-6 py-4">
            @if ($tab === 'branding')
                <button type="submit" name="reset_branding" value="1" class="btn btn-ghost text-sm" onclick="return confirm('Kembalikan branding ke default template?')">Reset ke default template</button>
            @else
                <span></span>
            @endif
            <button class="btn btn-primary">Simpan perubahan</button>
        </div>
    </form>
</x-website-layout>
