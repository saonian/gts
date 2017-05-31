<?php 
$sort = $this->input->get('sort');
$sort = (empty($sort) || $sort=='asc')?'desc':'asc';
?>
<div id="wrap">
    <div class="outer">
        <form id="projectStoryForm" method="post">
            <table class="table-1 colored tablesorter datatable">
                <caption class="caption-tl pb-10px">
                    <div class="f-left"><strong>项目列表</strong></div>
                    <div class="f-right"> <span class="link-button">
                           <?php if(has_permission($pmsdata['project']['powers']['create']['value'])):?>
                            <a class="" target="" href="/project/create">新增项目</a>
                            <?php endif;?>
                        </span></div>
                </caption>
                <thead>
                <tr class="colhead">
                    <th class="w-user"> <div class="header"><a href="?order=id&sort=<?php echo $sort?>">ID</a> </div></th>
                    <th class=""> <div class="header"><a href="?order=name&sort=<?php echo $sort?>">项目名称</a> </div></th>
                    <th class="w-200px "> <div class="header"><a href="?order=end_date&sort=<?php echo $sort?>">结束时间</a> </div></th>
                    <th class="w-status "> <div class="header"><a href="?order=status&sort=<?php echo $sort?>">项目状态</a> </div></th>
                    <th class="w-80px ">操作</th>
                </tr>
                </thead>
                <?php if(has_permission($pmsdata['project']['powers']['page']['value'])):?>
                <tbody>
                <?php foreach($data as $key => $val):?>
                    <tr class="a-center <?php echo ($key%2==0)?'odd':'even';?>">
                        <td><a href="/project/view/<?php echo $val->id;?>"><?php echo $val->id;?></a></td>
                        <td title="<?php echo $val->name;?>" class="a-left"><a href="/project/view/<?php echo $val->id;?>"><?php echo $val->name;?></a></td>
                        <td><?php echo $val->end_date;?></td>
                        <td><?php echo $val->status;?></td>
                        <td>
                            <?php if(has_permission($pmsdata['project']['powers']['view']['value'])):?>
                                <a href="/project/view/<?php echo $val->id;?>">查看</a>
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
                            本页共 <strong><?php echo $total;?></strong> 个项目
                        </div>
                        <?php echo $page_html;?>
                    </td>
                </tr>
                </tfoot>
            </table>
        </form>
    </div>
    <div id="divider"></div>
</div>