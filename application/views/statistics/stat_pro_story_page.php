<div id="wrap">
  <div class="outer">
    <form action="/statistics/pro_story" method="post">
    统计时间：<input type="text" value="<?php echo $begin_date;?>" class="text-2 datetime" name="begin_date"/>&nbsp;至&nbsp;<input type="text" value="<?php echo $end_date;?>" class="text-2 datetime" name="end_date"/>&nbsp;&nbsp;<input type="submit" value="查 询"/>
  </form>
    <br/><br/>
    <table class="datatable">
      <thead>
        <tr>
          <th><a href="javascript:">项目</a></th>
          <th><a href="javascript:">需求总数</a></th>
          <th><a href="javascript:">总计工时</a></th>
          <th><a href="javascript:">需求已完成数</a></th>
          <th><a href="javascript:">需求"好"占比</a></th>
          <th><a href="javascript:">需求"差"占比</a></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($result as $key => $val):?>
        <tr>
          <td><?php echo $key;?></td>
          <td><?php echo $val['story_total_count'];?></td>
          <td><?php echo $val['story_total_estimate'];?></td>
          <td><?php echo $val['story_finished_count'];?></td>
          <td><?php echo ($val['story_quality_good_rate']*100).'%';?></td>
          <td><?php echo ($val['story_quality_bad_rate']*100).'%';?></td>
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
$(".datatable tr th").toggle(function(){
  thi= $(this).index();
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