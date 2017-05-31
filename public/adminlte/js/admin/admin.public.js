function btnSub(formid,methodname){
	if(formid == 'formlist' && methodname == "order") {
		if ($("#content_list input[name='optid[]']").length == 0) {
			 $.dialog.tips(lang.pselect);
			 return;
		}
	}
	if(formid == 'formlist' && methodname == "readall") {
		if($("#formlist").find("input:checked").serialize() == ''){
			$.dialog.tips(lang.pselect);
			 return;
		}
	}
	if($("#"+formid).validform('validall')){
		$('#'+formid).attr('action',$("#action").val()+'/'+methodname);
		$('#'+formid).submit();
	}

}

function setClass(t){
	if($(t).val()==0){
			$("#tclass").show();
	}else{
		$("#tclass").hide();
	}
}

function setTid(t){
	var obj = $(t).children("td").children("input");
	if(obj.attr('checked')=='checked'){
		obj.prop('checked',false);
		$(t).children("td").removeClass('listhover');
	}else{
		obj.prop('checked',true);
		$(t).children("td").addClass('listhover');
	}
}
function checkAll(t,tname){
	tname = tname?tname:'optid[]'
	if($(t).attr('checked')=='checked'){
		$("input[name='"+tname+"']").attr('checked',true);
	}else{
		$("input[name='"+tname+"']").attr('checked',false);
	}
}

function checkAllMethod(t){
	if($(t).attr('checked')=='checked'){
		$("input[name*='_method']").attr('checked',true);
	}else{
		$("input[name*='_method']").attr('checked',false);
	}
}

function gotopage(num){
	$("#currentpage").val(num);
	$('#formpage').attr('action',$("#action").val());
	$('#formpage').submit();
}

function nTabs(t,tid,listid,hover,listclass){
	$(t).parent().children().removeClass(hover);
	$(t).addClass(hover);
	$("."+listclass).hide();
	$("#"+listid+tid).show();
}

function seton(t,url) {
	$(t).parent().parent().find('td').each(function(){
		$(this).removeClass("on");
	});
	$(t).addClass("on");
	parent.main.location.href=url;
}

function add(url){
	$.dialog({title:lang.add,lock:true,
		content:"页面努力加载中，请稍等片刻！",
		init:function(){
			var thisobj = this;
			$.ajax({
				type: "GET",
				url: url,
				dataType: "json",
				cache:false,
				success: function(data){
					if(data.status==200){
						var editors = new Array();
						thisobj.content(data.remsg);
						$(".editor").each(function(){
							var idname = this.id;
							var editor=KindEditor.create('#'+idname);
							editors.push(editor);
						});
				        $("#formview").validform();
				        thisobj.button({
				        	name:"提交",
					        callback: function () {
					        	if($("#formview").validform('validall')){
					        		var len = editors.length;
					        		for(var i=0;i<len;i++){
					        			editors[i].sync();
					        		}
					            	subOK(thisobj,'add');
					            }
					            return false;
					        },
					        focus: true
					    });
			      	}else{
			      		thisobj.close();
			      		showmsg(data);
			      	}
				}
			});
		}
	});
}
//查看信息
function viewit(url){
	$.dialog({title:lang.view,lock:true,
		content:"页面努力加载中，请稍等片刻！",
		init:function(){
			var thisobj = this;
			$.ajax({
				type: "GET",
				url: url,
				dataType: "json",
				cache: false,
				success: function(data){
					if(data.status==200){
						var editors = new Array();
						thisobj.content(data.remsg);
						$(".editor").each(function(){
								var idname = this.id;
								var editor=KindEditor.create('#'+idname);
								editors.push(editor);
						});
				        $("#formview").validform();
				        thisobj.button({
				        	name:"确认",
					        callback: function () {
					        		thisobj.close();
					            return false;
					        },
					        focus: true
					    });
			      	}else{
			      		thisobj.close();
			      		showmsg(data);
			      	}
				}
			});
		}
	});
}
function edit(url){
	$.dialog({title:lang.edit,lock:true,
		content:"页面努力加载中，请稍等片刻！",
		init:function(){
			var thisobj = this;
			$.ajax({
				type: "GET",
				url: url,
				dataType: "json",
				cache: false,
				success: function(data){
					if(data.status==200){
						var editors = new Array();
						thisobj.content(data.remsg);
						$(".editor").each(function(){
								var idname = this.id;
								var editor=KindEditor.create('#'+idname);
								editors.push(editor);
						});
				        $("#formview").validform();
				        thisobj.button({
				        	name:"提交",
					        callback: function () {
					        	if($("#formview").validform('validall')){
					        		var len = editors.length;
					        		for(var i=0;i<len;i++){
					        			editors[i].sync();
					        		}
					            	subOK(thisobj,'edit');
					            }
					            return false;
					        },
					        focus: true
					    });
			      	}else{
			      		thisobj.close();
			      		showmsg(data);
			      	}
				}
			});
		}
	});
}

function shouquan(url){
	$.dialog({title:lang.edit,lock:true,
		content:"页面努力加载中，请稍等片刻！",
		init:function(){
			var thisobj = this;
			$.ajax({
				type: "GET",
				url: url,
				dataType: "json",
				cache: false,
				success: function(data){
					if(data.status==200){
						thisobj.content(data.remsg);
				        thisobj.button({
				        	name:"提交",
					        callback: function () {
					            	subOK(thisobj,'shouquan');
					            return false;
					        },
					        focus: true
					    });
			      	}else{
			      		thisobj.close();
			      		showmsg(data);
			      	}
				}
			});
		}
	});
}

function del(url,ismultiple,tid){
	var data;
	$.dialog.confirm(lang.delnotice, function(){
		if(ismultiple){
			data = $("#formlist").find("input:checked").serialize();
		}else{
			data = "optid="+tid;
		}
		if(data==""){
				 $.dialog.tips(lang.pselect);
				 return;
		}
		$.dialog({title:lang.del,lock:true,
		content:"数据努力处理中，请稍等片刻！",
		init:function(){
			var thisobj = this;
			$.ajax({
				type: "POST",
				url: url,
				dataType: "json",
				data: data,
				success: function(data){
					if(data.status==200){
						//alert($.inArray('5',data.ids));
						$("#content_list").children().each(function(){
							if($.isArray(data.ids)){
								if($.inArray(this.id.substr(4),data.ids)>=0){
									$(this).remove();
								}
							}else{
								if(this.id.substr(4)==data.ids){
									$(this).remove();
								}
							}
						});
				       thisobj.close();
				       	$.dialog.tips("删除成功!");
			      	}else{
			      		thisobj.close();
			      		showmsg(data);
			      	}
				}
			});
		}
	});
	}, function(){
	    $.dialog.tips(lang.undelnotice);
	});
}

function unsetThumb(objid,imgobjid){
	$("#"+objid).val('');
	$("#"+imgobjid).attr('src',baseurl+'data/nopic8080.gif');
}

//status:200(正常),201(登录失效),202(无权限),203(请选择记录),204(用户名或密码错误),205(提交成功，需刷新本页面)206(记录重复);
function showmsg(data){
	if(data.status==201){
		showajaxlogin();
	}else if(data.status==202){
		$.dialog.tips("无此功能权限");
	}else if(data.status==203){
		$.dialog.tips("请选择记录");
	}else if(data.status==204){
		$.dialog.tips("用户名或密码错误");
	}else if(data.status==205){
		location.reload();
	}else if(data.status==206){
		$.dialog.tips("记录重复");
	}else if(data.status==207){
		$.dialog.tips("缺少添加/修改记录所必要的参");
	}else{
		$.dialog.tips(data.remsg);
	}
}
function showajaxlogin(){
	$.dialog({title:lang.login,lock:true,
		content:"页面努力加载中，请稍等片刻！",
		init:function(){
			var thisobj = this;
			$.ajax({
				type: "GET",
				url: siteaurl+'/login/ajaxlogin',
				dataType: "json",
				cache:false,
				success: function(data){
					if(data.status==200){
						thisobj.content(data.remsg);
						thisobj.button({
					        	name:lang.login,
						        callback: function () {
						        	var postdata = $("#ajaxlogin").serialize();
						        	$.ajax({
						        		type: "POST",
											url: siteaurl+'/main/ajaxlogin',
											dataType: "json",
											data:postdata,
											cache:false,
											success:function(data){
												if(data.status==200){
													thisobj.close();
													$.dialog.tips("登录成功");
												}else{
														showmsg(data);
												}
											}
						        	});
						            return false;
						        },
						        focus: true
						    });
			      	}else{
			      		thisobj.close();
			      		showmsg(data);
			      	}
				}
			});
		}
	});
}

function subOK(thisobj,type){
	var data = $("#formview").serialize();
	var url = $("#formview").find("#action").val()+'/'+type;
	var tobj;
	$.ajax({
		type: "POST",
		dataType:"json",
		url: url,
		data: data,
		cache:false,
		beforeSend:function(){
			tobj = $.dialog({fixed:true,lock:true,drag:false,content: '正在努力处理中，请稍等！'});
		},
		success: function(data){
			if(data.status==200){
				if(type=='add'){
					$("#content_list").prepend(data.remsg);
					$('html,body').animate({scrollTop: 0}, 300);
				}else if(type=='edit'){
					$("#content_list").children().each(function(){
						if(this.id.substr(4)==data.id){
							$(this).before(data.remsg);
							$(this).remove();
						}
					});
				}
				tobj.close();
				thisobj.close();
				$.dialog.tips("保存成功！");
			}else{
				tobj.close();
				showmsg(data);
			}
		}
	});
}
//上传图片
function uploadpic(t,picid){
	var editor = KindEditor.editor({
		allowFileManager : true
	});
	editor.loadPlugin('image', function() {
		editor.plugin.imageDialog({
			    imageUrl : KindEditor('#'+picid).val(),
			    showRemote : false,
				clickFn : function(url, title, width, height, border, align) {
					newurl = url.substr(url.indexOf("data"));
					$('#'+picid).val(newurl);
					if(t!=''){
						$(t).attr('src',url);
					}
					editor.hideDialog();
				}
			});
	});
}
//上传视频
function uploadmv(mvid,dis){
	var editor = KindEditor.editor({
		allowFileManager : false
	});
	if (dis == '') dis=false;
	editor.loadPlugin('media', function() {
		editor.plugin.media.edit({
		    clickFn : function(url) {
				newurl = url.substr(url.indexOf("data"));
				$('#'+mvid).val(newurl);
				editor.hideDialog();
			},disableUrl:dis
		});
	});
}

//上传文件
function uploadfile(fileid){
	var editor = KindEditor.editor({
		allowFileManager : false
	});
	editor.loadPlugin('insertfile', function() {
		editor.plugin.fileDialog({
			    fileUrl : KindEditor('#'+fileid).val(),
				clickFn : function(url, title) {
					newurl = url.substr(url.indexOf("data"));
					$('#'+fileid).val(newurl);
					editor.hideDialog();
				}
			});
	});
}

//上传IPA文件
function uploadipa(fileid){
	var editor = KindEditor.editor({
		allowFileManager : false,
		dirToName : 'ipa'
	});
	editor.loadPlugin('insertfile', function() {
		editor.plugin.fileDialog({
			    fileUrl : KindEditor('#'+fileid).val(),
				clickFn : function(url, title) {
					newurl = url.substr(url.indexOf("data"));
					$('#'+fileid).val(newurl);
					editor.hideDialog();
				}
			});
	});
}

//上传plist文件
function uploadplist(fileid,isajax){
	var editor = KindEditor.editor({
		allowFileManager : false,
		dirToName : 'plist'
	});
	editor.loadPlugin('insertfile', function() {
		editor.plugin.fileDialog({
			    fileUrl : KindEditor('#'+fileid).val(),
				clickFn : function(url, title) {
					newurl = url.substr(url.indexOf("data"));
					if(isajax == 1){//分离Plist文件
						showajaxplist(newurl);
					}
					$('#'+fileid).val(newurl);
					editor.hideDialog();
				}
			});
	});
}

//上传zip文件
function uploadzip(fileid){
	var editor = KindEditor.editor({
		allowFileManager : false,
		dirToName : 'zip'
	});
	editor.loadPlugin('insertfile', function() {
		editor.plugin.fileDialog({
			    fileUrl : KindEditor('#'+fileid).val(),
				clickFn : function(url, title) {
					newurl = url.substr(url.indexOf("data"));
					$('#'+fileid).val(newurl);
					editor.hideDialog();
				}
			});
	});
}
//版本管理--解析PLIST文件
function showajaxplist(plistfile){
	$.ajax({
		type: "POST",
		url: siteaurl+'/version/plistajax',
		dataType: "json",
		data:{"plistfile":plistfile},
		cache:false,
		success:function(data){
			if(data.status==200){
				$('#vercode').val(data.vcode);
			}
		}
	});
}
//版本管理--上传APP文件
function uploadapk(fileid){
	var editor = KindEditor.editor({
		allowFileManager : false,
		dirToName : 'apk'
	});
	editor.loadPlugin('insertfile', function() {
		editor.plugin.fileDialog({
			    fileUrl : KindEditor('#'+fileid).val(),
				clickFn : function(url, title) {
					newurl = url.substr(url.indexOf("data"));
					showajaxapk(newurl);
					$('#'+fileid).val(newurl);
					editor.hideDialog();
				}
			});
	});
}
//版本管理--分析APK包信息
function showajaxapk(apkfile){
	$.ajax({
		type: "POST",
		url: siteaurl+'/version/apkajax',
		dataType: "json",
		data:{"apkfile":apkfile},
		cache:false,
		success:function(data){
			if(data.status==200){
				$('#vervalue').val(data.vcode);
				$('#vercode').val(data.vname);
				$('#verbag').val(data.vbag);
			}
		}
	});
}
//版本管理--select选值判断
function changeAPKOption(optionV){
	if(optionV == 1){//苹果
		$('#ipafile').show();
		$('#apkfile').hide();
		$('#plistfile').show();
		$('#bagtag').html('授权文件');
	}else{//其他
		$('#ipafile').hide();
		$('#apkfile').show();
		$('#plistfile').hide();
		$('#bagtag').html('版本包名');
	}
	$('#vervalue').val('');
	$('#verfile').val('');
	$('#verbag').val('');
	$('#vercode').val('');
}
//----------------------------------
//上传APK文件--APPSTORE使用
function uploadappstorefile(fileid){
	var editor = KindEditor.editor({
		allowFileManager : false,
		dirToName : 'apk'
	});
	editor.loadPlugin('insertfile', function() {
		editor.plugin.fileDialog({
			    fileUrl : KindEditor('#'+fileid).val(),
				clickFn : function(url, title) {
					newurl = url.substr(url.indexOf("data"));
					showajaxappstore(newurl);
					$('#'+fileid).val(newurl);
					editor.hideDialog();
				}
			});
	});
}
//分析APK包信息--APPSTORE使用
function showajaxappstore(apkfile){
	$.ajax({
		type: "POST",
		url: siteaurl+'/appstore/apkajax',
		dataType: "json",
		data:{"apkfile":apkfile},
		cache:false,
		success:function(data){
			if(data.status==200){
				$('#apksize').val(data.vsize);
				$('#apkver').val(data.vname);
				$('#apkbag').val(data.vbag);
			}
		}
	});
}
//select选值判断--APPSTORE使用
function changeAPPStoreOption(optionV){
	if(optionV == 1){//苹果
		$('#apkver').attr('readonly',false);
		$('#apkbag').attr('readonly',false);
		$('#apksize').attr('readonly',false);
		$('#appfile').attr('readonly',false);
		$('#filetag').html('行为链接：');
		$('#bagtag').html('应用链接：');
		$('#apkbag').val('http://');
		$('#filebut').hide();
	}else{//其他
		$('#apkver').attr('readonly',true);
		$('#apkbag').attr('readonly',true);
		$('#apksize').attr('readonly',true);
		$('#appfile').attr('readonly',true);
		$('#filetag').html('应用文件：');
		$('#bagtag').html('应用包名：');
		$('#apkbag').val('');
		$('#filebut').show();
	}
	$('#apksize').val(0);
	$('#apkver').val('');
}

function colorpicker(colorid,textid){
	var K=KindEditor
	var colorpicker;
	K(".colorpicker").bind('click', function(e) {
		var thisobj = this;
		e.stopPropagation();
		if (colorpicker) {
			colorpicker.remove();
			colorpicker = null;
			return;
		}
		var colorpickerPos = K(thisobj).pos();
		colorpicker = K.colorpicker({
			x : colorpickerPos.x,
			y : colorpickerPos.y + K(thisobj).height(),
			z : 19811214,
			selectedColor : 'default',
			noColor : lang.nocolor,
			click : function(color) {
				$("#"+colorid).val(color);
				$("#"+textid).css('color',color);
				//K(thisobj).val(color);
				//K(thisobj).css('background',color);
				colorpicker.remove();
				colorpicker = null;
			}
		});
	});
	K(document).click(function() {
		if (colorpicker) {
			colorpicker.remove();
			colorpicker = null;
		}
	});
}
//js时间比较(yyyy-mm-dd hh:mi:ss)
function compToTime(begin,end,timeName) {
	var beginTime 	= $("#"+begin).val();
	var endTime 	= $("#"+end).val();
	if(beginTime == "" || endTime == ""){
		return true;
	}
	var beginArr 	= beginTime.substring(0, 10).split('-');
	var endArr 		= endTime.substring(0, 10).split('-');
	beginTime		= beginArr[1] + '/' + beginArr[2] + '/' + beginArr[0]+ ' ' + beginTime.substring(10, 19);
	endTime			= endArr[1] + '/' + endArr[2] + '/' + endArr[0]+ ' ' + endTime.substring(10, 19);
	var tempNum 	= (Date.parse(endTime) - Date.parse(beginTime)) / 3600 / 1000;
    if (tempNum < 0) {
        alert(timeName+"结束时间不能小于开始时间!");
        return false;
    }else{
       return true;
    }
}
//时间间隔天数
function timeDiffDays(sDate1,sDate2){
	var aDate,oDate1,oDate2,iDays;
	aDate  =  sDate1.split("-");
	oDate1 =  new  Date(aDate[1]+'/'+aDate[2]+'/'+aDate[0]);
	aDate  =  sDate2.split("-");  
	oDate2 =  new  Date(aDate[1]+'/'+aDate[2]+'/'+aDate[0]);
	return parseInt((oDate1 - oDate2)/1000/60/60/24);
}

$(document).ready(function(){
	//排序数字限制
	$("input[class='onlynum']").live('keyup',function(){var tempD=$(this).val().replace(/\D|^0/g,'');
	    $(this).val((tempD=='')? 0 : tempD);
	}).live("paste",function(){//CTR+V事件处理
	        $(this).val($(this).val().replace(/\D|^0/g,'0'));
	}).css("ime-mode", "disabled"); //CSS设置输入法不可用
	//全选效果
	$("input[name='optid[]']").live('click',function(){
		var checkAllObj = $('.content_list').children("thead").children("tr").children("th").children("input[type='checkbox']");
		if($(this).attr('checked')=='checked'){
			var flag = 1;
			$("input[name='optid[]']").each(function(){
				if($(this).attr('checked')!='checked'){
					flag = 0;
					return false;
				}
			});
			if(flag){
				checkAllObj.attr('checked',true);
			}
		}else{
			checkAllObj.attr('checked',false);
		}
	});
	//输入框最大输入限制
	$(".input-text").attr("maxlength","200");
});
