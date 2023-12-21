

@extends($_lib.'layout')
@section('template.sub','chat')
    
@section('body')


    <!-- Main Wrapper -->
    <div class="main-wrapper">

        <!-- content -->
        <div class="content main_content">
            @yield('content')
        </div>
        <!-- /Content -->
        @include($_template.'chat.modals')

    </div>
    <!-- /Main Wrapper -->

    @include($_template.'chat.js')

@endsection
