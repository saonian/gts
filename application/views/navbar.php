<?php
$roles = config_item('ratting_role');
?>
<table id="navbar" class="cont">
  <tbody><tr>
    <td id="modulemenu">
      <ul>
        <?php if($controller!='ratting'){?>
        <li>
          <?php if(in_array($controller, array('product','module'))):?>
            <select onchange="switchProduct($('#productID').val());" id="productID" name="productID" class="text-3">
              <?php foreach ($all_product as $key => $val):?>
                  <option value="<?php echo $val->id;?>" <?php echo $current_product_id == $val->id?'selected':'';?>><?php echo $val->name;?><?php echo empty($pmsdata['product']['status'][$val->status])?'':'('.$pmsdata['product']['status'][$val->status]['display'].')';?></option>
              <?php endforeach;?>
            </select>
          <?php else:?>
            <select onchange="switchProject($('#projectID').val());" id="projectID" name="projectID" class="text-3">
              <?php foreach ($all_project as $key => $val):?>
                  <option value="<?php echo $val->id;?>" <?php echo $current_project_id == $val->id?'selected':'';?>><?php echo $val->name;?><?php echo empty($pmsdata['project']['status'][$val->status])?'':'('.$pmsdata['project']['status'][$val->status]['display'].')';?></option>
              <?php endforeach;?>
            </select>
          <?php endif;?>
        </li>
        <?php } ?>
        <?php 
        $current_controller = $controller;
        if(in_array($controller, array_keys($menu['product']['children']))){
            $current_controller = 'product';
        }
        if(in_array($controller, array_keys($menu['project']['children']))){
            $current_controller = 'project';
        }
        if(in_array($controller, array_keys($menu['sys']['children']))){
            $current_controller = 'sys';
        }
        if($controller == 'grade'){
          $controller = ($method == 'index'?'gradestorylist':$method);
          if($controller == 'setting'){
            $controller = 'setlist';
          }else if($controller == 'taskview'){
            $controller = 'gradetasklist';
          }else if($controller == 'storyview'){
            $controller = 'gradestorylist';
          }else if($controller == 'adminview'){
            $controller = 'gradeadmin';
          }
        }
        if($controller == 'statistics'){
          $controller = ($method == 'index'?'overtime':$method);
        }
        if($controller == 'project'){
          $controller = $method;
        }
        if($controller == 'ratting'){
          $controller = $method;
          if($controller=='dept_rattingreport'){
            $controller = 'rattingreport';
          }
          
          if($controller=='rat_detail' || $controller=='rat_detail_by_content' || $controller =='ratting_content_detail'){
            $controller = 'personal_grade';
          }
          if($controller=='rat_index'){
            $controller = 'userlist';
          }
          if($controller=='ratting_modify'){
            $controller = 'rattinglist';
          }
        }
        if($controller == 'product'){
          $controller = ($method == 'story'?'story':$method);
        }
        if(isset($menu[$current_controller]['children'])):
        ?>
        <?php foreach ($menu[$current_controller]['children'] as $key => $val):?>
          <?php
            if(isset($val['is_admin']) && $val['is_admin'] && $_SESSION['userinfo']['is_admin'] != '1'){
              continue;
            }
            if(isset($val['is_manage']) && $val['is_manage'] && !in_array($_SESSION['userinfo']['role']->id,$roles)){
               continue;
             }
          ?>
            <li <?php echo $controller == $key?'class="active"':'';?>>
              <?php 
              $sorder = isset($_COOKIE['story_order'])?$_COOKIE['story_order']:'status';
              $ssort = isset($_COOKIE['story_sort'])?$_COOKIE['story_sort']:'asc';
              $torder = isset($_COOKIE['task_order'])?$_COOKIE['task_order']:'status';
              $tsort = isset($_COOKIE['task_sort'])?$_COOKIE['task_sort']:'asc';
              if($current_controller == 'project' && $key == 'story'){
                $val['url'] .= "&order={$sorder}&sort={$ssort}";
              }else if($current_controller == 'project' && $key == 'task'){
                $val['url'] .= "&order={$torder}&sort={$tsort}";
              }
              ?>
              <a href="<?php echo $val['url'];?>"><?php echo $val['display'];?></a> 
            </li>
        <?php endforeach;?>
        <?php endif;?>
      </ul></td>
  </tr>
</tbody></table>