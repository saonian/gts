<!-- 拇指评分 -->
<script src='/public/js/rating.js' type="text/javascript"></script>
<div id="wrap">
    <div class="outer">
        <div id="titlebar">
            <div id="main">您正在查看&nbsp;<font color="blue"><?php echo $ratting_content_details['rated_name']?></font>&nbsp;的正负能量详细评分数据:</div>
        </div>
        <table class="cont-rt5">
            <input type="hidden" name="id" value="<?php echo $ratting_content_details['id']?>" id="id"/>
            <input type="hidden" name="description_id" id="description_id" value="<?php echo $ratting_content_details['description_id']?>"/>
            <input type="hidden" name="description_level" id="description_level" value="<?php echo $ratting_content_details['level']?>" />
            <tbody>
            <tr valign="top">
                <td>
                    <fieldset>
                        <legend><?php echo $ratting_content_details['rat_set'][0]['content']?></legend>
                        <div id="quality" class="content infodiv">
                            <div class="quality_setting">
                                <p>
                                    我的评价：
                                </p>
                                <!-- 拇指评分 -->                                                    
                                <div class="starBox">
                                    <ul class="starMinus star">
                                        <?php for($i = $ratting_content_details['rat_set'][1]['end_value'];$i>=$ratting_content_details['rat_set'][1]['start_value'];$i--){?>
                                        <li <?php if($ratting_content_details['grade'] <= $i){?> class="act" data-c = "active" <?php }?> data-score="<?php echo $i?>" data-id="<?php echo $ratting_content_details['rat_set'][1]['id']?>" data-level="<?php echo $ratting_content_details['rat_set'][1]['level']?>" data-cid="<?php echo $v['id']?>" title="<?php echo $i?>"><?php echo $i?></li>
                                        <?php }?>
                                    </ul>
                                    <ul class="starPlus star">
                                        <?php for($i = $ratting_content_details['rat_set'][0]['start_value'];$i<=$ratting_content_details['rat_set'][0]['end_value'];$i++){?>
                                        <li <?php if($ratting_content_details['grade'] >= $i){?> class="act" data-c = "active" <?php }?> onclick="" onmouseover="" onmouseout="" data-score="<?php echo $i?>" data-id="<?php echo $ratting_content_details['rat_set'][0]['id']?>" data-level="<?php echo $ratting_content_details['rat_set'][0]['level']?>" data-cid="<?php echo $v['id']?>" title="<?php echo $i?>"><?php echo $i?></li>
                                        <?php }?>
                                    </ul>
                                </div>
                                <div class="rating_tips">
                                    <div class="rating_tips_default">
                                       <p><?php echo $ratting_content_details['rat_set'][0]['desc']?> (+<?php echo $ratting_content_details['rat_set'][0]['start_value']?>至+<?php echo $ratting_content_details['rat_set'][0]['end_value']?>分)</p> 
                                        <p><?php echo $ratting_content_details['rat_set'][1]['desc']?> (<?php echo $ratting_content_details['rat_set'][1]['start_value']?>至<?php echo $ratting_content_details['rat_set'][1]['end_value']?>分)</p> 
                                    </div>
                                    <div class="rating_tips_green">
                                        <p><span class="ratingNum"></span>分&nbsp&nbsp  <?php echo $ratting_content_details['rat_set'][0]['desc']?> (+<?php echo $ratting_content_details['rat_set'][0]['start_value']?>至+<?php echo $ratting_content_details['rat_set'][0]['end_value']?>分)</p>
                                    </div>
                                    <div class="rating_tips_red">
                                        <p><span class="ratingNum"></span>分&nbsp&nbsp  <?php echo $ratting_content_details['rat_set'][1]['desc']?> (<?php echo $ratting_content_details['rat_set'][1]['start_value']?>至<?php echo $ratting_content_details['rat_set'][1]['end_value']?>分)</p>
                                    </div>
                                </div>
                                <input type="hidden" class="scoreVal" name="grade" id="grade" value="<?php echo $ratting_content_details['grade']?>" />
                                <br/>
                                <p>
                                    评分事件：<textarea cols="50" rows="5" id="rating_desc" name="rating_desc"><?php echo $ratting_content_details['rating_desc']?></textarea>
                                </p>
                            </div>
                        </div>
                    </fieldset>
                    <div class="actionlink" style="margin-left:200px;">
                        <input class="button-s" value="保存" type="button" id="button" onclick="submitdata();">&nbsp;<input class="button-s" value="返回" type="button" onclick="javascript:history.go(-1);">
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div id="divider"></div>
</div>
<script type="text/javascript">
function get_desc_id(id,level){
    $("#description_id").val(id);
    $("#description_level").val(level);
}
function submitdata(){
    var description_id = $("#description_id").val();
    var description_level = $("#description_level").val();
    var id = $("#id").val();
    var grade = $("#grade").val();
    var rating_desc = $("#rating_desc").val();
    var log = 1;
    $.ajax({
       type: "POST",
       url: "/ratting/ratting_modify",
       data: "description_id="+description_id+"&description_level="+description_level+"&id="+id+"&grade="+grade+"&rating_desc="+rating_desc+"&log="+log,
       dataType:"json",
       success: function(msg){
            if(msg.flag == 1){
                layer.alert('本月加分剩余:'+msg.last_plus+'&nbsp;&nbsp;&nbsp;&nbsp;本月减分剩余:'+msg.last_minus, 9,'保存成功');
                $("#reset"+id).click();
            }else if(msg.flag == 0){
                layer.alert('本月加分剩余:'+msg.last_plus+'&nbsp;&nbsp;&nbsp;&nbsp;本月减分剩余:'+msg.last_minus, 8,'保存失败');
            }else{
                layer.alert(msg.info, 2,'提示信息');
            }
            $("#button").removeAttr("disabled");
       }
    });
}
$(document).ready(function(){
    var ratingScore = $(".scoreVal").val();
    var rating_tips_green = $('.rating_tips_green');
    var rating_tips_red = $('.rating_tips_red');
    if(ratingScore > 0){
        rating_tips_green.show().siblings('div').hide();
        rating_tips_green.find('.ratingNum').text(ratingScore);
    }else{
        rating_tips_red.show().siblings('div').hide();
        rating_tips_red.find('.ratingNum').text(ratingScore);
    }
});
</script>