@props(['status'])
@php
    $map = [
        'published' => ['badge-green', 'Published'], 'draft' => ['badge-slate', 'Draft'],
        'active' => ['badge-green', 'Active'], 'pending' => ['badge-amber', 'Pending'], 'verifying' => ['badge-blue', 'Verifying'],
        'failed' => ['badge-red', 'Failed'], 'inactive' => ['badge-slate', 'Inactive'], 'trialing' => ['badge-violet', 'Trial'],
        'canceled' => ['badge-red', 'Canceled'], 'past_due' => ['badge-amber', 'Past due'], 'suspended' => ['badge-red', 'Suspended'],
        // Online shop (orders, payments, products, reviews)
        'confirmed' => ['badge-blue', 'Confirmed'], 'processing' => ['badge-violet', 'Processing'], 'packed' => ['badge-violet', 'Packed'],
        'shipped' => ['badge-blue', 'Shipped'], 'completed' => ['badge-green', 'Completed'], 'cancelled' => ['badge-red', 'Cancelled'],
        'refunded' => ['badge-slate', 'Refunded'], 'paid' => ['badge-green', 'Paid'], 'unpaid' => ['badge-amber', 'Unpaid'],
        'archived' => ['badge-slate', 'Archived'], 'approved' => ['badge-green', 'Approved'], 'rejected' => ['badge-red', 'Rejected'],
    ];
    [$class, $label] = $map[$status] ?? ['badge-slate', ucfirst((string) $status)];
@endphp
<span {{ $attributes->merge(['class' => 'badge '.$class]) }}>
    <span class="size-1.5 rounded-full bg-current"></span>{{ $label }}
</span>
