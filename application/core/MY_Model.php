<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Model extends CI_Model {

	protected $controller = 'index';// 当前控制器名
	protected $method = 'index';// 当前方法名
	protected $pmsdata = array();
	protected $current_user = NULL;
	protected $current_user_id = NULL;
	protected $current_project_id = NULL;
	protected $current_product_id = NULL;
	protected $email_subjects = array();
	protected $base_url = NULL;

	public function __construct() {
		parent::__construct();
		if(!session_id()){
			session_start();
		}
		$this->controller = get_controller();
		$this->method = get_method();
		$this->pmsdata = $this->config->item('pms');
		$this->email_subjects = $this->config->item('email_subjects');
		$this->base_url = $this->config->item('base_url');
		// 初始化邮件发送类
		$this->load->library('email');
		$email_config = $this->config->item('email');
		$this->email->initialize($email_config);
		// 因为使用的post_controller_constructor钩子. CI是在控制器类实例化, 自动加载类实例化之后才调用钩子
		// 由于一些自动加载的类(Layout, Rbac)构造函数中有加载model, 于是这些类实例化的过程中$_SESSION为空值, 为了避免错误加上非空判断
		// 实例化之后, 调用钩子, 钩子判断$_SESSION为空跳转到登陆页, 登陆之后, $_SESSION有值.
		if(!empty($_SESSION['userinfo'])){
			$this->current_user = $_SESSION['userinfo'];
			$this->current_user_id = $_SESSION['userinfo']['id'];
	
			if(empty($_COOKIE['current_project_id'])){
				$default_project = $this->get_all_projects();
				$default_project = array_shift($default_project);
				$this->current_project_id = $default_project ? $default_project->id : 0;
				setcookie('current_project_id', $this->current_project_id, time() + 30*24*60*60, '/');
				$_COOKIE['current_project_id'] = $this->current_project_id;//设置cookie即时生效
			}else{
				$this->current_project_id = $_COOKIE['current_project_id'];
			}

			if(empty($_COOKIE['current_product_id'])){
				$default_product = $this->get_all_products();
				$default_product = array_shift($default_product);
				$this->current_product_id = $default_product ? $default_product->id : 0;
				setcookie('current_product_id', $this->current_product_id, time() + 30*24*60*60, '/');
				$_COOKIE['current_product_id'] = $this->current_product_id;//设置cookie即时生效
			}else{
				$this->current_product_id = $_COOKIE['current_product_id'];
			}
		}
	}

	/**
	 * 创建动作, 会在动作表中插入数据(没有加入事务, 包含在其他一系列操作的事务中执行)
	 * @param  $data array 需要保存的数据数组
	 * @return int 新插入的action id
	 */
	protected function create_action($data) {
		// project_id,object_id是有可能为0的
		if(!is_array($data) || !isset($data['project_id']) || !isset($data['object_id']) || empty($data['type']) || empty($data['action'])){
			return FALSE;
		}
		$data['actor_id'] = $this->current_user_id;
		$data['date'] = date('Y-m-d H:i:s');
		$data['comment'] = isset($data['comment']) ? $data['comment'] : '';
		$this->db->insert(TBL_ACTION, $data);
		return $this->db->insert_id();
	}

	/**
	 * 创建改动, 会在历史表中插入数据(没有加入事务, 包含在其他一系列操作的事务中执行)
	 * @param  $old array 原始数据数组
	 * @param  $new array 新数据数组
	 * @return array 改动数组
	 */
	protected function create_change($old, $new){
		if(empty($old)){
			return array();
		}
		$old = is_object($old)? (array)$old: $old;
		$new = is_object($new)? (array)$new: $new;

		$changes = array();
		$magic_quote = get_magic_quotes_gpc();
		// 不记录改动的字段
		$exclude_field = array('last_edited_date', 'last_edited_by', 'assigned_date');
		// 需要比较不同的字段
		$diff_fields = 'name,title,description,spec,content,report';
		foreach($new as $key => $value) {
			if(in_array(strtolower($key), $exclude_field)){
				continue;
			}
			if($magic_quote){
				$value = stripslashes($value);
			}
			$old[$key] = !isset($old[$key]) ? '' : $old[$key];//如果原值是enum中的‘0’，这里会变成‘’

			if($value != stripslashes($old[$key])) {
				$diff = '';
				if(substr_count($value, "\n") > 1 or substr_count($old[$key], "\n") > 1 or strpos($diff_fields, strtolower($key)) !== FALSE) {
					$diff = str_diff($old[$key], $value);
				} 
				$changes[] = array('field' => $key, 'old' => $old[$key], 'new' => $value, 'diff' => $diff);
			}
		}
		// print_r($changes);exit;
		return $changes;
		
	}

	/**
	 * 保存改变
	 *  @param  $action_id int 动作id
	 */
	protected function save_changes($action_id, $changes){
		if(!empty($changes) && !empty($action_id)){
			foreach ($changes as $key => $val) {
				$changes[$key]['action_id'] = $action_id;
			}
			$this->db->insert_batch(TBL_HISTORY, $changes);
		}
	}

	/**
	 * 保存附件并写入数据库
	 * @param string $field 上传文件的POST字段名
	 * @param string $label 自定义的文件标题POST参数名
	 * @param string $type 当前类型
	 * @param int $object_id 当前类型ID
	 * @param int $project_id 当前项目ID
	 * @return array 
	 */
	protected function save_attachment($project_id, $type, $object_id, $field = 'userfile', $label = 'labels'){
		$result = do_upload($field, $label);

		$project_id = is_array($project_id) ? (empty($project_id[0])?0:(int)$project_id[0]) : (int)$project_id;
		$object_id = is_array($object_id) ? (empty($object_id[0])?0:(int)$object_id[0]) : (int)$object_id;

		if(!empty($result['files'])){
			$data = array();
			$action_data = array();

			foreach ($result['files'] as $key => $val) {
				$file = array(
					'path' => $val['relative_path'],
					'title' => !empty($val['label']) ? $val['label'] : pathinfo($val['client_name'], PATHINFO_FILENAME),
					'extension' => $val['file_ext'],
					'size' => $val['file_size'],
					'type' => $type,
					'object_id' => $object_id,
					'added_by' => $this->current_user_id,
					'added_date' => date('Y-m-d H:i:s'),
					'downloads' => 0,
					'extra' => '',
					'is_deleted' => '0'
				);
				$data[] = $file;
				$action_name = '';
				if($type == 'story'){
					$action_name = $this->pmsdata['story']['action']['changed']['value'];
				}else if($type == 'task'){
					$action_name = $this->pmsdata['task']['action']['edited']['value'];
				}else if($type == 'taskprizes'){
					$action_name = $this->pmsdata['taskprizes']['action']['edited']['value'];
				}
				$action = array(
					'project_id' => $project_id,
					'object_id' => $object_id,
					'type' => $type,
					'action' => $action_name,
					'comment' => '上传了附件: '.$file['title']
				);
				$action_data[] = $action;
			}
			if(!empty($data)){
				$this->db->insert_batch(TBL_FILE, $data);
				array_walk($action_data, array($this, 'create_action'));
			}
		}
	}

	/**
	 * 比较修改数据，去除没有变化的字段
	 * @param  object 修改前的数据
	 * @param  object 修改提交的数据
	 * @return  object 需要修改的数据
	 */
	protected function diff_data($old, $new)
	{
		$data = null;
		foreach($new as $key=>$value)
		{
			if(array_key_exists($key, $old))
			{
				if($value != $old->$key)
					$data[$key] = $value;
			}
		}
		return $data;
	}

	/**
	 * 创建分页HTML
	 * @param  int $total_count 总数
	 * @param  int $page_size 每页显示数
	 * @param  string $base_url 一个完整的 URL 路径通向包含你的分页控制器名/方法名
	 * @return string 分页HTML
	 */
	protected function create_page($total_count, $page_size = PAGE_SIZE, $base_url = ''){
		if(empty($page_size) || empty($total_count)){
			return '';
		}
		$this->load->library('pagination');

		$page_config = $this->config->item('page');
		$page_config['per_page'] = (int)$page_size;
		$page_config['total_rows'] = (int)$total_count;
		$query_str = $this->input->get() ? http_build_query($this->input->get()) : '';
		$query_str = empty($query_str)?'':"?{$query_str}";
		$page_config['suffix'] = $query_str;
		$page_config['base_url'] = empty($base_url) ? '/'.$this->controller.'/'.$this->method : $base_url;
		$page_config['first_url'] = $page_config['base_url'].$query_str;
		$this->pagination->initialize($page_config);

		return $this->pagination->create_links();
	}

	/**
	 * 发送邮件
	 * @param string/array $to 收件人邮箱(多个邮箱可以用英文逗号隔开，或使用数组)
	 * @param string $subject 邮件标题
	 * @param string $content 邮件内容
	 * @return boolean 是否发送成功
	 */
	public function send_mail($to, $subject, $content){
		$this->email->clear();
		$this->email->from('liukai@rapoo.com', '工作管理系统');//发件人
		$this->email->to($to);
		$this->email->subject($subject);
		$this->email->message($content);
		echo $this->email->print_debugger();
		return $this->email->send();
	}

	/**
	 * 如果是私有产品则判断当前用户是否在该产品绑定的项目团队里面, 只有在该产品绑定的项目团队里的人才能看到这个产品
	 * 管理员能看到所有产品
	 * 产品创建人能看到自己的产品
	 */
	public function get_all_products(){
	    $this->db->order_by('id', 'desc');
	    $this->db->where("is_deleted = '0'");
	    $products = $this->db->get(TBL_PRODUCT)->result();
	    $result = array();
	    foreach ($products as $key => $val) {
	    	$result[$val->id] = $val;
	    	if($val->acl == 'private' && $_SESSION['userinfo']['is_admin'] == 0 && $val->created_by != $this->current_user_id){
		    	// 该产品绑定的所有项目
		    	$projects = $this->db->get_where(TBL_PROJECT_PRODUCT, array('product_id'=>$val->id))->result();
		    	foreach ($projects as $k => $v) {
			        if($val->acl == 'private'){
			            $sql = "SELECT COUNT(*) AS count FROM ".TBL_PROJECT_TEAM." pt JOIN ".TBL_PROJECT." p ON pt.project_id=p.id WHERE pt.project_id={$v->project_id} AND pt.user_id={$this->current_user_id}";
			            $query = $this->db->query($sql);
			            $count = $query->row();
			            if(empty($count) || $query->row()->count == 0){
			                unset($products[$key]);
			                unset($result[$val->id]);
			            }
			        }
		    	}
	    	}
	    }
	    return $result;
	}

	public function get_all_projects(){
	    if(empty($this->current_user_id)){
		return array();
	    }
	    $this->db->order_by('id', 'desc');
	    $this->db->where("is_deleted <> '1'");
	    $projects = $this->db->get(TBL_PROJECT)->result();
	    foreach ($projects as $key => $val) {
	        // 如果是私有项目则判断当前用户是否在该项目团队里面, 只有项目团队里的人才能看到这个项目
	        // 管理员能看到所有项目
	        // 项目创建人能看到自己的项目
	        if($val->is_private == 1 && $_SESSION['userinfo']['is_admin'] == 0 && $val->opened_by != $this->current_user_id){
	            $sql = "SELECT COUNT(*) AS count FROM ".TBL_PROJECT_TEAM." pt JOIN ".TBL_PROJECT." p ON pt.project_id=p.id WHERE pt.project_id={$val->id} AND pt.user_id={$this->current_user_id}";
	            $query = $this->db->query($sql);
	            $count = $query->row();
	            if(empty($count) || $query->row()->count == 0){
	                unset($projects[$key]);
	            }
	        }
	    }
	    // print_r($projects);exit;
	    return $projects;
	}

}
