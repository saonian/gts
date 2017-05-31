<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *@author liuqingling
 *date 2014-08-25
 * 用户信息设置
 */
class Userinfo extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
    }

    public function infoset(){
        // $user = $this->user_model->get_user_by_id($_SESSION['userinfo']['id']);
        // $this->load->vars('user',$user);
        $this->layout->view('userinfo/infoset');
    }
     

}

