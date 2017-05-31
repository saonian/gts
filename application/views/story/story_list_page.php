<?php 
$order = $this->input->get('order');
$order = $order ? $order : (isset($_COOKIE['story_order'])?$_COOKIE['story_order']:'status');
$sort = $this->input->get('sort');
$sort = $sort ? $sort : (isset($_COOKIE['story_sort'])?$_COOKIE['story_sort']:'asc');
setcookie('story_order', $order, time() + 30*24*60*60, '/');
setcookie('story_sort', $sort, time() + 30*24*60*60, '/');
$defualt_order = "&order={$order}&sort={$sort}";
$sort = (empty($sort) || $sort=='asc')?'desc':'asc';
if(isset($_GET['order'])) unset($_GET['order']);
if(isset($_GET['sort'])) unset($_GET['sort']);
?>
<?php
if(!$include_headfoot){
  echo <<<EOF
<link rel='stylesheet' type='text/css' href='/public/css/base_min.css' />
<link rel='stylesheet' type='text/css' href='/public/css/css.css' />
<script src='/public/js/jquery-1.8.3.min.js' type="text/javascript"></script>
<script src="/public/js/layer/layer.min.js"></script>
<script src='/public/js/jquery.cookie.js' type="text/javascript"></script>
<script src="/public/js/syntaxhighlighter/scripts/shCore.js" type="text/javascript" ></script>
<script src="/public/js/syntaxhighlighter/scripts/shAutoloader.js" type="text/javascript" ></script>
<script src='/public/js/common.js' type="text/javascript"></script>
<script src="/public/js/jquery-ui-1.9.2-min.js" type='text/javascript' ></script>
<script src="/public/js/jquery.ui.datepicker-zh-CN.min.js" type='text/javascript' ></script>
<script src="/public/js/jquery-ui-sliderAccess.js" type='text/javascript' ></script>
<script src="/public/js/jquery-ui-timepicker-addon.min.js" type='text/javascript' ></script>
<script src="/public/js/jquery.validate.min.js" type='text/javascript' ></script>
<script src="/public/js/kindeditor/kindeditor-min.js" type='text/javascript' ></script>
<script src="/public/js/kindeditor/zh_CN.js" type='text/javascript' ></script>
<script src="/public/js/autocomplete/jquery.autocomplete.min.js"></script>
<style>
body {background-color:white}
</style>
EOF;
}
?>
<div id="wrap" <?php echo !$include_headfoot ? 'style="padding:0"' : '';?>>
  <div class="outer" <?php echo !$include_headfoot ? 'style="padding:0"' : '';?>>
    <div id="featurebar">
      <div class="f-left">
      <span id="allTab" <?php echo empty($_GET['assignedtome']) && empty($_GET['openedbyme']) && empty($_GET['reviewedbyme'])?'class="active"':'';?>><a target="" href="/story<?php echo '?'.ltrim($defualt_order, '&');?>&body=<?php echo $body;?>&is_product=<?php echo $is_product;?>&allproject=<?php echo $this->input->get('allproject');?>">所有</a></span>
      <span id="assignedtomeTab" <?php echo $this->input->get('assignedtome')?'class="active"':'';?>><a target="" href="/story?assignedtome=1<?php echo $defualt_order;?>&body=<?php echo $body;?>&is_product=<?php echo $is_product;?>&allproject=<?php echo $this->input->get('allproject');?>">指派给我</a></span>
      <span id="assignedtomeTab" <?php echo $this->input->get('openedbyme')?'class="active"':'';?>><a target="" href="/story?openedbyme=1<?php echo $defualt_order;?>&body=<?php echo $body;?>&is_product=<?php echo $is_product;?>&allproject=<?php echo $this->input->get('allproject');?>">由我创建</a></span>
      <span id="assignedtomeTab" <?php echo $this->input->get('reviewedbyme')?'class="active"':'';?>><a target="" href="/story?reviewedbyme=1<?php echo $defualt_order;?>&body=<?php echo $body;?>&is_product=<?php echo $is_product;?>&allproject=<?php echo $this->input->get('allproject');?>">由我审核</a></span>
      <span id="statusTab">
        <select name="allproject" onchange="switchStatus('allproject',this.value)">
          <option value="0" <?php echo !$this->input->get('allproject')?'selected':'';?>>当前项目</option>
          <option value="1" <?php echo $this->input->get('allproject')?'selected':'';?>>所有项目</option>
        </select>
        <select onchange="switchStatus('status',this.value)" id="status" name="status" class="text-2">
          <option value="">所有状态</option>
          <?php foreach ($pmsdata['story']['status'] as $key => $val):?>
          <option value="<?php echo $val['value'];?>" <?php echo $this->input->get('status')==$val['value']?'selected':'';?>><?php echo $val['display'];?></option>
          <?php endforeach;?>
        </select>
        <select onchange="switchStatus('opened_by',this.value)" id="status" name="status" class="text-2">
          <option value="">所有创建人</option>
          <?php foreach ($all_creaters as $key => $val):?>
          <option value="<?php echo $val->id;?>" <?php echo $this->input->get('opened_by')==$val->id?'selected':'';?>><?php echo $val->real_name;?></option>
          <?php endforeach;?>
        </select>
      </span>
      <form action="" style="display:inline">
        <?php foreach($_GET as $k => $v):?>
        <?php if($k == 'keyword' || $k == 'keywordtype'){continue;}?>
        <input type="hidden" name="<?php echo $k;?>" value="<?php echo $v;?>"/>
        <?php endforeach;?>
        <select name="keywordtype" id="keywordtype">
          <option value="name" <?php echo !$this->input->get('keywordtype') || $this->input->get('keywordtype') == 'name'?'selected':'';?>>需求名</option>
          <option value="id" <?php echo $this->input->get('keywordtype') == 'id'?'selected':'';?>>需求ID</option>
        </select>：
        <input type="text" value="<?php echo $this->input->get('keyword')?$this->input->get('keyword'):'';?>" name="keyword" class="text-2"/>&nbsp;&nbsp;
        <input type="submit" value=" 查询 "/>
      </form>
      </div>
    </div>
    <form id="storyListForm" method="post" action="">
      <table class="table-1 fixed colored tablesorter datatable">
        <caption class="caption-tl pb-10px">
        <div class="f-left"><strong>需求列表</strong></div>
        <div class="f-right"> 
          <?php if(has_permission($pmsdata['story']['powers']['create']['value'])):?>
          <span class="link-button"><a class="" href="/story/create?body=<?php echo $body;?>">新增需求</a></span>
          <?php endif;?>
        </div>
        </caption>
        <thead>
          <tr class="colhead">
            <th class="w-id"> <div class="header"><a href="?order=id&sort=<?php echo $sort.'&'.http_build_query($_GET);?>">ID</a> </div></th>
            <th class="w-hour"> <div class="header"><a href="?order=level&sort=<?php echo $sort.'&'.http_build_query($_GET);?>">优先级</a> </div></th>
            <th class=""> <div class="header"><a href="?order=name&sort=<?php echo $sort.'&'.http_build_query($_GET);?>">需求名称</a> </div></th>
            <th class="w-user"> <div class="header"><a href="?order=opened_by&sort=<?php echo $sort.'&'.http_build_query($_GET);?>">创建</a> </div></th>
            <th class=""> <div class="header"><a href="?order=opened_date&sort=<?php echo $sort.'&'.http_build_query($_GET);?>">创建时间</a> </div></th>
            <th class="w-user"> <div class="header"><a href="?order=reviewed_by&sort=<?php echo $sort.'&'.http_build_query($_GET);?>">评审</a> </div></th>
            <th class="w-hour"> <div class="header"><a href="?order=assigned_to&sort=<?php echo $sort.'&'.http_build_query($_GET);?>">指派</a> </div></th>
            <th class="w-hour"> <div class="header"><a href="?order=estimate&sort=<?php echo $sort.'&'.http_build_query($_GET);?>">预计</a> </div></th>
            <th class="w-hour"> <div class="header"><a href="?order=status&sort=<?php echo $sort.'&'.http_build_query($_GET);?>">状态</a> </div></th>
            <th class="w-status"> <div class="header"><a href="?order=stage&sort=<?php echo $sort.'&'.http_build_query($_GET);?>">阶段</a> </div></th>
            <th class="w-50px">任务数</th>
            <th style="width:150px;">操作</th>
          </tr>
        </thead>
        <?php if(has_permission($pmsdata['story']['powers']['page']['value'])):?>
        <tbody>
          <?php foreach($data as $key => $val):?>
          <tr class="a-center <?php echo ($key%2==0)?'odd':'even';?>">
            <td><input type="checkbox" value="<?php echo $val->id;?>" name="story_id[]">
              <a href="/story/view/<?php echo $val->id;?>?body=<?php echo $body;?>"><?php echo $val->id;?></a></td>
            <td><?php echo $val->level;?></td>
            <td title="<?php echo $val->name;?>" class="a-left"><a href="/story/view/<?php echo $val->id;?>?body=<?php echo $body;?>"><?php echo $val->name;?></a></td>
            <td><?php echo empty($val->opened_by)?'':$val->opened_by->real_name;?></td>
            <td><?php echo $val->opened_date;?></td>
            <td><?php echo empty($val->reviewed_by)?'':($_SESSION['userinfo']['id']==$val->reviewed_by->id?'<font color=\'red\'>'.$val->reviewed_by->real_name.'</font>':$val->reviewed_by->real_name);?></td>
            <td><?php echo empty($val->assigned_to)?'':($_SESSION['userinfo']['id']==$val->assigned_to->id?'<font color=\'red\'>'.$val->assigned_to->real_name.'</font>':$val->assigned_to->real_name);?></td>
            <td><?php echo $val->estimate;?></td>
            <td><?php echo empty($val->status)?'':'<font color=\''.$pmsdata['story']['status'][$val->status]['color'].'\'>'.$pmsdata['story']['status'][$val->status]['display'].'</font>';?></td>
            <td><?php echo empty($val->stage)?'':$pmsdata['story']['stages'][$val->stage]['display'];?></td>
            <td><a href="/task?sid=<?php echo $val->id;?>&body=<?php echo $body;?>"><?php echo $val->task_count;?></a></td>
            <td>
              <?php if(has_permission($pmsdata['story']['powers']['view']['value'])):?>
              <a href="/story/view/<?php echo $val->id;?>?body=<?php echo $body;?>">查看</a>
              <?php endif;?>
              <?php if(has_permission($pmsdata['story']['powers']['edit']['value']) && $val->status != $pmsdata['story']['status']['closed']['value'] && $val->task_count==0):?>
              <a href="/story/edit/<?php echo $val->id;?>?body=<?php echo $body;?>">编辑</a> 
              <?php endif;?>
              <?php if(has_permission($pmsdata['story']['powers']['change']['value']) && $val->status == $pmsdata['story']['status']['draft']['value']):?>
              <a href="/story/change/<?php echo $val->id;?>?body=<?php echo $body;?>">变更</a> 
              <?php endif;?>
              <?php if(has_permission($pmsdata['story']['powers']['verify']['value']) && $val->status == $pmsdata['story']['status']['draft']['value']):?>
              <a href="/story/verify/<?php echo $val->id;?>?body=<?php echo $body;?>"><?php echo (!empty($val->reviewed_by) && $_SESSION['userinfo']['id'] == $val->reviewed_by->id)?'<font color="red">评审</font>':'评审';?></a> 
              <?php endif;?>
              <?php if(has_permission($pmsdata['story']['powers']['close']['value']) && $val->status != $pmsdata['story']['status']['closed']['value']):?>
              <a href="<?php echo $val->can_close?"/story/close/{$val->id}?body={$body}":"javascript:alert('需求下还有任务没有关闭，所有任务都关闭的情况下才能关闭需求！');";?>">关闭</a> 
              <?php endif;?>
              <?php if(has_permission($pmsdata['story']['powers']['active']['value']) && $val->status == $pmsdata['story']['status']['closed']['value']):?>
              <a href="/story/active/<?php echo $val->id;?>?body=<?php echo $body;?>">激活</a>
              <?php endif;?>
              <?php if(has_permission($pmsdata['task']['powers']['create']['value']) && $val->status == $pmsdata['story']['status']['active']['value']):?>
              <a href="/task/create/<?php echo $val->id;?>?body=<?php echo $body;?>">分解任务</a> 
              <?php endif;?>
              <?php if(has_permission($pmsdata['task']['powers']['create']['value']) && $val->status == $pmsdata['story']['status']['active']['value']):?>
              <a href="/task/batch_create/<?php echo $val->id;?>?body=<?php echo $body;?>">批量分解任务</a> 
              <?php endif;?>
            </td>
          </tr>
          <?php endforeach;?>
        </tbody>
        <?php else:?>
        <tbody><tr><td colspan="10" style="text-align:center;">没有查看列表的权限</td></tr></tbody>
        <?php endif;?>
        <tfoot>
          <tr>
            <td colspan="11"><div class="f-left">
                <input type="button" class="button-a" value="全选" id="allchecker" checkboxname="story_id[]"/>        
                <input type="button" class="button-a" value="反选" id="reversechecker" checkboxname="story_id[]"/>
                共 <strong><?php echo $total;?></strong> 个需求，预计 <strong><?php echo $total_estimate;?></strong> 个工时。 
                <?php if(has_permission($pmsdata['story']['powers']['edit']['value'])):?>
                <input type="button" class="button-a" onclick="batch_action('/story/batch_edit?body=<?php echo $body;?>');" value="编辑"/> 
                <?php endif;?>
                <?php if(has_permission($pmsdata['story']['powers']['close']['value'])):?>
                <input type="button" class="button-a" onclick="javascript:batch_action('/story/batch_close?body=<?php echo $body;?>');" value="关闭"/>
                <?php endif;?>
              </div>
              <?php echo $page_html;?>
            </td>
          </tr>
        </tfoot>
      </table>
    </form>
  </div>
  <?php
if($include_headfoot){
  echo <<<EOF
<div id="divider"></div>
EOF;
}
?>
</div>