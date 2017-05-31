/**
  * Created by zdw on 2014/12/23.
 */
    //表单验证

//--------------------------------------------


function validForm(thisobj){

    $("#FormView").Validform({
        tiptype:function(msg,o,cssctl){
            var tipmsg=msg;
            obj = o.obj;

            if(typeof($(obj).attr('tipfor')) != 'undefined')
            {
                //用于多属性验证
                oldobj = obj;
                obj = $('#'+$(obj).attr('tipfor'));
                if(typeof($(oldobj).attr('nullmsg')) != 'undefined'){
                    if(o.type==3){
                        tipmsg = $(oldobj).attr('nullmsg');
                    }else if(o.type==2){
                        var uncount = 0;
                        tipforInput = $('#'+$(obj).attr('parentdiv')).find('input[tipfor]');
                        for(var i=0 ; i < tipforInput.length; i++ )
                        {
                            if($(tipforInput[i]).val() == '')
                            {
                                ++uncount;
                            }
                        }
                        if(uncount > 0){
                            if($(oldobj).val()!="")
                            {
                                return ;
                            }
                        }

                    }
                }
            }

            switch (o.type){
                case 1:
                {
                    $('#ajaxTips').html("正在提交数据，请耐心等待......");
                };break;
                case 2:
                {
                    //校验成功
                    var objprev = $(obj).prev();
                    if($(objprev).hasClass('parentFormformID')){
                        $(obj).parent().find('div.reqformError').each(function(i){
                            $(this).remove();
                        });

                        $(obj).removeClass('Validform_error');
                    }
                };break;
                case 3:
                {
                        //表单校验失败
                        msgobj = $(obj).parent().find('div.formErrorContent');

                        if($(msgobj).length < 1){
                            var position = methods.getposition(obj);
                            var msgcontent = '<div style="left: '+position.left+'; top: '+position.top+'; position: absolute; opacity: 0.87;" class="reqformError parentFormformID formError"><div class="formErrorContent">'+tipmsg+'<br /></div><div class="formErrorArrow"><div class="line10"></div><div class="line9"><!-- --></div><div class="line8"><!-- --></div><div class="line7"><!-- --></div><div class="line6"><!-- --></div><div class="line5"><!-- --></div><div class="line4"><!-- --></div><div class="line3"><!-- --></div>	<div class="line2"><!-- --></div></div></div>';
                            $(obj).before(msgcontent);

                            var objprev = $(obj).prev();

                            objprev.css('margin-top','-'+(objprev.height()-15)+'px');
                            objprev.bind('click',function(){
                                $(this).remove();
                            });

                        }else{
                            $(msgobj).html(msg);
                        }

                };break;
                default :
                {

                }

            }

        },
        showAllError:true,
        ajaxPost:true,
        callback:function(data){
            $('#ajaxTips').html('');
            //提示
            if(typeof(data.callaction) != 'undefined'){
                switch (data.callaction){
                    case 'edit':methods.edit(data);break;
                    case 'login':methods.login(data);break;
                    case 'refresh':methods.refresh(data);break;
                    case 'del':methods.del(data);break;
                    case 'add':methods.add(data);break;
                }
            if(data.status !=200)
            {
                $.dialog.tips(data.remsg);
            }

            }else{
                $('#ajaxTips').html(data);
            }
        }
    });


    var methods = {
        //表单验证提示
        getposition:function(obj){
            var position = $(obj).position();
            var width = $(obj).width();
            var height = $(obj).height();
            position.left = (position.left+width-30)+'px';
            if($(obj).is("textarea")){
                var rows = $(obj).attr('rows');
                var tempH = 15;
                if(rows > 0){
                    tempH = rows*15;
                }else{
                    tempH = 30;
                }
                position.top = (position.top-height+tempH+5)+'px';
            }else{
                position.top = (position.top-height+5)+'px';
            }
            return position;
        },

        //登录操作
        login:function(data){

            $('#ajaxTips').html(data.remsg);

            switch (data.status){
                case '200':{
                    window.location.href = data.rediretUrl;
                };break;
                default :{

                };break;
            }
        },

        edit:function(data){

            if(data.status == 200){

                $("#content_list").children().each(function(){
                    if(this.id.substr(4)==data.remsg.id){
                        $(this).before(data.remsg.liststr);
                        $(this).remove();
                    }
                });

                thisobj.close();
                $.dialog.tips(data.remsg.tips);

            }else{
                $.dialog.tips(data.remsg);
            }
        },

        del:function(data){

            if(data.status==200){
                //alert($.inArray('5',data.ids));
                $("#content_list").children().each(function(){
                    if($.isArray(data.ids)){
                        if($.inArray(this.id.substr(4),data.ids)>=0){
                            $(this).remove();
                        }
                    }else{
                        if(this.id.substr(4)==data.ids){
                            $(this).remove();
                        }
                    }
                });
                thisobj.close();
                $.dialog.tips("删除成功!");
            }else{
                thisobj.close();
                showmsg(data);
            }
        },
        //新增
        add: function (data) {

            if(data.status == 200)
            {
                $('#content_list').prepend(data.remsg.liststr);
                thisobj.close();
                $.dialog.tips(data.remsg.tips);

            }else{
                $.dialog.tips(data.remsg);
            }
        },

        refresh:function(data){
            if(data.status == 200)
            {
                $('#ajaxTips').html("保存成功，页面正在刷新中...");
                window.location.reload();
            }else{
                $.dialog.tips(data.remsg);
            }
        }

    }

}
