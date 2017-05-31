<?php 
$sort = $this->input->get('sort');
$sort = (empty($sort) || $sort=='asc')?'desc':'asc';
?>
<script type="text/javascript" src="/public/js/grade_setting.js"></script>
<?php 
$sort = $this->input->get('sort');
$sort = (empty($sort) || $sort=='asc')?'desc':'asc';
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
        <form id="projectStoryForm" method="post">
            <table class="table-1 colored tablesorter datatable">
                <caption class="caption-tl pb-10px">
                    <div class="f-left"><strong>需求评分列表</strong></div>
                </caption>
                <thead>
                <tr class="colhead">
                    <th class="w-id"> <div class="header"><a href="?order=id&sort=<?php echo $sort?>">ID</a> </div></th>
                    <th class="">需求名称</th>
                    <th class="">评分人</th>
                    <th class="w-hour">状态</th>
                    <th class="w-80px"> 操作</th>
                </tr>
                </thead>
                <?php if(has_permission($pmsdata['grade']['powers']['gradestorylist']['value'])):?>
                <tbody>
                <?php foreach($data as $key => $val):?>
                    <tr class="a-center odd">
                        <td><?php echo $val->id;?></td>
                        <td title="<?php echo $val->story_name;?>" class="a-left"><a href="/grade/storyview/<?php echo $val->id;?>?body=<?php echo $body;?>"><?php echo $val->story_name;?></a></td>
                        <td><?php echo empty($val->grade_by)?'':$val->grade_by->real_name;?></td>
                        <td><?php echo $val->is_graded;?></td>
                        <?php if(has_permission($pmsdata['grade']['powers']['storyview']['value'])):?>
                        <td><a href="/grade/storyview/<?php echo $val->id;?>?body=<?php echo $body;?>"><?php echo $val->is_graded == '未评分'?'评分':'查看';?></a></td>
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
                            共 <strong><?php echo $total;?></strong> 个评分。</div>
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