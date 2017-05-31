<div id="wrap">
    <div class="outer">
        <form action="/ratting/rattingreport" id="rattingreport" method="GET">
            从 <input type="text" class="text-3 date"  value="<?php echo $params['begindate']?>" name="begindate" style="width:150px;" id="begindate"/>
            到 <input type="text" class="text-2 date" value="<?php echo $params['enddate']?>" name="enddate" id="enddate" style="width:150px;"/>
            &nbsp;&nbsp;<input type="submit" value="查询" class="button-s"/>
        </form>
        <div id="chart" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
    </div>
    <div id="divider"></div>
</div>
<script src="/public/js/highcharts/highcharts.js"></script>
<script src="/public/js/highcharts/modules/drilldown.js"></script>
<script src="/public/js/highcharts/modules/exporting.js"></script>
<script type="text/javascript">
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
      pointFormat: '<tr><td style="color:{series.color};padding:0">平均分: </td>' +
          '<td style="padding:0"><b>{point.y:.2f} 分</b></td></tr>',
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
              format: '{point.y}分'
          },
          cursor:'pointer',
          events:{
            click:function(e){
                var begindate = $.trim($("#begindate").val());
                var enddate = $.trim($("#enddate").val());
                window.location.href='/ratting/dept_rattingreport/?department_id='+e.point.department_id+'&begindate='+begindate+'&enddate='+enddate;
            }
          }
      }
  }
};
function chart_init(type,data){
  //console.log(data);exit;
    var chartData = [];
    var drilldownData = [];
    var name = '';
    $.each(data, function(i,d){
      switch(type){
        case 1:
          chartData.push({name:d.deptname, y:parseFloat(d.grades),department_id:d.id});
          name = '部门平均分';
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
      option.series= [{name: name, colorByPoint: true, data: chartData}]
    }else{
      var month = (new Date()).getMonth()+1;
      option.series = [{name: month+'fen',y: 0}];
    }
    if(drilldownData.length > 0){
      option.drilldown = {series: drilldownData};
    }
    // console.log(chartData);
    // console.log(drilldownData);
    // console.log(option);
    var chart = new Highcharts.Chart(option);
    // chart.series[0].setData(chartData);
  // });
}

function switchStat(type,data){
  switch(type){
    case 'my':
    	option.series = [{name: '部门平均分'}];
    	option.title = {text: '部门平均分统计'};
    	chart_init(1,data);
    	break;
  }
}
switchStat('my',<?php echo json_encode($results) ?>);


</script>