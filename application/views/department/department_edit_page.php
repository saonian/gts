<div id="wrap">
  <div class="outer">
    <form method="post" id="dataform" action="/department/department_update">
      <table align="center" class="table-1 a-left">
        <caption>
        <div class="f-left"><strong>部门编辑</strong><input type="hidden" name="level" value="<?php echo $data['id']?>"></div>
        </caption>
        <tbody>
			<tr class="tr_<?php echo $data['id']?>"><td width="100%">
				<table>
					<tr>
						<td><b>部门名称</b></td>
						<td>
							<input type="text" name="names[<?php echo $data['id']?>]" value="<?php echo $data['name']?>" id="names_<?php echo $data['id']?>" cid="<?php echo $data['id']?>" class="text-3">&nbsp;
							<span style="cursor:pointer" fid="<?php echo $data['id']?>" onclick="add_son(this)">[+]</span>
						</td>
						<td>
							<label>状态</label>
							<select id="is_enable_<?php echo $data['id']?>" name="is_enable[<?php echo $data['id']?>]">
								<option value="1" <?php if($data['is_enable']=='1'){echo "selected";}?>>启用</option>
								<option value="0" <?php if($data['is_enable']=='0'){echo "selected";}?>>停用</option>
							</select>&nbsp;
						</td>
					</tr>
				</table>
			</td></tr>	

			<?php if(isset($data['child']) && count($data['child'])>0){ ?>
			<?php foreach($data['child'] as $key=>$val){ ?>	
				<tr class="tr_<?php echo $val['id']?>"><td>
					<table><tr>
						<td><span style="padding-left:20px;">子部门(2级)</span></td>
						<td>
							<input type="text" name="names[<?php echo $val['id']?>]" id="names_<?php echo $val['id']?>" value="<?php echo $val['name']?>" cid="<?php echo $val['id']?>" class="text-3">&nbsp;
							<span style="cursor:pointer" fid="<?php echo $val['id']?>" sid="2" onclick="add_level(this)">[+]</span>&nbsp;&nbsp;<span style="cursor:pointer" fid="<?php echo $val['id']?>" onclick="cut_son(this)">[-]</span>
						</td>
						<td>
							<label>状态</label>
							<select id="is_enable_<?php echo $val['id']?>" name="is_enable[<?php echo $val['id']?>]">
								<option value="1" <?php if($val['is_enable']=='1'){echo "selected";}?>>启用</option>
								<option value="0" <?php if($val['is_enable']=='0'){echo "selected";}?>>停用</option>
							</select>&nbsp;
						</td>
					</tr></table>
				</td></tr>

				<?php if(isset($val['child']) && count($val['child'])>0){ ?>
				<?php foreach($val['child'] as $k3=>$item3){ ?>	
					<tr class="tr_<?php echo $val['id']?> tr_<?php echo $item3['id']?>"><td>
						<table><tr>
							<td><span style="font-style:italic;padding-left:60px;">子部门(3级)</span></td>
							<td>
								<input type="text" name="names[<?php echo $item3['id']?>]" id="names_<?php echo $item3['id']?>" value="<?php echo $item3['name']?>" cid="<?php echo $item3['id']?>" class="text-3">&nbsp;
								<span style="cursor:pointer" sid="3" fid="<?php echo $item3['id']?>" onclick="add_level(this)">[+]</span>&nbsp;&nbsp;<span style="cursor:pointer" fid="<?php echo $item3['id']?>" onclick="cut_son(this)">[-]</span>
							</td>
							<td>
								<label>状态</label>
								<select id="is_enable_<?php echo $item3['id']?>" name="is_enable[<?php echo $item3['id']?>]">
									<option value="1" <?php if($item3['is_enable']=='1'){echo "selected";}?>>启用</option>
									<option value="0" <?php if($item3['is_enable']=='0'){echo "selected";}?>>停用</option>
								</select>&nbsp;
							</td>
						</tr></table>
					</td></tr>

					<?php if(isset($item3['child']) && count($item3['child'])>0){ ?>
					<?php foreach($item3['child'] as $k4=>$item4){ ?>	
						<tr class="tr_<?php echo $val['id']?> tr_<?php echo $item3['id']?> tr_<?php echo $item4['id']?>"><td>
							<table><tr>
								<td><span style="padding-left:150px;">子部门(4级)</span></td>
								<td>
									<input type="text" name="names[<?php echo $item4['id']?>]" id="names_<?php echo $item4['id']?>" value="<?php echo $item4['name']?>" cid="<?php echo $item4['id']?>" class="text-3">&nbsp;
									<span style="cursor:pointer" sid="4" fid="<?php echo $item4['id']?>" onclick="add_level(this)">[+]</span>&nbsp;&nbsp;<span style="cursor:pointer" fid="<?php echo $item4['id']?>" onclick="cut_son(this)">[-]</span>
								</td>
								<td>
									<label>状态</label>
									<select id="is_enable_<?php echo $item4['id']?>" name="is_enable[<?php echo $item4['id']?>]">
										<option value="1" <?php if($item4['is_enable']=='1'){echo "selected";}?>>启用</option>
										<option value="0" <?php if($item4['is_enable']=='0'){echo "selected";}?>>停用</option>
									</select>&nbsp;
								</td>
							</tr></table>
						</td></tr>
					<?php } ?>
					<?php } ?>
				<?php } ?>
				<?php } ?>
			<?php } ?>
			<?php } ?>
          <tr>
            <td colspan="2" class="a-center">
				<input type="hidden" id="cut_id" name="cut_id" value="">
				<input type="submit" id="submit" value="保存" onclick="return checkForm()" class="button-s">
				<input type="reset" id="reset" value="重填" class="button-r">
				<input type="button" id="button" value="返回" class="button-r" onclick="location.href='/department/index'"></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
  <div id="divider"></div>
</div>


<script type="text/javascript">
function add_son(obj){
	var fid = $(obj).attr('fid');

	var html = '<tr><td width="100%"><table><tr><td><span style="padding-left:20px;">子部门(2级)</span></td><td><input type="text" name="new_department['+fid+'][]" value="" class="text-3">&nbsp;<span fid="0" style="cursor:pointer" onclick="add_level(this)">[+]</span> &nbsp;<span fid="0" style="cursor:pointer" onclick="cut_son(this)">[-]</span></td><td><label>状态</label><select name="is_enable_new[]"><option value="1" selected>启用</option><option value="0">停用</option></select>&nbsp;</td></tr></table></td></tr>';
	
	$('.tr_'+fid).after(html);
}


function cut_son(obj){
	var fid = $(obj).attr('fid');
	if(fid > 0){
		if(confirm('确定删除该部门？')){
			var del_id = '';
			$('.tr_'+fid+' input[name^=names]').each(function(){
				var cid = $(this).attr('cid');
				del_id += cid+',';
			});
			var cut_id = $.trim($('#cut_id').val());
			var new_cut_id = cut_id+del_id;
			$('#cut_id').val(new_cut_id);

			$('.tr_'+fid).remove();
		}
	}else{
		$(obj).parent().parent().parent().parent().parent().remove();
	}
}




function add_level(obj){
	var fid = $(obj).attr('fid');
	var sid = $(obj).attr('sid');
	var new_sid = parseInt(sid)+1;

	if(fid > 0){
		if(new_sid >= 5){
			alert('暂时只支持4级部门添加');
			return false;
		}
		
		if(new_sid == 4){
			var html = '<tr><td width="100%"><table><tr><td><span style="padding-left:150px;">子部门('+new_sid+'级)</span></td><td><input type="text" name="new_department['+fid+'][]" value="" class="text-3">&nbsp;<span fid="0" style="cursor:pointer" onclick="add_level(this)">[+]</span> &nbsp;<span fid="0" style="cursor:pointer" onclick="cut_son(this)">[-]</span></td><td><label>状态</label><select name="is_enable_new[]"><option value="1" selected>启用</option><option value="0">停用</option></select>&nbsp;</td></tr></table></td></tr>';
		}else if(new_sid == 3){
			var html = '<tr><td width="100%"><table><tr><td><span style="padding-left:60px;font-style:italic;">子部门('+new_sid+'级)</span></td><td><input type="text" name="new_department['+fid+'][]" value="" class="text-3">&nbsp;<span fid="0" style="cursor:pointer" onclick="add_level(this)">[+]</span> &nbsp;<span fid="0" style="cursor:pointer" onclick="cut_son(this)">[-]</span></td><td><label>状态</label><select name="is_enable_new[]"><option value="1" selected>启用</option><option value="0">停用</option></select>&nbsp;</td></tr></table></td></tr>';
		}

		
		$(obj).parent().parent().parent().parent().parent().parent().after(html);
	}else{
		alert('无法隔级添加，请逐级添加部门');
		return false;
	}
}





function checkForm(){
	var names = [];
	var ids = [];
	var new_names = [];

	var null_num = 0;
	$('input[name^=names]').each(function(){
		var value = $.trim($(this).val());
		if(value == ''){
			null_num++;
		}
		if(null_num > 0){
			alert('部门名称不能为空');
			$(this).focus();
			return false;
		}
		var cid = $(this).attr('cid');
		names.push(value);
		ids.push(cid);
	});

	if(null_num > 0){
		return false;
	}

	
	var is_exist = [];
	if(names.length>0 && ids.length>0 && names.length==ids.length){
		$.ajax({
		   type: "POST",
		   url: "/department/check_name",
		   data: {ids:ids,names:names},
		   dataType:'json',
		   async: false,
		   success: function(msg){
				$.each(msg,function(key,item){
					if(item == 1){
						is_exist.push(key);	
					}
				})
		   }
		});
	}

	if(is_exist.length > 0){
		alert('该部门名称已存在');
		$('#names_'+is_exist[0]).focus();
		return false;
	}
	

	var n = 0;
	$('input[name^=new_department]').each(function(){
		var val = $.trim($(this).val());
		
		$.ajax({
		   type: "POST",
		   url: "/department/check_name",
		   data: {name:val},
		   dataType:'json',
		   async: false,
		   success: function(msg){
				if(msg == 1){
					n++;
				}
		   }
		});

		if(n > 0){
			alert('部门名字有重复');
			$(this).focus();
			return false;
		}
	});

	if(n > 0){
		return false;
	}
}

</script>