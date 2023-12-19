        
		
		<script src="{{asset('static/manager/assets/vendors/base/vendors.bundle.js')}}" type="text/javascript"></script>
		<script src="{{asset('static/manager/assets/demo/default/base/scripts.bundle.js')}}" type="text/javascript"></script>
		<script src="{{asset('static/plugins/axios/axios.min.js')}}"></script>

		<script src="{{asset('static/manager/js/app.js')}}"></script>
		<script src="{{asset('static/manager/js/api.js')}}"></script>

		<script src="{{asset('static/crazy/js/modal.js')}}"></script>

		@include($_template.'js-src')

		@yield('js')

		
		@yield('custom_js')

