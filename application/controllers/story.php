<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Story extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('story_model');
		$this->load->model('team_model');
		$this->load->model('project_model');
	}

	public function index($page = 1) {
		$this->page($page);
	}

	/**
	 * 需求列表
	 */
	public function page($page = 1) {
		$data = $this->story_model->get_page($page, PAGE_SIZE, '/story/page');
		$data['all_creaters'] = $this->team_model->get_project_team();
		$this->load->model('rbac_model');
		foreach ($data['all_creaters'] as $key => $val) {
			$has_create_permission = $this->rbac_model->check_user_access($val->id, $this->pmsdata['story']['powers']['create']['value']);
			if(!$has_create_permission){
				unset($data['all_creaters'][$key]);
			}
		}
		$body = $this->input->get('body');
		$data['body'] = $body;
		$data['is_product'] = intval($_REQUEST['is_product']);
		if($body){
			$this->layout->view('story/story_list_page', $data, FALSE, FALSE);
		}else{
			$this->layout->view('story/story_list_page', $data);
		}
	}

	/**
	 * 添加需求
	 */
	public function create() {
		$this->load->model('grade_model');
		$can_create = $this->grade_model->has_ungrade_story();
		$body = $this->input->get('body');
		$data['body'] = $body;
		if(!$can_create){
			if($body){
				$this->alert_msg('你还有未评价/超过5天未关闭的需求，不能创建需求。');
			}else{
				show_msg(WARNING, '你还有未评价/超过5天未关闭的需求，不能创建需求。');
			}
			
		}
		$this->load->model('team_model');
		$this->load->model('module_model');
		$src = $this->input->get('src');
		$bug_id = $this->input->get('id');
		if($bug_id){
			$this->load->model('bug_model');
			$bug = $this->bug_model->get_bugs_by_id($bug_id);
			$data['bug'] = $bug;
		}
		$data['all_user'] = $this->team_model->get_team_verify_users();
		$data['src'] = $src?$src:'';

		if($body){
			$this->layout->view('story/story_add_page', $data, FALSE, FALSE);
		}else{
			$this->layout->view('story/story_add_page', $data);
		}
	}

	public function get_products_by_project(){
		$this->load->model('module_model');
		$project = intval($_REQUEST['project_id']);
		$arr = $this->project_model->get_products_in_project($project);
		$all_products= $this->module_model->get_all_products();
		$all_modules = array();
		foreach ($all_products as $key => $val){
			if(!in_array($val->id, $arr)){
				unset($all_products[$key]);
			}else{
				$all_modules[$val->id] = $this->module_model->get_all_modules($val->id);
			}
		}
		echo json_encode(array('products'=>$all_products,'modules'=>$all_modules));
	}

	/**
	 * 保存需求
	 */
	public function save() {
		$this->story_model->save_story();
	}

	/**
	 * 需求详情
	 */
	public function view($story_id = 0) {
		$body = $this->input->get('body');
		$data['body'] = $body;
		$story_id = (int)$story_id;
		$data['story'] = $this->story_model->get_story_detail($story_id);
		if(empty($data['story'])){
			if($body){
				$this->alert_msg('数据不存在!');
			}else{
				show_404();
			}
		}
		//$this->layout->view('story/story_view_page', $data);
		if($body){
			$this->layout->view('story/story_view_page', $data, FALSE, FALSE);
		}else{
			$this->layout->view('story/story_view_page', $data);
		}
	}

	/**
	 * 编辑需求
	 */
	public function edit($story_id = 0) {
		$data['story'] = $this->story_model->get_story_detail($story_id);
		$body = $this->input->get('body');
		$data['body'] = $body;
		if(empty($data['story'])){
			if($body){
				$this->alert_msg('数据不存在!');
			}else{
				show_404();
			}
		}
		$this->load->model('project_model');
		$data['all_projects'] = $this->project_model->get_all_projects();
		$data['all_user'] = $this->team_model->get_project_team();
		$data['verify_user'] = $this->user_model->get_user_by_item($this->pmsdata['story']['powers']['verify']['value']);
		$data['all_products'] = $this->module_model->get_all_products();
		$all_modules = array();
		foreach ($data['all_products'] as $key => $val) {
			$all_modules[$val->id] = $this->module_model->get_all_modules($val->id);
		}
		$data['all_modules'] = json_encode($all_modules);
		//$this->layout->view('story/story_edit_page', $data);
		if($body){
			$this->layout->view('story/story_edit_page', $data, FALSE, FALSE);
		}else{
			$this->layout->view('story/story_edit_page', $data);
		}
	}

	/**
	 * 变更需求
	 */
	public function change($story_id = 0) {
		$body = $this->input->get('body');
		$data['body'] = $body;
		$data['story'] = $this->story_model->get_story_detail($story_id);
		if(empty($data['story'])){
			if($body){
				$this->alert_msg('数据不存在!');
			}else{
				show_404();
			}
		}
		$data['all_user'] = $this->team_model->get_team_verify_users();
		// print_r($data);exit;
		//$this->layout->view('story/story_change_page', $data);
		if($body){
			$this->layout->view('story/story_change_page', $data, FALSE, FALSE);
		}else{
			$this->layout->view('story/story_change_page', $data);
		}
	}

	/**
	 * 指派需求
	 */
	public function assign($story_id = 0) {
		$body = $this->input->get('body');
		$data['body'] = $body;
		$data['story'] = $this->story_model->get_story_detail($story_id);
		if(empty($data['story'])){
			if($body){
				$this->alert_msg('数据不存在!');
			}else{
				show_404();
			}
		}
		$data['all_user'] = $this->team_model->get_team_verify_users();
		if($body){
			$this->layout->view('story/story_assign_page', $data, FALSE, FALSE);
		}else{
			$this->layout->view('story/story_assign_page', $data);
		}
	}

	public function assign_form(){
		$params['story_id'] = $this->input->post('story_id');
		$params['project_id'] = $this->input->post('project_id');
		$params['assigned_to'] = $this->input->post('assigned_to');
		$params['comment'] = $this->input->post('comment');
		$result = $this->story_model->assign_save($params);
		if($result){
			header('Location:/story?assignedtome=1&order=status&sort=asc');
		}else{
			show_error('保存失败');
		}
		
	}

	/**
	 * 审核需求
	 */
	public function verify($story_id = 0) {
		$body = $this->input->get('body');
		$data['body'] = $body;
		$data['story'] = $this->story_model->get_story_detail($story_id);
		if(empty($data['story'])){
			if($body){
				$this->alert_msg('数据不存在!');
			}else{
				show_404();
			}
		}
		$data['all_user'] = $this->team_model->get_project_team();
		//$this->layout->view('story/story_verify_page', $data);
		if($body){
			$this->layout->view('story/story_verify_page', $data, FALSE, FALSE);
		}else{
			$this->layout->view('story/story_verify_page', $data);
		}
	}

	/**
	 * 关闭需求
	 */
	public function close($story_id = 0) {
		$body = $this->input->get('body');
		$data['body'] = $body;
		$data['story'] = $this->story_model->get_story_by_id($story_id);
		if(empty($data['story'])){
			if($body){
				$this->alert_msg('数据不存在!');
			}else{
				show_404();
			}
		}
		//$this->layout->view('story/story_close_page', $data);
		if($body){
			$this->layout->view('story/story_close_page', $data, FALSE, FALSE);
		}else{
			$this->layout->view('story/story_close_page', $data);
		}
	}

	/**
	 * 激活需求
	 */
	public function active($story_id = 0) {
		$body = $this->input->get('body');
		$data['body'] = $body;
		$data['story'] = $this->story_model->get_story_by_id($story_id);
		if(empty($data['story'])){
			if($body){
				$this->alert_msg('数据不存在!');
			}else{
				show_404();
			}
		}
		$data['all_user'] = $this->team_model->get_project_team();
		//$this->layout->view('story/story_active_page', $data);
		if($body){
			$this->layout->view('story/story_active_page', $data, FALSE, FALSE);
		}else{
			$this->layout->view('story/story_active_page', $data);
		}
	}

	/**
	 * 复制需求
	 */
	public function copy($story_id = 0) {
		$body = $this->input->get('body');
		$data['body'] = $body;
		$data['story'] = $this->story_model->get_story_detail($story_id);
		if(empty($data['story'])){
			if($body){
				$this->alert_msg('数据不存在!');
			}else{
				show_404();
			}
		}
		$data['all_user'] = $this->team_model->get_project_team();
		//$this->layout->view('story/story_add_page', $data);
		if($body){
			$this->layout->view('story/story_add_page', $data, FALSE, FALSE);
		}else{
			$this->layout->view('story/story_add_page', $data);
		}
	}

	/**
	 * 删除需求
	 */
	public function delete($story_id = 0) {
		$body = $this->input->get('body');
		$data['body'] = $body;
		$data['story'] = $this->story_model->get_story_detail($story_id);
		if(empty($data['story'])){
			if($body){
				$this->alert_msg('数据不存在!');
			}else{
				show_404();
			}
		}
		//$this->layout->view('story/story_delete_page', $data);
		if($body){
			$this->layout->view('story/story_delete_page', $data, FALSE, FALSE);
		}else{
			$this->layout->view('story/story_delete_page', $data);
		}
	}

	/**
	 * 批量编辑需求
	 */
	public function batch_edit() {
		$body = $this->input->get('body');
		$data['body'] = $body;
		$data['stories'] = array();
		$story_id_arr = $this->input->post('story_id');
		// 需求下有任务之后就不能编辑了
		foreach ($story_id_arr as $val) {
			$taskcount = $this->story_model->get_story_task_count($val);
			if($taskcount > 0){
				continue;
			}
			$data['stories'][] = $this->story_model->get_story_by_id($val); 
		}
		//$this->layout->view('story/story_batch_edit_page', $data);
		if($body){
			$this->layout->view('story/story_batch_edit_page', $data, FALSE, FALSE);
		}else{
			$this->layout->view('story/story_batch_edit_page', $data);
		}
	}

	/**
	 * 批量关闭需求
	 */
	public function batch_close() {
		$body = $this->input->get('body');
		$data['body'] = $body;
		$data['stories'] = array();
		$story_id_arr = $this->input->post('story_id');
		foreach ($story_id_arr as $val) {
			$data['stories'][] = $this->story_model->get_story_by_id($val); 
		}
		//$this->layout->view('story/story_batch_close_page', $data);
		if($body){
			$this->layout->view('story/story_batch_close_page', $data, FALSE, FALSE);
		}else{
			$this->layout->view('story/story_batch_close_page', $data);
		}
	}

	public function get_story($sid){
		$story = $this->story_model->get_story_detail($sid);
		echo json_encode($story);
		exit;
	}

	public function alert_msg($msg){
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        echo "<script type='text/javascript'>alert('".$msg."');history.go(-1);</script>";
        exit();
    }
}