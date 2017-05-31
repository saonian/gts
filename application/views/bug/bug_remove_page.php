<div id="wrap">
  <div class="outer">
    <form method="post" id="dataform" action="/bug/remove">
      <input type="hidden" name="bug_id" value="<?php echo $data->id;?>" />
      <input type="hidden" id="project_id" name="project_id" value="<?php echo $data->project_id;?>" />
      <table align="center" class="table-1 a-left">
        <caption>
        <div class="f-left"><strong>DELETE BUG (<?php if(isset($data->title)) echo $data->title;?>)</strong></div>
        </caption>
        <tbody>
          <tr>
            <th class="rowhead">备注</th>
            <td><textarea name="comment" rows="6" class="editor"></textarea></td>
          </tr>
          <tr>
            <td colspan="2" class="a-center"><input type="submit" id="submit" value="保存" class="button-s">
              <input type="reset" id="reset" value="重填" class="button-r"> <input type="button" value="返回" class="button-s" onclick="window.location.href='/bug/'"></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
  <div id="divider"></div>
</div>