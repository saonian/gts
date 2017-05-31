<div id="wrap">
  <div class="outer">
    <form method="post" id="dataform" action="/role/save">
      <?php if(!empty($role)):?>
      <input type="hidden" name="id" value="<?php echo $role->id;?>"/>
      <?php endif;?>
      <table align="center" class="table-1 a-left">
        <caption>
        <div class="f-left"><strong><?php echo !empty($role)?'编辑':'新增';?>角色</strong></div>
        </caption>
        <tbody>
          <tr>
            <th class="rowhead">角色名</th>
            <td><input type="text" name="name" value="<?php echo empty($role)?'':$role->name;?>" class="text-3"></td>
          </tr>
          <tr>
            <th class="rowhead">角色描述</th>
            <td><textarea name="description" class="editor"><?php echo empty($role)?'':$role->description;?></textarea></td>
          </tr>
          <tr>
            <td colspan="2" class="a-center">
              <input type="button" value="全选" id="allchecker" checkboxname="powers[]"/>
              <input type="button" value="反选" id="reversechecker" checkboxname="powers[]"/>
            </td>
          </tr>
          <tr>
            <th class="rowhead">权限</th>
            <td>
              <ul>
              <?php foreach ($pmsdata as $key => $val):?>
              <?php 
                if(empty($val['powers'])){
                  continue;
                }
              ?>
              <li style="width:40%; padding-right: 10px; float:left;">
              <table class="table-1 fixed colored tablesorter datatable">
                <caption class="caption-tl pb-10px">
                <div class="f-left"><strong><?php echo $val['display'].'权限';?></strong></div>
                </caption>
                <thead>
                  <tr class="colhead">
                    <th class=""> <div class="header"><a href="#">权限</a> </div></th>
                    <th class=""> <div class="header"><a href="#">描述</a> </div></th>
                    <th class=""> <div class="header"><a href="#">选择</a> </div></th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach($val['powers'] as $k => $v):?>
                  <tr class="a-center odd">
                    <td><?php echo $v['value'];?></td>
                    <td><?php echo $v['display'];?></td>
                    <td><input type="checkbox" name="powers[]" value="<?php echo $v['value'];?>" <?php echo in_array($v['value'], $powers)?'checked':'';?>/></td>
                  </tr>
                  <?php endforeach;?>
                </tbody>
              </table>
              </li>
              <?php endforeach;?>
              </ul>
            </td>
          </tr>
          <tr>
            <td colspan="2" class="a-center"><input type="submit" id="submit" value="保存" class="button-s">
              <input type="reset" id="reset" value="重填" class="button-r"></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
  <div id="divider"></div>
</div>