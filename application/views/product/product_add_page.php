<?php
if(!$include_headfoot){
  echo <<<EOF
<link rel='stylesheet' type='text/css' href='/public/css/base_min.css' />
<link rel='stylesheet' type='text/css' href='/public/css/css.css' />
<script src='/public/js/jquery-1.8.3.min.js' type="text/javascript"></script>
<script src="/public/js/layer/layer.min.js"></script>
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
<style>
body {background-color:white}
</style>
EOF;
}
?>
<div id="wrap" <?php echo !$include_headfoot ? 'style="padding:0"' : '';?>>
  <div class="outer" <?php echo !$include_headfoot ? 'style="padding:0"' : '';?>>
    <form method="post" id="dataform" action="/product/save" enctype="multipart/form-data">
      <input type="hidden" name="product_id" value="<?php echo empty($product)?'':$product->id;?>"/>
      <table align="center" class="table-1 a-left">
        <caption>
        <div class="f-left"><strong>新增产品</strong></div>
        </caption>
        <tbody>
          <tr>
            <th class="rowhead">产品名称</th>
            <td><input type="text" name="name" id="name" value="<?php echo empty($product)?'':$product->name;?>" class="text-5 required">
              <span class="star"> * </span>
            </td>
          </tr>
          <tr>
            <th class="rowhead">产品代号</th>
            <td><input type="text" name="code" id="code" value="<?php echo empty($product)?'':$product->code;?>" class="text-5 required">
              <span class="star"> * </span>
            </td>
          </tr>
          <tr>
            <th class="rowhead">产品负责人</th>
<!--             <td><select name="PO" id="PO" class="text-2">
                  <?php foreach($verify_users as $val):?>
                    <option value="<?php echo $val->id;?>" <?php echo !empty($product) && $val->id == $product->PO?'selected':'';?>><?php echo $val->real_name;?></option>
                  <?php endforeach;?>
                </select>
            </td> -->
            <td><input name="PO_name" id="PO" type="text" class="text-2" value="<?php if(isset($product->PO)){echo $product->PO->real_name;}?>" onblur="checkname(this)" /><input type="hidden" name="PO" id="PO_id" value="<?php if(isset($product->PO)){echo $product->PO->id;}?>"/></td>
          </tr>
          <tr>
            <th class="rowhead">测试负责人</th>
<!--             <td><select name="QD" id="QD" class="text-2">
                  <?php foreach($verify_users as $val):?>
                    <option value="<?php echo $val->id;?>" <?php echo !empty($product) && $val->id == $product->QD?'selected':'';?>><?php echo $val->real_name;?></option>
                  <?php endforeach;?>
                </select>
            </td> -->
            <td><input name="QD_name" id="QD" type="text" class="text-2" value="<?php if(isset($product->QD)){echo $product->QD->real_name;}?>" onblur="checkname(this)" /><input type="hidden" name="QD" id="QD_id" value="<?php if(isset($product->QD)){echo $product->QD->id;}?>"/></td>
          </tr>
          <tr>
            <th class="rowhead">发布负责人</th>
<!--             <td><select name="RD" id="RD" class="text-2">
                  <?php foreach($verify_users as $val):?>
                    <option value="<?php echo $val->id;?>" <?php echo !empty($product) && $val->id == $product->RD?'selected':'';?>><?php echo $val->real_name;?></option>
                  <?php endforeach;?>
                </select>
            </td> -->
            <td><input name="RD_name" id="RD" type="text" class="text-2" value="<?php if(isset($product->RD)){echo $product->RD->real_name;}?>" onblur="checkname(this)" /><input type="hidden" name="RD" id="RD_id" value="<?php if(isset($product->RD)){echo $product->RD->id;}?>"/></td>
          </tr>
          <tr>
            <th class="rowhead">产品描述</th>
            <td>
              <textarea name="description" id="description" rows="6" class="editor required"><?php echo empty($product)?'':$product->description;?></textarea><span class="star"> * </span>
            </td>
          </tr>
          <tr>
              <th class="rowhead">访问控制</th>
              <td><input id="aclopen" <?php echo !empty($product) && $product->acl == 'open'?'checked="checked"':'checked="checked"';?> value="open" name="acl" type="radio">
                  <label for="aclopen">默认设置(有产品视图权限，即可访问)</label>
                  <br>
                  <input id="aclprivate" <?php echo !empty($product) && $product->acl == 'private'?'checked="checked"':'';?> value="private" name="acl" type="radio">
                  <label for="aclprivate">私有产品(只有项目团队成员才能访问)</label>
                  <br>
              </td>
          </tr>
          <tr>
            <td colspan="2" class="a-center"><input type="submit" id="submit" value="保存" class="button-s">
              <input type="reset" id="reset" value="重填" class="button-r">
              <input type="button" value=" 返回 " class="button-s" onclick="<?php echo empty($_SERVER['HTTP_REFERER'])?"hiproduct.back();":"window.location.href='{$_SERVER['HTTP_REFERER']}'";?>" class="button-act">
            </td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
  <?php
if($include_headfoot){
  echo <<<EOF
<div id="divider"></div>
EOF;
}
?>
</div>
<script type="text/javascript">
$("#PO").autocomplete("/real_namedata", {autoFill: true}).result(
  function(event, data, formatted){
    var get_data = data.toString();
    var index = get_data.lastIndexOf('-');
    var last_data = get_data.substring(0,index);
    var last_id = get_data.substr(index+1);
    $(this).val(last_data);
    var obj_id = $(this).attr('id');
    var hidden_id = obj_id+'_id';
    $("#"+hidden_id).val(last_id);
  }
);
$('#PO').bind("input.autocomplete", function(){ 
  $(this).trigger('keydown.autocomplete'); 
});
$("#QD").autocomplete("/real_namedata", {autoFill: true}).result(
  function(event, data, formatted){
    var get_data = data.toString();
    var index = get_data.lastIndexOf('-');
    var last_data = get_data.substring(0,index);
    var last_id = get_data.substr(index+1);
    $(this).val(last_data);
    var obj_id = $(this).attr('id');
    var hidden_id = obj_id+'_id';
    $("#"+hidden_id).val(last_id);
  }
);
$('#QD').bind("input.autocomplete", function(){ 
  $(this).trigger('keydown.autocomplete'); 
});
$("#RD").autocomplete("/real_namedata", {autoFill: true}).result(
  function(event, data, formatted){
    var get_data = data.toString();
    var index = get_data.lastIndexOf('-');
    var last_data = get_data.substring(0,index);
    var last_id = get_data.substr(index+1);
    $(this).val(last_data);
    var obj_id = $(this).attr('id');
    var hidden_id = obj_id+'_id';
    $("#"+hidden_id).val(last_id);
  }
);
$('#RD').bind("input.autocomplete", function(){ 
  $(this).trigger('keydown.autocomplete'); 
});
$("#dataform").validate({
  ignore: [],
  rules:{
    description: {required:true, minlength:20}
  },
  submitHandler:function(form){
    var subBths = $(form).find("input[type=submit]");
    subBths.attr("disabled", "");
    form.submit();
  }
});
</script>