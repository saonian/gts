<div id="wrap">
    <div class="outer">
        <div id="titlebar">
            <div id="main"><?php echo $project->name;?> </div>
        </div>
        <table class="cont-rt5">
            <tbody>
            <tr valign="top">
                <td>
                    <div id="comment" class="">
                        <fieldset>
                            <legend></legend>
                            <form method="post" action="/project/delay_project/<?php echo $project->id;?>">
                                <table align="center" class="table-1">
                                    <tbody>
                                    <tr>
                                        <th class="rowhead">开始日期</th>
                                        <td><input class="text-3 date dp-applied datetime" value="" id="begin" name="project_begin_date" type="text">
                                            <span class="star">* </span></td>
                                    </tr>
                                    <tr>
                                        <th class="rowhead">结束日期</th>
                                        <td><input class="text-3 date dp-applied datetime" value="" id="end" name="project_end_date" type="text">
                                            <span class="star">* </span></td>
                                    </tr>
                                    <tr>
                                        <th class="rowhead">备注</th>
                                        <td><textarea name="project_comment" id="comment" class="area-5 editor" rows="8"></textarea></td>
                                    </tr>
                                    <tr>
                                        <th class="rowhead"></th>
                                        <td><input type="submit" id="submit" value="保存" class="button-s">
                                            <a class="" href="/project/view/<?php echo $project->id;?>"><input type="button" value=" 返回 " class="button-r"></a>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </form>
                        </fieldset>
                    </div>
                    <div id="actionbox">
                        <fieldset>
                            <legend> 历史记录 </legend>
                            <ol id="historyItem">
                                <?php foreach ($action as $key => $val): ?>
                                    <li value="<?php echo ++$key.'.';?>"> <span> <?php echo $val->date;?>, 由 <strong><?php echo $val->actor;?></strong> <?php echo $pmsdata[$val->type]['action'][$val->action]['display']?>。 </span>
                                        <?php if(!empty($val->history)):?>
                                            <div id="changeBox5" class="changes" style="display: block;">
                                                <?php foreach ($val->history as $key => $val): ?>
                                                    修改了 <strong><i><?php echo $val->field;?></i></strong>，旧值为 "<?php echo $val->old;?>"，新值为 "<?php echo $val->new;?>"。<br>
                                                <?php endforeach;?>
                                            </div>
                                        <?php endif;?>
                                    </li>
                                <?php endforeach;?>
                            </ol>
                        </fieldset>
                    </div>
                </td>
                <td class="divider"></td>
            </tr>
            </tbody>
        </table>
    </div>
    <div id="divider"></div>
</div>