<div id="wrap">
  <div class="outer">
    <form action="/statistics/work" method="post">
    统计时间：<input type="text" value="<?php echo $begin_date;?>" class="text-2 datetime" name="begin_date"/>&nbsp;至&nbsp;<input type="text" value="<?php echo $end_date;?>" class="text-2 datetime" name="end_date"/>&nbsp;&nbsp;<input type="submit" value="查 询"/>
  </form>
    <br/><br/>
    <table class="datatable">
      <thead>
        <tr>
          <th rowspan="2">姓名</th>
          <th colspan="5">任务数统计</th>
          <th colspan="5">工时统计</th>
          <th colspan="2">任务评分统计</th>
          <th colspan="3">需求评分统计</th>
          <th colspan="2">任务时效统计</th>
        </tr>
        <tr>
          <th><a href="javascript:">任务数<a></th>
          <th><a href="javascript:">未开始<a></th>
          <th><a href="javascript:">开发中<a></th>
          <th><a href="javascript:">已完成<a></th>
          <th><a href="javascript:">完成率<a></th>
          <th><a href="javascript:">计划工时<a></th>
          <th><a href="javascript:">已完成<a></th>
          <th><a href="javascript:">计分工时<a></th>
          <th><a href="javascript:">有效率<a></th>
          <th><a href="javascript:">节约率<a></th>
          <th><a href="javascript:">好评率<a></th>
          <th><a href="javascript:">差评率<a></th>
          <th><a href="javascript:">好评率<a></th>
          <th><a href="javascript:">中评率<a></th>
          <th><a href="javascript:">差评率<a></th>
          <th><a href="javascript:">提前完成率<a></th>
          <th><a href="javascript:">延迟完成率<a></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($result as $key => $val):?>
        <tr>
          <td><?php echo $val->real_name;?></td>
          <td><?php echo $val->task_count['total'];?></td>
          <td><?php echo $val->task_count['wait'];?></td>
          <td><?php echo $val->task_count['doing'];?></td>
          <td><?php echo $val->task_count['finished'];?></td>
          <td><?php echo ($val->task_count['finish_rate']*100).'%';?></td>
          <td><?php echo $val->work_hour['estimate'];?></td>
          <td><?php echo $val->work_hour['consumed'];?></td>
          <td><?php echo $val->work_hour['score'];?></td>
          <td><?php echo ($val->work_hour['effect_rate']*100).'%';?></td>
          <td><?php echo ($val->work_hour['save_rate']*100).'%';?></td>
          <td><?php echo $is_admin? '<a href="javascript:" data=\''.json_encode($val->task_grade['grade_good_task']).'\' title="好评任务" class="popiframe">'.($val->task_grade['good_rate']*100).'%</a>' : ($val->task_grade['good_rate']*100).'%';?></td>
          <td><?php echo $is_admin? '<a href="javascript:" data=\''.json_encode($val->task_grade['grade_bad_task']).'\' title="差评任务" class="popiframe">'.($val->task_grade['bad_rate']*100).'%</a>' : ($val->task_grade['bad_rate']*100).'%';?></td>
          <td><?php echo $is_admin? '<a href="javascript:" data=\''.json_encode($val->story_grade['grade_good_story']).'\' title="好评需求" class="popiframe">'.($val->story_grade['good_rate']*100).'%</a>' : ($val->story_grade['good_rate']*100).'%';?></td>
          <td><?php echo $is_admin? '<a href="javascript:" data=\''.json_encode($val->story_grade['grade_medium_story']).'\' title="中评需求" class="popiframe">'.($val->story_grade['medium_rate']*100).'%</a>' : ($val->story_grade['medium_rate']*100).'%';?></td>
          <td><?php echo $is_admin? '<a href="javascript:" data=\''.json_encode($val->story_grade['grade_bad_story']).'\' title="差评需求" class="popiframe">'.($val->story_grade['bad_rate']*100).'%</a>' : ($val->story_grade['bad_rate']*100).'%';?></td>
          <td><?php echo ($val->task_aging['ahead_rate']*100).'%';?></td>
          <td><?php echo ($val->task_aging['delay_rate']*100).'%';?></td>
        </tr>
        <?php endforeach;?>
      </tbody>
    </table>
  </div>
  <div id="divider"></div>
</div>
<script type="text/javascript">
// 弹出层
$(".popiframe").live('click', function(){
    var title = $(this).attr("title");
    var src = $(this).attr("href");
    var data = $.parseJSON($(this).attr("data"));
    var html = '<table style="text-align:center"><tr><td>序号</td><td>'+title+'名称</td><td>评价人</td><td>操作</td></tr>';
    for(i in data){
      var url = '/grade/adminview/'+data[i].grade_id+'/'+data[i].type;
      html += '<tr><td>'+(parseInt(i)+1)+'</td><td>'+data[i].name+'</td><td>'+data[i].grade_by+'</td><td><a target="_blank" href="'+url+'">查看</a></td></tr>';
    }
    html += '</table>';
    if(data.length == 0){
      alert("没有数据");
      return false;
    }
    var i = $.layer({
      type: 1,
      title: false,
      border : [5, 0.5, '#666', true],
      offset:['20px' , ''],
      area: ['600','auto'],
      page: {
        html: html
      }
    });
    return false;
});

// 排序
// 存入点击列的每一个TD的内容；
var aTdCont = [];
//点击列的索引值
var thi = 0
//重新对TR进行排序
var setTrIndex = function(tdIndex){
  $(".datatable tbody").empty();
  for(i=0;i<aTdCont.length;i++){
    $(".datatable tbody").append(aTdCont[i]);
  }
}
//比较函数的参数函数
var compare_down = function(a,b){
    var x = $(a).children("td:eq("+thi+")").text();
    if(x.indexOf('%') >= 0){
      x = x.substring(0, x.indexOf('%'));
    }
    var y = $(b).children("td:eq("+thi+")").text();
    if(y.indexOf('%') >= 0){
      y = y.substring(0, y.indexOf('%'));
    }
    return x-y;
}
var compare_up = function(a,b){
    var x = $(a).children("td:eq("+thi+")").text();
    if(x.indexOf('%') >= 0){
      x = x.substring(0, x.indexOf('%'));
    }
    var y = $(b).children("td:eq("+thi+")").text();
    if(y.indexOf('%') >= 0){
      y = y.substring(0, y.indexOf('%'));
    }
    return y-x;
}
//比较函数
var fSort = function(compare){
  aTdCont.sort(compare);
}
//取出TD的值
var fSetTdCont = function(thIndex){
    $(".datatable tbody tr").each(function() {
      aTdCont.push($(this));
    });
}
//点击时需要执行的函数
var clickFun = function(thindex){
  aTdCont = [];
  //获取点击当前列的索引值
  var nThCount = thindex;
  //调用sortTh函数 取出要比较的数据
  fSetTdCont(nThCount);
}
//非合并列点击事件
$(".datatable thead tr:eq(1) th").toggle(function(){
  thi= $(this).index()+1;
  clickFun(thi);
  //调用比较函数,降序
  fSort(compare_up);
  //重新排序行
  setTrIndex(thi);
},function(){
  clickFun(thi);
  //调用比较函数 升序
  fSort(compare_down);
  //重新排序行
  setTrIndex(thi);
});
</script>