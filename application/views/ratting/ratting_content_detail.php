<div id="wrap">
    <div class="outer">
        <div id="titlebar">
            <div id="main">您正在查看&nbsp;<font color="blue"><?php echo $ratting_content_details['rated_name']?></font>&nbsp;的正负能量详细评分数据:</div>
        </div>
        <table class="cont-rt5">
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
                                        <li <?php if($ratting_content_details['grade'] <= $i){?> class="act" <?php }?> data-score="<?php echo $i?>" data-id="<?php echo $ratting_content_details['rat_set'][1]['id']?>" data-level="<?php echo $ratting_content_details['rat_set'][1]['level']?>" data-cid="<?php echo $v['id']?>" title="<?php echo $i?>"><?php echo $i?></li>
                                        <?php }?>
                                    </ul>
                                    <ul class="starPlus star">
                                        <?php for($i = $ratting_content_details['rat_set'][0]['start_value'];$i<=$ratting_content_details['rat_set'][0]['end_value'];$i++){?>
                                        <li <?php if($ratting_content_details['grade'] >= $i){?> class="act" <?php }?> onclick="" onmouseover="" onmouseout="" data-score="<?php echo $i?>" data-id="<?php echo $ratting_content_details['rat_set'][0]['id']?>" data-level="<?php echo $ratting_content_details['rat_set'][0]['level']?>" data-cid="<?php echo $v['id']?>" title="<?php echo $i?>"><?php echo $i?></li>
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
                                <input type="hidden" class="scoreVal" name="grade" value="<?php echo $ratting_content_details['grade']?>" />
                                <br/>
                                <p>
                                    评分事件：<textarea cols="50" rows="5" name="rating_desc"><?php echo $ratting_content_details['rating_desc']?></textarea>
                                </p>
                            </div>
                        </div>
                    </fieldset>
                    <div class="actionlink" style="margin-left:200px;">
                        <input class="button-s" value="返回" type="button" onclick="javascript:history.go(-1);">
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div id="divider"></div>
</div>
<script type="text/javascript">
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