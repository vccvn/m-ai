$(function(){
    $('.detail-contents .see-more-content .btn-see-more, .cate-content-box  .btn-see-more').on("click", function(e){
        e.preventDefault();
        var $detailBox = $(this).closest('.detail-box');
        if($detailBox.length){
            if(!$detailBox.hasClass("full-content")){
                $(this).html("Ẩn bớt");
                $detailBox.addClass("full-content");
            }else{
                $(this).html("Xem Thêm");
                $detailBox.removeClass("full-content");
            }
        }else{
            var $cateContentBox = $(this).closest('.cate-content-box');
            if($cateContentBox.length){
                if(!$cateContentBox.hasClass("full-content")){
                    $(this).html("Ẩn bớt");
                    $cateContentBox.addClass("full-content");
                }else{
                    $(this).html("Xem Thêm");
                    $cateContentBox.removeClass("full-content");
                }
            }
        }
    });

    $(document).on("click", ".crazy-cart-section .coupon-item", function(e){
        var code = $(this).data("coupon-code");
        $('.crazy-coupon-code').val(code);
    })

    $(document).on("click", ".pav-has-thumbnail", function(e){
        var value_id = $(this).data("value-id");
        console.log(value_id);
        $('#pav-thumbnail-'+value_id).trigger("click");
    })

    function checkSearchBoxPlaceholder(){
        $search = $('#sidebar-search-input-text');
        var width = window.innerWidth;
        if(width<992){
            $search.attr('placeholder', 'Tim kiếm phong cách cá nhân');
        }
        else if(width<1170){
            $search.attr('placeholder', 'Tim kiếm...');
        }
        else if(width<1170){
            $search.attr('placeholder', 'Tim kiếm...');
        }
        else if(width<1580){
            $search.attr('placeholder', 'Tim kiếm phong cách...');
        }
        else{
            $search.attr('placeholder', 'Tim kiếm phong cách cá nhân');
        }
    }

    window.addEventListener('resize', function(e){
        checkSearchBoxPlaceholder()
    })
    checkSearchBoxPlaceholder();
    /*--
        Header Sticky
    -----------------------------------*/
    window.onload = function(){
        //hide the preloader
        var preloader = document.querySelector(".preloader");
        if(preloader){
            preloader.style.display = "none";
        }
        
    }
})