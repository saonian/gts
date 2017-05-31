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
    <form method="post" id="dataform" action="/story/save">
      <input type="hidden" name="story_id" value="<?php echo $story->id;?>" />
      <input type="hidden" name="body" value="<?php echo $body;?>" />
      <input type="hidden" id="project_id" name="project_id" value="<?php echo $story->project_id;?>" />
      <table align="center" class="table-1 a-left">
        <caption>
        <div class="f-left"><strong>审核需求</strong></div>
        </caption>
        <tbody>
<!--           <tr>
            <th class="rowhead">审核时间</th>
            <td><input type="text" name="reviewed_date" class="text-3 datetime" value="<?php echo date('Y-m-d H:i:s');?>">
              <span class="star"> * </span></td>
          </tr> -->
          <tr>
            <th class="rowhead">审核结果</th>
            <td><select name="reviewed_result" class="text-2">
                  <?php foreach($pmsdata['story']['reviewed_result'] as $val):?>
                    <option value="<?php echo $val['value'];?>"><?php echo $val['display'];?></option>
                  <?php endforeach;?>
                </select>
            </td>
          </tr>
          <tr>
            <th class="rowhead">指派给</th>
            <td><select name="assigned_to" class="text-2">
                  <?php $opened_by = empty($story->opened_by)?0:$story->opened_by->id;?>
                  <option value="0"></option>
                  <?php foreach($all_user as $val):?>
                    <option value="<?php echo $val->id;?>" <?php echo ($val->id == $opened_by)?'selected':''?>><?php echo $val->real_name;?></option>
                  <?php endforeach;?>
                </select>
            </td>
          </tr>
          <!-- <tr>
            <th class="rowhead">由谁评审</th>
            <td><select name="reviewed_by">
                  <option value="0">没有评审</option>
                  <?php $reviewed_by = empty($story->reviewed_by)?0:$story->reviewed_by->id;?>
                  <?php foreach($all_user as $val):?>
                    <option value="<?php echo $val->id;?>" <?php echo ($val->id == $reviewed_by)?'selected':''?>><?php echo $val->real_name;?></option>
                  <?php endforeach;?>
                </select>
            </td>
          </tr> -->
          <tr>
            <th class="rowhead">评估工时</th>
            <td><input type="text" name="estimate" value="<?php echo $story->estimate;?>" class="text-2 required" placeholder="完成该需求的工作量">小时<span class="star"> * </span></td>
          </tr>
          <tr>
            <th class="rowhead">需求完整度</th>
            <td><select name="quality" class="text-2">
                  <?php foreach($pmsdata['story']['quality'] as $val):?>
                    <option value="<?php echo $val['value'];?>"><?php echo $val['display'];?></option>
                  <?php endforeach;?>
                </select>
            </td>
          </tr>
          <tr>
            <th class="rowhead">备注</th>
            <td><textarea name="comment" rows="6" class="editor"></textarea></td>
          </tr>
          <tr>
            <td colspan="2" class="a-center"><input type="submit" id="submit" value="保存" class="button-s">
              <input type="reset" id="reset" value="重填" class="button-r">
              <input type="button" value=" 返回 " class="button-s" onclick="<?php echo empty($_SERVER['HTTP_REFERER'])?"history.back();":"window.location.href='{$_SERVER['HTTP_REFERER']}'";?>" class="button-act">
            </td>
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
$("#dataform").validate({
  ignore: [],
  rules:{
    description: {required:true, minlength:20}
  }
});
</script>