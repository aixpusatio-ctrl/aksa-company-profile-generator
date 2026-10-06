@props(['status'])
@php
    $map = [
        'published' => ['badge-green', 'Published'], 'draft' => ['badge-slate', 'Draft'],
        'active' => ['badge-green', 'Active'], 'pending' => ['badge-amber', 'Pending'], 'verifying' => ['badge-blue', 'Verifying'],
        'failed' => ['badge-red', 'Failed'], 'inactive' => ['badge-slate', 'Inactive'], 'trialing' => ['badge-violet', 'Trial'],
        'canceled' => ['badge-red', 'Canceled'], 'past_due' => ['badge-amber', 'Past due'], 'suspended' => ['badge-red', 'Suspended'],
    ];
    [$class, $label] = $map[$status] ?? ['badge-slate', ucfirst((string) $status)];
@endphp
<span {{ $attributes->merge(['class' => 'badge '.$class]) }}>
    <span class="size-1.5 rounded-full bg-current"></span>{{ $label }}
</span>
