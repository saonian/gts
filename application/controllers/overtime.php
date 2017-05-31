<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Overtime extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('overtime_model');
		$this->load->model('task_model');
		// $this->load->model('duty_model');
	}

	/**
	 * 任务列表
	 */
	public function index($page = 1) {
		$Params = array();
		$Params['begin'] = isset($_REQUEST['begin'])?trim($_REQUEST['begin']):'';
		$Params['end'] = isset($_REQUEST['end'])?trim($_REQUEST['end']):'';
		$Params['audit_status'] = isset($_REQUEST['audit_status'])?trim($_REQUEST['audit_status']):'';
		$Params['search_type'] = isset($_REQUEST['search_type'])?trim($_REQUEST['search_type']):'';
		$Params['keyword'] = isset($_REQUEST['keyword'])?trim($_REQUEST['keyword']):'';
		$Params['page'] = $page;
		$Params['page_size'] = PAGE_SIZE;
		$searchs = $this->overtime_model->index_search($Params);
		$data['Params'] = $Params;
		$data['datas'] = $searchs['data'];
		$data['total'] = $searchs['total'];
		$data['pass_hours'] = $searchs['pass_hours'];
		$data['unpass_hours'] = $searchs['unpass_hours'];
		$data['reject_hours'] = $searchs['reject_hours'];
		$data['total_hours'] = $searchs['total_hours'];
		$data['page_html'] = $searchs['page_html'];
		$this->layout->view('overtime/index', $data);
	}


	/**
	 * 值班管理
	 */
	public function duty($page = 1)
	{
		$Params = array();
		$Params['begin'] = isset($_REQUEST['begin'])?trim($_REQUEST['begin']):'';
		$Params['end'] = isset($_REQUEST['end'])?trim($_REQUEST['end']):'';
		$Params['duty_status'] = isset($_REQUEST['duty_status'])?trim($_REQUEST['duty_status']):'';
		$Params['page'] = $page;
		$Params['page_size'] = PAGE_SIZE;
		$searchs = $this->duty_model->duty_search($Params);
		$data['Params'] = $Params;
		$data['datas'] = $searchs['data'];
		$data['page_html'] = $searchs['page_html'];
		$this->layout->view('overtime/duty', $data);
	}

	/**
	 * 我要值班页面
	 */
	public function selectduty()
	{
		$data = $this->duty_model->get_duty_infos();
		$this->load->vars('data',$data);
		$this->layout->view('/overtime/selectduty');
	}

	/**
	 * 提交值班处理
	 */
	public function selectduty_form(){
		$Params['support_content'] = $_POST['support_content'];
		$Params['support_range'] = $_POST['support_range'];
		$Params['is_update'] = $_POST['is_update'];
		$Params['user_id'] = $_POST['user_id'];
		$Params['user'] = $_SESSION['userinfo']['id'];
		$Params['loaduser'] =  $_POST['user'];
		//$Params['isadmin'] = $_SESSION['userinfo']['isadmin'];
		$status = $this->duty_model->add_update_duty($Params);
		if($status){
			echo 1;
		}else{
			echo 0;
		}
	}

	/**
	 * 申请页面
	 */

	public function add() {
		$stories = $this->overtime_model->get_stories();
		$id = isset($_REQUEST['id'])?$_REQUEST['id']:'';
		if($id!=''){
			$data = $this->overtime_model->get_overtime_infos($id);
			$temp = explode(' ',$data['begin']);
			$data['date_b'] = $temp[0];
			$tempp = explode(':',$temp[1]);
			$data['hour_b'] = $tempp[0];
			$data['minutes_b'] = $tempp[1];

			$temp = explode(' ',$data['end']);
			$data['date_e'] = $temp[0];
			$tempp = explode(':',$temp[1]);
			$data['hour_e'] = $tempp[0];
			$data['minutes_e'] = $tempp[1];
			$this->load->vars('data',$data);
		}
		$this->load->vars('stories',$stories);
		$this->layout->view('/overtime/add');

	}


	/**
	 * 申请处理
	 */
	public function add_form(){
		$Params['task'] = $_POST['task'];
		$Params['overtime_time'] = $_POST['overtime_time'];
		if(empty($_POST['date_b'])){
			$Params['begin'] = date('Y-m-d').' '.trim($_POST['hour_b']).':'.trim($_POST['minutes_b']);
		}else{
			$Params['begin'] = trim($_POST['date_b']).' '.trim($_POST['hour_b']).':'.trim($_POST['minutes_b']);
		}
		if(empty($_POST['date_e'])){
			$Params['end'] = date('Y-m-d').' '.trim($_POST['hour_e']).':'.trim($_POST['minutes_e']);
		}else{
			$Params['end'] = trim($_POST['date_e']).' '.trim($_POST['hour_e']).':'.trim($_POST['minutes_e']);
		}
		$Params['hour_counts'] = trim($_POST['hour_counts']);
		$Params['reason'] = $_POST['reason'];
		$Params['is_days_off'] = isset($_POST['is_days_off'])?$_POST['is_days_off']:'';
		$Params['ids'] = $_POST['ids'];
		$Params['user'] = $_SESSION['userinfo']['id'];
		//$Params['isadmin'] = $_SESSION['userinfo']['isadmin'];
		$status = $this->overtime_model->add_update_overtime_order($Params);
		if($status){
			header('location:/overtime/index');
		}


	}

	public function force()
	{
		$id = $_REQUEST['uid'];
		$this->db->query("update gg_duty set end_time=now(),duty_status='1' where id='$id'");
		header('location:/overtime/duty');
	}

	/**
	 * 审核
	 */
	public function shenhe(){
		$id = $_REQUEST['id'];
		$data = $this->overtime_model->get_overtime_infos($id);
		$tasks = $this->task_model->get_task_by_id($data['task_id']);
		$data['task'] = isset($tasks->name)?$tasks->name:'';
		$this->load->vars('data',$data);
		$this->layout->view('/overtime/shenhe');
	}

	public function shenhe_form(){
		$Params = $_POST;
		$Params['user'] = $_SESSION['userinfo']['id'];
		$status = $this->overtime_model->shenhe($Params);
		if($status){
			header('location:/overtime/index');
		}
	}

	public function delete(){
		$id = $_REQUEST['id'];
		$status = $this->overtime_model->delete_overtime($id);
		if($status){
			echo '删除成功';
		}else{
			echo '删除失败';
		}

	}

	public function view(){
		$this->load->model('task_model');
		$id = $_REQUEST['id'];
		$data = $this->overtime_model->get_overtime_infos($id);
		$this->load->vars('data',$data);
		$this->layout->view('/overtime/shenhe');
	}

}