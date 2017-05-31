<div id="wrap">
  <div class="outer">
    <div id="titlebar">
      <div id="main">BUG #<?php echo $bug->id;?> <?php echo $bug->title;?></div>
    </div>
    <table class="cont-rt5">
      <tbody>
        <tr valign="top">
          <td><fieldset>
              <legend>重现步骤</legend>
              <div class="content">
                <?php echo $bug->steps;?>
              </div>
            </fieldset>
            <fieldset>
              <legend>附件</legend>
              <div> 
                <?php foreach ($bug->attachment as $val):?>
                <a href="/download/<?php echo $val->id;?>"><?php echo empty($val->title) ? basename($val->path) : $val->title.$val->extension;?></a> <br/>
                <?php endforeach;?>
              </div>
            </fieldset>
            <div id="actionbox">
              <fieldset>
                <legend> 历史记录 </legend>
                <ol id="historyItem">
                  <?php foreach ($bug->actions as $key => $val): ?>
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
              <?php if(has_permission($pmsdata['bug']['powers']['assign']['value']) && has_permission($pmsdata['bug']['powers']['assign_form']['value'])):?>
              <input type="button" value=" 指派 " onclick="window.location.href='/bug/assign?id=<?php echo $bug->id;?>'" class="button-act">
              <?php endif;?>
              <?php if(has_permission($pmsdata['bug']['powers']['resolve']['value']) && has_permission($pmsdata['bug']['powers']['resolve_form']['value']) && $bug->status != $pmsdata['bug']['status']['resolved']['value'] && $bug->status != $pmsdata['bug']['status']['closed']['value']):?>
              <input type="button" value=" 解决" onclick="window.location.href='/bug/resolve?id=<?php echo $bug->id;?>'" class="button-act">
              <?php endif;?>
              <?php if(has_permission($pmsdata['story']['powers']['create']['value']) && has_permission($pmsdata['bug']['powers']['resolve_form']['value']) && $bug->status != $pmsdata['bug']['status']['resolved']['value'] && $bug->status != $pmsdata['bug']['status']['closed']['value']):?>
              <input type="button" value=" 转需求" onclick="window.location.href='/story/create?src=bug&id=<?php echo $bug->id;?>'" class="button-act">
              <?php endif;?>
              <?php if(has_permission($pmsdata['bug']['powers']['close']['value']) && $bug->status != $pmsdata['bug']['status']['closed']['value']):?>
              <input type="button" value=" 关闭 " onclick="window.location.href='/bug/close?id=<?php echo $bug->id;?>'" class="button-act">
              <?php endif;?>
              <?php if(has_permission($pmsdata['bug']['powers']['edit']['value'])):?>
              <input type="button" value=" 编辑 " onclick="window.location.href='/bug/create?id=<?php echo $bug->id;?>'" class="button-act">
              <?php endif;?>
              <?php if(has_permission($pmsdata['bug']['powers']['active']['value'])):?>
              <!-- <input type="button" value=" 激活 " onclick="window.location.href='#'" class="button-act"> -->
              <?php endif;?>
              <input type="button" value=" 返回 " onclick="<?php echo empty($_SERVER['HTTP_REFERER'])?"history.back();":"window.location.href='{$_SERVER['HTTP_REFERER']}'";?>" class="button-act">
             </div>
          </td>
          <td class="divider"></td>
          <td class="side"><fieldset>
              <legend>基本信息</legend>
              <table class="table-1">
                <tbody>
                  <tr>
                    <td class="rowhead">所属产品</td>
                    <td><?php echo empty($bug->product)?'':'<a href="/product/view/'.$bug->product->id.'">'.$bug->product->name.'</a>';?></a></td>
                  </tr>
                  <tr>
                    <td class="rowhead">所属模块</td>
                    <td><?php echo empty($bug->module)?'':'<a href="/module/'.$bug->module->id.'">'.$bug->module->name.'</a>';?></a></td>
                  </tr>
                  <tr>
                    <td class="rowhead">BUG类型</td>
                    <td><?php echo empty($bug->type)?'':$pmsdata['bug']['types'][$bug->type]['display'];?></td>
                  </tr>
                  <tr>
                    <td class="rowhead">BUG状态</td>
                    <td><?php echo $pmsdata['bug']['status'][$bug->status]['display'];?></td>
                  </tr>
                  <tr>
                    <td class="rowhead">当前指派</td>
                    <td><?php echo $bug->assigned_to->real_name;?></td>
                  </tr>
                 <tr>
                   <td class="rowhead">解决方案</td>
                   <td><?php echo empty($bug->resolution)?'':$pmsdata['bug']['resolutions'][$bug->resolution]['display'];?></td>
                 </tr>
                </tbody>
              </table>
            </fieldset>
            <fieldset>
              <legend>BUG的一生</legend>
              <table class="table-1">
                <tbody>
                  <tr>
                    <td class="rowhead w-p20">由谁创建</td>
                    <td><?php echo empty($bug->opened_by)?'':$bug->opened_by->real_name.' 于 '.$bug->opened_date;?></td>
                  </tr>
                  <tr>
                    <td class="rowhead">指派给</td>
                    <td><?php echo empty($bug->assigned_to)?'':$bug->assigned_to->real_name.' 于 '.$bug->assigned_date;?></td>
                  </tr>
                  
                  <tr>
                    <td class="rowhead">由谁关闭</td>
                    <td><?php echo empty($bug->closed_by)?'':$bug->closed_by->real_name.' 于 '.$bug->close_date;?></td>
                  </tr>
                  <tr>
                    <td class="rowhead">最后修改</td>
                    <td><?php echo empty($bug->last_edited_by)?'':$bug->last_edited_by->real_name.' 于 '.$bug->last_edited_date;?></td>
                  </tr>
                </tbody>
              </table>
            </fieldset>
            <fieldset>
              <legend>项目任务</legend>
              <table class="table-1">
                <tbody>
                  <tr>
                    <td class="rowhead"> 所属项目</td>
                    <td><?php  if(!empty($bug->project)) echo '<a href="/project/view/'.$bug->project_id.'">'.$bug->project->name.'</a>';?></td>
                  </tr>
                  <tr>
                    <td class="rowhead"> 所属需求</td>
                    <td><?php  if(!empty($bug->story)) echo '<a href="/story/view/'.$bug->story_id.'">'.$bug->story->name.'</a>';?></td>
                  </tr>
                  <tr>
                    <td class="rowhead"> 所属任务</td>
                    <td><?php if(!empty($bug->task)){?><a href="/task/view/<?php echo $bug->task_id;?>"><?php echo $bug->task->name;}?></a></td>
                  </tr>
                </tbody>
              </table>
            </fieldset>
            </td>
        </tr>
      </tbody>
    </table>
  </div>
  <div id="divider"></div>
</div>