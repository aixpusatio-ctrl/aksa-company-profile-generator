{{--
    Renders the enabled home page sections in the order chosen in the
    Section Builder. Each template ships its own design for every section
    (websites/templates/{layout}/sections/{key}); Corporate is the fallback.
--}}
@foreach ($sections as $section)
    @includeFirst(
        ["websites.templates.{$layout}.sections.{$section->key}", "websites.templates.corporate.sections.{$section->key}"],
        ['section' => $section, 'index' => $loop->index]
    )
@endforeach
