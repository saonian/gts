<?php 
$sort = $this->input->get('sort');
$sort = (empty($sort) || $sort=='asc')?'desc':'asc';
$status= array('1'=>'待确认','2'=>'已确认','3'=>'驳回');
$is_added= array('待统计','已统计');
?>
<div id="wrap">
  <div class="outer">
    <form id="projectStoryForm" method="get" action="/ratting/auditlist">
    	<input type="hidden" value="<?php echo $params['type']?>" id="type_sel"/>
        <input type="hidden" value="<?php echo $params['id']?>" id="id_sel"/>
    	<table style="width:100%;" class="table-2">
    		<tr>
    			<td>&nbsp;
<!-- 		    		<label>评分名称:</label>
					<select name="level" id="level" onchange="change();">
		                <option value=''>请选择</option>
		                <?php foreach($ratting_sets as $k => $v):?>
		                <option value="<?php echo $k?>" <?php if($params['level'] == $k){?>selected<?php }?>><?php echo $v['title']?></option>
		                <?php endforeach;?>  
		            </select>
		            <select name="type" id="type" onchange="changetype(1);">
		                <option value=''>请选择</option>
		            </select>
		            <select name="id" id="content_id">
		                <option value=''>请选择</option>
		            </select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; -->
    				<label>评分时间:</label>
    				<input type="text" name="addtime_start" style="width:100px;" id="addtime_start" class="date text-2" value="<?php if(isset($params['addtime_start'])){echo $params['addtime_start'];}?>">&nbsp;&nbsp;至&nbsp;&nbsp;
            		<input type="text" style="width:100px;" name="addtime_end" id="addtime_end" class="date text-2" value="<?php if(isset($params['addtime_end'])){echo $params['addtime_end'];}?>">&nbsp;&nbsp;
    				<label>审核时间:</label>
    				<input type="text" style="width:100px;" name="audit_time_start" id="begin" class="date text-3" value="<?php if(isset($params['audit_time_start'])){echo $params['audit_time_start'];}?>">&nbsp;&nbsp;至&nbsp;&nbsp;
            		<input type="text" style="width:100px;" name="audit_time_end" id="end" class="date text-3" value="<?php if(isset($params['audit_time_end'])){echo $params['audit_time_end'];}?>">&nbsp;&nbsp;
            		<select name="status">
            			<option value="">审核状态</option>
            			<option value="1" <?php if($params['status'] == 1){?>selected<?php }?>>待确认</option>
            			<option value="2" <?php if($params['status'] == 2){?>selected<?php }?>>已确认</option>
            			<option value="3" <?php if($params['status'] == 3){?>selected<?php }?>>驳回</option>
            		</select>&nbsp;&nbsp;
            		<select name="is_added">
		              <option value="">统计状态</option>
		              <option value='1' <?php if(isset($params['is_added']) && $params['is_added']=='1'){echo "selected='selected'";}?>>待统计</option>
		              <option value='2' <?php if(isset($params['is_added']) && $params['is_added']=='2'){echo "selected='selected'";}?>>已统计</option>
		            </select>&nbsp;&nbsp;&nbsp;&nbsp;
                    <label>得分人:</label>
                    <input id="rated_name" style="width:100px;" name="rated_name" type="text" class="text-2" value="<?php if(isset($params['rated_name'])){echo $params['rated_name'];}?>">&nbsp;&nbsp;
                    <label>评分人:</label>
                    <input id="rating_name" style="width:100px;" name="rating_name" type="text" class="text-2" value="<?php if(isset($params['rating_name'])){echo $params['rating_name'];}?>">
                    &nbsp;&nbsp;<input type="submit" value="搜索" class="button-s" style="width:60px;cursor:pointer;">
    			</td>
    		</tr>
    	</table>
    </form>
    <input type="button" value="批量确认" onclick="audit_confirm_reback_all('0');" class="button-s"/>&nbsp;<input type="button" value="批量驳回" onclick="audit_confirm_reback_all('1');" class="button-s"/>
      <table class='table-1 fixed colored tablesorter datatable'>
        <thead>
          <tr class="colhead" style="height:30px;">
            <th width="35px;"><input type="checkbox" id="sel" onclick="sel_all(this)" />选择</th>
            <th>评分列表</th>
            <th width="80px;">得分人</th>
            <th width="80px;">评分人</th>
            <th width="120px;">评分时间</th>
            <th width="50px;">评分</th>
            <th width="50px;">评分级别</th>
            <th>评分事件</th>
            <th width="120px;">审核状态</th>
            <th width="80px;">审核人</th>
            <th width="120px;">审核时间</th>
            <th width="80px">得分统计状态</th>
			<th width="80px;">操作</th>
          </tr>
        </thead>
         <tbody>
		 <?php if(count($audit_list['list'])>0){?>
		 <?php foreach($audit_list['list'] as $key => $val){?>
          <tr class="a-center <?php echo ($key%2==0)?'odd':'even';?>" style="height:30px;" >
	            <td id="checkbox<?php echo $val['id']?>"><?php if($val['status'] == 1){?><input type="checkbox" name="id[]" value="<?php echo $val['id']?>" onclick="sel_same(this)"/><?php }?></td>
	            <td><?php echo $val['content'];?></td>
	            <td><?php echo $val['rated_name'];?><input type="hidden" id="rated_uid<?php echo $val['id']?>" value="<?php echo $val['rated_uid']?>" /></td>
	            <td><?php echo $val['rating_name'];?><input type="hidden" id="rating_uid<?php echo $val['id']?>" value="<?php echo $val['rating_uid']?>" /></td>
	            <td><?php echo $val['addtime'];?></td>
	            <td <?php if($val['grade'] < 0){?>style="color:red;"<?php }?>><?php echo $val['grade'];?></td>
	            <td><?php echo $val['level'];?></td>
	            <td><span style="cursor:pointer;" onmouseover="over_f('<?php echo $val['rating_desc'];?>',this);"onmouseout="out_f();"><?php echo mb_substr($val['rating_desc'],0,10,'UTF-8');?></span></td>
	            <td id ="status<?php echo $val['id']?>" ><?php if($val['status'] == 3 && !empty($val['remark']) && $val['remark'] != 'null'){?><span style="cursor:pointer;" onmouseover="over_f('驳回原因：<?php echo $val['remark'] ?>',this);"onmouseout="out_f();">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $status[$val['status']];?>&nbsp;&nbsp;&nbsp;&nbsp;</span><?php }else{ echo $status[$val['status']];}?></td>
	            <td id ="audited_by<?php echo $val['id']?>"><?php echo $val['audited_by'];?></td>
	            <td id ="audit_time<?php echo $val['id']?>"><?php echo $val['audit_time'];?></td>
	            <td><?php if($val['status'] == 3){ ?>&nbsp;<?php }else{ echo $is_added[$val['is_added']];}?></td>
	            <td><?php if($val['status'] == 1){?><a href="javascript:void(0);" name="<?php echo $val['id']?>" onclick="audit_confirm(this);">确认</a> | <a href="javascript:void(0);" onclick="audit_reback(this);" name="<?php echo $val['id']?>">驳回</a><?php }?></td>
          </tr>
		<?php }?>
		<?php }else{ ?>
		 <tr class="a-center <?php echo ($key%2==0)?'odd':'even';?>">
            <td colspan="8">暂时无记录</td>
          </tr>
		<?php }?>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="10">
				<div class="f-left">
					总共 <strong><?php echo $audit_list['total'];?></strong> 个记录 &nbsp;
					共 <strong><?php echo $audit_list['total_page'];?></strong> 页 &nbsp;
				</div>
				<div class="f-left"><?php echo $audit_list['page_html'];?></div>
			</td>
          </tr>
        </tfoot>
     </table>
     <input type="button" value="批量确认" onclick="audit_confirm_reback_all('0');" class="button-s"/>&nbsp;<input type="button" value="批量驳回" onclick="audit_confirm_reback_all('1');" class="button-s"/>
  </div>
  <div id="divider"></div>
</div>
<script type="text/javascript">
$("#rating_name").autocomplete("/namedata", {autoFill: true});
$('#rating_name').bind("input.autocomplete", function(){ 
	$(this).trigger('keydown.autocomplete'); 
});
$("#rated_name").autocomplete("/namedata", {autoFill: true});
$('#rated_name').bind("input.autocomplete", function(){ 
	$(this).trigger('keydown.autocomplete'); 
});
// function change(){
//     var value = $("#level").val();
//     var type_sel = $("#type_sel").val();
//     var selected = '';
//     if(value != ''){
//         $.ajax({
//            type: "POST",
//            url: "/ratting/get_types",
//            dataType:"json",
//            data: "key="+value,
//            success: function(msg){
//                 if(!jQuery.isEmptyObject(msg)){
//                     var option = '';
//                     for(x in msg){
//                         if(x == type_sel){
//                             selected = 'selected';
//                         }else{
//                             selected = '';
//                         }
//                         option += "<option value="+x+" "+selected+">"+msg[x]+"</option>";
//                     }
//                     $('#type').html('<option value="">请选择</option>');
//                     $('#content_id').html('<option value="">请选择</option>');
//                     $('#type').append(option);
//                 }else{
//                     $('#type').html('<option value="">请选择</option>');
//                     $('#content_id').html('<option value="">请选择</option>');
//                 }
//            }
//         });
//     }else{
//         $('#type').html('<option value="">请选择</option>');
//         $('#content_id').html('<option value="">请选择</option>');
//     }
// }
// function changetype(log){
//     var value = $("#type").val();
//     if(value == '' && log != 1){
//         value = $("#type_sel").val();
//     }
//     //alert(value);
//     var content_id = $("#id_sel").val();
//     var selected = '';
//     if(value != ''){
//         $.ajax({
//            type: "POST",
//            url: "/ratting/get_types",
//            dataType:"json",
//            data: "type="+value,
//            success: function(msg){
//                 if(!jQuery.isEmptyObject(msg)){
//                     var option = '';
//                     for(x in msg){
//                         if(msg[x].id == content_id){
//                             selected = 'selected';
//                         }else{
//                             selected = '';
//                         }
//                         option += "<option value="+msg[x].id+" "+selected+">"+msg[x].content+"</option>";
//                     }
//                     $('#content_id').html('<option value="">请选择</option>');
//                     $('#content_id').append(option);
//                 }else{
//                     $('#content_id').html('<option value="">请选择</option>');
//                 }
//            }
//         });
//     }else{
//         $('#content_id').html('<option value="">请选择</option>');
//     }
// }
function audit_confirm(obj){
	var id = obj.name;
	if(id != ''){
		$.ajax({
			type: "POST",
			url: "/ratting/audit_confirm_reback",
			dataType:"json",
			data: "id="+id+"&log=0",
			success:function(msg){
				if(!jQuery.isEmptyObject(msg)){
                    if(msg.return == 1){
                    	$(obj).parent().html('');
                    	$("#checkbox"+id).html('');
                    	$("#status"+id).html('已确认');
                    	$("#audited_by"+id).html(msg.info.audited_by);
                    	$("#audit_time"+id).html(msg.info.audit_time);
                    }else{
                    	alert(msg.info);
                    }
                }else{
                    alert('操作失败!');
                }
			}
		});
	}
	
}
function audit_reback(obj){
	var id = obj.name;
	var reason = prompt('请输入驳回原因：');
	if(reason == null){
			return false;
		}
	if(id != ''){
		$.ajax({
			type: "POST",
			url: "/ratting/audit_confirm_reback",
			dataType:"json",
			data: "id="+id+"&reason="+reason+"&log=1",
			success:function(msg){
				if(!jQuery.isEmptyObject(msg)){
                    if(msg.return == 1){
                    	$(obj).parent().html('');
                    	$("#checkbox"+id).html('');
                    	if(msg.info.remark != 'null' && msg.info.remark != ''){
                    		var str = '<span style="cursor:pointer;" onmouseover="over_f(\'驳回原因：'+msg.info.remark+'\',this);"onmouseout="out_f();"><?php echo "(驳回原因：'+msg.info.remark+')";?></span>'
                    		$("#status"+id).html('已驳回' + str);
                    	}else{
                    		$("#status"+id).html('已驳回');
                    	}
                    	$("#audited_by"+id).html(msg.info.audited_by);
                    	$("#audit_time"+id).html(msg.info.audit_time);
                    }else{
                    	alert(msg.info);
                    }
                }else{
                    alert('操作失败!');
                }
			}
		});
	}
}

function sel_all(obj){
	var sel = $("input[name='id[]']");
	if(obj.checked){
		sel.attr('checked',true);
	}else{
		sel.attr('checked',false);
	}
}

function sel_same(obj){
	var sel = $("input[name='id[]']");
	var id = $(obj).val();
	var get_grade_uid = $("#rated_uid"+id).val();
	var rat_grade_uid = $("#rating_uid"+id).val();

	var same_id = '';
	var same_get_grade_uid = '';
	var same_rat_grade_uid = '';

	sel.each(function(){
		same_id = $(this).val();
		same_get_grade_uid = $("#rated_uid"+same_id).val();
		same_rat_grade_uid = $("#rating_uid"+same_id).val();
		if(same_get_grade_uid == get_grade_uid && same_rat_grade_uid == rat_grade_uid){
			if(obj.checked){
				$(this).attr('checked',true);
			}
			//else{
			// 	$(this).attr('checked',false);
			// }
		}
	});
}
function audit_confirm_reback_all(log){
	var sel = $("input[name='id[]']:checked");
	
	var arr = new Array();
	$.each(sel,function(i,obj){
		arr[i] = obj.value;
	});
	var arr_str = arr.join(",");
	if(arr_str == ''){
		alert('请选择记录');
		return false;
	}
	if(log == '1'){
		var reason = prompt('请输入驳回原因：');
		if(reason == null){
			return false;
		}
		var data_send = "id_str="+arr_str+"&reason="+reason;
	}else{
		var data_send = "id_str="+arr_str;
	}
	$.ajax({
		type: "POST",
		url: "/ratting/audit_confirm_reback_all",
		data: data_send+"&log="+log,
		success:function(msg){
			alert(msg);
			location.reload();
		}
	});
}
function over_f(val,obj){
	layer.tips(val, obj, {
		guide: 3,
        style: ['background-color:#0FA6D8; color:#fff', '#0FA6D8'],
        maxWidth:150
    });
}
function out_f(val,obj){
	$(".xubox_layer").remove();
}
</script>