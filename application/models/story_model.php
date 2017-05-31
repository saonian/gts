<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Story_model extends MY_Model {

	public function __construct() {
		parent::__construct();
		$this->load->model('user_model');
	}

	/**
	 * 按ID获取需求数据
	 * @param  int $story_id 需求ID
	 * @return array $data 需求数据
	 */
	public function get_story_by_id($story_id){
		return empty($story_id) ? NULL : $this->db->get_where(TBL_STORY, array('id' => (int)$story_id))->row();
	}

	public function get_all_stories(){
		return $this->db->get_where(TBL_STORY, array('is_deleted' => '0'))->result();
	}

	public function get_project_stories($project_id = NULL){
		return $this->db->get_where(TBL_STORY, array('project_id' => (empty($project_id)?$this->current_project_id:(int)$project_id)))->result();
	}

	/**
	 * 分页显示需求
	 * @param  int $page 第几页
	 * @param  int $task_id 每页数量
	 * @param  array $condition CI Active Record风格的sql条件
	 * @return array $data 页面数据
	 */
	public function get_page($page = 1, $page_size = 50, $base_url = '', $condition = ''){
		$page = (int)$page < 0 ? 1 : (int)$page;
		$limit = $page_size;
		$start = $limit * ($page - 1);

		$assignedtome = $this->input->get('assignedtome');
		$openedbyme = $this->input->get('openedbyme');
		$opened_by = $this->input->get('opened_by');
		$reviewedbyme = $this->input->get('reviewedbyme');
		$status = $this->input->get('status');
		$allproject = $this->input->get('allproject');
		$keyword = $this->input->get('keyword');
		$keywordtype = $this->input->get('keywordtype');

		$is_product = $this->input->get('is_product');
		$module_id = $this->input->get('mid', TRUE);
		$product_id = $this->current_product_id;
		$pids = array($this->current_project_id);
		if($allproject){
			$this->load->model('project_model');
			$projects = $this->project_model->get_all_projects();
			foreach ($projects as $key => $val) {
				$pids[] = $val->id;
			}
		}
		$where = ' WHERE is_deleted=\'0\' AND project_id IN ('.join(',',$pids).')';
		if($module_id){
			$this->load->model('module_model');
			$mids = $this->module_model->get_sub_mids($module_id);
			$mids = join(',', $mids);
			$where .= ' AND product_id='.$this->current_product_id.' AND module_id IN ('.$mids.')';
		}
		if($is_product){
			$where .= ' AND product_id='.$this->current_product_id;
		}
		if($assignedtome){
			$condition = array('assigned_to'=>$this->current_user_id);
			$where .= ' AND assigned_to='.$this->current_user_id;
		}
		if($openedbyme){
			$condition = array('opened_by'=>$this->current_user_id);
			$where .= ' AND opened_by='.$this->current_user_id;
		}
		if($opened_by){
			$condition = array('opened_by'=>(int)$opened_by);
			$where .= ' AND opened_by='.(int)$opened_by;
		}
		if($reviewedbyme){
			$condition = array('reviewed_by'=>$this->current_user_id);
			$where .= ' AND reviewed_by='.$this->current_user_id;
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
					$order_by = " ORDER BY find_in_set(status,'closed,finished,active,draft'), level DESC";
				}else{
					$order_by = " ORDER BY find_in_set(status,'draft,active,finished,closed'), level DESC";
				}
			}else{
				$order_by = " ORDER BY {$order} {$sort}";
			}
		}else{
			// $this->db->order_by('id', 'desc');
			$order_by = " ORDER BY find_in_set(status,'draft,active,finished,closed'), level DESC";//逗号两边不能有空格
		}

		$query = $this->db->query("SELECT * FROM ".TBL_STORY." {$where} {$order_by} LIMIT {$start},{$limit}");
		// $query = $this->db->get_where(TBL_STORY, array('is_deleted' => '0', 'project_id' => $this->current_project_id), $limit, $start);
		$result = $query->result();
		$total_estimate = 0;
		foreach($result as $key=>$val){
			$total_estimate += $val->estimate;
			$result[$key]->task_count = $this->get_story_task_count($val->id);
			$result[$key]->opened_by = $this->user_model->get_user_by_id($val->opened_by);
			$result[$key]->assigned_to = $this->user_model->get_user_by_id($val->assigned_to);
			$result[$key]->reviewed_by = $this->user_model->get_user_by_id($val->reviewed_by);
			// 需求是否能够关闭，只有在任务都关闭的情况下需求才能关闭
			// 避免任务还没关闭的时候需求关闭了，需求下的任务全部完成后又将需求变为已完成，这个时候需求又可以关闭
			$can_close = TRUE;
			// 只要需求下有一个任务没有关闭，则需求不能关闭
			if($result[$key]->task_count > 0){
				$tasks = $this->get_story_task($val->id);
				foreach ($tasks as $k => $v) {
					if($v->status != $this->pmsdata['task']['status']['closed']['value']){
						$can_close = FALSE;
						break;
					}
				}
			}
			$result[$key]->can_close = $can_close;
		}
		$data['data'] = $result;
		$data['total_estimate'] = $total_estimate;

		$query = $this->db->query("SELECT COUNT(*) AS total FROM ".TBL_STORY." {$where}");
		$data['total'] = $query->num_rows() > 0 ? $query->row()->total : 0;
		$data['current_page'] = $page;
		$data['total_page'] = (int)(($data['total']-1)/$limit + 1);
		$data['page_html'] = $this->create_page($data['total'], $page_size, $base_url);
		return $data;
	}

	/**
	 * 获取需求任务数
	 * @param  int $task_id 需求ID
	 * @return int $task_count 任务数量
	 */
	public function get_story_task_count($story_id){
		if(empty($story_id)){
			return FALSE;
		}

		$this->db->where(array('story_id' => $story_id, 'is_deleted' => '0'));
		return $this->db->count_all_results(TBL_TASK);
	}

	/**
	 * 保存需求(插入和更新)
	 * @return mixed 添加成功返回需求ID, 否则返回FALSE
	 */
	public function save_story(){
		// print_r($this->input->post());exit;
		$project_id = $this->input->post('project_id');
		$story_id = $this->input->post('story_id');
		$name = $this->input->post('name', TRUE);
		$description = $this->input->post('description');//不要进行xss过滤
		$assigned_to = $this->input->post('assigned_to');
		$source = $this->input->post('source');
		$level = $this->input->post('level');
		$estimate = $this->input->post('estimate');
		$deadline = $this->input->post('deadline');
		$reviewed_by = $this->input->post('reviewed_by');
		$reviewed_date = $this->input->post('reviewed_date');
		$closed_by = $this->input->post('closed_by');
		$closed_reason = $this->input->post('closed_reason');
		$status = $this->input->post('status');
		$stage = $this->input->post('stage');
		$reviewed_result = $this->input->post('reviewed_result');
		$quality = $this->input->post('quality');
		$is_deleted = $this->input->post('is_deleted');
		$product_id = $this->input->post('product_id');
		$module_id = $this->input->post('module_id');

		$comment = $this->input->post('comment');
		// 服务端验证
		if($project_id !== FALSE && empty($project_id)){
			show_msg(ERROR ,'请指定所属项目');
		}
		if($name !== FALSE && empty($name)){
			show_msg(ERROR ,'请填写需求名称');
		}
		if($description !== FALSE && empty($description)){
			show_msg(ERROR ,'请填写需求描述');
		}

		$con_var = empty($story_id)?$name:$story_id;
		if(!is_array($con_var)) {
			$story_id = array($story_id);
			$name = array($name);
			$assigned_to = array($assigned_to);
			$source = array($source);
			$level = array($level);
			$reviewed_by = array($reviewed_by);
			$reviewed_date = array($reviewed_date);
			$description = array($description);
			$estimate = array($estimate);
			$deadline = array($deadline);
			$closed_by = array($closed_by);
			$closed_reason = array($closed_reason);
			$status = array($status);
			$stage = array($stage);
			$reviewed_result = array($reviewed_result);
			$quality = array($quality);
			$is_deleted = array($is_deleted);
			$product_id = array($product_id);
			$module_id = array($module_id);
		}

		$this->db->trans_start();
		$cur_story_id = 0;
		$foreach_var = empty($story_id)?$name:$story_id;
		//跳转页面, 批量操作跳转到列表页, 单个操作跳转到详情页.
		$redirect_url = count($foreach_var) > 1 || empty($story_id[0]) ? "/story" : "/story/view/".$story_id[0];
		foreach ($foreach_var as $key => $val) {
			$_estimate = isset($estimate[$key]) ? (float)$estimate[$key] : 0;
			$_reviewed_by = isset($reviewed_by[$key]) ? (int)$reviewed_by[$key] : 0;
			$_reviewed_date = $_reviewed_by ? date('Y-m-d H:i:s') : 0;
			$_assigned_to = isset($assigned_to[$key]) ? (int)$assigned_to[$key] : 0;
			$_assigned_date = $_assigned_to ? date('Y-m-d H:i:s') : 0;

			$is_edit = FALSE;
			if(empty($story_id[$key])){
				$old = array();
				$data = array(
					'product_id' => $product_id[$key],
					'module_id' => $module_id[$key],
					'project_id' => $project_id,
					'name' => $name[$key],
					'description' => $description[$key],
					'source' => $source[$key],
					'level' => $level[$key],
					'quality' => '',
					'status' => $this->pmsdata['story']['status']['draft']['value'], // 新增需求状态为草案
					'stage' => $this->pmsdata['story']['stages']['planned']['value'], // 新增需求阶段为已计划
					'estimate' => $_estimate,
					// 创建需求时，当选择了由谁评审后，默认当前指派人为所选择的评审人
					'assigned_to' => $_reviewed_by,
					'assigned_date' => $_reviewed_date,
					'reviewed_by' => $_reviewed_by,
					'reviewed_date' => $_reviewed_date,
					'reviewed_result' => '',
					'opened_by' => $this->current_user_id,
					'opened_date' => date('Y-m-d H:i:s'),
					'finished_date' => 0,
					'last_edited_by' => $this->current_user_id,
					'last_edited_date' => date('Y-m-d H:i:s'),
					'closed_by' => 0,
					'closed_date' => 0,
					'closed_reason' => '',
					'is_deleted' => '0'
				);
				$this->db->insert(TBL_STORY, $data);
				$cur_story_id = $this->db->insert_id();
				$action_data = array(
					'project_id' => $project_id,
					'object_id' => $cur_story_id,
					'type' => $this->pmsdata['story']['value'],
					'action' => $this->pmsdata['story']['action']['opened']['value']
				);
				// 给审核人发送需求待评审邮件
				$subject = str_replace('#name#', $data['name'], $this->email_subjects['story_wait_review']);
				$content = "<a href='{$this->base_url}/story/view/{$cur_story_id}'>{$data['name']}</a>";
				$reviewer = $this->user_model->get_user_by_id($_reviewed_by);
				if($reviewer && !empty($reviewer->email)){
					$this->send_mail($reviewer->email, $subject, $content);
				}
			}else{
				$is_edit = TRUE;
				$cur_story_id = $story_id[$key];
				$old = $this->db->get_where(TBL_STORY, array('id' => $cur_story_id))->row();

				$data = array(
					'last_edited_by' => $this->current_user_id,
					'last_edited_date' => date('Y-m-d H:i:s')
				);

				if(!empty($product_id[$key])){
					$data['product_id'] = $product_id[$key];
				}

				if(!empty($module_id[$key])){
					$data['module_id'] = $module_id[$key];
				}

				if(!empty($level[$key])){
					$data['level'] = $level[$key];
				}

				if(!empty($_estimate)){
					$data['estimate'] = $_estimate;
				}

				// 编辑的时候可以指派
				if(!empty($_assigned_to)){
					$data['assigned_to'] = $_assigned_to;
				}
				if(!empty($_assigned_date)){
					$data['assigned_date'] = $_assigned_date;
				}
				
				if(isset($this->pmsdata['story']['sources'][$source[$key]])){
					$data['source'] = $source[$key];
				}
				if(isset($this->pmsdata['story']['status'][$status[$key]])){
					$data['status'] = $status[$key];
				}
				if(isset($this->pmsdata['story']['stages'][$stage[$key]])){
					$data['stage'] = $stage[$key];
				}

				$is_change = FALSE;
				if(!empty($name[$key])){
					$data['name'] = $name[$key];
					$is_change = TRUE;
				}
				if(!empty($reviewed_by[$key])){
					$data['reviewed_by'] = $reviewed_by[$key];
					$is_change = TRUE;
				}
				if(!empty($description[$key])){
					$data['description'] = $description[$key];
					$is_change = TRUE;
				}
				if(!empty($project_id)){
					$data['project_id'] = (int)$project_id;
					$is_change = TRUE;
				}

				$is_reviewed = FALSE;
				// 默认为当前时间, 这里废弃
				if(!empty($reviewed_date[$key])){
					$data['reviewed_date'] = $reviewed_date[$key];
					$is_reviewed = TRUE;
				}
				if(!empty($quality[$key])){
					$data['quality'] = $quality[$key];
					$data['reviewed_by'] = $this->current_user_id;
					$data['reviewed_date'] = date('Y-m-d H:i:s');// 默认为当前时间
					$is_reviewed = TRUE;
				}
				// if(!empty($reviewed_by[$key])){
				// 	$data['reviewed_by'] = $reviewed_by[$key];
				// 	$is_reviewed = TRUE;
				// }
				if(!empty($reviewed_result[$key]) && isset($this->pmsdata['story']['reviewed_result'][$reviewed_result[$key]])){
					$data['reviewed_result'] = $reviewed_result[$key];
					// 评审通过的时候变更为激活状态
					if($reviewed_result[$key] == $this->pmsdata['story']['reviewed_result']['pass']['value']){
						$data['status'] = $this->pmsdata['story']['status']['active']['value'];
						$data['stage'] = $this->pmsdata['story']['stages']['projected']['value'];
					}
					$is_reviewed = TRUE;
				}

				$is_closed = FALSE;
				if(!empty($closed_reason[$key])){
					$data['closed_reason'] = $closed_reason[$key];
					$data['closed_by'] = $this->current_user_id;
					$data['closed_date'] = date('Y-m-d H:i:s');
					$data['status'] = $this->pmsdata['story']['status']['closed']['value'];
					$data['stage'] = $this->pmsdata['story']['stages']['released']['value'];
					// 如果关闭原因是已完成, 则创建一条需求评分数据
					if($closed_reason[$key] == $this->pmsdata['story']['close_reason']['done']['value']){
						$data_grade = array(
							'type' => 'story',
							'object_id' => $story_id[$key],
							'grade_by' => $old->opened_by,
							'grade_date' => 0,
							'is_graded' => '0'
						);
						$this->db->insert(TBL_GRADE, $data_grade);
						$grade_id = $this->db->insert_id();
						$redirect_url = "/grade/storyview/{$grade_id}";
					}
					$is_closed = TRUE;
				}

				$is_actived = FALSE;
				// 不是编辑, 不是审核, 但同时有指派说明是激活
				if(empty($level[$key]) && !$is_reviewed && !empty($_assigned_to) && $old->status != $this->pmsdata['story']['status']['active']['value']){
					$data['status'] = $this->pmsdata['story']['status']['draft']['value'];
					$data['stage'] = $this->pmsdata['story']['stages']['projected']['value'];
					$is_actived = TRUE;
				}

				$deleted = FALSE;
				if(!empty($is_deleted[$key])){
					$data['is_deleted'] = '1';
					$deleted = TRUE;
				}

				// print_r($data);exit;
				
				$action = $this->pmsdata['story']['action']['edited']['value'];
				$subject = isset($data['name']) ? $data['name'] : $old->name;
				$content = "<a href='{$this->base_url}/story/view/{$cur_story_id}'>{$subject}</a>";
				if($is_change){
					$action = $this->pmsdata['story']['action']['changed']['value'];
					$is_edit = FALSE;
				}else if($is_reviewed){
					$action = $this->pmsdata['story']['action']['reviewed']['value'];
					$is_edit = FALSE;
					$subject = str_replace('#name#', $subject, $this->email_subjects['story_review_ok']);
					// 审核通过后发送邮件给需求创建人
					if($reviewed_result[$key] == $this->pmsdata['story']['reviewed_result']['pass']['value']){
						$assign = isset($data['assigned_to'])?$data['assigned_to']:$old->opened_by;
						$opener = $this->user_model->get_user_by_id($assign);
						if($opener && !empty($opener->email)){
							$this->send_mail($opener->email, $subject, $content);
						}
					}
				}else if($is_closed){
					$action = $this->pmsdata['story']['action']['closed']['value'];
					$is_edit = FALSE;
				}else if($is_actived){
					$action = $this->pmsdata['story']['action']['actived']['value'];
					$is_edit = FALSE;
				}else if($deleted){
					$action = $this->pmsdata['story']['action']['deleted']['value'];
					$is_edit = FALSE;
				}

				$this->db->where('id', $cur_story_id);
				$this->db->update(TBL_STORY, $data);
				$action_data = array(
					'project_id' => $project_id,
					'object_id' => $cur_story_id,
					'type' => $this->pmsdata['story']['value'],
					'action' => $action
				);
			}

			$changes = $this->create_change($old, $data);
			$action_id = NULL;
			if($is_edit){
				// 有改动的时候才创建编辑动作
				if(!empty($changes) || count($_FILES) > 0){
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
			if(!empty($comment)){
				if(is_string($comment)){
					$comment = array($comment);
				}
				foreach ($comment as $key => $val) {
					$action_data = array(
						'project_id' => $project_id,
						'object_id' => $cur_story_id,
						'type' => $this->pmsdata['story']['value'],
						'action' => $this->pmsdata['story']['action']['commented']['value'],
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
		}
		$this->save_attachment($project_id, 'story', $cur_story_id, 'files', 'labels');
		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE){
			show_msg(ERROR ,'保存失败');
			// return FALSE;
		}
		if(empty($story_id)){
			show_msg(INFO, '需求创建成功', '', array(array('name'=>'查看需求','url'=>'/story/view/'.$cur_story_id),array('name'=>'继续创建需求','url'=>$_SERVER['HTTP_REFERER']),array('name'=>'返回需求列表','url'=>'/story')));
		}
		$body = $this->input->get('body');
		$redirect_url = $redirect_url.'?body='.$body;
		header("Location:{$redirect_url}");
		exit;
		// return TRUE;
	}

	public function assign_save($params){
		$this->db->select("assigned_to");
		$old = $this->db->get_where(TBL_STORY,array('id'=>$params['story_id']))->row();
		$new['assigned_to'] = $params['assigned_to'];
		$new['reviewed_by'] = $params['assigned_to'];
		if(!empty($params['comment'])){
			$action_data = array(
					'project_id' => $params['project_id'],
					'object_id' => $params['story_id'],
					'type' => $this->pmsdata['story']['value'],
					'action' => $this->pmsdata['story']['action']['remark']['value'],
					'comment' => $params['comment']
			);
			$action_id = $this->create_action($action_data);	
		}
		$changes = $this->create_change($old, $new);
		if(!empty($changes)){
			$this->db->where('id',$params['story_id']);
			$this->db->update(TBL_STORY, $new);
			$action_data = array(
					'project_id' => $params['project_id'],
					'object_id' => $params['story_id'],
					'type' => $this->pmsdata['story']['value'],
					'action' => $this->pmsdata['story']['action']['assigned']['value']
			);
			$action_id = $this->create_action($action_data);
			$this->save_changes($action_id, $changes);
		}
		return true;
		
	}

	/**
	 * 移除需求(直接从数据库中删除), 同时删除该需求下所有的任务
	 * 支持批量移除
	 * @param  mixed $story_id 需求ID
	 * @return boolean 是否移除成功
	 */
	public function remove_story($story_id){
		if(empty($story_id)){
			return FALSE;
		}

		$story_id = is_array($story_id) ? $story_id : array($story_id);

		$this->db->trans_start();
		$this->db->where_in('id', $story_id);
		$this->db->delete(TBL_STORY);

		// 删除需求同时删除需求下所有任务
		$this->db->where_in('story_id', $story_id);
		$this->db->delete(TBL_TASK);

		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE){
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * 获取需求历史记录
	 * @param $story_id 需求ID
	 * @return array 需求详情
	 */
	public function get_story_history($story_id){
		// $this->db->order_by(TBL_ACTION.'.id', 'desc');
		$this->db->select(TBL_ACTION.'.*,'.TBL_USER.'.real_name AS actor');
		$this->db->join(TBL_USER, TBL_ACTION.'.actor_id = '.TBL_USER.'.id');
		$story_actions = $this->db->get_where(TBL_ACTION, array('type' => $this->pmsdata['story']['value'], 'object_id' => $story_id))->result();
		// $story_actions = $this->db->query("SELECT a.*,u.account,u.real_name FROM ".TBL_ACTION." a JOIN ".TBL_USER." u ON a.actor_id=u.id WHERE a.type='story' AND a.object_id={$story_id}")->result();
		// print_r($story_actions);exit;
		foreach ($story_actions as $key => $val) {
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
		}
		return $story_actions;
	}

	/**
	 * 获取需求任务
	 * @param $story_id 需求ID
	 * @return array 任务数组
	 */
	public function get_story_task($story_id){
		if(empty($story_id)){
			return NULL;
		}
		$this->db->select(TBL_TASK.'.*,'.TBL_PROJECT.'.name AS project_name');
		$this->db->order_by('id', 'desc');
		$this->db->join(TBL_PROJECT, TBL_TASK.'.project_id = '.TBL_PROJECT.'.id');
		$result = $this->db->get_where(TBL_TASK, array('story_id' => (int)$story_id, TBL_TASK.'.is_deleted' => '0'))->result();
		return $result; 
	}

	/**
	 * 获取需求附件
	 * @param $story_id 需求ID
	 * @return array 附件数组
	 */
	public function get_story_attachment($story_id){
		if(empty($story_id)){
			return NULL;
		}
		$result = $this->db->get_where(TBL_FILE, array('type' => 'story','object_id' => (int)$story_id, 'is_deleted' => '0'))->result();
		return $result; 
	}

	/**
	 * 加载需求详情(支持批量更新)
	 * @param $story_id 需求ID
	 * @return array 需求详情
	 */
	public function get_story_detail($story_id){
		$story = $this->get_story_by_id($story_id);
		if(empty($story)){
			return NULL;
		}
		$story->actions = $this->get_story_history($story->id);
		$story->opened_by = $this->user_model->get_user_by_id($story->opened_by);
		$story->last_edited_by = $this->user_model->get_user_by_id($story->last_edited_by);
		$story->closed_by = $this->user_model->get_user_by_id($story->closed_by);
		$story->assigned_to = $this->user_model->get_user_by_id($story->assigned_to);
		$story->reviewed_by = $this->user_model->get_user_by_id($story->reviewed_by);
		$tasks = $this->get_story_task($story_id);
		foreach ($tasks as $key => $val) {
			$tasks[$key]->assigned_to = $this->user_model->get_user_by_id($val->assigned_to);
			$tasks[$key]->finished_by = $this->user_model->get_user_by_id($val->finished_by);
		}
		$story->tasks = $tasks;
		$story->attachments = $this->get_story_attachment($story->id);
		$story->project = $this->project_model->get_project_by_id($story->project_id);
		$story->task_count = $this->get_story_task_count($story->id);

		$this->load->model('product_model');
		$this->load->model('module_model');
		if(!empty($story->product_id)){
			$story->product = $this->product_model->get_product_by_id($story->product_id);
		}else{
			$story->product = 0;
		}
		//$story->product = $this->product_model->get_product_by_id($story->product_id);
		$story->module = $this->module_model->get_module_by_id($story->module_id);
		// 需求是否能够关闭，只有在任务都关闭的情况下需求才能关闭
		// 避免任务还没关闭的时候需求关闭了，需求下的任务全部完成后又将需求变为已完成，这个时候需求又可以关闭
		$can_close = TRUE;
		// 只要需求下有一个任务没有关闭，则需求不能关闭
		if($story->task_count > 0){
			foreach ($story->tasks as $key => $val) {
				if($val->status != $this->pmsdata['task']['status']['closed']['value']){
					$can_close = FALSE;
					break;
				}
			}
		}
		$story->can_close = $can_close;
		return $story;
	}

}