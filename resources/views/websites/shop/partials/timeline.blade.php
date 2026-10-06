{{-- Order status timeline. Vars: $timeline (from OrderService::timeline). --}}
<ol class="relative grid gap-0 {{ [2 => 'sm:grid-cols-2', 3 => 'sm:grid-cols-3', 4 => 'sm:grid-cols-4', 5 => 'sm:grid-cols-5', 6 => 'sm:grid-cols-6'][count($timeline)] ?? 'sm:grid-cols-5' }}">
    @foreach ($timeline as $i => $stepItem)
        @php($danger = $stepItem['danger'] ?? false)
        <li class="relative flex gap-3 pb-6 last:pb-0 sm:flex-col sm:items-center sm:pb-0 sm:text-center">
            @unless ($loop->last)
                <span class="absolute top-8 bottom-0 left-4 w-0.5 sm:top-4 sm:right-[-50%] sm:bottom-auto sm:left-[50%] sm:h-0.5 sm:w-auto {{ $timeline[$i + 1]['done'] ? ($timeline[$i + 1]['danger'] ?? false ? 'bg-rose-400' : 'bg-primary') : 'bg-line' }}" aria-hidden="true"></span>
            @endunless
            <span class="relative z-10 inline-flex size-8 shrink-0 items-center justify-center rounded-full border-2 {{ $stepItem['done'] ? ($danger ? 'border-rose-500 bg-rose-500 text-white' : 'border-primary bg-primary text-on-primary') : 'border-line bg-surface text-muted' }}">
                @if ($danger)
                    <x-shop.icon name="close" class="size-4" />
                @elseif ($stepItem['done'])
                    <x-icon name="check" class="size-4" />
                @else
                    <span class="text-xs font-bold">{{ $i + 1 }}</span>
                @endif
            </span>
            <span class="pt-1 sm:pt-2">
                <span class="block text-sm font-semibold {{ $stepItem['done'] ? ($danger ? 'text-rose-600' : 'text-ink') : 'text-muted' }}">{{ $stepItem['label'] }}</span>
                @if ($stepItem['at'])
                    <span class="block text-xs text-muted">{{ $stepItem['at']->translatedFormat('d M Y, H:i') }}</span>
                @endif
            </span>
        </li>
    @endforeach
</ol>
