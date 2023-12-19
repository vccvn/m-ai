@if ($data = get_js_data())
    <script>
        @foreach($data as $name => $value)

        var {{$name}} = @json($value);

        @endforeach
    </script>
@endif

<script>
    window.HTML_DATA = {!! json_encode([
        'urls' => [
            'input' => route('merchant.html.input'),
            'inputList' => route('merchant.html.input-list'),
        ],
        'modals' => get_merchant_template_data('modals'),
        'js' => get_js_src(),
        'css' => get_css_link(),
        'data' => get_js_data()
    ]) !!};
</script>