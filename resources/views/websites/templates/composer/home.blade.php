@extends('websites.templates.composer.layout')

@section('content')
    @php($n = 0)
    @foreach ($sections as $section)
        @include($ds->view($section->key), [
            'section' => $section,
            'index' => $loop->index,
            'tone' => $section->key === 'hero' ? 'base' : $ds->tone($n++, $section->key),
        ])
    @endforeach
@endsection
