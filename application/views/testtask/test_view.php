<div id="wrap">
    <div class="outer">
        <div id="titlebar">
            <div id="main"><?php echo $testtask->name;?></div>
        </div>
        <table class="cont-rt5">
            <tbody>
            <tr valign="top">
                <td>
                    <fieldset>
                        <legend>任务描述</legend>
                        <div class="content">
                            <?php echo $testtask->description;?>
                        </div>
                    </fieldset>
                    <fieldset>
                        <legend>测试总结</legend>
                        <div class="content">
                            <?php echo $testtask->report;?>
                        </div>
                    </fieldset>
                    <div id="actionbox">
                        <fieldset>
                            <legend> 历史记录 </legend>
                            <ol id="historyItem">
                                <?php foreach ($action as $key => $val): ?>
                                    <li value="<?php echo ++$key.'.';?>"> <span> <?php echo $val->date;?>, 由 <strong><?php echo $val->actor;?></strong> <?php echo $pmsdata[$val->type]['action'][$val->action]['display']?>。 </span>
                                        <?php if(!empty($val->history)):?>
                                            <div id="changeBox5" class="changes" style="display: block;">
                                                <?php foreach ($val->history as $k => $v): ?>
                                                    修改了 <strong><i><?php echo $v->field;?></i></strong>，旧值为 "<?php echo $v->old;?>"，新值为 "<?php echo $v->new;?>"。<br>
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
                    <div class="a-center actionlink ">
                        <?php if(has_permission($pmsdata['testtask']['powers']['start']['value']) && $testtask->status == $pmsdata['testtask']['status']['wait']['value']):?>
                        <a class="" href="/testtask/start/<?php echo $testtask->id;?>"><input type="button" value=" 开始 " class="button-act"></a>
                        <?php endif;?>
                        <?php if(has_permission($pmsdata['testtask']['powers']['close']['value']) &&
                            ($testtask->status == $pmsdata['testtask']['status']['doing']['value'] || $testtask->status == $pmsdata['testtask']['status']['wait']['value'])):?>
                        <a class="" href="/testtask/close/<?php echo $testtask->id;?>"><input type="button" value=" 关闭 " class="button-act"></a>
                        <?php endif;?>
                        <?php if(has_permission($pmsdata['testtask']['powers']['edit']['value']) &&
                            ($testtask->status == $pmsdata['testtask']['status']['doing']['value'] || $testtask->status == $pmsdata['testtask']['status']['wait']['value'])):?>
                        <a class="" href="/testtask/edit/<?php echo $testtask->id;?>"><input type="button" value=" 编辑 " class="button-act"></a>
                        <?php endif;?>
                        <a class="" href="/testtask/"><input type="button" value=" 返回 " class="button-act"></a>
                    </div>
                </td>
                <td class="divider"></td>
                <td class="side"><fieldset>
                        <legend>基本信息</legend>
                        <table class="table-1">
                            <tbody>
                            <tr>
                                <td class="rowhead w-p20">所属项目</td>
                                <td><?php echo '<a href="/project/view/'.$testtask->project_id.'">'.$testtask->project_name.'</a>';?></td>
                            </tr>
                            <tr>
                                <td class="rowhead">负责人</td>
                                <td><?php echo $testtask->owner_account;?></td>
                            </tr>
                            <tr>
                                <td class="rowhead">优先级</td>
                                <td><?php echo $testtask->level;?></td>
                            </tr>
                            <tr>
                                <td class="rowhead">开始时间</td>
                                <td><?php echo $testtask->begin_date;?></td>
                            </tr>
                            <tr>
                                <td class="rowhead">结束时间</td>
                                <td><?php echo $testtask->end_date;?></td>
                            </tr>
                            <tr>
                                <td class="rowhead">当前状态</td>
                                <td><?php echo $testtask->status_display;?></td>
                            </tr>
                            <tr>
                                <td class="rowhead">相关需求</td>
                                <td><?php echo '<a href="/story/view/'.$testtask->story_id.'">'.$testtask->story_name.'</a>';?></td>
                            <tr>
                                <td class="rowhead">相关任务</td>
                                <td><?php echo '<a href="/task/view/'.$testtask->task_id.'">'.$testtask->task_name.'</a>';?></td>
                            </tr>
                            </tbody>
                        </table>
                    </fieldset></td>
            </tr>
            </tbody>
        </table>
    </div>
    <div id="divider"></div>
</div>