<div id="wrap">
  <div class="outer">
    <form method="post" id="dataform" action="/bug/assign_form">
      <input type="hidden" name="bug_id" value="<?php echo $data->id;?>" />
      <input type="hidden" id="project_id" name="project_id" value="<?php echo $data->project_id;?>" />
      <table align="center" class="table-1 a-left">
        <caption>
        <div class="f-left"><strong>重新指派 (BUG:<?php if(isset($data->title)) echo $data->title;?>)</strong></div>
        </caption>
        <tbody>
          <tr>
            <th class="rowhead">指派给</th>
            <td><select name="assigned_to" class="text-2">
                  <?php $assigned_to = empty($data->assigned_to)?0:$data->assigned_to;?>
                  <option value="0"></option>
                  <?php foreach($all_user as $val):?>
                    <option value="<?php echo $val->id;?>" <?php echo ($val->id == $assigned_to)?'selected':''?>><?php echo $val->real_name;?></option>
                  <?php endforeach;?>
                </select>
            </td>
          </tr>
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