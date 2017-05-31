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
      <div id="main">STORY #<?php echo $story->id;?> <?php echo $story->name;?></div>
    </div>
    <table class="cont-rt5">
      <tbody>
        <tr valign="top">
          <td><fieldset>
              <legend>需求描述</legend>
              <div class="content">
                <?php echo $story->description;?>
              </div>
            </fieldset>
            <fieldset>
              <legend>附件</legend>
              <div> 
                <?php foreach ($story->attachments as $val):?>
                <a href="/download/<?php echo $val->id;?>"><?php echo empty($val->title) ? basename($val->path) : $val->title.$val->extension;?></a> <br/>
                <?php endforeach;?>
              </div>
            </fieldset>
            <div id="actionbox">
              <fieldset>
                <legend> 历史记录 </legend>
                <ol id="historyItem">
                  <?php foreach ($story->actions as $key => $val): ?>
                  <li value="<?php echo ++$key.'.';?>"> <span> <?php echo $val->date;?>, 由 <strong><?php echo $val->actor;?></strong> <?php echo $pmsdata[$val->type]['action'][$val->action]['display']?>。<span onclick="switchChange(this)" class="hand change-show"></span></span>
                    <?php if(!empty($val->history)):?>
                    <div id="changeBox5" class="changes" style="display: none;">
                      <?php foreach ($val->history as $key => $val): ?>
                            <?php if(empty($val->diff)):?>
                            修改了 <strong><i><?php echo $val->field;?></i></strong>，旧值为 "<?php echo $val->old;?>"，新值为 "<?php echo $val->new;?>"。<br>
                          <?php else:?>
                            修改了 <strong><i><?php echo $val->field;?></i></strong>，区别为 <blockquote><?php echo $val->diff;?></blockquote><br>
                          <?php endif;?>
                      <?php endforeach;?>
                    </div>
                    <?php endif;?>
                    <?php if(!empty($val->comment)):?>
                    <div class="comment149" style="display: block;"><?php echo $val->comment;?></div>
                    <?php endif;?>
                  </li>
                  <?php endforeach;?>
                </ol>
              </fieldset>
            </div>
            <div class="a-center actionlink">
              <?php if(has_permission($pmsdata['story']['powers']['assign']['value']) && $story->status == $pmsdata['story']['status']['draft']['value']):?>
              <input type="button" value=" 指派 " onclick="window.location.href='/story/assign/<?php echo $story->id;?>?body=<?php echo $body;?>'" class="button-act">
              <?php endif;?>
              <?php if(has_permission($pmsdata['story']['powers']['change']['value']) && $story->status == $pmsdata['story']['status']['draft']['value']):?>
              <input type="button" value=" 变更 " onclick="window.location.href='/story/change/<?php echo $story->id;?>?body=<?php echo $body;?>'" class="button-act">
              <?php endif;?>
              <?php if(has_permission($pmsdata['story']['powers']['verify']['value']) && $story->status == $pmsdata['story']['status']['draft']['value']):?>
              <input type="button" value=" 评审 " onclick="window.location.href='/story/verify/<?php echo $story->id;?>?body=<?php echo $body;?>'" class="button-act">
              <?php endif;?>
              <?php if(has_permission($pmsdata['task']['powers']['create']['value']) && $story->status == $pmsdata['story']['status']['active']['value']):?>
              <input type="button" value=" 分解任务 " onclick="window.location.href='/task/create/<?php echo $story->id;?>?body=<?php echo $body;?>'" class="button-act">
              <?php endif;?>
              <?php if(has_permission($pmsdata['story']['powers']['close']['value']) && $story->status != $pmsdata['story']['status']['closed']['value']):?>
              <input type="button" value=" 关闭 " onclick="<?php echo $story->can_close?"window.location.href='/story/close/{$story->id}?body={$body}'":"alert('需求下还有任务没有关闭，所有任务都关闭的情况下才能关闭需求！');";?>" class="button-act">
              <?php endif;?>
              <?php if(has_permission($pmsdata['story']['powers']['edit']['value']) && $story->status != $pmsdata['story']['status']['closed']['value'] && $story->task_count == 0):?>
              <input type="button" value=" 编辑 " onclick="window.location.href='/story/edit/<?php echo $story->id;?>?body=<?php echo $body;?>'" class="button-act">
              <?php endif;?>
              <?php if(has_permission($pmsdata['story']['powers']['active']['value']) && $story->status == $pmsdata['story']['status']['closed']['value']):?>
              <input type="button" value=" 激活 " onclick="window.location.href='/story/active/<?php echo $story->id;?>?body=<?php echo $body;?>'" class="button-act">
              <?php endif;?>
              <input type="button" value=" 备注 " onclick="setComment();" class="button-act">
              <?php if(has_permission($pmsdata['story']['powers']['copy']['value'])):?>
              <input type="button" value=" 复制 " onclick="window.location.href='/story/copy/<?php echo $story->id;?>?body=<?php echo $body;?>'" class="button-act">
              <?php endif;?>
              <!-- <input type="button" value=" 返回 " onclick="window.location.href='/story?order=id&sort=desc&reviewedbyme=1&body=<?php echo $body;?>'" class="button-act"> -->
              <input type="button" value=" 返回 " onclick="<?php echo empty($_SERVER['HTTP_REFERER'])?"history.back();":"window.location.href='{$_SERVER['HTTP_REFERER']}'";?>" class="button-act">
            </div>
            <div id="commentBox" style="display: none;">
              <fieldset>
                <legend>备注</legend>
                <form action="/story/save" method="post">
		  <input type="hidden" name="body" value="<?php echo $body;?>" />
                  <input type="hidden" name="story_id" value="<?php echo $story->id;?>" />
                  <input type="hidden" id="project_id" name="project_id" value="<?php echo $story->project_id;?>" />
                  <table align="center" class="table-1">
                  <tbody><tr><td><textarea class="w-p100 editor" rows="5" id="comment" name="comment"></textarea>
                  </td></tr>
                  <tr><td> <input type="submit" class="button-s" value="保存" id="submit"> <input type="button" class="button-s" value="返回" onclick="javascript:history.go(-1);"></td></tr>
                  </tbody></table>
                </form>
              </fieldset>
            </div>
          </td>
          <td class="divider"></td>
          <td class="side"><fieldset>
              <legend>基本信息</legend>
              <table class="table-1">
                <tbody>
                  <tr>
                    <td class="rowhead">所属产品</td>
                    <td><?php echo empty($story->product)?'':'<a href="/product/view/'.$story->product->id.'?body='.$body.'">'.$story->product->name.'</a>';?></a></td>
                  </tr>
                  <tr>
                    <td class="rowhead">所属模块</td>
                    <td><?php echo empty($story->module)?'':'<a href="/module/'.$story->module->id.'?body='.$body.'">'.$story->module->name.'</a>';?></a></td>
                  </tr>
                  <tr>
                    <td class="rowhead">所属项目</td>
                    <td><?php echo empty($story->project)?'':'<a href="/project/view/'.$story->project->id.'?body='.$body.'">'.$story->project->name.'</a>';?></a></td>
                  </tr>
                  <tr>
                    <td class="rowhead">来源</td>
                    <td><?php echo $pmsdata['story']['sources'][$story->source]['display'];?></td>
                  </tr>
                  <tr>
                    <td class="rowhead">当前状态</td>
                    <td><?php echo $pmsdata['story']['status'][$story->status]['display'];?></td>
                  </tr>
                  <tr>
                    <td class="rowhead">所处阶段</td>
                    <td><?php echo $pmsdata['story']['stages'][$story->stage]['display'];?></td>
                  </tr>
                  <tr>
                    <td class="rowhead">优先级</td>
                    <td><?php echo $story->level;?></td>
                  </tr>
                  <tr>
                    <td class="rowhead">预计工时</td>
                    <td><?php echo $story->estimate;?></td>
                  </tr>
                </tbody>
              </table>
            </fieldset>
            <fieldset>
              <legend>需求的一生</legend>
              <table class="table-1">
                <tbody>
                  <tr>
                    <td class="rowhead w-p20">由谁创建</td>
                    <td><?php echo empty($story->opened_by)?'':$story->opened_by->real_name.' 于 '.$story->opened_date;?></td>
                  </tr>
                  <tr>
                    <td class="rowhead">指派给</td>
                    <td><?php echo empty($story->assigned_to)?'':$story->assigned_to->real_name.' 于 '.$story->assigned_date;?></td>
                  </tr>
                  <tr>
                    <td class="rowhead">由谁评审</td>
                    <td> <?php echo empty($story->reviewed_by)?'':$story->reviewed_by->real_name;?> </td>
                  </tr>
                  <tr>
                    <td class="rowhead">评审时间</td>
                    <td><?php echo empty($story->reviewed_by)?'':$story->reviewed_date;?></td>
                  </tr>
                  <tr>
                    <td class="rowhead">由谁关闭</td>
                    <td><?php echo empty($story->closed_by)?'':$story->closed_by->real_name.' 于 '.$story->closed_date;?></td>
                  </tr>
                  <tr>
                    <td class="rowhead">关闭原因</td>
                    <td> <?php echo empty($story->closed_reason)?'':$pmsdata['story']['close_reason'][$story->closed_reason]['display'];?> </td>
                  </tr>
                  <tr>
                    <td class="rowhead">最后修改</td>
                    <td><?php echo empty($story->last_edited_by)?'':$story->last_edited_by->real_name.' 于 '.$story->last_edited_date;?></td>
                  </tr>
                </tbody>
              </table>
            </fieldset>
            <fieldset>
              <legend>项目任务</legend>
              <table class="table-1 fixed">
                <tbody>
                  <?php foreach ($story->tasks as $key => $val):?>
                  <tr>
                    <td><a href="/task/view/<?php echo $val->id;?>"><?php $val->project_name;?></a> <span title="<?php echo $val->name;?>"><a href="/task/view/<?php echo $val->id;?>">#<?php echo $val->id;?> <?php echo $val->name;?></a> <?php echo empty($val->finished_by)?'('.(empty($val->assigned_to)?'':'<a href="/task?order=status&sort=asc&allproject=1&assigned_to='.$val->assigned_to->id.'">'.$val->assigned_to->real_name.'</a>').')':'('.(empty($val->finished_by)?'':'<a href="/task?order=status&sort=asc&allproject=1&assigned_to='.$val->finished_by->id.'">'.$val->finished_by->real_name.'</a>').')';?></span><br></td>
                  </tr>
                  <?php endforeach;?>
                </tbody>
              </table>
            </fieldset>
            </td>
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