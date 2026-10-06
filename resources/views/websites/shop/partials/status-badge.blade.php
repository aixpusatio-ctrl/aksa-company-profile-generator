{{-- Order status pill. Vars: $status. --}}
@php
    $statusMap = [
        'pending' => ['Menunggu', 'bg-amber-100 text-amber-800'],
        'confirmed' => ['Dikonfirmasi', 'bg-sky-100 text-sky-800'],
        'processing' => ['Diproses', 'bg-sky-100 text-sky-800'],
        'packed' => ['Dikemas', 'bg-indigo-100 text-indigo-800'],
        'shipped' => ['Dikirim', 'bg-violet-100 text-violet-800'],
        'completed' => ['Selesai', 'bg-emerald-100 text-emerald-800'],
        'cancelled' => ['Dibatalkan', 'bg-rose-100 text-rose-800'],
        'refunded' => ['Dikembalikan', 'bg-neutral-200 text-neutral-800'],
        'paid' => ['Lunas', 'bg-emerald-100 text-emerald-800'],
        'failed' => ['Gagal', 'bg-rose-100 text-rose-800'],
        'approved' => ['Tampil', 'bg-emerald-100 text-emerald-800'],
        'rejected' => ['Ditolak', 'bg-rose-100 text-rose-800'],
    ];
    [$statusLabel, $statusClass] = $statusMap[$status] ?? [ucfirst((string) $status), 'bg-neutral-100 text-neutral-700'];
@endphp
<span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $statusClass }}">{{ $statusLabel }}</span>
