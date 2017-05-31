<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Product_model extends MY_Model {

	public function __construct() {
		parent::__construct();
		$this->load->model('user_model');
	}

	public function get_product_by_id($product_id = 0){
		$product_id = empty($product_id) ? $this->current_product_id : (int)$product_id;
		return empty($product_id) ? NULL : $this->db->get_where(TBL_PRODUCT, array('id' => $product_id))->row();
	}

	/**
	 * 分页显示需求
	 * @param  int $page 第几页
	 * @param  int $page_size 每页数量
	 * @param  array $condition CI Active Record风格的sql条件
	 * @return array $data 页面数据
	 */
	public function get_page($page = 1, $page_size = 50, $base_url = '', $condition = ''){
		$page = (int)$page < 0 ? 1 : (int)$page;
		$limit = $page_size;
		$start = $limit * ($page - 1);

		if($_SESSION['userinfo']['is_admin'] == 0){
			$products = $this->get_all_products();
			if($products){
				$this->db->start_cache();
				$this->db->where_in('id', array_keys($products));
			}
		}else{
			$this->db->start_cache();
		}
		$order = $this->input->get('order', TRUE);
		$sort = $this->input->get('sort', TRUE);
		if($order && $sort){
			$this->db->order_by($order, $sort);
		}else{
			$this->db->order_by('id', 'desc');
		}

		$query = $this->db->get_where(TBL_PRODUCT, array('is_deleted' => '0'), $limit, $start);
		$result = $query->result();
		$this->db->stop_cache();

		$total = $this->db->count_all_results(TBL_PRODUCT);
		$this->db->flush_cache();

		foreach($result as $key=>$val){
			$val->active_story = $this->get_story_count($val->id, $this->pmsdata['story']['status']['active']['value']);
			$val->draft_story = $this->get_story_count($val->id, $this->pmsdata['story']['status']['draft']['value']);
			$val->closed_story = $this->get_story_count($val->id, $this->pmsdata['story']['status']['closed']['value']);

			$val->relate_bug = $this->get_bug_count($val->id);
			$val->active_bug = $this->get_bug_count($val->id, 'resolved_by = 0');
			$val->no_assigned_bug = $this->get_bug_count($val->id, 'assigned_to = 0');
		}
		$data['data'] = $result;
		$data['total'] = $total;
		$data['current_page'] = $page;
		$data['total_page'] = (int)(($data['total']-1)/$limit + 1);
		$data['page_html'] = $this->create_page($data['total'], $page_size, $base_url);
		return $data;
	}

	/**
	 * 获取产品关联的需求数
	 * @param  int $product_id 产品ID
	 * @param  string $status 需求状态
	 * @return int 关联数
	 */
	public function get_story_count($product_id, $status){
		$this->db->where('is_deleted', '0');
		$this->db->where('product_id', (int)$product_id);
		if(isset($this->pmsdata['story']['status'][$status])){
			$this->db->where('status', $status);
		}
		return $this->db->count_all_results(TBL_STORY);
	}

	/**
	 * 获取产品关联的BUG数
	 * @param  int $product_id 产品ID
	 * @param  string $status BUG状态
	 * @return int 关联数
	 */
	public function get_bug_count($product_id, $condition = ''){
		$this->db->where('is_deleted', '0');
		$this->db->where('product_id', (int)$product_id);
		if($condition){
			$this->db->where($condition);
		}
		return $this->db->count_all_results(TBL_BUG);
	}

	public function save_product(){
		$name = $this->input->post('name', TRUE);
		$code = $this->input->post('code', TRUE);
		$PO = $this->input->post('PO', TRUE);
		$QD = $this->input->post('QD', TRUE);
		$RD = $this->input->post('RD', TRUE);
		$description = $this->input->post('description');
		$acl = $this->input->post('acl');
		$product_id = $this->input->post('product_id');
		$comment = $this->input->post('comment');

		$redirect_url = empty($product_id) ? '' : "/product/view/{$product_id}";
		$this->db->trans_start();
		if(empty($product_id)){
			$is_edit = FALSE;
			if(empty($name) || empty($code) || empty($description)){
				show_msg(WARNING, '请填写必填项', '', array(array('name'=>'返回上一页面','url'=>$_SERVER['HTTP_REFERER'])));
			}
			$old_product = array();
			$product_data = array(
				'name' => $name,
				'code' => $code,
				'status' => $this->pmsdata['product']['status']['normal']['value'],
				'PO' => $PO,
				'QD' => $QD,
				'RD' => $RD,
				'description' => $description,
				'created_by' => $this->current_user_id,
				'created_date' => date('Y-m-d H:i:s'),
				'created_version' => '1.0.0',
				'is_deleted' => '0'
			);
			$this->db->insert(TBL_PRODUCT, $product_data);
			$product_id = $this->db->insert_id();
			$action_data = array(
				'project_id' => 0,
				'object_id' => $product_id,
				'type' => $this->pmsdata['product']['value'],
				'action' => $this->pmsdata['product']['action']['opened']['value']
			);
		}else{
			$is_edit = TRUE;
			$old_product = $this->db->get_where(TBL_PRODUCT, array('id' => $product_id))->row();
			$product_data = array();
			if(!empty($name)){
				$product_data['name'] = $name;
			}
			if(!empty($code)){
				$product_data['code'] = $code;
			}
			if(!empty($PO)){
				$product_data['PO'] = $PO;
			}
			if(!empty($QD)){
				$product_data['QD'] = $QD;
			}
			if(!empty($RD)){
				$product_data['RD'] = $RD;
			}
			if(!empty($description)){
				$product_data['description'] = $description;
			}
			if(!empty($acl)){
				$product_data['acl'] = $acl;
			}
			$action_data = array();
			if(!empty($product_data)){
				$this->db->where('id', $product_id);
				$this->db->update(TBL_PRODUCT, $product_data);
				$action_data = array(
					'project_id' => 0,
					'object_id' => $product_id,
					'type' => $this->pmsdata['product']['value'],
					'action' => $this->pmsdata['product']['action']['edited']['value']
				);
			}
		}

		$changes = $this->create_change($old_product, $product_data);
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
					break;
				}
			}
		}
		if($comment !== FALSE && (!empty($comment) || (empty($comment) && $has_file))){
			if(is_string($comment)){
				$comment = array($comment);
			}
			foreach ($comment as $key => $val) {
				$action_data = array(
					'project_id' => 0,
					'object_id' => $product_id,
					'type' => $this->pmsdata['product']['value'],
					'action' => $this->pmsdata['product']['action']['commented']['value'],
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

		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE){
			show_msg(ERROR ,'保存失败');
		}
		if(empty($redirect_url)){
			show_msg(INFO, '产品保存成功', '', array(array('name'=>'继续添加/编辑产品','url'=>$_SERVER['HTTP_REFERER']),array('name'=>'返回产品列表','url'=>'/product'),array('name'=>'返回产品需求列表','url'=>'/product/story')));
		}
		header("Location:{$redirect_url}");
		exit;
	}

	public function get_product_history($product_id){
		// $this->db->order_by(TBL_ACTION.'.id', 'desc');
		$this->db->select(TBL_ACTION.'.*,'.TBL_USER.'.real_name AS actor');
		$this->db->join(TBL_USER, TBL_ACTION.'.actor_id = '.TBL_USER.'.id');
		$product_actions = $this->db->get_where(TBL_ACTION, array('type' => $this->pmsdata['product']['value'], 'object_id' => $product_id))->result();
		// $product_actions = $this->db->query("SELECT a.*,u.account,u.real_name FROM ".TBL_ACTION." a JOIN ".TBL_USER." u ON a.actor_id=u.id WHERE a.type='product' AND a.object_id={$product_id}")->result();
		foreach ($product_actions as $key => $val) {
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
		return $product_actions;
	}

	public function get_product_stories($product_id){
		return $this->db->get_where(TBL_STORY, array('product_id' => (int)$product_id))->result();
	}

	public function get_product_detail($product_id){
		$product = $this->get_product_by_id($product_id);
		if(empty($product)){
			return NULL;
		}
		$product->actions = $this->get_product_history($product->id);
		$product->created_by = $this->user_model->get_user_by_id($product->created_by);
		$product->PO = $this->user_model->get_user_by_id($product->PO);
		$product->QD = $this->user_model->get_user_by_id($product->QD);
		$product->RD = $this->user_model->get_user_by_id($product->RD);

		$product->active_story = $this->get_story_count($product->id, $this->pmsdata['story']['status']['active']['value']);
		$product->draft_story = $this->get_story_count($product->id, $this->pmsdata['story']['status']['draft']['value']);
		$product->closed_story = $this->get_story_count($product->id, $this->pmsdata['story']['status']['closed']['value']);

		$product->relate_bug = $this->get_bug_count($product->id);
		$product->active_bug = $this->get_bug_count($product->id, 'resolved_by = 0');
		$product->no_assigned_bug = $this->get_bug_count($product->id, 'assigned_to = 0');

		$stories = $this->get_product_stories($product->id);
		$can_close = TRUE;
		// 只要产品下有一个需求没有关闭，则产品不能关闭
		foreach ($stories as $key => $val) {
			if($val->status != $this->pmsdata['story']['status']['closed']['value']){
				$can_close = FALSE;
				break;
			}
		}
		$product->can_close = $can_close;
		return $product;
	}
}