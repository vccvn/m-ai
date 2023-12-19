
@extends($_lib.'layout')
@section('body')

    <div class="main-wrapper">

        @yield('content')

    </div>


    @include($_template.'js')



@endsection
