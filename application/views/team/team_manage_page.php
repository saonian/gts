<form method="post" id="myform" action="/team/team_manage">
  <table class="table-4 a-center" align="center"> 
    <tbody><tr>
      <th style="text-align:center"><b>所属部门</b></th>	
      <th style="text-align:center"><b>用户</b></th>
      <th style="text-align:center"><b>角色</b></th>
      <th style="text-align:center"><b>操作</b></th>
    </tr>
    <tr>
      <td>
  		<select name="deparment[]" onchange="changeDept(this)" id="department">
  			<option value="">选择部门</option>
  			<?php foreach($departments as $key=>$val){
  				echo '<optgroup label="'.$val['name'].'">';
  				echo '<option value="'.$val['id'].'">所有组</option>';
  				foreach ($val['child'] as $k => $v) {
  					echo '<option value="'.$v['id'].'">'.$v['name'].'</option>';
  				}
  				echo '</optgroup>';
  			}?>
  		</select>
  	  </td>
      <td>
		<select name="users[]" onchange="changeSelect(this)" id="users">
			<option value="0">选择团队成员</option>
			<?php foreach($user_list as $key=>$val){
				echo '<option value="'.$val['id'].'" role="'.$val['role'].'">'.$val['real_name'].'</option>';
			}?>
		</select>
	  </td>
      <td>
		<input name="roles[]" value="" class="text-2" type="text">
	  </td>
      <td align="center"><input type="button" value="+" class="button-r" onclick="add_line(this)"></td>
    </tr>

	<tr>
		<td colspan="3">
			<input type="hidden" name="project_id" value="">
			<input type="submit" value="保存" class="button-s" onclick="return checkForm()">&nbsp&nbsp
			<input type="button" value="返回" class="button-r" onclick="location.href='/team/index'">&nbsp&nbsp
		</td>
	</tr>
  </tbody></table>


  <script type="text/javascript">

	var option ;
	$.ajax({
	   type: "POST",
	   url: "/team/user_list_ajax",
	   dataType:'json',
	   async: false,
	   success: function(msg){
			$.each(msg, function(key, val){
				option += '<option value="'+val.id+'" role="'+val.role+'">'+val.real_name+'</option>';
			});
	   }
	});
  
  function changeSelect(obj){
		var value = $.trim($(obj).val());
		var role = $(obj).find('option:selected').attr('role');
		 $(obj).parent().parent().find('input[type=text]').val(role);
  }
  

  function add_line(obj){
    var dptHtml = $("#department").parent().html();
		var html = '<tr><td>'+dptHtml+'</td><td><select name="users[]" onchange="changeSelect(this)"><option value="">选择团队成员</option>'+option+'</select></td><td><input value=""  name="roles[]" class="text-2" type="text"></td><td><input type="button" value="+" class="button-r" onclick="add_line(this)">&nbsp;<input type="button" value="-" class="button-r" onclick="cut_line(this)"></td></tr>';

		$(obj).parent().parent().after(html);
  }

  function cut_line(obj){
		$(obj).parent().parent().remove();
  }

  function checkForm(){
  	var uid = $('#users').val();
    if(uid == 0){
      alert("请选择用户");
      return false;
    }
  }

  function changeDept(obj){
  	var $dpts = $(obj);
  	$.ajax({
  	   type: "POST",
  	   url: "/team/user_list_ajax/"+$dpts.find("option:selected").val(),
  	   dataType:'json',
  	   async: false,
  	   success: function(msg){
        var target = $dpts.parents("tr").find("select[name='users[]']");
        console.log(target);
        target.empty();
  			$.each(msg, function(key, val){
  				target.append('<option value="'+val.id+'" role="'+val.role+'">'+val.real_name+'</option>');
  			});
        target.trigger("change");
  	   }
  	});
  }
  </script>
