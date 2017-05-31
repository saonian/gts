<div id='wrap'>
  <div class='outer'>
    <form id="dataform" method="post" action="/overtime/add_form" onsubmit="return check()">
      <table align="center" class="table-1 a-left" id="addtable" style="width:100%">
        <caption class="caption-tl pb-10px">
        	<div class="f-left"><strong>加班申请</strong></div>
        </caption>
        <tbody>
          <tr>
            <th class="rowhead">相关任务:</th>
            <td>
            	<select id="task" name="task" class="text-3">
            		<option value='0'>选择任务</option>
            		<?php if(count($stories)>0){?>
            		<?php foreach($stories as $story){?>
            		<option value="<?php echo $story['id']?>" <?php if(isset($data['task_id']) && ($data['task_id']== $story['id'])){echo "selected = 'selected'";}?>><?php echo $story['name']?></option>
            		<?php }?>
            		<?php }?>
            	</select>
            </td>
          </tr>
          <tr>
            <th class="rowhead">加班时段:</th> 
            <td>
            	 <input type="radio"  value="0" name="overtime_time" <?php if(isset($data['overtime_time']) && $data['overtime_time']=='0')echo "checked='checked'";?> onclick="hidd()" checked><label> 工作日加班 </label>
            	 <input type="radio"  value="1" name="overtime_time" <?php if(isset($data['overtime_time']) && $data['overtime_time']=='1')echo "checked='checked'";?> id="shijian" onclick="show()"><label> 周末假日加班 </label>
                 <span class="star"> * </span>
            </td>
          </tr>
         
          <tr>
            <th class="rowhead">加班时间:</th>
            <td>
              <input type="text" id="date_b" name="date_b" class="text-2 date" value="<?php if(isset($data['date_b'])) echo $data['date_b'];?>" style="width:80px;display:none;"/> 
              <select name="hour_b" id="hour_b" class="text-2 change" style="width:50px;">
                <?for($i=18;$i<24;$i++):?>
                <option value="<?php echo $i;?>" <?php echo isset($data['hour_b']) && $i==$data['hour_b']?'selected':'';?>><?php echo $i;?></option>
                <?endfor;?>
              </select>点
              <select name="minutes_b" id="minutes_b" class="text-2 change" style="width:50px;">
                <option value="0" <?php echo isset($data['minutes_b']) && $data['minutes_b'] == 0?'selected':'';?>>00</option>
                <option value="30" <?php echo isset($data['minutes_b']) && $data['minutes_b'] == 30?'selected':'';?>>30</option>
              </select>分 至  
              <input type="text" id="date_e" name="date_e" class="text-2 date" value="<?php if(isset($data['date_e'])) echo $data['date_e'];?>" style="width:80px;display:none;"/> 
              <select name="hour_e" id="hour_e" class="text-2 change" style="width:50px;">
                <?for($i=18;$i<24;$i++):?>
                <option value="<?php echo $i;?>" <?php echo isset($data['hour_e']) && $i==$data['hour_e']?'selected':'';?>><?php echo $i;?></option>
                <?endfor;?>
              </select>点
              <select name="minutes_e" id="minutes_e" class="text-2 change" style="width:50px;">
                <option value="0" <?php echo isset($data['minutes_e']) && $data['minutes_e'] == 0?'selected':'';?>>00</option>
                <option value="30" <?php echo isset($data['minutes_e']) && $data['minutes_e'] == 30?'selected':'';?>>30</option>
              </select>分  共
              <input type="text" id="hour_counts" name="hour_counts" class="text-2" value="<?php echo isset($data['hour_counts'])?$data['hour_counts']:0;?>" style="width:40px;"/>小时
      			<span class="star">* </span>  &nbsp;&nbsp;(备注:周末加班格式 ：2014-01-02 08 点 30 分 至 2014-01-02  18点00 分 共 8小时; <span style="color: red">工作日加班不需要填日期，务必当天填写加班信息</span>)</td>
          </tr>
          
          <tr>
            <th class="rowhead">加班理由:</th>
            <td><textarea class="area-4" rows="6" id="reason" name="reason" ><?php if(isset($data['reason']))echo $data['reason'];?></textarea><span class="star">* </span></td>
          </tr>
          <tr>
            <td class="a-left" colspan="2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="hidden" value="<?php if(isset($data['id']))echo $data['id'];?>" name="ids"/><input type="submit" class="button-s" value="保存" id="submit">&nbsp;&nbsp;&nbsp;&nbsp;
              <input type="reset" class="button-r" value="重填" id="reset">&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" class="button-s" value="返回" id="fanhui" onclick="window.location.href='/overtime/index'"></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
  <div id='divider'></div>
</div>
<script type="text/javascript">
$(document).ready(function(){
	if($('#shijian').attr('checked')){
		$("#date_b,#date_e").show();
		$("#addtable tbody").find('tr:eq(2)').after('<tr id="showtr"><th class="rowhead">是否调休:</th><td><input type="radio" value="0" name="is_days_off" <?php if(isset($data['is_days_off']) && $data['is_days_off']=='0')echo "checked";?>/><label> 否 </label> <input type="radio" value="1" name="is_days_off" <?php if(isset($data['is_days_off']) && $data['is_days_off']=='1') echo "checked";?>><label> 是 </label></td></tr>');
	}
})

function show(){
	$id = $("#showtr");
	if($id.length>0){}else{
	  $("#addtable tbody").find('tr:eq(2)').after('<tr id="showtr"><th class="rowhead">是否调休:</th><td><input type="radio" id="yes" value="0" name="is_days_off" checked /><label> 否 </label><input type="radio" id="no" value="1" name="is_days_off"><label> 是 </label><span class="star"> * </span></td></tr>');
	}
	$("#date_b,#date_e").show();
  $("#hour_b").empty();
  $("#hour_e").empty();
  for(var i=8;i<24;i++){
    $("#hour_b").append("<option value='"+i+"'>"+i+"</option>");
    $("#hour_e").append("<option value='"+i+"'>"+i+"</option>");
  }
}
function hidd(){
	$id = $("#showtr");
	if($id.length>0){
		$id.remove();
	}
	$("#date_b,#date_e").hide();
  $("#hour_b").empty();
  $("#hour_e").empty();
  for(var i=18;i<24;i++){
    $("#hour_b").append("<option value='"+i+"'>"+i+"</option>");
    $("#hour_e").append("<option value='"+i+"'>"+i+"</option>");
  }
}
		


function check(){
	$time = $("input[name='overtime_time']:checked").val();
	if($time==null){
		alert('加班时段不能为空');
		return false;
	}
	$date_b = $('#date_b').val();
	$hour_b = $('#hour_b').val();
	if($date_b==''){
		$hour_b = parseInt($hour_b);
		if($hour_b<18){
			alert('工作日加班时间应改从18点开始');
			$('#hour_b').focus();
			return false;
		}
	}

	$hour_counts = $('#hour_counts').val();
	if($hour_counts==''){
		alert('加班小时数不能为空，请填写清楚加班时间再提交');
		$('#hour_counts').focus();
		return false;
	}
	
	$reason = $('#reason').val();
	if($reason==''){
		alert('加班理由不能为空');
		$('#reason').focus();
		return false;
	}

	return true;
}

$(".change").change(function(){
  var bh = parseInt($("#hour_b").val());
  var bm = parseInt($("#minutes_b").val());
  var eh = parseInt($("#hour_e").val());
  var em = parseInt($("#minutes_e").val());
  var sum = (eh - bh) + (em - bm)/60;
  $("#hour_counts").val(sum);
});
</script>