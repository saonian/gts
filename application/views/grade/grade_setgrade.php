<script type="text/javascript" src="/public/js/grade_setting.js"></script>
<?php 
$sort = $this->input->get('sort');
$sort = (empty($sort) || $sort=='asc')?'desc':'asc';
?>
<?php
if(!$include_headfoot){
  echo <<<EOF
<link rel='stylesheet' type='text/css' href='/public/css/base_min.css' />
<link rel='stylesheet' type='text/css' href='/public/css/css.css' />
<script src='/public/js/jquery-1.8.3.min.js' type="text/javascript"></script>
<script src="/public/js/layer/layer.min.js"></script>
<script src='/public/js/jquery.cookie.js' type="text/javascript"></script>
<script src="/public/js/syntaxhighlighter/scripts/shCore.js" type="text/javascript" ></script>
<script src="/public/js/syntaxhighlighter/scripts/shAutoloader.js" type="text/javascript" ></script>
<script src='/public/js/common.js' type="text/javascript"></script>
<script src="/public/js/jquery-ui-1.9.2-min.js" type='text/javascript' ></script>
<script src="/public/js/jquery.ui.datepicker-zh-CN.min.js" type='text/javascript' ></script>
<script src="/public/js/jquery-ui-sliderAccess.js" type='text/javascript' ></script>
<script src="/public/js/jquery-ui-timepicker-addon.min.js" type='text/javascript' ></script>
<script src="/public/js/jquery.validate.min.js" type='text/javascript' ></script>
<script src="/public/js/kindeditor/kindeditor-min.js" type='text/javascript' ></script>
<script src="/public/js/kindeditor/zh_CN.js" type='text/javascript' ></script>
<script src="/public/js/autocomplete/jquery.autocomplete.min.js"></script>
<style>
body {background-color:white}
</style>
EOF;
}
?>
<div id="wrap" <?php echo !$include_headfoot ? 'style="padding:0"' : '';?>>
  <div class="outer" <?php echo !$include_headfoot ? 'style="padding:0"' : '';?>>
        <form id="dataform" method="post" action="/grade/setgrade/<?php echo $project->id; ?>?body=<?php echo $body;?>">
        <div class="titlebg" id="titlebar">
            <div id="main">项目: <?php echo $project->name; ?></div>
        </div>
        <table class="cont-rt5">
            <tbody>
            <tr valign="top">
                <td>
                    <fieldset>
                        <legend>需求</legend>
                        <div id="story" class="content infodiv">
                            <p class="pb15">
                                <input id="add_story_setting" type="button" value=" 添加评价内容 " class="button-c">
                            </p>
                            <?php if(!empty($settings['story'])):?>
                            <?php foreach($settings['story'] as $k => $v):?>
                            <div class="story_setting">
                                <p>
                                    评价内容
                                    <input type="hidden" name="setting_story_id[]" value="<?php echo $v->id;?>"/>
                                    <input type="text" name="story_content[]" id="" value="<?php echo empty($v->content)?'':$v->content;?>" class="text-3">
                                    <input type="button" value=" 删除 " class="button-c del_setting">
                                </p>
                                <table width="80%" cellspacing="1" cellpadding="1" border="1" style="background-color:#f8f8f8;" class="infotable">
                                    <tbody><tr>
                                        <td align="center"><strong>评价说明</strong></td>
                                        <td align="center"><strong>分值</strong></td>
                                        <td width="20%" align="center"><strong>评语是否必填</strong></td>
                                        <td align="center"><strong>操作</strong></td>
                                    </tr>
                                    <?php if(!empty($v->description)):?>
                                        <?php foreach ($v->description as $key => $val):?>
                                        <tr>
                                            <input type="hidden" name="setting_story_description_id[<?php echo $k;?>][]" value="<?php echo $val->id;?>"/>
                                            <td bgcolor="#fff" align="center"><input type="text" name="story_desc[<?php echo $k;?>][]" id="" value="<?php echo $val->desc;?>" class="text-4"></td>
                                            <td bgcolor="#fff" align="center"><input type="text" name="story_score[<?php echo $k;?>][]" id="" value="<?php echo $val->score;?>" class="text-2"></td>
                                            <td bgcolor="#fff" align="center"><input type="hidden" name="story_reviews_required[<?php echo $k;?>][]" value="<?php echo empty($val->review_required)?0:1;?>"/><input type="checkbox" class="req_chkbox" value="1" <?php echo empty($val->review_required)?'':'checked';?>/></td>
                                            <td bgcolor="#fff" align="center"><input type="button" value=" 添加 " class="button-c add_story_desc"></td>
                                            <td bgcolor="#fff" align="center"><input type="button" value=" 删除 " class="button-c del_desc"></td>
                                        </tr>
                                        <?php endforeach;?>
                                    <?php else:?>
                                    <tr>
                                        <td bgcolor="#fff" align="center"><input type="text" name="story_desc[<?php echo $k;?>][]" id="" value="" class="text-4"></td>
                                        <td bgcolor="#fff" align="center"><input type="text" name="story_score[<?php echo $k;?>][]" id="" value="" class="text-2"></td>
                                        <td bgcolor="#fff" align="center"><input type="hidden" name="story_reviews_required[<?php echo $k;?>][]" value="1"/><input type="checkbox" class="req_chkbox" value="1" checked/></td>
                                        <td bgcolor="#fff" align="center"><input type="button" value=" 添加 " class="button-c add_story_desc"></td>
                                        <td bgcolor="#fff" align="center"><input type="button" value=" 删除 " class="button-c del_desc"></td>
                                    </tr>
                                    <tr>
                                        <td bgcolor="#fff" align="center"><input type="text" name="story_desc[<?php echo $k;?>][]" id="" value="" class="text-4"></td>
                                        <td bgcolor="#fff" align="center"><input type="text" name="story_score[<?php echo $k;?>][]" id="" value="" class="text-2"></td>
                                        <td bgcolor="#fff" align="center"><input type="hidden" name="story_reviews_required[<?php echo $k;?>][]" value="1"/><input type="checkbox" class="req_chkbox" value="1" checked/></td>
                                        <td bgcolor="#fff" align="center"><input type="button" value=" 添加 " class="button-c add_story_desc"></td>
                                        <td bgcolor="#fff" align="center"><input type="button" value=" 删除 " class="button-c del_desc"></td>
                                    </tr>
                                    <?php endif;?>
                                    </tbody>
                                </table>
                            </div>
                            <?php endforeach;?>
                            <?php else:?>
                            <div class="story_setting">
                                <p>
                                    评价内容
                                    <input type="text" name="story_content[]" id="" value="" class="text-3">
                                    <input type="button" value=" 删除 " class="button-c del_setting">
                                </p>
                                <table width="80%" cellspacing="1" cellpadding="1" border="1" style="background-color:#f8f8f8;" class="infotable">
                                    <tbody><tr>
                                        <td align="center"><strong>评价说明</strong></td>
                                        <td align="center"><strong>分值</strong></td>
                                        <td width="20%" align="center"><strong>评语是否必填</strong></td>
                                        <td align="center"><strong>操作</strong></td>
                                    </tr>
                                    <tr>
                                        <td bgcolor="#fff" align="center"><input type="text" name="story_desc[0][]" id="" value="" class="text-4"></td>
                                        <td bgcolor="#fff" align="center"><input type="text" name="story_score[0][]" id="" value="" class="text-2"></td>
                                        <td bgcolor="#fff" align="center"><input type="hidden" name="story_reviews_required[0][]" value="1"/><input type="checkbox" class="req_chkbox" value="1" checked/></td>
                                        <td bgcolor="#fff" align="center"><input type="button" value=" 添加 " class="button-c add_story_desc"></td>
                                        <td bgcolor="#fff" align="center"><input type="button" value=" 删除 " class="button-c del_desc"></td>
                                    </tr>
                                    <tr>
                                        <td bgcolor="#fff" align="center"><input type="text" name="story_desc[0][]" id="" value="" class="text-4"></td>
                                        <td bgcolor="#fff" align="center"><input type="text" name="story_score[0][]" id="" value="" class="text-2"></td>
                                        <td bgcolor="#fff" align="center"><input type="hidden" name="story_reviews_required[0][]" value="1"/><input type="checkbox" class="req_chkbox" value="1" checked/></td>
                                        <td bgcolor="#fff" align="center"><input type="button" value=" 添加 " class="button-c add_story_desc"></td>
                                        <td bgcolor="#fff" align="center"><input type="button" value=" 删除 " class="button-c del_desc"></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <?php endif;?>
                        </div>
                    </fieldset>
                    <fieldset >
                        <legend>任务</legend>
                        <div id="task" class="content infodiv">
                            <p class="pb15">
                                <input id="add_task_setting" type="button" value=" 添加评价内容 " class="button-c">
                            </p>
                            <?php if(!empty($settings['task'])):?>
                            <?php foreach($settings['task'] as $k => $v):?>
                            <div class="task_setting">
                            <p>
                                评价内容
                                <input type="hidden" name="setting_task_id[]" value="<?php echo $v->id;?>"/>
                                <input type="text" name="task_content[]" id="" value="<?php echo empty($v->content)?'':$v->content;?>" class="text-3">
                                <input type="button" value=" 删除 " class="button-c del_setting">
                            </p>
                            <table width="80%" cellspacing="1" cellpadding="1" border="1" style="background-color:#f8f8f8;" class="infotable">
                                <tbody><tr>
                                    <td align="center"><strong>评价说明</strong></td>
                                    <td align="center"><strong>分值</strong></td>
                                    <td width="20%" align="center"><strong>评语是否必填</strong></td>
                                    <td align="center"><strong>操作</strong></td>
                                </tr>
                                <?php if(!empty($v->description)):?>
                                    <?php foreach ($v->description as $key => $val):?>
                                    <tr>
                                        <input type="hidden" name="setting_task_description_id[<?php echo $k;?>][]" value="<?php echo $val->id;?>"/>
                                        <td bgcolor="#fff" align="center"><input type="text" name="task_desc[<?php echo $k;?>][]" id="" value="<?php echo $val->desc;?>" class="text-4"></td>
                                        <td bgcolor="#fff" align="center"><input type="text" name="task_score[<?php echo $k;?>][]" id="" value="<?php echo $val->score;?>" class="text-2"></td>
                                        <td bgcolor="#fff" align="center"><input type="hidden" name="task_reviews_required[<?php echo $k;?>][]" value="<?php echo empty($val->review_required)?0:1;?>"/><input type="checkbox" class="req_chkbox" value="1" <?php echo empty($val->review_required)?'':'checked';?>/></td>
                                        <td bgcolor="#fff" align="center"><input type="button" value=" 添加 " class="button-c add_task_desc"></td>
                                        <td bgcolor="#fff" align="center"><input type="button" value=" 删除 " class="button-c del_desc"></td>
                                    </tr>
                                    <?php endforeach;?>
                                <?php else:?>
                                <tr>
                                    <td bgcolor="#fff" align="center"><input type="text" name="task_desc[<?php echo $k;?>][]" id="" value="" class="text-4"></td>
                                    <td bgcolor="#fff" align="center"><input type="text" name="task_score[<?php echo $k;?>][]" id="" value="" class="text-2"></td>
                                    <td bgcolor="#fff" align="center"><input type="hidden" name="task_reviews_required[<?php echo $k;?>][]" value="1"/><input type="checkbox" class="req_chkbox" value="1" checked/></td>
                                    <td bgcolor="#fff" align="center"><input type="button" value=" 添加 " class="button-c add_task_desc"></td>
                                    <td bgcolor="#fff" align="center"><input type="button" value=" 删除 " class="button-c del_desc"></td>
                                </tr>
                                <tr>
                                    <td bgcolor="#fff" align="center"><input type="text" name="task_desc[<?php echo $k;?>][]" id="" value="" class="text-4"></td>
                                    <td bgcolor="#fff" align="center"><input type="text" name="task_score[<?php echo $k;?>][]" id="" value="" class="text-2"></td>
                                    <td bgcolor="#fff" align="center"><input type="hidden" name="task_reviews_required[<?php echo $k;?>][]" value="1"/><input type="checkbox" class="req_chkbox" value="1" checked/></td>
                                    <td bgcolor="#fff" align="center"><input type="button" value=" 添加 " class="button-c add_task_desc"></td>
                                    <td bgcolor="#fff" align="center"><input type="button" value=" 删除 " class="button-c del_desc"></td>
                                </tr>
                                <?php endif;?>
                                </tbody>
                            </table>
                            </div>
                            <?php endforeach;?>
                            <?php else:?>
                            <div class="task_setting">
                                <p>
                                    评价内容
                                    <input type="text" name="task_content[]" id="" value="" class="text-3">
                                    <input type="button" value=" 删除 " class="button-c del_setting">
                                </p>
                                <table width="80%" cellspacing="1" cellpadding="1" border="1" style="background-color:#f8f8f8;" class="infotable">
                                    <tbody><tr>
                                        <td align="center"><strong>评价说明</strong></td>
                                        <td align="center"><strong>分值</strong></td>
                                        <td width="20%" align="center"><strong>评语是否必填</strong></td>
                                        <td align="center"><strong>操作</strong></td>
                                    </tr>
                                    <tr>
                                        <td bgcolor="#fff" align="center"><input type="text" name="task_desc[0][]" id="" value="" class="text-4"></td>
                                        <td bgcolor="#fff" align="center"><input type="text" name="task_score[0][]" id="" value="" class="text-2"></td>
                                        <td bgcolor="#fff" align="center"><input type="hidden" name="task_reviews_required[0][]" value="1"/><input type="checkbox" class="req_chkbox" value="1" checked/></td>
                                        <td bgcolor="#fff" align="center"><input type="button" value=" 添加 " class="button-c add_task_desc"></td>
                                        <td bgcolor="#fff" align="center"><input type="button" value=" 删除 " class="button-c del_desc"></td>
                                    </tr>
                                    <tr>
                                        <td bgcolor="#fff" align="center"><input type="text" name="task_desc[0][]" id="" value="" class="text-4"></td>
                                        <td bgcolor="#fff" align="center"><input type="text" name="task_score[0][]" id="" value="" class="text-2"></td>
                                        <td bgcolor="#fff" align="center"><input type="hidden" name="task_reviews_required[0][]" value="1"/><input type="checkbox" class="req_chkbox" value="1" checked/></td>
                                        <td bgcolor="#fff" align="center"><input type="button" value=" 添加 " class="button-c add_task_desc"></td>
                                        <td bgcolor="#fff" align="center"><input type="button" value=" 删除 " class="button-c del_desc"></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <?php endif;?>
                        </div>
                    </fieldset>
                    <div class="a-center actionlink">
                        <input class="button-s" value="保存" id="submit" type="submit">
                    </div>
                    </td>
                <td class="divider"></td>
            </tr>
            </tbody>
        </table>
        </form>
    </div>
  <?php
if($include_headfoot){
  echo <<<EOF
<div id="divider"></div>
EOF;
}
?>
</div>
<script type="text/javascript">
(function(){
    $("#add_story_setting").live("click", function(){
        var index = $(".story_setting").length;
        var html = '<div class="story_setting"><p>评价内容 <input type="text" class="text-3" value="" id="" name="story_content[]"> <input type="button" class="button-c del_setting" value=" 删除 "></p><table width="80%" cellspacing="1" cellpadding="1" border="1" class="infotable" style="background-color:#f8f8f8;"><tbody><tr><td align="center"><strong>评价说明</strong></td><td align="center"><strong>分值</strong></td><td width="20%" align="center"><strong>评语是否必填</strong></td><td align="center"><strong>操作</strong></td></tr><tr><td bgcolor="#fff" align="center"><input type="text" class="text-4" value="" id="" name="story_desc['+index+'][]"></td><td bgcolor="#fff" align="center"><input type="text" class="text-2" value="" id="" name="story_score['+index+'][]"></td><td bgcolor="#fff" align="center"><input type="hidden" name="story_reviews_required['+index+'][]" value="1"/><input type="checkbox" class="req_chkbox" value="1" checked/></td><td bgcolor="#fff" align="center"><input type="button" class="button-c add_story_desc" value=" 添加 "></td><td bgcolor="#fff" align="center"><input type="button" class="button-c del_desc" value=" 删除 "></td></tr><tr><td bgcolor="#fff" align="center"><input type="text" class="text-4" value="" id="" name="story_desc['+index+'][]"></td><td bgcolor="#fff" align="center"><input type="text" class="text-2" value="" id="" name="story_score['+index+'][]"></td><td bgcolor="#fff" align="center"><input type="hidden" name="story_reviews_required['+index+'][]" value="1"/><input type="checkbox" class="req_chkbox" value="1" checked/></td><td bgcolor="#fff" align="center"><input type="button" class="button-c add_story_desc" value=" 添加 "></td><td bgcolor="#fff" align="center"><input type="button" class="button-c del_desc" value=" 删除 "></td></tr></tbody></table></div>';
        $("#story").append(html);
    });
    $("#add_task_setting").live("click", function(){
        var index = $(".task_setting").length;
        var html = '<div class="task_setting"><p>评价内容 <input type="text" class="text-3" value="" id="" name="task_content[]"> <input type="button" class="button-c del_setting" value=" 删除 "></p><table width="80%" cellspacing="1" cellpadding="1" border="1" class="infotable" style="background-color:#f8f8f8;"><tbody><tr><td align="center"><strong>评价说明</strong></td><td align="center"><strong>分值</strong></td><td width="20%" align="center"><strong>评语是否必填</strong></td><td align="center"><strong>操作</strong></td></tr><tr><td bgcolor="#fff" align="center"><input type="text" class="text-4" value="" id="" name="task_desc['+index+'][]"></td><td bgcolor="#fff" align="center"><input type="text" class="text-2" value="" id="" name="task_score['+index+'][]"></td><td bgcolor="#fff" align="center"><input type="hidden" name="task_reviews_required['+index+'][]" value="1"/><input type="checkbox" class="req_chkbox" value="1" checked/></td><td bgcolor="#fff" align="center"><input type="button" class="button-c add_task_desc" value=" 添加 "></td><td bgcolor="#fff" align="center"><input type="button" class="button-c del_desc" value=" 删除 "></td></tr><tr><td bgcolor="#fff" align="center"><input type="text" class="text-4" value="" id="" name="task_desc['+index+'][]"></td><td bgcolor="#fff" align="center"><input type="text" class="text-2" value="" id="" name="task_score['+index+'][]"></td><td bgcolor="#fff" align="center"><input type="hidden" name="task_reviews_required['+index+'][]" value="1"/><input type="checkbox" class="req_chkbox" value="1" checked/></td><td bgcolor="#fff" align="center"><input type="button" class="button-c add_task_desc" value=" 添加 "></td><td bgcolor="#fff" align="center"><input type="button" class="button-c del_desc" value=" 删除 "></td></tr></tbody></table></div>';
        $("#task").append(html);
    });
    $(".del_setting").live("click", function(){
        if($(".story_setting").length == 1 && $(".task_setting").length == 1){
            return;
        }
        $(this).parents(".story_setting").remove();
        $(this).parents(".task_setting").remove();
    });
    $(".add_story_desc").live("click", function(){
        var index = $(this).parents(".story_setting").index() - 1;
        var html = '<tr><td bgcolor="#fff" align="center"><input type="text" class="text-4" value="" id="" name="story_desc['+index+'][]"></td><td bgcolor="#fff" align="center"><input type="text" class="text-2" value="" id="" name="story_score['+index+'][]"></td><td bgcolor="#fff" align="center"><input type="hidden" name="story_reviews_required['+index+'][]" value="1"/><input type="checkbox" class="req_chkbox" value="1" checked/></td><td bgcolor="#fff" align="center"><input type="button" class="button-c add_story_desc" value=" 添加 "></td><td bgcolor="#fff" align="center"><input type="button" class="button-c del_desc" value=" 删除 "></td></tr>';
        $(this).parents("table[class='infotable'] tbody").append(html);
    });
    $(".add_task_desc").live("click", function(){
        var index = $(this).parents(".task_setting").index() - 1;
        var html = '<tr><td bgcolor="#fff" align="center"><input type="text" class="text-4" value="" id="" name="task_desc['+index+'][]"></td><td bgcolor="#fff" align="center"><input type="text" class="text-2" value="" id="" name="task_score['+index+'][]"></td><td bgcolor="#fff" align="center"><input type="hidden" name="task_reviews_required['+index+'][]" value="1"/><input type="checkbox" class="req_chkbox" value="1" checked/></td><td bgcolor="#fff" align="center"><input type="button" class="button-c add_task_desc" value=" 添加 "></td><td bgcolor="#fff" align="center"><input type="button" class="button-c del_desc" value=" 删除 "></td></tr>';
        $(this).parents("table[class='infotable'] tbody").append(html);
    });
    $(".del_desc").live("click", function(){
        if($(this).parent().parent().siblings("tr").length == 2){
            return;
        }
        $(this).parent().parent().remove();
    });
    $(".req_chkbox").live("click", function(){
        var v = $(this).attr("checked")?1:0;
        $(this).siblings("input:hidden").val(v);
    });
})();
</script>