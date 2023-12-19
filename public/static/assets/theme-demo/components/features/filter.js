$(function(){
    $('.shop-sidebar').each(function(i, e){
        var $sidebar = $(e);
        var $searchForm = $sidebar.find('#product-filter-form');
        var submiting = false;
        function submitFilter(){
            var data = $searchForm.serializeArray();
            var d = {};
            var arrayNames = {};
            for (let i = 0; i < data.length; i++) {
                const inp = data[i];
                if (inp.name.substr(inp.name.length - 2, 2) == '[]') {
                    let n = inp.name.substr(0, inp.name.length - 2);
                    arrayNames[n] = true;
                    if (typeof d[n] == "undefined") {
                        d[n] = [];
                    }
                    d[n].push(encodeURIComponent(inp.value));
                } else {
                    d[inp.name] = encodeURIComponent(inp.value);
                }

            }

            var fd = {};
            Object.keys(d).map(function(name){
                if(arrayNames[name] === true){
                    fd[name] = d[name].join(",");
                }else if(d[name].length){
                    fd[name] = d[name];
                }
            });
            // console.log(fd)
            var url = filter_data.search_url;
            var a = url.split('?');
            if(a.length > 1){
                url = a[0] + '?';
            }else{
                url = url + '?';
            }
            var i = 0;
            Object.keys(fd).map(function(name){
                if(i == 0) url+= name+'='+fd[name];
                else url += '&' + name+'='+fd[name];
                i++;
            });
            window.location.href = url;
            submiting = true;
            
        }
        $searchForm.on("submit", function(e){
            e.preventDefault();
            submitFilter();
            return false;
        });
        $sidebar.on('change', '.submit-on-change input', function(){
            setTimeout(function(){submitFilter()}, 20);
        })
        // $sidebar.on('click', '.submit-on-change input[type="radio"]:checked', function(){
        //     $(this).prop("checked", false);
        //     setTimeout(function(){submitFilter()}, 20);
        // });
        $sidebar.on('click', '.submit-on-change input[type="radio"]:checked+label.form-check-label', function(){
            var $this = $(this);

            setTimeout(function(){
                if(!submiting){
                    var checkbox = $("#"+$this.attr('for'));
                    if(checkbox.length){
                        if(checkbox.is(":checked")){
                            checkbox.prop("checked", false);
                            submitFilter();
                        }
                    }
                }
            }, 40);
        });

        $(document).on("click", '.btn-close-filter-category', function(event){
            event.preventDefault();
            var id = $(this).data('id');
            $(this).closest('li').hide(200, function(){
                $(this).remove();
            });
            $('#cate--' + id).prop('checked', false);
            submitFilter();
            
        })
        
        
    })
})
