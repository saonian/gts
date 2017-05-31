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
    <form method="post" action="/story/save?body=<?php echo $body;?>" enctype="multipart/form-data">
    <div id="titlebar">
      <div id="main">STORY #<?php echo $story->id;?> <input type="text" name="name" value="<?php echo $story->name;?>" class="text-5"/></div>
    </div>
      <input type="hidden" name="story_id" value="<?php echo $story->id;?>" />
      <input type="hidden" id="project_id" name="project_id" value="<?php echo $story->project_id;?>" />
    <table class="cont-rt5">
      <tbody>
        <tr valign="top">
          <td><fieldset>
              <legend>需求描述</legend>
              <div class="content">
                <textarea name="description" rows="6" class="editor"><?php echo $story->description;?></textarea>
              </div>
            </fieldset>
            <fieldset>
              <legend>备注</legend>
              <div class="content">
                <textarea name="comment" class="editor"></textarea>
              </div>
            </fieldset>
              <legend>附件</legend>
              <div> 
                <?php foreach ($story->attachments as $val):?>
                <a href="/download/<?php echo $val->id;?>"><?php echo empty($val->title) ? basename($val->path) : $val->title.$val->extension;?></a> 
                <input type="button" value=" 删除 " onclick="window.location.href='/delfile/<?php echo $val->id;?>'" class="button-c"><br/>
                <?php endforeach;?>
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
            </fieldset>
            <div class="a-center">
              <input type="submit" class="button-s" value="保存" id="submit">
              <input type="button" class="button-r" onclick="<?php echo empty($_SERVER['HTTP_REFERER'])?"history.back();":"window.location.href='{$_SERVER['HTTP_REFERER']}'";?>" value="返回">
            </div>
            <div id="actionbox">
              <fieldset>
                <legend> 历史记录 </legend>
                <ol id="historyItem">
                  <?php foreach ($story->actions as $key => $val): ?>
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
          <td class="side"><fieldset>
              <legend>基本信息</legend>
              <table class="table-1">
                <tbody>
                  <tr>
                    <td class="rowhead">所属产品</td>
                    <td>
                      <select name="product_id" class="text-2" id="product_id" onchange="getModules(this.value)">
                      <option value="0">无</option>
<!--                       <?php foreach ($all_products as $val):?>
                        <option value="<?php echo $val->id;?>" <?php echo isset($story->product_id) && $story->product_id==$val->id?'selected':'';?>><?php echo $val->name;?></option>
                      <?php endforeach;?> -->
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td class="rowhead">所属模块</td>
                    <td>
                      <select name="module_id" id="module_id" class="text-2">
                      <option value="0">/</option>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td class="rowhead">所属项目</td>
                    <td>
                      <select name="project_id" class="text-2" onchange="getProjects(this.value)">
                      <?php foreach ($all_projects as $val):?>
                        <option value="<?php echo $val->id;?>" <?php echo isset($story->project) && $story->project->id==$val->id?'selected':'';?>><?php echo $val->name;?></option>
                      <?php endforeach;?>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td class="rowhead">来源</td>
                    <td>
                      <select name="source" class="text-2">
                        <?php foreach($pmsdata['story']['sources'] as $val):?>
                          <option value="<?php echo $val['value'];?>" <?php echo ($val['value'] == $story->source)?'selected':''?>><?php echo $val['display'];?></option>
                        <?php endforeach;?>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td class="rowhead">当前状态</td>
                    <td>
                      <select name="status" class="text-2">
                        <?php foreach($pmsdata['story']['status'] as $val):?>
                          <option value="<?php echo $val['value'];?>" <?php echo ($val['value'] == $story->status)?'selected':''?>><?php echo $val['display'];?></option>
                        <?php endforeach;?>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td class="rowhead">所处阶段</td>
                    <td>
                      <select name="stage" class="text-2">
                        <?php foreach($pmsdata['story']['stages'] as $val):?>
                          <option value="<?php echo $val['value'];?>" <?php echo ($val['value'] == $story->stage)?'selected':''?>><?php echo $val['display'];?></option>
                        <?php endforeach;?>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td class="rowhead">优先级</td>
                    <td>
                      <select name="level" class="text-2">
                        <?php $level = array(1,2,3,4,5);foreach($level as $val):?>
                          <option value="<?php echo $val;?>" <?php echo ($val == $story->level)?'selected':''?>><?php echo $val;?></option>
                        <?php endforeach;?>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td class="rowhead" class="text-2">预计工时</td>
                    <td><input type="text" value="<?php echo $story->estimate;?>" name="estimate" class="text-2"/></td>
                  </tr>
                </tbody>
              </table>
            </fieldset>
            <fieldset>
              <legend>需求的一生</legend>
              <table class="table-1">
                <tbody>
                  <tr>
                    <td class="rowhead w-p20">由谁创建</td>
                    <td><?php echo empty($story->opened_by)?'':$story->opened_by->real_name.' 于 '.$story->opened_date;?></td>
                  </tr>
                  <tr>
                    <td class="rowhead">指派给</td>
                    <td>
                      <select name="assigned_to" class="text-2">
                        <option value="0"></option>
                        <?php $assigned_to = empty($story->assigned_to)?0:$story->assigned_to->id;?>
                        <?php foreach($all_user as $val):?>
                          <option value="<?php echo $val->id;?>" <?php echo ($val->id == $assigned_to)?'selected':''?>><?php echo $val->real_name;?></option>
                        <?php endforeach;?>
                      </select>
                    </td>
                  </tr>
                   <tr>
                    <td class="rowhead">由谁评审</td>
                    <td> <?php echo empty($story->reviewed_by)?'':$story->reviewed_by->real_name;?> </td>
                  </tr>
<!--                   <tr>
                    <td class="rowhead">由谁评审</td>
                    <td>
                      <select name="reviewed_by">
                        <option value="0"></option>
                        <?php $reviewed_by = empty($story->reviewed_by)?0:$story->reviewed_by->id;?>
                        <?php foreach($verify_user as $val):?>
                          <option value="<?php echo $val->id;?>" <?php echo ($val->id == $reviewed_by)?'selected':''?>><?php echo $val->real_name;?></option>
                        <?php endforeach;?>
                      </select>
                    </td>
                  </tr> -->
                  <tr>
                    <td class="rowhead">评审时间</td>
                    <td><?php echo empty($story->reviewed_by)?'':$story->reviewed_date;?></td>
                  </tr>
                  <tr>
                    <td class="rowhead">由谁关闭</td>
                    <td><?php echo empty($story->closed_by)?'':$story->closed_by->real_name.' 于 '.$story->closed_date;?></td>
                  </tr>
                  <tr>
                    <td class="rowhead">关闭原因</td>
                    <td> <?php echo empty($story->closed_reason)?'':$pmsdata['story']['close_reason'][$story->closed_reason]['display'];?> </td>
                  </tr>
                  <tr>
                    <td class="rowhead">最后修改</td>
                    <td><?php echo empty($story->last_edited_by)?'':$story->last_edited_by->real_name.' 于 '.$story->last_edited_date;?></td>
                  </tr>
                </tbody>
              </table>
            </fieldset>
            <fieldset>
              <legend>项目任务</legend>
              <table class="table-1 fixed">
                <tbody>
                  <?php foreach ($story->tasks as $key => $val):?>
                  <tr>
                    <td><a href="/task/view/<?php echo $val->id;?>"><?php $val->project_name;?></a> <span title="<?php echo $val->name;?>"><a href="/task-view-310.html">#<?php echo $val->id;?> <?php echo $val->name;?></a> </span><br></td>
                  </tr>
                  <?php endforeach;?>
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
var modulesData = '';
var curModuleId = <?php echo $story->module_id;?>;
function getModules(productId){
  if(productId != 0){
    for(i in modulesData){
      if(i == productId){
        $("#module_id").html('<option value="0">/</option>');
        $.each(modulesData[i], function(index, val) {
          var selected = curModuleId == val.id ? "selected" : "";
          $("#module_id").append('<option '+selected+' value="'+val.id+'">'+val.name+'</option>')
        });
      }
    }
  }else{
    $("#module_id").html('<option value="0">/</option>');
  }
}
var curproduct_id = <?php echo $story->product_id?>;
function getProjects(project_id){
  if(project_id != ''){
    $.ajax({
      type: "POST",
      url: "/story/get_products_by_project",
      dataType:"json",
      data: "project_id="+project_id,
      success:function(msg){
        if(!jQuery.isEmptyObject(msg)){
          modulesData = msg.modules;
          getModules(curproduct_id);
          if(msg.products != ''){
            $("#product_id").html('<option value="0">无</option>');
            $.each(msg.products, function(index, val) {
              var selected = curproduct_id == val.id ? "selected" : "";
              $("#product_id").append('<option '+selected+' value="'+val.id+'">'+val.name+'</option>')
            });
          }else{
            $("#product_id").html('<option value="0">无</option>');
            $("#module_id").html('<option value="0">/</option>');
          }
        }else{
            $("#product_id").html('<option value="0">无</option>');
            $("#module_id").html('<option value="0">/</option>');
        }
      }
    });
  }
}
$(document).ready(
  getProjects('<?php echo $story->project->id;?>')
);
$("#product_id").trigger("change");
</script>