<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Task_model extends MY_Model {

	public function __construct() {
		parent::__construct();
		$this->load->model('user_model');
		$this->load->model('story_model');
		$this->load->model('project_model');
	}

	/**
	 * 按ID获取任务数据
	 * @param  int $task_id 任务ID
	 * @return array $data 任务数据
	 */
	public function get_task_by_id($task_id){
		return empty($task_id) ? NULL : $this->db->get_where(TBL_TASK, array('id' => (int)$task_id))->row();
	}

	/**
	 * 分页显示任务
	 * @param  int $page 第几页
	 * @param  int $task_id 每页数量
	 * @param  array $condition CI Active Record风格的sql条件
	 * @return array $data 页面数据
	 */
	public function get_page($page = 1, $page_size = 50, $base_url, $condition = ''){
		$page = (int)$page < 0 ? 1 : (int)$page;
		$limit = $page_size;
		$start = $limit * ($page - 1);

		$assignedtome = $this->input->get('assignedtome');
		$openedbyme = $this->input->get('openedbyme');
		$reviewedbyme = $this->input->get('reviewedbyme');
		$finishedbyme = $this->input->get('finishedbyme');
		$finished_by = $this->input->get('finished_by');
		$assigned_to = $this->input->get('assigned_to');
		$type = $this->input->get('type');
		$status = $this->input->get('status');
		$allproject = $this->input->get('allproject');
		$keyword = $this->input->get('keyword');
		$keywordtype = $this->input->get('keywordtype');

		$where = ' WHERE is_deleted=\'0\' ';
		$order_by = '';
		if($assignedtome){
			$condition = array('assigned_to'=>$this->current_user_id);
			$where .= ' AND assigned_to='.$this->current_user_id;
		}
		if($openedbyme){
			$condition = array('opened_by'=>$this->current_user_id);
			$where .= ' AND opened_by='.$this->current_user_id;
		}
		if($reviewedbyme){
			$condition = array('assigned_to'=>$this->current_user_id, 'status'=>$this->pmsdata['task']['status']['verifytest']['value']);
			$where .= ' AND assigned_to='.$this->current_user_id.' AND status=\''.$this->pmsdata['task']['status']['verifytest']['value'].'\'';
		}
		if($finishedbyme){
			$condition = array('finished_by'=>$this->current_user_id);
			$where .= ' AND finished_by='.$this->current_user_id;
		}else{
			if($finished_by){
				$condition = array('finished_by'=>(int)$finished_by);
				$where .= ' AND finished_by='.(int)$finished_by;
			}
		}
		if($assigned_to){
			$condition = array('assigned_to'=>(int)$assigned_to);
			$where .= ' AND assigned_to='.(int)$assigned_to;
		}
		if($type){
			$condition = array('type'=>$type);
			$where .= ' AND type=\''.$type.'\'';
		}
		if($status){
			$condition = array('status'=>trim(strtolower($status)));
			$where .= ' AND status=\''.trim(strtolower($status)).'\'';
		}
		if($keyword){
			switch ($keywordtype) {
				case 'id':
					$keyword = intval($keyword);
					$where .= " AND id={$keyword}";
					break;
				
				case 'name':
					$where .= ' AND name LIKE \'%'.trim($keyword).'%\'';
					break;
			}
		}

		if($condition && is_array($condition)){
			// $this->db->where($condition);
		}

		$order = $this->input->get('order');
		$sort = $this->input->get('sort');
		if($order && $sort){
			// $this->db->order_by($order, $sort);
			if($order == 'status'){
				if($sort == 'desc'){
					$order_by = " ORDER BY find_in_set(status,'closed,online,comptest,testing,waittest,verifytest,submittest,wait,doing'), level DESC, deadline ASC";
				}else{
					$order_by = " ORDER BY find_in_set(status,'doing,wait,submittest,verifytest,waittest,testing,comptest,online,closed'), level DESC, deadline ASC";
				}
			}else{
				$order_by = " ORDER BY {$order} {$sort}";
			}
		}else{
			// $this->db->order_by('id', 'DESC');
			$order_by = " ORDER BY find_in_set(status,'doing,wait,submittest,verifytest,waittest,testing,comptest,online,closed'), level DESC, deadline ASC";//逗号两边不能有空格
		}
		// 如果指定了需求ID则取该需求下的任务
		$story_id = $this->input->get('sid');
		if($story_id){
			// $this->db->where('story_id', (int)$story_id);
			$where .= ' AND story_id='.(int)$story_id;
		}else{
			// 获取当前项目下任务的同时获取未指定项目的任务
			// $this->db->where_in('project_id', array($this->current_project_id, 0));
			$pids = array($this->current_project_id, 0);
			if($allproject){
				$this->load->model('project_model');
				$projects = $this->project_model->get_all_projects();
				foreach ($projects as $key => $val) {
					$pids[] = $val->id;
				}
			}
			$where .= ' AND project_id IN ('.join(',',$pids).')';
			// $where .= ' AND project_id IN ('.join(',' ,array($this->current_project_id, 0)).')';
		}
		$query = $this->db->query("SELECT * FROM ".TBL_TASK." {$where} {$order_by} LIMIT {$start},{$limit}");
		// $query = $this->db->get_where(TBL_TASK, array('is_deleted' => '0'), $limit, $start);
		$result = $query->result();
		$total_estimate = 0;
		$total_consumed = 0;
		$total_left = 0;
		foreach($result as $key=>$val){
			$total_estimate += $val->estimate;
			$total_consumed += $val->consumed;
			$total_left += $val->estimate - $val->consumed;
			$result[$key]->story = $this->story_model->get_story_by_id($val->story_id);
			$result[$key]->opened_by = $this->user_model->get_user_by_id($val->opened_by);
			$result[$key]->finished_by = $this->user_model->get_user_by_id($val->finished_by);
			$result[$key]->assigned_to = $this->user_model->get_user_by_id($val->assigned_to);
			$result[$key]->test_by = $this->user_model->get_user_by_id($val->test_by);
		}
		$data['data'] = $result;
		$data['total_estimate'] = $total_estimate;
		$data['total_consumed'] = $total_consumed;
		$data['total_left'] = $total_left;

		$query = $this->db->query("SELECT COUNT(*) AS total FROM ".TBL_TASK." {$where}");
		$data['total'] = $query->num_rows() > 0 ? $query->row()->total : 0;
		$data['current_page'] = $page;
		$data['total_page'] = (int)(($data['total']-1)/$limit + 1);
		$data['page_html'] = $this->create_page($data['total'], $page_size, $base_url);
		return $data;
	}

	/**
	 * 保存任务(插入和更新)
	 * @return mixed 添加成功返回任务ID, 否则返回FALSE
	 */
	public function save_task(){
		// print_r($this->input->post());exit;
		$project_id = $this->input->post('project_id');
		$story_id = $this->input->post('story_id');
		$task_id = $this->input->post('task_id');
		$name = $this->input->post('name', TRUE);
		$type = $this->input->post('type');
		$status = $this->input->post('status');
		$estimate = $this->input->post('estimate');
		$consumed = $this->input->post('consumed');
		$deadline = $this->input->post('deadline');
		$description = $this->input->post('description');// 不要进行xss过滤
		$opened_by = $this->input->post('opened_by');
		$opened_date = $this->input->post('opened_date');
		$assigned_to = $this->input->post('assigned_to');
		$assigned_date = $this->input->post('assigned_date');
		$finished_by = $this->input->post('finished_by');
		$finished_date = $this->input->post('finished_date');
		$canceled_by = $this->input->post('canceled_by');
		$canceled_date = $this->input->post('canceled_date');
		$closed_by = $this->input->post('closed_by');
		$closed_date = $this->input->post('closed_date');
		$closed_reason = $this->input->post('closed_reason');
		$last_edited_by = $this->input->post('last_edited_by');
		$last_edited_date = $this->input->post('last_edited_date');
		$is_deleted = $this->input->post('is_deleted');
		$level = $this->input->post('level');
		$est_started_date = $this->input->post('est_started_date');
		$real_started_date = $this->input->post('real_started_date');
		$difficulty = $this->input->post('difficulty');
		$file_id = $this->input->post('file_id');
		$action = $this->input->post('action');
		$need_test = $this->input->post('need_test');
		$need_summary = $this->input->post('need_summary');
		$test_by = $this->input->post('test_by');

		$comment = $this->input->post('comment');
		// 服务端验证
		// project_id有为0的情况
		if($project_id !== FALSE && !isset($project_id)){
			show_msg(ERROR ,'请选择项目');
		}
		if($name !== FALSE && empty($name)){
			show_msg(ERROR ,'请填写任务名称');
		}
		if($description !== FALSE && empty($description)){
			show_msg(ERROR ,'请填写任务描述');
		}
		if($estimate !== FALSE && empty($estimate)){
			show_msg(ERROR ,'请填写预计工时');
		}
		if($deadline !== FALSE && empty($deadline)){
			show_msg(ERROR ,'请填写最后完成时间');
		}
		if($type !== FALSE && empty($type)){
			show_msg(ERROR ,'请选择任务类型');
		}

		$con_var = empty($task_id)?$name:$task_id;
		if(!is_array($con_var)) {
			$story_id = array($story_id);
			$task_id = array($task_id);
			$name = array($name);
			$type = array($type);
			$status = array($status);
			$estimate = array($estimate);
			$consumed = array($consumed);
			$deadline = array($deadline);
			$description = array($description);
			$opened_by = array($opened_by);
			$opened_date = array($opened_date);
			$assigned_to = array($assigned_to);
			$assigned_date = array($assigned_date);
			$finished_by = array($finished_by);
			$finished_date = array($finished_date);
			$canceled_by = array($canceled_by);
			$canceled_date = array($canceled_date);
			$closed_by = array($closed_by);
			$closed_date = array($closed_date);
			$closed_reason = array($closed_reason);
			$last_edited_by = array($last_edited_by);
			$last_edited_date = array($last_edited_date);
			$is_deleted = array($is_deleted);
			$level = array($level);
			$est_started_date = array($est_started_date);
			$real_started_date = array($real_started_date);
			$difficulty = array($difficulty);
			$need_test = array($need_test);
			$need_summary = array($need_summary);
			$test_by = array($test_by);
		}
		$this->db->trans_start();
		$cur_task_id = 0;
		$foreach_var = empty($task_id)?$name:$task_id;
		//跳转页面, 批量操作跳转到列表页, 单个操作跳转到详情页.
		$redirect_url = count($foreach_var) > 1 || empty($task_id[0]) ? "/task" : "/task/view/".$task_id[0];
		foreach ($foreach_var as $key => $val) {
			$_estimate = isset($estimate[$key]) ? (float)$estimate[$key] : 0;
			$_assigned_to = isset($assigned_to[$key]) ? (int)$assigned_to[$key] : 0;
			$_assigned_date = $_assigned_to ? date('Y-m-d H:i:s') : 0;

			$is_edit = FALSE;
			if(empty($task_id[$key])){
				if(empty($name[$key])){
					continue;
				}
				$old = array();
				$data = array(
					'project_id' => $project_id,
					'story_id' => $story_id[$key],
					'name' => $name[$key],
					'type' => $type[$key],
					'status' => $this->pmsdata['task']['status']['wait']['value'],
					'estimate' => $_estimate,
					'consumed' => 0,
					'deadline' => $deadline[$key],
					'level' => $level[$key],
					'difficulty' => $difficulty[$key],
					'est_started_date' => empty($est_started_date[$key])?0:$est_started_date[$key],
					'real_started_date' => 0,
					'description' => $description[$key],
					'opened_by' => $this->current_user_id,
					'opened_date' => date('Y-m-d H:i:s'),
					'assigned_to' => $_assigned_to,
					'assigned_date' => $_assigned_date,
					'finished_by' => 0,
					'finished_date' => 0,
					'canceled_by' => 0,
					'canceled_date' => 0,
					'closed_by' => 0,
					'closed_date' => 0,
					'closed_reason' => '',
					'last_edited_by' => $this->current_user_id,
					'last_edited_date' => date('Y-m-d H:i:s'),
					'need_test' => empty($need_test[$key])?'0':(string)$need_test[$key],//数据库为枚举类型, 这里需要为string
					'need_summary' => empty($need_summary[$key])?'0':(string)$need_summary[$key],
					'test_by' => $need_test[$key] ? $test_by[$key] : '0',
					'test_date' => 0,
					'test_finished_date' => 0,
					'online_date' => 0,
					'is_deleted' => '0'
				);
				$this->db->insert(TBL_TASK, $data);
				$cur_task_id = $this->db->insert_id();
				$action_data = array(
					'project_id' => $project_id,
					'object_id' => $cur_task_id,
					'type' => $this->pmsdata['task']['value'],
					'action' => $this->pmsdata['task']['action']['opened']['value']
				);
				// 给执行任务的人发送邮件
				$subject = str_replace('#name#', $data['name'], $this->email_subjects['task_assigined']);
				$content = "<a href='{$this->base_url}/task/view/{$cur_task_id}'>{$data['name']}</a>";
				$performer = $this->user_model->get_user_by_id($_assigned_to);
				if($performer && !empty($performer->email)){
					$this->send_mail($performer->email, $subject, $content);
				}
			}else{
				$is_edit = TRUE;
				$cur_task_id = $task_id[$key];
				$old = $this->db->get_where(TBL_TASK, array('id' => $cur_task_id))->row();
				$data = array(
					'last_edited_by' => $this->current_user_id,
					'last_edited_date' => date('Y-m-d H:i:s')
				);

				if(!empty($project_id)){
					$data['project_id'] = $project_id;
				}
				if(!empty($story_id[$key])){
					$data['story_id'] = $story_id[$key];
				}
				if(!empty($level[$key])){
					$data['level'] = $level[$key];
				}
				if(!empty($difficulty[$key])){
					$data['difficulty'] = $difficulty[$key];
				}
				if(!empty($_estimate)){
					$data['estimate'] = $_estimate;
				}
				if(!empty($est_started_date[$key])){
					$data['est_started_date'] = $est_started_date[$key];
				}
				if(!empty($real_started_date[$key])){
					$data['real_started_date'] = $real_started_date[$key];
				}
				if(!empty($deadline[$key])){
					$data['deadline'] = $deadline[$key];
				}

				if(isset($this->pmsdata['task']['types'][$type[$key]])){
					$data['type'] = $type[$key];
				}
				if(isset($this->pmsdata['task']['status'][$status[$key]])){
					$data['status'] = $status[$key];
				}

				if(!empty($name[$key])){
					$data['name'] = $name[$key];
				}
				if(!empty($description[$key])){
					$data['description'] = $description[$key];
				}

				if(!empty($need_test[$key])){
					$data['need_test'] = (string)$need_test[$key];
				}
				if(!empty($need_summary[$key])){
					$data['need_summary'] = (string)$need_summary[$key];
				}
				if(!empty($need_test[$key]) && !empty($test_by[$key])){
					$data['test_by'] = $test_by[$key];
				}
				if(isset($need_test[$key]) && $need_test[$key] === '0'){
					$data['need_test'] = '0';
					$data['test_by'] = 0;
				}
				if(isset($need_summary[$key]) && $need_summary[$key] === '0'){
					$data['need_summary'] = '0';
				}

				$referer = empty($_SERVER['HTTP_REFERER'])?'':$_SERVER['HTTP_REFERER'];
				// 指派
				$is_assigned = FALSE;
				if(!empty($_assigned_to) && $_assigned_to != $old->assigned_to){
					$data['assigned_to'] = $_assigned_to;
					$data['assigned_date'] = $_assigned_date;
					$is_assigned = TRUE;
				}
				// 开始
				$is_started = FALSE;
				if(stripos($referer, 'task/start') !== FALSE || $action == 'start'){
					$data['real_started_date'] = date('Y-m-d H:i:s');
					if(!empty($consumed[$key])){
						$data['consumed'] = (float)$consumed[$key];
					}
					$data['status'] = $this->pmsdata['task']['status']['doing']['value'];
					$is_started = TRUE;
				}
				// 关闭
				$is_closed = FALSE;
				if(!empty($closed_reason[$key])){
					$data['closed_reason'] = $closed_reason[$key];
					$data['closed_by'] = $this->current_user_id;
					$data['closed_date'] = date('Y-m-d H:i:s');
					$data['status'] = $this->pmsdata['task']['status']['closed']['value'];
					$is_closed = TRUE;
				}
				// 激活
				$is_actived = FALSE;
				// 上个状态为已关闭,已取消,已完成的情况下再指派则判断为激活
				$acrive_status = array(
					// $this->pmsdata['task']['status']['done']['value'],
					$this->pmsdata['task']['status']['canceled']['value'],
					$this->pmsdata['task']['status']['closed']['value']
				);
				if(in_array($old->status, $acrive_status) && !empty($_assigned_to)){
					$data['status'] = $this->pmsdata['task']['status']['doing']['value'];
					$data['assigned_to'] = $_assigned_to;
					$data['assigned_date'] = $_assigned_date;
					if(!empty($consumed[$key])){
						$data['consumed'] = (float)$consumed[$key];
					}
					$is_actived = TRUE;
				}
				// 取消
				$is_canceled = FALSE;
				if(stripos($referer, 'task/cancel') !== FALSE){
					$data['canceled_by'] = $this->current_user_id;
					$data['canceled_date'] = date('Y-m-d H:i:s');
					$data['status'] = $this->pmsdata['task']['status']['canceled']['value'];
					$is_canceled = TRUE;
				}
				// 完成
				$is_finished = FALSE;
				if($action == 'finish'){
					$data['finished_by'] = $this->current_user_id;
					$data['finished_date'] = date('Y-m-d H:i:s');
					$data['status'] = $this->pmsdata['task']['status']['done']['value'];
					if(!empty($consumed[$key])){
						$data['consumed'] = (float)$consumed[$key];
					}
					// 创建一条任务评分数据
					$data_grade = array(
						'type' => 'task',
						'object_id' => $task_id[$key],
						'grade_by' => $old->opened_by,
						'grade_date' => 0,
						'is_graded' => '0'
					);
					$this->db->insert(TBL_GRADE, $data_grade);
					$is_finished = TRUE;
				}
				// 删除
				$deleted = FALSE;
				if(!empty($is_deleted[$key])){
					$data['is_deleted'] = '1';
					$deleted = TRUE;
				}

				// print_r($data);exit;
				
				$action = $this->pmsdata['task']['action']['edited']['value'];
				$subject = isset($data['name']) ? $data['name'] : $old->name;
				$content = "<a href='{$this->base_url}/story/view/{$cur_task_id}'>{$subject}</a>";
				if($is_assigned){
					$action = $this->pmsdata['task']['action']['assigned']['value'];
					// 给执行任务的人发送邮件
					$subject = str_replace('#name#', $subject, $this->email_subjects['task_assigined']);
					$performer = $this->user_model->get_user_by_id($_assigned_to);
					if($performer && !empty($performer->email)){
						$this->send_mail($performer->email, $subject, $content);
					}
				}
				if($is_started){
					$action = $this->pmsdata['task']['action']['started']['value'];
				}
				if($is_canceled){
					$action = $this->pmsdata['task']['action']['canceled']['value'];
				}
				if($is_closed){
					$action = $this->pmsdata['task']['action']['closed']['value'];
				}
				if($is_actived){
					$action = $this->pmsdata['task']['action']['actived']['value'];
				}
				if($deleted){
					$action = $this->pmsdata['task']['action']['deleted']['value'];
				}
				if($is_finished){
					$action = $this->pmsdata['task']['action']['finished']['value'];
					// 给任务创建人发送邮件
					$subject = str_replace('#name#', $subject, $this->email_subjects['task_wait_test_review']);
					$opener = $this->user_model->get_user_by_id($old->opened_by);
					if($opener && !empty($opener->email)){
						$this->send_mail($opener->email, $subject, $content);
					}
				}
				
				$this->db->where('id', $cur_task_id);
				$this->db->update(TBL_TASK, $data);
				$action_data = array(
					'project_id' => $project_id,
					'object_id' => $cur_task_id,
					'type' => $this->pmsdata['task']['value'],
					'action' => $action
				);
			}

			$changes = $this->create_change($old, $data);
			$action_id = NULL;
			if($is_edit){
				// 有改动的时候才创建编辑动作
				if(!empty($changes) || (count($_FILES) > 0 && $comment === FALSE)){
					$action_id = $this->create_action($action_data);
					if(!$action_id){
						$this->db->trans_rollback();
						continue;
					}
				}
			}else{
				// 新建的时候直接创建动作
				$action_id = $this->create_action($action_data);
			}
			// 有备注的时候创建备注动作
			// 可以允许空备注(有附件的时候)
			$has_file = FALSE;
			foreach ($_FILES as $key => $val) {
				foreach ($val['tmp_name'] as $k => $v) {
					if(is_uploaded_file($v)){
						$has_file = TRUE;
					}
				}
			}
			if($comment !== FALSE && (!empty($comment) || (empty($comment) && $has_file))){
				if(is_string($comment)){
					$comment = array($comment);
				}
				foreach ($comment as $key => $val) {
					$action_data = array(
						'project_id' => $project_id,
						'object_id' => $cur_task_id,
						'type' => $this->pmsdata['task']['value'],
						'action' => $this->pmsdata['task']['action']['commented']['value'],
						'comment' => $val
					);
					$comment_action_id = $this->create_action($action_data);
					if(!$comment_action_id){
						$this->db->trans_rollback();
						continue;
					}
				}
			}
			$this->save_changes($action_id, $changes);
			// 处理附件
			$atta_type = 'task';
			$object_id = $cur_task_id;
			if(!empty($comment_action_id)){
				$atta_type = 'action';
				$object_id = $comment_action_id;
			}
			$this->save_attachment($project_id, $atta_type, $object_id, 'files', 'labels');
			// 处理从需求带过来的附件
			if($file_id && is_array($file_id)){
				$file_data = array();
				$action_data = array();
				foreach ($file_id as $key => $val) {
					$fileinfo = $this->db->get_where(TBL_FILE, array('id' => (int)$val))->row_array();
					if($fileinfo){
						unset($fileinfo['id']);
						$fileinfo['type'] = 'task';
						$fileinfo['object_id'] = $cur_task_id;
						$fileinfo['added_by'] = $this->current_user_id;
						$fileinfo['added_date'] = date('Y-m-d H:i:s');
						$fileinfo['downloads'] = 0;
						$file_data[] = $fileinfo;

						$action = array(
							'project_id' => $project_id,
							'object_id' => $cur_task_id,
							'type' => 'task',
							'action' => 'edited',
							'comment' => '上传了附件: '.$fileinfo['title']
						);
						$action_data[] = $action;
						unset($fileinfo);
						unset($action);
					}
				}
				if(!empty($file_data)){
					$this->db->insert_batch(TBL_FILE, $file_data);
					array_walk($action_data, array($this, 'create_action'));
				}
			}
		}
		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE){
			show_msg(ERROR ,'保存失败');
			// return FALSE;
		}
		$body = intval($this->input->post('body'));
		if(!$is_edit){
			show_msg(INFO, '任务创建成功', '', array(array('name'=>'继续分解任务','url'=>$_SERVER['HTTP_REFERER']),array('name'=>'返回任务列表','url'=>'/task?body='.$body),array('name'=>'返回需求列表','url'=>'/story?body='.$body)));
		}
		header("Location:{$redirect_url}");
		exit;
		// return TRUE;
	}

	/**
	 * 移除任务(直接从数据库中删除), 同时删除该任务下所有的任务
	 * 支持批量移除
	 * @param  mixed $task_id 任务ID
	 * @return boolean 是否移除成功
	 */
	public function remove_task($task_id){
		if(empty($task_id)){
			return FALSE;
		}

		$task_id = is_array($task_id) ? $task_id : array($task_id);

		$this->db->trans_start();
		$this->db->where_in('id', $task_id);
		$this->db->delete(TBL_TASK);

		// 删除任务同时删除任务下所有任务
		$this->db->where_in('task_id', $task_id);
		$this->db->delete(TBL_TASK);

		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE){
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * 获取任务历史记录
	 * @param $task_id 任务ID
	 * @return array 任务详情
	 */
	public function get_task_history($task_id){
		// $this->db->order_by(TBL_ACTION.'.id', 'desc');
		$this->db->select(TBL_ACTION.'.*,'.TBL_USER.'.real_name AS actor');
		$this->db->join(TBL_USER, TBL_ACTION.'.actor_id = '.TBL_USER.'.id');
		$task_actions = $this->db->get_where(TBL_ACTION, array('type' => $this->pmsdata['task']['value'], 'object_id' => $task_id))->result();
		// $task_actions = $this->db->query("SELECT a.*,u.account,u.real_name FROM ".TBL_ACTION." a JOIN ".TBL_USER." u ON a.actor_id=u.id WHERE a.type='task' AND a.object_id={$task_id}")->result();
		foreach ($task_actions as $key => $val) {
			$histories = $this->db->get_where(TBL_HISTORY, array('action_id' => $val->id))->result();
			foreach ($histories as $k => $v) {
				if($v->diff != ''){
				    $v->diff = str_replace(array('<ins>', '</ins>', '<del>', '</del>'), array('[ins]', '[/ins]', '[del]', '[/del]'), $v->diff);
				    $v->diff = ($v->field != 'subversion' && $v->field != 'git') ? htmlspecialchars($v->diff) : $v->diff;
				    $v->diff = str_replace(array('[ins]', '[/ins]', '[del]', '[/del]'), array('<ins>', '</ins>', '<del>', '</del>'), $v->diff);
				    $v->diff = nl2br($v->diff);
				    $v->noTagDiff = preg_replace('/&lt;\/?([a-z][a-z0-9]*)[^\/]*\/?&gt;/Ui', '', $v->diff);
				}
			}
			$val->history = $histories;
			$attachments = $this->db->get_where(TBL_FILE, array('object_id' => $val->id, 'type' => 'action'))->result();
			$val->attachments = $attachments;
		}
		// print_r($task_actions);exit;
		return $task_actions;
	}

	/**
	 * 获取任务附件
	 * @param $task_id 任务ID
	 * @return array 附件数组
	 */
	public function get_task_attachment($task_id){
		if(empty($task_id)){
			return NULL;
		}
		$result = $this->db->get_where(TBL_FILE, array('type' => 'task','object_id' => (int)$task_id, 'is_deleted' => '0'))->result();
		return $result; 
	}

	/**
	 * 加载任务详情(支持批量更新)
	 * @param $task_id 任务ID
	 * @return array 任务详情
	 */
	public function get_task_detail($task_id){
		$task = $this->get_task_by_id($task_id);
		if(empty($task)){
			return NULL;
		}
		$task->actions = $this->get_task_history($task->id);
		$task->opened_by = $this->user_model->get_user_by_id($task->opened_by);
		$task->last_edited_by = $this->user_model->get_user_by_id($task->last_edited_by);
		$task->closed_by = $this->user_model->get_user_by_id($task->closed_by);
		$task->canceled_by = $this->user_model->get_user_by_id($task->canceled_by);
		$task->finished_by = $this->user_model->get_user_by_id($task->finished_by);
		$task->assigned_to = $this->user_model->get_user_by_id($task->assigned_to);
		$task->test_by = $this->user_model->get_user_by_id($task->test_by);
		$task->attachments = $this->get_task_attachment($task->id);
		$task->project = $this->project_model->get_project_by_id($task->project_id);
		$story = $this->story_model->get_story_by_id($task->story_id);
		$story->opener = $this->user_model->get_user_by_id($story->opened_by);
		$task->story = $story;

		$tasks = $this->story_model->get_story_task($task->story_id);
		foreach ($tasks as $key => $val) {
			if($val->id == $task->id){
				unset($tasks[$key]);
				continue;
			}
			$tasks[$key]->assigned_to = $this->user_model->get_user_by_id($val->assigned_to);
			$tasks[$key]->finished_by = $this->user_model->get_user_by_id($val->finished_by);
		}
		$task->tasks = $tasks;
		return $task;
	}

	public function start($tid){
		$task = $this->get_task_by_id($tid);
		if(!$task){
			show_404();
		}
		$this->db->trans_start();

		$data = array(
			// 'assigned_to' => $this->current_user_id,
			// 'assigned_date' => date('Y-m-d H:i:s'),
			'real_started_date' => date('Y-m-d H:i:s'),
			'status' => $this->pmsdata['task']['status']['doing']['value'],
			'last_edited_by' => $this->current_user_id,
			'last_edited_date' => date('Y-m-d H:i:s')
		);
		$this->db->where('id', $task->id);
		$this->db->update(TBL_TASK, $data);

		$action_data = array(
			'project_id' => $task->project_id,
			'object_id' => $task->id,
			'type' => $this->pmsdata['task']['value'],
			'action' => $this->pmsdata['task']['action']['started']['value']
		);
		$action_id = $this->create_action($action_data);
		$changes = $this->create_change($task, $data);
		$this->save_changes($action_id, $changes);

		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE){
			show_msg(ERROR ,'保存失败');
		}
		header("Location:".$_SERVER['HTTP_REFERER']);
		exit;
	}

	public function submittest(){
		$tid = $this->input->post('task_id');
		$comment = $this->input->post('comment');
		$summary = $this->input->post('summary');
		$consumed = $this->input->post('consumed')?(float)$this->input->post('consumed'):0;
		$task = $this->get_task_by_id($tid);
		if(!$task){
			show_404();
		}
		$this->db->trans_start();

		$data = array(
			'consumed' => $consumed,
			'assigned_to' => $task->opened_by,
			'assigned_date' => date('Y-m-d H:i:s'),
			'finished_by' => $this->current_user_id,
			'finished_date' => date('Y-m-d H:i:s'),
			'status' => $this->pmsdata['task']['status']['verifytest']['value'],
			'last_edited_by' => $this->current_user_id,
			'last_edited_date' => date('Y-m-d H:i:s')
		);
		if(!empty($summary)){
			$data['summary'] = $summary;
		}
		$this->db->where('id', $task->id);
		$this->db->update(TBL_TASK, $data);

		$action_data = array(
			'project_id' => $task->project_id,
			'object_id' => $task->id,
			'type' => $this->pmsdata['task']['value'],
			'action' => $this->pmsdata['task']['action']['submittest']['value']
		);
		$action_id = $this->create_action($action_data);
		$changes = $this->create_change($task, $data);
		$this->save_changes($action_id, $changes);

		// 有备注的时候创建备注动作
		if(!empty($comment)){
			$action_data = array(
				'project_id' => $task->project_id,
				'object_id' => $task->id,
				'type' => $this->pmsdata['task']['value'],
				'action' => $this->pmsdata['task']['action']['commented']['value'],
				'comment' => $comment
			);
			$comment_action_id = $this->create_action($action_data);
		}
		// 有任务心得的时候创建
		if(!empty($summary)){
			$action_data = array(
				'project_id' => $task->project_id,
				'object_id' => $task->id,
				'type' => $this->pmsdata['task']['value'],
				'action' => $this->pmsdata['task']['action']['summary']['value'],
				'comment' => $summary
			);
			$summary_action_id = $this->create_action($action_data);
		}
		// 处理附件
		$this->save_attachment($task->project_id, 'action', $action_id, 'files', 'labels');

		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE){
			show_msg(ERROR ,'保存失败');
		}
		// 给任务创建人发送邮件
		$subject = str_replace('#name#', $task->name, $this->email_subjects['task_wait_test_review']);
		$content = "<a href='{$this->base_url}/task/view/{$task->id}'>{$task->name}</a>";
		$opener = $this->user_model->get_user_by_id($task->opened_by);
		if($opener && !empty($opener->email)){
			$this->send_mail($opener->email, $subject, $content);
		}
		header("Location:/task/view/{$task->id}");
		exit;
	}

	/*public function verifytest($tid){
		$task = $this->get_task_by_id($tid);
		if(!$task){
			show_404();
		}
		$this->db->trans_start();

		$data = array(
			'status' => $this->pmsdata['task']['status']['verifytest']['value'],
			'last_edited_by' => $this->current_user_id,
			'last_edited_date' => date('Y-m-d H:i:s')
		);
		$this->db->where('id', $task->id);
		$this->db->update(TBL_TASK, $data);

		$action_data = array(
			'project_id' => $task->project_id,
			'object_id' => $task->id,
			'type' => $this->pmsdata['task']['value'],
			'action' => $this->pmsdata['task']['action']['verifytest']['value']
		);
		$action_id = $this->create_action($action_data);
		$changes = $this->create_change($task, $data);
		$this->save_changes($action_id, $changes);

		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE){
			show_msg(ERROR ,'保存失败');
		}
		header("Location:".$_SERVER['HTTP_REFERER']);
		exit;
	}*/

	public function verifyok($tid){
		$task = $this->get_task_by_id($tid);
		if(!$task){
			show_404();
		}
		$this->db->trans_start();

		$data = array(
			'assigned_to' => $task->test_by,
			'assigned_date' => date('Y-m-d H:i:s'),
			'status' => $this->pmsdata['task']['status']['waittest']['value'],
			'last_edited_by' => $this->current_user_id,
			'last_edited_date' => date('Y-m-d H:i:s')
		);
		if($task->need_test == 0){
			$data['assigned_to'] = $task->finished_by;
			$data['status'] = $this->pmsdata['task']['status']['comptest']['value'];
		}
		$this->db->where('id', $task->id);
		$this->db->update(TBL_TASK, $data);

		$action_data = array(
			'project_id' => $task->project_id,
			'object_id' => $task->id,
			'type' => $this->pmsdata['task']['value'],
			'action' => $this->pmsdata['task']['action']['verifyok']['value']
		);
		if($task->need_test == 0){
			$action_data['type'] = $this->pmsdata['task']['value'];
			$action_data['action'] = $this->pmsdata['task']['action']['comptest']['value'];

		}
		$action_id = $this->create_action($action_data);
		$changes = $this->create_change($task, $data);
		$this->save_changes($action_id, $changes);

		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE){
			show_msg(ERROR ,'保存失败');
		}
		// 给测试人发送邮件
		if($task->need_test == 1){
			$subject = str_replace('#name#', $task->name, $this->email_subjects['task_wait_test']);
			$content = "<a href='{$this->base_url}/task/view/{$task->id}'>{$task->name}</a>";
			$tester = $this->user_model->get_user_by_id($task->test_by);
			if($tester && !empty($tester->email)){
				$this->send_mail($tester->email, $subject, $content);
			}
		}
		header("Location:".$_SERVER['HTTP_REFERER']);
		exit;
	}

	public function starttest($tid){
		$task = $this->get_task_by_id($tid);
		if(!$task){
			show_404();
		}
		$this->db->trans_start();

		$data = array(
			'test_date' => date('Y-m-d H:i:s'),
			'status' => $this->pmsdata['task']['status']['testing']['value'],
			'last_edited_by' => $this->current_user_id,
			'last_edited_date' => date('Y-m-d H:i:s')
		);
		$this->db->where('id', $task->id);
		$this->db->update(TBL_TASK, $data);

		$action_data = array(
			'project_id' => $task->project_id,
			'object_id' => $task->id,
			'type' => $this->pmsdata['task']['value'],
			'action' => $this->pmsdata['task']['action']['starttest']['value']
		);
		$action_id = $this->create_action($action_data);
		$changes = $this->create_change($task, $data);
		$this->save_changes($action_id, $changes);

		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE){
			show_msg(ERROR ,'保存失败');
		}
		header("Location:".$_SERVER['HTTP_REFERER']);
		exit;
	}

	public function finishtest($tid){
		$task = $this->get_task_by_id($tid);
		if(!$task){
			show_404();
		}
		$this->db->trans_start();

		$data = array(
			'assigned_to' => $task->finished_by,
			'assigned_date' => date('Y-m-d H:i:s'),
			'test_finished_date' => date('Y-m-d H:i:s'),
			'status' => $this->pmsdata['task']['status']['comptest']['value'],
			'last_edited_by' => $this->current_user_id,
			'last_edited_date' => date('Y-m-d H:i:s')
		);
		$this->db->where('id', $task->id);
		$this->db->update(TBL_TASK, $data);

		$action_data = array(
			'project_id' => $task->project_id,
			'object_id' => $task->id,
			'type' => $this->pmsdata['task']['value'],
			'action' => $this->pmsdata['task']['action']['comptest']['value']
		);
		$action_id = $this->create_action($action_data);
		$changes = $this->create_change($task, $data);
		$this->save_changes($action_id, $changes);

		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE){
			show_msg(ERROR ,'保存失败');
		}
		// 给完成任务的人发送邮件
		$subject = str_replace('#name#', $task->name, $this->email_subjects['task_wait_online']);
		$content = "<a href='{$this->base_url}/task/view/{$task->id}'>{$task->name}</a>";
		$finisher = $this->user_model->get_user_by_id($task->finished_by);
		if($finisher && !empty($finisher->email)){
			$this->send_mail($finisher->email, $subject, $content);
		}
		header("Location:".$_SERVER['HTTP_REFERER']);
		exit;
	}

	public function online($tid){
		$tid = empty($tid) ? ($this->input->post('task_id')?$this->input->post('task_id'):0) : $tid;
		$task = $this->get_task_by_id($tid);
		if(!$task){
			show_404();
		}
		$comment = $this->input->post('comment');
		$summary = $this->input->post('summary');
		$this->db->trans_start();
		$data = array(
			'assigned_to' => $task->opened_by,
			'assigned_date' => date('Y-m-d H:i:s'),
			'online_date' => date('Y-m-d H:i:s'),
			'status' => $this->pmsdata['task']['status']['online']['value'],
			'last_edited_by' => $this->current_user_id,
			'last_edited_date' => date('Y-m-d H:i:s')
		);
		if(!empty($summary)){
			$data['summary'] = $summary;
		}
		// if($task->need_test == 0){
		// 	$data['finished_by'] = $this->current_user_id;
		// 	$data['finished_date'] = date('Y-m-d H:i:s');
		// 	$data['consumed'] = $this->input->post('consumed') ? (float)$this->input->post('consumed') : $task->consumed;
		// }
		$this->db->where('id', $task->id);
		$this->db->update(TBL_TASK, $data);

		$action_data = array(
			'project_id' => $task->project_id,
			'object_id' => $task->id,
			'type' => $this->pmsdata['task']['value'],
			'action' => $this->pmsdata['task']['action']['online']['value']
		);
		$action_id = $this->create_action($action_data);
		$changes = $this->create_change($task, $data);
		$this->save_changes($action_id, $changes);

		// 有备注的时候创建备注动作
		if(!empty($comment)){
			$action_data = array(
				'project_id' => $task->project_id,
				'object_id' => $task->id,
				'type' => $this->pmsdata['task']['value'],
				'action' => $this->pmsdata['task']['action']['commented']['value'],
				'comment' => $comment
			);
			$comment_action_id = $this->create_action($action_data);
		}
		if(!empty($summary)){
			$action_data = array(
				'project_id' => $task->project_id,
				'object_id' => $task->id,
				'type' => $this->pmsdata['task']['value'],
				'action' => $this->pmsdata['task']['action']['summary']['value'],
				'comment' => $summary
			);
			$summary_action_id = $this->create_action($action_data);
		}
		// 处理附件
		$this->save_attachment($task->project_id, 'action', $action_id, 'files', 'labels');

		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE){
			show_msg(ERROR ,'保存失败');
		}
		// 给任务创建人发送邮件
		$subject = str_replace('#name#', $task->name, $this->email_subjects['task_wait_close']);
		$content = "<a href='{$this->base_url}/task/view/{$task->id}'>{$task->name}</a>";
		$opener = $this->user_model->get_user_by_id($task->opened_by);
		if($opener && !empty($opener->email)){
			$this->send_mail($opener->email, $subject, $content);
		}
		// if($task->need_test == 0){
		// 	header("Location:/task/view/{$task->id}");
		// }else{
		// 	header("Location:".$_SERVER['HTTP_REFERER']);
		// }
		header("Location:".$_SERVER['HTTP_REFERER']);
		exit;
	}

	public function close($tid){
		$task = $this->get_task_by_id($tid);
		if(!$task){
			show_404();
		}
		$this->db->trans_start();

		$data = array(
			'closed_by' => $this->current_user_id,
			'closed_date' => date('Y-m-d H:i:s'),
			'closed_reason' => $task->status == $this->pmsdata['task']['status']['canceled']['value'] ? $this->pmsdata['task']['close_reason']['cancel']['value'] : $this->pmsdata['task']['close_reason']['done']['value'],
			'status' => $this->pmsdata['task']['status']['closed']['value'],
			'last_edited_by' => $this->current_user_id,
			'last_edited_date' => date('Y-m-d H:i:s')
		);
		$this->db->where('id', $task->id);
		$this->db->update(TBL_TASK, $data);

		$action_data = array(
			'project_id' => $task->project_id,
			'object_id' => $task->id,
			'type' => $this->pmsdata['task']['value'],
			'action' => $this->pmsdata['task']['action']['closed']['value']
		);
		$action_id = $this->create_action($action_data);
		$changes = $this->create_change($task, $data);
		$this->save_changes($action_id, $changes);

		// 如果关闭前状态为已取消，则不生成评价任务，也不处理相关需求，直接返回。
		if($task->status == $this->pmsdata['task']['status']['canceled']['value']){
			$this->db->trans_complete();
			if ($this->db->trans_status() === FALSE){
				show_msg(ERROR ,'保存失败');
			}
			header("Location:".$_SERVER['HTTP_REFERER']);
			exit;
		}

		// 创建一条任务评分数据
		$data_grade = array(
			'type' => 'task',
			'object_id' => $task->id,
			'grade_by' => $task->opened_by,
			'grade_date' => 0,
			'is_graded' => '0'
		);
		$this->db->insert(TBL_GRADE, $data_grade);
		$grade_id = $this->db->insert_id();

		// 如果该需求所有的任务都已关闭, 则完成该任务对应需求
		if($task->story_id > 0){
			$tasks = $this->db->get_where(TBL_TASK, array('is_deleted'=>'0', 'story_id'=>$task->story_id))->result();
			$is_all_finished = TRUE;
			foreach ($tasks as $key => $val) {
				if($val->status != $this->pmsdata['task']['status']['closed']['value']){
					$is_all_finished = FALSE;
					break;
				}
			}
			if($is_all_finished){
				$story = $this->db->get_where(TBL_STORY, array('id'=>$task->story_id))->row();
				// $story_data['closed_reason'] = $this->pmsdata['story']['close_reason']['done']['value'];
				// $story_data['closed_by'] = $this->current_user_id;
				// $story_data['closed_date'] = date('Y-m-d H:i:s');
				// 完成需求
				$story_data['status'] = $this->pmsdata['story']['status']['finished']['value'];
				$story_data['finished_date'] = date('Y-m-d H:i:s');
				$story_data['stage'] = $this->pmsdata['story']['stages']['released']['value'];
				$this->db->where('id', $story->id);
				$this->db->update(TBL_STORY, $story_data);
				// 给需求指派人发送邮件
				$subject = str_replace('#name#', $story->name, $this->email_subjects['story_finished']);
				$content = "<a href='{$this->base_url}/story/view/{$story->id}'>{$story->name}</a>";
				$opener = $this->user_model->get_user_by_id($story->assigned_to);
				if($opener && !empty($opener->email)){
					$this->send_mail($opener->email, $subject, $content);
				}
			}
		}

		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE){
			show_msg(ERROR ,'保存失败');
		}
		// header("Location:".$_SERVER['HTTP_REFERER']);
		// 跳转到评分页面
		header("Location:/grade/taskview/{$grade_id}");
		exit;
	}

	public function active(){
		$tid = $this->input->post('task_id');
		$est_started_date = $this->input->post('est_started_date');
		$deadline = $this->input->post('deadline');
		$task = $this->get_task_by_id($tid);
		if(!$task){
			show_404();
		}
		$assigned_to = $this->input->post('assigned_to');
		if(!$assigned_to){
			show_msg(ERROR, '请选择指派人');
		}
		$consumed = $this->input->post('consumed');
		$consumed = $consumed ? $consumed : 0;
		$this->db->trans_start();

		$data = array(
			'assigned_to' => $assigned_to,
			'assigned_date' => date('Y-m-d H:i:s'),
			'consumed' => $consumed,
			'real_started_date' => 0,
			'est_started_date' => $est_started_date ? $est_started_date : 0,
			'deadline' => $deadline ? $deadline : 0,
			'finished_by' => 0,
			'finished_date' => 0,
			'canceled_by' => 0,
			'canceled_date' => 0,
			'closed_by' => 0,
			'closed_date' => 0,
			'closed_reason' => '',
			'test_date' => 0,
			'test_finished_date' => 0,
			'online_date' => 0,
			'status' => $this->pmsdata['task']['status']['wait']['value'],
			'last_edited_by' => $this->current_user_id,
			'last_edited_date' => date('Y-m-d H:i:s')
		);
		$this->db->where('id', $task->id);
		$this->db->update(TBL_TASK, $data);

		$action_data = array(
			'project_id' => $task->project_id,
			'object_id' => $task->id,
			'type' => $this->pmsdata['task']['value'],
			'action' => $this->pmsdata['task']['action']['actived']['value']
		);
		$action_id = $this->create_action($action_data);
		$changes = $this->create_change($task, $data);
		$this->save_changes($action_id, $changes);

		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE){
			show_msg(ERROR ,'保存失败');
		}
		// 给执行任务的人发送邮件
		$subject = str_replace('#name#', $data['name'], $this->email_subjects['task_assigined']);
		$content = "<a href='{$this->base_url}/task/view/{$cur_task_id}'>{$data['name']}</a>";
		$performer = $this->user_model->get_user_by_id($assigned_to);
		if($performer && !empty($performer->email)){
			$this->send_mail($performer->email, $subject, $content);
		}
		header("Location:/task/view/{$task->id}");
		exit;
	}

	public function cancel($tid){
		$task = $this->get_task_by_id($tid);
		if(!$task){
			show_404();
		}
		$this->db->trans_start();

		$data = array(
			'canceled_by' => $this->current_user_id,
			'canceled_date' => date('Y-m-d H:i:s'),
			'status' => $this->pmsdata['task']['status']['canceled']['value'],
			'last_edited_by' => $this->current_user_id,
			'last_edited_date' => date('Y-m-d H:i:s')
		);
		$this->db->where('id', $task->id);
		$this->db->update(TBL_TASK, $data);

		$action_data = array(
			'project_id' => $task->project_id,
			'object_id' => $task->id,
			'type' => $this->pmsdata['task']['value'],
			'action' => $this->pmsdata['task']['action']['canceled']['value']
		);
		$action_id = $this->create_action($action_data);
		$changes = $this->create_change($task, $data);
		$this->save_changes($action_id, $changes);

		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE){
			show_msg(ERROR ,'保存失败');
		}
		header("Location:/task/view/{$task->id}");
		exit;
	}

	public function get_unfinished_task($uid = NULL){
		$uid = empty($uid)?$this->current_user_id:(int)$uid;
		// 当前指派的但是还未完成的任务(未开始/进行中)
		$sql = "SELECT t.* FROM ".TBL_TASK." t WHERE t.assigned_to={$uid} AND t.finished_by=0";
		return $this->db->query($sql)->result();
	}

}