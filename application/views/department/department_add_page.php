<div id="wrap">
  <div class="outer">
    <form method="post" id="dataform" action="/department/department_add">
      <table align="center" class="table-1 a-left">
        <caption>
        <div class="f-left"><strong>创建部门</strong></div>
        </caption>
        <tbody>
			<tr class="level_1"><td width="100%">
				<table>
					<tr>
						<td>部门名称</td>
						<td>
							<input type="text" name="level1" id="level1" value="" class="text-3">&nbsp;
							<span class="add_son_1" sid="1" style="cursor:pointer" onclick="add_son(this)">[+]</span>
						</td>
					</tr>
				</table>
			</td></tr>	

          <tr>
            <td>
			<label>状态</label>&nbsp;&nbsp;
			<select id="is_enable" name="is_enable">
				<option value="1">启用</option>
				<option value="0">停用</option>
			</select>&nbsp;
              <span class="star"> * </span>
			</td>
          </tr>
          <tr>
            <td colspan="2" class="a-center"><input type="submit" id="submit" value="保存" onclick="return checkForm()" class="button-s">
              <input type="reset" id="reset" value="重填" class="button-r"><input type="button" id="button" value="返回" class="button-r" onclick="location.href='/department/index'"></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
  <div id="divider"></div>
</div>

<script type="text/javascript">

function add_son(obj){
	var sid = $(obj).attr('sid');
	var new_sid = parseInt(sid)+1;
		
	var str = '('+new_sid+'级)';
	var length = $('.level_'+new_sid).length;
	var new_length = parseInt(length)+1;

	var html = '<tr class="level_'+new_sid+'"><td width="100%"><table><tr><td>部门名称'+str+'</td><td><input type="text" name="level'+new_sid+'['+new_length+']" cid="'+new_length+'"  pid="'+new_length+'" value="" class="text-3">&nbsp;<span class="add_son_'+new_sid+'" sid="'+new_sid+'" style="cursor:pointer" onclick="add_level(this)">[+]</span> &nbsp;<span sid="'+new_sid+'" class="cut_son_'+new_sid+'" style="cursor:pointer" onclick="cut_son(this)">[-]</span></td></tr></table></td></tr>';
	
	$('.level_'+sid).after(html);
}



function cut_son(obj){
	$(obj).parent().parent().parent().parent().parent().remove();
}


function add_level(obj){
	var sid = $(obj).attr('sid');
	var new_sid = parseInt(sid)+1;
	var pid = $(obj).parent().parent().find('input').attr('pid');
	var cid = $(obj).parent().parent().find('input').attr('cid');
	var fclass = $(obj).parent().parent().attr('fclass');

	var str = '('+new_sid+'级)';
	var length = $('.level_'+new_sid).length;
	var new_length = parseInt(length)+1;

	if(new_sid == 4){
		var html = '<tr><td colspan="2"><table><tr class="level_'+new_sid+'"><td>子部门名称'+str+'</td><td><input type="text" name="level'+new_sid+'['+pid+']['+cid+']['+new_length+']" value="" class="text-3" cid="'+new_length+'" pid="'+pid+'">&nbsp;<span class="add_son_'+new_sid+'" sid="'+new_sid+'" style="cursor:pointer" onclick="add_level(this)">[+]</span> &nbsp;<span sid="'+new_sid+'" class="cut_son_'+new_sid+'" style="cursor:pointer" onclick="cut_son(this)">[-]</span></td></tr></table></td></tr>';
	}else{
		var html = '<tr><td colspan="2"><table><tr class="level_'+new_sid+'"><th class="rowhead">子部门名称'+str+'</th><td><input type="text" name="level'+new_sid+'['+pid+']['+new_length+']" value="" class="text-3" cid="'+new_length+'" pid="'+pid+'">&nbsp;<span class="add_son_'+new_sid+'" sid="'+new_sid+'" style="cursor:pointer" onclick="add_level(this)">[+]</span> &nbsp;<span sid="'+new_sid+'" class="cut_son_'+new_sid+'" style="cursor:pointer" onclick="cut_son(this)">[-]</span></td></tr></table></td></tr>';
	}
	
	$(obj).parent().parent().after(html);
}



function checkForm(){
	var level5_length = $('input[name^=level5]').length;
	if(level5_length > 0){
		alert('暂时只支持4级部门添加');
		return false;
	}

	var names = [];
	var n = 0;
	$('input[name^=level]').each(function(){
		var value = $.trim($(this).val());
		if(value == ''){
			alert('请填写部门名称');
			n++;
			$(this).focus();
			return false;
		}else{
			$.ajax({
			   type: "POST",
			   url: "/department/check_name",
			   data: {name:value},
			   async: false,
			   success: function(msg){
					if(msg == 1){
						n++;
					}
			   }
			});
		}
		if(n > 0){
			alert("部门名称有重复,请重新填写");
			$(this).focus();
			return false;
		}
	});

	if(n > 0){		
		return false;
	}
}
</script>