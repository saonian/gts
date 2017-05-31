<?php
  $empty_story = new stdClass;
  $empty_story->project_id = $current_project_id;
  $empty_story->product_id = $current_product_id;
  $empty_story->source = '';
  $empty_story->name = '';
  $empty_story->description = '';
  $empty_story->level = 1;
  $empty_story->estimate = '';
  $empty_story->assigned_to = 0;
  $empty_story->review_by = 0;
  $story = !empty($story) ? $story : $empty_story;
?>
<?php
if(!$include_headfoot){
  echo <<<EOF
<link rel="stylesheet" type="text/css" href="/public/css/datetimepicker.css" />
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
    <form method="post" id="dataform" action="/story/save?body=<?php echo $body;?>" enctype="multipart/form-data">
      <table align="center" class="table-1 a-left">
        <caption>
        <div class="f-left"><strong><?php echo empty($story->id)?'添加':'复制'?>需求</strong></div>
        </caption>
        <tbody>
          <tr>
          <th class="rowhead">所属产品</th>
            <td><select name="product_id" id="product_id" class="text-3" onchange="getModules(this.value)">
              <option value="0">无</option>
<!--               <?php foreach ($all_products as $val):?>
                <option value="<?php echo $val->id;?>" <?php echo ($val->id == $story->product_id)?'selected':''?>><?php echo $val->name;?></option>
              <?php endforeach;?> -->
              </select>
              <select name="module_id" id="module_id">
                <option value="0">/</option>
              </select>
            </td>
          </tr>
          <tr>
          <tr>
          <th class="rowhead">所属项目</th>
            <td><select name="project_id" class="text-3" onchange="getProjects(this.value)">
              <?php foreach ($all_project as $val):?>
                <option value="<?php echo $val->id;?>" <?php echo ($val->id == $story->project_id)?'selected':''?>><?php echo $val->name;?></option>
              <?php endforeach;?>
              </select>
            </td>
          </tr>
          <tr>
          <th class="rowhead">需求来源</th>
            <td><select name="source" class="text-3">
              <?php foreach ($pmsdata['story']['sources'] as $val):?>
                <option value="<?php echo $val['value'];?>" <?php echo ($val['value'] == $story->source)?'selected':''?>><?php echo $val['display'];?></option>
              <?php endforeach;?>
              </select>
            </td>
          </tr>
          <tr>
            <th class="rowhead">需求名称</th>
            <td><input type="text" name="name" id="name" value="<?php echo $story->name;?>" class="text-5 required">
              <span class="star"> * </span>
              <span style="color:red; display:block;">为保证需求后续查询，请将需求名称及需求描述尽量写完整，尽可能将需求描述都在此填写，不建议使用附件文档</span>
            </td>
          </tr>
          <tr>
            <th class="rowhead">需求描述</th>
            <td>
              <textarea name="description" id="description" rows="6" class="editor required"><?php echo $story->description;?></textarea><span class="star"> * </span>
              <span style="color:red; display:block;">注意：如果描述中需要包含程序代码，请将代码放入附件或使用编辑器上的插入代码功能，不要直接粘贴到编辑器。</span>
            </td>
          </tr>
          <tr>
            <th class="rowhead">优先级</th>
            <td><select name="level" class="text-2">
                  <?php $level = array(1,2,3,4,5);foreach($level as $val):?>
                    <option value="<?php echo $val;?>" <?php echo ($val == $story->level)?'selected':''?>><?php echo $val;?></option>
                  <?php endforeach;?>
                </select>
                <font color="red">等级1最低,等级5最高</font>
            </td>
          </tr>
          <tr>
            <th class="rowhead">预计工时</th>
            <td><input type="text" name="estimate" value="<?php echo $story->estimate;?>" class="text-2 required" placeholder="完成该需求的工作量">小时<span class="star"> * </span></td>
          </tr>
<!--           <tr>
            <th class="rowhead">指派给</th>
            <td><select name="assigned_to">
                  <option value="0"></option>
                  <?php $assigned_to = empty($story->assigned_to)?0:$story->assigned_to->id;?>
                  <?php foreach($all_user as $val):?>
                    <option value="<?php echo $val->id;?>" <?php echo ($val->id == $assigned_to)?'selected':''?>><?php echo $val->real_name;?></option>
                  <?php endforeach;?>
                </select>
            </td>
          </tr> -->
          <tr>
            <th class="rowhead">由谁评审</th>
            <td><select name="reviewed_by" id="reviewed_by" class="text-2">
                  <?php $reviewed_by = empty($story->reviewed_by)?0:$story->reviewed_by->id;?>
                  <?php foreach($all_user as $val):?>
                    <option value="<?php echo $val->id;?>" <?php echo ($val->id == $reviewed_by)?'selected':''?>><?php echo $val->real_name;?></option>
                  <?php endforeach;?>
                </select>
            </td>
          </tr>
          <tr>
            <th class="rowhead">附件</th>
              <td><div id="fileform">
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
<input type="hidden" id="all_modules" />
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
function switchProject(pid){
  $.get('/team/get_verify_users/'+pid, function(data){
    $("#reviewed_by").empty();
    for(i in data){
      $("#reviewed_by").append('<option value="'+data[i].id+'">'+data[i].real_name+'</option>');
    }
  }, 'json');
}
$("#name").val("<?php echo isset($bug)?$bug->title:'';?>");
<?php 
$steps = isset($bug)?$bug->steps:'';
$steps = str_replace(array("\r\n", "\r", "\n"), '', $steps);
?>
$("#description").val('<?php echo $steps;?>');

//var modulesData = $.parseJSON('<?php echo $all_modules;?>');
var modulesData = '';
function getModules(productId){
  if(productId != 0){
    for(i in modulesData){
      if(i == productId){
        $("#module_id").html('<option value="0">/</option>');
        $.each(modulesData[i], function(index, val) {
          $("#module_id").append('<option value="'+val.id+'">'+val.name+'</option>')
        });
      }
    }
  }else{
    $("#module_id").html('<option value="0">/</option>');
  }
}
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
          if(msg.products != ''){
            $("#product_id").html('<option value="0">无</option>');
            $.each(msg.products, function(index, val) {
              $("#product_id").append('<option value="'+val.id+'">'+val.name+'</option>')
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
$("#product_id").trigger("change");
$(document).ready(
  getProjects('<?php echo $story->project_id;?>')
);

</script>