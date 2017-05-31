<!-- 拇指评分 -->
<script src='/public/js/rating.js' type="text/javascript"></script>
<div id="wrap">
    <div class="outer">
        <div id="titlebar">
            <div id="main">您正在给&nbsp;<font color="blue"><?php echo $rated_user['real_name']?></font>&nbsp;进行正负能量评分:
            <span style="margin-left:26px;"><input type="button" value="保存所有" onclick="submitall();" class="button-s"/>&nbsp;&nbsp;<input type="button" value="返回" onclick="javascript:window.location.href='/ratting/userlist'" class="button-s"/></span>
            </div>
        </div>
        <input type="hidden" value="<?php echo $param['type']?>" id="type_sel"/>
        <input type="hidden" value="<?php echo $param['id']?>" id="id_sel"/>
        <form method="post" action="/ratting/rat_index" id="submit_form">
        <input type="hidden" name="real_name" id="real_name" value="<?php echo $rated_user['real_name']?>" />
        <input type="hidden" name="rated_account" id="rated_account"  value="<?php echo $rated_user['account']?>" />
        <input type="hidden" name="uid" id="uid" value="<?php echo $rated_user['id']?>" />
            <span style="font-weight:bold;">&nbsp;评分名称:</span>
            <select name="level" id="level" onchange="change();" onclick="onselect_opt(this);">
                <option value=''>请选择</option>
                <?php foreach($ratting_sets as $k => $v):?>
                <option value="<?php echo $k?>" <?php if($param['level'] == $k){?>selected<?php }?>><?php echo $v['title']?></option>
                <?php endforeach;?>  
            </select>
            <select name="type" id="type" onchange="changetype('');" onclick="onselect_opt(this);">
                <option value=''>请选择</option>
            </select>
            <select name="id" id="content_id" onchange="submit_form();">
                <option value=''>请选择</option>
            </select>&nbsp;&nbsp;&nbsp;
        </form>
        <br/>
            <?php foreach($ratting_lists as $key => $val){?>

            <div class="titlebg" id="titlebar" style="font-size: 20px;">
                <div id="main"><?php echo $val['title']?></div>
            </div>
            <table class="cont-rt5">
                <tbody>
                    <tr valign="top">
                        <td>
                            <?php foreach($val['child'] as $ke => $va){?>
                            <?php if(empty($va['child'])) continue;?>
                                <fieldset>
                                    <legend style="font-size: 14px;"><?php echo $va['title']?></legend>
                                    <!-- <div id="divider"></div> -->
                                    <?php foreach($va['child'] as $k => $v){?>
                                        <input type="hidden" name="content_id[]" value="<?php echo $v['id']?>" id="content_id<?php echo $v['id']?>"/>
                                        <input type="hidden" name="content_type[]" value="<?php echo $v['type']?>" id="content_type<?php echo $v['id']?>"/>
                                        <input type="hidden" name="content_name[]" value="<?php echo $v['content']?>" id="content_name<?php echo $v['id']?>"/>
                                        <input type="hidden" name="description_id[]" value="0" id="description_id<?php echo $v['id']?>"/>
                                        <input type="hidden" name="description_level[]" id="description_level<?php echo $v['id']?>"/>
                                            <div class="content infodiv" style="font-weight:bold;color: #000;"><?php echo $v['content']?></div>
                                            <div id="quality" class="content infodiv">
                                                <div class="quality_setting">
                                                    <p>
                                                        我的评价：
                                                    </p>
                                                    <!-- 拇指评分 -->                                                    
                                                    <div class="starBox">
                                                        <ul class="starMinus star">
                                                            <?php for($i = $v['description'][1]['end_value'];$i>=$v['description'][1]['start_value'];$i--){?>
                                                            <li data-score="<?php echo $i?>" data-id="<?php echo $v['description'][1]['id']?>" data-level="<?php echo $v['description'][1]['level']?>" data-cid="<?php echo $v['id']?>" title="<?php echo $i?>"><?php echo $i?></li>
                                                            <?php }?>
                                                        </ul>
                                                        <ul class="starPlus star">
                                                            <?php for($i = $v['description'][0]['start_value'];$i<=$v['description'][0]['end_value'];$i++){?>
                                                            <li data-score="<?php echo $i?>" data-id="<?php echo $v['description'][0]['id']?>" data-level="<?php echo $v['description'][0]['level']?>" data-cid="<?php echo $v['id']?>" title="<?php echo $i?>"><?php echo $i?></li>
                                                            <?php }?>
                                                        </ul>
                                                    </div>
                                                    <div class="rating_tips">
                                                        <div class="rating_tips_default">
                                                           <p><?php echo $v['description'][0]['desc']?> (+<?php echo $v['description'][0]['start_value']?>至+<?php echo $v['description'][0]['end_value']?>分)</p> 
                                                            <p><?php echo $v['description'][1]['desc']?> (<?php echo $v['description'][1]['start_value']?>至<?php echo $v['description'][1]['end_value']?>分)</p> 
                                                        </div>
                                                        <div class="rating_tips_green">
                                                            <p><span class="ratingNum"></span>分&nbsp&nbsp  <?php echo $v['description'][0]['desc']?> (+<?php echo $v['description'][0]['start_value']?>至+<?php echo $v['description'][0]['end_value']?>分)</p>
                                                        </div>
                                                        <div class="rating_tips_red">
                                                            <p><span class="ratingNum"></span>分&nbsp&nbsp  <?php echo $v['description'][1]['desc']?> (<?php echo $v['description'][1]['start_value']?>至<?php echo $v['description'][1]['end_value']?>分)</p>
                                                        </div>
                                                    </div>
                                                    <input type="hidden" class="scoreVal" name="grade[]" id="grade<?php echo $v['id']?>"/>
                                                    <br/>
                                                    <p>
                                                        评分事件：<textarea cols="50" rows="5" name="rating_desc[]" class="textarea_class" id="rating_desc<?php echo $v['id']?>" placeholder="必须写出评价的具体事件，否则审核不通过。" ></textarea>
                                                    </p>
                                                </div>
                                            </div>
                                        <div class="actionlink" style="margin-left:200px;">
                                        <input class="button-s" value="保存" type="button" onclick="submitdata('<?php echo $v['id']?>');" id="button<?php echo $v['id']?>">
                                        <button class="reset-s" id="reset<?php echo $v['id']?>">重置</button>
                                        </div>
                                        <br/>
                                    <?php }?>
                                </fieldset>
                            <?php }?>
                        </td>
                    </tr>
                </tbody>
            </table> 
            <?php }?>
    </div>
    <div id="divider"></div>
</div>
<script type="text/javascript">
function change(){
    $('#type').html('<option value="">请选择</option>');
    $('#content_id').html('<option value="">请选择</option>');
    var value = $("#level").val();
    var type_sel = $("#type_sel").val();
    var selected = '';
    if(value != ''){
        $.ajax({
           type: "POST",
           url: "/ratting/get_types",
           dataType:"json",
           data: "key="+value,
           success: function(msg){
                if(!jQuery.isEmptyObject(msg)){
                    var option = '';
                    for(x in msg){
                        if(x == type_sel){
                            selected = 'selected';
                        }else{
                            selected = '';
                        }
                        option += "<option value="+x+" "+selected+">"+msg[x]+"</option>";
                    }
                    $('#type').append(option);
                    if(type_sel != ''){
                        changetype(type_sel);
                        $("#type_sel").val('');
                    }
                }else{
                    $('#type').html('<option value="">请选择</option>');
                    $('#content_id').html('<option value="">请选择</option>');
                }
           }
        });
    }else{
        $('#type').html('<option value="">请选择</option>');
        $('#content_id').html('<option value="">请选择</option>');
    }
}
function changetype(value){
    if(value == ''){
        value = $("#type").val();
    }
    var content_id = $("#id_sel").val();
    var selected = '';
    if(value != ''){
        $.ajax({
           type: "POST",
           url: "/ratting/get_types",
           dataType:"json",
           data: "type="+value,
           success: function(msg){
                if(!jQuery.isEmptyObject(msg)){
                    var option = '';
                    for(x in msg){
                        if(msg[x].id == content_id){
                            selected = 'selected';
                        }else{
                            selected = '';
                        }
                        option += "<option value="+msg[x].id+" "+selected+">"+msg[x].content+"</option>";
                    }
                    $('#content_id').html('<option value="">请选择</option>');
                    $('#content_id').append(option);
                    $("#id_sel").val('');
                }else{
                    $('#content_id').html('<option value="">请选择</option>');
                }
           }
        });
    }else{
        $('#content_id').html('<option value="">请选择</option>');
    }
}
function get_desc_id(obj,id,level){
    var value = $(obj).val();
    $("#description_id"+id).val(value);
    $("#description_level"+id).val(level);
}
function submitdata(id){
    var real_name = $("#real_name").val();
    var uid = $("#uid").val();
    var rated_account = $("#rated_account").val();
    var content_id = $("#content_id"+id).val();
    var content_type = $("#content_type"+id).val();
    var description_id = $("#description_id"+id).val();

    var content_name = $("#content_name"+id).val();
    var description_level = $("#description_level"+id).val();

    var grade = $("#grade"+id).val();
    var rating_desc = $("#rating_desc"+id).val();
    $.ajax({
       type: "POST",
       dataType:"json",
       url: "/ratting/save_ratting_single",
       data: "content_name="+content_name+"&description_level="+description_level+"&content_id="+content_id+"&content_type="+content_type+"&description_id="+description_id+"&grade="+grade+"&rating_desc="+rating_desc+"&real_name="+real_name+"&uid="+uid+"&rated_account="+rated_account,
       success: function(msg){
            if(msg.flag == 1){
                layer.alert('本月加分剩余:'+msg.last_plus+'&nbsp;&nbsp;&nbsp;&nbsp;本月减分剩余:'+msg.last_minus, 9,'保存成功');
                $("#reset"+id).click();
            }else if(msg.flag == 0){
                layer.alert('本月加分剩余:'+msg.last_plus+'&nbsp;&nbsp;&nbsp;&nbsp;本月减分剩余:'+msg.last_minus, 8,'保存失败');
            }else{
                layer.alert(msg.info, 2,'提示信息');
            }
            $("#button"+id).removeAttr("disabled");
       }
    });
}
function submitall(){
    var real_name = $("#real_name").val();
    var uid = $("#uid").val();
    var rated_account = $("#rated_account").val();
    var content_id =  $("input[name='content_id[]']").serialize();
    var content_type =  $("input[name='content_type[]']").serialize();
    var description_id =  $('input[name="description_id[]"]').serialize();

    var content_name =  $("input[name='content_name[]']").serialize();
    var description_level =  $('input[name="description_level[]"]').serialize();

    var grade =  $('input[name="grade[]"]').serialize();
    var rating_desc =  $('textarea').serialize();
    $.ajax({
        type: "POST",
	dataType:"json",
        url: "/ratting/save_ratting_all",
        data: content_name+'&'+description_level+'&'+content_id+'&'+content_type+'&'+description_id+'&'+grade+'&'+rating_desc+'&'+real_name+'&'+uid+"&real_name="+real_name+"&uid="+uid+"&rated_account="+rated_account,
        success: function(msg){
            if(msg.flag == 1){
                 layer.alert('本月加分剩余:'+msg.last_plus+'&nbsp;&nbsp;&nbsp;&nbsp;本月减分剩余:'+msg.last_minus, 9,'保存成功',function(){window.location.reload();});
            }else{
                layer.alert('本月加分剩余:'+msg.last_plus+'&nbsp;&nbsp;&nbsp;&nbsp;本月减分剩余:'+msg.last_minus, 8,'保存失败');
            }
        }
    });
}
$(document).ready(function(){
    change();
});
function submit_form(){
    $('#submit_form').submit();
}

var isSelect=true;
function onselect_opt(V){
    var selectedOption=V.options[V.selectedIndex];
    if(V.value==selectedOption.value){
        isSelect=!isSelect;
    }else{
        isSelect=true; 
    }
    if(isSelect==true){
        $('#submit_form').submit();
    }  
}
</script>