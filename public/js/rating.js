  window.onload = function(){
    var allList = $('.starBox');
    //循环页面里所有的星星列表
    $.each(allList, function(inde, ele) {
    
        var allStar = $(allList[inde]).find('li');
        var starLiPlus = $(allList[inde]).children('.starPlus').find('li');
        var starLiMinus = $(allList[inde]).children('.starMinus').find('li');
        var rating_tips_green = $(allList[inde]).next('.rating_tips').find('.rating_tips_green');
        var rating_tips_red = $(allList[inde]).next('.rating_tips').find('.rating_tips_red');
        var rating_tips_default = $(allList[inde]).next('.rating_tips').find('.rating_tips_default');

        function forClass(starlist,that){   
            //加分和负分使用不同的循环
            /*if(starlist == starLiPlus){
                for (var i = 0; i < that+1; i++) {
                    $(starlist[i]).addClass('act');
                };
            }else{
                for (var i = starlist.length; i >= that; i--) {
                    $(starlist[i]).addClass('act');
                };

            }*/
            for (var i = 0; i < that+1; i++) {
                $(starlist[i]).addClass('act');
            };
        }
        function forData(starlist,that){
           /* if(starlist == starLiPlus){
                for (var i = 0; i < that+1; i++) {
                    $(starlist[i]).addClass('active').data('c', 'active');
                };
            }else{
                for (var i = starlist.length; i >= that; i--) {
                    $(starlist[i]).addClass('active').data('c', 'active');
                };
            }*/
            for (var i = 0; i < that+1; i++) {
                $(starlist[i]).addClass('active').data('c', 'active');
            };
            
        }
        // 循环16个星星
        function eachStar(starlist){
            $.each(starlist, function(index, ele) {
                $(ele).mouseover(function(){
                    allStar.removeClass();
                    var thisStar = $(this).index();
                    forClass(starlist,thisStar);
                });
                $(ele).mouseout(function(){
                    for (var i = 0; i < starlist.length; i++) {
                        $(starlist[i]).removeClass('act');
                        if(starlist.eq(i).data('c')){
                            starlist.eq(i).addClass(starlist.data('c'));
                        }
                    };
                    for (var i = 0; i < allStar.length; i++) {
                        if(allStar.eq(i).data('c') != ""){
                            allStar.eq(i).addClass(allStar.eq(i).data('c'));
                        }
                    };
                });
                $(ele).click(function(){
                    //得分结果
                    var ratingScore = $(this).data('score');
                    var ratingId = $(this).data('id');
                    var ratingLevel = $(this).data('level');
                    var ratingCid = $(this).data('cid');

                    if(ratingScore > 0){
                        rating_tips_green.show().siblings('div').hide();
                        rating_tips_green.find('.ratingNum').text(ratingScore);
                    }else{
                        rating_tips_red.show().siblings('div').hide();
                        rating_tips_red.find('.ratingNum').text(ratingScore);
                    }
                    $("#description_id"+ratingCid).val(ratingId);
                    $("#description_level"+ratingCid).val(ratingLevel);
                    
                    $(allList[inde]).siblings('.scoreVal').val(ratingScore);
                    var thisStar = $(this).index();
                    allStar.removeClass().data('c', '');
                    forData(starlist,thisStar);
                    //点击效果
                    $(this).before('<li class="act starscale">★</li>');
                    var starscale = $(allList[inde]).find('.starscale');
                    starscale.css({
                        left: $(this).offset().left - $(document).scrollLeft(),
                        top: $(this).offset().top - $(document).scrollTop()
                    }).addClass('scale');
                    setTimeout(function(){starscale.remove()},200)


                });
            });
        }
        eachStar(starLiPlus);
        eachStar(starLiMinus);

        //重置按钮
        $('.reset-s').eq(inde).click(function(){
            // if(confirm('确定要重置吗？')){
                rating_tips_default.show().siblings('div').hide();      //下面的文字提示
                allStar.removeClass().data('c','');                     //所有的星星class和data
                $('.textarea_class').eq(inde).val('');                  //文本框的内容
                $(allList[inde]).siblings('.scoreVal').val('');         //隐藏域的值
            // }
        })
    });
}