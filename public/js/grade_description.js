$(function(){
    //大小项目完成质量及整体协作性
    $(".add_quality_desc").live("click", function(){
        var index = $(this).parents(".quality_setting").index() - 1;
        var html = '<tr><td bgcolor="#fff" align="center"><input type="text" class="text-4" value="" id="" name="quality_desc['+index+'][]"></td><td bgcolor="#fff" align="center"><input type="text" class="text-2" value="" id="" name="quality_start_value['+index+'][]" style="width:55px;"/>&nbsp;-&nbsp;<input type="text" class="text-2" value="" id="" name="quality_end_value['+index+'][]" style="width:55px;"/></td><td bgcolor="#fff" align="center"><input type="hidden" name="quality_reviews_required['+index+'][]" value="1"/><input type="checkbox" class="req_chkbox" value="1" checked/></td><td bgcolor="#fff" align="center"><input type="button" class="button-c add_quality_desc" value=" 添加 "></td><td bgcolor="#fff" align="center"><input type="button" class="button-c del_desc" value=" 删除 "></td></tr>';
        $(this).parents("table[class='infotable'] tbody").append(html);
    });
    $(".del_desc").live("click", function(){
        if($(this).parent().parent().siblings("tr").length == 3){
            return;
        }
        $(this).parent().parent().remove();
    });
    $(".del_quality_setting").live("click", function(){
        if($(".quality_setting").length == 1){
            return;
        }
        $(this).parents(".quality_setting").remove();
    });
    //协作与态度
    $("#add_attitude_setting").live("click", function(){
        var index = $(".attitude_setting").length;
        var html = '<div class="attitude_setting"><p>评价内容 <input type="text" class="text-3" value="" id="" name="attitude_content[]"> <input type="button" class="button-c del_attitude_setting" value=" 删除 "></p><table width="80%" cellspacing="1" cellpadding="1" border="1" class="infotable" style="background-color:#f8f8f8;"><tbody><tr><td align="center"><strong>评价说明</strong></td><td align="center"><strong>区间分值</strong></td><td width="20%" align="center"><strong>评语是否必填</strong></td><td align="center"><strong>操作</strong></td></tr><tr><td bgcolor="#fff" align="center"><input type="text" class="text-4" value="" id="" name="attitude_desc['+index+'][]"></td><td bgcolor="#fff" align="center"><input type="text" class="text-2" value="" id="" name="attitude_start_value['+index+'][]" style="width:55px;" />&nbsp;-&nbsp;<input type="text" class="text-2" value="" id="" name="attitude_end_value['+index+'][]" style="width:55px;" /></td><td bgcolor="#fff" align="center"><input type="hidden" name="attitude_reviews_required['+index+'][]" value="1"/><input type="checkbox" class="req_chkbox" value="1" checked/></td><td bgcolor="#fff" align="center"><input type="button" class="button-c add_attitude_desc" value=" 添加 "></td><td bgcolor="#fff" align="center"><input type="button" class="button-c del_desc" value=" 删除 "></td></tr><tr><td bgcolor="#fff" align="center"><input type="text" class="text-4" value="" id="" name="attitude_desc['+index+'][]"></td><td bgcolor="#fff" align="center"><input type="text" class="text-2" value="" id="" name="attitude_start_value['+index+'][]" style="width:55px;" />&nbsp;-&nbsp;<input type="text" class="text-2" value="" id="" name="attitude_end_value['+index+'][]" style="width:55px;" /></td><td bgcolor="#fff" align="center"><input type="hidden" name="attitude_reviews_required['+index+'][]" value="1"/><input type="checkbox" class="req_chkbox" value="1" checked/></td><td bgcolor="#fff" align="center"><input type="button" class="button-c add_attitude_desc" value=" 添加 "></td><td bgcolor="#fff" align="center"><input type="button" class="button-c del_desc" value=" 删除 "></td></tr><tr><td bgcolor="#fff" align="center"><input type="text" class="text-4" value="" id="" name="attitude_desc['+index+'][]"></td><td bgcolor="#fff" align="center"><input type="text" class="text-2" value="" id="" name="attitude_start_value['+index+'][]" style="width:55px;" />&nbsp;-&nbsp;<input type="text" class="text-2" value="" id="" name="attitude_end_value['+index+'][]" style="width:55px;" /></td><td bgcolor="#fff" align="center"><input type="hidden" name="attitude_reviews_required['+index+'][]" value="1"/><input type="checkbox" class="req_chkbox" value="1" checked/></td><td bgcolor="#fff" align="center"><input type="button" class="button-c add_attitude_desc" value=" 添加 "></td><td bgcolor="#fff" align="center"><input type="button" class="button-c del_desc" value=" 删除 "></td></tr></tbody></table></div>';
        $("#attitude").append(html);
    });
    $(".add_attitude_desc").live("click", function(){
        var index = $(this).parents(".attitude_setting").index() - 1;
        var html = '<tr><td bgcolor="#fff" align="center"><input type="text" class="text-4" value="" id="" name="attitude_desc['+index+'][]"></td><td bgcolor="#fff" align="center"><input type="text" class="text-2" value="" id="" name="attitude_start_value['+index+'][]" style="width:55px;"/>&nbsp;-&nbsp;<input type="text" class="text-2" value="" id="" name="attitude_end_value['+index+'][]" style="width:55px;"/></td><td bgcolor="#fff" align="center"><input type="hidden" name="attitude_reviews_required['+index+'][]" value="1"/><input type="checkbox" class="req_chkbox" value="1" checked/></td><td bgcolor="#fff" align="center"><input type="button" class="button-c add_attitude_desc" value=" 添加 "></td><td bgcolor="#fff" align="center"><input type="button" class="button-c del_desc" value=" 删除 "></td></tr>';
        $(this).parents("table[class='infotable'] tbody").append(html);
    });
    $(".del_desc").live("click", function(){
        if($(this).parent().parent().siblings("tr").length == 3){
            return;
        }
        $(this).parent().parent().remove();
    });
    $(".del_attitude_setting").live("click", function(){
        if($(".attitude_setting").length == 1){
            return;
        }
        $(this).parents(".attitude_setting").remove();
    });
    //忠诚度
    $("#add_loyalty_setting").live("click", function(){
        var index = $(".loyalty_setting").length;
        var html = '<div class="loyalty_setting"><p>评价内容 <input type="text" class="text-3" value="" id="" name="loyalty_content[]"> <input type="button" class="button-c del_loyalty_setting" value=" 删除 "></p><table width="80%" cellspacing="1" cellpadding="1" border="1" class="infotable" style="background-color:#f8f8f8;"><tbody><tr><td align="center"><strong>评价说明</strong></td><td align="center"><strong>区间分值</strong></td><td width="20%" align="center"><strong>评语是否必填</strong></td><td align="center"><strong>操作</strong></td></tr><tr><td bgcolor="#fff" align="center"><input type="text" class="text-4" value="" id="" name="loyalty_desc['+index+'][]"></td><td bgcolor="#fff" align="center"><input type="text" class="text-2" value="" id="" name="loyalty_start_value['+index+'][]" style="width:55px;" />&nbsp;-&nbsp;<input type="text" class="text-2" value="" id="" name="loyalty_end_value['+index+'][]" style="width:55px;" /></td><td bgcolor="#fff" align="center"><input type="hidden" name="loyalty_reviews_required['+index+'][]" value="1"/><input type="checkbox" class="req_chkbox" value="1" checked/></td><td bgcolor="#fff" align="center"><input type="button" class="button-c add_loyalty_desc" value=" 添加 "></td><td bgcolor="#fff" align="center"><input type="button" class="button-c del_desc" value=" 删除 "></td></tr><tr><td bgcolor="#fff" align="center"><input type="text" class="text-4" value="" id="" name="loyalty_desc['+index+'][]"></td><td bgcolor="#fff" align="center"><input type="text" class="text-2" value="" id="" name="loyalty_start_value['+index+'][]" style="width:55px;" />&nbsp;-&nbsp;<input type="text" class="text-2" value="" id="" name="loyalty_end_value['+index+'][]" style="width:55px;" /></td><td bgcolor="#fff" align="center"><input type="hidden" name="loyalty_reviews_required['+index+'][]" value="1"/><input type="checkbox" class="req_chkbox" value="1" checked/></td><td bgcolor="#fff" align="center"><input type="button" class="button-c add_loyalty_desc" value=" 添加 "></td><td bgcolor="#fff" align="center"><input type="button" class="button-c del_desc" value=" 删除 "></td></tr><tr><td bgcolor="#fff" align="center"><input type="text" class="text-4" value="" id="" name="loyalty_desc['+index+'][]"></td><td bgcolor="#fff" align="center"><input type="text" class="text-2" value="" id="" name="loyalty_start_value['+index+'][]" style="width:55px;" />&nbsp;-&nbsp;<input type="text" class="text-2" value="" id="" name="loyalty_end_value['+index+'][]" style="width:55px;" /></td><td bgcolor="#fff" align="center"><input type="hidden" name="loyalty_reviews_required['+index+'][]" value="1"/><input type="checkbox" class="req_chkbox" value="1" checked/></td><td bgcolor="#fff" align="center"><input type="button" class="button-c add_loyalty_desc" value=" 添加 "></td><td bgcolor="#fff" align="center"><input type="button" class="button-c del_desc" value=" 删除 "></td></tr></tbody></table></div>';
        $("#loyalty").append(html);
    });
    $(".add_loyalty_desc").live("click", function(){
        var index = $(this).parents(".loyalty_setting").index() - 1;
        var html = '<tr><td bgcolor="#fff" align="center"><input type="text" class="text-4" value="" id="" name="loyalty_desc['+index+'][]"></td><td bgcolor="#fff" align="center"><input type="text" class="text-2" value="" id="" name="loyalty_start_value['+index+'][]" style="width:55px;"/>&nbsp;-&nbsp;<input type="text" class="text-2" value="" id="" name="loyalty_end_value['+index+'][]" style="width:55px;"/></td><td bgcolor="#fff" align="center"><input type="hidden" name="loyalty_reviews_required['+index+'][]" value="1"/><input type="checkbox" class="req_chkbox" value="1" checked/></td><td bgcolor="#fff" align="center"><input type="button" class="button-c add_loyalty_desc" value=" 添加 "></td><td bgcolor="#fff" align="center"><input type="button" class="button-c del_desc" value=" 删除 "></td></tr>';
        $(this).parents("table[class='infotable'] tbody").append(html);
    });
    $(".del_desc").live("click", function(){
        if($(this).parent().parent().siblings("tr").length == 3){
            return;
        }
        $(this).parent().parent().remove();
    });
    $(".del_loyalty_setting").live("click", function(){
        if($(".loyalty_setting").length == 1){
            return;
        }
        $(this).parents(".loyalty_setting").remove();
    });
    //遵守纪律
    $("#add_discipline_setting").live("click", function(){
        var index = $(".discipline_setting").length;
        var html = '<div class="discipline_setting"><p>评价内容 <input type="text" class="text-3" value="" id="" name="discipline_content[]"> <input type="button" class="button-c del_discipline_setting" value=" 删除 "></p><table width="80%" cellspacing="1" cellpadding="1" border="1" class="infotable" style="background-color:#f8f8f8;"><tbody><tr><td align="center"><strong>评价说明</strong></td><td align="center"><strong>区间分值</strong></td><td width="20%" align="center"><strong>评语是否必填</strong></td><td align="center"><strong>操作</strong></td></tr><tr><td bgcolor="#fff" align="center"><input type="text" class="text-4" value="" id="" name="discipline_desc['+index+'][]"></td><td bgcolor="#fff" align="center"><input type="text" class="text-2" value="" id="" name="discipline_start_value['+index+'][]" style="width:55px;" />&nbsp;-&nbsp;<input type="text" class="text-2" value="" id="" name="discipline_end_value['+index+'][]" style="width:55px;" /></td><td bgcolor="#fff" align="center"><input type="hidden" name="discipline_reviews_required['+index+'][]" value="1"/><input type="checkbox" class="req_chkbox" value="1" checked/></td><td bgcolor="#fff" align="center"><input type="button" class="button-c add_discipline_desc" value=" 添加 "></td><td bgcolor="#fff" align="center"><input type="button" class="button-c del_desc" value=" 删除 "></td></tr><tr><td bgcolor="#fff" align="center"><input type="text" class="text-4" value="" id="" name="discipline_desc['+index+'][]"></td><td bgcolor="#fff" align="center"><input type="text" class="text-2" value="" id="" name="discipline_start_value['+index+'][]" style="width:55px;" />&nbsp;-&nbsp;<input type="text" class="text-2" value="" id="" name="discipline_end_value['+index+'][]" style="width:55px;" /></td><td bgcolor="#fff" align="center"><input type="hidden" name="discipline_reviews_required['+index+'][]" value="1"/><input type="checkbox" class="req_chkbox" value="1" checked/></td><td bgcolor="#fff" align="center"><input type="button" class="button-c add_discipline_desc" value=" 添加 "></td><td bgcolor="#fff" align="center"><input type="button" class="button-c del_desc" value=" 删除 "></td></tr><tr><td bgcolor="#fff" align="center"><input type="text" class="text-4" value="" id="" name="discipline_desc['+index+'][]"></td><td bgcolor="#fff" align="center"><input type="text" class="text-2" value="" id="" name="discipline_start_value['+index+'][]" style="width:55px;" />&nbsp;-&nbsp;<input type="text" class="text-2" value="" id="" name="discipline_end_value['+index+'][]" style="width:55px;" /></td><td bgcolor="#fff" align="center"><input type="hidden" name="discipline_reviews_required['+index+'][]" value="1"/><input type="checkbox" class="req_chkbox" value="1" checked/></td><td bgcolor="#fff" align="center"><input type="button" class="button-c add_discipline_desc" value=" 添加 "></td><td bgcolor="#fff" align="center"><input type="button" class="button-c del_desc" value=" 删除 "></td></tr></tbody></table></div>';
        $("#discipline").append(html);
    });
    $(".add_discipline_desc").live("click", function(){
        var index = $(this).parents(".discipline_setting").index() - 1;
        var html = '<tr><td bgcolor="#fff" align="center"><input type="text" class="text-4" value="" id="" name="discipline_desc['+index+'][]"></td><td bgcolor="#fff" align="center"><input type="text" class="text-2" value="" id="" name="discipline_start_value['+index+'][]" style="width:55px;"/>&nbsp;-&nbsp;<input type="text" class="text-2" value="" id="" name="discipline_end_value['+index+'][]" style="width:55px;"/></td><td bgcolor="#fff" align="center"><input type="hidden" name="discipline_reviews_required['+index+'][]" value="1"/><input type="checkbox" class="req_chkbox" value="1" checked/></td><td bgcolor="#fff" align="center"><input type="button" class="button-c add_discipline_desc" value=" 添加 "></td><td bgcolor="#fff" align="center"><input type="button" class="button-c del_desc" value=" 删除 "></td></tr>';
        $(this).parents("table[class='infotable'] tbody").append(html);
    });
    $(".del_desc").live("click", function(){
        if($(this).parent().parent().siblings("tr").length == 3){
            return;
        }
        $(this).parent().parent().remove();
    });
    $(".del_discipline_setting").live("click", function(){
        if($(".discipline_setting").length == 1){
            return;
        }
        $(this).parents(".discipline_setting").remove();
    });
    $(".req_chkbox").live("click", function(){
        var v = $(this).attr("checked")?1:2;
        $(this).siblings("input:hidden").val(v);
    });

});