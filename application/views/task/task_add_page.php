<?php
  $empty_task = new stdClass;
  $empty_task->project_id = $current_project_id;
  $empty_task->story_id = $story_id;
  $empty_task->source = '';
  $empty_task->name = '';
  $empty_task->description = '';
  $empty_task->level = 1;
  $empty_task->difficulty = '1';
  $empty_task->type = '';
  $empty_task->estimate = '';
  $empty_task->assigned_to = 0;
  $empty_task->review_by = 0;
  $empty_task->source = '';
  $empty_task->est_started_date = '';
  $empty_task->real_started_date = '';
  $task = !empty($task) ? $task : $empty_task;
?>
<?php
if(!$include_headfoot){
  echo <<<EOF
<link rel='stylesheet' type='text/css' href='/public/css/base_min.css' />
<link rel='stylesheet' type='text/css' href='/public/css/css.css' />
<link rel="stylesheet" type="text/css" href="/public/css/jquery-ui.css" />
<link rel="stylesheet" type="text/css" href="/public/css/jquery-ui-timepicker-addon.min.css" />
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
    <form method="post" id="dataform" action="/task/save" enctype="multipart/form-data">
    <input type="hidden" name="body" value="<?php echo $body;?>" />
      <table align="center" class="table-1 a-left">
        <caption>
        <div class="f-left"><strong><?php echo empty($task->id)?'添加':'复制'?>任务</strong></div>
        </caption>
        <tbody>
          <tr>
          <th class="rowhead">所属项目</th>
            <td><select name="project_id" class="text-3 required">
              <option value="">请选择</option>
              <?php foreach ($all_project as $val):?>
                <option value="<?php echo $val->id;?>" <?php echo ($val->id == $task->project_id)?'selected':''?>><?php echo $val->name;?></option>
              <?php endforeach;?>
              </select>
              <span class="star"> * </span>
            </td>
          </tr>
          <tr>
            <th class="rowhead">指派给</th>
            <td><select name="assigned_to" id="assigned_to" class="text-2 required" onchange="get_assign_name(this);">
                  <?php $assigned_to = empty($task->assigned_to)?0:$task->assigned_to->id;?>
                  <option value="">请选择</option>
                  <?php foreach($all_user as $val):?>
                    <option value="<?php echo $val->id;?>" <?php echo ($val->id == $assigned_to)?'selected':''?>><?php echo $val->real_name;?></option>
                  <?php endforeach;?>
                </select>
                <span class="star"> * </span>
                <span id="msg"></span>
            </td>
          </tr>
          <tr>
          <th class="rowhead">任务类型</th>
            <td><select name="type" class="text-2 required">
              <option value="">请选择</option>
              <?php foreach ($pmsdata['task']['types'] as $val):?>
                <option value="<?php echo $val['value'];?>" <?php echo ($val['value'] == $task->type)?'selected':''?>><?php echo $val['display'];?></option>
              <?php endforeach;?>
              </select>
              <span class="star"> * </span>
            </td>
          </tr>
          <tr>
          <th class="rowhead">相关需求</th>
            <td><select name="story_id" class="text-3 required" onchange="changeStory(this)" id="story">
              <option value="">请选择</option>
              <?php foreach ($all_story as $val):?>
                <option value="<?php echo $val->id;?>" <?php echo ($val->id == $task->story_id)?'selected':''?>><?php echo $val->name;?></option>
              <?php endforeach;?>
              </select>
              <span class="star"> * </span>
            </td>
          </tr>
          <tr>
            <th class="rowhead">任务名称</th>
            <td><input type="text" name="name" id="name" value="<?php echo $task->name;?>" class="text-5 required">
              <span class="star"> * </span></td>
          </tr>
          <tr>
            <th class="rowhead">任务描述</th>
            <td>
              <textarea name="description" id="description" rows="6" class="editor required"><?php echo $task->description;?></textarea><span class="star"> * </span>
              <span style="color:red; display:block;">注意：如果描述中需要包含程序代码，请将代码放入附件或使用编辑器上的插入代码功能，不要直接粘贴到编辑器。</span>
            </td>
          </tr>
          <tr>
            <th class="rowhead">优先级</th>
            <td><select name="level" class="text-2">
                  <?php $level = array(1,2,3,4,5);foreach($level as $val):?>
                    <option value="<?php echo $val;?>" <?php echo ($val == $task->level)?'selected':''?>><?php echo $val;?></option>
                  <?php endforeach;?>
                </select>
                <font color="red">等级1最低,等级5最高</font>
            </td>
          </tr>
          <tr>
            <th class="rowhead">难易度</th>
            <td><select name="difficulty" class="text-2">
                  <?php $difficulty = array(1,2,3,4,5);foreach($difficulty as $val):?>
                    <option value="<?php echo $val;?>" <?php echo ($val == $task->difficulty)?'selected':''?>><?php echo $val;?></option>
                  <?php endforeach;?>
                </select>
                <font color="red">等级1最低,等级5最高</font>
            </td>
          </tr>
          <tr>
            <th class="rowhead">预计工时</th>
            <td><input type="text" name="estimate" value="<?php echo $task->estimate;?>" class="text-2 required" placeholder="完成该需求的工作量">小时<span class="star"> * </span></td>
          </tr>
          <tr>
            <th class="rowhead">预计开始</th>
            <td><input type="text" name="est_started_date" value="<?php echo $task->est_started_date;?>" class="text-3 datetime required"><span class="star"> * </span></td>
          </tr>
          <tr>
            <th class="rowhead">最后完成时间</th>
            <td><input type="text" name="deadline" value="<?php echo $task->estimate;?>" class="text-3 datetime required"><span class="star"> * </span></td>
          </tr>
          <tr>
            <th class="rowhead">是否测试</th>
            <td>
              <input type="radio" name="need_test" value="1" class="required" checked/>需要测试
              <input type="radio" name="need_test" value="0" class="required"/>无需测试
              <span class="star"> * </span>
            </td>
          </tr>
          <tr id="tester">
            <th class="rowhead">测试人</th>
            <td>
              <select name="test_by" class="text-2 required">
                  <?php $test_by = empty($story->opened_by)?0:$story->opened_by;?>
                  <?php foreach($all_user as $val):?>
                    <option value="<?php echo $val->id;?>" <?php echo ($val->id == $test_by)?'selected':''?>><?php echo $val->real_name;?></option>
                  <?php endforeach;?>
              </select>
              <span class="star"> * </span>
            </td>
          </tr>
          <tr>
            <th class="rowhead">是否总结</th>
            <td>
              <input type="radio" name="need_summary" value="1" class="required"/>需要总结
              <input type="radio" name="need_summary" value="0" class="required" checked/>无需总结
              <span class="star"> * </span>
            </td>
          </tr>
          <tr id="summary">
            <th class="rowhead">总结人</th>
            <td><span id="summary_by"></span></td>
          </tr>
          <tr>
            <th class="rowhead">附件</th>
              <td>
                <div id="storyfiles">
                </div>
            <div id="fileform">
            <div id="fileBox1" class="fileBox">
              <input type="file" tabindex="-1" class="fileControl" name="files[]">
              <label class="fileLabel" tabindex="-1">标题：</label>
              <input type="text" tabindex="-1" class="text-3" name="labels[]"> 
              <input type="button" onclick="addFile(this)" value="增加"/>
              <input type="button" onclick="delFile(this)" value="删除"/>
            </div>
            <div id="fileBox2" class="fileBox">
              <input type="file" tabindex="-1" class="fileControl" name="files[]">
              <label class="fileLabel" tabindex="-1">标题：</label>
              <input type="text" tabindex="-1" class="text-3" name="labels[]"> 
              <input type="button" onclick="addFile(this)" value="增加"/>
              <input type="button" onclick="delFile(this)" value="删除"/>
            </div></div>
          </td>
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
  },
  submitHandler:function(form){
    var subBths = $(form).find("input[type=submit]");
    subBths.attr("disabled", "");
    form.submit();
  }
});

$("#assigned_to").change(function(){
  $uid = $(this).val();
  if($uid != ''){
    $.ajax({
       type: "GET",
       url: "/task/get_unfinished_task_count/"+$uid,
       success: function(msg){
          $("#msg").html("未完成任务数："+msg);
       }
    });
  }else{
    $("#msg").html("");
  }
});

function changeStory(obj){
  $story = $(obj);
  $.ajax({
     type: "POST",
     url: "/story/get_story/"+$story.find("option:selected").val(),
     dataType:'json',
     async: false,
     success: function(msg){
        if(!msg){
          return;
        }
        $("#name").val(msg.name);
        if(!editor){
          $("#description").val(msg.description);
        }else{
          editor.html(msg.description);
        }
        $("#storyfiles").empty();
        $.each(msg.attachments, function(key, val){
          $("#storyfiles").append('<input name="file_id[]" type="hidden" value="'+val.id+'"/>');
          $("#storyfiles").append('<a href="/download/'+val.id+'">'+val.title+val.extension+'</a><br/>');
        });
      }
  });
}

$("#assigned_to").trigger("change");
$("#story").trigger("change");

$("input[name=need_test]").change(function(){
  if($(this).val() == 0){
    $("#tester").hide();
  }else{
    $("#tester").show();
  }
});
$("input[name=need_summary]").change(function(){
  if($(this).val() == 0){
    $("#summary").hide();
  }else{
    $("#summary").show();
  }
});
$("input[name=need_summary]").trigger("change");
function get_assign_name(obj){
  var uid = $(obj).val();
  var u_name = $("select[name=assigned_to] option[value="+uid+"]").html();
  $("#summary_by").html(u_name);
}
</script>