<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Statistics_model extends MY_Model {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * 当前用户最近6个月加班
	 * @return array
	 */
	public function my_monthly_overtime_stat($begin_date = NULL, $end_date = NULL, $overtime_type = 'all'){
		// 最近6个月的
		$where = ' 1=1 ';
		switch ($overtime_type) {
			case 'workday':
				$where .= ' AND overtime_time=0 ';
				break;
			case 'weekend':
				$where .= ' AND overtime_time=1 ';
				break;
		}
		if($begin_date && $end_date){
			$where .= " AND proposer={$this->current_user_id} AND audit_status=1 AND begin>='{$begin_date}' AND begin<='{$end_date}'";
			$sql = "SELECT MONTH(`begin`) AS mon,SUM(hour_counts) AS total FROM ".TBL_OVERTIME." WHERE {$where} GROUP BY mon ORDER BY id DESC";
		}else{
			$where .= " AND proposer={$this->current_user_id} AND audit_status=1";
			$sql = "SELECT MONTH(`begin`) AS mon,SUM(hour_counts) AS total FROM ".TBL_OVERTIME." WHERE {$where} GROUP BY mon ORDER BY id DESC LIMIT 6";
		}
		// echo $sql;exit;
		return array_reverse($this->db->query($sql)->result());
	}

	/**
	 * 当月各部门加班
	 * @return array
	 */
	public function dpt_overtime_stat($begin_date = NULL, $end_date = NULL, $overtime_type = 'all'){
		$where = ' 1=1 ';
		switch ($overtime_type) {
			case 'workday':
				$where .= ' AND o.overtime_time=0 ';
				break;
			case 'weekend':
				$where .= ' AND o.overtime_time=1 ';
				break;
		}
		$this->load->model('department_model');
		$dpts = $this->db->get_where(TBL_DEPARTMENT, array('parent_id'=>0))->result();
		$result = array();
		foreach ($dpts as $key => $val) {
			$dpt_ids = $this->department_model->get_child_ids($val->id);
			$sql = "SELECT GROUP_CONCAT(id) AS uids FROM ".TBL_USER." WHERE department_id IN (".join(',', $dpt_ids).")";
			$user_ids = $this->db->query($sql)->row();
			$user_ids = $user_ids->uids;

			$total = 0;//部门总加班时间
			$user_total = array();//部门各员工加班时间
			if(!empty($user_ids)){
				if($begin_date && $end_date){
					$where_time = " AND begin>='{$begin_date} 00:00:00' AND begin<='{$end_date} 23:59:59' ";
				}else{
					$where_time = " AND MONTH(o.begin) = MONTH(NOW())";
				}
				$sql = "SELECT SUM(o.hour_counts) AS total FROM ".TBL_OVERTIME." o WHERE {$where} AND o.proposer IN ({$user_ids}) AND o.audit_status=1 {$where_time}";
				$total = $this->db->query($sql)->row();
				$total = empty($total->total)?0:$total->total;

				if($total > 0){
					// 没调休的加班时间
					$sql = "SELECT u.id AS user_id,u.real_name AS name,o.is_days_off,SUM(o.hour_counts) AS total FROM ".TBL_OVERTIME." o JOIN ".TBL_USER." u ON o.proposer=u.id WHERE {$where} AND o.proposer IN ({$user_ids}) AND o.audit_status=1 AND o.is_days_off=0 {$where_time} GROUP BY o.proposer ORDER BY total DESC";
					// 有调休的加班时间
					$sql1 = "SELECT u.id AS user_id,u.real_name AS name,o.is_days_off,SUM(o.hour_counts) AS total FROM ".TBL_OVERTIME." o JOIN ".TBL_USER." u ON o.proposer=u.id WHERE {$where} AND o.proposer IN ({$user_ids}) AND o.audit_status=1 AND o.is_days_off=1 {$where_time} GROUP BY o.proposer ORDER BY total DESC";
					$user_total_detail = $this->db->query($sql)->result();
					foreach ($user_total_detail as $k => $v) {
						// 先将调休时间都置为0
						$user_total[$v->user_id] = array($v->name, (float)$v->total, 0);//注意int转换，highcharts只认int类型的数值
					}
					$user_total_detail = $this->db->query($sql1)->result();
					foreach ($user_total_detail as $k => $v) {
						if(isset($user_total[$v->user_id])){
							// 更新总加班时间和调休时间
							$total = $user_total[$v->user_id][1];
							$user_total[$v->user_id][1] = (float)($total + $v->total);//注意int转换，highcharts只认int类型的数值
							$user_total[$v->user_id][2] = (float)$v->total;
						}else{
							$user_total[$v->user_id][0] = $v->name;
							$user_total[$v->user_id][1] = (float)($v->total);//注意int转换，highcharts只认int类型的数值
							$user_total[$v->user_id][2] = (float)$v->total;
						}
					}
				}
			}
			$user_total = array_values($user_total);
			$result[] = array('dpt_id'=>$val->id, 'dpt_name'=>$val->name, 'total'=>(float)$total, 'detail'=>$user_total);
		}
		// var_dump($result);exit;
		return $result;
	}

	public function work_stat($begin_date, $end_date){
		if(!strtotime($begin_date) || !strtotime($end_date)){
			return FALSE;
		}
		// 任务以预计开始时间为基准
		$task_where = " deadline>='{$begin_date}' AND deadline<='{$end_date}' ";
		// 需求以创建日期为基准
		$story_where = " s.opened_date>='{$begin_date}' AND s.opened_date<='{$end_date}' ";
		// 超级管理员权限能查看所有统计，其他人只能查看自己的
		$is_admin = is_admin();
		if($is_admin){
			// $users = $this->db->get(TBL_USER)->result();
			$this->load->model('department_model');
			$dpt_ids = $this->department_model->get_child_ids(1);// 只显示技术部的
			$this->db->where_in('department_id', $dpt_ids);
			$users = $this->db->get(TBL_USER)->result();
		}else{
			$this->load->model('rbac_model');
			$role = $this->rbac_model->get_user_role($this->current_user_id);
			// 如果是研发主管或PM主管，则可以看到所在组员的工作统计
			if($role->id == 96 || $role->id == 99){
				$dpt_id = $this->current_user['department_id'];
				if(empty($dpt_id)){
					$users = $this->db->get_where(TBL_USER, array('id'=>$this->current_user_id))->result();
				}else{
					$users = $this->db->get_where(TBL_USER, array('department_id'=>$dpt_id))->result();
				}
			}else{
				$users = $this->db->get_where(TBL_USER, array('id'=>$this->current_user_id))->result();
			}
		}
		foreach ($users as $key => $val) {
			// 排除admin
			if($val->account == 'admin'){
				unset($users[$key]);
				continue;
			}
			// 初始化任务数统计
			$users[$key]->task_count = array(
				'total' => 0,//总任务数
				'wait' => 0,//未开始
				'doing' => 0,//开发中
				'finished' => 0,//已完成
				'finish_rate' => 0 //完成率
			);
			$sql = "SELECT t.status,COUNT(*) AS task_count,SUM(t.estimate) AS estimate,SUM(t.consumed) AS consumed,SUM(t.estimate*(1+t.difficulty/10)) AS score,t.finished_by FROM ".TBL_TASK." t WHERE {$task_where} AND (t.assigned_to={$val->id} OR t.finished_by={$val->id}) GROUP BY t.status,t.finished_by";
			$task_count_info = $this->db->query($sql)->result();
			$task_total_count = 0;
			$task_total_estimate = 0;
			$work_score_hour = 0;
			$work_finished_hour = 0;
			foreach ($task_count_info as $count) {
				switch ($count->status) {
					case 'wait':
						$task_total_count += $count->task_count;
						$task_total_estimate += $count->estimate;
						// $work_score_hour += $count->score;
						$users[$key]->task_count['wait'] = $count->task_count;
						break;
					case 'doing':
						$task_total_count += $count->task_count;
						$task_total_estimate += $count->estimate;
						// $work_score_hour += $count->score;
						$users[$key]->task_count['doing'] = $count->task_count;
						break;
					// 除了未开始和进行中的都是已完成的
					default:
						if($count->finished_by == $val->id){
							$task_total_count += $count->task_count;
							$task_total_estimate += $count->estimate;
							$work_score_hour += $count->score;
							$work_finished_hour += $count->consumed;
							$users[$key]->task_count['finished'] += $count->task_count;
						}
						break;
				}
			}
			$users[$key]->task_count['total'] = $task_total_count;
			$users[$key]->task_count['finish_rate'] = sprintf("%.2f", empty($task_total_count)?0:($users[$key]->task_count['finished']/$task_total_count));

			
			// 初始化工时统计
			$users[$key]->work_hour = array(
				'estimate' => 0, //计划工时
				'consumed' => 0, //已完成
				'score' => 0, //计分工时
				'effect_rate' => 0, //有效率
				'save_rate' => 0  //节约率
			);
			$users[$key]->work_hour['estimate'] = !empty($task_total_estimate)?sprintf("%.2f", $task_total_estimate):0;
			$users[$key]->work_hour['consumed'] = !empty($work_finished_hour)?sprintf("%.2f", $work_finished_hour):0;
			$users[$key]->work_hour['score'] = !empty($work_score_hour)?sprintf("%.2f", $work_score_hour):0;
			$users[$key]->work_hour['effect_rate'] = empty($users[$key]->work_hour['estimate'])?0:sprintf("%.2f", $users[$key]->work_hour['consumed']/$users[$key]->work_hour['estimate']);
			$users[$key]->work_hour['save_rate'] = empty($users[$key]->work_hour['estimate'])?0:sprintf("%.2f", ($users[$key]->work_hour['estimate']-$users[$key]->work_hour['consumed'])/$users[$key]->work_hour['estimate']);

			// 初始化任务评分统计
			$users[$key]->task_grade = array(
				'good_rate' => 0, //好评率
				'bad_rate' => 0 //差评率
			);
			$sql = "SELECT u.real_name,t.id AS task_id,t.name,g.* FROM ".TBL_TASK." t JOIN ".TBL_GRADE." g ON g.object_id=t.id JOIN ".TBL_USER." u ON g.grade_by=u.id WHERE {$task_where} AND g.type='task' AND t.status='closed' AND g.is_graded='1' AND t.finished_by={$val->id}";
			$graded_task = $this->db->query($sql)->result();
			$grade_task_count = count($graded_task);//已评价的任务数量
			$task_good_count = 0;//好评任务数
			$task_bad_count = 0;//差评任务数
			$grade_good_task = array();
			$grade_bad_task = array();
			foreach ($graded_task as $g) {
				$sql = "SELECT SUM(gd.score) AS score FROM ".TBL_GRADE_SCORE." gs JOIN ".TBL_GRADE_DESCRIPTION." gd ON gs.description_id=gd.id WHERE grade_id={$g->id}";
				$score = $this->db->query($sql)->row();
				$task_name = htmlspecialchars($g->name, ENT_QUOTES);
				if(isset($score->score) && $score->score>=2){
					$task_good_count++;
					$grade_good_task[] = array('type'=>'task', 'id'=>$g->task_id, 'name'=>$task_name, 'grade_id'=>$g->id, 'grade_by'=>$g->real_name);
				}else if(isset($score->score) && $score->score==0){
					$task_bad_count++;
					$grade_bad_task[] = array('type'=>'task', 'id'=>$g->task_id, 'name'=>$task_name, 'grade_id'=>$g->id, 'grade_by'=>$g->real_name);
				}
			}
			$users[$key]->task_grade['grade_good_task'] = $grade_good_task;
			$users[$key]->task_grade['grade_bad_task'] = $grade_bad_task;
			$users[$key]->task_grade['good_rate'] = sprintf("%.2f", empty($grade_task_count)?0:$task_good_count/$grade_task_count);
			$users[$key]->task_grade['bad_rate'] = sprintf("%.2f", empty($grade_task_count)?0:$task_bad_count/$grade_task_count);

			// 初始化需求评分统计
			$users[$key]->story_grade = array(
				'good_rate' => 0, //好评率
				'medium_rate' => 0, //中评率
				'bad_rate' => 0 //差评率
			);
			$sql = "SELECT u.real_name,s.id AS story_id,s.name,g.* FROM ".TBL_STORY." s JOIN ".TBL_TASK." t ON t.story_id=s.id JOIN ".TBL_GRADE." g ON g.object_id=s.id JOIN ".TBL_USER." u ON g.grade_by=u.id  WHERE {$story_where} AND g.type='story' AND s.status='closed' AND g.is_graded='1' AND t.finished_by={$val->id} GROUP BY s.id";
			$graded_story = $this->db->query($sql)->result();
			$grade_story_count = count($graded_story);//已评价的需求数量
			$story_good_count = 0;//好评需求数
			$story_medium_count = 0;//中评需求数
			$story_bad_count = 0;//差评需求数
			$grade_good_story = array();
			$grade_medium_story = array();
			$grade_bad_story = array();
			foreach ($graded_story as $g) {
				$sql = "SELECT SUM(gd.score) AS score FROM ".TBL_GRADE_SCORE." gs JOIN ".TBL_GRADE_DESCRIPTION." gd ON gs.description_id=gd.id WHERE grade_id={$g->id}";
				$score = $this->db->query($sql)->row();
				$story_name = htmlspecialchars($g->name, ENT_QUOTES);
				if(isset($score->score) && $score->score>2){
					$story_good_count++;
					$grade_good_story[] = array('type'=>'story', 'id'=>$g->story_id, 'name'=>$story_name, 'grade_id'=>$g->id, 'grade_by'=>$g->real_name);
				}else if(isset($score->score) && $score->score<2){
					$story_bad_count++;
					$grade_bad_story[] = array('type'=>'story', 'id'=>$g->story_id, 'name'=>$story_name, 'grade_id'=>$g->id, 'grade_by'=>$g->real_name);
				}else if(isset($score->score) && $score->score==2){
					$story_medium_count++;
					$grade_medium_story[] = array('type'=>'story', 'id'=>$g->story_id, 'name'=>$story_name, 'grade_id'=>$g->id, 'grade_by'=>$g->real_name);
				}
			}
			$users[$key]->story_grade['grade_good_story'] = $grade_good_story;
			$users[$key]->story_grade['grade_medium_story'] = $grade_medium_story;
			$users[$key]->story_grade['grade_bad_story'] = $grade_bad_story;
			$users[$key]->story_grade['good_rate'] = sprintf("%.2f", empty($grade_story_count)?0:$story_good_count/$grade_story_count);
			$users[$key]->story_grade['medium_rate'] = sprintf("%.2f", empty($grade_story_count)?0:$story_medium_count/$grade_story_count);
			$users[$key]->story_grade['bad_rate'] = sprintf("%.2f", empty($grade_story_count)?0:$story_bad_count/$grade_story_count);

			// 初始化任务时效统计
			$users[$key]->task_aging = array(
				'ahead_rate' => 0, //提前完成率
				'delay_rate' => 0  //延迟完成率
			);
			// 只要有完成者就算完成了，不管完成后是否取消
			// $sql = "SELECT (UNIX_TIMESTAMP(deadline)-UNIX_TIMESTAMP(finished_date))/3600 AS ahead FROM ".TBL_TASK." WHERE {$task_where} AND finished_by={$val->id}";
			$sql = "SELECT DATEDIFF(deadline,finished_date) AS ahead FROM ".TBL_TASK." WHERE {$task_where} AND finished_by={$val->id}";
			$task_aging_info = $this->db->query($sql)->result();
			$finished_task_count = count($task_aging_info);
			$ahead_count = 0;
			$delay_count = 0;
			foreach ($task_aging_info as $a) {
				/*if($a->ahead >= 24){
					$ahead_count++;
				}else if($a->ahead <= -24){
					$delay_count++;
				}*/
				// 不按小时，只有日期提前至少1天才算提前，至少延迟一天才算延迟
				// 如果是当天，不管完成时间在截止日期前还是后，都不算作提前或延迟
				if($a->ahead >= 1){
					$ahead_count++;
				}else if($a->ahead <= -1){
					$delay_count++;
				}
			}
			$users[$key]->task_aging['ahead_rate'] = sprintf("%.2f", empty($finished_task_count)?0:$ahead_count/$finished_task_count);
			$users[$key]->task_aging['delay_rate'] = sprintf("%.2f", empty($finished_task_count)?0:$delay_count/$finished_task_count);
		}
		return $users;
	}

	public function pro_story_stat($begin_date, $end_date){
		if(!strtotime($begin_date) || !strtotime($end_date)){
			return FALSE;
		}
		$projects = $this->db->get_where(TBL_PROJECT, array('is_deleted'=>'0'))->result();
		$result = array();
		$story_where = " s.opened_date>='{$begin_date}' AND s.opened_date<='{$end_date}' ";
		foreach ($projects as $key => $val) {
			// 需求总数，总计工时
			$sql = "SELECT status,COUNT(*) AS total_count,SUM(estimate) AS total_estimate FROM ".TBL_STORY." s WHERE project_id={$val->id} AND {$story_where} GROUP BY status";
			$stat_info = $this->db->query($sql)->result();
			$story_total_count = 0;
			$story_total_estimate = 0;
			$story_finished_count = 0;
			$story_finished_estimate = 0;
			foreach ($stat_info as $v) {
				$story_total_count += $v->total_count;
				$story_total_estimate += $v->total_estimate;
				if($v->status == 'finished' || $v->status == 'closed'){
					$story_finished_count += $v->total_count;
					$story_finished_estimate += $v->total_estimate;
				}
			}
			$result[$val->name]['story_total_count'] = $story_total_count;
			$result[$val->name]['story_total_estimate'] = sprintf("%.2f", $story_total_estimate);
			$result[$val->name]['story_finished_count'] = $story_finished_count;
			$result[$val->name]['story_finished_estimate'] = sprintf("%.2f", $story_finished_estimate);

			// 需求好差评比
			/*$sql = "SELECT s.id AS story_id,s.name,g.* FROM ".TBL_STORY." s JOIN ".TBL_GRADE." g ON g.object_id=s.id WHERE g.type='story' AND s.project_id={$val->id} AND s.status='closed' AND g.is_graded='1' AND {$story_where} GROUP BY s.id";
			$graded_story = $this->db->query($sql)->result();
			$grade_story_count = count($graded_story);//已评价的需求数量
			$story_good_count = 0;//好评需求数
			$story_bad_count = 0;//差评需求数
			foreach ($graded_story as $g) {
				$sql = "SELECT SUM(gd.score) AS score FROM ".TBL_GRADE_SCORE." gs JOIN ".TBL_GRADE_DESCRIPTION." gd ON gs.description_id=gd.id WHERE grade_id={$g->id}";
				$score = $this->db->query($sql)->row();
				if(isset($score->score) && $score->score>2){
					$story_good_count++;
				}else if(isset($score->score) && $score->score<2){
					$story_bad_count++;
				}
			}
			$result[$val->name]['story_good_count'] = $story_good_count;
			$result[$val->name]['story_bad_count'] = $story_bad_count;
			$result[$val->name]['story_good_rate'] = sprintf("%.2f", empty($grade_story_count)?0:$story_good_count/$grade_story_count);
			$result[$val->name]['story_bad_rate'] = sprintf("%.2f", empty($grade_story_count)?0:$story_bad_count/$grade_story_count);*/
			$sql = $sql = "SELECT quality,COUNT(*) AS total_count FROM ".TBL_STORY." s WHERE project_id={$val->id} AND {$story_where} AND quality<>'' GROUP BY quality";
			$story_quality_info = $this->db->query($sql)->result();
			$story_quality_good_count = 0;//评审好的需求数
			$story_quality_average_count = 0;//评审基本符合的需求数
			$story_quality_bad_count = 0;//评审差的需求数
			foreach ($story_quality_info as $info) {
				if($info->quality == 'good'){
					$story_quality_good_count += $info->total_count;
				}else if($info->quality == 'average'){
					$story_quality_average_count += $info->total_count;
				}else if($info->quality == 'bad'){
					$story_quality_bad_count += $info->total_count;
				}
			}
			$result[$val->name]['story_quality_good_count'] = $story_quality_good_count;
			$result[$val->name]['story_quality_average_count'] = $story_quality_average_count;
			$result[$val->name]['story_quality_bad_count'] = $story_quality_bad_count;
			$result[$val->name]['story_quality_good_rate'] = sprintf("%.2f", empty($story_total_count)?0:$story_quality_good_count/$story_total_count);
			$result[$val->name]['story_quality_average_rate'] = sprintf("%.2f", empty($story_total_count)?0:$story_quality_average_count/$story_total_count);
			$result[$val->name]['story_quality_bad_rate'] = sprintf("%.2f", empty($story_total_count)?0:$story_quality_bad_count/$story_total_count);
		}
		// print_r($result);exit;
		return $result;
	}
	
}
