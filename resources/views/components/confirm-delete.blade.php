{{-- Small inline delete button with browser confirmation. --}}
@props(['action', 'message' => 'Yakin ingin menghapus item ini?', 'label' => null])
<form method="POST" action="{{ $action }}" onsubmit="return confirm(@js($message))" {{ $attributes->only('class') }}>
    @csrf
    @method('DELETE')
    <button type="submit" class="{{ $label ? 'btn btn-secondary btn-sm text-rose-600' : 'rounded-lg p-2 text-slate-400 hover:bg-rose-50 hover:text-rose-600' }}" title="Hapus">
        <x-icon name="trash" class="size-4" />
        @if ($label) {{ $label }} @endif
    </button>
</form>
