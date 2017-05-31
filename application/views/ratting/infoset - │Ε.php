<?php
$userinfo = $_SESSION['userinfo'];
if(empty($userinfo['image'])){
	$userinfo['image'] = 'default.gif';
}
$config_img = config_item('upimg');
?>
<link href="/public/headimg/css/style.css" rel="stylesheet" type="text/css" />
<link href="/public/headimg/css/jcrop.css" rel="stylesheet" type="text/css" />
<link href="/public/headimg/css/bootstrap.css" rel="stylesheet" type="text/css" />
<script src="/public/headimg/js/jquery.uploadify.min.js" type="text/javascript"></script>
<script src="/public/headimg/js/jcrop.js" type="text/javascript"></script>
<div id="wrap">
    <div class="outer">
        <div class="usercenter">
			<div class="user-right">
			    <h3>头像设置</h3>
			    <form id="pic" class="update-pic clearfix">
			        <div class="upload-area">
			            <input type="file" id="user-pic">
			            <div class="file-type">
			                支持JPG,GIF,PNG图片小于<em>2MB</em>，尺寸<em>不小于160*160</em>,真实高清头像更受欢迎！
			            </div>
			            <div class="preview hidden" id="preview-hidden"></div>
			        </div>
			        <div class="pull-left">
			            <input type="hidden" id="x" name="x" />
			            <input type="hidden" id="y" name="y" />
			            <input type="hidden" id="w" name="w" />
			            <input type="hidden" id="h" name="h" />
			            <input type="hidden" id='img_src' name='src'/>
			            <div class="tcrop">头像预览</div>
			            <div class="crop crop100"><img id="crop-preview-100" src="<?php echo '/'.$config_img['big_img'].$userinfo['image'];?>" alt="" width='160' height='160' style="vertical-align: middle;"></div>
			            <div class="crop crop60"><img id="crop-preview-60" src="<?php echo '/'.$config_img['small_img'].$userinfo['image'];?>" alt="" width='64' height='64'></div>
			            <div class="tcrop"><input type="text" name="sign" id = "sign" class="text-3" style="margin-bottom:20px; height:30px;" placeholder="个性签名(限定100字符)" maxlength="100" value="<?php echo $userinfo['sign'];?>"/></div>
			            <div class='clearfix'></div>
			            <a id="save-pic" class="btn btn-primary" href="javascript:;">保存</a>
			            <a id="reupload-img" class="btn btn-primary" href="javascript:$('#user-pic').uploadify('cancel','*');">重新上传</a>
			        </div>
			    </form>
			</div>
		</div>

    </div>

    <div id="divider"></div>
</div>
<!-- <input type="text" name="session_name" id="session_name" value="<?php echo session_name();?>" />
<input type="text" name="session_id" id="session_id" value="<?php echo session_id();?>" /> -->
   <script type="text/javascript">
        $(function() {
        	var session_name = $("#session_name").val();
        	var session_id = $("#session_id").val();
        	$("#user-pic").uploadify({
        		'swf' : '/public/headimg/js/uploadify.swf',
        		'uploader' : '/ratting/setimg',
        		// 'uploader' : '/ratting/setimg?'+session_name+'='+session_id,
        		// 'formData' : {session_name:$("#session_id").val()},
        		'width' : '200',
        		'height' : '200',
        		'buttonText' : '上传头像',
        		'fileTypeExts' : '*.jpg; *.gif;*.png;',
        		'fileObjName' : 'filedata',
        		'debug' : false,
        		'onUploadSuccess' : function(file,data,respone){ //文件上传成功后触发事件

        			var data = $.parseJSON(data); //解析json字符串
        			if(data['status'] != 1){ //表明上传成功
        				alert("上传失败!");
        				return false;
        			}
        			var imgurl = data['src']; //图片地址
                    alert(imgurl)
        			var preview = $('.upload-area').children('#preview-hidden');
        			preview.show().removeClass('hidden');
        			//两个预览窗口赋值
        			$('.crop').children('img').attr('src',imgurl + '?random='+Math.random());
        			//隐藏表单赋值
        			$('#img_src').val(imgurl);
        			//绑定需要裁切的图片 即给隐藏div添加图片
        			var img = $('<img />');
        			preview.append(img);
        			preview.children('img').attr('src',imgurl+'?random='+Math.random());
        			var crop_img = preview.children('img');
                    crop_img.attr('id', "cropbox").show();
                    var img = new Image();
                    img.src = imgurl + '?random=' + Math.random();
                    //根据图片大小居中
                    img.onload = function() {
                        var img_height = 0;
                        var img_width = 0;
                        var real_height = img.height;
                        var real_width = img.width;
                        if (real_height > real_width && real_height > 200) {
                            var persent = real_height / 200;
                            real_height = 200;
                            real_width = real_width / persent;
                        } else if (real_width > real_height && real_width > 200) {
                            var persent = real_width / 200;
                            real_width = 200;
                            real_height = real_height / persent;
                        }
                        if (real_height < 200) {
                            img_height = (200 - real_height) / 2;
                        }
                        if (real_width < 200) {
                            img_width = (200 - real_width) / 2;
                        }
                        preview.css({width: (200 - img_width) + 'px', height: (200 - img_height) + 'px'}); //将样式给予隐藏div
                        preview.css({paddingTop: img_height + 'px', paddingLeft: img_width + 'px'});
                    }
                    
                     $('#cropbox').Jcrop({
                        bgColor: '#333', //选区背景色
                        bgFade: true, //选区背景渐显
                        fadeTime: 1000, //背景渐显时间
                        allowSelect: false, //是否可以选区，
                        allowResize: true, //是否可以调整选区大小
                        aspectRatio: 1, //约束比例 控制等比例裁图
                        minSize: [120, 120],
                        boxWidth: 200,
                        boxHeight: 200,
                        onChange: showPreview,  //选框改变时的事件
                        onSelect: showPreview,  //选框选定时的事件
                        setSelect: [0, 0, 200, 200], //创建选框 0,0选区所在位置！200选区的大小
                    });
                    
        		}
        	});
        	
        	//提交裁剪好的图片
            var CutJson = {};
        	//预览图
            function showPreview(coords) {
                var img_width = $('#cropbox').width();
                var img_height = $('#cropbox').height();
                //根据包裹的容器宽高,设置被除数
                var rx = 160 / coords.w;
                var ry = 160 / coords.h;
                $('#crop-preview-100').css({
                    width: Math.round(rx * img_width) + 'px',
                    height: Math.round(ry * img_height) + 'px',
                    marginLeft: '-' + Math.round(rx * coords.x) + 'px',
                    marginTop: '-' + Math.round(ry * coords.y) + 'px'
                });
                rx = 64 / coords.w;
                ry = 64 / coords.h;
                $('#crop-preview-60').css({
                    width: Math.round(rx * img_width) + 'px',
                    height: Math.round(ry * img_height) + 'px',
                    marginLeft: '-' + Math.round(rx * coords.x) + 'px',
                    marginTop: '-' + Math.round(ry * coords.y) + 'px'
                });
                var imgurl = $('#img_src').val();
                CutJson = {
                    'path': imgurl,
                    'x': Math.floor(coords.x),
                    'y': Math.floor(coords.y),
                    'w': Math.floor(coords.w),
                    'h': Math.floor(coords.h)
                };
            }
        	
        	//保存头像
            $('#save-pic').click(function() {
            	var sign = $("#sign").val();
                if ($('#preview-hidden').html() == '' && sign == '') {
                    alert('请先上传图片！');
                } else {
                    $.ajax({
                    	type:'POST',
                        // dataType: "JSON",
                        url: "/ratting/cutimg?action=jcrop&sign="+sign,
                        data: {'crop': CutJson},
                        success: function(data) {
                            alert(data);
                        	alert('保存成功!');
                            window.location.reload();
                        }
                    });
                }
            });
        	
            //重新上传
            var i = 0;
            $('#reupload-img').click(function() {
                $('#preview-hidden').find('*').remove();
                $('#preview-hidden').hide().addClass('hidden').css({'padding-top': 0, 'padding-left': 0});
            });
        
        });
    </script>
