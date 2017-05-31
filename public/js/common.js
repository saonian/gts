var editor;
$(document).ready(function(){
	//代码高亮
	SyntaxHighlighter.autoloader(
		'applescript            /public/js/syntaxhighlighter/scripts/shBrushAppleScript.js',
		'actionscript3 as3      /public/js/syntaxhighlighter/scripts/shBrushAS3.js',
		'bash shell             /public/js/syntaxhighlighter/scripts/shBrushBash.js',
		'coldfusion cf          /public/js/syntaxhighlighter/scripts/shBrushColdFusion.js',
		'cpp c                  /public/js/syntaxhighlighter/scripts/shBrushCpp.js',
		'c# c-sharp csharp      /public/js/syntaxhighlighter/scripts/shBrushCSharp.js',
		'css                    /public/js/syntaxhighlighter/scripts/shBrushCss.js',
		'delphi pascal          /public/js/syntaxhighlighter/scripts/shBrushDelphi.js',
		'diff patch pas         /public/js/syntaxhighlighter/scripts/shBrushDiff.js',
		'erl erlang             /public/js/syntaxhighlighter/scripts/shBrushErlang.js',
		'groovy                 /public/js/syntaxhighlighter/scripts/shBrushGroovy.js',
		'java                   /public/js/syntaxhighlighter/scripts/shBrushJava.js',
		'jfx javafx             /public/js/syntaxhighlighter/scripts/shBrushJavaFX.js',
		'js jscript javascript  /public/js/syntaxhighlighter/scripts/shBrushJScript.js',
		'perl pl                /public/js/syntaxhighlighter/scripts/shBrushPerl.js',
		'php                    /public/js/syntaxhighlighter/scripts/shBrushPhp.js',
		'text plain             /public/js/syntaxhighlighter/scripts/shBrushPlain.js',
		'py python              /public/js/syntaxhighlighter/scripts/shBrushPython.js',
		'ruby rails ror rb      /public/js/syntaxhighlighter/scripts/shBrushRuby.js',
		'sass scss              /public/js/syntaxhighlighter/scripts/shBrushSass.js',
		'scala                  /public/js/syntaxhighlighter/scripts/shBrushScala.js',
		'sql                    /public/js/syntaxhighlighter/scripts/shBrushSql.js',
		'vb vbnet               /public/js/syntaxhighlighter/scripts/shBrushVb.js',
		'xml xhtml xslt html    /public/js/syntaxhighlighter/scripts/shBrushXml.js'
	);
	SyntaxHighlighter.defaults['toolbar'] = false;
	SyntaxHighlighter.all();
	
	// 绑定所有的全选, 反选按钮
	$("#allchecker").click(function(obj){
		var checkboxname = $(obj.currentTarget).attr("checkboxname");
		$("input[name='"+checkboxname+"']").attr("checked", true);
	});

	$("#reversechecker").click(function(obj){
		var checkboxname = $(obj.currentTarget).attr("checkboxname");
		$("input[name='"+checkboxname+"']").attr("checked", false);
	});

	// 设置 jquery ui datepicker 外观
	var datetimeFormatObj = {
		showSecond: true,
		changeYear: true,
		changeMonth: true,
		timeFormat: 'HH:mm:ss',
		dateFormat: 'yy-mm-dd',
		timeText: '时间',
		hourText: '时',
		minuteText: '分',
		secondText: '秒',
		stepHour: 1,
		stepMinute: 1,
		stepSecond: 1,
		currentText: '现在',
		closeText: '完成'
	};
	var dateFormatObj = {
		showTimepicker: false,
		changeYear: true,
		changeMonth: true,
		dateFormat: 'yy-mm-dd',
		currentText: '现在',
		closeText: '完成'
	};
	$('.datetime').datetimepicker(datetimeFormatObj);
	$('.date').datetimepicker(dateFormatObj);

	// kindeditor
	var bugTools =
	[ 'formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic','underline', '|', 
	'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist', 'insertunorderedlist', '|',
	'emoticons', 'image', 'code', 'link', '|', 'removeformat','undo', 'redo', 'fullscreen', 'source', 'savetemplate', 'about'];

	var simpleTools = 
	[ 'formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic','underline', '|', 
	'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist', 'insertunorderedlist', '|',
	'emoticons', 'image', 'code', 'link', '|', 'removeformat','undo', 'redo', 'fullscreen', 'source', 'about'];

	var fullTools = 
	[ 'formatblock', 'fontname', 'fontsize', 'lineheight', '|', 'forecolor', 'hilitecolor', '|', 'bold', 'italic','underline', 'strikethrough', '|',
	'justifyleft', 'justifycenter', 'justifyright', 'justifyfull', '|',
	'insertorderedlist', 'insertunorderedlist', '|',
	'emoticons', 'image', 'insertfile', 'hr', '|', 'link', 'unlink', '/',
	'undo', 'redo', '|', 'selectall', 'cut', 'copy', 'paste', '|', 'plainpaste', 'wordpaste', '|', 'removeformat', 'clearhtml','quickformat', '|',
	'indent', 'outdent', 'subscript', 'superscript', '|',
	'table', 'code', '|', 'pagebreak', 'anchor', '|', 
	'fullscreen', 'source', 'preview', 'about'];
	KindEditor.ready(function(K) {
		editor = K.create('textarea[class="editor"]', {
			resizeType : 1,
			filterMode: true,
			urlType: 'absolute',
			allowPreviewEmoticons : false,
			allowImageUpload : true,
			uploadJson: '/upload4kindeditor',
			//fileManagerJson: '',//图片中心获取图片数据url
			allowFileManager: true,
			items: simpleTools,
			afterBlur: function(){
				this.sync();
				$('textarea[class="editor"]').trigger("blur");
			},
			afterCreate : function(){
				var doc = this.edit.doc; 
				var cmd = this.edit.cmd; 
				/* Paste in chrome.*/
				/* Code reference from http://www.foliotek.com/devblog/copy-images-from-clipboard-in-javascript/. */
				if(K.WEBKIT)
				{
					$(doc.body).bind('paste', function(ev) {
						var $this = $(this);
						var clipboardData = ev.originalEvent.clipboardData;
						for(var i=0; i<clipboardData.items.length; i++){
							var item = clipboardData.items[i];
							if(item.kind=='file' && item.type.match(/^image\//i)){
								//file就是剪贴板中的二进制图片数据
								var file = item.getAsFile(), reader = new FileReader();
								//定义fileReader读取完数据后的回调
								reader.onload = function (evt) {
									var result = evt.target.result;
									var arr = result.split(",");
									var data = arr[1]; // raw base64
									var contentType = arr[0].split(";")[0].split(":")[1];

									html = '<img src="' + result + '" alt="" />';
									$.post('/pasteimg', {editor: html}, function(data){cmd.inserthtml(data);});
								};
								reader.readAsDataURL(file);//用fileReader读取二进制图片，完成后会调用上面定义的回调函数
							}
						}
					});
				}

				/* Paste in firfox.*/
				if(K.GECKO)
				{
					K(doc.body).bind('paste', function(ev)
					{
						setTimeout(function()
						{
							var html = K(doc.body).html();
							if(html.search(/<img src="data:.+;base64,/) > -1)
							{
								$.post('/pasteimg', {editor: html}, function(data){K(doc.body).html(data);});
							}
						}, 80);
					});
				}
				/* End */
			}
		});
	});
	
	$.extend($.validator.messages, {
		required: "必填字段",
		remote: "请修正该字段",
		email: "请输入正确格式的电子邮件",
		url: "请输入合法的网址",
		date: "请输入合法的日期",
		dateISO: "请输入合法的日期 (ISO).",
		number: "请输入合法的数字",
		digits: "只能输入整数",
		creditcard: "请输入合法的信用卡号",
		equalTo: "请再次输入相同的值",
		accept: "请输入拥有合法后缀名的字符串",
		maxlength: $.validator.format("请输入一个长度最多是 {0} 的字符串"),
		minlength: $.validator.format("请输入一个长度最少是 {0} 的字符串"),
		rangelength: $.validator.format("请输入一个长度介于 {0} 和 {1} 之间的字符串"),
		range: $.validator.format("请输入一个介于 {0} 和 {1} 之间的值"),
		max: $.validator.format("请输入一个最大为 {0} 的值"),
		min: $.validator.format("请输入一个最小为 {0} 的值")
	});

	$(".datatable tr").live('mouseover',function(){
		$(this).css("background-color", "rgb(208, 222, 227)");
	});
	$(".datatable tr").live('mouseleave',function(){
		$(this).css("background-color", "");
	});
	// JS作的防止表单重复提交(自用，不需要防止机器，所以JS就够了)
	$(".actionlink input[type=button]").click(function(){
		if($(this).val().indexOf("备注")>=0){
			return;
		}
		$(this).attr("disabled", "");
	});
	$(".datatable tr").find("td:last a").click(function(){
		window.location.href = $(this).attr("href");
		$(this).attr("href", "javascript:");
	});
});

/**
 * Add a file input control.
 * 
 * @param  object $clickedButton 
 * @access public
 * @return void
 */
function addFile(clickedButton)
{
	fileRow = "<div class='fileBox' id='fileBox$i'><input type='file' name='files[]' class='fileControl'  tabindex='-1' \/><label tabindex='-1' class='fileLabel'>&nbsp;标题：&nbsp;<\/label><input type='text' name='labels[]' class='text-3' tabindex='-1' \/>&nbsp;<input type='button' onclick='addFile(this)' value='增加'/>&nbsp;<input type='button' onclick='delFile(this)' value='删除'/><\/div>";
	fileRow = fileRow.replace('$i', $('.fileID').size() + 1);
	$(clickedButton).parent().after(fileRow);

	setFileFormWidth(0.9);
	updateID();
}

/**
 * Delete a file input control.
 * 
 * @param  object $clickedButton 
 * @access public
 * @return void
 */
function delFile(clickedButton)
{
	if($('.fileBox').size() == 1) return;
	$(clickedButton).parent().remove();
	updateID();
}

function switchProject(projectId){
	if(projectId){
		$.cookie("current_project_id", projectId, {expires: 30*24*60*60, path: '/'})
		window.location.href = window.location.href;
	}
}

function switchProduct(productId){
	if(productId){
		$.cookie("current_product_id", productId, {expires: 30*24*60*60, path: '/'})
		window.location.href = window.location.href;
	}
}

function batch_action(actionUrl){
	if(!actionUrl){
		return;
	}
	var length = $("form:first").find("input:checkbox:checked").length;
	if(length == 0){
		alert("请选择需要批量操作的项");
		return;
	}
	$("form:first").attr("action", actionUrl);
	$("form:first").submit();
}

function setComment(){
	$('#commentBox').toggle();
	$('.ke-container').css('width', '100%');
	setTimeout(function() { $('#commentBox textarea').focus(); }, 50);
}

function switchStatus(name, value){
	var url = window.location.href;
	var index = url.indexOf('?');
	if(!name || !value){
		var reg = new RegExp('&'+name+'=[^&]*|'+name+'=[^&]*',"gi");
		// var reg = new RegExp('(.+\?.*)('+name+'=[^&]*|&'+name+'=[^&]*)(.*)',"gi");
		url = url.replace(reg, "");
		if(url.indexOf("?&") > 0){
			url = url.replace("?&", "?");
		}
		window.location.href = url;
		return;
	}
	if(url.indexOf("?"+name+"=") > 0 || url.indexOf("&"+name+"=") > 0){
		var reg = new RegExp('(.+[\?&])('+name+'=[^&]*)(.*)',"gi");
		url = url.replace(reg, "$1"+name+'='+value+"$3");
	}else{
		var addstr = index > 0 ? (index+1==url.length?'':'&') : '?';
		url += addstr+name+'='+value;
	}
	window.location.href = url;
}

function switchChange(obj){
	$(obj).parent().parent().find("div[class=changes]").toggle("fast" ,function(){
		if($(this).is(":hidden")){
			$(obj).attr("class", "hand change-show");
		}else{
			$(obj).attr("class", "hand change-hide");
		}
	});
}