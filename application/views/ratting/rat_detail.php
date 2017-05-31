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
    width:600px;
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
            <div id="main">您正在查看&nbsp;<font color="blue"><?php echo $params['real_name']?></font>&nbsp;的正负能量评分数据:
            </div>
        </div>
        <form method="post" action="/ratting/rat_detail">
        <input type="hidden" name="real_name" id="real_name" value="<?php echo $params['real_name']?>" />
        <input type="hidden" name="uid" id="uid" value="<?php echo $params['uid']?>" />
        <label>&nbsp;评分时间:</label>
                <select name="year" id="year" onchange="get_month();">
                <?php for($i=$year;$i>=2014;$i--){?>
                    <option value="<?php echo $i;?>" <?php if($params['year']== $i){echo 'selected';}?> ><?php echo $i;?></option>
                <?php }?>
                </select>
                <select name="month" id="month">
                </select>&nbsp;&nbsp;&nbsp;&nbsp;
        <input type="submit" value="查询" class="button-s"/> &nbsp;&nbsp; <input type="button" value="返回" onclick="javascript:window.location.href='/ratting/userlist'" class="button-s"/>
        </form>
        <br/>
    </div>
    <div class="outer">
    <?php if(empty($ratset_detail) || empty($item_grade)){?>
        <div id="titlebar">
            <div id="main" style="color:red">尚无评分数据</div>
        </div>
    <?php }else{?>
    <?php foreach($ratset_detail as $k => $v){?>
        <div id="titlebar">
            <div id="main"><?php echo $v['title']?>(<?php echo $v['percent']*100?>%)</div>
        </div>
        <?php foreach($v['child'] as $ke => $va){?>
        <table>
            <tr>
                <td style="width:50px;">&nbsp;</td>
                <td style="width:50px;">序号</td>
                <td>评分列表</td>
                <td style="width:80px;">得分</td>
                <?php if(in_array($_SESSION['userinfo']['role']->id,$roles)){?>
                    <td style="width:50px;">操作</td>
                <?php }?>
            </tr>

            <?php foreach($va['child'] as $key => $val){?>
                <?php if($key == 0){?>
                <tr>
                    <td rowspan="<?php echo count($va['child'])?>"><?php echo $va['title']?></td>
                    <td><?php echo $key+1?></td>
                    <td align="left"><?php echo $val['content']?></td>
                    <td><?php if(empty($val['grade'])){echo '--';}else{echo $val['grade'];}?></td>
                    <?php if(in_array($_SESSION['userinfo']['role']->id,$roles)){?>
                        <td><a href="/ratting/rat_detail_by_content?uid=<?php echo $params['uid']?>&year=<?php echo $params['year']?>&month=<?php echo $params['month']?>&content_id=<?php echo $val['id']?>">详情</a></td>
                    <?php }?>
                </tr>
                <?php }else{?>
                <tr>
                    <td><?php echo $key+1?></td>
                    <td align="left"><?php echo $val['content']?></td>
                    <td><?php if(empty($val['grade'])){echo '--';}else{echo $val['grade'];}?></td>
                    <?php if(in_array($_SESSION['userinfo']['role']->id,$roles)){?>
                        <td><a href="/ratting/rat_detail_by_content?uid=<?php echo $params['uid']?>&year=<?php echo $params['year']?>&month=<?php echo $params['month']?>&content_id=<?php echo $val['id']?>">详情</a></td>
                    <?php }?>
                </tr>
                <?php }?>
            <?php }?>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td align="left">汇总</td>
                <td><?php echo $va['total_score']?></td>
                <?php if(in_array($_SESSION['userinfo']['role']->id,$roles)){?>
                <td>&nbsp;</td>
                <?php }?>
            </tr>
        </table>
        <?php }?>
    <?php }?>
    <div id="divider"></div>
    <div id="titlebar">
            <div id="main">汇总</div>
    </div>
    <table>
        <tr>
            <td style="width:50px;">序号</td>
            <td>汇总项</td>
            <td style="width:80px;">得分</td>
        </tr>
    <?php $total_all = 0;?>
    <?php foreach($ratset_detail as $k => $v){?>
        <tr>
            <td style="width:50px;"><?php echo $k?></td>
            <td align="left"><?php echo $v['title']?></td>
            <td style="width:80px;"><?php echo $v['total_score']?></td>
        </tr>
        <?php $total_all += $v['total_score']*$v['percent'];?>
    <?php }?>
        <tr>
            <td style="width:50px;">&nbsp;</td>
            <td>总分</td>
            <td style="width:80px;"><?php echo $total_all?></td>
        </tr>
    <?php }?>
    </table>
    <div id="divider"></div>
    </div>
</div>
<script type="text/javascript">
function get_month(){
    var year = $("#year").val();
    var month = $("#month_input").val();
    $('#month').empty();
    if( year != '' ){
        $.ajax({
            type:'GET',
            url:'/ratting/get_month?year='+year,
            dataType:'json',
            success:function(result){
                var len = result.length;
                for( i=0;i<len;i++ ){
                    var is_select = '';
                    if( result[i].month == month ){
                        is_select = "selected='selected'";
                        $("#month_input").val('');
                    }
                    $("#month").append("<option value='"+result[i].month+"' "+is_select+">"+result[i].month+"</option>");
                }
            }
        });
    }
}
$(document).ready(function(){
    get_month();
});
</script>