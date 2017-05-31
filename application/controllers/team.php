<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Team extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('team_model');
		
	}
	
	/**
	 * 团队列表
	 */
	public function index(){
		$team_data = $this->team_model->team_list();
		$this->load->vars('team_data',$team_data);
		$this->layout->view('team/team_list_page');
	}
	
	
	/**
	 * 添加成员
	 */
	public function team_manage(){
		$this->load->model('department_model');
		$depts = $this->department_model->get_all_departments();
		$user_ids = $this->input->post('users');
		$roles = $this->input->post('roles');

		if(is_array($user_ids) && count($user_ids)>0 && is_array($roles) && count($roles)>0){
			$bool = $this->team_model->team_manage($user_ids,$roles);
			if($bool){
				header('location:/team/index');
			}
		}
		$user_list = $this->team_model->get_user_list();
		$this->load->vars('user_list',$user_list);
		$this->load->vars('departments',$depts);
		$this->layout->view('team/team_manage_page');
	}
	
	
	/**
	 * 删除团队人员
	 */
	public function team_del(){
		$project_id = $this->input->get('project_id');
		$user_id = $this->input->get('user_id');
		
		$bool = $this->team_model->team_del($project_id,$user_id);
		if($bool){
			header('location:/team/index');
		}
	}
	
	
	/**
	 * 添加成员时的下拉列表
	 */
	public function user_list_ajax($dpt_id = NULL){
		$user_list = $this->team_model->get_user_list($dpt_id);
		echo json_encode($user_list);
	}

	public function get_verify_users($pid = NULL){
		echo json_encode($this->team_model->get_team_verify_users($pid));

	}
}

?>