<?php 
$status= array('1'=>'待确认','2'=>'已确认','3'=>'驳回');
$sort = $this->input->get('sort');
$sort = (empty($sort) || $sort=='asc')?'desc':'asc';
$is_added= array('待统计','待统计');
?>
<div id="wrap">
  <div class="outer">
    <form id="projectStoryForm" method="get" action="/ratting/rattinglist">
      <table style="width:100%;" class="table-2">
        <tr>
          <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <label>部门:</label>
            <select id="department_id" name="department_id" class="text-2" onchange="form_submit();">
                <option value="" <?php if($params['department_id']==''){echo "selected";}?>>所有部门</option>
                <?php foreach($parent_department as $key=>$val){ ?>
                <option value="<?php echo $key;?>" <?php if($key==$params['department_id']){echo "selected";}?>><?php echo $val;?></option>
                <?php } ?>
            </select>&nbsp;
            <label>得分人:</label>
            <input id="rated_name" type="text" name="rated_name" class="text-2" value="<?php if(isset($Params['rated_name'])){echo $Params['rated_name'];}?>"/>&nbsp;&nbsp;&nbsp;&nbsp;
            <label>评分时间:</label>&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="text" name="begin" style="width:100px;" id="begin" class="date text-2" value="<?php if(isset($params['begin'])){echo $params['begin'];}?>">&nbsp;&nbsp;至&nbsp;&nbsp;
            <input type="text" name="end" style="width:100px;" id="end" class="date text-2" value="<?php if(isset($params['end'])){echo $params['end'];}?>">&nbsp;&nbsp;&nbsp;&nbsp;
            <select class="text-2" name="level">
                <option value="">评分级别</option>
                <option value="好" <?php if(isset($params['level']) && $params['level']=='好'){echo "selected='selected'";}?>>好</option>
                <!-- <option value="中" <?php if(isset($params['level']) && $params['level']=='中'){echo "selected='selected'";}?>>中</option> -->
                <option value="差" <?php if(isset($params['level']) && $params['level']=='差'){echo "selected='selected'";}?>>差</option>
            </select>&nbsp;&nbsp;&nbsp;&nbsp;
            <select class="text-2" name="status">
              <option value="">审核状态</option>
              <option value='1' <?php if(isset($params['status']) && $params['status']=='1'){echo "selected='selected'";}?>>待审核</option>
              <option value='2' <?php if(isset($params['status']) && $params['status']=='2'){echo "selected='selected'";}?>>已确认</option>
              <option value='3' <?php if(isset($params['status']) && $params['status']=='3'){echo "selected='selected'";}?>>驳回</option>
            </select>&nbsp;&nbsp;&nbsp;&nbsp;
            <!-- <select class="text-2" name="is_added">
              <option value="">统计状态</option>
              <option value='1' <?php if(isset($params['is_added']) && $params['is_added']=='1'){echo "selected='selected'";}?>>待统计</option>
              <option value='2' <?php if(isset($params['is_added']) && $params['is_added']=='2'){echo "selected='selected'";}?>>已统计</option>
            </select>&nbsp;&nbsp;&nbsp;&nbsp; -->
            <input type="submit" class="button-s" value="搜索" style="width:80px;"/>&nbsp;&nbsp;&nbsp;&nbsp;
            
          </td>
        </tr>
      </table>
    </form>
      <table class='table-1 fixed colored tablesorter datatable'>
        <thead>
            <tr class="colhead">
              <th class='w-id'> <div class='header'><a href='?order=addtime&sort=<?php echo $sort;?>' >序号</a> </div></th>
              <th class='w-user'>评分列表</a></th>
              <th width="50px">得分人</a></th>
              <th width="80px">评分时间</th>
              <th width="50px">评分</th>
              <th width="50px">评分级别</th>
              <th width="50px">评分事件</th>
              <th width="50px">审核状态</th>
              <th width="80px">审核时间</th>
              <!-- <th width="80px">得分统计状态</th> -->
              <th width="100px">操作</th>
            </tr>
        </thead>
        <tbody>
          <?php if(count($myrattinglist['list'])>0){?>
          <?php foreach ($myrattinglist['list'] as $key=>$data){?>
      <tr class='a-center <?php echo ($key%2==0)?'odd':'even';?>'>
        <td><?php echo $key + 1?></td>
        <td><?php echo $data['content']?></td>
        <td><?php echo $data['rated_name']?></td>
        <td><?php echo $data['addtime']?></td>
        <td><?php echo $data['grade']?></td>
        <td><?php echo $data['level']?></td>
        <td><span style="cursor:pointer;" onmouseover="over_f('<?php echo $data['rating_desc'];?>',this);"onmouseout="out_f();"><?php echo mb_substr($data['rating_desc'],0,10,'UTF-8');?></span></td>
        <td><?php echo $status[$data['status']];?><?php if($data['status'] == 3 && !empty($data['remark']) && $data['remark'] != 'null'){?><span style="cursor:pointer;" onmouseover="over_f('驳回原因：<?php echo $data['remark'] ?>',this);"onmouseout="out_f();"><?php echo "(驳回原因：".$data['remark'].")";?></span><?php }?></td>
        <td><?php echo $data['audit_time'];?></td>
        <!-- <td><?php echo $is_added[$data['is_added']];?></td> -->
        <td><?php if($data['status'] == 2){?><a href="/ratting/ratting_content_detail?id=<?php echo $data['id']?>">查看</a><?php }else{?><a href="/ratting/ratting_modify?id=<?php echo $data['id']?>">修改</a> | <a href="javascript:void(0);" name="<?php echo $data['id']?>" onclick="del(this);">删除</a><?php }?></td>
        </td>
      </tr>
      <?php }?>
      <?php }?>
        </tbody>
        <tfoot>
          <tr>
            <td colspan='8'>
              <div class="f-left">
                总共 <strong><?php echo $myrattinglist['total'];?></strong> 个记录 &nbsp;
                共 <strong><?php echo $myrattinglist['total_page'];?></strong> 页 &nbsp;
              </div>
              <div class="f-left"><?php echo $myrattinglist['page_html'];?></div>
            </td>
          </tr>
        </tfoot>
     </table>   
    
  </div>
  <div id="divider"></div>
</div>
<script type="text/javascript">
$("#rated_name").autocomplete("/real_namedata", {autoFill: true});
$('#rated_name').bind("input.autocomplete", function(){ 
  $(this).trigger('keydown.autocomplete'); 
});
function del(obj){
  var id = obj.name;
  if(id!=''){
    layer.confirm('你确定要删除该记录？',function(){
      $.ajax({
        type:"POST",
        data:{'id':id},
        dataType:"json",
        url:'/ratting/ratting_del',
        success:function(result){
          if(result.flag == 1){
                layer.alert('本月加分剩余:'+result.last_plus+'&nbsp;&nbsp;&nbsp;&nbsp;本月减分剩余:'+result.last_minus, 9,'删除成功');
            }else{
                layer.alert('本月加分剩余:'+result.last_plus+'&nbsp;&nbsp;&nbsp;&nbsp;本月减分剩余:'+result.last_minus, 8,'删除失败');
            }
          if(result.flag == 1){
            $(obj).parent().parent().remove();
          }
        }
      });

    });
  }
  
}
function over_f(val,obj){
  layer.tips(val, obj, {
    guide: 3,
        style: ['background-color:#0FA6D8; color:#fff', '#0FA6D8'],
        maxWidth:150
    });
}
function out_f(val,obj){
  $(".xubox_layer").remove();
}
function form_submit(){
  $('#projectStoryForm').submit();
}
</script>