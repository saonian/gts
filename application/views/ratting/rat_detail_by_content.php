<?php if(!empty($params['month'])){?>
    <input type="hidden" id="month_input" value="<?php echo $params['month']?>"/>
<?php }else{?>
    <input type="hidden" id="month_input" value="<?php echo $month?>"/>
<?php }?>
<?php
$roles = config_item('ratting_role');
?>
<style type="text/css">
.outer table{
    border:1px solid #CCCCCC;
    border-collapse:collapse;
    width:1000px;
    font:Georgia 11px;
    color:#333333;
    text-align:center;
} 
.outer table td{
    border:1px solid #CCCCCC;

    height:30px;
} 
.outer table td a{
    text-decoration: none;
} 
.outer table td a:hover{
    text-decoration: underline;
    color: #f60;
}
</style>
<div id="wrap">
    <div class="outer">
        <div id="titlebar">
            <div id="main">您正在查看&nbsp;<font color="blue"><?php echo $params['real_name']?></font>&nbsp;的正负能量详细评分数据:
            </div>
        </div>
        <div id="titlebar">
            <div id="main">评分标题：<?php echo $params['content']?></div>
        </div>
        <form method="post" action="/ratting/rat_detail_by_content">
            <input type="hidden" name="real_name" id="real_name" value="<?php echo $params['real_name']?>" />
            <input type="hidden" name="uid" id="uid" value="<?php echo $params['uid']?>" />
            <input type="hidden" name="content_id"  value="<?php echo $params['content_id']?>" />
            <label>&nbsp;评分时间:</label>
            <input type="text" name="start_date" id="begin" class="datetime text-2" value="<?php if(isset($params['start_date'])){echo $params['start_date'];}?>">&nbsp;&nbsp;至&nbsp;&nbsp;
            <input type="text" name="end_date" id="end" class="datetime text-2" value="<?php if(isset($params['end_date'])){echo $params['end_date'];}?>">&nbsp;&nbsp;
            <label>&nbsp;评分级别:</label>
            <select name="level">
                <option value="">所有</option>
                <option value="好" <?php if($params['level'] == '好'){?> selected <?php }?> >好</option>
                <option value="中" <?php if($params['level'] == '中'){?> selected <?php }?> >中</option>
                <option value="差" <?php if($params['level'] == '差'){?> selected <?php }?> >差</option>
            </select>&nbsp;&nbsp;
            <input type="submit" value="查询" class="button-s"/> &nbsp;&nbsp; <input type="button" value="返回" onclick="javascript:window.location.href='/ratting/rat_detail?uid=<?php echo $params['uid']?>&year=<?php echo substr($params['start_date'],0,4);?>&month=<?php echo intval(substr($params['start_date'],5,2));?>'" class="button-s"/>
        </form>
        <br/>
    </div>
    <div class="outer">
        <table>
            <tbody>
                <tr>
                    <td style="width:50px;">序号</td>
                    <td style="width:80px;">评分人</td>
                    <td style="width:150px;">评分时间</td>
                    <td style="width:100px;">评分级别</td>
                    <td style="width:50px;">得分</td>
                    <td>评分事件</td>
                    <?php if(in_array($_SESSION['userinfo']['role']->id,$roles)){?>
                        <td style="width:80px;">操作</td>
                    <?php }?>
                </tr>
                <?php foreach($detail_lists['list'] as $key => $val){?>
                <tr>
                    <td><?php echo $key + 1?></td>
                    <td><?php echo $val['rating_name']?></td>
                    <td><?php echo $val['addtime']?></td>
                    <td><?php echo $val['level']?></td>
                    <td><?php echo $val['grade']?></td>
                    <td><?php echo $val['rating_desc']?></td>
                    <?php if(in_array($_SESSION['userinfo']['role']->id,$roles)){?>
                        <td><a href="/ratting/ratting_content_detail?id=<?php echo $val['id']?>">查看详情</a></td>
                    <?php }?>
                </tr>
                <?php }?>
            </tbody>
        <tfoot>
          <tr>
            <td colspan="10">
                <div class="f-left">
                    总共 <strong><?php echo $detail_lists['total'];?></strong> 个记录 &nbsp;
                    共 <strong><?php echo $detail_lists['total_page'];?></strong> 页 &nbsp;
                </div>
                <div class="f-left"><?php echo $detail_lists['page_html'];?></div>
            </td>
          </tr>
        </tfoot>
        </table>
        <div id="divider"></div>
    </div>
</div>
<script type="text/javascript">
</script>