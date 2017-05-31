<link rel='stylesheet' type='text/css' href='/public/css/base_min.css' />
<link rel='stylesheet' type='text/css' href='/public/css/css.css' />
<body style="background-color: white">
<form method="post" action="/module/save">
  <input type="hidden" name="module_id" value="<?php echo $cur_module->id;?>"/>
  <table class="table-1" style="border:none"> 
    <caption>编辑</caption>
        <tbody><tr>
      <th class="rowhead">上级模块</th>
      <td>
      	<select class="select-1" id="parent_id" name="parent_id">
		  <option selected="selected" value="0">/</option>
		  <?php foreach($modules as $val):?>
		  	<option value="<?php echo $val->id;?>" <?php echo $cur_module->parent == $val->id ? 'selected':'';?>><?php echo $val->name;?></option>
		  <?php endforeach;?>
		</select>
	  </td>
    </tr>
    <tr>
      <th class="rowhead">模块名称</th>
      <td>
      	<input type="text" class="text-1" value="<?php echo $cur_module->name;?>" id="name" name="name">
	  </td>
    </tr>
    <tr>
      <td class="a-center" colspan="2">
        <input type="submit" class="button-s" value="保存" id="submit">
      </td>
    </tr>
  </tbody>
</table>
</form>
</body>