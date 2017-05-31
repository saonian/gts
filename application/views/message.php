<div id="wrap">
  <div style="min-height: 440px;" class="outer">
    <table align="center" class="table-6">
      <caption><?php echo $caption;?></caption>
      <tbody>
        <tr>
          <th><strong class="red f16"><?php echo $message;?></strong></th>
        </tr>
        <tr valign="top">
          <td class="a-center">
            <div class="roadmap release">
              <p><?php echo $description;?></p>
              <p>
                <?php foreach($back_urls as $val):?>
                <a href="<?php echo $val['url'];?>">&lt;&lt;<?php echo $val['name'];?></a>
                <?php endforeach;?>
              </p>
            </div></td>
        </tr>
      </tbody>
    </table>
  </div>
  <div id="divider"></div>
</div>