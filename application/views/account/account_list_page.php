<?php 
$sort = $this->input->get('sort');
$sort = (empty($sort) || $sort=='asc')?'desc':'asc';
?>
<div id="wrap">
  <div class="outer">
		<form id="projectStoryForm" method="get">
		<table class="table-1 fixed colored tablesorter datatable" width="100%">
        <caption class="caption-tl pb-10px">
        <div class="f-left">
			<select id="department_id" name="department_id" class="text-2">
				<option value="">所属部门</option>
				<?php foreach($parent_department as $key=>$val){ ?>
					<option value="<?php echo $val['id'];?>" <?php if($params['department_id']==$val['id']){echo "selected";}?>><?php echo $val['name'];?></option>
				<?php } ?>
			</select>&nbsp;&nbsp;

			<select id="search_type" name="search_type" class="text-2">
				<option value="account" <?php if($params['search_type']=='account'){echo "selected";}?>>用户名</option>
				<option value="real_name" <?php if($params['search_type']=='real_name'){echo "selected";}?>>姓名</option>
			</select>&nbsp;&nbsp;
			<input id="keyword" name="keyword" type="text" class="text-3" value="<?php if(isset($params['keyword'])){echo $params['keyword'];}?>">&nbsp;&nbsp;
			<input type="submit" value="搜索" class="button-s" style="width:60px;cursor:pointer;">&nbsp;&nbsp;
		</div>
		<div class="f-right">
			<a href="/account/add?body=" class="">新增用户</a>
		</div>
        </caption>
		</table>
		</form>

		<table class="table-1 fixed colored tablesorter datatable">
        <thead>
          <tr class="colhead" style="height:30px;">
            <th> <div class="header"><a href="?order=id&sort=<?php echo $sort?>">ID</a> </div></th>
            <th> <div class="header"><a href="?order=account&sort=<?php echo $sort?>">用户名</a> </div></th>
            <th> <div class="header"><a href="?order=real_name&sort=<?php echo $sort?>">姓名</a> </div></th>
			<!-- <th> <div class="header"><a href="?order=is_admin&sort=<?php echo $sort?>">用户中心管理员</a> </div></th> -->
			<th> <div class="header"><a href="?order=department_id&sort=<?php echo $sort?>">所属部门</a> </div></th>
			<th>角色</th>
			<th style="width: 200px"> <div class="header"><a href="?order=email&sort=<?php echo $sort?>">邮箱</a> </div></th>
            <th> <div class="header"><a href="?order=join_date&sort=<?php echo $sort?>">创建时间</a> </div></th>
			<th style="width:15%">操作</th>
          </tr>
        </thead>
        <tbody>
         
		 <?php if(count($account['list'])>0){?>
		 <?php foreach($account['list'] as $key => $val){?>
          <tr class="a-center <?php echo ($key%2==0)?'odd':'even';?>" style="height:30px;">
            <td><?php echo $val['id'];?></td>
            <td><?php echo $val['account'];?></td>
            <td><?php echo $val['real_name'];?></td>
            <!-- <td><?php echo $is_admin[$val['is_admin']];?></td> -->
            <td><?php echo $val['department_name'];?></td>
			<th><?php echo $val['role'];?></th>
			<th><?php echo $val['email'];?></th>
            <td><?php echo $val['join_date'];?></td>
			<td>&nbsp;&nbsp;<input type="button" value="设置部门-角色" onclick="location.href='/account/account_set/<?php echo $val['id'];?>'" class="button-r">
				&nbsp;&nbsp;<input type="button" value="修改" onclick="location.href='/account/edit/<?php echo $val['id'];?>'" class="button-r">
			</td>
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
					总共 <strong><?php echo $account['total'];?></strong> 个记录 &nbsp;
					共 <strong><?php echo $account['total_page'];?></strong> 页 &nbsp;
				</div>
				<div class="f-left"><?php echo $account['page_html'];?></div>
			</td>
          </tr>
        </tfoot>
      </table>
  </div>
  <div id="divider"></div>
</div>
<script type="text/javascript">
$("#keyword").autocomplete("/namedata", {autoFill: true});
$('#keyword').bind("input.autocomplete", function(){ 
	$(this).trigger('keydown.autocomplete'); 
});
</script>