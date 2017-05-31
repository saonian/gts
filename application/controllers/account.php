<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Account extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('account_model');
		$this->load->model('department_model');
		$this->load->model('user_model');
		$this->load->model('rbac_model');
	}
	
	
	/**
	 * 帐户列表
	 */
	public function index($page = 1){
		// $this->account_model->syn_users();
		$params = array();
		$params['search_type'] = $this->input->get('search_type');		
		$params['department_id'] = $this->input->get('department_id');			
		$params['keyword'] = trim($this->input->get('keyword'));		

		$account = $this->account_model->account_list($params,$page);
		$parent_department = $this->department_model->parent_department_list();

		$this->load->vars('params',$params);
		$this->load->vars('account',$account);
		$this->load->vars('parent_department',$parent_department);
		$this->load->vars('is_admin',array('0'=>'否','1'=>'是'));
		$this->layout->view('account/account_list_page');
	}
	
	
	/**
	 * 设置用户部门和角色
	 * @param int $userid
	 */
	public function account_set($userid = NULL){
		$user_id = $this->input->post('user_id');
		$role_id = $this->input->post('role_id');
		$department_id = intval($this->input->post('department_id'));
		$email = $this->input->post('email');
		$phone = $this->input->post('phone');

		if($user_id && $role_id){
			$bool = $this->account_model->account_set($user_id,$role_id,$department_id,$email,$phone);
			if($bool){
				header('location:/account/index');
			}
		}
		
		if($userid){
			$get_role_list = $this->account_model->get_role_list();
			$department_list = $this->department_model->get_all_department_info();
			
			$user_info = $this->user_model->get_user_by_id($userid);
			$row = $this->rbac_model->get_item_by_userid($userid);
			
			$this->load->vars('role_id',!empty($row) ? $row->itemid : '');
			$this->load->vars('user_info',$user_info);
			$this->load->vars('get_role_list',$get_role_list);
			$this->load->vars('department_list',$department_list);
		}

		$this->layout->view('account/account_set_page');
	}

	public function add(){
		// $this->account_model->send_mail('liukai@rapoo.com', 'test', 'test');exit;
		$data['get_role_list'] = $this->account_model->get_role_list();
		$data['department_list'] = $this->department_model->get_all_department_info();

		$this->layout->view('account/account_add_page', $data);
	}

	public function save(){
		$user['account'] = $this->input->post('account');
		$user['real_name'] = $this->input->post('real_name');
		$user['password'] = $this->input->post('password');
		if(empty($user['password'])){
			unset($user['password']);
		}else{
			$user['password'] = md5(substr(md5($user['password']), 10));
		}
		$role_id = $this->input->post('role_id');
		$user['department_id'] = intval($this->input->post('department_id'));
		$user['email'] = $this->input->post('email');
		$user['phone'] = '';
		$user['join_date'] = date('Y-m-d H:i:s');
		$user_id = $this->input->post('user_id');

// print_r($user);exit;
		$this->db->trans_start();

		if($user_id){
			$this->db->where('id', $user_id);
			$this->db->update(TBL_USER, $user);
		}else{
			$this->db->insert(TBL_USER, $user);
			$user_id = $this->db->insert_id();
		}
		$this->account_model->account_set($user_id,$role_id,$user['department_id'],$user['email'],'');

		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE){
			show_msg(ERROR ,'保存失败');
		}
		header('location:/account');
	}

	public function edit($user_id){
		$data['get_role_list'] = $this->account_model->get_role_list();
		$data['department_list'] = $this->department_model->get_all_department_info();
		$data['user_info'] = $this->user_model->get_user_by_id($user_id);
		$role = $this->rbac_model->get_item_by_userid($user_id);
		$data['role_id'] = $role->itemid;
		$this->layout->view('account/account_add_page', $data);
	}
}
