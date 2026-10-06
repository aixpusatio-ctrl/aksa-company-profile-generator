<x-website-layout :company="$company" title="Template">
    <form method="POST" action="{{ route('websites.template.update', $company) }}" class="space-y-6">
        @csrf
        @method('PUT')
        <div class="card card-body flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Template saat ini: {{ $company->template?->name ?? '—' }}</h2>
                <p class="text-sm text-slate-500">Mengganti template tidak menghapus data. Website langsung dirender ulang dengan desain baru.</p>
            </div>
            <label class="flex items-center gap-2 text-sm text-slate-600"><input type="checkbox" name="reset_layout" value="1" class="form-checkbox"> Reset branding & urutan section ke default template</label>
        </div>
        @include('dashboard.websites.partials.template-picker', ['selected' => $company->template?->slug])
        <div class="sticky bottom-4 z-20 flex justify-end"><button class="btn btn-primary btn-lg shadow-xl">Gunakan template ini</button></div>
    </form>
</x-website-layout>
