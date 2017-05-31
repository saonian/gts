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
      <strong>批量关闭任务</strong>
      </caption>
      <tbody>
        <tr>
          <th class="w-20px">ID</th>
          <th>任务名</th>
          <th class="red">状态</th>
          <th class="w-60px red">关闭原因</th>
          <th class="">备注</th>
        </tr>
        <?php foreach ($tasks as $key => $val):?>
        <tr class="a-center">
          <input type="hidden" name="task_id[]" value="<?php echo $val->id;?>"/>
          <td><?php echo $val->id;?></td>
          <td class="a-left"><?php echo $val->name;?></td>
          <td><?php echo $pmsdata['task']['status'][$val->status]['display'];?></td>
          <td><select name="closed_reason[]" <?php echo $val->status == $pmsdata['task']['status']['closed']['value']?'disabled="true"':'';?>>
              <?php foreach ($pmsdata['task']['close_reason'] as $v):?>
                <option value="<?php echo $v['value'];?>"><?php echo $v['display'];?></option>
              <?php endforeach;?>
              </select></td>
          <td class="a-left"><input name="comment[]" value="" class="text-1" <?php echo $val->status == $pmsdata['task']['status']['closed']['value']?'disabled="true"':'';?>/></td>
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