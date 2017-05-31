<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Role extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('rbac_model');
	}

	public function index(){
		$this->page(1);
	}

	/**
	 * 角色列表
	 */
	public function page($page = 1){
		$data['roles'] = $this->rbac_model->get_roles();
		$this->layout->view('rbac/role_list_page.php', $data);
	}

	/**
	 * 添加角色
	 */
	public function create(){
		$data['powers'] = array();
		$this->layout->view('rbac/role_edit_page.php', $data);
	}

	/**
	 * 编辑角色
	 */
	public function edit($role_id = 1){
		$data['role'] = $this->rbac_model->get_role_by_id($role_id);
		$data['powers'] = array();
		$this->rbac_model->get_item_operations($role_id, $data['powers']);
		$this->layout->view('rbac/role_edit_page.php', $data);
	}

	/**
	 * 保存角色
	 */
	public function save(){
		$data['role'] = $this->rbac_model->save_role();
		header('Location: /role');
	}

	/**
	 * 删除角色(数据库配置了外键级联, 当删除角色的时候, 该角色关联的用户也会删除)
	 */
	public function delete($role_id = NULL){
		$this->rbac_model->delete_role($role_id);
		header('Location: /role');
	}
}