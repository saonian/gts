<?php 
$order = $this->input->get('order');
$order = $order ? $order : (isset($_COOKIE['story_order'])?$_COOKIE['story_order']:'status');
$sort = $this->input->get('sort');
$sort = $sort ? $sort : (isset($_COOKIE['story_sort'])?$_COOKIE['story_sort']:'asc');
setcookie('product_order', $order, time() + 30*24*60*60, '/');
setcookie('product_sort', $sort, time() + 30*24*60*60, '/');
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
    <form id="storyListForm" method="post" action="">
      <table class="table-1 fixed colored tablesorter datatable">
        <caption class="caption-tl pb-10px">
        <div class="f-left"><strong>产品列表</strong></div>
        <div class="f-right"> 
          <?php if(has_permission($pmsdata['product']['powers']['create']['value'])):?>
          <span class="link-button"><a class="" target="" href="/product/create">新增产品</a></span>
          <?php endif;?>
        </div>
        </caption>
        <thead>
          <tr class="colhead">
            <th class="w-id"> <div class="header"><a href="?order=id&sort=<?php echo $sort.'&'.http_build_query($_GET);?>">ID</a> </div></th>
            <th class=""> <div class="header"><a href="?order=name&sort=<?php echo $sort.'&'.http_build_query($_GET);?>">产品名称</a> </div></th>
            <th class="w-hour"> <div class="header"><a href="?order=status&sort=<?php echo $sort.'&'.http_build_query($_GET);?>">状态</a> </div></th>
            <th class="w-hour">激活需求</th>
            <th class="w-hour">草稿需求</th>
            <th class="w-hour">已关闭需求</th>
            <th class="w-hour">相关BUG</th>
            <th class="w-hour">未解决</th>
            <th class="w-hour">未指派</th>
            <th class="">操作</th>
          </tr>
        </thead>
        <?php if(has_permission($pmsdata['product']['powers']['page']['value'])):?>
        <tbody>
          <?php foreach($data as $key => $val):?>
          <tr class="a-center <?php echo ($key%2==0)?'odd':'even';?>">
            <td><input type="checkbox" value="<?php echo $val->id;?>" name="product_id[]">
              <a href="/product/view/<?php echo $val->id;?>"><?php echo $val->id;?></a></td>
            <td title="<?php echo $val->name;?>" class="a-left"><a href="/product/view/<?php echo $val->id;?>"><?php echo $val->name;?></a></td>
            <td><?php echo empty($val->status)?'':'<font color=\''.$pmsdata['product']['status'][$val->status]['color'].'\'>'.$pmsdata['product']['status'][$val->status]['display'].'</font>';?></td>
            <td><?php echo $val->active_story;?></td>
            <td><?php echo $val->draft_story;?></td>
            <td><?php echo $val->closed_story;?></td>
            <td><?php echo $val->relate_bug;?></td>
            <td><?php echo $val->active_bug;?></td>
            <td><?php echo $val->no_assigned_bug;?></td>
            <td>
              <?php if(has_permission($pmsdata['product']['powers']['view']['value'])):?>
              <a href="/product/view/<?php echo $val->id;?>">查看</a>
              <?php endif;?>
              <?php if(has_permission($pmsdata['product']['powers']['edit']['value'])):?>
              <a href="/product/edit/<?php echo $val->id;?>">编辑</a> 
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
            <td colspan="10"><div class="f-left">
                <input type="button" class="button-a" value="全选" id="allchecker" checkboxname="product_id[]"/>        
                <input type="button" class="button-a" value="反选" id="reversechecker" checkboxname="product_id[]"/>
                共 <strong><?php echo $total;?></strong> 个产品
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