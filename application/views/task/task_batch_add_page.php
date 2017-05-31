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
  <div class="outer" <?php echo !$include_headfoot ? 'style="padding:0"' : '';?>>
  <form method="post" action="/task/save">
  <input name="body" value="<?php echo $body;?>" type="hidden"/>
    <input name="project_id" value="<?php echo $current_project_id;?>" type="hidden"/>
    <table class="table-1 fixed">
      <caption>
      <strong>批量生成任务</strong>
      </caption>
      <tbody>
        <tr>
          <th class="w-20px">ID</th>
          <th>相关需求</th>
          <th class="red">任务名称</th>
          <th class="w-60px red">类型</th>
          <th class="w-80px">指派给</th>
          <th class="w-50px red">预</th>
          <th class="red">最后完成时间</th>
          <th class="w-200px red">任务描述</th>
          <th class="w-50px">优先级</th>
          <th class="w-50px">难易度</th>
          <th class="w-80px">是否测试</th>
          <th class="w-80px">测试人</th>
        </tr>
        <?php 
          $num_arr = array(1,2,3,4,5);
          foreach ($num_arr as $k => $value):
        ?>
        <tr class="a-center">
          <td><?php echo $value;?></td>
          <td class="a-left">
            <select name="story_id[]" id="story0" class="select-1 chzn-done">
              <option value="0">请选择</option>
              <?php foreach($stories as $key => $val):?>
              <option value="<?php echo $val->id;?>" <?php echo $val->id == $current_story_id && $k == 0?'selected':'';?>><?php echo $key;?>:<?php echo $val->name;?>(优先级:<?php echo $val->level;?>, 预计工时: <?php echo $val->estimate;?>)</option>
              <?php endforeach;?>
              <option value="<?php echo $current_story_id;?>" <?php echo $k>0?'selected':'';?>>同上</option>
            </select>
          </td>
          <td><input type="text" name="name[]" class="text-1"></td>
          <td><select name="type[]">
              <?php foreach ($pmsdata['task']['types'] as $val):?>
                <option value="<?php echo $val['value'];?>"><?php echo $val['display'];?></option>
              <?php endforeach;?>
              </select></td>
          <td><select name="assigned_to[]">
                <?php $assigned_to = empty($task->assigned_to)?0:$task->assigned_to->id;?>
                <?php foreach($all_user as $val):?>
                  <option value="<?php echo $val->id;?>" <?php echo ($val->id == $assigned_to)?'selected':''?>><?php echo $val->real_name;?></option>
                <?php endforeach;?>
              </select></td>
          <td><input type="text" name="estimate[]" value="" class="text-1"></td>
          <td><input type="text" name="deadline[]" class="text-1 datetime"></td>
          <td><input type="text" name="description[]" id="desc[0]" value="" class="text-1"></td>
          <td><select name="level[]">
                <?php $level = array(1,2,3,4,5);foreach($level as $val):?>
                  <option value="<?php echo $val;?>"><?php echo $val;?></option>
                <?php endforeach;?>
              </select></td>
          <td><select name="difficulty[]">
                <?php $difficulty = array(1,2,3,4,5);foreach($difficulty as $val):?>
                  <option value="<?php echo $val;?>"><?php echo $val;?></option>
                <?php endforeach;?>
              </select></td>
          <td>
            <select name="need_test">
              <option value="1">需要测试</option>
              <option value="0">无需测试</option>
            </select>
          </td>
          <td>
            <select name="test_by">
              <?php $test_by = empty($current_story->opened_by)?0:$current_story->opened_by;?>
              <?php foreach($all_user as $val):?>
                <option value="<?php echo $val->id;?>" <?php echo ($val->id == $test_by)?'selected':''?>><?php echo $val->real_name;?></option>
              <?php endforeach;?>
            </select>
          </td>
        </tr>
        <?php endforeach;?>
        <tr>
          <td colspan="8" class="a-center"><input type="submit" id="submit" value="保存" class="button-s">
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