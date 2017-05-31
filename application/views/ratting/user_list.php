<?php 
$sort = $this->input->get('sort');
$sort = (empty($sort) || $sort=='asc')?'desc':'asc';
$config_item = config_item('upimg');
?>
<?php if(!empty($params['month'])){?>
	<input type="hidden" id="month_input" value="<?php echo $params['month']?>"/>
<?php }else{?>
	<input type="hidden" id="month_input" value="<?php echo $month?>"/>
<?php }?>
<?php
$roles = config_item('ratting_role');
?>
<style type="text/css">
.bigpicWrap {
	position: fixed;
	top: 1;
	left: 13%;
	z-index: 99;
	background-color: #FFFFFF;
	border:1px solid gray;
	-moz-border-radius: 10px;
	-webkit-border-radius: 10px;
	color:#fff;
	padding: 5px;
	width: 250px;
}
.bigpicWrap p {
	font-weight: bold;
	font-family:"Microsoft Yahei";
	color:#141414;
	font-size: 16px;
	margin-left: 3px;
	margin-bottom: 2px;
	text-align: left;
	float:left;
	clear:left;
}
.p1 {
	margin-top: 10px;
}
.bigpicWrap p img {
	width: 160px;
}
.bigpicWrap p span {
	color:gray;
	font-size: 14px;
	table-layout: fixed;
	word-wrap:break-word; 
	word-break:break-all;
	white-space:normal;
}
</style>
<div id="wrap">
  <div class="outer">
    <form id="projectStoryForm" method="get" action="/ratting/userlist">
    	<table style="width:100%;" class="table-2">
    		<tr>
    			<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<select id="department_id" name="department_id" class="text-2" onchange="form_submit();">
						<option value="">所有部门</option>
						<?php if(!in_array($_SESSION['userinfo']['role']->id,$roles)){?><option value="tt" <?php if($params['department_id']=='tt'){echo "selected";}?>>特别关注</option><?php }?>
						<?php foreach($parent_department as $key=>$val){ ?>
						<option value="<?php echo $key;?>" <?php if($key==$params['department_id']){echo "selected";}?>><?php echo $val;?></option>
						<?php } ?>
					</select>&nbsp;
					<label>真实姓名:</label>
    				<input id="keyword" name="real_name" type="text" class="text-2" value="<?php if(isset($params['real_name'])){echo $params['real_name'];}?>">&nbsp;&nbsp;
    				<?php if(in_array($_SESSION['userinfo']['role']->id,$roles)){?>
    					<select name="year" id="year" onchange="get_month();">
						<?php for($i=$year;$i>=2014;$i--){?>
							<option value="<?php echo $i;?>" <?php if($params['year']== $i){echo 'selected';}?> ><?php echo $i;?></option>
						<?php }?>
						</select>
						<select name="month" id="month">
						</select>
    				<?php }?>&nbsp;
    				<?php if(in_array($_SESSION['userinfo']['role']->id,$roles)){?>
    				<label>今日是否登录:</label>
    				<select name="is_login" onchange="form_submit();">
    					<option value="0">所有</option>
    					<option value="1" <?php if($params['is_login']== 1){echo "selected";}?> >是</option>
    					<option value="2" <?php if($params['is_login']== 2){echo "selected";}?> >否</option>
    				</select>
    				<?php }?>
					<input type="submit" value="搜索" class="button-s" style="width:60px;cursor:pointer;">&nbsp;&nbsp;
    			</td>
    			<td>今日登录比例：<?php echo $account['online_num']?>/<?php echo $account['total_num']?></td>
    		</tr>
    	</table>
    </form>
      <table class='table-1 fixed colored tablesorter datatable'>
        <thead>
          <tr class="colhead" style="height:30px;">
          <?php if(in_array($_SESSION['userinfo']['role']->id,$roles)){?>
            <th width="50px;"> <div class="header"><a href="#">序号</a> </div></th>
            <th width="80px;"> <div class="header"><a href="#">头像</a> </div></th>
            <th width="100px;"> <div class="header"><a href="?order_by=account&sort=<?php echo $sort?>">用户名</a> </div></th>
            <th width="80px;"> <div class="header"><a href="?order_by=real_name&sort=<?php echo $sort?>">姓名</a> </div></th>
            <th width="150px;"> <div class="header"><a href="?order_by=dept_name&sort=<?php echo $sort?>">部门</a> </div></th>
            <th> <div class="header"><a href="?order_by=performance_score&sort=<?php echo $sort?>">业绩得分</a> </div></th>
            <th> <div class="header"><a href="?order_by=behavior_score&sort=<?php echo $sort?>">行为得分</a> </div></th>
            <th> <div class="header"><a href="?order_by=plus&sort=<?php echo $sort?>">当月加分</a> </div></th>
            <th> <div class="header"><a href="?order_by=minus&sort=<?php echo $sort?>">当月减分</a> </div></th>
            <th> <div class="header"><a href="?order_by=grade&sort=<?php echo $sort?>">当月得分</a> </div></th>
            <th width="100px;"> <div class="header"><a href="?order_by=plus_last&sort=<?php echo $sort?>">当月加分剩余</a> </div></th>
            <th width="100px;"> <div class="header"><a href="?order_by=minus_last&sort=<?php echo $sort?>">当月减分剩余</a> </div></th>
<!--             <th> <div class="header"><a href="#">业绩加分</a> </div></th>
            <th> <div class="header"><a href="#">业绩减分</a> </div></th>
            <th> <div class="header"><a href="#">行为加分</a> </div></th>
            <th> <div class="header"><a href="#">行为减分</a> </div></th> -->
            <!-- <th> <div class="header"><a href="?order_by=total&sort=<?php echo $sort?>">总得分</a> </div></th> -->
			<th width="260px;">操作</th>
          <?php }else{?>
          	<th width="50px;"> <div class="header"><a href="#">序号</a> </div></th>
          	<th width="80px;"> <div class="header"><a href="#">头像</a> </div></th>
            <th width="20%"> <div class="header"><a href="?order_by=account&sort=<?php echo $sort?>">用户名</a> </div></th>
            <th width="20%"> <div class="header"><a href="?order_by=real_name&sort=<?php echo $sort?>">姓名</a> </div></th>
            <th width="20%"> <div class="header"><a href="?order_by=dept_name&sort=<?php echo $sort?>">部门</a> </div></th>
			<th width="50%">操作</th>
			<?php }?>
          </tr>
        </thead>
         <tbody>
		 <?php if(count($account['list'])>0){?>
		 <?php foreach($account['list'] as $key => $val){?>
          <tr class="a-center <?php echo ($key%2==0)?'odd':'even';?>" style="height:30px;<?php if($val['online'] == 1 && $val['is_manage'] == 1){ echo 'color:blue';}else if($val['online'] == 0){ echo 'color:gray';}?>" >
         	<?php if(in_array($_SESSION['userinfo']['role']->id,$roles)){?>
	            <td style="vertical-align:middle;"><?php echo $key+1+$account['pagesize']*($account['curpage']-1);?></td>
	            <td style="vertical-align:middle;"><?php if(empty($val['image'])){?><img class="showbig" src="/<?php echo $config_item['small_img'].'default.gif';?>" width="64" height="64" data-big="/<?php echo $config_item['big_img'].'default.gif';?>" data-uname= "<?php echo $val['realname']?>" data-sign="<?php if(empty($val['sign'])){ echo '天使之所以能飞翔，是因为她把自己看得很轻^o^';} else{ echo $val['sign'];}?>"/><?php }else{?><img class="showbig" src="/<?php echo $config_item['small_img'].$val['image'];?>" width="64" height="64" data-big="/<?php echo $config_item['big_img'].$val['image'];?>" data-uname= "<?php echo $val['realname']?>" data-sign="<?php if(empty($val['sign'])) echo '天使之所以能飞翔，是因为她把自己看得很轻^o^'; else echo $val['sign'];?>"/><?php }?></td>
	            <td style="vertical-align:middle;"><?php echo $val['account'];?></td>
	            <td style="vertical-align:middle;"><?php echo $val['realname'];?></td>
	            <td style="vertical-align:middle;"><?php echo $val['dept_name'];?></td>
	            <td style="vertical-align:middle;"><?php echo $val['performance_score'];?></td>
	            <td style="vertical-align:middle;"><?php echo $val['behavior_score'];?></td>
	            <td style="vertical-align:middle;"><?php echo $val['plus'];?></td>
	            <td style="vertical-align:middle;"><?php echo $val['minus'];?></td>
	            <td style="vertical-align:middle;"><?php echo $val['grade'];?></td>
	            <td style="vertical-align:middle;"><?php echo $val['plus_last'];?></td>
	            <td style="vertical-align:middle;"><?php echo $val['minus_last'];?></td>
<!-- 	            <td><?php echo $val['performance_plus'];?></td>
	            <td><?php echo $val['performance_minus'];?></td>
	            <td><?php echo $val['behavior_plus'];?></td>
	            <td><?php echo $val['behavior_minus'];?></td> -->
	            <!-- <td><?php echo $val['total'];?></td> -->
	            <td style="vertical-align:middle;"><a href="/ratting/rat_detail?uid=<?php echo $val['u_id']?>&year=<?php echo $params['year']?>&month=<?php echo $params['month']?>">查看</a> | <a href="/ratting/rat_index?uid=<?php echo $val['u_id']?>">评分</a> | <a href="/ratting/auditlist?rated_name=<?php echo $val['real_name']?>&rated_uid=<?php echo $val['u_id']?>">审核</a> | <?php if($val['is_manage'] == 1){?><a href="javascript:void(0);" onclick="change_manage(this)" id="manage" name="<?php echo $val['u_id'];?>">取消管理</a><?php }else{?><a href="javascript:void(0);" id="manage" name="<?php echo $val['u_id'];?>" onclick="change_manage(this)" >设为管理</a><?php }?></td>
	            <?php }else{?>
	            <td style="vertical-align:middle;"><?php echo $key+1+$account['pagesize']*($account['curpage']-1);?></td>
	            <td style="vertical-align:middle;"><?php if(empty($val['image'])){?><img class="showbig" src="/<?php echo $config_item['small_img'].'default.gif';?>" width="64" height="64" data-big="/<?php echo $config_item['big_img'].'default.gif';?>" data-uname= "<?php echo $val['realname']?>" data-sign="<?php if(empty($val['sign'])){ echo '天使之所以能飞翔，是因为她把自己看得很轻^o^';} else{ echo $val['sign'];}?>"/><?php }else{?><img class="showbig" src="/<?php echo $config_item['small_img'].$val['image'];?>" width="64" height="64" data-big="/<?php echo $config_item['big_img'].$val['image'];?>" data-uname= "<?php echo $val['realname']?>" data-sign="<?php if(empty($val['sign'])) echo '天使之所以能飞翔，是因为她把自己看得很轻^o^'; else echo $val['sign'];?>"/><?php }?></td>
	            <td style="vertical-align:middle;"><?php echo $val['account'];?></td>
	            <td style="vertical-align:middle;"><?php echo $val['realname'];?></td>
	            <td style="vertical-align:middle;"><?php echo $val['dept_name'];?></td>
	            <td style="vertical-align:middle;"><a href="/ratting/rat_index?uid=<?php echo $val['u_id']?>">评分</a> | <?php if($val['attention'] == 1){?><a href="javascript:void(0);" onclick="change(this)" id="att" name="<?php echo $val['u_id'];?>">取消关注</a><?php }else{?><a href="javascript:void(0);" id="att" name="<?php echo $val['u_id'];?>" onclick="change(this)" >关注</a><?php }?></td>
         	<?php }?>
          </tr>
		<?php }?>
		<?php }else{ ?>
		 <tr class="a-center <?php echo ($key%2==0)?'odd':'even';?>">
            <td colspan="8">暂时无记录</td>
          </tr>
		<?php }?>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="10">
				<div class="f-left">
					总共 <strong><?php echo $account['total'];?></strong> 个记录 &nbsp;
					共 <strong><?php echo $account['total_page'];?></strong> 页 &nbsp;
				</div>
				<div class="f-left"><?php echo $account['page_html'];?></div>
			</td>
          </tr>
        </tfoot>
     </table>
  </div>
  <div id="divider"></div>
</div>
<script type="text/javascript">
$("#keyword").autocomplete("/namedata", {autoFill: true});
$('#keyword').bind("input.autocomplete", function(){ 
	$(this).trigger('keydown.autocomplete'); 
});
function change(obj){
	var uid = obj.name;
	if(uid!=''){
		$.ajax({
			type:"POST",
			data:{'uid':uid},
			url:'/ratting/attention',
			success:function(result){
				if(result){
					$(obj).html(result);
				}
			}
			
		});		
	}
	
}
function change_manage(obj){
	var uid = obj.name;
	if(uid!=''){
		$.ajax({
			type:"POST",
			data:{'uid':uid},
			url:'/ratting/change_manage',
			success:function(result){
				if(result){
					if(result == 1){
						alert('评分已经超过普通用户可评分值，请下月再进行设定！');
					}else{
						$(obj).html(result);
						if(result == '取消管理'){
							location.reload();
							$(obj).parent().parent().css('color','blue');
						}else{
							location.reload();
							$(obj).parent().parent().css('color','');

						}
					}
					
				}
			}
			
		});		
	}
	
}
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
function form_submit(){
	$('#projectStoryForm').submit()
}
$(document).ready(function(){
	get_month();
});

$('.showbig').hover(function(){
	if($('.bigpicWrap').size()==0){
		var $this = $(this);
		var bigUrl = $this.data('big');
		var sign = $this.data('sign');
		var uname = $this.data('uname');
		var bigpicWrap = '<div class="bigpicWrap"><p>'+uname+'</p><p><img src="' + bigUrl + '"></p><p class="p1">个性签名</p><p><span>'+sign+'</span></p></div>';
		$this.before(bigpicWrap);
		var thisBigpicWrap = $this.prev('.bigpicWrap');
		var thisToLeft = $this.offset().left - $(document).scrollLeft(),
			thisTotoTop = $this.offset().top - $(document).scrollTop()
		thisBigpicWrap.css({
			left: thisToLeft + 100,
			top: thisTotoTop
		});
		if(thisBigpicWrap.offset().top - $(document).scrollTop() < 0){
			thisBigpicWrap.css('top', 0);
		}
		if(($(window).height()+$(document).scrollTop()) < (thisBigpicWrap.height()+thisBigpicWrap.offset().top)){
			thisBigpicWrap.css({
				top: 'auto',
				bottom: 0
			});
		}
	}
}, function(){
	$(this).prev('.bigpicWrap').remove();
});
</script>