<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class My extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('common_model');
	}

	public function index(){
		$menu = config_item('menu');
		$pmsdata = config_item('pms');
		$url = 'my';
		foreach ($menu as $key => $val){
			if(has_permission($pmsdata['menu_show']['powers'][$key]['value'], TRUE)){
				if($key == 'statistics'){
					$url = 'statistics/overtime';
				}else if($key == 'ratting'){
					$url = 'ratting/userlist';
				}else if($key == 'sys'){
					$url = 'role';
				}else{
					$url = $key;
				}
		    	break;
		    }
		}
		if($url == 'my'){
			$data = $this->common_model->get_my_data();
			$this->layout->view('my_page', $data);
		}else{
			header('Location: /'.$url);
		}
		
	}
}