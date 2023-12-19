<?php
$url = urlencode(isset($link)?$link:route('home'));
$desc = urlencode(isset($description)?$description:'');
$img = urlencode(isset($image)?$image:'');
$tit = urlencode(isset($title)?$title:'');
?>
<div class="blog-tags-social">
    <div class="blog-tags">

    </div>
    <div class="blog-social">
        <ul class="social">
            <li><a class="facebook" href="https://www.facebook.com/sharer/sharer.php?u={{$url}}&amp;src=sdkpreparse" target="new"><i class="fab fa-facebook-f"></i></a></li>
            <li><a class="twitter" href="https://twitter.com/home?status={{$url}}{{$desc?' '.$desc:''}}" target="new"><i class="fab fa-twitter"></i></a></li>
            <li><a class="pinterest" href="http://pinterest.com/pin/create/button/?url={{$url}}{{isset($desc)?'&description='.$desc:''}}" target="new"><i class="fab fa-pinterest-p"></i></a></li>
        </ul>
    </div>
</div>
