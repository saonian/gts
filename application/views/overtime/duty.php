<?php 
$status= array('1'=>'离线','2'=>'上线');
$sort = $this->input->get('sort');
$sort = (empty($sort) || $sort=='asc')?'desc':'asc';
?>
<div id="wrap">
  <div class="outer">
    <form id="projectStoryForm" method="get" action="/overtime/duty">
    	<table style="width:100%;" class="table-2">
    		<tr>
    			<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    				<label>值班时间:</label>&nbsp;&nbsp;&nbsp;&nbsp;
    				<input type="text" name="begin" id="begin" class="datetime text-2" value="<?php if(isset($Params['begin'])){echo $Params['begin'];}?>">&nbsp;&nbsp;至&nbsp;&nbsp;
    				<input type="text" name="end" id="end" class="datetime text-2" value="<?php if(isset($Params['end'])){echo $Params['end'];}?>">&nbsp;&nbsp;&nbsp;&nbsp;
    				<select class="text-2" name="duty_status">
    					<option value="">值班状态</option>
    					<option value='1' <?php if(isset($Params['duty_status']) && $Params['duty_status']=='1'){echo "selected='selected'";}?>>离线</option>
    					<option value='2' <?php if(isset($Params['duty_status']) && $Params['duty_status']=='2'){echo "selected='selected'";}?>>上线</option>
    				</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    				<input type="submit" class="button-s" value="搜索" style="width:80px;"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    				<input type="button" name="audit" class="button-s" value="我要值班" onclick="window.location.href='/overtime/selectduty'"/>
    			</td>
    		</tr>
    	</table>
    </form>
      <table class='table-1 fixed colored tablesorter datatable'>
        <thead>
          	<tr class="colhead">
            	<th class='w-id'> <div class='header'><a href='?order=id&sort=<?php echo $sort;?>' >序号</a> </div></th>
            	<th class='w-user'  style="width: 240px;"> <div class='header'><a href='?order=duty_user&sort=<?php echo $sort;?>' >值班人</a> </div></th>
            	<th class='w-user' style="width: 240px;"> <div class='header'><a href='?order=create_time&sort=<?php echo $sort;?>' >开始值班时间</a> </div></th>
              <th class='w-user' style="width: 240px;"> <div class='header'><a href='?order=end_times&sort=<?php echo $sort;?>' >下线时间</a> </div></th>
	            <th class='w-50px' style="width: 240px;"> <div class='header'><a href='?order=duty_status&sort=<?php echo $sort;?>'>值班状态</a> </div></th>
	            <th class='w-hour' style="width: 240px;">操作</th>
          	</tr>
        </thead>
        <tbody>
        	<?php if(isset($datas) && count($datas)>0){?>
        	<?php foreach ($datas as $key=>$data){?>
			<tr class='a-center <?php echo ($key%2==0)?'odd':'even';?>'>
				<td><?php echo $data['id']?></td>
				<td><?php echo $data['duty_user']?></td>
				<td><?php echo $data['create_time']?></td>
        		<td><?php echo $data['end_time']?></td>
				<td <?php if($data['duty_status']=='1'){?> style="color:blue;"<?php }else if($data['duty_status']=='2'){?>style="color:red;"<?php }?>><?php echo $status[$data['duty_status']];?></td>
				<td <?php if($data['duty_status']=='2'){?>>
					<a href="/overtime/force?uid=<?php echo $data['id']?>">强制下线</a><?php }?>
				</td>
			</tr>
			<?php }?>
			<?php }?>
        </tbody>
        <tfoot>
        
          <tr>
            <td colspan='8'>
              <div class='f-left'>
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
function change(obj){
	var uid = obj.name;
	if(uid!=''){
		$.ajax({
			type:"POST",
			data:{'uid':uid},
			url:'/overtime/force',
			success:function(result){
				if(result==1){
					$(obj).html(result);
				}
			}
			
		});		
	}
	
}
</script>