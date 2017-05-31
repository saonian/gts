<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Team_model extends MY_Model {

	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * 团队列表
	 */
	public function team_list($page = 1,$pagesize = 20){
		$page = (int)$page < 1 ? 1 : (int)$page;
		$limit = ($page - 1)*$pagesize;
		
		$data = array();
		$params = array('project_id'=>$_COOKIE['current_project_id']);
		$this->db->where($params);
		$total = $this->db->count_all_results(TBL_PROJECT_TEAM);
		
		$list = $this->db->get_where(TBL_PROJECT_TEAM,$params/*,$pagesize, $limit*/)->result_array();
		foreach($list as $key=>$val){
			$row = $this->db->get_where(TBL_USER,array('id'=>$val['user_id']))->row_array();
			if(!empty($row)){
				$list[$key]['real_name'] = $row['real_name'];
			}else{
				$list[$key]['real_name'] = '';
			}
		}
		$data['total'] = $total;
		$data['list'] = $list;
		return $data;
	}

	
	
	
	/**
	 * 删除团队成员
	 * @param int $project_id
	 * @param int $user_id
	 */
	public function team_del($project_id,$user_id){
		if(!empty($project_id) && !empty($user_id)){
			$array['project_id'] = $project_id;
			$array['user_id'] = $user_id;
			return $this->db->delete(TBL_PROJECT_TEAM, $array); 	
		}else{
			return false;
		}
	}
	
	
	/**
	 * 添加团队成员
	 * @param array $users
	 * @param array $roles
	 */
	public function team_manage($users,$roles){
		$this->db->trans_start();
		foreach($users as $key=>$val){
			$rs = $this->db->get_where(TBL_PROJECT_TEAM,array('project_id'=>$this->current_project_id,'user_id'=>$val))->row_array();
			// 不存在才添加, 存在则略过
			if(empty($rs)){
				$data = array();
				$data['project_id'] = $_COOKIE['current_project_id'];
				$data['user_id'] = $val;
				$data['role'] = $roles[$key];
				$data['join_date'] = date('Y-m-d H:i:s');
				
				$this->db->insert(TBL_PROJECT_TEAM,$data);	
			}
		}
		
		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE){
			return FALSE;
		}	
		return TRUE;
	}
	
	
	/**
	 * 获取用户跟角色对应关系
	 */
	public function get_user_list($dpt_id = NULL){
		if($dpt_id){
			$this->load->model('department_model');
			$child_ids = $this->department_model->get_child_ids($dpt_id);
			$this->db->where_in('department_id', $child_ids);
		}
		$user_list = $this->db->get(TBL_USER)->result_array();
		foreach($user_list as $key=>$val){
			$itemid_row = $this->db->get_where(TBL_AUTH_ASSIGNMENT,array('userid'=>$val['id']))->row_array();
			if(isset($itemid_row['itemid'])){
				$user_list[$key]['role_id'] = $itemid_row['itemid'];
				$user_list[$key]['role'] = $this->db->get_where(TBL_AUTH_ITEM,array('id'=>$itemid_row['itemid']))->row()->name;
			}else{
				$user_list[$key]['role'] = '角色未指定';
				$user_list[$key]['role_id'] = '';
			}
		}
		return $user_list;
	}
	


	/**
	 * 获取项目团队TEAM
	 * @param  int $project_id 项目ID
	 * @return array
	 */
	public function get_project_team($project_id = NULL){
		$project_id = empty($project_id)?$this->current_project_id:(int)$project_id;
		$sql = "SELECT u.id, u.real_name FROM ".TBL_USER." u JOIN ".TBL_PROJECT_TEAM." pt ON pt.user_id=u.id WHERE pt.project_id={$project_id}";
		return $this->db->query($sql)->result();
	}

	public function get_team_verify_users($pid = NULL){
		$team = $this->team_model->get_project_team($pid);
		$users = $this->user_model->get_user_by_item($this->pmsdata['story']['powers']['verify']['value']);
		$temp = array();
		foreach ($team as $key => $val) {
			$temp[$val->id] = $val;
		}
		$team = $temp;
		unset($temp);
		foreach ($users as $key => $val) {
			unset($users[$key]);
			$temp[$val->id] = $val;
		}
		$users = $temp;
		unset($temp);
		$users = array_intersect_key($users, $team);
		return $users;
	}

}
?>