@props(['title' => null])
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <x-layouts.partials-head :title="$title" />
</head>
<body class="h-full bg-white">
<div class="grid min-h-full lg:grid-cols-2">
    <div class="flex flex-col px-6 py-10 sm:px-12 lg:px-20">
        <a href="{{ route('home') }}"><x-brand-logo /></a>
        <div class="mx-auto flex w-full max-w-md flex-1 flex-col justify-center py-12">
            <x-flash :summary="false" />
            {{ $slot }}
        </div>
        <p class="text-xs text-slate-400">&copy; {{ date('Y') }} {{ app_name() }}</p>
    </div>
    <div class="relative hidden overflow-hidden bg-slate-950 lg:block">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_20%_20%,rgba(59,101,245,.45),transparent_50%),radial-gradient(circle_at_80%_70%,rgba(139,92,246,.4),transparent_50%)]"></div>
        <div class="absolute inset-0 bg-[linear-gradient(rgba(255,255,255,.04)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,.04)_1px,transparent_1px)] bg-[size:48px_48px]"></div>
        <div class="relative flex h-full flex-col justify-center px-16 text-white">
            <p class="font-display text-4xl leading-tight font-extrabold">Company profile profesional,<br>tanpa coding.</p>
            <p class="mt-4 max-w-md text-white/70">Pilih template, isi data perusahaan, dan publish ke subdomain atau domain Anda sendiri dalam hitungan menit.</p>
            <div class="mt-10 grid max-w-md grid-cols-3 gap-3">
                @foreach (['10+ template', 'Custom domain', 'SEO ready'] as $feature)
                    <div class="rounded-xl border border-white/10 bg-white/5 px-3 py-4 text-center text-xs font-semibold backdrop-blur">{{ $feature }}</div>
                @endforeach
            </div>
        </div>
    </div>
</div>
</body>
</html>
