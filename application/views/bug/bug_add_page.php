<?php
?>
<div id="wrap">
  <div class="outer">
    <form method="post" id="dataform" action="/bug/save" enctype="multipart/form-data" onsubmit="return check()">
      <table align="center" class="table-1 a-left">
        <caption>
        <div class="f-left"><strong><?php if(isset($res->id)){?>编辑BUG<?php }else{?>提BUG<?php }?></strong></div>
        </caption>
        <tbody>
          <tr>
          <th class="rowhead">所属产品</th>
            <td><select name="product_id" id="product_id" class="text-3" onchange="getModules(this.value)">
              <option value="0">无</option>
              <?php foreach ($all_products as $val):?>
                <option value="<?php echo $val->id;?>" <?php echo $val->id == $res->product_id?'selected':'';?>><?php echo $val->name;?></option>
              <?php endforeach;?>
              </select>
              <select name="module_id" id="module_id">
                <option value="0">/</option>
              </select>
            </td>
          </tr>
          <tr>
          <th class="rowhead">所属项目</th>
            <td><select name="project_id" class="text-3">
              <?php foreach ($all_project as $key => $val):?>
                <option value="<?php echo $val->id;?>" <?php echo $current_project_id == $val->id?'selected':'';?>><?php echo $val->name;?><?php echo empty($pmsdata['project']['status'][$val->status])?'':'('.$pmsdata['project']['status'][$val->status]['display'].')';?></option>
            <?php endforeach;?>
              </select>
            </td>
          </tr>
          <tr>
          <th class="rowhead">当前指派</th>
            <td><select name="assigned_to" class="text-3">
              	<?php foreach($datas as $val):?>
                    <option value="<?php echo $val->id;?>" <?php if(isset($res->assigned_to) && $val->id == $res->assigned_to){echo 'selected';}?>><?php echo $val->real_name;?></option>
                  <?php endforeach;?>
              </select>
            </td>
          </tr>
          <tr>
            <th class="rowhead">bug标题</th>
            <td><input type="text" name="title" id="title" value="<?php if(isset($res->title)){echo $res->title;}?>" class="text-5">
              <span class="star"> * </span></td>
          </tr>
          <tr>
            <th class="rowhead">重现步骤</th>
            <td><textarea name="steps" id="steps" rows="6" class="editor"><?php if(isset($res->steps)){echo $res->steps;}?></textarea></td>
          </tr>
          <tr>
            <th class="rowhead">相关需求</th>
            <td><select name="story_id" class="text-3">
            	<option value="">选择需求</option>
                  <?php foreach ($stories as $story){?>
                  <option value="<?php echo $story->id?>" <?php if(isset($res->story_id) && $story->id == $res->story_id){echo 'selected';}?>><?php echo $story->name;?></option>
                  <?php }?>
                </select>
            </td>
          </tr>
          <tr>
            <th class="rowhead">相关任务</th>
            <td><select name="task_id" class="text-3">
                <option value="">选择任务</option>
                <?php foreach ($tasks as $task){?>
                  <option value="<?php echo $task->id?>" <?php if(isset($res->task_id) && $task->id == $res->task_id){echo 'selected';}?>><?php echo $task->name;?></option>
                  <?php }?>
                </select>
            </td>
          </tr>
          <tr>
            <th class="rowhead">类型</th>
            <td><select name="type" id="type" class="text-3">
            	<option value="">选择bug类型</option>
            <?php foreach ($pmsdata['bug']['types'] as $val){?>
                  <option value="<?php echo $val['value']?>" <?php if(isset($res->type) && $res->type == $val['value']) echo 'selected';?>><?php echo $val['display']?></option> 
            <?php }?>  
                </select>
                <span class="star"> * </span>
            </td>
          </tr>
         
          <tr>
            <th class="rowhead">附件</th>
              <td>
              	<div id="fileform">
              	<?php if(isset($res->attachment)){?>
              	<?php foreach ($res->attachment as $val):?>
                <a href="/download/<?php echo $val->id;?>"><?php echo empty($val->title) ? basename($val->path) : $val->title.$val->extension;?></a> 
                <input type="button" value="删除 " onclick="window.location.href='/delfile/<?php echo $val->id;?>'" class="button-c"><br/>
                <?php endforeach;?>
                <?php }?>
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
            		</div>
           	</div>
          </td>
          </tr>
          <tr>
            <td colspan="2" class="a-center"><input type="submit" id="submit" value="保存" class="button-s"><input type="hidden" name="bug_id" value="<?php if(isset($res->id)) echo $res->id;?>">
              <input type="reset" id="reset" value="重填" class="button-r"> <input type="button" id="fanhui" value="返回" class="button-s" onclick="window.location.href='/bug/'"></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
  <div id="divider"></div>
</div>
<script type="text/javascript">
function check(){
	$title = $("#title").val();
	if($title==''){
		alert('bug 标题不能为空');
		return false;
	}
	$steps = $("#steps").val();
	if($steps==''){
		alert('重现步骤不能为空');
		return false;
	}
	$type = $("#type").val();
	if($type==''){
		alert('bug类型不能为空');
		return false;
	}
	
	return true;
}
var modulesData = $.parseJSON('<?php echo $all_modules;?>');
var curModuleId = <?php echo $res->module_id;?>;
function getModules(productId){
  for(i in modulesData){
    if(i == productId){
      $("#module_id").html('<option value="0">/</option>');
      $.each(modulesData[i], function(index, val) {
        var selected = curModuleId == val.id ? "selected" : "";
        $("#module_id").append('<option '+selected+' value="'+val.id+'">'+val.name+'</option>')
      });
    }
  }
}
$("#product_id").trigger("change");
</script>