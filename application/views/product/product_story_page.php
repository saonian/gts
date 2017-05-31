<link rel="stylesheet" type="text/css" href="/public/js/treeview/jquery.treeview.css"/>
<div id="wrap">
  <div class="outer" style="min-height: 206px;">
    <form id="productStoryForm" method="post">
    <table class="cont-lt1">
    <tbody>
      <tr valign="top">
        <td id="treebox" class="side" style="height:auto;">
          <div class="box-title"><?php echo empty($product)?'请先添加产品':$product->name;?></div>
          <div class="box-content" style="height:auto;">
            <?php echo $modules_tree;?>
            <div class="a-right" style="height:auto;">
            <a target="" href="/module">维护模块</a>
            </div>
            <div class="divider" style="height:100px;"></div>
          </div>
        </td>
        <td class="divider" style="width:10px;"></td>
        <td>
          <iframe src="/story?body=1&is_product=1&allproject=1" width="100%" height="490px" id="story_list" name="story_list"></iframe>
        </td>
      </tr>
  </tbody>
  </table>
</form>
<script src="/public/js/treeview/jquery.treeview.js" type="text/javascript"></script>
<script type="text/javascript">
$(".tree").treeview({ persist: "cookie", collapsed: true, unique: false});
$(".tree li a").click(function(){
  $(".tree li a").attr("style", "");
  $(this).attr("style", "color: blue;font-weight: bold");
});
</script>
</div>
  <div id="divider"></div>
  <div id="divider"></div>
</div>