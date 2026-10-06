<x-layouts.app title="Templates">
    <x-page-header title="Templates" description="Pilih desain untuk website baru, atau ganti template website yang sudah ada dari menu Template." />
    @include('templates.partials.grid', ['link' => fn ($t) => route('templates.show', $t)])
</x-layouts.app>
