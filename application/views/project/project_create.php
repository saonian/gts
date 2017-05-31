<!--<script type="text/javascript">
    $(document).ready(function(){
        var result = '<?php /*echo $result; */?>';
        show_error_msg(result);
    });
</script>-->
<div id="wrap">
    <div class="outer">
        <form id="dataform" method="post" action="/project/create_project">
            <table class="table-1 a-left" align="center">
                <caption>
                    <div class="f-left"><strong><a class="" target="" href="/project/create_project">新增项目</a></strong></div>
                </caption>
                <tbody>
                <tr>
                    <th class="rowhead">项目名称</th>
                    <td><input class="text-3" value="" id="name" name="project_name" type="text">
                        <span class="star"> * </span></td>
                </tr>
                <tr>
                    <th class="rowhead">项目负责人</th>
                    <td><select name="project_manage_by" id="manage_by" class="text-3">
                            <?php foreach($users as $key => $user):?>
                            <option value="<?php echo $user->id;?>"><?php echo $user->real_name;?></option>
                            <?php endforeach;?>
                        </select>
                        <span class="star"> * </span>
                    </td>
                </tr>
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
                    <th class="rowhead">关联产品</th>
                    <td>
                        <?php foreach ($all_products as $val) {
                            echo "<input type=\"checkbox\" name=\"product_id[]\" value=\"{$val->id}\"/>&nbsp;{$val->name}&nbsp;&nbsp;";
                        }?>
                    </td>
                </tr>
                <tr>
                    <th class="rowhead">项目描述</th>
                    <td><textarea class="area-1 editor" rows="6" id="desc" name="project_description"></textarea></td>
                </tr>
                <tr>
                    <th class="rowhead">访问控制</th>
                    <td><input id="aclopen" onclick="setWhite(this.value);" checked="checked" value="0" name="project_is_private" type="radio">
                        <label for="aclopen">默认设置(有项目视图权限，即可访问)</label>
                        <br>
                        <input id="aclprivate" onclick="setWhite(this.value);" value="1 " name="project_is_private" type="radio">
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