

    <link rel="stylesheet" href="{{asset('static/plugins/swal/sweetalert2.min.css')}}">
    <link rel="stylesheet" href="{{asset('static/app/css/app.min.css')}}">


	@yield('css')

    @if ($css = get_custom_css())
        <style>
        {!! $css !!}
        </style>
    @endif

    @if ($links = get_css_link())

        @foreach ($links as $link)

        <link rel="stylesheet" href="{{$link}}">

        @endforeach

    @endif
