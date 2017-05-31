<div id='wrap'>
  <div class='outer'>
    <form id="dataform" method="post" action="/account/save">
      <?php if(!empty($user_info)):?>
      <input type="hidden" name="user_id" value="user_id"/>
      <?php endif;?>
      <table align="center" class="table-1 a-left">
        <caption class="caption-tl pb-10px">
          <div class="f-left"><strong><?php echo empty($user_info)?'新增用户':'编辑用户'?></strong></div>
        </caption>
        <tbody>
          <tr>
            <th class="rowhead">用户名:</th>
            <td>
              <input class="text-3 required" type="text" name="account" value="<?php echo $user_info->account;?>"/>
            </td>
          </tr>
          <tr>
            <th class="rowhead">密码:</th>
            <td>
              <input class="text-3 <?php if(empty($user_info)):?>required<?php endif;?>" type="password" name="password" value=""/><?php if(!empty($user_info)):?><font color="red">(如果是修改密码这里直接写入新密码，否则留空)</font><?php endif;?>
            </td>
          </tr>
          <tr>
            <th class="rowhead">真实姓名:</th>
            <td>
              <input class="text-3 required" type="text" name="real_name" value="<?php echo $user_info->real_name;?>"/>
            </td>
          </tr>
          <tr>
            <th class="rowhead">选择部门:</th> 
            <td>
               <select name="department_id" id="department_id" class="text-2">
          <option value="">请选择</option>
          <?php foreach($department_list as $key=>$val){ ?>
            <option value="<?php echo $key;?>" <?php if($key==$user_info->department_id){echo "selected";}?>><?php echo $val;?></option>
          <?php } ?>
        </select>
        <span class="star"> * </span>
            </td>
          </tr>
          <tr>
            <th class="rowhead">选择角色:</th> 
            <td>
        <select name="role_id" id="role_id" class="text-2 required">
          <option value="">请选择</option>
          <?php foreach($get_role_list as $key=>$val){ ?>
            <option value="<?php echo $val['id']?>" <?php if(isset($role_id) && ($val['id']==$role_id)){echo "selected";}?>><?php echo $val['name']?></option>
          <?php } ?>
        </select>
        <span class="star"> * </span>
            </td>
          </tr>
      <tr>
            <th class="rowhead">邮箱:</th> 
            <td>
        <input id="email" name="email" type="email" value="<?php echo $user_info->email;?>" class="text-3 required"/>
        <span class="star"> * </span>
            </td>
          </tr>
          <tr>
            <td class="a-left" colspan="2">
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <input type="hidden" name="user_id" value="<?php echo $user_info->id;?>">
        <input type="submit" class="button-s" value="保存" id="submit">&nbsp;&nbsp;&nbsp;&nbsp;
        <input type="button" class="button-r" value="返回" id="fanhui" onclick="window.location.href='/account/index'">
       </td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
  <div id='divider'></div>
</div>
<script type="text/javascript">
$("#dataform").validate({
  ignore: [],
  rules:{
    description: {required:true, minlength:20}
  }
});
</script>
