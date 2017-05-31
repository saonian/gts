<div id="wrap">
  <div class="outer">
    <form method="post" id="dataform" action="/bug/resolve_form" onsubmit="return check()">
      <input type="hidden" name="bug_id" value="<?php echo $data->id;?>" />
      <input type="hidden" id="project_id" name="project_id" value="<?php echo $data->project_id;?>" />
      <table align="center" class="table-1 a-left">
        <caption>
        <div class="f-left"><strong>解决 (BUG:<?php if(isset($data->title)) echo $data->title;?>)</strong></div>
        </caption>
        <tbody>
          <tr>
          	<th class="rowhead">解决方案</th>
          	<td>
              <select name="resolution" class="text-2">
                <?php foreach($pmsdata['bug']['resolutions'] as $val):?>
                <option value="<?php echo $val['value'];?>" <?php echo isset($data->resolution) && $data->resolution==$val['value']?'selected':'';?>><?php echo $val['display'];?></option>
                <?php endforeach;?>
              </select>
            </td>
          </tr>
          <tr>	
          	<th class="rowhead">解决日期</th>
          	<td><input type="text" name="resolved_date" id="resolved_date" class="datetime text-2" value="<?php if(isset($data->resolved_date)){echo $data->resolved_date;}else{echo '';}?>"></td>
           </tr>
           <tr>
            <th class="rowhead">指派给</th>
            <td><select name="assigned_to" class="text-2">
                  <?php $opened_by = empty($data->opened_by)?0:$data->opened_by;?>
                  <option value="0"></option>
                  <?php foreach($all_user as $val):?>
                    <option value="<?php echo $val->id;?>" <?php echo ($val->id == $opened_by)?'selected':''?>><?php echo $val->real_name;?></option>
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
              <input type="reset" id="reset" value="重填" class="button-r"> <input type="button" value="返回" class="button-s" onclick="window.location.href='/bug/index'"></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
  <div id="divider"></div>
</div>
<script type="text/javascript">
function check(){
	$resolution = $("#resolution").val();
	if($resolution==''){
		alert("解决方案不能为空");
		return false;
	}

	$resolved_date = $.trim($("#resolved_date").val());
	if($resolved_date=='0000-00-00 00:00:00'){
		alert('解决日期不能为空');
		return false;
	}
	return true;
	
}
</script>