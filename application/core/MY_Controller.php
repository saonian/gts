<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

	protected $pmsdata = array();

	public function __construct() {
		parent::__construct();
		$this->pmsdata = $this->config->item('pms');
	}
	
}