@if ($plugin_javascript = get_html_plugins('javascript'))
@foreach ($plugin_javascript as $f)
    @include('admin._plugins.'.$f)
@endforeach
@endif

@if ($data = get_js_src())
    @foreach ($data as $src)

    <script src="{{$src}}"></script>
    <!-- {{$src}} -->
    @endforeach
@endif