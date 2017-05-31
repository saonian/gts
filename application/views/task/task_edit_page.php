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
    <form method="post" action="/task/save" enctype="multipart/form-data">
      <input name="action" value="edit" type="hidden"/>
      <input name="body" value="<?php echo $body;?>" type="hidden"/>
    <div id="titlebar">
      <div id="main">TASK #<?php echo $task->id;?> <input type="text" name="name" value="<?php echo $task->name;?>" class="text-5"/></div>
    </div>
      <input type="hidden" name="task_id" value="<?php echo $task->id;?>" />
    <table class="cont-rt5">
      <tbody>
        <tr valign="top">
          <td><fieldset>
              <legend>需求描述</legend>
              <div class="content">
                <textarea name="description" class="editor"><?php echo $task->description;?></textarea>
              </div>
            </fieldset>
            <fieldset>
              <legend>备注</legend>
              <div class="content">
                <textarea name="comment" class="editor"></textarea>
              </div>
            </fieldset>
            <fieldset>
              <legend>附件</legend>
              <div> 
                <?php foreach ($task->attachments as $val):?>
                <a href="/download/<?php echo $val->id;?>"><?php echo empty($val->title) ? basename($val->path) : $val->title.$val->extension;?></a> 
                <input type="button" value=" 删除 " onclick="window.location.href='/delfile/<?php echo $val->id;?>'" class="button-c"><br/>
                <?php endforeach;?>
                <div id="fileform">
                    <div id="fileBox1" class="fileBox">
                    <input type="file" tabindex="-1" class="fileControl" name="files[]">
                    <label class="fileLabel" tabindex="-1">标题：</label>
                    <input type="text" tabindex="-1" class="text-3" name="labels[]"> 
                    <input type="button" onclick="addFile(this)" value="增加"/>
                    <input type="button" onclick="delFile(this)" value="删除"/>
                  </div>  <div id="fileBox2" class="fileBox">
                    <input type="file" tabindex="-1" class="fileControl" name="files[]">
                    <label class="fileLabel" tabindex="-1">标题：</label>
                    <input type="text" tabindex="-1" class="text-3" name="labels[]"> 
                    <input type="button" onclick="addFile(this)" value="增加"/>
                    <input type="button" onclick="delFile(this)" value="删除"/>
                  </div></div>
              </div>
            </fieldset>
            <div class="a-center">
              <input type="submit" class="button-s" value="保存" id="submit">
              <input type="button" class="button-r" onclick="<?php echo empty($_SERVER['HTTP_REFERER'])?"history.back();":"window.location.href='{$_SERVER['HTTP_REFERER']}'";?>" value="返回">
            </div>
            <div id="actionbox">
              <fieldset>
                <legend> 历史记录 </legend>
                <ol id="historyItem">
                  <?php foreach ($task->actions as $key => $val): ?>
                  <li value="<?php echo ++$key.'.';?>"> <span> <?php echo $val->date;?>, 由 <strong><?php echo $val->actor;?></strong> <?php echo $pmsdata[$val->type]['action'][$val->action]['display']?>。 </span>
                    <?php if(!empty($val->history)):?>
                    <div id="changeBox5" class="changes" style="display: block;">
                      <?php foreach ($val->history as $key => $val): ?>
                            <?php if(empty($val->diff)):?>
                            修改了 <strong><i><?php echo $val->field;?></i></strong>，旧值为 "<?php echo $val->old;?>"，新值为 "<?php echo $val->new;?>"。<br>
                          <?php else:?>
                            修改了 <strong><i><?php echo $val->field;?></i></strong>，区别为 <blockquote><?php echo $val->diff;?></blockquote><br>
                          <?php endif;?>
                      <?php endforeach;?>
                    </div>
                    <?php endif;?>
                    <?php if(!empty($val->comment)):?>
                    <div class="comment149" style="display: block;"><?php echo $val->comment;?></div>
                    <?php endif;?>
                  </li>
                  <?php endforeach;?>
                </ol>
              </fieldset>
            </div>
          <td class="divider"></td>
          <td class="side">
            <fieldset>
              <legend>基本信息</legend>
              <table class="table-1">
                <tbody>
                  <tr>
                    <td class="rowhead">所属项目</td>
                    <td>
                      <select name="project_id" class="text-3">
                        <option value="0"></option>
                        <?php foreach ($all_project as $val):?>
                          <option value="<?php echo $val->id;?>" <?php echo ($val->id == $task->project_id)?'selected':''?>><?php echo $val->name;?></option>
                        <?php endforeach;?>
                        </select>
                      </td>
                  </tr>
                  <tr>
                    <td class="rowhead">相关需求</td>
                    <td>
                      <select name="story_id" class="text-3">
                        <option value="0"></option>
                        <?php foreach ($all_story as $val):?>
                          <option value="<?php echo $val->id;?>" <?php echo ($val->id == $task->story_id)?'selected':''?>><?php echo $val->name;?></option>
                        <?php endforeach;?>
                        </select>
                    </td>
                  </tr>
                  <tr>
                  <tr>
                    <td class="rowhead">指派给</td>
                    <td>
                      <select name="assigned_to" id="assigned_to" class="text-2" onchange="get_assign_name(this);">
                        <option value="0"></option>
                        <?php $assigned_to = empty($task->assigned_to)?0:$task->assigned_to->id;?>
                        <?php foreach($all_user as $val):?>
                          <option value="<?php echo $val->id;?>" <?php echo ($val->id == $assigned_to)?'selected':''?>><?php echo $val->real_name;?></option>
                        <?php endforeach;?>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td class="rowhead">是否测试</td>
                    <td>
                      <input type="radio" name="need_test" value="1" class="required" <?php echo $task->need_test==1?'checked':'';?>/>需要测试
                      <input type="radio" name="need_test" value="0" class="required" <?php echo $task->need_test==0?'checked':'';?>/>无需测试
                    </td>
                  </tr>
                  <tr id="tester">
                    <td class="rowhead">测试人</td>
                    <td>
                      <select name="test_by" class="text-2">
                        <option value="0">请选择</option>
                        <?php $test_by = empty($task->test_by)?0:$task->test_by->id;?>
                        <?php foreach($all_user as $val):?>
                          <option value="<?php echo $val->id;?>" <?php echo ($val->id == $test_by)?'selected':''?>><?php echo $val->real_name;?></option>
                        <?php endforeach;?>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <th class="rowhead">是否总结</th>
                    <td>
                      <input type="radio" name="need_summary" value="1" class="required" <?php echo $task->need_summary==1?'checked':'';?>/>需要总结
                      <input type="radio" name="need_summary" value="0" class="required" <?php echo $task->need_summary==0?'checked':'';?>/>无需总结
                      <span class="star"> * </span>
                    </td>
                  </tr>
                  <tr id="summary">
                    <th class="rowhead">总结人</th>
                    <td><span id="summary_by"></span></td>
                  </tr>
                  <tr>
                  <tr>
                    <td class="rowhead">任务类型</td>
                    <td>
                      <select name="type" class="text-3">
                        <?php foreach ($pmsdata['task']['types'] as $val):?>
                          <option value="<?php echo $val['value'];?>" <?php echo ($val['value'] == $task->type)?'selected':''?>><?php echo $val['display'];?></option>
                        <?php endforeach;?>
                        </select>
                    </td>
                  </tr>
                  <tr>
                    <td class="rowhead">任务状态</td>
                    <td><select name="status" class="text-3">
                    <?php foreach ($pmsdata['task']['status'] as $val):?>
                      <option value="<?php echo $val['value'];?>" <?php echo ($val['value'] == $task->status)?'selected':''?>><?php echo $val['display'];?></option>
                    <?php endforeach;?>
                    </select></td>
                  </tr>
                  <tr>
                    <th class="rowhead">优先级</th>
                    <td><select name="level" class="text-2">
                          <?php $level = array(1,2,3,4,5);foreach($level as $val):?>
                            <option value="<?php echo $val;?>" <?php echo ($val == $task->level)?'selected':''?>><?php echo $val;?></option>
                          <?php endforeach;?>
                        </select>
                    </td>
                  </tr>
                  <tr>
                    <th class="rowhead">难易度</th>
                    <td><select name="difficulty" class="text-2">
                          <?php $difficulty = array(1,2,3,4,5);foreach($difficulty as $val):?>
                            <option value="<?php echo $val;?>" <?php echo ($val == $task->difficulty)?'selected':''?>><?php echo $val;?></option>
                          <?php endforeach;?>
                        </select>
                    </td>
                  </tr>
                </tbody>
              </table>
            </fieldset>
            <fieldset>
              <legend>工时信息</legend>
              <table class="table-1"> 
                <tbody><tr>
                  <th class="rowhead">预计开始</th>
                  <td><input type="text" name="est_started_date" value="<?php echo $task->est_started_date;?>" class="datetime text-3"/></td>
                </tr>  
                <tr>
                  <th class="rowhead">实际开始</th>
                  <td><input type="text" name="real_started_date" value="<?php echo $task->real_started_date;?>" class="datetime text-3"/></td>
                </tr>  
                <tr>
                  <th class="rowhead">截止日期</th>
                  <td><input type="text" name="deadline" value="<?php echo $task->deadline;?>" class="datetime text-3"/></td>
                </tr>  
                <tr>
                  <th class="rowhead w-p20">最初预计</th>
                  <td><input type="text" name="estimate" value="<?php echo $task->estimate;?>" class="text-3"/>工时</td>
                </tr>  
                <tr>
                  <th class="rowhead">总消耗</th>
                  <td><?php echo $task->consumed;?>工时</td>
                </tr>  
                <tr>
                  <th class="rowhead">预计剩余</th>
                  <td><?php echo $task->estimate - $task->consumed;?>工时</td>
                </tr>
              </tbody></table>
            </fieldset>
            <fieldset>
              <legend>需求的一生</legend>
              <table class="table-1">
                <tbody>
                  <tr>
                    <td class="rowhead w-p20">由谁创建</td>
                    <td><?php echo empty($task->opened_by)?'':$task->opened_by->real_name.' 于 '.$task->opened_date;?></td>
                  </tr>
                  <tr>
                    <td class="rowhead">由谁完成</td>
                    <td><?php echo empty($task->finished_by)?'':$task->finished_by->real_name.' 于 '.$task->finished_date;?></td>
                  </tr>
                  <tr>
                    <td class="rowhead">由谁取消</td>
                    <td> <?php echo empty($task->canceled_by)?'':$task->canceled_by->real_name;?> </td>
                  </tr>
                  <tr>
                    <td class="rowhead">由谁关闭</td>
                    <td><?php echo empty($task->closed_by)?'':$task->closed_by->real_name.' 于 '.$task->closed_date;?></td>
                  </tr>
                  <tr>
                    <td class="rowhead">关闭原因</td>
                    <td> <?php echo empty($task->closed_reason)?'':$pmsdata['story']['close_reason'][$task->closed_reason]['display'];?> </td>
                  </tr>
                  <tr>
                    <td class="rowhead">最后修改</td>
                    <td><?php echo empty($task->last_edited_by)?'':$task->last_edited_by->real_name.' 于 '.$task->last_edited_date;?></td>
                  </tr>
                </tbody>
              </table>
            </fieldset>
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
$("input[name=need_test]").change(function(){
  if($(this).val() == 0 && $(this).attr('checked') == 'checked'){
    $("#tester").hide();
  }else{
    $("#tester").show();
  }
});

$("input[name=need_summary]").change(function(){
  if($(this).val() == 0 && $(this).attr('checked') == 'checked'){
    $("#summary").hide();
  }else{
    $("#summary").show();
  }
});
function get_assign_name(obj){
  var uid = $(obj).val();
  var u_name = $("select[name=assigned_to] option[value="+uid+"]").html();
  $("#summary_by").html(u_name);
}
$("#assigned_to").trigger("change");
$("input[name=need_summary]").trigger("change");
$("input[name=need_test]").trigger("change");
</script>