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
      <div id="main">TASK #<?php echo $task->id;?> <?php echo $task->name;?></div>
    </div>
    <table class="cont-rt5">
      <tbody>
        <tr valign="top">
          <td><fieldset>
              <legend>任务描述</legend>
              <div class="content">
                <?php echo $task->description;?>
              </div>
            </fieldset>
            <fieldset>
              <legend>附件</legend>
              <div> 
                <?php foreach ($task->attachments as $val):?>
                <a href="/download/<?php echo $val->id;?>"><?php echo empty($val->title) ? basename($val->path) : $val->title.$val->extension;?></a> <br/>
                <?php endforeach;?>
              </div>
            </fieldset>
            <div id="actionbox">
              <fieldset>
                <legend> 历史记录 </legend>
                <ol id="historyItem">
                  <?php foreach ($task->actions as $key => $val): ?>
                  <li value="<?php echo ++$key.'.';?>"> <span> <?php echo $val->date;?>, 由 <strong><?php echo $val->actor;?></strong> <?php echo $pmsdata[$val->type]['action'][$val->action]['display']?>。<span onclick="switchChange(this)" class="hand change-show"></span></span>
                    <?php if(!empty($val->history)):?>
                    <div id="changeBox" class="changes" style="display: none;">
                      <?php foreach ($val->history as $key => $h): ?>
                            <?php if(empty($h->diff)):?>
                            修改了 <strong><i><?php echo $h->field;?></i></strong>，旧值为 "<?php echo $h->old;?>"，新值为 "<?php echo $h->new;?>"。<br>
                          <?php else:?>
                            修改了 <strong><i><?php echo $h->field;?></i></strong>，区别为 <blockquote><?php echo $h->diff;?></blockquote><br>
                          <?php endif;?>
                      <?php endforeach;?>
                    </div>
                    <?php endif;?>
                    <?php if(!empty($val->comment)):?>
                    <div class="comment149" style="display: block;"><?php echo $val->comment;?></div>
                    <?php endif;?>
                    <?php if(!empty($val->attachments)):?>
                    <?php foreach($val->attachments as $atta):?>
                      <div>附件：<a href="/download/<?php echo $atta->id;?>"><?php echo empty($atta->title) ? basename($atta->path) : $atta->title.$atta->extension;?></a> <br/></div>
                    <?php endforeach;?>
                    <?php endif;?>
                  </li>
                  <?php endforeach;?>
                </ol>
              </fieldset>
            </div>
            <div class="a-center actionlink">
              <?php if(has_permission($pmsdata['task']['powers']['edit']['value']) && ($task->status == $pmsdata['task']['status']['wait']['value'] || $task->status == $pmsdata['task']['status']['doing']['value'] || $task->status == $pmsdata['task']['status']['verifytest']['value'])):?>
              <input type="button" value=" 编辑 " onclick="window.location.href='/task/edit/<?php echo $task->id;?>?body=<?php echo $body;?>'" class="button-act">
              <?php endif;?>
              <?php if(has_permission($pmsdata['task']['powers']['start']['value']) && $task->status == $pmsdata['task']['status']['wait']['value']):?>
              <input type="button" value=" 开始 " onclick="window.location.href='/task/start/<?php echo $task->id;?>?body=<?php echo $body;?>'" class="button-act">
              <?php endif;?>

              <?php if(has_permission($pmsdata['task']['powers']['submittest']['value']) && $task->status == $pmsdata['task']['status']['doing']['value']):?>
              <input type="button" value=" 提交测试 " onclick="window.location.href='/task/submittest/<?php echo $task->id;?>?body=<?php echo $body;?>'" class="button-act">
              <?php endif;?>
              <?php if(has_permission($pmsdata['task']['powers']['verifyok']['value']) && $task->status == $pmsdata['task']['status']['verifytest']['value']):?>
              <input type="button" value=" 审核通过 " onclick="window.location.href='/task/verifyok/<?php echo $task->id;?>?body=<?php echo $body;?>'" class="button-act">
              <?php endif;?>
              <?php if($task->need_test == 1):?>
              <?php if(has_permission($pmsdata['task']['powers']['starttest']['value']) && $task->status == $pmsdata['task']['status']['waittest']['value']):?>
              <input type="button" value=" 开始测试 " onclick="window.location.href='/task/starttest/<?php echo $task->id;?>?body=<?php echo $body;?>'" class="button-act">
              <?php endif;?>
              <?php if(has_permission($pmsdata['task']['powers']['finishtest']['value']) && $task->status == $pmsdata['task']['status']['testing']['value']):?>
              <input type="button" value=" 测试完成 " onclick="window.location.href='/task/finishtest/<?php echo $task->id;?>?body=<?php echo $body;?>'" class="button-act">
              <?php endif;?>
              <?php endif;?>

              <?php if(has_permission($pmsdata['task']['powers']['online']['value']) && $task->status == $pmsdata['task']['status']['comptest']['value']):?>

              <input type="button" value=" 上线 " onclick="window.location.href='/task/online/<?php echo $task->id;?>?body=<?php echo $body;?>'" class="button-act">
              <?php endif;?>
              <?php if(has_permission($pmsdata['task']['powers']['close']['value']) && ($task->status == $pmsdata['task']['status']['online']['value'] || $task->status == $pmsdata['task']['status']['canceled']['value'])):?>
              <input type="button" value=" 关闭 " onclick="window.location.href='/task/close/<?php echo $task->id;?>?body=<?php echo $body;?>'" class="button-act">
              <?php endif;?>
              <?php if(has_permission($pmsdata['task']['powers']['active']['value']) && $task->status == $pmsdata['task']['status']['closed']['value']):?>
              <input type="button" value=" 激活 " onclick="window.location.href='/task/active/<?php echo $task->id;?>?body=<?php echo $body;?>'" class="button-act">
              <?php endif;?>
              <?php if(has_permission($pmsdata['task']['powers']['cancel']['value']) && $task->status != $pmsdata['task']['status']['closed']['value'] && $task->status != $pmsdata['task']['status']['canceled']['value']):?>
              <input type="button" value=" 取消 " onclick="window.location.href='/task/cancel/<?php echo $task->id;?>?body=<?php echo $body;?>'" class="button-act">
              <?php endif;?>
              <input type="button" value=" 备注 " onclick="setComment();" class="button-act">
              <input type="button" value=" 返回 " onclick="window.location.href='/task?assignedtome=1&body=<?php echo $body;?>'" class="button-act">
            </div>
            <div id="commentBox" style="display: none;">
              <fieldset>
                <legend>备注</legend>
                <form action="/task/save" method="post" enctype="multipart/form-data">
		  <input name="body" value="<?php echo $body;?>" type="hidden"/>
                  <input type="hidden" name="task_id" value="<?php echo $task->id;?>" />
                  <input type="hidden" id="project_id" name="project_id" value="<?php echo $task->project_id;?>" />
                  <table align="center" class="table-1">
                  <tbody><tr><td><textarea class="w-p100 editor" rows="5" id="comment" name="comment"></textarea>
                  </td></tr>
                  <tr><td>
                    <div id="fileform">
                        <div id="fileBox1" class="fileBox">
                        <input type="file" tabindex="-1" class="fileControl" name="files[]">
                        <label class="fileLabel" tabindex="-1">标题：</label>
                        <input type="text" tabindex="-1" class="text-3" name="labels[]"> 
                        <input type="button" onclick="addFile(this)" value="增加"/>
                        <input type="button" onclick="delFile(this)" value="删除"/>
                      </div>  <div id="fileBox2" class="fileBox">
                        <input type="file" tabindex="-1" class="fileControl" name="files[]">
                        <label class="fileLabel" tabindex="-1">标题：</label>
                        <input type="text" tabindex="-1" class="text-3" name="labels[]"> 
                        <input type="button" onclick="addFile(this)" value="增加"/>
                        <input type="button" onclick="delFile(this)" value="删除"/>
                      </div></div>
                  </td></tr>
                  <tr><td> <input type="submit" class="button-s" value="保存" id="submit"> <input type="button" class="button-s" value="返回" onclick="window.location.href='/task?assignedtome=1&body=<?php echo $body;?>'"></td></tr>
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
                    <td class="rowhead">所属项目</td>
                    <td><?php echo empty($task->project)?'':'<a href="/project/view/'.$task->project->id.'">'.$task->project->name.'</a>';?></td>
                  </tr>
                  <tr>
                    <td class="rowhead">相关需求</td>
                    <td><?php echo empty($task->story)?'':'<a href="/story/view/'.$task->story->id.'">'.$task->story->name.'</a>';?></td>
                  </tr>
                  <tr>
                    <td class="rowhead">需求创建人</td>
                    <td><?php echo empty($task->story->opener)?'':$task->story->opener->real_name?></td>
                  </tr>
                  <tr>
                  <tr>
                    <td class="rowhead">指派给</td>
                    <td><?php echo empty($task->assigned_to)?'':'<a href="/task?order=status&sort=asc&allproject=1&assigned_to='.$task->assigned_to->id.'&body="'.$this->input->get('body').'">'.$task->assigned_to->real_name.'</a>';?></td>
                  </tr>
                  <tr>
                    <td class="rowhead">是否测试</td>
                    <td><?php echo empty($task->need_test)?'无需测试':'需要测试';?></td>
                  </tr>
                  <tr>
                    <td class="rowhead">测试人</td>
                    <td><?php echo empty($task->test_by)?'':$task->test_by->real_name;?></td>
                  </tr>
                  <tr>
                    <td class="rowhead">是否总结</td>
                    <td><?php echo empty($task->need_summary)?'无需总结':'需要总结';?></td>
                  </tr>
                  <?php if($task->need_summary == 1){?>
                  <tr>
                    <td class="rowhead">总结人</td>
                    <td><?php echo $task->assigned_to->real_name?></td>
                  </tr>
                  <?php }?>
                  <tr>
                  <tr>
                    <td class="rowhead">任务类型</td>
                    <td><?php echo $pmsdata['task']['types'][$task->type]['display'];?></td>
                  </tr>
                  <tr>
                    <td class="rowhead">任务状态</td>
                    <td><?php echo $pmsdata['task']['status'][$task->status]['display'];?></td>
                  </tr>
                  <tr>
                    <td class="rowhead">优先级</td>
                    <td><?php echo $task->level;?></td>
                  </tr>
                  <tr>
                    <td class="rowhead">难易度</td>
                    <td><?php echo $task->difficulty;?></td>
                  </tr>
                </tbody>
              </table>
            </fieldset>
            <fieldset>
              <legend>工时信息</legend>
              <table class="table-1"> 
                <tbody><tr>
                  <th class="rowhead">预计开始</th>
                  <td><?php echo $task->est_started_date;?></td>
                </tr>  
                <tr>
                  <th class="rowhead">实际开始</th>
                  <td><?php echo $task->real_started_date;?></td>
                </tr>  
                <tr>
                  <th class="rowhead">截止日期</th>
                  <td><?php echo $task->deadline;?></td>
                </tr>  
                <tr>
                  <th class="rowhead w-p20">最初预计</th>
                  <td><?php echo $task->estimate;?>工时</td>
                </tr>  
                <tr>
                  <th class="rowhead">总消耗</th>
                  <td><?php echo $task->consumed;?>工时</td>
                </tr>  
                <tr>
                  <th class="rowhead">预计剩余</th>
                  <td><?php echo $task->estimate - $task->consumed;?>工时</td>
                </tr>
                <tr>
                  <th class="rowhead">测试开始</th>
                  <td><?php echo $task->test_date;?></td>
                </tr>
                <tr>
                  <th class="rowhead">测试结束</th>
                  <td><?php echo $task->test_finished_date;?></td>
                </tr>
                <tr>
                  <th class="rowhead">上线日期</th>
                  <td><?php echo $task->online_date;?></td>
                </tr>
              </tbody></table>
            </fieldset>
            <fieldset>
              <legend>任务的一生</legend>
              <table class="table-1">
                <tbody>
                  <tr>
                    <td class="rowhead w-p20">由谁创建</td>
                    <td><?php echo empty($task->opened_by)?'':$task->opened_by->real_name.' 于 '.$task->opened_date;?></td>
                  </tr>
                  <tr>
                    <td class="rowhead">由谁完成</td>
                    <td><?php echo empty($task->finished_by)?'':$task->finished_by->real_name.' 于 '.$task->finished_date;?></td>
                  </tr>
                  <tr>
                    <td class="rowhead">由谁测试</td>
                    <td> <?php echo empty($task->test_by)?'':$task->test_by->real_name;?> </td>
                  </tr>
                  <tr>
                    <td class="rowhead">由谁取消</td>
                    <td> <?php echo empty($task->canceled_by)?'':$task->canceled_by->real_name;?> </td>
                  </tr>
                  <tr>
                    <td class="rowhead">由谁关闭</td>
                    <td><?php echo empty($task->closed_by)?'':$task->closed_by->real_name.' 于 '.$task->closed_date;?></td>
                  </tr>
                  <tr>
                    <td class="rowhead">关闭原因</td>
                    <td> <?php echo empty($task->closed_reason)?'':$pmsdata['story']['close_reason'][$task->closed_reason]['display'];?> </td>
                  </tr>
                  <tr>
                    <td class="rowhead">最后修改</td>
                    <td><?php echo empty($task->last_edited_by)?'':$task->last_edited_by->real_name.' 于 '.$task->last_edited_date;?></td>
                  </tr>
                </tbody>
              </table>
            </fieldset>
            <fieldset>
              <legend>同需求任务</legend>
              <table class="table-1 fixed">
                <tbody>
                  <?php foreach ($task->tasks as $key => $val):?>
                  <tr>
                    <td><a href="/task/view/<?php echo $val->id;?>?body=<?php echo $body;?>">#<?php echo $val->id;?> <?php echo $val->name;?></a> <?php echo empty($val->finished_by)?'('.(empty($val->assigned_to)?'':$val->assigned_to->real_name).')':'('.(empty($val->finished_by)?'':$val->finished_by->real_name).')';?></span><br></td>
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