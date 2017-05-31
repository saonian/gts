<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dli'>
<html xmlns='http://www.w3.org/1999/xhtml'>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<title><?php echo empty($pmsdata[$controller]['display'])?'':$pmsdata[$controller]['display'];?> - 技术部工作管理系统(GTS)</title>
<link rel='icon' href='/favicon.ico' type='image/x-icon' />
<link rel='shortcut icon' href='/favicon.ico' type='image/x-icon' />
<script src='/public/js/jquery-1.8.3.min.js' type="text/javascript"></script>
<script src="/public/js/layer/layer.min.js"></script>
<link rel="stylesheet" type="text/css" href="/public/css/jquery-ui.css" />
<link rel="stylesheet" type="text/css" href="/public/css/jquery-ui-timepicker-addon.min.css" />
<link rel="stylesheet" type="text/css" href="/public/css/datetimepicker.css" />
<link rel="stylesheet" type="text/css" href="/public/css/validate/validate.css" />
<link rel="stylesheet" type="text/css" href="/public/js/kindeditor/themes/default/default.css" />
<link rel="stylesheet" type="text/css" href="/public/js/autocomplete/jquery.autocomplete.css" />
<link rel='stylesheet' type='text/css' href='/public/css/base_min.css' />
<link rel='stylesheet' type='text/css' href='/public/css/css.css' />
<link rel="stylesheet" type="text/css" href="/public/js/syntaxhighlighter/styles/shCoreEclipse.css"/>
<link rel='stylesheet' type='text/css' href='/public/css/rating.css' />
<script src='/public/js/jquery.cookie.js' type="text/javascript"></script>
<script src="/public/js/syntaxhighlighter/scripts/shCore.js" type="text/javascript" ></script>
<script src="/public/js/syntaxhighlighter/scripts/shAutoloader.js" type="text/javascript" ></script>
<script src='/public/js/common.js' type="text/javascript"></script>
<script src="/public/js/jquery-ui-1.9.2-min.js" type='text/javascript' ></script>
<script src="/public/js/jquery.ui.datepicker-zh-CN.min.js" type='text/javascript' ></script>
<script src="/public/js/jquery-ui-sliderAccess.js" type='text/javascript' ></script>
<script src="/public/js/jquery-ui-timepicker-addon.min.js" type='text/javascript' ></script>
<script src="/public/js/jquery.validate.min.js" type='text/javascript' ></script>
<script src="/public/js/kindeditor/kindeditor-min.js" type='text/javascript' ></script>
<script src="/public/js/kindeditor/zh_CN.js" type='text/javascript' ></script>
<script src="/public/js/autocomplete/jquery.autocomplete.min.js"></script>


</head>
<body>
<div id='header'>
  <table class='cont' id='topbar'>
    <tr>
      <td class='w-p50'><span id='companyname'>GTS - 技术部工作管理系统</span></td>
      <?php $date = getdate();?>
      <td class='a-right'>今天是<?php echo $date['mon'];?>月<?php echo $date['mday'];?>日，<?php echo $_SESSION['userinfo']['real_name'];?> <a href='/logout' >退出</a> &nbsp;|&nbsp; <!-- <a href='/application/views/GTS.pptx' class='about'>关于</a> &nbsp;|&nbsp; --> <a href='javascript:alert("修改密码请找刘凯")'>修改密码</a></td>
    </tr>
  </table>
  <?php 
    $pmsdata = config_item('pms');
    $is_has_permission_story  = has_permission($pmsdata['story']['powers']['page']['value'], TRUE);
    $is_has_permission_task  = has_permission($pmsdata['task']['powers']['page']['value'], TRUE);
    $roles = config_item('ratting_role');
  ?>
  <table class='cont' id='navbar'>
    <tr>
      <td id='mainmenu'>
        <ul>
          <?php $index = 1;foreach ($menu as $key => $val):?>
      			<?php
      				$subcontros = isset($menu[$key]['children'])?array_keys($menu[$key]['children']):array();
                // if((isset($val['is_admin']) && $val['is_admin'] && $_SESSION['userinfo']['is_admin'] != '1')){
                //   continue;
                // }
              if(!has_permission($pmsdata['menu_show']['powers'][$key]['value'], TRUE)){
                continue;
              }
              // var_dump((in_array($controller, $subcontros) && !in_array($controller, array('statistics','overtime'))));
            ?>
            <li <?php echo ($controller == $key || stripos($_SERVER['REQUEST_URI'], $val['url']) !== FALSE || ((in_array($controller, $subcontros) && in_array($controller, array('statistics','overtime')) && $controller==$key) || (in_array($controller, $subcontros) && !in_array($controller, array('statistics','overtime')) && $key != 'product')))?'class="active"':'';?>><nobr>
            <?php if($key == 'project'){?>
              <a href='<?php if($is_has_permission_story){ echo $menu[$key]['children']['story']['url'];}else if($is_has_permission_task){ echo $menu[$key]['children']['task']['url'];}else{ echo $val['url'];}?>'><?php if($index == 1):?><span id="mainbg">&nbsp;</span><?php endif;?><?php echo $val['display'];?></a>
            <?php }else{?>
              <a href='<?php echo $val['url'];?>'><?php if($index == 1):?><span id="mainbg">&nbsp;</span><?php endif;?><?php echo $val['display'];?></a>
            <?php }?>
            </nobr></li>

          <?php $index++;endforeach;?>
          <!-- <li id='searchbox'>
            <input type='text' name='searchQuery' id='searchQuery' value='编号(ctrl+g)' onclick=this.value='' onkeydown='if(event.keyCode==13) shortcut()' class='w-80px' />
            <input type='button' id='objectSwitcher' onclick='shortcut()' />
          </li> -->
        </ul></td>
    </tr>
  </table>
  <div class="quickbtn"><a href="/overtime/add" class="q_overtime" title="申请加班">加班</a><a href="/story?order=id&sort=desc&reviewedbyme=1&allproject=1" class="q_story" title="我的需求">需求</a><a href="/task?openedbyme=1&order=status&sort=asc&allproject=1" class="q_task" title="我的任务">任务</a></div>
</div>