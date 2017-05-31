<script type="text/javascript" src="/public/js/testtask.js"></script>
<div id="wrap">
    <div class="outer">
        <form method="post" id="dataform" action="/testtask/edit_test/<?php echo $testtask->id;?>">
            <table align="center" class="table-1 a-left">
                <caption>
                    <div class="f-left"><strong>编辑测试</strong></div>
                    <div class="f-right"><span>
        </span> </div>
                </caption>
                <tbody>
                <tr>
                    <th class="rowhead">所属项目</th>
                    <td><select id="t_project_id" name="test_project_id" class="text-3">
                            <?php foreach ($projects as $key => $val):?>
                                <option value="<?php echo $val->id;?>" <?php echo $testtask->project_id == $val->id?'selected':'';?>><?php echo $val->name;?><?php echo empty($pmsdata['project']['status'][$val->status])?'':'('.$pmsdata['project']['status'][$val->status]['display'].')';?></option>
                            <?php endforeach;?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th class="rowhead">负责人</th>
                    <td><select name="test_owner" id="t_owner" class="text-3">
                            <?php foreach($users as $key => $user):?>
                                <option value="<?php echo $user->id;?>"<?php echo $testtask->owner == $user->id?'selected':'';?>><?php echo $user->real_name;?></option>
                            <?php endforeach;?>
                        </select>
                        <span class="star"> * </span>
                    </td>
                </tr>
                <tr>
                    <th class="rowhead">优先级</th>
                    <td><select name="test_level" id="t_level" class="text-3">
                            <?php for($i=1; $i<6; $i++){
                                if($i == $testtask->level)
                                    echo "<option value='".$i."' selected>".$i."</option>";
                                else
                                    echo "<option value='".$i."'>".$i."</option>";
                            }?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th class="rowhead">开始日期</th>
                    <td><input class="text-3 date dp-applied datetime" id="t_begin_date" name="test_begin_date" type="text" value="<?php echo $testtask->begin_date; ?>">
                        <span class="star">* </span></td>
                </tr>
                <tr>
                    <th class="rowhead">结束日期</th>
                    <td><input class="text-3 date dp-applied datetime" id="t_end_date" name="test_end_date" type="text" value="<?php echo $testtask->end_date; ?>">
                        <span class="star">* </span></td>
                </tr>
                <tr>
                    <th class="rowhead">当前状态</th>
                    <td><select name="test_status" id="t_status" class="text-3">
                            <?php foreach($status as $key => $s):?>
                                <option value="<?php echo $key;?>" <?php echo $testtask->status == $key?'selected':'';?>><?php echo $s['display'];?></option>
                            <?php endforeach;?>
                        </select>
                    </td>
                </tr>
                <tr id="tr_story" class="">
                    <th class="rowhead">相关需求</th>
                    <td><select id="t_story_id" name="test_story_id" class="text-1">
                            <option value="<?php echo $testtask->story_id;?>" selected><?php echo $testtask->story_name;?></option>
                        </select>
                    </td>
                </tr>
                <tr id="tr_task" class="">
                    <th class="rowhead">相关任务</th>
                    <td><select id="t_task_id" name="test_task_id" class="text-1">
                            <option value="<?php echo $testtask->task_id;?>" selected><?php echo $testtask->task_name;?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th class="rowhead">测试名称</th>
                    <td><input class="text-1" value="<?php echo $testtask->name; ?>" id="t_name" name="test_name" type="text">
                        <span class="star"> * </span></td>
                </tr>
                <tr>
                    <th class="rowhead">项目描述</th>
                    <td><textarea class="area-1 editor" rows="6" id="t_description" name="test_description">
                            <?php echo $testtask->description; ?>
                        </textarea>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="a-center">
                        <input type="submit" id="submit" value="保存" class="button-s">
                        <a class="" href="/testtask/page/"><input type="button" value=" 返回 " class="button-r"></a>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
    <div id="divider"></div>
</div>