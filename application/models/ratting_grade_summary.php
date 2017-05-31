<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ratting_grade_summary extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }
        //获取员工得分
    public function get_grade_detail($uid,$year,$month){
    	$sql = "select * from gg_ratting_grade_summary where uid = ".$uid." and year = ".$year." and month = ".$month;
    	$query = $this->db->query($sql);
    	return $query->row_array();
    }

        //判断是否有评分
    public function get_is_rating($uid){
    	$date = date('Y-m');
    	$sql = "select count(1) as total from gg_rating where rating_uid = ".$uid." and substr(`addtime`,1,7) = '".$date."'";
    	$query = $this->db->query($sql);
    	return $query->row()->total;
    }

    /**
	 * 账户列表
	 * @param array $data
	 * @param int $page
	 * @param int $pagesize
	 */
	public function account_list($data, $page = 1, $pagesize = 30){
		$this->load->model('department_model');

		$order_by = $this->input->get('order_by');
        $sort = $this->input->get('sort');
        $order = $order_by?$order_by:'a.department_id';
        if($order == 'real_name'){
        	$order = 'convert(a.real_name using gbk)';
        }
		$sort = $sort?$sort:'asc';

		$page = (int)$page < 1 ? 1 : (int)$page;
		$where = " WHERE 1=1";
		$where .= " and ((b.year = ".$data['year']." and b.month = ".$data['month'].") or (b.year is null and b.month is null))";
		$roles = config_item('ratting_role');
		// if(!in_array($_SESSION['userinfo']['role']->id,$roles)){
		// 	$where .=" AND a.`id` != ".$_SESSION['userinfo']['id'];
		// }
		//查询今天访问过的人
		$date = date('Y-m-d');
		$sql = "select `uid` from gg_rating_log where login_date = '".$date."'";
		$query = $this->db->query($sql);
		$uid_online_arr = $query->result_array();
		$uid_online_total = count($uid_online_arr);
		
		$uid_online_new_arr = array();
		foreach ($uid_online_arr as $key => $value) {
			$uid_online_new_arr[] = $value['uid'];
		}
		$uid_online_str = implode(',',$uid_online_new_arr);

		if(!empty($data['is_login'])){
			if($data['is_login'] == 1){
				$where .= " and a.id in (".$uid_online_str.") ";
			}else{
				$where .= " and a.id not in (".$uid_online_str.") ";
			}
		}

		$rs = array();
		if(!empty($data['department_id'])){
			if($data['department_id'] == 'tt'){//表示特别关注的
				$attention = $_SESSION['userinfo']['attention'];
				if(empty($attention)){
					$attention = 0;
				}
				$where .=" AND a.`id` IN (".$attention.")";
			}else{
				$all_child_id = $this->department_model->get_all_parent_id($data['department_id']);
				$ids = '';
				foreach($all_child_id as $key=>$val){
					$ids .= $val.',';
				}
				$ids = trim($ids,',');
				$where .= "  AND a.`department_id` IN (".$ids.")";
			}
		}
		if(!empty($data['real_name'])){
			$where .= "  AND (a.`real_name` LIKE '%".$data['real_name']."%' or a.`account` LIKE '%".$data['real_name']."%')";
		}
		$where .=" AND a.`department_id` > 0";
		$attention = $_SESSION['userinfo']['attention'];
		$total = $this->db->query("select COUNT(1) AS total from ".TBL_USER." as a left join gg_ratting_grade_summary as b on a.id = b.uid ".$where);
    	$total_num = $total->row()->total;
		
    	// $sql = "SELECT * FROM ".TBL_USER.$where;
    	if($attention){
    		if($uid_online_str){
    			$sql = "select (case when a.id in (".$attention.") then 1 else 0 end) as attention,(case when a.id in (".$uid_online_str.") then 1 else 0 end) as online, a.account,a.image,a.sign,g.name as dept_name,a.id as u_id,a.real_name as realname,a.department_id,a.account,a.is_manage,b.* from ".TBL_USER." as a left join gg_department as g on a.department_id = g.id left join gg_ratting_grade_summary as b on a.id = b.uid ".$where." ORDER BY attention desc,";
    		}else{
    			$sql = "select (case when a.id in (".$attention.") then 1 else 0 end) as attention, a.account,a.image,a.sign,g.name as dept_name,a.id as u_id,a.real_name as realname,a.department_id,a.account,a.is_manage,b.* from ".TBL_USER." as a left join gg_department as g on a.department_id = g.id left join gg_ratting_grade_summary as b on a.id = b.uid ".$where." ORDER BY attention desc,";
    		}
    	}else{
    		if($uid_online_str){
    			$sql = "select (case when a.id in (".$uid_online_str.") then 1 else 0 end) as online, a.account,g.name as dept_name,a.id as u_id,a.real_name as realname,a.department_id,a.account,a.image,a.sign,a.is_manage,b.* from ".TBL_USER." as a left join gg_department as g on a.department_id = g.id left join gg_ratting_grade_summary as b on a.id = b.uid ".$where." ORDER BY ";
    		}else{
    			$sql = "select a.account,g.name as dept_name,a.id as u_id,a.real_name as realname,a.department_id,a.account,a.image,a.sign,a.is_manage,b.* from ".TBL_USER." as a left join gg_department as g on a.department_id = g.id left join gg_ratting_grade_summary as b on a.id = b.uid ".$where." ORDER BY ";
    		}
    	}

	    $limit = " LIMIT ".($page-1)*$pagesize.",".$pagesize;
	    $sql .= "{$order} {$sort}";
		$sql .=  $limit;
    	$result = $this->db->query($sql)->result_array();
    	foreach($result as $key=>$res){
    		$str = $this->get_parent_id($res['department_id']);
			$arr = explode('>>', $str);
			if(!empty($arr[2])){
				unset($arr[2]);
			}
			$result[$key]['dept_name'] = implode('>>', $arr);
		}

		$rs['total'] = $total_num;
		$rs['list'] = $result;
		$rs['total_page'] = (int)(($total_num-1)/$pagesize + 1);
		$rs['page_html'] = $this->create_page($total_num, $pagesize);
		$rs['pagesize'] = $pagesize;
		$rs['curpage'] = $page;
		$rs['online_num'] = $uid_online_total;
		$total = $this->db->query("select COUNT(1) AS total from ".TBL_USER." where `department_id` > 0");
		$rs['total_num'] = $total->row()->total;
		return $rs;
	}
	/**
	*@desc 获取某个部门的父部门名称
	*/
	public function get_parent_id($id){
		$query = $this->db->query("select parent_id,name from ".TBL_DEPARTMENT." where id=".$id);
		$row = $query->row_array();
		$string=$row['name'];
		if($row && $row['parent_id']!=0){
			$t_str = $this->get_parent_id($row['parent_id']);
			$string=$t_str.">>".$string;
		}
		return $string;
	}
	public function get_user_info_by_id($id){
		$sql = "select * from gg_user where id = ".$id;
		$query = $this->db->query($sql);
		return $query->row_array();
	}

	public function set_init_summary_value(){
		$flag = true;
        $this->db->trans_start();
		$this->load->model('rating_model');
		$grade_set = $this->rating_model->get_grade_set();
		$year = intval(date('Y'));
		$month = intval(date('m'));

		$sql = "select * from ".TBL_USER." where department_id > 0";
		$query = $this->db->query($sql);
		$users = $query->result_array();
		foreach($users as $k => $v){
			$info = $this->get_grade_detail($v['id'],$year,$month);
			if(empty($info)){
				$data = array(
					'uid' => $v['id'],
					'real_name' => $v['real_name'],
					'year' => $year,
					'month' => $month,
					'addtime' => date('Y-m-d H:i:s')
					);
				if($v['is_manage'] == 1){
					$data['plus_last'] = $grade_set['manage_plus'];
					$data['minus_last'] = $grade_set['manage_minus'];
				}else{
					$data['plus_last'] = $grade_set['common_plus'];
					$data['minus_last'] = $grade_set['common_minus'];
				}
				$return = $this->db->insert('gg_ratting_grade_summary',$data);
            	$this->rating_model->_write_log('modify_ratting_grade_summary','ratting',$this->db->last_query(),'初始化gg_ratting_grade_summary表数据');
            	if(!$return){
            		$flag = false;
        		}
			}
			
		}
        $this->db->trans_complete();
        if($this->db->trans_status() === false || $flag === false){
            return false;
        }else{
            return true;
        }
	}

	public function login_log(){
		$date = date('Y-m-d');
		$uid = $_SESSION['userinfo']['id'];
		if(empty($uid)){
			return;
		}
		$sql = " select id from gg_rating_log where login_date = '".$date."' and uid = ".$uid;
		$query = $this->db->query($sql);
		$id = $query->row()->id;
		if(empty($id)){
			$data = array(
				'uid' => $_SESSION['userinfo']['id'],
				'login_date' => date('Y-m-d'),
				'first_login_time' => date('Y-m-d H:i:s'),
				'last_login_time' => date('Y-m-d H:i:s'),
				'last_login_ip' => $_SERVER['REMOTE_ADDR']
			);
			$this->db->insert('gg_rating_log',$data);
		}else{
			$this->db->where('id',$id);
			$data = array(
				'last_login_time' => date('Y-m-d H:i:s'),
				'last_login_ip' => $_SERVER['REMOTE_ADDR']
				);
			$this->db->update('gg_rating_log',$data);
		}
	}

	public function add_grade_by_time(){
		$this->load->model('rating_model');
		$grade_set = $this->rating_model->get_grade_set();
		$delay_days = intval($grade_set['delay_days']);
		$sql = "select * from gg_rating where status = 2 and is_added = 0 and DATEDIFF(NOW(),`audit_time`) >= ".$delay_days;
		$query = $this->db->query($sql);
		$result = $query->result_array();
		$this->db->trans_start();
		$flag = true;
		$sql = "update gg_rating set is_added = 1 where status = 2 and DATEDIFF(NOW(),`audit_time`) >= ".$delay_days;
		$query = $this->db->query($sql);
        foreach($result as $k => $v){
        	// $sql = "update gg_rating set is_added = 1 where id = ".$v['id'];
	        // $query = $this->db->query($sql);
	        $date = substr($v['addtime'],0,7);
	        $arr = $this->rating_model->get_new_grade($date,$v['rated_uid']);
	        $arr['uid'] = $v['rated_uid'];
	        $arr['real_name'] = $v['rated_name'];
	        $arr['year'] = intval(substr($v['addtime'],0,4));
	        $arr['month'] = intval(substr($v['addtime'],5,2));
	        $desc = '通过审核--延迟加分--引起'.$v['rated_name'].'得分变化';
	        $return = $this->rating_model->update_grade_summary($v['rated_uid'],$arr,$this->db,$desc);
	        if(!$return){
	            $flag = false;
	        }
    	}
    	$this->db->trans_complete();
    	if($this->db->trans_status() === false || $flag === false){
            return false;
        }else{
            return true;
        }
	}
}

?>