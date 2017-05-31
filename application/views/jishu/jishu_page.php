<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dli'>
<html xmlns='http://www.w3.org/1999/xhtml'>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<?php 
$content= array('1'=>'网站','2'=>'系统');
$status= array('1'=>'离线','2'=>'上线');
?>
<div style = "text-align: center;">
	<div >
  			<div><h1>当日在线技术支持</h1></div>
            <table >
                <tbody>
                <?php foreach($list as $key => $val):?>
                        <tr><td><?php echo $content[$val['support_content']]?></td></tr>
                        <tr><td><?php echo $val['real_name']?>(<font color="blue"><?php echo $status[$val['duty_status']]?></font>)&nbsp;&nbsp;&nbsp;&nbsp;联系电话：<?php echo $val['phone']?></td></tr>
                        <tr><td>登陆日志：<?php echo $val['create_time']?></td></tr>
                        <tr><td>支持范围：<?php echo $val['support_range']?></td></tr>
                <?php endforeach;?>
                </tbody>
            <tr><td>投诉电话：钟启仁  18688970089</td></tr>
            <tr><td>遇到以下情况请及时投诉，支持电话无人接听、问题处理不及时等</td></tr>
            </table>
    </div>
</div>
</head>
</html>