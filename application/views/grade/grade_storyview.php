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
        <div id="titlebar">
            <div id="main"><?php echo $project_name.' &gt;&gt; STORY #'.$story_id.'&nbsp;'."<a href='/story/view/{$story_id}' target='_blank'>{$story_name}</a>"; ?></div>
        </div>
        <table class="cont-rt5">
            <tbody>
            <tr valign="top">
                <td>
                    <div id="comment" class="">
                        <fieldset>
                          <legend>需求描述</legend>
                          <div class="content"><?php echo $story_description;?></div>
                        </fieldset>
                        <fieldset>
                          <legend>附件</legend>
                          <div> 
                            <?php foreach ($story_attachments as $val):?>
                            <a href="/download/<?php echo $val->id;?>"><?php echo empty($val->title) ? basename($val->path) : $val->title.$val->extension;?></a> <br/>
                            <?php endforeach;?>
                          </div>
                        </fieldset>
                        <form id="dataform" method="post" action="/grade/gradestoryedit/<?php echo $grade_id; ?>?body=<?php echo $body;?>">
                        <fieldset>
                            <legend>我的评价</legend>
                            <table align="center" class="table-1 lblock">
                                <tbody>
                                <?php $can_save = FALSE;?>
                                <?php if(empty($data)):?>
                                    <tr><td><font color="red">评分项还未设置</font></td></tr>
                                <?php else:?>
                                <?php foreach($data as $key => $val):?>
                                    <div class="hidden">
                                        <input type="hidden" name="grade_desc_id[]" value="<?php echo $val->id; ?>" />
                                    </div>
                                    <tr>
                                        <th class="rowhead"><?php echo $val->content; ?></th>
                                        <td>
                                            <?php 
                                                if(empty($val->score) && !$can_save){
                                                    $can_save = TRUE;
                                                }
                                            ?>
                                            <?php foreach($val->description_item as $k => $v):?>
                                                <label>
                                                    <input name="grade_description_radio[<?php echo $val->id; ?>]" type="radio"
                                                           value="<?php echo $v->id; ?>"
                                                        <?php
                                                        if(!empty($val->score)){
                                                            echo ((int)$v->id == (int)$val->score->description_id)?'checked ':'';
                                                        }
                                                        echo !empty($val->score)?'disabled':'';
                                                        ?>
                                                         required req="<?php echo empty($v->review_required)?0:1;?>"/>
                                                    <?php echo $v->desc ?>
                                                </label>
                                            <?php endforeach;?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="rowhead"></th>
                                        <td><textarea name="grade_description[<?php echo $val->id; ?>]" minlength="10" class="area-5" rows="3" <?php echo !empty($val->score)?'disabled':'';?> required>
<?php
 if(!empty($val->score))
echo empty($val->score->description)?'':$val->score->description;
?>
</textarea></td>
                                    </tr>
                                <?php endforeach;?>
                                <?php endif;?>
                                <tr>
                                    <td class="a-left" colspan="2">
                                        <?php if(!empty($data) && $can_save):?>
                                        <input class="button-s" value="保存" id="submit" type="submit">
                                        <?php endif;?>
                                        <a class="" href="/grade/gradestorylist/?body=<?php echo $body?>"><input type="button" value=" 返回 " class="button-r"></a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </fieldset>
                        </form>
                    </div>
                </td>
                <td class="divider"></td>
            </tr>
            </tbody>
        </table>
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
$("input:radio").click(function(){
    if($(this).attr("req") && $(this).attr("req") == 0){
        $(this).parents("tr").first().next("tr").find("textarea").removeAttr("required");
    }else{
        $(this).parents("tr").first().next("tr").find("textarea").attr("required","");
    }
});
$("#dataform").validate();
</script>