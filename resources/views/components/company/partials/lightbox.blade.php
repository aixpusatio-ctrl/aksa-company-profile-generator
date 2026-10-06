{{-- Lightbox overlay; place inside an element with x-data="lightbox". --}}
<div x-cloak x-show="current" x-transition.opacity @click="hide()" @keydown.escape.window="hide()" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/90 p-4 sm:p-8" role="dialog" aria-modal="true">
    <button type="button" class="absolute top-4 right-4 inline-flex size-11 items-center justify-center rounded-full bg-white/10 text-white" aria-label="Tutup"><x-icon name="x" class="size-6" /></button>
    <figure class="max-w-6xl" @click.stop>
        <img :src="current?.src" :alt="current?.title" class="max-h-[80vh] w-auto rounded-brand object-contain">
        <figcaption class="mt-3 text-center text-sm text-white/80" x-text="current?.title"></figcaption>
    </figure>
</div>
