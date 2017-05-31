<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Jishu extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('jishu_model');
	}
	
	public function  index()
	{
		$data = $this->jishu_model->index_list();
		$this->layout->view('jishu/jishu_page', $data, FALSE, FALSE);
	}
	
}
?>