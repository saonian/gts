issencond = false;
jQuery(function ($) {

    $('.easy-pie-chart.percentage').each(function () {
        var $box = $(this).closest('.infobox');
        var barColor = $(this).data('color') || (!$box.hasClass('infobox-dark') ? $box.css('color') : 'rgba(255,255,255,0.95)');
        var trackColor = barColor == 'rgba(255,255,255,0.95)' ? 'rgba(255,255,255,0.25)' : '#E2E2E2';
        var size = parseInt($(this).data('size')) || 50;
        $(this).easyPieChart({
            barColor: barColor,
            trackColor: trackColor,
            scaleColor: false,
            lineCap: 'butt',
            lineWidth: parseInt(size / 10),
            animate: /msie\s*(8|7|6)/.test(navigator.userAgent.toLowerCase()) ? false : 1000,
            size: size
        });
    })

    $('.sparkline').each(function () {
        var $box = $(this).closest('.infobox');
        var barColor = !$box.hasClass('infobox-dark') ? $box.css('color') : '#FFF';
        $(this).sparkline('html',
            {
                tagValuesAttribute: 'data-values',
                type: 'bar',
                barColor: barColor,
                chartRangeMin: $(this).data('min') || 0
            });
    });



})

$(document).on('click', 'th input:checkbox' , function(){
    var that = this;
    $(this).closest('table').find('tr > td:first-child input:checkbox')
        .each(
        function(){
            this.checked = that.checked;
            $(this).closest('tr').toggleClass('selected');
        });
});


$(document).on('click', 'td .shouquan input:checkbox[action="left"]' , function(){
    var that = this;
    $(this).closest('table').find('tr > td:first-child input:checkbox')
        .each(
        function(){
            this.checked = that.checked;
            $(this).closest('tr').toggleClass('selected');
        });
});



$(document).on('click', 'td .shouquan input:checkbox[action="right"]' , function(){

    var that = this;
    $(this).closest('table').find('tr > td[flag="second"] input:checkbox')
        .each(
        function(){
            this.checked = that.checked;
            $(this).closest('tr').toggleClass('selected');
        });
});


//添加
function  ajaxForm(url,title){
    $.dialog({title:title,lock:true,
        content:"页面努力加载中，请稍等片刻！",
        init:function(){
            var thisobj = this;
            $.ajax({
                type: "GET",
                url: url,
                dataType: "json",
                cache:false,
                success: function(data){

                    if(data.status==200){

                        var editors = new Array();
                        thisobj.content(data.remsg);
                        validForm(thisobj);

                    }else{

                        thisobj.close();
                        $.dialog.tips(data.remsg);

                    }
                }
            });
        }
    });
}


function ajaxDel(url,ismultiple,tid){
    var data;
    $.dialog.confirm('确定要删除吗', function(){
        if(ismultiple){
            data = $("#content_list").find("input:checked").serialize();
        }else{
            data = "optid="+tid;
        }
        if(data==""){
            $.dialog.tips('请选择要删除的选项!');
            return;
        }
        $.dialog({title:'删除',lock:true,
            content:"数据努力处理中，请稍等片刻！",
            init:function(){
                var thisobj = this;
                $.ajax({
                    type: "POST",
                    url: url,
                    dataType: "json",
                    data: data,
                    success: function(data){
                        if(data.status==200){
                            $("#content_list").children().each(function(){
                                if($.isArray(data.ids)){
                                    if($.inArray(this.id.substr(4),data.ids)>=0){
                                            $(this).remove();

                                    }
                                }else{
                                    if(this.id.substr(4)==data.ids){
                                        $(this).fadeOut(1000,function(){
                                            $(this).remove();
                                        });
                                    }
                                }
                            });
                            thisobj.close();
                            $.dialog.tips("删除成功!");
                        }else{
                            thisobj.close();
                            $.dialog.tips(data.remsg);
                        }
                    }
                });
            }
        });
    }, function(){
        $.dialog.tips('取消删除');
    });
}

//ajax排序
function ajaxOrder(url,ismultiple,tid){
    var data;

    if(ismultiple){
        data = $("#content_list input[order='listorder']").serialize();
    }else{
        data = "optid="+tid;
    }
    if(data==""){
        $.dialog.tips('请选择要排序的选项!');
        return;
    }
    $.dialog({title:'排序',lock:true,
        content:"数据努力处理中，请稍等片刻！",
        init:function(){
            var thisobj = this;
            $.ajax({
                type: "POST",
                url: url,
                dataType: "json",
                data: data,
                success: function(data){
                    if(data.status==200){

                        thisobj.close();
                        $.dialog.tips("排序成功!");
                        window.location.reload();

                    }else{
                        thisobj.close();
                        $.dialog.tips(data.remsg);
                    }
                }
            });
        }
    });
}

//ajax上传
function ajaxFileUpload(inputObj,imgObj,uploadurl) {

    $.ajaxFileUpload
    ({
        url: uploadurl, //用于文件上传的服务器端请求地址
        secureuri: false, //是否需要安全协议，一般设置为false
        fileElementId: 'uploadfile', //文件上传域的ID
        dataType: 'json', //返回值类型 一般设置为json
        success: function (data)  //服务器成功响应处理函数
        {
            if(data.status == 200 ){

                $(imgObj).attr("src", data.remsg.fullimg);
                $(inputObj).val(data.remsg.imgpath);
            }else{

                $.dialog.tips(data.remsg);

            }
        }
    });

    return false;

}

function uploadFile(imgObj,uploadurl){
    inputObj = $(imgObj).parent().find('input[_postfield="1"]')[0];
    //alert($(inputObj).val());
    $('#uploadfile').change(function(){
        ajaxFileUpload(inputObj,imgObj,uploadurl)
    });
    $('#uploadfile').click();
}