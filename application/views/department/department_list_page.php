<?php 
$sort = $this->input->get('sort');
$sort = (empty($sort) || $sort=='asc')?'desc':'asc';
?>
<div id="wrap">
  <div class="outer">
		<form id="projectStoryForm" method="post">
		<table class="table-1 fixed colored tablesorter datatable">
        <caption class="caption-tl pb-10px">
        <div class="f-left">
			<select id="search_date" name="search_date" class="text-2">
				<option value="created_date" <?php if(isset($params['search_date']) && $params['search_date']=='created_date'){echo "selected";}?>>创建时间</option>
				<option value="last_edited_date" <?php if(isset($params['search_date']) && $params['search_date']=='last_edited_date'){echo "selected";}?>>修改时间</option>
			</select>&nbsp;&nbsp;
			<input id="start" name="start" type="text" class="datetime text-3" value="<?php if(isset($params['start'])){echo $params['start'];}?>">
			<input id="end" name="end" type="text" class="datetime text-3" value="<?php if(isset($params['end'])){echo $params['end'];}?>">&nbsp;&nbsp;
			<select id="is_enable" name="is_enable" class="text-2">
				<option value="1" <?php if(isset($params['is_enable']) && $params['is_enable']=='1'){echo "selected";}?>>启用</option>
				<option value="0" <?php if(isset($params['is_enable']) && $params['is_enable']=='0'){echo "selected";}?>>禁用</option>
			</select>&nbsp;&nbsp;
			<select id="search_type" name="search_type" class="text-2">
				<option value="name" <?php if(isset($params['search_type']) && $params['search_type']=='name'){echo "selected";}?>>部门名称</option>
				<option value="created_by" <?php if(isset($params['search_type']) && $params['search_type']=='created_by'){echo "selected";}?>>创建人</option>
				<option value="last_edited_by" <?php if(isset($params['search_type']) && $params['search_type']=='last_edited_by'){echo "selected";}?>>修改人</option>
			</select>&nbsp;&nbsp;
			<input id="keyword" name="keyword" class="text-2" type="text" value="<?php if(isset($params['keyword'])){echo $params['keyword'];}?>">&nbsp;&nbsp;
			<input type="submit" value="搜索" class="button-s" style="width:60px;cursor:pointer;">&nbsp;&nbsp;
		</div>
        <div class="f-right"> <span class="link-button"><a class="" target="" href="/department/department_add">新建部门</a></span></div>
        </caption>
		</table>
		</form>

		<table class="table-1 fixed colored tablesorter datatable">
        <thead>
          <tr class="colhead" style="height:30px;">
            <th> <div class="header"><a href="?order=id&sort=<?php echo $sort?>">ID</a> </div></th>
            <th> <div class="header"><a href="?order=name&sort=<?php echo $sort?>">部门名称</a> </div></th>
            <th> <div class="header"><a href="?order=is_enable&sort=<?php echo $sort?>">状态</a> </div></th>
            <th> <div class="header"><a href="?order=created_date&sort=<?php echo $sort?>">创建时间</a> </div></th>
            <th> <div class="header"><a href="?order=created_by&sort=<?php echo $sort?>">创建人</a> </div></th>
            <th> <div class="header"><a href="?order=last_edited_date&sort=<?php echo $sort?>">修改时间</a> </div></th>
            <th> <div class="header"><a href="?order=last_edited_by&sort=<?php echo $sort?>">修改人</a> </div></th>
            <th>操作</th>
          </tr>
        </thead>
        <tbody>
         
		 <?php if(count($department_list['data'])>0){?>
		 <?php foreach($department_list['data'] as $key => $val){?>
          <tr class="a-center odd" style="height:30px;">
            <td><?php echo $val['id'];?></td>
            <td><?php echo $val['name'];?></td>
            <td><?php echo $is_enable[$val['is_enable']];?></td>
            <td><?php echo $val['created_date'];?></td>
            <td><?php echo $val['created_by_user'];?></td>
            <td><?php echo $val['last_edited_date'];?></td>
            <td><?php echo $val['last_edited_by_user'];?></td> 
            <td>
				<a href="/department/view?id=<?php echo $val['id'];?>">查看</a>&nbsp;&nbsp;
				<a href="/department/edit?id=<?php echo $val['id'];?>">编辑</a>&nbsp;&nbsp;
				<a href="#" onclick="javascript:if(confirm('确定删除该部门及其所有子部门？')){location.href='/department/del/<?php echo $val['id'];?>'}">删除</a>&nbsp;&nbsp;
			</td>
          </tr>
		<?php }?>
		<?php }else{ ?>
		 <tr class="a-center odd">
            <td colspan="8">暂时无记录</td>
          </tr>
		<?php }?>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="10">
				<div class="f-left">
					总共 <strong><?php echo $department_list['total'];?></strong> 个记录 &nbsp;
					共 <strong><?php echo $department_list['total_page'];?></strong> 页 &nbsp;
				</div>
				<div class="f-left"><?php echo $department_list['page_html'];?></div>
			</td>
          </tr>
        </tfoot>
      </table>
  </div>
  <div id="divider"></div>
</div>