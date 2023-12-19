<?php 
$url = urlencode(isset($link)?$link:route('home'));
$desc = urlencode(isset($description)?$description:'');
$img = urlencode(isset($image)?$image:'');
$tit = urlencode(isset($title)?$title:'');
?>
<div class="social-media media-center">
    <a href="https://www.facebook.com/sharer/sharer.php?u={{$url}}&amp;src=sdkpreparse" target="new">
        <div class="social-icon-box social-color">
            <i class="fab fa-facebook-f"></i>
        </div>
    </a>
    <a href="https://twitter.com/home?status={{$url}}{{$desc?' '.$desc:''}}" target="new">
        <div class="social-icon-box social-color">
            <i class="fab fa-twitter"></i>
        </div>
    </a>
    <a href="http://pinterest.com/pin/create/button/?url={{$url}}{{isset($desc)?'&description='.$desc:''}}" target="new">
        <div class="social-icon-box social-color">
            <i class="fab fa-pinterest-p"></i>
        </div>
    </a>
</div>