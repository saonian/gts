<?php
$userinfo = $_SESSION['userinfo'];
if(empty($userinfo['image'])){
	$userinfo['image'] = 'default.gif';
}
$config_img = config_item('upimg');
?>
<link href="/public/headimg/css/style.css?20141017" rel="stylesheet" type="text/css" />
<link href="/public/headimg/css/bootstrap.css" rel="stylesheet" type="text/css" />
<script src="/public/headimg/js/swfobject.js" type="text/javascript"></script>
<script src="/public/headimg/js/fullAvatarEditor.js" type="text/javascript"></script>
<div id="upload_head" class="usercenter">
    <h3>头像设置</h3>
    <div class="flash_head">
        <p id="swfContainer">
            本组件需要安装Flash Player后才可使用，请从<a target="_blank" href="http://www.adobe.com/go/getflashplayer"> 这里 </a>下载安装。
        </p>
    </div>
    <div class="pull-left">
        <input type="hidden" id="x" name="x">
        <input type="hidden" id="y" name="y">
        <input type="hidden" id="w" name="w">
        <input type="hidden" id="h" name="h">
        <input type="hidden" id="img_src" name="src">
        <div class="tcrop">目前头像</div>
        <div class="crop crop100"><img id="crop-preview-100" src="/public/headimg/uploads/big160_<?php echo $userinfo['image'];?>" alt="" width="160" height="160" style="vertical-align: middle;"></div>
        <!-- <div class="crop crop60"><img id="crop-preview-60" src="/public/headimg/uploads/small64_default.gif" alt="" width="64" height="64"></div> -->
        <div class="tcrop"><textarea  rows="3" cols="35" name="sign" id="sign" placeholder="个性签名(限定100字符)" maxlength="100" onblur="save_sign();"><?php echo $userinfo['sign'];?></textarea></div>
        <div class="clearfix"></div>
        </div>
</div>
        <script type="text/javascript">
            swfobject.addDomLoadEvent(function () {
                var swf = new fullAvatarEditor("swfContainer",430,620, {
                        id: 'filedata',
                        upload_url: '/ratting/setimg',
                        src_upload:2,
                        tab_visible:false,
                        isShowUploadResultIcon:true,
                        browse_tip:' 支持JPG,GIF,PNG图片小于<em>2MB</em>，<br />尺寸<em>不小于160*160</em>,真实高清头像更受欢迎！',
                        browse_tip_y:240,
                        checkbox_visible:false,
                        avatar_sizes:'160*160|64*64',
                        avatar_sizes_desc:'160*160像素|64*64像素',
                        src_upload: 0 ,
                        avatar_field_names:'_160img|_64img'                 
                    },
                    //callback 回调函数
                    function (msg) {
                        switch(msg.code)
                        {
                            case 1 : console.log("页面成功加载了组件！");break;
                            case 2 : console.log("已成功加载默认指定的图片到编辑面板。");break;
                            break;
                            case 5 :
                                // 上传成功 
                                if(msg.type == 0)
                                {   
                                    $("crop-preview-100").attr("src",msg.content.url);
                                    window.location.reload();
                                    //console.log("头像已成功保存至服务器");
                                }
                            break;
                        }
                    }
                );
            });

function save_sign(){
    var value = $("#sign").val();
    if(value != ''){
        $.ajax({
           type: "POST",
           url: "/ratting/save_sign",
           data: "sign="+value,
           success: function(msg){
           }
        });
    }
}
        </script>
