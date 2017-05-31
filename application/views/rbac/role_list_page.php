<?php 
$sort = $this->input->get('sort');
$sort = (empty($sort) || $sort=='asc')?'desc':'asc';
?>
<div id="wrap">
  <div class="outer">
    <form id="projectStoryForm" method="post">
      <table class="table-1 fixed colored tablesorter datatable">
        <caption class="caption-tl pb-10px">
        <div class="f-left"><strong>角色列表</strong></div>
        <?php if(has_permission($pmsdata['role']['powers']['create']['value'])):?>
        <div class="f-right"> <span class="link-button"><a class="" target="" href="/role/create">新增角色</a></span></div>
        <?php endif;?>
        </caption>
        <thead>
          <tr class="colhead">
            <th class="w-id"> <div class="header"><a href="?order=id&sort=<?php echo $sort?>">ID</a> </div></th>
            <th class="w-hour"> <div class="header"><a href="?order=name&sort=<?php echo $sort?>">角色名</a> </div></th>
            <th class="">描述</th>
            <th class="w-80px ">操作</th>
          </tr>
        </thead>
        <?php if(has_permission($pmsdata['role']['powers']['page']['value'])):?>
        <tbody>
          <?php foreach($roles as $key => $val):?>
          <tr class="a-center <?php echo ($key%2==0)?'odd':'even';?>">
            <td><input type="checkbox" value="<?php echo $val->id;?>" name="role_id[]"><?php echo $val->id;?></td>
            <td><?php echo $val->name;?></td>
            <td><?php echo empty($val->description)?'':$val->description;?></td>
            <td>
              <?php if(has_permission($pmsdata['role']['powers']['create']['value'])):?>
              <a href="/role/edit/<?php echo $val->id;?>">编辑</a> 
              <?php endif;?>
              <?php if(has_permission($pmsdata['role']['powers']['create']['value'])):?>
              <a href="#" onclick="javascript:if(confirm('确认删除角色 <?php echo $val->name;?> 吗? 该角色下所有的用户也会被删除.')){location.href='/role/delete/<?php echo $val->id;?>'}">删除</a></td>
              <?php endif;?>
          </tr>
          <?php endforeach;?>
        </tbody>
        <?php else:?>
        <tbody><tr><td colspan="10" style="text-align:center;">没有查看列表的权限</td></tr></tbody>
        <?php endif;?>
        <tfoot>
          <tr>
            <td colspan="10"><div class="f-left">
                <input type="button" value="全选" id="allchecker" checkboxname="role_id[]"/>        
                <input type="button" value="反选" id="reversechecker" checkboxname="role_id[]"/>
                本页共 <strong><?php echo count($roles);?></strong> 个角色
            </td>
          </tr>
        </tfoot>
      </table>
    </form>
  </div>
  <div id="divider"></div>
</div>