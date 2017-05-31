<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Department extends MY_Controller {

	public $is_enable = array('0'=>'停用','1'=>'启用');
	
	public function __construct(){
		parent::__construct();
		$this->load->model('department_model');
	}

	
	/**
	 * 部门列表
	 */
	public function index($page = 1) {
		$params = array();
		$params['search_date'] = $this->input->post('search_date');		
		$params['start'] = $this->input->post('start');		
		$params['end'] = $this->input->post('end');		
		$params['is_enable'] = isset($_POST['is_enable']) ? trim($_POST['is_enable']) : 1;		
		$params['search_type'] = $this->input->post('search_type');		
		$params['keyword'] = $this->input->post('keyword');		
		
		$department_list = $this->department_model->department_list($params,$page);

		$this->load->vars('params',$params);
		$this->load->vars('department_list',$department_list);
		$this->load->vars('is_enable',$this->is_enable);
		$this->layout->view('department/department_list_page');
	}
	
	
	
	/**
	 * 部门添加
	 * Enter description here ...
	 */
	public function department_add(){
		$params = array();
		$params['level1'] = $this->input->post('level1');
		$params['level2'] = $this->input->post('level2');
		$params['level3'] = $this->input->post('level3');
		$params['level4'] = $this->input->post('level4');
		$params['is_enable'] = $this->input->post('is_enable');

		if(is_array($params['level4']) && count($params['level4'])){
			$params['type'] = 4;
		}elseif(is_array($params['level3']) && count($params['level3'])){
			$params['type'] = 3;
		}elseif(is_array($params['level2']) && count($params['level2'])){
			$params['type'] = 2;
		}elseif(!empty($params['level1'])){
			$params['type'] = 1;
		}
		
		if(isset($params['type']) && $params['type'] >= 1){
			$flag = $this->department_model->department_add($params);
			if($flag == true){
				header("location:/department/index"); 
			}
		}

		$this->layout->view('department/department_add_page');
	}
	
	
	

	
	
	/**
	 * 部门查看详情
	 * Enter description here ...
	 */
	public function view(){
		$id = $this->input->get('id');
		if($id){
			$data = $this->department_model->department_info($id);
			
			$this->load->vars('is_enable',$this->is_enable);
			$this->load->vars('department_info',$data['info']);
			$this->load->vars('department_list',$data['list']);
			
		}
		$this->layout->view('department/department_info_page');
	}
	
	
	/**
	 * 获取子部门信息
	 */
	public function get_child_info(){
		$department_id = $this->input->post('department_id');
		$data = $this->department_model->get_child_info($department_id);
		echo json_encode($data);
	}
	
	
	
	/**
	 * 部门编辑(视图)
	 */
	public function edit(){
		$id = $this->input->get('id');
		if($id){
			$data = $this->department_model->department_edit($id);
			$this->load->vars('data',$data);
		}
		$this->layout->view('department/department_edit_page');
	}
	
	
	
	/**
	 * 部门编辑
	 */
	public function department_update(){
		$params = array();
		$params['level'] = $this->input->post('level');
		$params['department_names'] = $this->input->post('names');
		$params['department_is_enable'] = $this->input->post('is_enable');
		$params['new_department'] = $this->input->post('new_department');	//新增部门名称
		$params['is_enable_new'] = $this->input->post('is_enable_new');		//新增部门状态
		$params['cut_id'] = $this->input->post('cut_id');					//删除id
		
		$flag = $this->department_model->department_update($params);
		if($flag){
			header("LOCATION:/department/index");
		}
	}
	
	
	/**
	 * 验证名称重复
	 */
	public function check_name(){
		$ids = $this->input->post('ids');
		$names = $this->input->post('names');
		$name = $this->input->post('name');
		
		if(is_array($ids) && count($ids)>0 && is_array($names) && count($names)>0 && count($ids)==count($names)){
			$data = $this->department_model->check_name($ids,$names,$type = 1);
			echo json_encode($data);
		}elseif(!empty($name)){
			$data = $this->department_model->check_name($ids,$name,$type = 2);
			echo $data;
		}
	}
	
	/**
	 * 删除该部门及其所有子部门
	 */
	public function del($id = NULL){
		if($id){
			$bool = $this->department_model->del($id);
			if($bool){
				header("LOCATION:/department/index");
			}
		}
	}

}