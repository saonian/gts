<?php
$roles = config_item('ratting_role');
?>
<div id="wrap">
    <div class="outer">
    &nbsp;&nbsp;
        <?php if(!in_array($_SESSION['userinfo']['role']->id,$roles)){?>
        <span>本月加分剩余：<?php echo $last_grade['last_plus']?></span>&nbsp;&nbsp;&nbsp;&nbsp;
        <span>本月减分剩余：<?php echo $last_grade['last_minus']?></span>
        <?php }?>
        <form action="/ratting/personal_grade" id="mygrade" method="GET" >
        <?php if(in_array($_SESSION['userinfo']['role']->id,$roles)){?>
        	真实姓名 &nbsp;<input type="text" class="text-2" name="user" id="user" value="<?php echo $params['user'];?>"/>
        <?php }?>
            &nbsp;&nbsp;
            从 <input type="text" class="text-3 date"  value="<?php echo $params['begindate']?>" name="begindate" style="width:150px;" id="begindate"/>
            到 <input type="text" class="text-2 date" value="<?php echo $params['enddate']?>" name="enddate" id="enddate" style="width:150px;"/>

            &nbsp;&nbsp;<input type="submit" value="查询" class="button-s"/>&nbsp;&nbsp;&nbsp;
        </form>
        <div id="chart" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
    </div>
    <div id="divider"></div>
</div>
<input type="hidden" class="text-2" id="real_name" value="<?php echo $userinfo['real_name'];?>"/>
<input type="hidden" class="text-2" id="uid" value="<?php echo $userinfo['id'];?>"/>
<script src="/public/js/highcharts/highcharts.js"></script>
<script src="/public/js/highcharts/modules/drilldown.js"></script>
<script src="/public/js/highcharts/modules/exporting.js"></script>
<script type="text/javascript">
$("#user").autocomplete("/namedata", {autoFill: true});
$('#user').bind("input.autocomplete", function(){ 
  $(this).trigger('keydown.autocomplete'); 
});
Highcharts.setOptions({
  lang: {
    printChart: '打印',
    downloadJPEG: '导出为JPEG图片',
    downloadPDF: '导出为PDF文件',
    downloadPNG: '导出为PNG图片',
    downloadSVG: '导出为SVG图片'
  }
});
var option = {
  chart: {
      type: 'column',
      renderTo: 'chart'

  },
  xAxis: {
      categories: []
  },
  yAxis: {
     // min: 0,
      title: {
        text: ''
      }
  },
  tooltip: {
      headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
      pointFormat: '<tr><td style="color:{series.color};padding:0">得分: </td>' +
          '<td style="padding:0"><b>{point.y:.1f} 分</b></td></tr>',
      footerFormat: '</table>',
      shared: true,
      useHTML: true
  },
  plotOptions: {
      column: {
          pointPadding: 0.2,
          borderWidth: 0,
          pointWidth: 60
      },
      series: {
          borderWidth: 0,
          dataLabels: {
              enabled: true,
              format: '{point.y}分'
          },
          cursor:'pointer',
          events:{
            click:function(e){
                var uid = $.trim($("#uid").val());
                window.location.href='/ratting/rat_detail/?uid='+uid+'&year='+e.point.year+'&month='+e.point.month;
            }
          }
      }
  }
};
function chart_init(type,data){
    var chartData = [];
    var name = '';
    $.each(data, function(i,d){
      switch(type){
        case 1:
          chartData.push({name:d.years+'年'+d.mon+'月', y:parseFloat(d.grades),year:d.years,month:d.mon});
          name = '得分统计';
          break;
      }
      
    });
    if(chartData.length > 0){
      option.series= [{name: name, colorByPoint: true, data: chartData}]
    }else{
      var month = (new Date()).getMonth()+1;
      option.series = [{name: month+'月',y: 0}];
    }
    var chart = new Highcharts.Chart(option);
    
}

function switchStat(type,data){
    var user = $.trim($("#real_name").val());
    switch(type){
        case 'my':
        	option.series = [{name: ''}];
        	option.title = {text: user+'得分统计'};
        	chart_init(1,data);
        	break;
    }
}
switchStat('my',<?php echo json_encode($results) ?>);


</script>