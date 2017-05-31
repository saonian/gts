<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Product extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('product_model');
		$this->load->model('team_model');
		$this->load->model('user_model');
		$this->load->model('module_model');
	}

	public function index($page = 1) {
		$this->page($page);
	}

	/**
	 * 产品列表
	 */
	public function page($page = 1) {
		$body = $this->input->get('body');
		$data['body'] = $body;
		$data = $this->product_model->get_page($page, PAGE_SIZE, '/product/page');
		//$this->layout->view('product/product_list_page', $data);

		if($body){
			$this->layout->view('product/product_list_page', $data, FALSE, FALSE);
		}else{
			$this->layout->view('product/product_list_page', $data);
		}
	}

	/**
	 * 添加产品
	 */
	public function create() {
		// $data['verify_users'] = $this->team_model->get_team_verify_users();
		$data['verify_users'] = $this->user_model->get_all_users();

		$body = $this->input->get('body');
		$data['body'] = $body;
		//$this->layout->view('product/product_add_page', $data);
		if($body){
			$this->layout->view('product/product_add_page', $data, FALSE, FALSE);
		}else{
			$this->layout->view('product/product_add_page', $data);
		}
	}

	/**
	 * 保存产品
	 */
	public function save() {
		$this->product_model->save_product();
	}

	/**
	 * 产品详情
	 */
	public function view($product_id = 0) {
		$body = $this->input->get('body');
		$data['body'] = $body;
		$data['product'] = $this->product_model->get_product_detail((int)$product_id);
		if(empty($data['product'])){
			if($body){
				$this->alert_msg('数据不存在!');
			}else{
				show_404();
			}
		}
		//$this->layout->view('product/product_view_page', $data);
		if($body){
			$this->layout->view('product/product_view_page', $data, FALSE, FALSE);
		}else{
			$this->layout->view('product/product_view_page', $data);
		}
	}

	/**
	 * 编辑产品
	 */
	public function edit($product_id = 0) {
		$body = $this->input->get('body');
		$data['body'] = $body;
		$data['product'] = $this->product_model->get_product_detail($product_id);
		if(empty($data['product'])){
			//show_404();
			if($body){
				$this->alert_msg('数据不存在!');
			}else{
				show_404();
			}
		}
		$data['verify_users'] = $this->user_model->get_all_users();
		//$this->layout->view('product/product_add_page', $data);
		if($body){
			$this->layout->view('product/product_add_page', $data, FALSE, FALSE);
		}else{
			$this->layout->view('product/product_add_page', $data);
		}
	}

	public function story($module_id = 0){
		$body = $this->input->get('body');
		$data['body'] = $body;
		$data['product'] = $this->product_model->get_product_by_id();
		$data['modules_tree'] = $this->module_model->get_modules_tree_html(0, 0, FALSE);//树
		//$this->layout->view('product/product_story_page', $data);
		if($body){
			$this->layout->view('product/product_story_page', $data, FALSE, FALSE);
		}else{
			$this->layout->view('product/product_story_page', $data);
		}
	}

	public function alert_msg($msg){
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        echo "<script type='text/javascript'>alert('".$msg."');history.go(-1);</script>";
        exit();
    }
}