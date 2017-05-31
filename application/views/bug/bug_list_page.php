<?php 
$order = $this->input->get('order');
$order = $order ? $order : 'status';
$sort = $this->input->get('sort');
$sort = $sort ? $sort : 'asc';
setcookie('bug_order', $order, time() + 30*24*60*60, '/');
setcookie('bug_sort', $sort, time() + 30*24*60*60, '/');
$defualt_order = "&order={$order}&sort={$sort}";
$sort = (empty($sort) || $sort=='asc')?'desc':'asc';
if(isset($_GET['order'])) unset($_GET['order']);
if(isset($_GET['sort'])) unset($_GET['sort']);
?>
<div id="wrap">
  <div class="outer">
    <div id="featurebar">
      <div class="f-left">
      <span id="allTab" <?php echo empty($_GET['assignedtome']) && empty($_GET['openedbyme'])?'class="active"':'';?>><a target="" href="/bug<?php echo '?'.ltrim($defualt_order, '&');?>">所有</a></span>
      <span id="assignedtomeTab" <?php echo $this->input->get('assignedtome')?'class="active"':'';?>><a target="" href="/bug?assignedtome=1<?php echo $defualt_order;?>">指派给我</a></span>
      <span id="assignedtomeTab" <?php echo $this->input->get('openedbyme')?'class="active"':'';?>><a target="" href="/bug?openedbyme=1<?php echo $defualt_order;?>">由我创建</a></span>
      <span id="statusTab">
        <select onchange="switchStatus('status',this.value)" id="status" name="status" class="text-2">
          <option value="">所有状态</option>
          <?php foreach ($pmsdata['bug']['status'] as $key => $val):?>
          <option value="<?php echo $val['value'];?>" <?php echo $this->input->get('status')==$val['value']?'selected':'';?>><?php echo $val['display'];?></option>
          <?php endforeach;?>
        </select>
      </span>
      </div>
    </div>
      <table class="table-1 fixed colored tablesorter datatable">
        <caption class="caption-tl pb-10px">
        <div class="f-left"><strong>BUG列表</strong></div>
        <div class="f-right"> <span class="link-button"><a  href="/bug/create">提BUG</a></span></div>
        </caption>
        <thead>
          <tr class="colhead">
            <th class="w-id"> <div class="header"><a href="?order=id&sort=<?php echo $sort.'&'.http_build_query($_GET);?>">ID</a> </div></th>
            <th class=""> <div class="header"><a href="?order=title&sort=<?php echo $sort.'&'.http_build_query($_GET);?>">BUG标题</a> </div></th>
            <th class="w-user"> <div class="header"><a href="?order=opened_by&sort=<?php echo $sort.'&'.http_build_query($_GET);?>">由谁创建</a> </div></th>
            <th class="w-hour"> <div class="header"><a href="?order=assigned_to&sort=<?php echo $sort.'&'.http_build_query($_GET);?>">指派给</a> </div></th>
            <th class="w-hour"> <div class="header"><a href="?order=resolved_by&sort=<?php echo $sort.'&'.http_build_query($_GET);?>">解决者</a> </div></th>
            <th class="w-hour"> <div class="header"><a href="?order=status&sort=<?php echo $sort.'&'.http_build_query($_GET);?>">BUG状态</a> </div></th>
            <th style="width:100px">操作</th>
          </tr>
        </thead>
        <tbody>
        	<?php if(isset($datas) && count($datas)>0){?>
        	<?php foreach ($datas as $key=>$data){?>
        	<?php if(is_array($data)){?>
        	<tr class="a-center <?php echo ($key%2==0)?'odd':'even';?>">
        		<td><?php echo $data['id']?></td>
        		<td><a href='/bug/detail?id=<?php echo $data['id']?>'><?php echo $data['title']?></a></td>
        		<td><?php echo empty($data['opened_by'])?'':$data['opened_by']->real_name?></td>
        		<td><?php echo empty($data['assigned_to'])?'':$data['assigned_to']->real_name?></td>
        		<td><?php if($data['resolved_by']){ echo $data['resolved_by']->real_name;}?></td>
        		<td <?php if($data['status']=='active'){?>style="color: green"<?php }else if($data['status']=='closed'){?>style="color:red;"<?php }else{?>style="color:blue;"<?php }?>><?php echo $pmsdata['bug']['status'][$data['status']]['display'];?></td>
        		<td>
              <?php if(has_permission($pmsdata['bug']['powers']['edit']['value']) && $pmsdata['bug']['status']['active']['value']==$data['status']){?>
              <a href="/bug/create?id=<?php echo $data['id']?>">编辑</a>&nbsp;&nbsp;
              <?php }?>
              <a href='/bug/assign?id=<?php echo $data['id']?>'>重新指派</a>&nbsp;&nbsp;
              <?php if(has_permission($pmsdata['bug']['powers']['edit']['value']) && $data['status']!='resolved' && $data['status']!='closed'){?>
              <a href="/bug/resolve?id=<?php echo $data['id']?>">解决</a><?php }?>&nbsp;&nbsp;
              <?php if(has_permission($pmsdata['bug']['powers']['close']['value']) && $data['status']!='closed'){?> 
              <a href="/bug/close?id=<?php echo $data['id']?>">关闭</a>&nbsp;&nbsp;<?php }?>
            </td>
        		
        	</tr>
        	<?php }?>
        	<?php }?>
        	<?php }?>     
       	</tbody>
        <tfoot>
        <tr>
            <td colspan="7"><?php echo $datas['page_html'];?></td> 
         </tr>
        </tfoot>
      </table>
    
  </div>
  <div id="divider"></div>
</div>