{{-- Simple accessible pagination for a LengthAwarePaginator ($paginator). --}}
@if ($paginator->hasPages())
    @php
        $current = $paginator->currentPage();
        $last = $paginator->lastPage();
        $window = collect(range(max(1, $current - 1), min($last, $current + 1)))->merge([1, $last])->unique()->sort()->values();
    @endphp
    <nav class="mt-10 flex flex-wrap items-center justify-center gap-1.5" aria-label="Halaman">
        @if ($paginator->onFirstPage())
            <span class="inline-flex h-10 items-center gap-1 rounded-btn px-3 text-sm text-muted opacity-50"><x-icon name="chevron-left" class="size-4" /> <span class="hidden sm:inline">Sebelumnya</span></span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex h-10 items-center gap-1 rounded-btn border border-line px-3 text-sm text-ink hover:border-primary hover:text-primary"><x-icon name="chevron-left" class="size-4" /> <span class="hidden sm:inline">Sebelumnya</span></a>
        @endif

        @foreach ($window as $i => $page)
            @if ($i > 0 && $page - $window[$i - 1] > 1)
                <span class="px-1 text-muted">…</span>
            @endif
            @if ($page === $current)
                <span aria-current="page" class="inline-flex size-10 items-center justify-center rounded-btn bg-primary text-sm font-semibold text-on-primary">{{ $page }}</span>
            @else
                <a href="{{ $paginator->url($page) }}" class="inline-flex size-10 items-center justify-center rounded-btn border border-line text-sm text-ink hover:border-primary hover:text-primary">{{ $page }}</a>
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex h-10 items-center gap-1 rounded-btn border border-line px-3 text-sm text-ink hover:border-primary hover:text-primary"><span class="hidden sm:inline">Berikutnya</span> <x-icon name="chevron-right" class="size-4" /></a>
        @else
            <span class="inline-flex h-10 items-center gap-1 rounded-btn px-3 text-sm text-muted opacity-50"><span class="hidden sm:inline">Berikutnya</span> <x-icon name="chevron-right" class="size-4" /></span>
        @endif
    </nav>
@endif
