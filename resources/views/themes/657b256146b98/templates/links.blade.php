    
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "WebSite",
            "url": "{{route('home')}}",
            "potentialAction": {
                "@type": "SearchAction",
                "target": "{{route('home')}}/collections?keyword={search_term_string}",
                "query-input": "required name=search_term_string"
            }
        }
    </script>
    <script type='application/ld+json'>
        {
            "@context":"https://schema.org",
            "@type":"Organization",
            "@id":"{{route('home')}}#organization",
            "url":"{{route('home')}}",
            "name":"{{$siteinfo->site_name}}",
            "description" : "{{$siteinfo->meta_description("Wisestyle Việt Nam là một thương hiệu thời trang nam cao cấp cung cấp trải nghiệm mua sắm nhanh chóng, chất lượng tốt với mức chi phí hợp lý cho người Việt")}}",
            "image":"{{$siteinfo->logo}}",
            "sameAs":[
                "https://www.facebook.com/wisestylevietnam/",
                "https://www.instagram.com/wisestylevn/",
                "https://www.youtube.com/channel/UCc3Sy_iOuBU1_TnuQmL93Ow",
                "https://www.tiktok.com/@wisestylevn?lang=en",
                "https://www.reddit.com/user/wisestylevn"
            ],
            "logo":"{{$siteinfo->logo}}",
            "telephone" : "0367773102",
            "email" : "wisestylevn@gmail.com",
            "address" : {
                "@type" : "PostalAddress",
                "streetAddress" : "375 Nguyen Thai Binh, Phuong 12, Quan Tan Binh",
                "addressLocality" : "Ho Chi Minh",
                "postalCode" : "700000",
                "addressCountry" : "VN"
            }, 
            "contactPoint": {
                "@type": "ContactPoint",
                "contactType": "customer support",
                "telephone": "0367773102",
                "email": "wisestylevn@gmail.com"
            }
                
        }
    </script>

    <script type="application/ld+json">
        {!! json_schema_encode([
            "@context" => "https://schema.org",
            "@type" => "Store",
            "name" => $siteinfo->site_name,
            "description" => $siteinfo->meta_description("Wisestyle Việt Nam là một thương hiệu thời trang nam cao cấp cung cấp trải nghiệm mua sắm nhanh chóng, chất lượng tốt với mức chi phí hợp lý cho người Việt"),
            "image" => $siteinfo->logo,
            "@id" => route('home'),
            "url" => route('home'),
            "telephone" => "0367773102",
            "email" => "wisestylevn@gmail.com",
            "priceRange" => "VND",
            "address" => [
                "@type" => "PostalAddress",
                "streetAddress" => "Nguyễn Thái Bình",
                "addressLocality" => "Ho Chi Minh",
                "postalCode" => "700000",
                "addressCountry" => "VN"
            ],
            "geo" => [
                "@type" => "GeoCoordinates",
                "latitude" => 10.795597,
                "longitude" => 106.6486478
            ],
            "openingHoursSpecification" => [
                "@type" => "OpeningHoursSpecification",
                "dayOfWeek" => [
                    "Monday",
                    "Tuesday",
                    "Wednesday",
                    "Thursday",
                    "Friday",
                    "Saturday"
                ],
                "opens" => "09:00",
                "closes" => "18:00"
            ],
            "sameAs" => [
                "https://www.facebook.com/wisestylevietnam/",
                "https://www.instagram.com/wisestylevn/",
                "https://www.youtube.com/channel/UCc3Sy_iOuBU1_TnuQmL93Ow",
                "https://www.tiktok.com/@wisestylevn?lang=en",
                "https://www.reddit.com/user/wisestylevn"
            ]
        ]) !!}
    </script>

    @if (count($schemas = get_schema_data()))
        @foreach ($schemas as $schema)
        <script type="application/ld+json">{!! json_schema_encode($schema) !!}</script>
        @endforeach
    @endif



    <link rel="preconnect" href="https://fonts.googleapis.com/" />
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin />
    <link rel="preconnect" href="https://fonts.googleapis.com/" />
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin />

    
    <link rel="shortcut icon" type="image/x-icon" href="{{$siteinfo->favicon?$siteinfo->favicon:theme_asset('images/favicon/2.png')}}">
    <!--Google font-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Oswald:wght@200;300;400;500;600&display=swap" rel="stylesheet">
    
    {{-- <link rel="stylesheet" href="{{asset('static/plugins/rangeslider/rangeslider.css')}}"> --}}
    <link rel="stylesheet" href="{{theme_asset('css/main.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{theme_asset('css/home.css')}}">