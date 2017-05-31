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
    <div id="titlebar">
      <div id="main">PRODUCT #<?php echo $product->id;?> <?php echo $product->name;?></div>
    </div>
    <table class="cont-rt5">
      <tbody>
        <tr valign="top">
          <td><fieldset>
              <legend>产品描述</legend>
              <div class="content">
                <?php echo $product->description;?>
              </div>
            </fieldset>
            <div id="actionbox">
              <fieldset>
                <legend> 历史记录 </legend>
                <ol id="hiproductItem">
                  <?php foreach ($product->actions as $key => $val): ?>
                  <li value="<?php echo ++$key.'.';?>"> <span> <?php echo $val->date;?>, 由 <strong><?php echo $val->actor;?></strong> <?php echo $pmsdata[$val->type]['action'][$val->action]['display']?>。<span onclick="switchChange(this)" class="hand change-show"></span></span>
                    <?php if(!empty($val->history)):?>
                    <div id="changeBox5" class="changes" style="display: none;">
                      <?php foreach ($val->history as $key => $val): ?>
                            <?php if(empty($val->diff)):?>
                            修改了 <strong><i><?php echo $val->field;?></i></strong>，旧值为 "<?php echo $val->old;?>"，新值为 "<?php echo $val->new;?>"。<br>
                          <?php else:?>
                            修改了 <strong><i><?php echo $val->field;?></i></strong>，区别为 <blockquote><?php echo $val->diff;?></blockquote><br>
                          <?php endif;?>
                      <?php endforeach;?>
                    </div>
                    <?php endif;?>
                    <?php if(!empty($val->comment)):?>
                    <div class="comment149" style="display: block;"><?php echo $val->comment;?></div>
                    <?php endif;?>
                  </li>
                  <?php endforeach;?>
                </ol>
              </fieldset>
            </div>
            <div class="a-center actionlink">
              <?php if(has_permission($pmsdata['product']['powers']['close']['value']) && $product->status != $pmsdata['product']['status']['closed']['value']):?>
              <input type="button" value=" 关闭 " onclick="<?php echo $product->can_close?"window.location.href='/product/close/{$product->id}?body='.$body":"alert('产品下还有需求没有关闭，所有需求都关闭的情况下才能关闭产品！');";?>" class="button-act">
              <?php endif;?>
              <?php if(has_permission($pmsdata['product']['powers']['edit']['value']) && $product->status != $pmsdata['product']['status']['closed']['value']):?>
              <input type="button" value=" 编辑 " onclick="window.location.href='/product/edit/<?php echo $product->id;?>?body=<?php echo $body;?>'" class="button-act">
              <?php endif;?>
              <input type="button" value=" 备注 " onclick="setComment();" class="button-act">
              <input type="button" value=" 返回 " onclick="<?php echo empty($_SERVER['HTTP_REFERER'])?"history.back();":"window.location.href='{$_SERVER['HTTP_REFERER']}'";?>" class="button-act">
            </div>
            <div id="commentBox" style="display: none;">
              <fieldset>
                <legend>备注</legend>
                <form action="/product/save" method="post">
                  <input type="hidden" name="product_id" value="<?php echo $product->id;?>" />
                  <table align="center" class="table-1">
                  <tbody><tr><td><textarea class="w-p100 editor" rows="5" id="comment" name="comment"></textarea>
                  </td></tr>
                  <tr><td> <input type="submit" class="button-s" value="保存" id="submit"> <input type="button" class="button-s" value="返回" onclick="<?php echo empty($_SERVER['HTTP_REFERER'])?"history.back();":"window.location.href='{$_SERVER['HTTP_REFERER']}'";?>"></td></tr>
                  </tbody></table>
                </form>
              </fieldset>
            </div>
          </td>
          <td class="divider"></td>
          <td class="side"><fieldset>
              <legend>基本信息</legend>
              <table class="table-1">
                <tbody>
                  <tr>
                    <td class="rowhead">产品名称</td>
                    <td><?php echo $product->name;?></td>
                  </tr>
                  <tr>
                    <td class="rowhead">产品代号</td>
                    <td><?php echo $product->code;?></td>
                  </tr>
                  <tr>
                    <td class="rowhead">产品负责人</td>
                    <td><?php echo empty($product->PO)?'':$product->PO->real_name;?></td>
                  </tr>
                  <tr>
                    <td class="rowhead">测试负责人</td>
                    <td><?php echo empty($product->QD)?'':$product->QD->real_name;?></td>
                  </tr>
                  <tr>
                    <td class="rowhead">发布负责人</td>
                    <td><?php echo empty($product->RD)?'':$product->RD->real_name;?></td>
                  </tr>
                  <tr>
                    <td class="rowhead">状态</td>
                    <td><?php echo $pmsdata['product']['status'][$product->status]['display'];?></td>
                  </tr>
                  <tr>
                    <td class="rowhead">访问控制</td>
                    <td><?php echo $product->acl == 'open'?'默认设置(有产品视图权限，即可访问)':'私有产品(只有项目团队成员才能访问) ';?></td>
                  </tr>
                  <tr>
                    <td class="rowhead">创建人</td>
                    <td><?php echo $product->created_by->real_name;?></td>
                  </tr>
                </tbody>
              </table>
            </fieldset>
            <fieldset>
              <legend>其他信息</legend>
              <table class="table-1">
                <tbody>
                  <tr>
                    <td class="rowhead w-p20">激活需求</td>
                    <td><?php echo $product->active_story;?></td>
                  </tr>
                  <tr>
                    <td class="rowhead">草稿需求</td>
                    <td><?php echo $product->draft_story;?></td>
                  </tr>
                  <tr>
                    <td class="rowhead">已关闭需求</td>
                    <td><?php echo $product->closed_story;?></td>
                  </tr>
                  <tr>
                    <td class="rowhead">相关BUG</td>
                    <td><?php echo $product->relate_bug;?></td>
                  </tr>
                  <tr>
                    <td class="rowhead">未解决</td>
                    <td><?php echo $product->active_bug;?></td>
                  </tr>
                  <tr>
                    <td class="rowhead">未指派</td>
                    <td><?php echo $product->no_assigned_bug;?></td>
                  </tr>
                </tbody>
              </table>
            </fieldset>
            </td>
        </tr>
      </tbody>
    </table>
  </div>
  <?php
if($include_headfoot){
  echo <<<EOF
<div id="divider"></div>
EOF;
}
?>
</div>