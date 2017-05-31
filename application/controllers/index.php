<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Index extends MY_Controller {

	public function __construct(){
		parent::__construct();
	}

	public function index(){
		// $this->load->view('welcome_message');
		$this->layout->view('welcome_message');
	}

	public function checksso(){
		$sid = $this->input->get('sid', TRUE);
		$url = base64_decode($this->input->get('url'));
		$rs = file_get_contents(config_item('sso_server')."/login/index/checksso/?&sid=".$sid);
		if(trim($rs) == 'fbd'){
			die('fbidden');
		}
		$obj = json_decode(base64_decode($rs));
		$userinfo = (array)$obj;
		$this->load->model('user_model');
		$this->load->model('rbac_model');
		$this->load->model('account_model');

		//同步一次用户，以免获取不到新用户
		$this->account_model->syn_users();

		$userinfo = (array)$this->user_model->get_user_by_account($userinfo['username']);
		if(empty($userinfo)){
			show_msg('ERROR', '用户名或密码错误');
		}
		$_SESSION['userinfo'] = $userinfo;
		$role = $this->rbac_model->get_user_role($userinfo['id']);
		$_SESSION['userinfo']['role'] = $role;
		// 不使用用户中心的is_admin字段来判断
		$_SESSION['userinfo']['is_admin'] = empty($role) ? 0 : (($role->id == 1)?1:0);
		$_SESSION['powers'] = array();
		$this->rbac_model->get_user_operations($userinfo['id'], $_SESSION['powers']);
		header('Location: '.$url);
	}

	public function login(){
		$username = $this->input->post('username', TRUE);
		$password = $this->input->post('password', TRUE);
		$password = md5(substr(md5($password), 10));

		$userinfo = (array)$this->user_model->attempt($username, $password);
		if(empty($userinfo)){
			show_msg('ERROR', '用户名或密码错误');
		}
		$_SESSION['userinfo'] = $userinfo;
		$role = $this->rbac_model->get_user_role($userinfo['id']);
		$_SESSION['userinfo']['role'] = $role;
		// 不使用用户中心的is_admin字段来判断
		$_SESSION['userinfo']['is_admin'] = empty($role) ? 0 : (($role->id == 1)?1:0);
		$_SESSION['powers'] = array();
		$this->rbac_model->get_user_operations($userinfo['id'], $_SESSION['powers']);

		// print_r($_SESSION['userinfo']);exit;
		header('Location: '.'http://'.$_SERVER['HTTP_HOST'].'/my');
	}

	public function show_login(){
		include('application/views/login.php');
	}

	public function logout(){
		session_destroy();
		setcookie('current_project_id', '', time() - 30*24*60*60, '/');
		header('location:'.'http://'.$_SERVER['HTTP_HOST'].'/login');
	}

	public function upload4kindeditor(){
		$result = do_upload('imgFile');
		if(empty($result['errors'])){
			die(json_encode(array('error'=>0, 'url'=>$result['files'][0]['url'])));
		}else{
			die(json_encode(array('error'=>1, 'message'=>strip_tags($result['errors'][0]))));
		}
	}

	public function download($file_id = 0){
		$this->load->model('common_model');
		$this->common_model->download($file_id);
	}

	public function delfile($file_id = 0){
		$this->load->model('common_model');
		$this->common_model->delete_file($file_id);
	}

	public function usernamedata(){
		$q = $this->input->get('q');
		$this->load->driver('cache', array('adapter' => 'file'));
		if (!$usernamedata = $this->cache->get('usernamedata')){
			$this->load->model('user_model');
			$allusers = $this->user_model->get_all_users();
			$usernamedata = array();
			foreach ($allusers as $key => $val) {
				$usernamedata[] = $val->account;
				$usernamedata[] = $val->real_name;
			}
		    $this->cache->file->save('usernamedata', $usernamedata, 24*60*60);
		}
		foreach ($usernamedata as $key => $val) {
			if (strpos($val, $q) !== FALSE) {
				echo "$val\n";
			}
		}
	}
	public function real_usernamedata(){
		$q = $this->input->get('q');
		$this->load->driver('cache', array('adapter' => 'file'));
		if (!$usernamedata = $this->cache->get('real_usernamedata')){
			$this->load->model('user_model');
			$allusers = $this->user_model->get_all_users();
			$usernamedata = array();
			foreach ($allusers as $key => $val) {
				//$usernamedata[] = $val->account;
				$usernamedata[$key]['real_name'] = $val->real_name;
				$usernamedata[$key]['uid'] = $val->id;
			}
		    $this->cache->file->save('real_usernamedata', $usernamedata, 24*60*60);
		}
		foreach ($usernamedata as $key => $val) {
			if (strpos($val['real_name'], $q) !== FALSE) {
				echo $val['real_name']."-".$val['uid']."\n";
			}
		}
	}

	public function paste_img(){
		$data = $this->input->post('editor');
		$data = str_replace('\"', '"', $data);
		// 调整preg_match/preg_match_all的字符串长度限制避免匹配不上
		ini_set('pcre.backtrack_limit', strlen($data));
		preg_match_all('/<img src="(data:image\/(\S+);base64,(\S+))" .+ \/>/U', $data, $out);

		$img_upload_path = $this->config->item('upload_path');
		$target_path = $img_upload_path.date('Ym');
		mkdirs($target_path);

		foreach($out[3] as $key => $base64Image){
		    $imageData = base64_decode($base64Image);

		    $file_path = '/'.$target_path.'/'.unique_file_name().'.'.$out[2][$key];
		    $file = array(
		    	'path' => $file_path,
		    	'title' => basename($file_path),
		    	'extension' => '.'.$out[2][$key],
		    	'size' => strlen($imageData),
		    	'type' => '',
		    	'object_id' => 0,
		    	'added_by' => $_SESSION['userinfo']['id'],
		    	'added_date' => date('Y-m-d H:i:s'),
		    	'downloads' => 0,
		    	'extra' => '',
		    	'is_deleted' => '0'
		    );
		    // print_r($file);exit;
		    $result = file_put_contents(ltrim($file_path, '/'), $imageData);
		    if($result){
		    	$this->db->insert(TBL_FILE, $file);
		    }

		    $data = str_replace($out[1][$key], $file_path, $data);
		}

		echo $data;
	}
}
