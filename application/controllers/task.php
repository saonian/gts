<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class task extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('task_model');
		$this->load->model('team_model');
		$this->load->model('story_model');
		$this->load->model('project_model');
	}

	public function index($page = 1) {
		$this->page($page);
	}

	/**
	 * 任务列表
	 */
	public function page($page = 1) {
		$body = $this->input->get('body');
		$data['body'] = $body;
		$data = $this->task_model->get_page($page, PAGE_SIZE, '/task/page');
		$data['all_user'] = $this->team_model->get_project_team();
		if($body){
			$this->layout->view('task/task_list_page', $data, FALSE, FALSE);
		}else{
			$this->layout->view('task/task_list_page', $data);
		}
	}

	/**
	 * 添加任务
	 */
	public function create() {
		$body = $this->input->get('body');
		$data['body'] = $body;
		$data['all_user'] = $this->team_model->get_project_team();
		$story_id = $this->uri->segment(3);
		$story_id = !empty($story_id)?(int)$story_id : (!empty($task)?$task->story_id:0);
		$data['story_id'] = $story_id;
		$data['story'] = $this->story_model->get_story_by_id($story_id);
		$data['all_story'] = $this->project_model->get_project_stories($data['story']->project_id);
		// $this->layout->view('task/task_add_page', $data);
		if($body){
			$this->layout->view('task/task_add_page', $data, FALSE, FALSE);
		}else{
			$this->layout->view('task/task_add_page', $data);
		}
	}

	/**
	 * 批量添加任务
	 */
	public function batch_create($story_id) {
		$body = $this->input->get('body');
		$data['body'] = $body;
		$data['stories'] = $this->story_model->get_project_stories();
		$data['current_story_id'] = $story_id;
		$data['current_story'] = $this->story_model->get_story_by_id($story_id);
		$data['all_user'] = $this->team_model->get_project_team();
		//$this->layout->view('task/task_batch_add_page', $data);
		if($body){
			$this->layout->view('task/task_batch_add_page', $data, FALSE, FALSE);
		}else{
			$this->layout->view('task/task_batch_add_page', $data);
		}

	}

	/**
	 * 保存任务
	 */
	public function save() {
		$action = $this->input->post('action');
		switch ($action) {
			case 'submittest':
				$this->task_model->submittest();
				break;
			case 'active':
				$this->task_model->active();
				break;
			case 'online':
				$this->task_model->online();
				break;
			default:
				$this->task_model->save_task();
				break;
		}
	}

	/**
	 * 任务详情
	 */
	public function view($task_id = 0) {
		$body = $this->input->get('body');
		$data['body'] = $body;
		$task_id = (int)$task_id;
		$data['task'] = $this->task_model->get_task_detail($task_id);
		if(empty($data['task'])){
			if($body){
				$this->alert_msg('数据不存在!');
			}else{
				show_404();
			}
		}
		//$this->layout->view('task/task_view_page', $data);
		if($body){
			$this->layout->view('task/task_view_page', $data, FALSE, FALSE);
		}else{
			$this->layout->view('task/task_view_page', $data);
		}
	}

	/**
	 * 编辑任务
	 */
	public function edit($task_id = 0) {
		$body = $this->input->get('body');
		$data['body'] = $body;
		$data['task'] = $this->task_model->get_task_detail($task_id);
		if(empty($data['task'])){
			if($body){
				$this->alert_msg('数据不存在!');
			}else{
				show_404();
			}
		}
		$data['all_user'] = $this->team_model->get_project_team();
		$data['all_story'] = $this->project_model->get_project_stories();
		//$this->layout->view('task/task_edit_page', $data);
		if($body){
			$this->layout->view('task/task_edit_page', $data, FALSE, FALSE);
		}else{
			$this->layout->view('task/task_edit_page', $data);
		}
	}

	/**
	 * 指派任务
	 */
	public function assign($task_id = 0) {
		$body = $this->input->get('body');
		$data['body'] = $body;
		$data['task'] = $this->task_model->get_task_by_id($task_id);
		if(empty($data['task'])){
			if($body){
				$this->alert_msg('数据不存在!');
			}else{
				show_404();
			}
		}
		$data['all_user'] = $this->team_model->get_project_team();
		//$this->layout->view('task/task_assign_page', $data);
		if($body){
			$this->layout->view('task/task_assign_page', $data, FALSE, FALSE);
		}else{
			$this->layout->view('task/task_assign_page', $data);
		}
	}

	/**
	 * 开始任务
	 */
	public function start($task_id = 0) {
		$this->task_model->start($task_id);
	}

	/**
	 * 提交测试
	 */
	public function submittest($task_id = 0) {
		$body = $this->input->get('body');
		$data['body'] = $body;
		$data['task'] = $this->task_model->get_task_by_id($task_id);
		if(empty($data['task'])){
			if($body){
				$this->alert_msg('数据不存在!');
			}else{
				show_404();
			}
		}
		$data['all_user'] = $this->team_model->get_project_team();
		//$this->layout->view('task/task_finish_page', $data);
		if($body){
			$this->layout->view('task/task_finish_page', $data, FALSE, FALSE);
		}else{
			$this->layout->view('task/task_finish_page', $data);
		}
	}

	/**
	 * 激活任务
	 */
	public function active($task_id = 0) {
		$body = $this->input->get('body');
		$data['body'] = $body;
		$data['task'] = $this->task_model->get_task_by_id($task_id);
		if(empty($data['task'])){
			if($body){
				$this->alert_msg('数据不存在!');
			}else{
				show_404();
			}
		}
		$data['all_user'] = $this->team_model->get_project_team();
		//$this->layout->view('task/task_active_page', $data);
		if($body){
			$this->layout->view('task/task_active_page', $data, FALSE, FALSE);
		}else{
			$this->layout->view('task/task_active_page', $data);
		}
	}

	/**
	 * 关闭任务
	 */
	public function close($task_id = 0) {
		$this->task_model->close($task_id);
	}

	/**
	 * 取消任务
	 */
	public function cancel($task_id = 0) {
		$this->task_model->cancel($task_id);
	}

	/**
	 * 删除任务
	 */
	public function delete($task_id = 0) {
		$body = $this->input->get('body');
		$data['body'] = $body;
		$data['task'] = $this->task_model->get_task_detail($task_id);
		// print_r($data['task']);exit;
		if(empty($data['task'])){
			if($body){
				$this->alert_msg('数据不存在!');
			}else{
				show_404();
			}
		}
		//$this->layout->view('task/task_delete_page', $data);
		if($body){
			$this->layout->view('task/task_delete_page', $data, FALSE, FALSE);
		}else{
			$this->layout->view('task/task_delete_page', $data);
		}
	}

	/**
	 * 批量编辑任务
	 */
	public function batch_edit() {
		$body = $this->input->get('body');
		$data['body'] = $body;
		$data['tasks'] = array();
		$story_id_arr = $this->input->post('task_id');
		foreach ($story_id_arr as $val) {
			$data['tasks'][] = $this->task_model->get_task_by_id($val); 
		}
		$data['all_user'] = $this->team_model->get_project_team();
		//$this->layout->view('task/task_batch_edit_page', $data);
		if($body){
			$this->layout->view('task/task_batch_edit_page', $data, FALSE, FALSE);
		}else{
			$this->layout->view('task/task_batch_edit_page', $data);
		}
	}

	/**
	 * 批量关闭任务
	 */
	public function batch_close() {
		$body = $this->input->get('body');
		$data['body'] = $body;
		$data['tasks'] = array();
		$story_id_arr = $this->input->post('task_id');
		foreach ($story_id_arr as $val) {
			$data['tasks'][] = $this->task_model->get_task_by_id($val); 
		}
		//$this->layout->view('task/task_batch_close_page', $data);
		if($body){
			$this->layout->view('task/task_batch_close_page', $data, FALSE, FALSE);
		}else{
			$this->layout->view('task/task_batch_close_page', $data);
		}
	}

	/**
	 * 审核测试
	 */
	public function verifytest($task_id){
		$this->task_model->verifytest($task_id);
	}

	/**
	 * 审核通过
	 */
	public function verifyok($task_id){
		$this->task_model->verifyok($task_id);
	}

	/**
	 * 开始测试
	 */
	public function starttest($task_id){
		$this->task_model->starttest($task_id);
	}

	/**
	 * 完成测试
	 */
	public function finishtest($task_id){
		$this->task_model->finishtest($task_id);
	}

	/**
	 * 任务上线
	 */
	public function online($task_id){
		$body = $this->input->get('body');
		$data['body'] = $body;
		$task = $this->task_model->get_task_by_id($task_id);
		if(!$task){
			if($body){
				$this->alert_msg('数据不存在!');
			}else{
				show_404();
			}
		}
		$this->task_model->online($task_id);
		// if($task->need_test == 0){
		// 	$data['all_user'] = $this->team_model->get_project_team();
		// 	$data['is_online'] = TRUE;
		// 	$data['task'] = $task;
		// 	//$this->layout->view('task/task_finish_page', $data);
		// 	if($body){
		// 		$this->layout->view('task/task_finish_page', $data, FALSE, FALSE);
		// 	}else{
		// 		$this->layout->view('task/task_finish_page', $data);
		// 	}
		// }else{
		// 	$this->task_model->online($task_id);
		// }
	}

	/**
	 * 获取指派人未完成的任务数
	 */
	public function get_unfinished_task_count($uid){
		echo count($this->task_model->get_unfinished_task($uid));
	}

	public function alert_msg($msg){
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        echo "<script type='text/javascript'>alert('".$msg."');history.go(-1);</script>";
        exit();
    }
}