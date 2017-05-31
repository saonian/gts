<?php 
$status= array('0'=>'待审核','1'=>'通过','2'=>'驳回');
$sort = $this->input->get('sort');
$sort = (empty($sort) || $sort=='asc')?'desc':'asc';
?>
<div id="wrap">
  <div class="outer">
    <form id="projectStoryForm" method="get" action="/overtime/index">
    	<table style="width:100%;" class="table-2">
    		<tr>
    			<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    				<label>申请时间:</label>&nbsp;&nbsp;&nbsp;&nbsp;
    				<input type="text" name="begin" id="begin" class="datetime text-2" value="<?php if(isset($Params['begin'])){echo $Params['begin'];}?>">&nbsp;&nbsp;至&nbsp;&nbsp;
    				<input type="text" name="end" id="end" class="datetime text-2" value="<?php if(isset($Params['end'])){echo $Params['end'];}?>">&nbsp;&nbsp;&nbsp;&nbsp;
    				<select class="text-2" name="audit_status">
    					<option value="">申请状态</option>
    					<option value='0' <?php if(isset($Params['audit_status']) && $Params['audit_status']=='0'){echo "selected='selected'";}?>>待审核</option>
    					<option value='1' <?php if(isset($Params['audit_status']) && $Params['audit_status']=='1'){echo "selected='selected'";}?>>通过</option>
    					<option value='2' <?php if(isset($Params['audit_status']) && $Params['audit_status']=='2'){echo "selected='selected'";}?>>驳回</option>
    				</select>&nbsp;&nbsp;&nbsp;&nbsp;
    				<select class="text-2" name="search_type">
    					<?php if(has_permission($pmsdata['overtime']['powers']['shenhe']['value'])){?><option value="proposer" <?php if(isset($Params['search_type']) && $Params['search_type']=="application"){echo "selected='selected'";}?>>申请人</option><?php }?>
    					<option value="reason" <?php if(isset($Params['search_type']) && $Params['search_type']=="reason"){echo "selected='selected'";}?>>加班理由</option>
    				</select>&nbsp;&nbsp;&nbsp;&nbsp;
    				<input type="text" name="keyword" class="text-2" value="<?php if(isset($Params['keyword'])){echo $Params['keyword'];}?>"/>&nbsp;&nbsp;&nbsp;&nbsp;
    				<input type="submit" class="button-s" value="搜索" style="width:80px;"/>&nbsp;&nbsp;&nbsp;&nbsp;
    				<input type="button" name="audit" class="button-s" value="加班申请" onclick="window.location.href='/overtime/add'"/>
    			</td>
    		</tr>
    	</table>
    </form>
      <table class='table-1 fixed colored tablesorter datatable'>
        <thead>
          	<tr class="colhead">
            	<th class='w-id'> <div class='header'><a href='?order=id&sort=<?php echo $sort;?>' >序号</a> </div></th>
            	<th class='w-user'> <div class='header'><a href='?order=proposer&sort=<?php echo $sort;?>' >申请人</a> </div></th>
            	<th class='w-user'> <div class='header'><a href='?order=create_time&sort=<?php echo $sort;?>' >申请时间</a> </div></th>
              <th class='w-user'> <div class='header'><a href='?order=hour_counts&sort=<?php echo $sort;?>' >加班时间</a> </div></th>
            	<th class='w-user'> <div class='header'><a href='?order=reason&sort=<?php echo $sort;?>' >加班理由</a> </div></th>
            	
            	<th class='w-hour'> <div class='header'><a href='?order=auditor&sort=<?php echo $sort;?>' >审核人</a> </div></th>
	            <th class='w-50px'> <div class='header'><a href='?order=audit_status&sort=<?php echo $sort;?>' style="color: red;">状态</a> </div></th>
	            <th class='w-hour'>操作</th>
          	</tr>
        </thead>
        <tbody>
        	<?php if(isset($datas) && count($datas)>0){?>
        	<?php foreach ($datas as $key=>$data){?>
			<tr class='a-center <?php echo ($key%2==0)?'odd':'even';?>'>
				<td><?php echo $data['id']?></td>
				<td><?php echo $data['proposer']?></td>
				<td><?php echo $data['create_time']?></td>
        <td><font color="red"><?php echo $data['hour_counts']?>小时</font></td>
				<td><?php echo $data['reason']?></td>
				<td><?php echo $data['auditor'];?></td>
				<td <?php if($data['audit_status']=='1'){?> style="color:blue;"<?php }else if($data['audit_status']=='2'){?>style="color:red;"<?php }?>><?php echo $status[$data['audit_status']];?></td>
				<td>
					<?php if($data['audit_status']=='0'){?><?php if(has_permission($pmsdata['overtime']['powers']['shenhe']['value'])){?><input type="button" value="审核" onclick="window.location.href='/overtime/shenhe/?id=<?php echo $data['id'];?>'"/><?php }?><input type="button" value="编辑" onclick="window.location.href='/overtime/add/?id=<?php echo $data['id'];?>'"/><?php if(has_permission($pmsdata['overtime']['powers']['delete']['value'], TRUE)){?><input type="button" value="删除" name="<?php echo $data['id']?>" onclick="return deletea(this)"/><?php }?><?php }?>
					<?php if($data['audit_status']!='0'){?><input type="button" value="查看" onclick="window.location.href='/overtime/view/?id=<?php echo $data['id'];?>'"/><?php }?>
				</td>
			</tr>
			<?php }?>
			<?php }?>
        </tbody>
        <tfoot>
        
          <tr>
            <td colspan='8'>
              <div class='f-left'>
                共 <strong><?php echo $total;?></strong> 个加班，共计 <strong><?php echo $total_hours;?></strong> 个小时，其中审核通过 <strong><?php echo $pass_hours;?></strong> 个小时，驳回 <strong><?php echo $reject_hours;?></strong> 个小时，待审核 <strong><?php echo $unpass_hours;?></strong> 个小时。
              </div>
              <?php echo $page_html;?>
            </td>
          </tr>
        </tfoot>
     </table> 	
    
  </div>
  <div id="divider"></div>
</div>
<script type="text/javascript">
function deletea(obj){
	var sid = obj.name;
	if(sid!=''){
		if(confirm('你确定要删除该项加班记录？')){
			$.ajax({
				type:"POST",
				data:{'id':sid},
				url:'/overtime/delete',
				success:function(result){
					alert(result);
					if(result='删除成功'){
						$(obj).parent().parent().remove();
					}
				}
				
			});		
		}else{
			return false;
		}
	}
	
}
</script>