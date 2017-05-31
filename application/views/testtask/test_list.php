<?php 
$sort = $this->input->get('sort');
$sort = (empty($sort) || $sort=='asc')?'desc':'asc';
?>
<div id="wrap">
    <div class="outer">
        <div id="featurebar">
          <div class="f-left">
          <span id="allTab" <?php echo empty($_GET['assignedtome']) && empty($_GET['openedbyme'])?'class="active"':'';?>><a target="" href="/testtask">所有</a></span>
          <span id="assignedtomeTab" <?php echo $this->input->get('assignedtome')?'class="active"':'';?>><a target="" href="/testtask?assignedtome=1">指派给我</a></span>
          <span id="statusTab">
            <select onchange="switchStatus('status',this.value)" id="status" name="status" class="text-2">
              <option value="">所有状态</option>
              <?php foreach ($pmsdata['testtask']['status'] as $key => $val):?>
              <option value="<?php echo $val['value'];?>" <?php echo $this->input->get('status')==$val['value']?'selected':'';?>><?php echo $val['display'];?></option>
              <?php endforeach;?>
            </select>
          </span>
          </div>
        </div>
        <form id="projectStoryForm" method="post">
            <table class="table-1 colored tablesorter datatable">
                <caption class="caption-tl pb-10px">
                    <div class="f-left"><strong>待测列表</strong></div>
                    <div class="f-right"> <span class="link-button">
                            <?php if(has_permission($pmsdata['testtask']['powers']['create']['value'])):?>
                            <a class="" target="" href="/testtask/create">提交测试</a>
                            <?php endif;?>
                        </span></div>
                </caption>
                <thead>
                <tr class="colhead">
                    <th class="w-user"> <div class="header"><a href="?order=id&sort=<?php echo $sort?>">ID</a> </div></th>
                    <th class=""> <div class="header"><a href="?order=name&sort=<?php echo $sort?>">测试名称</a> </div></th>
                    <th class="w-user"> <div class="header"><a href="?order=owner&sort=<?php echo $sort?>">负责人</a> </div></th>
                    <th class="w-200px "> <div class="header"><a href="?order=begin_date&sort=<?php echo $sort?>">开始时间</a> </div></th>
                    <th class="w-200px "> <div class="header"><a href="?order=end_date&sort=<?php echo $sort?>">结束时间</a> </div></th>
                    <th class="w-status "> <div class="header"><a href="?order=status&sort=<?php echo $sort?>">项目状态</a> </div></th>
                    <th class="w-80px ">操作</th>
                </tr>
                </thead>
                <?php if(has_permission($pmsdata['testtask']['powers']['page']['value'])):?>
                <tbody>
                <?php foreach($data as $key => $val):?>
                    <tr class="a-center odd">
                        <td><a href="/testtask/view/<?php echo $val->id;?>"><?php echo $val->id;?></a></td>
                        <td title="<?php echo $val->name;?>" class="a-left"><a href="/testtask/view/<?php echo $val->id;?>"><?php echo $val->name;?></a></td>
                        <td><?php echo $val->owner;?></td>
                        <td><?php echo $val->begin_date;?></td>
                        <td><?php echo $val->end_date;?></td>
                        <td><?php echo $val->status;?></td>
                        <td>
                            <?php if(has_permission($pmsdata['testtask']['powers']['view']['value'])):?>
                            <a href="/testtask/view/<?php echo $val->id;?>">查看</a>
                            <?php endif;?>
                        </td>
                    </tr>
                <?php endforeach;?>
                </tbody>
                <?php else:?>
                    <tbody><tr><td colspan="10" style="text-align:center;">没有查看列表的权限</td></tr></tbody>
                <?php endif;?>
                <tfoot>
                <tr>
                    <td colspan="10"><div class="f-left">
                            本页共 <strong><?php echo $total;?></strong> 个测试
                        </div></td>
                </tr>
                </tfoot>
            </table>
        </form>
    </div>
    <div id="divider"></div>
</div>