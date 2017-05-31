<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bug extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('bug_model');
		$this->load->model('user_model');
		$this->load->model('team_model');
	}

	public function index($page=1) {
		$datas = $this->bug_model->get_page($page);
		$this->load->vars('datas',$datas);
		$this->layout->view('bug/bug_list_page');
	}
	
	public function create() {
		$id = $this->input->get('id');
		if($id){
			$res = $this->bug_model->get_bugs_by_id($id);
			$this->load->vars('res',$res); 
		}
		$data['all_user'] = $this->team_model->get_project_team();
		$stories = $this->bug_model->get_currentproject_stories();
		$tasks = $this->bug_model->get_currentproject_tasks();
		$this->load->vars('stories',$stories);
		$this->load->vars('tasks',$tasks);
		$this->load->vars('datas',$data['all_user']);

		$this->load->model('module_model');
		$data['all_products'] = $this->module_model->get_all_products();
		$all_modules = array();
		foreach ($data['all_products'] as $key => $val) {
			$all_modules[$val->id] = $this->module_model->get_all_modules($val->id);
		}
		$data['all_modules'] = json_encode($all_modules);
		$this->layout->view('bug/bug_add_page', $data);
	}
	
	public function save(){
		$result = $this->bug_model->save_bug();
		if($result){
			header('Location:/bug');
		}else{
			show_error('保存失败');
		}
	}
	
	public function assign(){
		$id = $this->input->get('id');
		$data = $this->bug_model->get_bugs_by_id($id);
		$user['all_user'] = $this->team_model->get_project_team();
		$this->load->vars('all_user',$user['all_user']);
		$this->load->vars('data',$data);
		$this->layout->view('bug/bug_assign_page');
	}
	
	public function assign_form(){
		$params['bug_id'] = $this->input->post('bug_id');
		$params['project_id'] = $this->input->post('project_id');
		$params['assigned_to'] = $this->input->post('assigned_to');
		$params['comment'] = $this->input->post('comment');
		$result = $this->bug_model->assign_save($params);
		if($result){
			header('Location:/bug');
		}else{
			show_error('保存失败');
		}
		
	}
	
	public function resolve(){
		$id = $this->input->get('id');
		$data = $this->bug_model->get_bugs_by_id($id);
		$user['all_user'] = $this->team_model->get_project_team();
		$this->load->vars('all_user',$user['all_user']);
		$this->load->vars('data',$data);
		$this->layout->view('bug/bug_resolved_page');
	}
	
	public function resolve_form(){
		$params['bug_id'] = $this->input->post('bug_id');
		$params['project_id'] = $this->input->post('project_id');
		$params['resolution'] = $this->input->post('resolution');
		$params['resolved_date'] = $this->input->post('resolved_date');
		$params['assigned_to'] = $this->input->post('assigned_to');
		$params['comment'] = $this->input->post('comment');
		$result = $this->bug_model->resolve_save($params);
		if($result){
			header('Location:/bug');
		}else{
			show_error('保存失败');
		}
	
	}
	public function close(){
		$id = $this->input->get('id');
		if(!empty($id)){
			$data = $this->bug_model->get_bugs_by_id($id);
			$this->load->vars('data',$data);
			$this->layout->view('bug/bug_close_page');
		}else{
			$params['bug_id'] = $this->input->post('bug_id');
			$params['project_id'] = $this->input->post('project_id');
			$params['comment'] = $this->input->post('comment');
			$result = $this->bug_model->close_save($params);
			if($result){
				header('Location:/bug');
			}else{
				show_error('保存失败');
			}		
		}	
	}
	
	public function detail(){
		$id = $this->input->get('id');
		$data = $this->bug_model->get_bug_detail($id);
		$this->load->vars('bug',$data);
		$this->layout->view('bug/bug_view_page');
		
	}
	
	public function remove(){
		$id = $this->input->get('id');
		if(!empty($id)){
			$data = $this->bug_model->get_bugs_by_id($id);
			$this->load->vars('data',$data);
			$this->layout->view('bug/bug_remove_page');
		}else{
			$params['bug_id'] = $this->input->post('bug_id');
			$params['project_id'] = $this->input->post('project_id');
			$params['comment'] = $this->input->post('comment');
			$result = $this->bug_model->remove_save($params);
			if($result){
				header('Location:/bug');
			}else{
				show_error('删除失败');
			}
		}
		
	}
}