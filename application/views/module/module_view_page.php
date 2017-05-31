<link rel="stylesheet" type="text/css" href="/public/js/treeview/jquery.treeview.css"/>
<div id="wrap">
  <div class="outer" style="min-height: 449px;">
    <table class="cont-lt5">
      <tbody><tr valign="top">
        <td class="side">
          <form action="/module/save" method="post">
            <table class="table-1">
              <caption><?php echo $product->name?>::维护产品视图模块</caption>
              <tbody><tr>
                <td>
                  <div id="main"><?php echo $modules_tree;?></div>
                  <div class="a-center">
                    <input type="submit" class="button-s" value="更新排序" id="submit" <?php echo empty($modules_tree)?'style="display:none"':'';?>>               </div>
                  </td>
                </tr>
              </tbody></table>
            </form>
          </td>
          <td class="divider" style="width:10px;"></td>
          <td>
            <form action="/module/save" method="post">
              <input type="hidden" name="parent_id" value="<?php echo $parent_id?>"/>
              <table align="center" class="table-1">
                <caption>维护子模块</caption>
                <tbody><tr>
                  <td width="10%">
                    <nobr>
                    <a href="/module"><?php echo $product->name?></a>
                    &nbsp;<span class="icon-angle-right"></span>
                    </nobr>
                  </td>
                  <td id="moduleBox">
                    父模块：
                    <select id="productModule" name="parent">
                      <option value="0">/</option>
                      <?php echo $modules_select;?>
                    </select>
                    <div id="sonModule">
                    <?php foreach($current_sub_modules as $val):?>
                    <span><input type="text" style="margin-bottom:5px" class="text-3" value="<?php echo $val->name?>" id="modules[id<?php echo $val->id?>]" name="modules[id<?php echo $val->id?>]"><br></span><br>
                    <?php endforeach;?>
                    <span><input type="text" style="margin-bottom:5px" class="text-3" value="" id="modules[]" name="modules[]"><br></span><br>
                    <span><input type="text" style="margin-bottom:5px" class="text-3" value="" id="modules[]" name="modules[]"><br></span><br>
                    <span><input type="text" style="margin-bottom:5px" class="text-3" value="" id="modules[]" name="modules[]"><br></span><br>
                    <span><input type="text" style="margin-bottom:5px" class="text-3" value="" id="modules[]" name="modules[]"><br></span><br>
                    <span><input type="text" style="margin-bottom:5px" class="text-3" value="" id="modules[]" name="modules[]"><br></span><br>
                    <span><input type="text" style="margin-bottom:5px" class="text-3" value="" id="modules[]" name="modules[]"><br></span><br>
                    <br></span>
                  </div>
                </td>
              </tr>
              <tr>
                <td></td>
                <td colspan="2">
                  <input type="submit" class="button-s" value="保存" id="submit">
                  <input type="button" class="button-s" value="返回" onclick="javascript:history.go(-1);">
                </td>
              </tr>
            </tbody></table>
          </form>
        </td>
      </tr>
    </tbody></table>
  </div>
</div>
<script src="/public/js/treeview/jquery.treeview.js" type="text/javascript"></script>
<script type="text/javascript">
$(".tree").treeview({ persist: "cookie", collapsed: true, unique: false});

$(".iframe").click(function(){
  var url = $(this).data('url');
  $.layer({
      type: 2,
      border: [0],
      title: false,
      shadeClose: true,
      closeBtn: true,
      iframe: {src : url},
      area: ['500px', '170px']
  });
});
</script>