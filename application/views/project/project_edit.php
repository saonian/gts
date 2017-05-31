<!--<script type="text/javascript">
    $(document).ready(function(){
        var result = '<?php /*echo $result; */?>';
        show_error_msg(result);
    });
</script>-->
<div id="wrap">
    <div class="outer">
        <form id="dataform" method="post" action="/project/edit_project/<?php echo $project->id; ?>">
            <table class="table-1 a-left" align="center">
                <caption>
                    <div class="f-left"><strong><a class="" target="" href="/project/edit_project">编辑项目</a></strong></div>
                </caption>
                <tbody>
                <tr>
                    <th class="rowhead">项目名称</th>
                    <td><input class="text-3" value="<?php echo $project->name; ?>" id="name" name="project_name" type="text">
                        <span class="star"> * </span></td>
                </tr>
                <tr>
                    <th class="rowhead">项目负责人</th>
                    <td><select name="project_manage_by" id="manage_by" class="text-3">
                            <?php foreach($users as $key => $user):?>
                                <?php if($user->id == $project->manage_by)
                                    echo '<option value="'.$user->id.'" selected >'.$user->real_name.'</option>';
                                else
                                    echo '<option value="'.$user->id.'" >'.$user->real_name.'</option>';
                                ?>
                            <?php endforeach;?>
                        </select>
                        <span class="star"> * </span>
                    </td>
                </tr>
                <tr>
                    <th class="rowhead">开始日期</th>
                    <td><input class="text-3 date dp-applied datetime" value="<?php echo $project->begin_date; ?>" id="begin" name="project_begin_date" type="text">
                        <a title="选择日期" class="dp-choose-date" href="#">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a><span class="star">* </span></td>
                </tr>
                <tr>
                    <th class="rowhead">结束日期</th>
                    <td><input class="text-3 date dp-applied datetime" value="<?php echo $project->end_date; ?>" id="end" name="project_end_date" type="text">
                        <a title="选择日期" class="dp-choose-date" href="#">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a><span class="star">* </span></td>
                </tr>
                <tr>
                    <th class="rowhead">关联产品</th>
                    <td>
                        <?php 
                        if(empty($all_products)){
                            echo '<font color="red"><a href="/product/create">请先添加产品</a></font>';
                        }else{
                            foreach ($all_products as $val) {
                                $checked = in_array($val->id, $project->product_ids) ? 'checked':'';
                                echo "<input type=\"checkbox\" {$checked} name=\"product_id[]\" value=\"{$val->id}\"/>&nbsp;{$val->name}&nbsp;&nbsp;";
                            }
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th class="rowhead">项目描述</th>
                    <td><textarea class="area-1 editor" rows="6" id="desc" name="project_description"><?php echo $project->description; ?></textarea></td>
                </tr>
                <tr>
                    <th class="rowhead">访问控制</th>
                    <td><input id="aclopen" onclick="setWhite(this.value);" <?php if($project->is_private=='0') echo "checked='checked'" ?> value="0" name="project_is_private" type="radio">
                        <label for="aclopen">默认设置(有项目视图权限，即可访问)</label>
                        <br>
                        <input id="aclprivate" onclick="setWhite(this.value);" <?php if($project->is_private=='1') echo "checked='checked'" ?> value="1 " name="project_is_private" type="radio">
                        <label for="aclprivate">私有项目(只有项目团队成员才能访问)</label>
                        <br>
                    </td>
                </tr>
                <tr>
                    <td class="a-center" colspan="2">
                        <input class="button-s" value="保存" id="submit" type="submit">
                        <a class="" href="/project/page/"><input type="button" value=" 返回 " class="button-r"></a>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
    <div id="divider"></div>
</div>