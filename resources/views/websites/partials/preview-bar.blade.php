@if ($site->isPreview() && ! $site->isTemplatePreview())
    <div class="pointer-events-none fixed bottom-5 left-5 z-50 rounded-full bg-black/80 px-4 py-2 text-xs font-medium text-white shadow-lg backdrop-blur">
        Preview {{ $company->isPublished() ? '' : '· Draft' }} — {{ $company->subdomainHost() }}
    </div>
@endif
