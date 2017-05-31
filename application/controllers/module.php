<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Module extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('module_model');
		$this->load->model('product_model');
	}

	public function index($parent_id = 0) {
		$products = $this->module_model->get_all_products();
		if(empty($products)){
			show_msg(WARNING, '请先添加产品');
		}
		$data['parent_id'] = (int)$parent_id;
		$data['modules'] = $this->module_model->get_product_modules();//所有
		$data['modules_select'] = $this->module_model->get_modules_select_html(0, 0, $parent_id);//当前
		$data['current_sub_modules'] = $this->module_model->get_sub_modules($parent_id);//当前子模块
		$data['modules_tree'] = $this->module_model->get_modules_tree_html();//树
		$data['product'] = $this->product_model->get_product_by_id();//当前产品
		$this->layout->view('module/module_view_page.php', $data);
	}

	/**
	 * 保存模块
	 */
	public function save() {
		$this->module_model->save_module();
	}

	public function delete($module_id){
		$this->module_model->delete($module_id);
	}

	public function edit($module_id){
		$data['cur_module'] = $this->module_model->get_module_by_id($module_id);
		$data['modules'] = $this->module_model->get_all_modules();
		$this->load->view('module/module_edit_page.php', $data);
	}

}