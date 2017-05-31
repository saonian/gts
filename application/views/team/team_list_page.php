<div id="wrap">
  <div class="outer">
		<table align="center" class="table-1 colored tablesorter datatable" style="width:600px;">
        <thead>
			<caption>
				<div class="f-right"> <span class="link-button"><a class="" target="" href="/team/team_manage">添加成员</a></span></div>
			</caption>
          <tr class="colhead" style="height:30px;">
            <th> <div class="header"><a href="#">用户</a> </div></th>
            <th> <div class="headerSortDown"><a href="#">角色</a> </div></th>
			<th> <div class="header">操作</div></th>
          </tr>
        </thead>
        <tbody>
		 <?php if(count($team_data['list'])>0){?>
		 <?php foreach($team_data['list'] as $key => $val){?>
          <tr class="a-center odd" style="height:30px;">
            <td><?php echo $val['real_name'];?></td>
            <td><?php echo $val['role'];?></td>
			<th> <a href="#" onclick="javascript:if(confirm('确认删除该成员？')){location.href='/team/team_del?project_id=<?php echo $val['project_id'];?>&user_id=<?php echo $val['user_id'];?>'}">删除</a></th>
          </tr>
		<?php }?>
		<?php }else{ ?>
		 <tr class="a-center odd">
            <td colspan="3">暂时无记录</td>
          </tr>
		<?php }?>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="10">
				<div class="f-left">总共 <strong><?php echo $team_data['total'];?></strong> 个记录</div>
			</td>
          </tr>
        </tfoot>
      </table>
  </div>
  <div id="divider"></div>
</div>