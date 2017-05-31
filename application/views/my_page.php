<div id="wrap">
  <div class="outer">
    <table class="cont" id="row2">
      <tbody>
        <tr valign="top">          
          <td width="50%" style="padding-right:20px"><div style="height: 238px;" class="block linkbox2">
              <table class="table-1 fixed colored">
                <caption>
                <div class="f-left">我的任务</div>
                <div class="f-right"><a href="/task?finishedbyme=1&order=status&sort=asc&allproject=1">更多<span class="icon-more"></span></a> </div>
                </caption>
                <tbody>
                  <?php foreach($my_task as $val):?>
                  <tr style="background: none repeat scroll 0% 0% rgb(255, 255, 255);">
                    <td class="nobr">#<?php echo $val->id;?> <font color="red">LV.<?php echo $val->level;?></font>&nbsp;<a href="/task/view/<?php echo $val->id.'?p='.$val->project_id;?>"><?php echo $val->name;?></a>&nbsp;&nbsp;(<font color="<?php echo $pmsdata['task']['status'][$val->status]['color'];?>"><?php echo $pmsdata['task']['status'][$val->status]['display'];?></font>&nbsp;<font color="red">截止日期:<?php echo $val->deadline;?></font>)</td>
                    <td width="5">&nbsp;</td>
                  </tr>
                  <?php endforeach;?>
                </tbody>
              </table>
            </div></td>
            <td width="50%" style="padding-right:20px"><div style="height: 238px;" class="block linkbox2">
              <table class="table-1 fixed colored">
                <caption>
                <div class="f-left">我的需求</div>
                <div class="f-right"><a href="/story?openedbyme=1&order=status&sort=asc&allproject=1">更多<span class="icon-more"></span></a> </div>
                </caption>
                <tbody>
                  <?php foreach($my_verify_story as $val):?>
                  <tr style="background: none repeat scroll 0% 0% rgb(255, 255, 255);">
                    <td class="nobr">#<?php echo $val->id;?> <font color="red">LV.<?php echo $val->level;?></font>&nbsp;<a href="/story/view/<?php echo $val->id.'?p='.$val->project_id;?>"><?php echo $val->name;?></a>(<font color="<?php echo $pmsdata['story']['status'][$val->status]['color'];?>"><?php echo $pmsdata['story']['status'][$val->status]['display'];?></font>&nbsp;<font color="red">待审核</font>)<?php echo !empty($val->reviewed_result)&&$val->reviewed_result!=$pmsdata['story']['reviewed_result']['pass']['value']?'&nbsp;<font color="red">(审核未通过:'.$pmsdata['story']['reviewed_result'][$val->reviewed_result]['display'].')</font>':'';?></td>
                    <td width="5">&nbsp;</td>
                  </tr>
                  <?php endforeach;?>
                  <?php foreach($my_story as $val):?>
                  <tr style="background: none repeat scroll 0% 0% rgb(255, 255, 255);">
                    <td class="nobr">#<?php echo $val->id;?> <font color="red">LV.<?php echo $val->level;?></font>&nbsp;<a href="/story/view/<?php echo $val->id.'?p='.$val->project_id;?>"><?php echo $val->name;?></a>(<font color="<?php echo $pmsdata['story']['status'][$val->status]['color'];?>"><?php echo $pmsdata['story']['status'][$val->status]['display'];?></font>)<?php echo !empty($val->reviewed_result)&&$val->reviewed_result!=$pmsdata['story']['reviewed_result']['pass']['value']?'&nbsp;<font color="red">(审核未通过:'.$pmsdata['story']['reviewed_result'][$val->reviewed_result]['display'].')</font>':'';?></td>
                    <td width="5">&nbsp;</td>
                  </tr>
                  <?php endforeach;?>
                </tbody>
              </table>
            </div></td>
        </tr>
        <tr valign="top">
          <td width="50%" style="padding-right:20px"><div style="height: 238px;" class="block linkbox2">
              <table class="table-1 fixed colored">
                <caption>
                <div class="f-left">我的BUG</div>
                <div class="f-right"><a href="/bug">更多<span class="icon-more"></span></a> </div>
                </caption>
                <tbody>
                  <?php foreach($my_bug as $val):?>
                  <tr style="background: none repeat scroll 0% 0% rgb(255, 255, 255);">
                    <td class="nobr">#<?php echo $val->id;?> <a href="/bug/detail?id=<?php echo $val->id.'&p='.$val->project_id;?>"><?php echo $val->title;?></a>(<font color="green"><?php echo $pmsdata['bug']['status'][$val->status]['display'];?></font>)</td>
                    <td width="5">&nbsp;</td>
                  </tr>
                  <?php endforeach;?>
                </tbody>
              </table>
            </div></td>
          <td width="50%" style="padding-right:20px"><div style="height: 238px;" class="block linkbox2">
              <table class="table-1 fixed colored">
                <caption>
                <div class="f-left">我的评分</div>
                <div class="f-right"><a href="/grade">更多<span class="icon-more"></span></a> </div>
                </caption>
                <tbody>
                  <?php foreach($my_story_grade as $val):?>
                  <tr style="background: none repeat scroll 0% 0% rgb(255, 255, 255);">
                    <td class="nobr"><?php echo $val->id;?> <a href="/grade/storyview/<?php echo $val->id.'?p='.$val->project_id;?>"><?php echo $val->name;?></a></td>
                    <td width="5">&nbsp;</td>
                  </tr>
                  <?php endforeach;?>
                  <?php foreach($my_task_grade as $val):?>
                  <tr style="background: none repeat scroll 0% 0% rgb(255, 255, 255);">
                    <td class="nobr"><?php echo $val->id;?> <a href="/grade/taskview/<?php echo $val->id.'?p='.$val->project_id;?>"><?php echo $val->name;?></a></td>
                    <td width="5">&nbsp;</td>
                  </tr>
                  <?php endforeach;?>
                </tbody>
              </table>
            </div></td>
          <td width="50%">&nbsp;</td>
        </tr>
      </tbody>
    </table>
  </div>
  <div id="divider"></div>
</div>