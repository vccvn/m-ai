@extends($_layout . 'clean')
@section('content')


<div class="error-area">
    <div class="d-table">
        <div class="d-table-cell">
            <div class="error-content">
                <img src="{{theme_asset('img/404-error.jpg')}}" alt="Image">
                <h3>Oops! Page Not Found</h3>
                <p>The page you are looking for might have been removed had its name changed or is temporarily unavailable.</p>
                <a href="/" class="default-btn-two">
                    Return To Home Page
                </a>
            </div>
        </div>
    </div>
</div>


@endsection
