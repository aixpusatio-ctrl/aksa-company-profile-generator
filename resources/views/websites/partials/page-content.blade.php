{{-- Body of a custom page (title is rendered by the template). --}}
@if ($page->featured_image)
    <img src="{{ $page->url('featured_image') }}" alt="{{ $page->title }}" class="mb-10 aspect-[2/1] w-full rounded-brand object-cover">
@endif
<div class="site-prose">
    {!! $page->content !!}
</div>
