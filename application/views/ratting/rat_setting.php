<input type="hidden" id="month_input" value="<?php echo $params['month']?>"/>
<script type="text/javascript" src="/public/js/grade_setting.js"></script>
<div id="wrap">
    <div class="outer">
        <form id="projectStoryForm" method="get" action="/ratting/ratsetting">
          <table style="width:100%;" class="table-2">
            <tr>
              <td>
                <label>&nbsp;评分时间:</label>
                <select name="year" id="year" onchange="get_month();">
                <?php for($i=$year;$i>=2014;$i--){?>
                    <option value="<?php echo $i;?>" <?php if($params['year']== $i){echo 'selected';}?> ><?php echo $i;?></option>
                <?php }?>
                </select>
                <select name="month" id="month">
                </select>&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="submit" class="button-s" value="搜索" style="width:80px;"/>
              </td>
            </tr>
          </table>
        </form>
        <form id="dataform" method="post" action="/ratting/ratsetting">
        <input type="hidden" name="year" value="<?php echo $params['year']?>"/>
        <input type="hidden" name="month" value="<?php echo $params['month']?>"/>
        <div class="titlebg" id="titlebar">
            <div id="main">设定初始分值</div>
        </div>
        <table class="cont-rt5" style="margin-left:20px;">
            <tbody>
            <tr>
                <td width="140px;"><strong>管理用户：</strong></td>
                <td></td>
                <td width="120px;"><strong>普通用户：</strong></td>
                <td></td>
            </tr>
            <tr>
                <td><strong>月度初始加分分值</strong></td>
                <td><input type="text" name="manage_plus" class="text-3" <?php if(!empty($grade_set['manage_plus'])){?>value="<?php echo $grade_set['manage_plus']?>"<?php }else{?>value="100"<?php }?>/> 分</td>
                <td><strong>月度初始加分分值</strong></td>
                <td><input type="text" name="common_plus" class="text-3" <?php if(!empty($grade_set['common_plus'])){?>value="<?php echo $grade_set['common_plus']?>"<?php }else{?>value="50"<?php }?>/> 分</td>
            </tr>
             <tr>
                <td><strong>月度初始减分分值</strong></td>
                <td><input type="text" name="manage_minus" class="text-3" <?php if(!empty($grade_set['manage_minus'])){?>value="<?php echo $grade_set['manage_minus']?>"<?php }else{?>value="100"<?php }?>/> 分</td>
                <td><strong>月度初始减分分值</strong></td>
                <td><input type="text" name="common_minus" class="text-3" <?php if(!empty($grade_set['common_minus'])){?>value="<?php echo $grade_set['common_minus']?>"<?php }else{?>value="50"<?php }?>/> 分</td>
            </tr>
            <tr>
                <td><strong>被评得分延迟显示天数</strong></td>
                <td><input type="text" name="delay_days" class="text-3" <?php if(!empty($grade_set['delay_days'])){?>value="<?php echo $grade_set['delay_days']?>"<?php }else{?>value="3"<?php }?>/> 天</td>
                <td><strong>&nbsp;</strong></td>
                <td>&nbsp;</td>
            </tr>
            <div class="divider"></div>
            </tbody>
        </table>
        <br/><br/>
        <?php foreach($ratting_lists as $key => $val){?>
            <div class="titlebg" id="titlebar">
                <div id="main"><?php echo $val['title']?></div>
            </div>
            <table class="cont-rt5">
                <tbody>
                <tr valign="top">
                    <td>
                        <?php foreach($val['child'] as $ke => $va){?>
                            <fieldset>
                                <legend><?php echo $va['title']?></legend>
                                <div id="<?php echo $va['type']?>" class="content infodiv">
                                    <p class="pb15">
                                        <input type="button" value=" 添加评价内容 " class="button-c" onclick="add_setting('<?php echo $va['type']?>');">
                                    </p>
                                    <?php if(!empty($va['child'])):?>
                                    <?php foreach($va['child'] as $k => $v):?>
                                    <div class="<?php echo $va['type']?>_setting">
                                        <p>
                                            评价内容
                                            <input type="hidden" name="setting_<?php echo $va['type']?>_id[]" value="<?php echo $v['id'];?>"/>
                                            <input type="text" name="<?php echo $va['type']?>_content[]" id="" value="<?php echo empty($v['content'])?'':$v['content'];?>" class="text-3">
                                            <input type="button" value=" 删除 " class="button-c" onclick="del_setting('<?php echo $va['type']?>',this)" />
                                            <span style="color:red;" id="msg_alert"></span>
                                        </p>
                                        <table width="80%" cellspacing="1" cellpadding="1" border="1"  style="background-color:#f8f8f8;" class="infotable">
                                            <tbody><tr>
                                                <td align="center"><strong>评价说明</strong></td>
                                                <td align="center"><strong>区间分值</strong></td>
                                                <td width="20%" align="center"><strong>评语是否必填</strong></td>
                                                <td align="center"><strong>操作</strong></td>
                                            </tr>
                                            <?php if(!empty($v['description'])):?>
                                                <?php foreach ($v['description'] as $keyword => $value):?>
                                                <tr>
                                                    <input type="hidden" name="setting_<?php echo $va['type']?>_description_id[<?php echo $k;?>][]" value="<?php echo $value['id'];?>"/>
                                                    <td bgcolor="#fff" align="center"><select name="<?php echo $va['type']?>_level[<?php echo $k;?>][]"><option value="好" <?php if($value['level'] == '好') echo 'selected' ?> >好</option><!--<option value="中" <?php if($value['level'] == '中') echo 'selected' ?>>中</option>--><option value="差" <?php if($value['level'] == '差') echo 'selected' ?> >差</option></select><input type="text" name="<?php echo $va['type']?>_desc[<?php echo $k;?>][]" id="" value="<?php echo $value['desc'];?>" class="text-4"></td>
                                                    <td bgcolor="#fff" align="center"><input type="text" name="<?php echo $va['type']?>_start_value[<?php echo $k;?>][]" id="" value="<?php echo $value['start_value'];?>" class="text-2" style="width:55px;text-align:center;" />&nbsp;-&nbsp;<input type="text" name="<?php echo $va['type']?>_end_value[<?php echo $k;?>][]" id="" value="<?php echo $value['end_value'];?>" class="text-2" style="width:55px;text-align:center;" /></td>
                                                    <td bgcolor="#fff" align="center"><input type="hidden" name="<?php echo $va['type']?>_reviews_required[<?php echo $k;?>][]" value="<?php echo $value['review_required']?>"/><input type="checkbox" class="req_chkbox" value="1" <?php echo $value['review_required']==1?'checked':'';?>/></td>
                                                    <td bgcolor="#fff" align="center"><input type="button" value=" 添加 " class="button-c" onclick="add_desc('<?php echo $va['type']?>',this);"></td>
                                                    <td bgcolor="#fff" align="center"><input type="button" value=" 删除 " class="button-c" onclick="del_desc(this);"></td>
                                                </tr>
                                                <?php endforeach;?>
                                            <?php else:?>
                                            <tr>
                                                <td bgcolor="#fff" align="center"><input type="text" name="<?php echo $va['type']?>_desc[<?php echo $k;?>][]" id="" value="" class="text-4"></td>
                                                <td bgcolor="#fff" align="center"><select name="<?php echo $va['type']?>_level[<?php echo $k;?>][]"><option value="好" selected >好</option><!--<!--<option value="中">中</option>-->--><option value="差">差</option></select><input type="text" name="<?php echo $va['type']?>_start_value[<?php echo $k;?>][]" id="" class="text-2" style="width:55px;text-align:center;" />&nbsp;-&nbsp;<input type="text" name="<?php echo $va['type']?>_end_value[<?php echo $k;?>][]" id="" class="text-2" style="width:55px;text-align:center;" /></td>
                                                <td bgcolor="#fff" align="center"><input type="hidden" name="<?php echo $va['type']?>_reviews_required[<?php echo $k;?>][]" value="1"/><input type="checkbox" class="req_chkbox" value="1" checked/></td>
                                                <td bgcolor="#fff" align="center"><input type="button" value=" 添加 " class="button-c" onclick="add_desc('<?php echo $va['type']?>',this);"></td>
                                                <td bgcolor="#fff" align="center"><input type="button" value=" 删除 " class="button-c" onclick="del_desc(this);"></td>
                                            </tr>
                                            <tr>
                                                <td bgcolor="#fff" align="center"><input type="text" name="<?php echo $va['type']?>_desc[<?php echo $k;?>][]" id="" value="" class="text-4"></td>
                                                <td bgcolor="#fff" align="center"><select name="<?php echo $va['type']?>_level[<?php echo $k;?>][]"><option value="好">好</option><option value="中" selected >中</option><option value="差">差</option></select><input type="text" name="<?php echo $va['type']?>_start_value[<?php echo $k;?>][]" id="" class="text-2" style="width:55px;text-align:center;" />&nbsp;-&nbsp;<input type="text" name="<?php echo $va['type']?>_end_value[<?php echo $k;?>][]" id="" class="text-2" style="width:55px;text-align:center;" /></td>
                                                <td bgcolor="#fff" align="center"><input type="hidden" name="<?php echo $va['type']?>_reviews_required[<?php echo $k;?>][]" value="1"/><input type="checkbox" class="req_chkbox" value="1" checked/></td>
                                                <td bgcolor="#fff" align="center"><input type="button" value=" 添加 " class="button-c" onclick="add_desc('<?php echo $va['type']?>',this);"></td>
                                                <td bgcolor="#fff" align="center"><input type="button" value=" 删除 " class="button-c" onclick="del_desc(this);"></td>
                                            </tr>
                                            <tr>
                                                <td bgcolor="#fff" align="center"><input type="text" name="<?php echo $va['type']?>_desc[<?php echo $k;?>][]" id="" value="" class="text-4"></td>
                                                <td bgcolor="#fff" align="center"><select name="<?php echo $va['type']?>_level[<?php echo $k;?>][]"><option value="好">好</option><!--<option value="中">中</option>--><option value="差" selected>差</option></select><input type="text" name="<?php echo $va['type']?>_start_value[<?php echo $k;?>][]" id="" class="text-2" style="width:55px;text-align:center;" />&nbsp;-&nbsp;<input type="text" name="<?php echo $va['type']?>_end_value[<?php echo $k;?>][]" id="" class="text-2" style="width:55px;text-align:center;" /></td>
                                                <td bgcolor="#fff" align="center"><input type="hidden" name="<?php echo $va['type']?>_reviews_required[<?php echo $k;?>][]" value="1"/><input type="checkbox" class="req_chkbox" value="1" checked/></td>
                                                <td bgcolor="#fff" align="center"><input type="button" value=" 添加 " class="button-c" onclick="add_desc('<?php echo $va['type']?>',this);"></td>
                                                <td bgcolor="#fff" align="center"><input type="button" value=" 删除 " class="button-c" onclick="del_desc(this);"></td>
                                            </tr>
                                            <?php endif;?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php endforeach;?>
                                    <?php else:?>
                                    <div class="<?php echo $va['type']?>_setting">
                                        <p>
                                            评价内容
                                            <input type="text" name="<?php echo $va['type']?>_content[]" id="" value="" class="text-3">
                                            <input type="button" value=" 删除 " class="button-c" onclick="del_setting('<?php echo $va['type']?>',this)" />
                                        </p>
                                        <table width="80%" cellspacing="1" cellpadding="1" border="1" style="background-color:#f8f8f8;" class="infotable">
                                            <tbody><tr>
                                                <td align="center"><strong>评价说明</strong></td>
                                                <td align="center"><strong>区间分值</strong></td>
                                                <td width="20%" align="center"><strong>评语是否必填</strong></td>
                                                <td align="center"><strong>操作</strong></td>
                                            </tr>
                                            <tr>
                                                <td bgcolor="#fff" align="center"><select name="<?php echo $va['type']?>_level[0][]"><option value="好" selected >好</option><!--<option value="中">中</option>--><option value="差">差</option></select><input type="text" name="<?php echo $va['type']?>_desc[0][]" id="" value="" class="text-4"></td>
                                                <td bgcolor="#fff" align="center"><input type="text" name="<?php echo $va['type']?>_start_value[0][]" id="" value="" class="text-2" style="width:55px;text-align:center;">&nbsp;-&nbsp;<input type="text" name="<?php echo $va['type']?>_end_value[0][]" id="" value="" class="text-2" style="width:55px;text-align:center;"></td>
                                                <td bgcolor="#fff" align="center"><input type="hidden" name="<?php echo $va['type']?>_reviews_required[0][]" value="1"/><input type="checkbox" class="req_chkbox" value="1" checked/></td>
                                                <td bgcolor="#fff" align="center"><input type="button" value=" 添加 " class="button-c" onclick="add_desc('<?php echo $va['type']?>',this);" /></td>
                                                <td bgcolor="#fff" align="center"><input type="button" value=" 删除 " class="button-c" onclick="del_desc(this);"/></td>
                                            </tr>
                                            <tr>
                                                <td bgcolor="#fff" align="center"><select name="<?php echo $va['type']?>_level[0][]"><option value="好" >好</option><option value="中" selected >中</option><option value="差">差</option></select><input type="text" name="<?php echo $va['type']?>_desc[0][]" id="" value="" class="text-4"></td>
                                                <td bgcolor="#fff" align="center"><input type="text" name="<?php echo $va['type']?>_start_value[0][]" id="" value="" class="text-2" style="width:55px;text-align:center;">&nbsp;-&nbsp;<input type="text" name="<?php echo $va['type']?>_end_value[0][]" id="" value="" class="text-2" style="width:55px;text-align:center;">
                                                <td bgcolor="#fff" align="center"><input type="hidden" name="<?php echo $va['type']?>_reviews_required[0][]" value="1"/><input type="checkbox" class="req_chkbox" value="1" checked/></td>
                                                <td bgcolor="#fff" align="center"><input type="button" value=" 添加 " class="button-c" onclick="add_desc('<?php echo $va['type']?>',this);"/></td>
                                                <td bgcolor="#fff" align="center"><input type="button" value=" 删除 " class="button-c" onclick="del_desc(this);"/></td>
                                            </tr>
                                            <tr>
                                                <td bgcolor="#fff" align="center"><select name="<?php echo $va['type']?>_level[0][]"><option value="好">好</option><!--<option value="中">中</option>--><option value="差" selected >差</option></select><input type="text" name="<?php echo $va['type']?>_desc[0][]" id="" value="" class="text-4"></td>
                                                <td bgcolor="#fff" align="center"><input type="text" name="<?php echo $va['type']?>_start_value[0][]" id="" value="" class="text-2" style="width:55px;text-align:center;">&nbsp;-&nbsp;<input type="text" name="<?php echo $va['type']?>_end_value[0][]" id="" value="" class="text-2" style="width:55px;text-align:center;">
                                                <td bgcolor="#fff" align="center"><input type="hidden" name="<?php echo $va['type']?>_reviews_required[0][]" value="1"/><input type="checkbox" class="req_chkbox" value="1" checked/></td>
                                                <td bgcolor="#fff" align="center"><input type="button" value=" 添加 " class="button-c" onclick="add_desc('<?php echo $va['type']?>',this);"/></td>
                                                <td bgcolor="#fff" align="center"><input type="button" value=" 删除 " class="button-c" onclick="del_desc(this);"/></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php endif;?>
                                </div>
                            </fieldset>
                        <?php }?>
                        </td>
                    <td class="divider"></td>
                </tr>
                </tbody>
            </table>
        <?php }?>
        <?php if((strtotime($params['year'].'-'.$params['month']) > strtotime(date('Y-m'))) || (strtotime($params['year'].'-'.$params['month']) == strtotime(date('Y-m')) && $button_hidden != 1)){?>
            <div class="a-center actionlink">
                <input class="button-s" value="保存" id="submit" type="submit" onclick="return confirm('请确定好再保存哦？尽量不要修改！')">
            </div>
        <?php }?>
        </form>
    </div>
    <div id="divider"></div>
</div>
<script type="text/javascript">
function get_month(){
    var year = $("#year").val();
    var month = $("#month_input").val();//当前搜索月份，当用户还没有点搜索的时候，获取到的是当前月
    $('#month').empty();
    if( year != '' ){
        $.ajax({
            type:'GET',
            url:'/ratting/get_month_next?year='+year,
            dataType:'json',
            success:function(result){
                var len = result.length;
                for( i=0;i<len;i++ ){
                    var is_select = '';
                    if( result[i].month == month ){
                        is_select = "selected='selected'";
                        //$("#month_input").val('');
                    }
                    $("#month").append("<option value='"+result[i].month+"' "+is_select+">"+result[i].month+"</option>");
                }
            }
        });
    }
}
$(document).ready(function(){
    get_month();
});
function add_setting(type){
    var index = $("."+type+"_setting").index();
    var html = '<div class="'+type+'_setting">'
                     +'<p>评价内容' 
                         +'<input type="text" class="text-3" value="" id="" name="'+type+'_content[]">'
                         +'<input type="button" class="button-c" value=" 删除 " onclick="del_setting(\''+type+'\',this)">'
                     +'</p>'
                     +'<table width="80%" cellspacing="1" cellpadding="1" border="1" class="infotable" style="background-color:#f8f8f8;">'
                         +'<tbody>'
                            +'<tr><td align="center"><strong>评价说明</strong></td><td align="center"><strong>区间分值</strong></td><td width="20%" align="center"><strong>评语是否必填</strong></td><td align="center"><strong>操作</strong></td></tr>'
                            +'<tr><td bgcolor="#fff" align="center"><select name="'+type+'_level['+index+'][]"><option value="好" selected >好</option><!--<option value="中">中</option>--><option value="差">差</option></select><input type="text" class="text-4" value="" id="" name="'+type+'_desc['+index+'][]"></td><td bgcolor="#fff" align="center"><input type="text" class="text-2" value="" id="" name="'+type+'_start_value['+index+'][]" style="width:55px;text-align:center;" />&nbsp;-&nbsp;<input type="text" class="text-2" value="" id="" name="'+type+'_end_value['+index+'][]" style="width:55px;text-align:center;" /></td><td bgcolor="#fff" align="center"><input type="hidden" name="'+type+'_reviews_required['+index+'][]" value="1"/><input type="checkbox" class="req_chkbox" value="1" checked/></td><td bgcolor="#fff" align="center"><input type="button" class="button-c" value=" 添加 " onclick="add_desc(\''+type+'\',this)"></td><td bgcolor="#fff" align="center"><input type="button" class="button-c" value=" 删除 " onclick="del_desc(this)"></td></tr>'
                            +'<tr><td bgcolor="#fff" align="center"><select name="'+type+'_level['+index+'][]"><option value="好">好</option><!--<option value="中">中</option>--><option value="差" selected >差</option></select><input type="text" class="text-4" value="" id="" name="'+type+'_desc['+index+'][]"></td><td bgcolor="#fff" align="center"><input type="text" class="text-2" value="" id="" name="'+type+'_start_value['+index+'][]" style="width:55px;text-align:center;" />&nbsp;-&nbsp;<input type="text" class="text-2" value="" id="" name="'+type+'_end_value['+index+'][]" style="width:55px;text-align:center;" /></td><td bgcolor="#fff" align="center"><input type="hidden" name="'+type+'_reviews_required['+index+'][]" value="1"/><input type="checkbox" class="req_chkbox" value="1" checked/></td><td bgcolor="#fff" align="center"><input type="button" class="button-c" value=" 添加 " onclick="add_desc(\''+type+'\',this)"></td><td bgcolor="#fff" align="center"><input type="button" class="button-c" value=" 删除 " onclick="del_desc(this)"></td></tr>'
                        +'</tbody>'
                    +'</table>'
                +'</div>';
    $("#"+type).append(html);
}
function add_desc(type,obj){
    var index = $(obj).parents("."+type+"_setting").index() - 1;
    var html = '<tr>'
                    +'<td bgcolor="#fff" align="center">'
                        +'<select name="'+type+'_level['+index+'][]"><option value="好">好</option><!--<option value="中">中</option>--><option value="差">差</option></select>'
                        +'<input type="text" class="text-4" value="" id="" name="'+type+'_desc['+index+'][]"></td><td bgcolor="#fff" align="center">'
                        +'<input type="text" class="text-2" value="" id="" name="'+type+'_start_value['+index+'][]" style="width:55px;text-align:center;"/>'
                        +'&nbsp;-&nbsp;<input type="text" class="text-2" value="" id="" name="'+type+'_end_value['+index+'][]" style="width:55px;text-align:center;"/>'
                    +'</td>'
                    +'<td bgcolor="#fff" align="center">'
                        +'<input type="hidden" name="'+type+'_reviews_required['+index+'][]" value="1"/>'
                        +'<input type="checkbox" class="req_chkbox" value="1" checked/>'
                    +'</td>'
                    +'<td bgcolor="#fff" align="center"><input type="button" class="button-c" value=" 添加 " onclick="add_desc(\''+type+'\',this)"></td>'
                    +'<td bgcolor="#fff" align="center"><input type="button" class="button-c" value=" 删除 " onclick="del_desc(this)"></td>'
                +'</tr>';
    $(obj).parents("table[class='infotable'] tbody").append(html);
}
function del_desc(obj){
    if($(obj).parent().parent().siblings("tr").length == 2){
        return;
    }
    $(obj).parent().parent().remove();
}
function del_setting(type,obj){
    if($("."+type+"_setting").length == 1){
        return;
    }
    $(obj).parents("."+type+"_setting").remove();
}
$(".req_chkbox").live("click", function(){
    var v = $(this).attr("checked")?1:2;
    $(this).siblings("input:hidden").val(v);
});
</script>