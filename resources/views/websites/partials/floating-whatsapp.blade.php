@if ($company->whatsappUrl())
    <a href="{{ $company->whatsappUrl() }}" target="_blank" rel="noopener noreferrer"
       class="fixed right-5 bottom-5 z-40 inline-flex size-14 items-center justify-center rounded-full bg-[#25D366] text-white shadow-lg shadow-black/20 transition hover:scale-105"
       aria-label="Chat WhatsApp">
        <x-icon name="whatsapp" class="size-7" />
    </a>
@endif
