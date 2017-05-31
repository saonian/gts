<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Account_model extends MY_Model {

	public function __construct() {
		parent::__construct();
		$this->load->model('department_model');
		$this->load->model('rbac_model');
	}
	
	/**
	 * 账户列表
	 * @param array $data
	 * @param int $page
	 * @param int $pagesize
	 */
	public function account_list($data, $page = 1, $pagesize = 20){
		$page = (int)$page < 1 ? 1 : (int)$page;
		$order = $this->input->get('order');
		$sort = $this->input->get('sort');
		$order = $order?$order:'id';
		$sort = $order?$sort:'desc';

		$where = " WHERE 1=1";

		$rs = array();
		if(!empty($data['department_id'])){

			$all_child_id = $this->department_model->get_all_parent_id($data['department_id']);
			$ids = '';
			foreach($all_child_id as $key=>$val){
				$ids .= $val.',';
			}
			$ids = trim($ids,',');
			$where .= "  AND `department_id` IN (".$ids.")";

		}
		if(!empty($data['search_type']) && !empty($data['keyword'])){
			$where .= "  AND `".$data['search_type']."` LIKE '%".$data['keyword']."%'";
		}
		
		$total = $this->db->query("SELECT COUNT(1) AS total FROM ".TBL_USER.$where);
    	$total_num = $total->row()->total;
		
    	$sql = "SELECT * FROM ".TBL_USER.$where;
    	$sql .= " ORDER BY `{$order}` {$sort}";
	    $limit = " LIMIT ".($page-1)*$pagesize.",".$pagesize;
		$sql .=  $limit;
			
    	$result = $this->db->query($sql)->result_array();
		foreach($result as $key=>$val){
			$row = $this->db->get_where(TBL_AUTH_ASSIGNMENT,array('userid'=>$val['id']))->row_array();
			if(!empty($row)){
				$result[$key]['role'] = $this->db->get_where(TBL_AUTH_ITEM,array('id'=>$row['itemid']))->row()->name;
			}else{
				$result[$key]['role'] = '';
			}
			if(!empty($val['department_id'])){
				// $result[$key]['department_name'] = $this->department_model->get_only_parentname($val['department_id']);
				$result[$key]['department_name'] = $this->department_model->get_department_path($val['department_id']);
			}else{
				$result[$key]['department_name'] = '';
			}
		}
		$rs['total'] = $total_num;
		$rs['list'] = $result;
		$rs['total_page'] = (int)(($total_num-1)/$pagesize + 1);
		$rs['page_html'] = $this->create_page($total_num, $pagesize);
		
		return $rs;
	}
	
	
	/**
	 * 获取type=2的所有角色信息
	 */
	public function get_role_list(){
		return $this->db->get_where(TBL_AUTH_ITEM,array('type'=>2))->result_array();
	}
	
	
	/**
	 * 用户设置角色和部门
	 * @param int $user_id
	 * @param int $role_id
	 * @param int $department_id
	 */
	public function account_set($user_id,$role_id,$department_id,$email,$phone){
		$this->db->trans_start();
		$this->db->where(array('userid' => $user_id));
		$is_count = $this->db->count_all_results(TBL_AUTH_ASSIGNMENT);
		if( $is_count > 0 ){
			$this->db->where(array('userid'=>$user_id));
			$this->db->update(TBL_AUTH_ASSIGNMENT, array('itemid'=>$role_id));
		}else{
			$data = array();
			$data['itemid'] = $role_id;
			$data['userid'] = $user_id;
			$this->db->insert(TBL_AUTH_ASSIGNMENT, $data);	
		}
		$this->db->where(array('id'=>$user_id)); 
		$this->db->update(TBL_USER, array('department_id'=>$department_id, 'email'=>trim($email), 'phone'=>trim($phone)));	
		
		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE){
			return FALSE;
		}	
		return TRUE;
	}

	/**
	 * 同步用户中心用户
	 */
	public function syn_users(){
		$ch = curl_init();
		// curl_setopt($ch, CURLOPT_URL, config_item('sso_server').'/welcome/listusers');
		curl_setopt($ch, CURLOPT_URL, config_item('sso_server').'/login/safelog/listusers');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		$output = curl_exec($ch);
		curl_close($ch);

		$server_users = (array)json_decode($output);
		if(empty($server_users)){
			return;
		}

		$ucenter_users = array();
		$update_users = array();
		foreach($server_users as $user){
			$ucenter_users[$user->account] = (array)$user;
			$sql = "select id from gg_user where account = '".$user->account."'";
			$query = $this->db->query($sql);
			$id = $query->row()->id;
			if(!empty($id)){
				$update_users[] = array(
					'id' => $id,
					'real_name' => $user->real_name,
					'join_date' => $user->join_date,
					'is_admin' => $user->is_admin
				);
			}
		}
		if(!empty($update_users)){
			$this->db->update_batch(TBL_USER, $update_users, 'id');
		}
		// print_r($ucenter_users);exit;
	
		$local_users = $this->db->get(TBL_USER)->result_array();
		foreach ($local_users as $key => $user) {
			$local_users[$user['account']] = $user;
			unset($local_users[$key]);
		}
		// print_r($local_users);exit;
		
		$need_add_users = array_diff_key($ucenter_users, $local_users);
		// print_r($need_add_users);exit;
		$need_delete_users = array_diff_key($local_users, $ucenter_users);
		// print_r($need_delete_users);exit;
		if(count($need_add_users) > 0){
			$this->db->insert_batch(TBL_USER, $need_add_users);
		}
		if(count($need_delete_users) > 0){
			foreach ($need_delete_users as $user) {
				$this->db->where('account', $user['account']);
				$this->db->delete(TBL_USER);
			}
		}
	}
}

?>