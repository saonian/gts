<div id="wrap">
  <div class="outer">
    <form method="post" target="hiddenwin" id="dataform" action="/department/department_add">
      <table align="center" class="table-1 a-left">
        <tbody>
         <tr>
			<td>
				<div class="f-left">
				&nbsp;&nbsp;<strong>部门名称 : </strong>
				<strong><?php echo $department_info['name'];?></strong>
				</div>
			</td>
			<td>
				<label><b>状态：</b></label>
				<?php if($department_info['is_enable']=='0'){echo "<font color='red'>";}?>
					<b><?php echo $is_enable[$department_info['is_enable']];?></b>
				<?php if($department_info['is_enable']=='0'){echo "</font>";}?>
			</td>
		 </tr>
		  <?php if(is_array($department_list) && count($department_list)>0){ ?>
		  <?php foreach($department_list as $key=>$val){?>
		  <tr id="category_tr<?php echo $val['id'];?>" fclass="tr_<?php echo $key;?>" val='0' son='1' cid="<?php echo $val['id'];?>">
			  <td>
				&nbsp;&nbsp;<?php if($val['is_exist_child']=='1'){?>
					<b id="is_parent_<?php echo $val['id'];?>" style="cursor:pointer;">[+]</b>
				<?php }else{ ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php } ?>
				<strong style="cursor:pointer;" onclick="get_child_info('<?php echo $val['id'];?>','0','<?php echo $key;?>')"><?php echo $val['name'];?></strong>
			</td>
			<td>
				<label>状态：</label>
				<?php if($val['is_enable']=='0'){echo "<font color='red'>";}?>
					<strong><?php echo $is_enable[$val['is_enable']];?></strong>
				<?php if($val['is_enable']=='0'){echo "</font>";}?>
			</td>
		  </tr>
		  <?php } ?>
		  <?php } ?>
          
		  <tr>
            <td colspan="2" class="a-center"><input type="button" id="submit" value="返回" onclick="location.href='/department/index'" class="button-r">
          </tr>
        </tbody>
      </table>
    </form>
  </div>
  <div id="divider"></div>
</div>

<script type="text/javascript">

function get_child_info(parent_id,cat,key1){
	var val = $('#category_tr'+parent_id).attr('val');
	var son = $('#category_tr'+parent_id).attr('son');	//1顶级 0下级
	if(val == 0){
		if(parent_id > 0){
			$.ajax({
			   type: "POST",
			   url: "/department/get_child_info",
			   async:false,
			   dataType: 'json',	  
			   data: "department_id="+parent_id,
			   success: function(data){
					var html = '';
					if(son=='1'){
						var fclass = $('#category_tr'+parent_id).attr('fclass');
					}else{
						var fclass = $('#category_tr'+parent_id).attr('class');
					}
					if(typeof(data) != 'undefined'){
						cat = parseInt(cat)+1;
						$.each(data,function(key,item){

							html += '<tr id="category_tr'+item.id+'" class="'+fclass+' tr_'+parent_id+'" fclass="tr_'+parent_id+'" cid="'+item.id+'" val="0" son="0">';
							html += '<td><span style="padding-left:'+cat*30+'px;cursor: pointer;" onclick="get_child_info('+item.id+','+cat+',\''+key1+'\');">';

							if(item.is_exist_child == '1'){
								html += '<b id="is_parent_'+item.id+'">[+]</b>';
							}else{
								html += '&nbsp;&nbsp;&nbsp;&nbsp;';
							}
							html += item.name+'</span></td><td><label>状态：</label>'+item.is_enable_zh+'</td></tr>';
						})
						$('#category_tr'+parent_id).after(html);
						$('#category_tr'+parent_id).attr('val','1');
						$('#is_parent_'+parent_id).html('[-]');
					}
			   }
			});
		}
	}else{
		var cid = $('#category_tr'+parent_id).attr('cid');
		$('.tr_'+cid).remove();
		$('#category_tr'+parent_id).attr('val','0');
		$('#is_parent_'+parent_id).html('[+]');
	}
}
</script>