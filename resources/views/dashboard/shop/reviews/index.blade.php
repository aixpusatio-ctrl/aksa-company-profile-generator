@php
    $tabs = ['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak', 'all' => 'Semua'];
@endphp
<x-website-layout :company="$company" title="Ulasan">
    @include('dashboard.shop.partials.nav')

    <div class="card">
        <div class="flex flex-col gap-3 border-b border-slate-100 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Ulasan produk</h2>
                <p class="text-xs text-slate-500">Setujui ulasan agar tampil di halaman produk.</p>
            </div>
            <div class="-mx-1 flex gap-1 overflow-x-auto px-1">
                @foreach ($tabs as $key => $label)
                    @php($count = $key === 'all' ? $counts->sum() : ($counts[$key] ?? 0))
                    <a href="{{ route('websites.shop.reviews.index', [$company, 'status' => $key]) }}" class="inline-flex shrink-0 items-center gap-1.5 rounded-lg px-3 py-1.5 text-sm font-medium whitespace-nowrap {{ $status === $key ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                        {{ $label }} <span class="rounded-full px-1.5 text-[11px] {{ $status === $key ? 'bg-white/20' : 'bg-slate-100 text-slate-500' }}">{{ $count }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        @if ($reviews->isEmpty())
            <div class="p-6"><x-empty-state title="Tidak ada ulasan" description="Ulasan pelanggan akan muncul di sini." icon="star" /></div>
        @else
            <ul class="divide-y divide-slate-100">
                @foreach ($reviews as $review)
                    <li class="flex flex-col gap-3 px-4 py-4 sm:flex-row sm:px-6">
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-amber-500" aria-label="{{ $review->rating }} dari 5">{{ str_repeat('★', (int) $review->rating) }}<span class="text-slate-200">{{ str_repeat('★', 5 - (int) $review->rating) }}</span></span>
                                <x-status-badge :status="$review->status" />
                                @if ($review->verified_purchase)<span class="badge badge-green"><x-icon name="check" class="size-3" /> Pembeli terverifikasi</span>@endif
                            </div>
                            @if ($review->title)<p class="mt-1 font-semibold text-slate-900">{{ $review->title }}</p>@endif
                            <p class="mt-1 text-sm whitespace-pre-line text-slate-700">{{ $review->body }}</p>
                            @if ($review->url('image'))
                                <a href="{{ $review->url('image') }}" target="_blank"><img src="{{ $review->url('image') }}" alt="" class="mt-2 size-20 rounded-lg object-cover ring-1 ring-slate-200"></a>
                            @endif
                            <p class="mt-2 text-xs text-slate-500">
                                {{ $review->name }} @if ($review->email)&lt;{{ $review->email }}&gt;@endif · {{ $review->created_at->format('d M Y H:i') }} ·
                                @if ($review->product)
                                    <a href="{{ route('websites.shop.products.edit', [$company, $review->product]) }}" class="font-medium text-brand-600 hover:underline">{{ $review->product->name }}</a>
                                @else
                                    Produk dihapus
                                @endif
                            </p>
                        </div>
                        <div class="flex shrink-0 flex-wrap items-start gap-2">
                            @if ($review->status !== 'approved')
                                <form method="POST" action="{{ route('websites.shop.reviews.status', [$company, $review]) }}">@csrf<input type="hidden" name="status" value="approved"><button class="btn btn-success btn-sm"><x-icon name="check" class="size-3.5" /> Setujui</button></form>
                            @endif
                            @if ($review->status !== 'rejected')
                                <form method="POST" action="{{ route('websites.shop.reviews.status', [$company, $review]) }}">@csrf<input type="hidden" name="status" value="rejected"><button class="btn btn-secondary btn-sm"><x-icon name="x" class="size-3.5" /> Tolak</button></form>
                            @endif
                            <x-confirm-delete :action="route('websites.shop.reviews.destroy', [$company, $review])" message="Hapus ulasan ini secara permanen?" />
                        </div>
                    </li>
                @endforeach
            </ul>
            @if ($reviews->hasPages())
                <div class="border-t border-slate-100 px-6 py-4">{{ $reviews->links() }}</div>
            @endif
        @endif
    </div>
</x-website-layout>
