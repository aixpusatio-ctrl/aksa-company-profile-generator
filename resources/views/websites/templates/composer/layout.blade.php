{{--
    Composer theme: assembles a website from the component library
    (resources/views/components/company/*) using the template's design
    system ($ds). Only the components chosen by the template are rendered.
--}}
<!DOCTYPE html>
<html lang="id">
<head>
    <script>if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) document.documentElement.classList.add('js-anim');</script>
    @include('websites.partials.head')
</head>
<body class="{{ $ds->bodyClass() }}">
    <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-[100] focus:rounded focus:bg-primary focus:px-4 focus:py-2 focus:text-on-primary">Lewati ke konten</a>

    @include($ds->view('navbar'))

    <main id="main">
        @yield('content')
    </main>

    @include($ds->view('footer'))

    @include('websites.partials.floating-whatsapp')
    @include('websites.partials.preview-bar')
</body>
</html>
