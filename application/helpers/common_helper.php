<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 获取当前控制器名
 * @return string 控制器名
 */
function get_controller(){
	$controller = '';
	$CI =& get_instance();
	if(config_item('enable_query_strings')){
		$c = config_item('controller_trigger');
		$controller = $CI->input->get($c, TRUE) ? $CI->input->get($c, TRUE) : 'index';
	}else{
		$controller = $CI->uri->rsegment(1);
	}
	return $controller;
}

/**
 * 获取当前方法名
 * @return string 方法名
 */
function get_method(){
	$method = '';
	$CI =& get_instance();
	if(config_item('enable_query_strings')){
		$m = config_item('function_trigger');
		$method = $CI->input->get($m, TRUE) ? $CI->input->get($m, TRUE) : 'index';
	}else{
		$method = $CI->uri->rsegment(2);
	}
	return $method;
}

/**
 * 比较2个字符串的区别
 * @param string $text1 
 * @param string $text2
 * @return string
 */
function str_diff($text1, $text2){
    $text1 = str_replace('&nbsp;', '', trim($text1));
    $text2 = str_replace('&nbsp;', '', trim($text2));
    $w  = explode("\n", $text1);
    $o  = explode("\n", $text2);
    $w1 = array_diff_assoc($w,$o);
    $o1 = array_diff_assoc($o,$w);
    $w2 = array();
    $o2 = array();
    foreach($w1 as $idx => $val) $w2[sprintf("%03d<",$idx)] = sprintf("%03d- ", $idx+1) . "<del>" . trim($val) . "</del>";
    foreach($o1 as $idx => $val) $o2[sprintf("%03d>",$idx)] = sprintf("%03d+ ", $idx+1) . "<ins>" . trim($val) . "</ins>";
    $diff = array_merge($w2, $o2);
    ksort($diff);
    return implode("\n", $diff);
}

/**
 * 递归创建文件夹
 * @return void
 */
if ( ! function_exists('mkdirs')){
	function mkdirs($path){
		if(is_dir($path)){
			return;
		}
		if (!file_exists($path)){
			mkdirs(dirname($path));
			@mkdir($path, DIR_WRITE_MODE);
		}
	}
}

/**
 * 生成唯一文件名
 * @return string 唯一字符串
 */
if ( ! function_exists('unique_file_name')){
	function unique_file_name(){
		return strtoupper(md5(uniqid()));
	}
}

/**
 * 处理上传$_FILES, 以支持多文件上传
 * @param string $field 文件上传的POST参数名
 * @param string $label 自定义的文件标题POST参数名
 * @return void
 */
function multifile_array($field = 'userfile', $label = 'labels') {
    if(count($_FILES) == 0)
        return;

    $files = array();
    $CI =& get_instance();
    $label = $CI->input->post($label, TRUE);
    $label = empty($label) ? array() : (is_array($label) ? $label : array((string)$label));

    if(is_string($_FILES[$field]['name'])){
    	$_FILES[$field]['name'] = array($_FILES[$field]['name']);
    	$_FILES[$field]['type'] = array($_FILES[$field]['type']);
    	$_FILES[$field]['tmp_name'] = array($_FILES[$field]['tmp_name']);
    	$_FILES[$field]['error'] = array($_FILES[$field]['error']);
    	$_FILES[$field]['size'] = array($_FILES[$field]['size']);
    }

    $all_files = $_FILES[$field]['name'];
    $i = 0;

    foreach ($all_files as $filename) {
    	if(empty($filename)){
    		next($_FILES[$field]['type']);
    		next($_FILES[$field]['tmp_name']);
    		next($_FILES[$field]['error']);
    		next($_FILES[$field]['size']);
    		next($label);
    		$i++;
    		continue;
    	}
        $files[$i]['name'] = $filename;
        $files[$i]['type'] = current($_FILES[$field]['type']);
        next($_FILES[$field]['type']);
        $files[$i]['tmp_name'] = current($_FILES[$field]['tmp_name']);
        next($_FILES[$field]['tmp_name']);
        $files[$i]['error'] = current($_FILES[$field]['error']);
        next($_FILES[$field]['error']);
        $files[$i]['size'] = current($_FILES[$field]['size']);
        next($_FILES[$field]['size']);
        $files[$i]['label'] = isset($label[$i]) ? $label[$i] : '';
        $i++;
    }

    $_FILES = $files;
}

/**
 * 上传文件, 支持多文件上传
 * @param string $field 文件上传的POST参数名
 * @param string $label 自定义的文件标题POST参数名
 * @return array 上传结果
 */
function do_upload($field = 'userfile', $label = 'labels'){
	$CI =& get_instance();
	$img_upload_path = $CI->config->item('upload_path');
	$allowed_types = $CI->config->item('allowed_types');
	$allowed_types = empty($allowed_types)?'*':$allowed_types;
	$max_size = $CI->config->item('max_size');
	$max_size = empty($max_size)?10240:$max_size;

	$target_path = $img_upload_path.date('Ym');
	mkdirs($target_path);
	$config = array(
		'upload_path' => $target_path,
		'allowed_types' => $allowed_types,
		'max_size' => $max_size,
		'file_name'=> unique_file_name()
	);
	$CI->load->library('upload', $config);
	$data = array();
	multifile_array($field, $label);
	foreach ($_FILES as $file => $file_data) {
		if (!$CI->upload->do_upload($file)){
			$data['errors'][] = $CI->upload->display_errors();
		}else{
			$fileinfo = $CI->upload->data();
			$fileinfo['relative_path'] = '/'.$target_path.'/'.$fileinfo['file_name'];
			$fileinfo['url'] = config_item('base_url').$fileinfo['relative_path'];
			$fileinfo['label'] = $file_data['label'];
			$data['files'][] = $fileinfo;
		}
	}
	return $data;
}

/**
 * 是否具有某权限
 * @param string $power 权限名
 * @param boolean $live 是否事实验证(查询数据库)
 * @return boolean 是否通过
 */
function has_permission($power, $live = FALSE){
	if($_SESSION['userinfo']['id'] == 1) {
		return TRUE;
	}
	if($live){
		// 实时检测
		$CI =& get_instance();
		$CI->load->model('rbac_model');
		return $CI->rbac_model->check_user_access($_SESSION['userinfo']['id'], $power);
	}
	// 通过验证SESSION来检测
	session_id() || session_start();
	$user_powers = empty($_SESSION['powers'])?array():$_SESSION['powers'];
	return in_array(trim($power), $user_powers);
}

/**
 * 消息提示页面
 * @param  string $type        提示类型
 * @param  string $msg         提示信息
 * @param  string $description 提示信息详情
 * @param  array $back_urls    需要返回的链接数组
 * @return void
 */
function show_msg($type = 'info', $msg, $description = '', $back_urls = NULL){
	$CI =& get_instance();
	switch (trim(strtolower($type))) {
		case INFO:
			$data['caption'] = '系统提示';
			break;
		case WARNING:
			$data['caption'] = '系统警告';
			break;
		case ERROR:
			$data['caption'] = '错误提示';
			break;
		default:
			$data['caption'] = '系统提示';
			break;
	}
	$data['message'] = $msg;
	$data['description'] = (string)$description;
	$data['back_urls'] = empty($back_urls) ? array(array('name'=>'返回上一页','url'=>$_SERVER['HTTP_REFERER'])) : $back_urls;
	echo $CI->layout->view('message', $data, TRUE);
	exit;
}

function is_admin(){
	$CI =& get_instance();
	$CI->load->model('rbac_model');
	$role = $CI->rbac_model->get_user_role($_SESSION['userinfo']['id']);
	return $role && $role->id == 1 ? TRUE : FALSE;
}