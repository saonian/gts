<?php 
$order = $this->input->get('order');
$order = $order ? $order : (isset($_COOKIE['task_order'])?$_COOKIE['task_order']:'status');
$sort = $this->input->get('sort');
$sort = $sort ? $sort : (isset($_COOKIE['task_sort'])?$_COOKIE['task_sort']:'asc');
setcookie('task_order', $order, time() + 30*24*60*60, '/');
setcookie('task_sort', $sort, time() + 30*24*60*60, '/');
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
      <span id="allTab" <?php echo empty($_GET['assignedtome']) && empty($_GET['openedbyme']) && empty($_GET['reviewedbyme']) && empty($_GET['finishedbyme'])?'class="active"':'';?>><a target="" href="/task<?php echo '?'.ltrim($defualt_order, '&');?>&body=<?php echo $this->input->get('body');?>">所有</a></span>
      <span id="assignedtomeTab" <?php echo $this->input->get('assignedtome')?'class="active"':'';?>><a target="" href="/task?assignedtome=1<?php echo $defualt_order;?>&body=<?php echo $this->input->get('body');?>">指派给我</a></span>
      <span id="assignedtomeTab" <?php echo $this->input->get('openedbyme')?'class="active"':'';?>><a target="" href="/task?openedbyme=1<?php echo $defualt_order;?>&body=<?php echo $this->input->get('body');?>">由我创建</a></span>
      <span id="assignedtomeTab" <?php echo $this->input->get('reviewedbyme')?'class="active"':'';?>><a target="" href="/task?reviewedbyme=1<?php echo $defualt_order;?>&body=<?php echo $this->input->get('body');?>">由我审核</a></span>
      <span id="assignedtomeTab" <?php echo $this->input->get('finishedbyme')?'class="active"':'';?>><a target="" href="/task?finishedbyme=1<?php echo $defualt_order;?>&body=<?php echo $this->input->get('body');?>">由我完成</a></span>
      <span id="statusTab">
        <select name="allproject" onchange="switchStatus('allproject',this.value)">
          <option value="0" <?php echo !$this->input->get('allproject')?'selected':'';?>>当前项目</option>
          <option value="1" <?php echo $this->input->get('allproject')?'selected':'';?>>所有项目</option>
        </select>
        <select onchange="switchStatus('status',this.value)" id="status" name="status" class="text-2">
          <option value="">所有状态</option>
          <?php foreach ($pmsdata['task']['status'] as $key => $val):?>
          <option value="<?php echo $val['value'];?>" <?php echo $this->input->get('status')==$val['value']?'selected':'';?>><?php echo $val['display'];?></option>
          <?php endforeach;?>
        </select>
        <select onchange="switchStatus('assigned_to',this.value)" id="status" name="status" class="text-2">
          <option value="">所有指派人</option>
          <?php foreach ($all_user as $key => $val):?>
          <option value="<?php echo $val->id;?>" <?php echo $this->input->get('assigned_to')==$val->id?'selected':'';?>><?php echo $val->real_name;?></option>
          <?php endforeach;?>
        </select>
        <select onchange="switchStatus('type',this.value)" id="status" name="status" class="text-2">
          <option value="">所有任务类型</option>
          <?php foreach ($pmsdata['task']['types'] as $key => $val):?>
          <option value="<?php echo $val['value'];?>" <?php echo $this->input->get('type')==$val['value']?'selected':'';?>><?php echo $val['display'];?></option>
          <?php endforeach;?>
        </select>
        <select onchange="switchStatus('finished_by',this.value)" id="status" name="status" class="text-2">
          <option value="">所有完成者</option>
          <?php foreach ($all_user as $key => $val):?>
          <option value="<?php echo $val->id;?>" <?php echo $this->input->get('finished_by')==$val->id?'selected':'';?>><?php echo $val->real_name;?></option>
          <?php endforeach;?>
        </select>
        <form action="" style="display:inline">
        <input type="hidden" value="<?php echo $this->input->get('body');?>" name="body"/>
          <?php foreach($_GET as $k => $v):?>
          <?php if($k == 'keyword' || $k == 'keywordtype'){continue;}?>
          <input type="hidden" name="<?php echo $k;?>" value="<?php echo $v;?>"/>
          <?php endforeach;?>
          <select name="keywordtype" id="keywordtype">
            <option value="name" <?php echo !$this->input->get('keywordtype') || $this->input->get('keywordtype') == 'name'?'selected':'';?>>任务名</option>
            <option value="id" <?php echo $this->input->get('keywordtype') == 'id'?'selected':'';?>>任务ID</option>
          </select>：
          <input type="text" value="<?php echo $this->input->get('keyword')?$this->input->get('keyword'):'';?>" name="keyword" class="text-2"/>&nbsp;&nbsp;
          <input type="submit" value=" 查询 "/>
        </form>
      </span>
      </div>
    </div>
    <form id="projectStoryForm" method="post">
      <table class="table-1 fixed colored tablesorter datatable">
        <caption class="caption-tl pb-10px">
        <div class="f-left"><strong>任务列表</strong></div>
<!--         <?php if(has_permission($pmsdata['task']['powers']['create']['value'])):?>
        <div class="f-right"> <span class="link-button"><a class="" target="" href="/task/create">新增任务</a></span></div>
        <?php endif;?> -->
        </caption>
        <thead>
          <tr class="colhead">
            <th class="w-id"> <div class="header"><a href="?order=id&sort=<?php echo $sort.'&'.http_build_query($_GET);?>">ID</a> </div></th>
            <th class="w-id"> <div class="header"><a href="?order=level&sort=<?php echo $sort.'&'.http_build_query($_GET);?>">优先级</a> </div></th>
            <th class=""> <div class="header"><a href="?order=name&sort=<?php echo $sort.'&'.http_build_query($_GET);?>">任务名称</a> </div></th>
            <th class="w-status"> <div class="header"><a href="?order=status&sort=<?php echo $sort.'&'.http_build_query($_GET);?>">状态</a> </div></th>
            <th style="width:130px"> <div class="header"><a href="?order=deadline&sort=<?php echo $sort.'&'.http_build_query($_GET);?>">截至</a> </div></th>
            <th class="w-hour"> <div class="header"><a href="?order=assigned_to&sort=<?php echo $sort.'&'.http_build_query($_GET);?>">指派给</a> </div></th>
            <th class="w-hour"> <div class="header"><a href="?order=finished_by&sort=<?php echo $sort.'&'.http_build_query($_GET);?>">完成者</a> </div></th>
            <th class="w-id"> <div class="header"><a href="?order=estimate&sort=<?php echo $sort.'&'.http_build_query($_GET);?>">预计</a> </div></th>
            <th class="w-id"> <div class="header"><a href="?order=consumed&sort=<?php echo $sort.'&'.http_build_query($_GET);?>">消耗</a> </div></th>
            <th class="w-id">剩余</th>
            <th class="">相关需求</th>
            <th style="width:130px">操作</th>
          </tr>
        </thead>
        <?php if(has_permission($pmsdata['task']['powers']['page']['value'])):?>
        <tbody>
          <?php foreach($data as $key => $val):?>
          <tr class="a-center <?php echo ($key%2==0)?'odd':'even';?>">
            <td><input type="checkbox" value="<?php echo $val->id;?>" name="task_id[]">
              <a href="/task/view/<?php echo $val->id;?>?body=<?php echo $this->input->get('body');?>"><?php echo $val->id;?></a></td>
            <td><?php echo $val->level;?></td>
            <td title="<?php echo $val->name;?>" class="a-left"><a href="/task/view/<?php echo $val->id;?>?body=<?php echo $this->input->get('body');?>"><?php echo $val->name;?></a></td>
            <td><font color="<?php echo $pmsdata['task']['status'][$val->status]['color'];?>"><?php echo $pmsdata['task']['status'][$val->status]['display'];?></font></td>
            <td><?php echo $val->deadline;?><?php echo empty($val->deadline)?'':(strtotime($val->deadline)<time() && ($val->status == $pmsdata['task']['status']['doing']['value'] || $val->status == $pmsdata['task']['status']['wait']['value'])?'(<font color="red">已延期</font>)':'');?></td>
            <td><?php echo empty($val->assigned_to)?'':'<a target="_blank" href="/task?order=status&sort=asc&allproject=1&assigned_to='.$val->assigned_to->id.'">'.$val->assigned_to->real_name.'</a>';?></td>
            <td><?php echo empty($val->finished_by)?'':$val->finished_by->real_name;?></td>
            <td><?php echo $val->estimate;?></td>
            <td><?php echo $val->consumed;?></td>
            <td><?php echo $val->estimate - $val->consumed;?></td>
            <td><?php echo empty($val->story)?'':'<a href="/story/view/'.$val->story->id.'?body='.$this->input->get('body').'" title="'.$val->story->name.'">'.$val->story->name.'<a/>';?></td>
            <td>
              <?php if(has_permission($pmsdata['task']['powers']['edit']['value']) && ($val->status == $pmsdata['task']['status']['wait']['value'] || $val->status == $pmsdata['task']['status']['doing']['value'] || $val->status == $pmsdata['task']['status']['verifytest']['value'])):?>
              <a href="/task/edit/<?php echo $val->id;?>?body=<?php echo $this->input->get('body');?>">编辑</a>
              <?php endif;?>
              <?php if(has_permission($pmsdata['task']['powers']['start']['value']) && $val->status == $pmsdata['task']['status']['wait']['value']):?>
              <a href="/task/start/<?php echo $val->id;?>?body=<?php echo $this->input->get('body');?>">开始</a>
              <?php endif;?>

              
              <?php if(has_permission($pmsdata['task']['powers']['submittest']['value']) && $val->status == $pmsdata['task']['status']['doing']['value']):?>
              <a href="/task/submittest/<?php echo $val->id;?>?body=<?php echo $this->input->get('body');?>">提交测试</a> 
              <?php endif;?>
              <?php if(has_permission($pmsdata['task']['powers']['verifyok']['value']) && $val->status == $pmsdata['task']['status']['verifytest']['value']):?>
              <a href="/task/verifyok/<?php echo $val->id;?>?body=<?php echo $this->input->get('body');?>">审核通过</a>
              <?php endif;?>
              <?php if($val->need_test == 1):?>
              <?php if(has_permission($pmsdata['task']['powers']['starttest']['value']) && $val->status == $pmsdata['task']['status']['waittest']['value']):?>
              <a href="/task/starttest/<?php echo $val->id;?>?body=<?php echo $this->input->get('body');?>">开始测试</a>
              <?php endif;?>
              <?php if(has_permission($pmsdata['task']['powers']['finishtest']['value']) && $val->status == $pmsdata['task']['status']['testing']['value']):?>
              <a href="/task/finishtest/<?php echo $val->id;?>?body=<?php echo $this->input->get('body');?>">测试完成</a>
              <?php endif;?>
              <?php endif;?>

              <?php if(has_permission($pmsdata['task']['powers']['online']['value']) && $val->status == $pmsdata['task']['status']['comptest']['value']):?>
              <a href="/task/online/<?php echo $val->id;?>?body=<?php echo $this->input->get('body');?>">上线</a>
              <?php endif;?>
              <?php if(has_permission($pmsdata['task']['powers']['close']['value']) && ($val->status == $pmsdata['task']['status']['online']['value'] || $val->status == $pmsdata['task']['status']['canceled']['value'])):?>
              <a href="/task/close/<?php echo $val->id;?>?body=<?php echo $this->input->get('body');?>">关闭</a> 
              <?php endif;?>
              <?php if(has_permission($pmsdata['task']['powers']['active']['value']) && $val->status == $pmsdata['task']['status']['closed']['value']):?>
              <a href="/task/active/<?php echo $val->id;?>?body=<?php echo $this->input->get('body');?>">激活</a>
              <?php endif;?>
              <?php if(has_permission($pmsdata['task']['powers']['cancel']['value']) && $val->status != $pmsdata['task']['status']['closed']['value'] && $val->status != $pmsdata['task']['status']['canceled']['value']):?>
              <a href="/task/cancel/<?php echo $val->id;?>?body=<?php echo $this->input->get('body');?>">取消</a>
              <?php endif;?>
          </tr>
          <?php endforeach;?>
        </tbody>
        <?php else:?>
        <tbody><tr><td colspan="10" style="text-align:center;">没有查看列表的权限</td></tr></tbody>
        <?php endif;?>
        <tfoot>
          <tr>
            <td colspan="12"><div class="f-left">
                <input type="button" class="button-a" value="全选" id="allchecker" checkboxname="task_id[]"/>        
                <input type="button" class="button-a" value="反选" id="reversechecker" checkboxname="task_id[]"/>
                共 <strong><?php echo $total;?></strong> 个任务，预计 <strong><?php echo $total_estimate;?></strong> 个工时，已消耗 <strong><?php echo $total_consumed;?></strong> 个工时，剩余 <strong><?php echo $total_left;?></strong> 个工时。 
                <?php if(has_permission($pmsdata['task']['powers']['edit']['value'])):?>
                <input type="button" class="button-a" onclick="javascript:batch_action('/task/batch_edit?body=<?php echo $this->input->get('body');?>');" value="编辑"/> 
                <?php endif;?>
                <?php if(has_permission($pmsdata['task']['powers']['close']['value'])):?>
                <input type="button" class="button-a" onclick="javascript:batch_action('/task/batch_close?body=<?php echo $this->input->get('body');?>');" value="关闭"/> 
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