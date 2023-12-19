@php
$cache_data_time = system_setting('cache_data_time', 0);
    if($cache_data_time){
        add_js_data('____crazy_cache', true);
    }else{
        add_js_data('____crazy_cache', false);
    }
@endphp


<script src="{{asset('static/plugins/axios.min.js')}}"></script>
<script src="{{asset('static/plugins/swal/sweetalert2.all.min.js')}}"></script>

    <script src="{{asset("static/app/js/app.min.js")}}"></script>


    @if ($data = get_js_data())
        <script>
            @foreach($data as $name => $value)

            var {{$name}} = @json($value);

            @endforeach


        </script>
    @endif

    @yield('jsinit')

    @if ($data = get_js_src())
        @foreach ($data as $src)

        <script src="{{$src}}"></script>

        @endforeach
    @endif

    <script src="{{asset("static/app/js/app.modules.js")}}"></script>



    @yield('js-loaded')


    @yield('js')



    @php
        $messKeys = ['warning', 'success', 'error', 'info', 'alert'];
    @endphp
    <script>

        App.onModuleLoaded(function(){
            App.task(function(){

    // @foreach ($messKeys as $key)
       //  @if ($_message = session($key.'_message'))

            App.Swal.{{$key}}({!!json_encode($_message)!!});

        // @endif

    // @endforeach

            })

        })
    </script>
