<script language="javascript">
    function delcfm() {
        var res = confirm("确认要删除？");
        return res;
    }
    function closecfm() {
        var res = confirm("是否结束该项目？");
        return res;
    }
</script>
<div id="wrap">
<div class="outer">
<div id="titlebar">
    <div id="main"><?php echo $project->name;?></div>
</div>
<table class="cont-rt5">
    <tbody>
    <tr valign="top">
        <td>
            <fieldset>
                <legend>项目描述</legend>
                <div class="content">
                    <?php echo $project->description;?>
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
            <div class="a-center actionlink ">
                <?php if(has_permission($pmsdata['project']['powers']['start']['value']) && $project->status == $pmsdata['project']['status']['wait']['value']):?>
                <a class="" href="/project/start/<?php echo $project->id;?>"><input type="button" value=" 开始 " class="button-act"></a>
                <?php endif;?>
                <?php if(has_permission($pmsdata['project']['powers']['delay']['value']) &&
                    ($project->status == $pmsdata['project']['status']['doing']['value'] || $project->status == $pmsdata['project']['status']['wait']['value'])):?>
                <a class="" href="/project/delay/<?php echo $project->id;?>"><input type="button" value=" 延期 " class="button-act"></a>
                <?php endif;?>
                <?php if(has_permission($pmsdata['project']['powers']['hang']['value']) &&
                    ($project->status == $pmsdata['project']['status']['doing']['value'] || $project->status == $pmsdata['project']['status']['wait']['value'])):?>
                <a class="" href="/project/hang/<?php echo $project->id;?>"><input type="button" value=" 挂起 " class="button-act"></a>
                <?php endif;?>
                <?php if(has_permission($pmsdata['project']['powers']['close']['value']) && $project->status == $pmsdata['project']['status']['doing']['value']):?>
                <a class="" href="/project/close/<?php echo $project->id;?>" onclick="return closecfm();"><input type="button" value=" 结束 " class="button-act"></a>
                <?php endif;?>
                <?php if(has_permission($pmsdata['project']['powers']['edit']['value'])):?>
                <a class="" href="/project/edit/<?php echo $project->id;?>"><input type="button" value=" 编辑 " class="button-act"></a>
                <?php endif;?>
                <a class="" href="/project/"><input type="button" value=" 返回 " class="button-act"></a>
            </div>
        </td>
        <td class="divider"></td>
        <td class="side"><fieldset>
                <legend>基本信息</legend>
                <table class="table-1">
                    <tbody>
                    <tr>
                        <td class="rowhead w-p20">项目名称</td>
                        <td><?php echo $project->name;?></td>
                    </tr>
                    <tr>
                        <td class="rowhead w-p20">相关产品</td>
                        <td>
                            <?php 
                                foreach ($project->products as $val) {
                                    echo "<a href=\"/product/view/{$val->id}\">{$val->name}</a></br>";
                                }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="rowhead">起止时间</td>
                        <td><?php echo $project->begin_date.'--'.$project->end_date;?></td>
                    </tr>
                    <tr>
                        <td class="rowhead">项目状态</td>
                        <td><?php echo $pmsdata['project']['status'][$project->status]['display'];?></td>
                    </tr>
                    <tr>
                        <td class="rowhead">访问控制</td>
                        <td><?php echo $project->is_private_display;?></td>
                    </tr>
                    <tr>
                        <td class="rowhead">项目负责人</td>
                        <td><?php echo $project->manage_account;?></td>
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