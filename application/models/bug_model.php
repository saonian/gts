<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bug_model extends MY_Model {

	public function __construct() {
		parent::__construct();
		$this->load->model(array('user_model','project_model','story_model','task_model'));
	}
	
	public function get_currentproject_stories(){
		return $this->db->get_where(TBL_STORY, array('is_deleted' => '0','project_id' => $this->current_project_id))->result();
	}
	
	public function get_currentproject_tasks(){
		return $this->db->get_where(TBL_TASK,array('is_deleted'=>'0','project_id'=>$this->current_project_id))->result();
	}
	public function get_bugs_by_id($id){
		$bug = empty($id) ? NULL : $this->db->get_where(TBL_BUG, array('id' => (int)$id))->row();
		if(empty($bug)){
			return null;
		}
		$bug->attachment =  $this->get_bug_attachment($bug->id);
		return $bug;
	}
	
	public function get_bug_attachment($bug_id){
		if(empty($bug_id)){
			return NULL;
		}
		$result = $this->db->get_where(TBL_FILE, array('type' => 'bug','object_id' => (int)$bug_id, 'is_deleted' => '0'))->result();
		return $result;
	}
	
	public function save_bug(){
		$bug_id = $this->input->post('bug_id');
		$project_id = $this->input->post('project_id');
		$story_id = $this->input->post('story_id');
		$task_id = $this->input->post('task_id');
		$assigned_to = $this->input->post('assigned_to');
		$title = $this->input->post('title', TRUE);
		$steps = $this->input->post('steps');
		$type = $this->input->post('type');
		$product_id = $this->input->post('product_id');
		$module_id = $this->input->post('module_id');
		if(empty($bug_id)){
			$data = array(
				'product_id'=>$product_id,
				'module_id'=>$module_id,
				'project_id'=>$project_id,
				'story_id'=>$story_id,
				'task_id'=>$task_id,
				'title'=>$title,
				'steps'=>$steps,
				'type'=>$type,
				'status'=>$this->pmsdata['bug']['status']['active']['value'],
				'opened_by'=>$this->current_user_id,
				'opened_date'=>date('Y-m-d H:i:s'),
				'assigned_to'=>$assigned_to,
				'assigned_date'=>date('Y-m-d H:i:s'),
				'resolution' => '',
				'resolved_by'=>0,
				'resolved_date'=>0,
				'closed_by'=>0,
				'close_date'=>0,
				'last_edited_by'=>$this->current_user_id,
				'last_edited_date'=>date('Y-m-d H:i:s'),
				'is_deleted'=>'0'
			);
			$this->db->insert(TBL_BUG, $data);
			$cur_bug_id = $this->db->insert_id();
			$action_data = array(
				'project_id' => $project_id,
				'object_id' => $cur_bug_id,
				'type' => $this->pmsdata['bug']['value'],
				'action' => $this->pmsdata['bug']['action']['opened']['value']
			);
			$action_id = $this->create_action($action_data);
			$this->save_attachment($project_id, 'bug', $cur_bug_id, 'files', 'labels');
			// 给BUG指派人发送邮件
			$subject = str_replace('#name#', $data['title'], $this->email_subjects['bug_wait_process']);
			$content = "<a href='{$this->base_url}/bug/detail?id={$cur_bug_id}'>{$data['title']}</a>";
			$performer = $this->user_model->get_user_by_id($assigned_to);
			if($performer && !empty($performer->email)){
				$this->send_mail($performer->email, $subject, $content);
			}
			return true;
		}else{
			$old = $this->db->get_where(TBL_BUG, array('id' => $bug_id))->row();
			$data = array(
				'product_id'=>$product_id,
				'module_id'=>$module_id,
				'project_id'=>$project_id,
				'story_id'=>$story_id,
				'task_id'=>$task_id,
				'title'=>$title,
				'steps'=>$steps,
				'type'=>$type,
				'assigned_to'=>$assigned_to
			);
			$changes = $this->create_change($old, $data);
			if(!empty($changes)){
				$this->db->where('id', $bug_id);
				$this->db->update(TBL_BUG, $data);
				$action_data = array(
						'project_id' => $project_id,
						'object_id' => $bug_id,
						'type' => $this->pmsdata['bug']['value'],
						'action' => $this->pmsdata['bug']['action']['edited']['value']
				);
				$action_id = $this->create_action($action_data);
				$this->save_changes($action_id, $changes);
			}
			$this->save_attachment($project_id, 'bug', $bug_id, 'files', 'labels');
			return true;
			
		}
	}
	
	public function get_page($page = 1, $page_size = 50,$base_url = '/bug/index',$condition = ''){
		$page = (int)$page < 0 ? 1 : (int)$page;
		$limit = $page_size;
		$start = $limit * ($page - 1);

		$assignedtome = $this->input->get('assignedtome');
		$openedbyme = $this->input->get('openedbyme');
		$status = $this->input->get('status');
		if($assignedtome){
		    $condition = array('assigned_to'=>$this->current_user_id);
		}
		if($openedbyme){
		    $condition = array('opened_by'=>$this->current_user_id);
		}
		if($status){
		    $condition = array('status'=>trim(strtolower($status)));
		}
		
		if($condition && is_array($condition)){
			$this->db->where($condition);
		}	
		$order = $this->input->get('order');
        $sort = $this->input->get('sort');
        if($order && $sort){
            $this->db->order_by($order, $sort);
        }else{
            $this->db->order_by('id', 'desc');
        }
		$query = $this->db->get_where(TBL_BUG, array('is_deleted' => '0', 'project_id' => $this->current_project_id), $limit, $start);
		$results = $query->result_array();
		$datas = array();
		foreach($results as $result){
			$result['opened_by'] = $this->user_model->get_user_by_id($result['opened_by']);
			$result['assigned_to'] = $this->user_model->get_user_by_id($result['assigned_to']);
			$result['resolved_by'] = $this->user_model->get_user_by_id($result['resolved_by']);
			$datas[] = $result;
		}
		
		if($condition && is_array($condition)){
			$this->db->where($condition);
		}
		$this->db->where(array('is_deleted' => '0', 'project_id' => $this->current_project_id));
		$datas['total'] = $this->db->count_all_results(TBL_BUG);
		$datas['current_page'] = $page;
		$datas['total_page'] = (int)(($datas['total']-1)/$limit + 1);
		$datas['page_html'] = $this->create_page($datas['total'], $page_size, $base_url);
		
		return $datas;
	}
	
	public function assign_save($params){
		$this->db->select("assigned_to");
		$old = $this->db->get_where(TBL_BUG,array('id'=>$params['bug_id']))->row();
		$new['assigned_to'] = $params['assigned_to'];
		if(!empty($params['comment'])){
			$action_data = array(
					'project_id' => $params['project_id'],
					'object_id' => $params['bug_id'],
					'type' => $this->pmsdata['bug']['value'],
					'action' => $this->pmsdata['bug']['action']['assigned']['value'],
					'comment' => $params['comment']
			);
			$action_id = $this->create_action($action_data);	
		}
		$changes = $this->create_change($old, $new);
		if(!empty($changes)){
			$this->db->where('id',$params['bug_id']);
			$this->db->update(TBL_BUG, $new);
			$action_data = array(
					'project_id' => $params['project_id'],
					'object_id' => $params['bug_id'],
					'type' => $this->pmsdata['bug']['value'],
					'action' => $this->pmsdata['bug']['action']['assigned']['value']
			);
			$action_id = $this->create_action($action_data);
			$this->save_changes($action_id, $changes);
		}
		return true;
		
	}
	
	public function resolve_save($params){
		$old = $this->db->get_where(TBL_BUG,array('id'=>$params['bug_id']))->row();
		$new['resolution'] = $params['resolution'];
		$new['resolved_date'] = $params['resolved_date'];
		$new['assigned_to'] = $params['assigned_to'];
// 		$new['comment'] = $params['comment'];
		if(!empty($params['comment'])){
			$action_data = array(
					'project_id' => $params['project_id'],
					'object_id' => $params['bug_id'],
					'type' => $this->pmsdata['bug']['value'],
					'action' => $this->pmsdata['bug']['action']['resolved']['value'],
					'comment' => $params['comment']
			);
			$action_id = $this->create_action($action_data);	
		}
		$changes = $this->create_change($old, $new);
		if(!empty($changes)){
			$new['resolved_by'] = $this->current_user_id;
			$new['status'] = $this->pmsdata['bug']['status']['resolved']['value'];
			$this->db->where('id',$params['bug_id']);
			$this->db->update(TBL_BUG, $new);
			$action_data = array(
					'project_id' => $params['project_id'],
					'object_id' => $params['bug_id'],
					'type' => $this->pmsdata['bug']['value'],
					'action' => $this->pmsdata['bug']['action']['resolved']['value']
			);
			$action_id = $this->create_action($action_data);
			$this->save_changes($action_id, $changes);		
			// 给BUG创建人发送邮件
			$subject = str_replace('#name#', $old->title, $this->email_subjects['bug_resolved']);
			$content = "<a href='{$this->base_url}/bug/detail?id={$old->id}'>{$old->title}</a>";
			$opener = $this->user_model->get_user_by_id($old->opened_by);
			if($opener && !empty($opener->email)){
				$this->send_mail($opener->email, $subject, $content);
			}
		}
		return true;
	
	}
	
	public function close_save($params){
		if(!empty($params['comment'])){
				$action_data = array(
						'project_id' => $params['project_id'],
						'object_id' => $params['bug_id'],
						'type' => $this->pmsdata['bug']['value'],
						'action' => $this->pmsdata['bug']['action']['closed']['value'],
						'comment' => $params['comment']
				);
				$action_id = $this->create_action($action_data);	
		}
		$new['closed_by'] = $this->current_user_id;
		$new['close_date'] = date('Y-m-d H:i:s');
		$new['status'] = $this->pmsdata['bug']['status']['closed']['value'];
		$this->db->where('id',$params['bug_id']);
		$this->db->update(TBL_BUG, $new);
		$action_data = array(
				'project_id' => $params['project_id'],
				'object_id' => $params['bug_id'],
				'type' => $this->pmsdata['bug']['value'],
				'action' => $this->pmsdata['bug']['action']['closed']['value']
		);
		$action_id = $this->create_action($action_data);
		return true;	
	}
	
	public function get_bug_detail($id){
		$bug = $this->get_bugs_by_id($id);
		if(empty($bug)){
			return NULL;
		}
		$bug->actions = $this->get_bug_history($bug->id);
		$bug->opened_by = $this->user_model->get_user_by_id($bug->opened_by);
		$bug->last_edited_by = $this->user_model->get_user_by_id($bug->last_edited_by);
		$bug->closed_by = $this->user_model->get_user_by_id($bug->closed_by);
		$bug->assigned_to = $this->user_model->get_user_by_id($bug->assigned_to);
		$bug->resolved_by = $this->user_model->get_user_by_id($bug->resolved_by);
		$bug->project = $this->project_model->get_project_by_id($bug->project_id);
		$bug->story = $this->story_model->get_story_by_id($bug->story_id);
		$bug->task = $this->task_model->get_task_by_id($bug->task_id);

		$this->load->model('product_model');
		$this->load->model('module_model');
		$bug->product = $this->product_model->get_product_by_id($bug->product_id);
		$bug->module = $this->module_model->get_module_by_id($bug->module_id);
		return $bug;
	}
	
	public function get_bug_history($id){
		$this->db->select(TBL_ACTION.'.*,'.TBL_USER.'.real_name AS actor');
		$this->db->join(TBL_USER, TBL_ACTION.'.actor_id = '.TBL_USER.'.id');
		$bug_actions = $this->db->get_where(TBL_ACTION, array('type' => $this->pmsdata['bug']['value'], 'object_id' => $id))->result();
		foreach ($bug_actions as $key => $val) {
			$histories = $this->db->get_where(TBL_HISTORY, array('action_id' => $val->id))->result();
			$val->history = $histories;
		}
		return $bug_actions;
	}
	
	public function remove_save($params){
		if(!empty($params['comment'])){
			$action_data = array(
					'project_id' => $params['project_id'],
					'object_id' => $params['bug_id'],
					'type' => $this->pmsdata['bug']['value'],
					'action' => $this->pmsdata['bug']['action']['canceled']['value'],
					'comment' => $params['comment']
			);
			$action_id = $this->create_action($action_data);
		}
		$new['is_deleted'] = '1';
		$this->db->where('id',$params['bug_id']);
		$this->db->update(TBL_BUG, $new);
		$action_data = array(
				'project_id' => $params['project_id'],
				'object_id' => $params['bug_id'],
				'type' => $this->pmsdata['bug']['value'],
				'action' => $this->pmsdata['bug']['action']['canceled']['value']
		);
		$action_id = $this->create_action($action_data);
		return true;	
	}
	


}