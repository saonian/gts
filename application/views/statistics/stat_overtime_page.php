<div id="wrap">
    <div class="outer">
        <form action="/statistics/exporting" id="exporting">
            <select onchange="switchStat(this.value)" name="stattype" id="stattype">
                <option value="my">我的月度加班统计</option>
                <option value="my_dpt">本部当月加班排名</option>
                <?php if(has_permission($pmsdata['statistics']['powers']['get_dpt_overtime_stat']['value'])):?>
                <option value="dpt">部门加班统计</option>
                <?php endif;?>
            </select>
            <?php if(has_permission($pmsdata['statistics']['powers']['exporting']['value'])):?>
            <div id="exportdiv">
                </br>
                导出：</br>
                <select name="overtimetype">
                    <option value="all">全部加班</option>
                    <option value="workday">工作日加班</option>
                    <option value="weekend">周末加班</option>
                </select>
                从 <input type="text" class="text-3 date" name="begindate"/>
                到 <input type="text" class="text-2 date" name="enddate"/>
                <input type="submit" value="导出"/>
            </div>
            <?php endif;?>
        </form>
        <div id="chart" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
    </div>
    <div id="divider"></div>
</div>
<script src="/public/js/highcharts/highcharts.js"></script>
<script src="/public/js/highcharts/modules/drilldown.js"></script>
<script src="/public/js/highcharts/modules/exporting.js"></script>
<script type="text/javascript">
$("#exporting").validate({
  ignore: [],
  rules:{
    description: {required:true, minlength:20}
  }
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
  title: {
      text: '我的月度加班统计'
  },
  subtitle: {
      text: '最近6个月的加班统计'
  },
  xAxis: {
      categories: []
  },
  yAxis: {
      min: 0,
      title: {
        text: '总加班小时'
      }
  },
  tooltip: {
      headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
      pointFormat: '<tr><td style="color:{series.color};padding:0">加班: </td>' +
          '<td style="padding:0"><b>{point.y:.1f} 小时</b></td></tr>',
      footerFormat: '</table>',
      shared: true,
      useHTML: true
  },
  plotOptions: {
      column: {
          pointPadding: 0.2,
          borderWidth: 0
      },
      series: {
          borderWidth: 0,
          dataLabels: {
              enabled: true,
              format: '{point.y}小时'
          }
      }
  }
};
function chart_init(type){
  if(type == 1){
    url = '/statistics/get_my_overtime_monthly_stat/';
  }else if(type == 2){
    url = '/statistics/get_dpt_overtime_stat/';
  }else if(type == 3){
    url = '/statistics/get_my_dpt_ranking/';
  }
  $.get(url, function(data){
    var chartData = [];
    var drilldownData = [];
    var name = '';
    $.each(data, function(i,d){
      switch(type){
        case 1:
          chartData.push({name:d.mon+"月", y:parseFloat(d.total)});
          name = '月度加班';
          break;
        case 2:
          chartData.push({name:d.dpt_name, y:parseFloat(d.total), drilldown:d.dpt_name});
          drilldownData.push({name:d.dpt_name, id:d.dpt_name, data:d.detail});
          name = '部门加班';
          break;
        case 3:
          chartData.push({name:d[0], y:parseFloat(d[1])});
          name = '本部门当月加班排名';
          break;
      }
      
    });
    if(chartData.length > 0){
      option.series = [{name: name, colorByPoint: true, data: chartData}]
    }else{
      var month = (new Date()).getMonth()+1;
      option.series = [{name: month+'月',y: 0}];
    }
    if(drilldownData.length > 0){
      option.drilldown = {series: drilldownData};
    }
    // console.log(chartData);
    // console.log(drilldownData);
    // console.log(option);
    var chart = new Highcharts.Chart(option);
    // chart.series[0].setData(chartData);
  }, 'json');
}

function switchStat(type){
  switch(type){
    case 'my':
      $("#exportdiv").show();
      option.series = [{name: '我的加班'}];
      option.title = {text: '我的月度加班统计'};
      option.subtitle = {text: '最近6个月的加班统计'};
      chart_init(1);
      break;
    case 'my_dpt':
      $("#exportdiv").hide();
      option.series = [{name: '加班排名'}];
      option.title = {text: '本部门当月加班排名'};
      option.subtitle = {text: ''};
      option.legend = {enabled: false},
      chart_init(3);
      break;
    case 'dpt':
      $("#exportdiv").show();
      option.series = [{name: '部门加班'}];
      option.title = {text: '部门加班'};
      option.subtitle = {text: '当月各部门加班汇总'};
      option.legend = {enabled: false},
      chart_init(2);
      break;
  }
}
switchStat('my');

if($("#stattype option").length == 0){
  $("#stattype").html("<option>你没有查看任何统计数据的权限</option>");
}
</script>