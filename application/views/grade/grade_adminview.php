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
        <div id="titlebar">
            <?php
            if($type == 'story'){
                $datas = $story;
                $id = $story['story_id'];
                $name = $story['story_name'];
            }else if($type == 'task'){
                $datas = $task;
                $id = $task['task_id'];
                $name = $task['task_name'];
            }
            ?>
            <div id="main"><?php echo $datas['project_name']; ?> &gt;&gt; <?php echo strtoupper($type);?> #<?php echo $id;?> <?php echo "<a href='/{$type}/view/{$id}' target='_blank'>{$name}</a>";?></div>
        </div>
        <table class="cont-rt5">
            <tbody>
            <tr valign="top">
                <td>
                    <div id="comment" class="">
                        <?php 
                        $description = '';
                        $attachments = array();
                        $type_name = '';
                        if($type=='story'){
                            $description = $story['story_description'];
                            $attachments = $story['story_attachments'];
                            $type_name = '需求';
                        }else if($type=='task'){
                            $description = $task['task_description'];
                            $attachments = $task['task_attachments'];
                            $type_name = '任务';
                        }
                        ?>
                        <fieldset>
                          <legend><?php echo $type_name;?>描述</legend>
                          <div class="content"><?php echo $description;?></div>
                        </fieldset>
                        <fieldset>
                          <legend>附件</legend>
                          <div> 
                            <?php foreach ($attachments as $val):?>
                            <a href="/download/<?php echo $val->id;?>"><?php echo empty($val->title) ? basename($val->path) : $val->title.$val->extension;?></a> <br/>
                            <?php endforeach;?>
                          </div>
                        </fieldset>
                        <fieldset>
                            <legend>评价</legend>
                                <table align="center" class="table-1">
                                    <tbody>
                                    <?php if(empty($datas['data'])):?>
                                        <tr><td><font color="red">评分项还未设置</font></td></tr>
                                    <?php else:?>
                                    <?php foreach($datas['data'] as $key => $val):?>
                                        <tr>
                                            <th class="rowhead"><strong><?php echo $val->content; ?></strong></th>
                                            <td>
                                                <?php foreach( $val->description_item as $k => $v)
                                                    if($val->score && $val->score->description_id == $v->id)
                                                        echo $v->desc;
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="rowhead"><strong>说明</strong></th>
                                            <td><?php
                                                    echo empty($val->score->description)?'':$val->score->description;
                                                    ?>
                                            </td>
                                        </tr>
                                    <?php endforeach;?>
                                    <?php endif;?>
                                    </tbody>
                                </table>
                        </fieldset>
                        <div class="a-center actionlink ">
                            <a class="" href="/grade/gradeadmin/?body=<?php echo $body;?>"><input type="button" value=" 返回 " class="button-act"></a>
                        </div>
                    </div>
                </td>
                <td class="divider"></td>
            </tr>
            </tbody>
        </table>
    </div>
  <?php
if($include_headfoot){
  echo <<<EOF
<div id="divider"></div>
EOF;
}
?>
</div>