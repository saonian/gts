<?php $time= array('0'=>'工作日加班','1'=>'周末假日加班');$daysoff= array('0'=>'否','1'=>'是');$status= array('0'=>'待审核','1'=>'通过','2'=>'驳回');?>
<div id='wrap'>
  <div class='outer'>
    <form id="dataform" method="post" action="/overtime/shenhe_form" onsubmit="return check()">
      <table align="center" class="table-1 a-left">
        <caption class="caption-tl pb-10px">
        	<div class="f-left"><strong>审核</strong></div>
        	<div class="f-right"><strong><a href='/overtime/index'>返回</a></strong></div>
        </caption>
        <tbody>
          <tr>
            <th class="rowhead">相关任务:</th>
            <td>
            	<?php echo $data['task_name'];?>
            </td>
          </tr>
          <tr>
            <th class="rowhead">申请人:</th> 
            <td>
               <?php echo $data['proposer'];?>
            </td>
          </tr>
          <tr>
            <th class="rowhead">加班时段:</th> 
            <td>
            	 <?php echo $time[$data['overtime_time']];?>
            </td>
          </tr>
          <?php if($data['overtime_time']=='1'){?>
          <tr>
            <th class="rowhead">是否调休:</th> 
            <td>
            	 <?php echo $daysoff[$data['is_days_off']];?>
            </td>
          </tr>
          <?php }?>
          <tr>
            <th class="rowhead">加班时间:</th>
            <td>从<?php echo $data['begin'];?> 到 <?php echo $data['end'];?> &nbsp;&nbsp; 共 <?php echo $data['hour_counts']?> 小时
      			</td>
          </tr>
          <tr>
            <th class="rowhead">加班理由:</th>
            <td><?php echo $data['reason'];?></td>
          </tr>
          <tr>
          	<th class="rowhead">申请时间:</th>
          	<td><?php echo $data['create_time']?></td>
          </tr>
          <tr>
          	<th class="rowhead">审核结果:</th> 
            <td>
            	<?php if($data['audit_status']=='0'){?>
            	 <input type="radio" value="1" name="audit_status" <?php if(isset($data['audit_status']) && $data['audit_status']=='1')echo "checked='checked'";?>><label> 通过 </label>
            	 <input type="radio" value="2" name="audit_status" <?php if(isset($data['audit_status']) && $data['audit_status']=='2')echo "checked='checked'";?>><label> 驳回 </label>
                 <span class="star"> * </span>
                 <?php }else{ echo $status[$data['audit_status']];}?>
                 
            </td>
          </tr>
          <tr>
          	<th class="rowhead">备注:</th>
            <td><?php if($data['audit_status']=='0'){?><textarea class="area-4" rows="6" id="remark" name="remark" ><?php if(isset($data['remark']))echo $data['remark'];?></textarea><?php }else{echo $data['remark'];}?></td>
          </tr>
          <tr>
            <td class="a-left" colspan="2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="hidden" value="<?php if(isset($data['id']))echo $data['id'];?>" name="ids"/><?php if($data['audit_status']=='0'){?><input type="submit" class="button-s" value="保存" id="submit">&nbsp;&nbsp;&nbsp;&nbsp;
              <input type="reset" class="button-r" value="重填" id="reset"><?php }?>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" class="button-s" value="返回" onclick="window.location.href='/overtime/index'" /></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
  <div id='divider'></div>
</div>
<script type="text/javascript">
function check(){
	$status = $("input[name='audit_status']:checked").val();
	if($status==null){
		alert('审核结果不能为空');
		return false;
	}
	return true;
}
</script>