<div id='wrap'>
  <div class='outer'>
    <form id="dataform" method="post" action="/overtime/selectduty_form">
      <table align="center" class="table-1 a-left" id="addtable" style="width:100%">
        <caption class="caption-tl pb-10px">
        	<div class="f-left"><strong>值班确认</strong></div>
        </caption>
        <tbody>
          <tr>
            <th class="rowhead">支持内容:</th> 
            <td>
            	 <input type="radio"  value="1" name="support_content" <?php if($data['default'] != 2) echo 'checked';?> id="wangzhan" onclick="change(this)"><label> 网站 </label>
            	 <input type="radio"  value="2" name="support_content" <?php if($data['default'] == 2) echo 'checked';?> id="xitong" onclick="change(this)" ><label> 系统 </label>
            </td>
          </tr>
         
          <tr>
            <th class="rowhead">支持范围:</th>
            <td>
            
            	<textarea class="area-4" rows="6" id="support_range" name="support_range"><?php if($data['default'] != 2) echo $data[0]['desc']; else echo $data[1]['desc']; ?></textarea><span class="star">* </span>
            
            </td>
          </tr>
          <tr>
          	<td></td>
          	<td>
          		<input type="checkbox" name="is_update" id="new_is_update">是否更新<支持范围>默认内容，如不更新，修改内容只保存当前一次
          	</td>
          </tr>																
          <tr>
            <td class="a-left" colspan="2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            	<input type="hidden" value="<?php if(isset($data['id']))echo $data['id'];?>" name="ids" id="select_id"/>
            	<input type="button" class="button-s" value="确认" id="button" onclick="check();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="button" class="button-s" value="返回" id="fanhui" onclick="window.location.href='/overtime/duty'"></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
  <div id='divider'></div>
</div>
<script type="text/javascript">
/**
$(document).ready(function(){
	if($('#wangzhan').attr('checked')){
		$('#support_range').val('ERP、PMS、WMS、ebay-ERP各系统访问及使用支持');
	}
})

function show(){
	
	$('#support_range').val('各网站异常问题手机反馈、客户异常问题反馈、网站功能使用支持');
}
function hidd(){
	$('#support_range').val('ERP、PMS、WMS、ebay-ERP各系统访问及使用支持');
	
}	*/	


function check(){
	var support_content = $("input[name='support_content']:checked").val();
	if(support_content==null){
		alert('支持内容不能为空');
		return false;
	}
	
	var support_range = $('#support_range').val();
	if(support_range==''){
		alert('支持范围不能为空');
		$('#support_range').focus();
		return false;
	}
	
	if($('#new_is_update').is(":checked")){
		var is_update = 1;
	}else{
		var is_update = 2;
	}

	var user_id = $('#select_id').val();
	
	$.ajax({
		   type: "POST",
		   url: "/overtime/selectduty_form",
		   data:  "support_content="+support_content+"&support_range="+support_range+"&is_update="+is_update+"&user_id="+user_id,
		   success: function(msg){
			if(msg == 1){
				//location.reload();
				window.location.href ='/overtime/duty';
			}else{
				layer.alert('提交失败', 8, 'PMS系统提示信息');
			}
		   }
		});
}
function change(obj){
	var val = $(obj).val();
	if(val == 1){
		$("#support_range").val('<?php echo $data[0]['desc']?>');
	}else{
		$("#support_range").val('<?php echo $data[1]['desc']?>');
	}
	
}
</script>