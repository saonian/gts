<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Overtime_model extends MY_Model {

	public function __construct() {
		parent::__construct();
	}
	
	public function add_update_overtime_order($Params){
		if($Params['ids']!=''){
			$this->db->query("update gg_overtime set task_id={$Params['task']},overtime_time='{$Params['overtime_time']}',reason='{$Params['reason']}',is_days_off='{$Params['is_days_off']}',begin='{$Params['begin']}',end='{$Params['end']}',hour_counts='{$Params['hour_counts']}' where id={$Params['ids']}");
			return true;
		}else{
			$this->db->query("insert into gg_overtime(proposer,task_id,overtime_time,reason,is_days_off,begin,end,hour_counts,create_time)values('{$Params['user']}',{$Params['task']},'{$Params['overtime_time']}','{$Params['reason']}','{$Params['is_days_off']}','{$Params['begin']}','{$Params['end']}','{$Params['hour_counts']}',now())");
			// 给加班审核人发送邮件
			$insert_id = $this->db->insert_id();
			$proposer = $this->user_model->get_user_by_id($Params['user']);
			$subject = str_replace('#name#', $proposer->real_name, $this->email_subjects['overtime_wait_review']);
			$content = "{$proposer->real_name}<a href='{$this->base_url}/overtime/shenhe?id={$insert_id}'>加班审核</a>。加班理由：{$Params['reason']}";
			// 所有具有审核加班权限的人
			$auditors = $this->user_model->get_user_by_item($this->pmsdata['overtime']['powers']['shenhe']['value']);
			$auditor = array();
			foreach ($auditors as $key => $val) {
				// 发给同一部门具有审核加班权限的人
				if($val->department_id == $proposer->department_id && !empty($val->email)){
					$auditor[] = $val->email;
				}
			}
			if(!empty($auditor)){
				$this->send_mail($auditor, $subject, $content);
			}
			return true;
		}
	}
	
	public function get_page_path($path,$params){
		if(!is_array($params) || empty($params)) return $path;
		foreach($params as $k=>$v){
			if($v != ''){
				$path .= "&".$k."=".$v;
			}
		}
		return $path;
	}
	
	public function index_search($params,$order='order by id desc'){
		$page = (int)$params['page'] < 0 ? 1 : (int)$params['page'];
		$page_size = (int)$params['page_size'];
		$limit = $page_size;
		$start = $limit * ($page - 1);

		$where = ' WHERE 1=1 ';
		if($params['begin'] != ''){
			$where .= " AND '".$params['begin']."' <= o.begin ";
		}
		if($params['end'] != ''){
			$where .= " AND o.begin <= '".$params['end']."'";
		}
		if($params['audit_status'] != ''){
			$where .= " AND o.audit_status = '".$params['audit_status']."'";
		}
		if($params['search_type'] != '' && $params['keyword']!='' ){
			if($params['search_type'] == 'proposer' AND has_permission($this->pmsdata['overtime']['powers']['shenhe']['value'])){
				$query = $this->db->query("SELECT id FROM ".TBL_USER." WHERE real_name LIKE '%{$params['keyword']}%'");
				if($query->num_rows() > 0){
					$findusers = $query->result();
					$findids = array(0);
					foreach ($findusers as $key => $val) {
						$findids[] = $val->id;
					}
					$where .= " AND proposer IN (".join(',', $findids).") ";
				}
			}else{
				$where .= " AND ".$params['search_type']." LIKE '%".$params['keyword']."%' ";
			}
		}
		if(!has_permission($this->pmsdata['overtime']['powers']['shenhe']['value'])){
				$where .= " AND o.proposer=".$this->current_user_id;;
		}else{
			$users = $this->get_users($_SESSION['userinfo']['department_id']);
			if(count($users)>0 && $params['search_type']!='proposer'){
				$user = implode(',',$users);
				$where .=" AND o.proposer in (".$user.") ";
			}
		}

		$order = $this->input->get('order');
		$sort = $this->input->get('sort');
		$order_by = '';
		if($order && $sort){
			$order_by = " ORDER BY o.{$order} {$sort} ";
		}else{
			$order_by = ' ORDER BY o.id DESC ';
		}

		// echo $where;exit;
		$total = $this->db->query("SELECT COUNT(1) as total FROM ".TBL_OVERTIME." o {$where}");
		$total_num = $total->row()->total;
		$sql = "SELECT o.*, u.real_name AS proposer FROM ".TBL_OVERTIME." o JOIN ".TBL_USER." u ON o.proposer=u.id {$where} {$order_by} LIMIT {$start},{$limit}";
		// echo $sql;exit;
		$query = $this->db->query($sql);
		$rs = $query->result_array();
		foreach ($rs as $key => $val) {
			if(!empty($val['auditor']) && $val['audit_status'] != 0){
				$auditor = $this->user_model->get_user_by_id($val['auditor']);
				$rs[$key]['auditor'] = empty($auditor) ? '' : $auditor->real_name;
			}else{
				$rs[$key]['auditor'] = '';
			}
		}
		// print_r($rs);exit;
		$sql = "SELECT SUM(hour_counts) AS c, audit_status AS status FROM ".TBL_OVERTIME." o {$where} GROUP BY audit_status";
		$hours = $this->db->query($sql)->result();
		$pass_hours = 0;
		$unpass_hours = 0;
		$reject_hours = 0;
		foreach ($hours as $key => $val) {
			if($val->status == 0){
				$unpass_hours += $val->c;
			}elseif ($val->status == 1) {
				$pass_hours += $val->c;
			}elseif ($val->status == 2) {
				$reject_hours += $val->c;
			}
		}
		$TemArr['total'] = $total_num;
		$TemArr['data'] = $rs;
		$TemArr['pass_hours'] = $pass_hours;
		$TemArr['unpass_hours'] = $unpass_hours;
		$TemArr['reject_hours'] = $reject_hours;
		$TemArr['total_hours'] = $pass_hours + $unpass_hours + $reject_hours;
		$TemArr['page_html'] = $this->create_page($total_num, $page_size);
		return $TemArr;	
	}
	
	public function get_overtime_infos($id){
		$query = $this->db->query("SELECT o.*,t.name AS task_name,u.real_name AS proposer FROM ".TBL_OVERTIME." o LEFT JOIN ".TBL_TASK." t ON o.task_id=t.id JOIN ".TBL_USER." u ON o.proposer=u.id WHERE o.id=$id");
		return $query->row_array();
	}
	
	public function delete_overtime($id){
		$this->db->query("delete from gg_overtime where id= $id");
		return true;
	}
	
	public function shenhe($Params){
		$this->db->query("update gg_overtime set auditor='{$Params['user']}',audit_status={$Params['audit_status']},remark='{$Params['remark']}' where id={$Params['ids']}");
		// 给加班申请人发送邮件
		$overtime = $this->db->get_where(TBL_OVERTIME, array('id'=>$Params['ids']))->row();
		$proposer = $this->user_model->get_user_by_id($overtime->proposer);
		$subject = $Params['audit_status'] == 1 ? $this->email_subjects['overtime_review_ok'] : $this->email_subjects['overtime_review_reject'];
		$subject = str_replace('#name#', $proposer->real_name, $subject);
		$content = "<a href='{$this->base_url}/overtime/view?id={$overtime->id}'>查看</a>";
		if($proposer && !empty($proposer->email)){
			$this->send_mail($proposer->email, $subject, $content);
		}
		return true;
	}

	public function get_stories(){
		$sql = "SELECT t.* FROM ".TBL_TASK." t WHERE t.status<>'{$this->pmsdata['story']['status']['closed']['value']}' AND (t.assigned_to={$this->current_user_id} OR t.finished_by={$this->current_user_id})";
		$result = $this->db->query($sql)->result_array();
		return $result;
	}
	
	public function get_users($department_id){
		$this->load->model('department_model');
		$ids = $this->department_model->get_all_parent_id($department_id);
		$this->db->where_in('department_id',$ids);
		$users = $this->db->get('gg_user')->result_array();
		foreach($users as $user){
			$ss[]= "'".$user['id']."'";
		}
		return $ss;		
	}

	
}