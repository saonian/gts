<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Sso {

	private $pmsdata;

	public function __construct(){
		$this->pmsdata = config_item('pms');
	}

	public function check_login(){
		if(!session_id()){
			session_start();
		}
		$controller = get_controller();
		$method = get_method();
		$cm = $controller.'/'.$method;
		// 不需要检查的控制器和方法
		$exclude = array('index/checksso', 'index/logout','jishu/index', 'index/login');
		if(!in_array($cm, $exclude)){
			if(empty($_SESSION['userinfo']['id'])){
				// $referer = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
				// $checkurl = "http://".$_SERVER['HTTP_HOST']."/checksso";
				// $strurli = base64_encode($checkurl.'|'.$referer);
				// header('Location:'.config_item('sso_server').'/login/index/sso/?struli='.$strurli."&from=gts");
				// exit;// 这里必须exit
				// 
				include('application/views/login.php');
				exit;
			}else{
				// print_r($_SESSION['userinfo']);exit;
				$exclude_method = array(
					'index', 
					'save',
					'create_project',
					'setgrade',
					'json_story_by_pid',
					'json_task_by_tid',
					'add_form',
					'shenhe_form',
					'gradetaskedit',
					'gradestoryedit',
					'edit_project',
					'start_project',
					'delay_project',
					'hang_project',
					'close_project',
					'create_test',
					'start_test',
					'edit_test',
					'close_test',
					'get_story',
					'usernamedata',
					'real_usernamedata',
					'paste_img',
					'overtime',
					'get_my_overtime_monthly_stat',
					'get_my_dpt_ranking',
					'work',
					'fix_story',
					'fix_task',
					'get_verify_users',
					'get_unfinished_task_count',
					'assign_form',
					// 'ratsetting',
					//'userlist',
					'attention',
					'rat_index',
					'get_types',
					'save_ratting_single',
					'save_ratting_all',
					'get_month',
					'change_manage',
					'rat_detail',
					'rat_detail_by_content',
					'ratting_content_detail',
					// 'auditlist',
					'audit_confirm_reback',
					'audit_confirm_reback_all',
					// 'rattinglist',
					'dept_rattingreport',
					'ratting_del',
					'ratting_modify',
					'get_month_next',
					// 'personal_grade',
					'get_products_by_project',
					'set_init_summary_value',
					'setimg',
					'cutimg',
					'add_grade_by_time',
					'save_sign'
				);
				if($_SESSION['userinfo']['id'] > 1) {
					if(!in_array($method, $exclude_method) && empty($this->pmsdata[$controller]['powers'][$method]['value'])){
						show_error('未存在的权限!');
					}
					if(!in_array($method, $exclude_method) && !has_permission($this->pmsdata[$controller]['powers'][$method]['value'], TRUE)){
						show_error("你没有{$this->pmsdata[$controller]['powers'][$method]['display']}的权限!");
					}
				}
			}
		}
	}

}
?>