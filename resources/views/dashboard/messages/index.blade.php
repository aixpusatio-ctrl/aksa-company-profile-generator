<x-website-layout :company="$company" title="Messages">
    <div class="card">
        <div class="border-b border-slate-100 px-6 py-4">
            <h2 class="text-base font-semibold text-slate-900">Pesan dari contact form</h2>
            <p class="text-xs text-slate-500">Pesan yang dikirim pengunjung melalui website Anda.</p>
        </div>
        @if ($messages->isEmpty())
            <div class="p-6"><x-empty-state title="Belum ada pesan" description="Pesan dari pengunjung akan muncul di sini." icon="mail" /></div>
        @else
            <ul class="divide-y divide-slate-100">
                @foreach ($messages as $message)
                    <li class="px-6 py-5 {{ $message->read_at ? '' : 'bg-brand-50/40' }}" x-data="{ open: {{ $message->read_at ? 'false' : 'true' }} }">
                        <div class="flex flex-wrap items-start gap-3">
                            <span class="inline-flex size-10 shrink-0 items-center justify-center rounded-full bg-slate-100 text-sm font-bold text-slate-600">{{ mb_strtoupper(mb_substr($message->name, 0, 1)) }}</span>
                            <button type="button" class="min-w-0 flex-1 text-left" @click="open = !open">
                                <p class="flex items-center gap-2 text-sm font-semibold text-slate-900">@unless ($message->read_at)<span class="size-2 rounded-full bg-brand-500"></span>@endunless {{ $message->name }} <span class="font-normal text-slate-400">&lt;{{ $message->email }}&gt;</span></p>
                                <p class="text-sm text-slate-600">{{ $message->subject ?: \Illuminate\Support\Str::limit($message->message, 70) }}</p>
                            </button>
                            <span class="text-xs text-slate-400">{{ $message->created_at->format('d M Y H:i') }}</span>
                        </div>
                        <div x-show="open" x-collapse class="mt-3 pl-13">
                            <p class="rounded-xl bg-white p-4 text-sm whitespace-pre-line text-slate-700 ring-1 ring-slate-200">{{ $message->message }}</p>
                            <div class="mt-3 flex flex-wrap items-center gap-2">
                                <a href="mailto:{{ $message->email }}?subject={{ rawurlencode('Re: '.($message->subject ?: 'Pesan Anda')) }}" class="btn btn-primary btn-sm"><x-icon name="mail" class="size-3.5" /> Balas email</a>
                                @if ($message->phone)<a href="tel:{{ $message->phone }}" class="btn btn-secondary btn-sm"><x-icon name="phone" class="size-3.5" /> {{ $message->phone }}</a>@endif
                                <form method="POST" action="{{ route('websites.messages.update', [$company, $message]) }}">@csrf @method('PATCH')<button class="btn btn-ghost btn-sm">{{ $message->read_at ? 'Tandai belum dibaca' : 'Tandai dibaca' }}</button></form>
                                <x-confirm-delete :action="route('websites.messages.destroy', [$company, $message])" message="Hapus pesan ini?" />
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
            <div class="px-6 py-4">{{ $messages->links() }}</div>
        @endif
    </div>
</x-website-layout>
