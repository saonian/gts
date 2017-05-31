<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Department_model extends MY_Model {

	public function __construct() {
		parent::__construct();
		$this->load->model('user_model');
	}

	
	/**
	 * 部门列表
	 * Enter description here ...
	 * @param array $params
	 * @param int $page
	 * @param int $pagesize
	 */
	public function department_list($params, $page = 1, $pagesize = 20){
		$TemArr = array();
		$order = $this->input->get('order');
		$sort = $this->input->get('sort');
		$order = $order?$order:'id';
		$sort = $order?$sort:'desc';
		if($params['search_type'] != 'name' && $params['keyword'] != ''){
			$where = ' WHERE `d`.`parent_id`=0';
			if(!empty($params['search_date'])){
				if(!empty($params['start'])){
					$where .= " AND `d`.`".$params['search_date']."` >= '{$params['start']}'";
				}
				if(!empty($params['end'])){
					$where .= " AND `d`.`".$params['search_date']."` <= '{$params['end']}'";
				}
			}
			if($params['is_enable']!=''){
				$where .= " AND `d`.`is_enable` = '{$params['is_enable']}'";
			}
			if($params['search_type']!='' && $params['keyword']!=''){
				if($params['search_type'] == 'created_by'){
					$where .= " AND `d`.`created_by` = `u`.`id` AND (`u`.`account` LIKE '{$params['keyword']}%' OR `u`.`real_name` LIKE '{$params['keyword']}%')";
				}
				if($params['search_type'] == 'last_edited_by'){
					$where .= " AND `d`.`last_edited_by` = `u`.`id` AND (`u`.`account` LIKE '{$params['keyword']}%' OR `u`.`real_name` LIKE '{$params['keyword']}%')";
				}
			}
			
			$sql = "SELECT `d`.*,`u`.`real_name` FROM ".TBL_DEPARTMENT." AS `d`,".TBL_USER." AS `u`".$where;
			$sql .= " ORDER BY `d`.`{$order}` {$sort}";
			
			$total = $this->db->query("SELECT COUNT(1) AS total FROM ".TBL_DEPARTMENT." AS `d`,".TBL_USER." AS `u`".$where);
    		$total_num = $total->row()->total;
			
	    	$limit = " LIMIT ".($page-1)*$pagesize.",".$pagesize;
			$sql .=  $limit;
			
			$query = $this->db->query($sql);
    		$data = $query->result_array();
		}else{
			$where = " WHERE `parent_id`=0";
			if(!empty($params['search_date'])){
				if(!empty($params['start'])){
					$where .= " AND `".$params['search_date']."` >= '{$params['start']}'";
				}
				if(!empty($params['end'])){
					$where .= " AND `".$params['search_date']."` <= '{$params['end']}'";
				}
			}
			if($params['is_enable']!=''){
				$where .= " AND `is_enable` = '{$params['is_enable']}'";
			}
			
			if($params['keyword']!='' && $params['search_type'] == 'name'){
				$list_id = $this->get_id_list($params['keyword']);	
				if($list_id != ''){
					$where .= " AND `id` in (".$list_id.")";
				}else{
					$where .= " AND 1=2";
				}
			}
			
			$total = $this->db->query("SELECT COUNT(1) AS total FROM ".TBL_DEPARTMENT.$where);
    		$total_num = $total->row()->total;
    		
			$sql = "SELECT * FROM ".TBL_DEPARTMENT.$where;
			$sql .= " ORDER BY `{$order}` {$sort}";
			
			$limit = " LIMIT ".($page-1)*$pagesize.",".$pagesize;
			$sql .=  $limit;
			
			$query = $this->db->query($sql);
    		$data = $query->result_array();
		}

	    foreach($data as $key=>$val){
    		$created_by_user = $this->user_model->get_user_by_id($val['created_by']);
    		$data[$key]['created_by_user'] = $created_by_user->real_name;
    		if(!empty($val['last_edited_by'])){
    			$last_edited_by_user = $this->user_model->get_user_by_id($val['last_edited_by']);
    			$data[$key]['last_edited_by_user'] = $last_edited_by_user->real_name;
    		}else{
    			$data[$key]['last_edited_by_user'] = '';
    		}
   		}

		$TemArr['data'] = $data;
		$TemArr['total'] = $total_num;
		$TemArr['current_page'] = $page;
		$TemArr['total_page'] = (int)(($total_num-1)/$pagesize + 1);
		$TemArr['page_html'] = $this->create_page($total_num, $pagesize, '/department/index');

		return $TemArr;
	}
	
	
	
	/**
	 * 根据部门名称获取查询id
	 * @param string $name
	 */
	public function get_id_list($name){
		$this->db->like('name',$name,'after');
		$rs = $this->db->get(TBL_DEPARTMENT)->result_array();
		$ids = '';
		if(count($rs) > 0){
			foreach($rs as $key=>$val){
				$list = $this->get_parent_id($val['id'],array());
				foreach($list as $item_id){
					$ids .= $item_id.',';
				}
			}
		}
		if($ids != ''){
			$ids = trim($ids,',');
		}
		return $ids;
	}
	
	
	
	/**
	 * 根据子类ID获取所有父类ID
	 * @param int $id
	 * @param array $list
	 */
	public function get_parent_id($id,$list){
		$query = $this->db->get_where(TBL_DEPARTMENT,array('id' => $id));
		$data = $query->row_array();
    	if(isset($data['parent_id']) && $data['parent_id'] != '0'){
    		$list[] = $data['parent_id'];
    		return $this->get_parent_id($data['parent_id'],$list);
    	}else{
    		if(isset($data['id'])){
    			$list[] = $data['id'];
    		}
    	}
    	return $list;
	}
	
	
	/**
	 * 根据ID获取该ID的顶级部门名称
	 * @param int $id
	 */
	public function get_only_parentname($id){
		$data = $this->db->get_where(TBL_DEPARTMENT,array('id' => $id))->row_array();
		if(isset($data['parent_id'])){
			if($data['parent_id'] == 0){
				return $data['name'];
			}else{
				return $this->get_only_parentname($data['parent_id']);
			}
		}
	}
	
	
	/**
	 * 根据父类ID获取所有子类ID
	 * @param int $parent_id
	 */
	public function get_all_parent_id($parent_id){
  		$list[] = $parent_id;
	    do{
	        $ids = '';
	        $temp = $this->db->query('SELECT `id` FROM '.TBL_DEPARTMENT.' WHERE `parent_id` IN ('.$parent_id.')');
	        $data = $temp->result_array();
	        foreach ($data as $val){
	            $list[] = $val['id'];
	            $ids .= ',' . $val['id'];
	        }
	        $parent_id = trim($ids,',');
	    }while (!empty($data));

	    return $list;
	}
	
	
	/**
	 * 编辑部门(显示列表)
	 * @param int $id
	 */
	public function department_edit($id){
		$rs = $this->db->get_where(TBL_DEPARTMENT, array('id' => (int)$id))->row_array();
		$child = $this->get_all_child($id);
		if(!empty($child)) $rs['child'] = $child;
		
		return $rs;
	}
	

	/**
	 * 获取所有子部门
	 * @param int $id
	 */
	public function get_all_child($id){
		$rs = $this->db->get_where(TBL_DEPARTMENT, array('parent_id' => (int)$id))->result_array();
		if(is_array($rs) && count($rs)>0){
			foreach($rs as $key=>$val){
				$child = $this->get_all_child($val['id']);
				if(!empty($child)) $rs[$key]['child'] = $child;
			}
		}
		return $rs;
	}
	
	
	
	/**
	 * 部门详情
	 * @param int $id
	 */
	public function department_info($id){
		$data = array();
		$row = $this->db->get_where(TBL_DEPARTMENT, array('id' => (int)$id))->row_array();
		$rs = $this->db->get_where(TBL_DEPARTMENT, array('parent_id' => (int)$id))->result_array();
		foreach($rs as $key=>$val){
			$rs[$key]['is_exist_child'] = $this->is_exist_child($val['id']);
		}
		
		$data['info'] = $row;
		$data['list'] = $rs;
		
		return $data;
	}
	
	
	/**
	 * 是否有子部门
	 */
	public function is_exist_child($id){
		$rs = $this->db->get_where(TBL_DEPARTMENT, array('parent_id' => (int)$id))->row_array();
		if(is_array($rs) && count($rs)>0){
			return 1;
		}else{
			return 0;
		}
	}
	
	
	

	/**
	 * 获取子部门信息
	 * @param int $id
	 */
	public function get_child_info($id){
		$row = $this->db->get_where(TBL_DEPARTMENT, array('parent_id' => (int)$id))->result_array();
		if(count($row)>0){
			foreach($row as $key=>$val){
				$row[$key]['is_exist_child'] = $this->is_exist_child($val['id']);
				$row[$key]['is_enable_zh'] = $val['is_enable'] == 1 ? '启用' : '停用';
			}
		}
		return $row;
	}
	
	
	
	/**
	 * Enter 创建部门
	 * @param array $params
	 */
	public function department_add($params){
		$this->db->trans_start();
		if($params['type'] >= 1){
			$TemArr = array();
			$TemArr['name'] = $params['level1'];
			$TemArr['is_enable'] = $params['is_enable'];
			$TemArr['created_by'] = $_SESSION['userinfo']['id'];
			$TemArr['created_date'] = date('Y-m-d H:i:s');
				
			$this->db->insert(TBL_DEPARTMENT, $TemArr);
			$new_id = $this->db->insert_id();
			
			$this->db->where('id',$new_id);
			$this->db->update(TBL_DEPARTMENT, array('path'=>$new_id));
		}
		
		/**二级部门添加**/
		if($params['type'] == 2){		
			foreach($params['level2'] as $key=>$val){
				$TemArr2 = array();
				$TemArr2['name'] = $val;
				$TemArr2['grade'] = $params['type'];
				$TemArr2['parent_id'] = $new_id;
				$TemArr2['is_enable'] = $params['is_enable'];
				$TemArr2['created_by'] = $_SESSION['userinfo']['id'];
				$TemArr2['created_date'] = date('Y-m-d H:i:s');
				
				$this->db->insert(TBL_DEPARTMENT, $TemArr2);
				$new_id2 = $this->db->insert_id();
				
				$path = $new_id.','.$new_id2;
				$this->db->where('id',$new_id2);
				$this->db->update(TBL_DEPARTMENT, array('path'=>$path));
			}
		}
		
		/**三级部门添加**/
		if($params['type'] == 3){		
			$level2_ids = array();
			foreach($params['level2'] as $key=>$val){
				$TemArr2 = array();
				$TemArr2['name'] = $val;
				$TemArr2['grade'] = 2;
				$TemArr2['parent_id'] = $new_id;
				$TemArr2['is_enable'] = $params['is_enable'];
				$TemArr2['created_by'] = $_SESSION['userinfo']['id'];
				$TemArr2['created_date'] = date('Y-m-d H:i:s');
				
				$this->db->insert(TBL_DEPARTMENT, $TemArr2);
				$new_id2 = $this->db->insert_id();
				$level2_ids[$key] = $new_id2;
				
				$path = $new_id.','.$new_id2;
				$this->db->where('id',$new_id2);
				$this->db->update(TBL_DEPARTMENT, array('path'=>$path));
			}
			
			foreach($params['level3'] as $key=>$val){
				$level2_id = $level2_ids[$key];
				foreach($val as $k=>$v){
					$TemArr3 = array();
					$TemArr3['name'] = $v;
					$TemArr3['grade'] = $params['type'];
					$TemArr3['parent_id'] = $level2_id;
					$TemArr3['is_enable'] = $params['is_enable'];
					$TemArr3['created_by'] = $_SESSION['userinfo']['id'];
					$TemArr3['created_date'] = date('Y-m-d H:i:s');
					
					$this->db->insert(TBL_DEPARTMENT, $TemArr3);
					$new_id3 = $this->db->insert_id();
					$level3_ids[$key] = $new_id3;
					
					$path = $new_id.','.$level2_id.','.$new_id3;
					$this->db->where('id',$new_id3);
					$this->db->update(TBL_DEPARTMENT, array('path'=>$path));
					
				}
			}	
		}
		
		/**四级部门添加**/
		if($params['type'] == 4){
			$level2_ids = array();
			foreach($params['level2'] as $key=>$val){
				$TemArr2 = array();
				$TemArr2['name'] = $val;
				$TemArr2['grade'] = 2;
				$TemArr2['parent_id'] = $new_id;
				$TemArr2['is_enable'] = $params['is_enable'];
				$TemArr2['created_by'] = $_SESSION['userinfo']['id'];
				$TemArr2['created_date'] = date('Y-m-d H:i:s');
				
				$this->db->insert(TBL_DEPARTMENT, $TemArr2);
				$new_id2 = $this->db->insert_id();
				$level2_ids[$key] = $new_id2;
				
				$path = $new_id.','.$new_id2;
				
				$this->db->where('id',$new_id2);
				$this->db->update(TBL_DEPARTMENT, array('path'=>$path));
			}
			
			$level3_ids = array();
			$path_arr = array();
			foreach($params['level3'] as $key=>$val){
				$level2_id = $level2_ids[$key];
				foreach($val as $k=>$v){
					$TemArr3 = array();
					$TemArr3['name'] = $v;
					$TemArr3['grade'] = 3;
					$TemArr3['parent_id'] = $level2_id;
					$TemArr3['is_enable'] = $params['is_enable'];
					$TemArr3['created_by'] = $_SESSION['userinfo']['id'];
					$TemArr3['created_date'] = date('Y-m-d H:i:s');
					
					$this->db->insert(TBL_DEPARTMENT, $TemArr3);
					$new_id3 = $this->db->insert_id();
					$level3_ids[$k] = $new_id3;
					
					$path = $new_id.','.$level2_id.','.$new_id3;
					
					$path_arr[$k] = $path;
					$this->db->where('id',$new_id3);
					$this->db->update(TBL_DEPARTMENT, array('path'=>$path));
				}
			}

			foreach($params['level4'] as $key=>$val){
				foreach($val as $k3=>$v3){
					$level3_id = $level3_ids[$k3];
					$path3 = $path_arr[$k3];
					foreach($v3 as $k4=>$v4){
						$TemArr4 = array();
						$TemArr4['name'] = $v4;
						$TemArr4['grade'] = $params['type'];
						$TemArr4['parent_id'] = $level3_id;
						$TemArr4['is_enable'] = $params['is_enable'];
						$TemArr4['created_by'] = $_SESSION['userinfo']['id'];
						$TemArr4['created_date'] = date('Y-m-d H:i:s');

						$this->db->insert(TBL_DEPARTMENT, $TemArr4);
						$new_id4 = $this->db->insert_id();

						$path = $path3.','.$new_id4;
						
						$this->db->where('id',$new_id4);
						$this->db->update(TBL_DEPARTMENT, array('path'=>$path));					
					}
					
				}
			}	
		}
		
		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE){
			return FALSE;
		}	
		return TRUE;
	}
	
	
	
	/**
	 * 验证部门名称唯一性
	 * @param array $ids
	 * @param array $names
	 * @param int $type
	 */
	public function check_name($ids,$names,$type){
		if($type == 1){
			$data = array();
			foreach($ids as $key=>$val){
				$this->db->where('name',$names[$key]);
				//$this->db->where('parent_id',0);
				$this->db->where_not_in('id', $val);
				$row = $this->db->get(TBL_DEPARTMENT)->row_array();				
				if(is_array($row) && count($row)>0){
					$data[$val] = 1;
				}else{
					$data[$val] = 0;
				}
			}
			return $data;
		}elseif($type == 2){
			$this->db->where('name',$names);
			//$this->db->where('parent_id',0);
			$row = $this->db->get(TBL_DEPARTMENT)->row_array();				
			if(is_array($row) && count($row)>0){
				return 1;
			}else{
				return 0;
			}
		}
	}
	
	
	
	/**
	 * 更新部门信息
	 * @param array $data
	 */
	public function department_update($data){
		$this->db->trans_start();
		$data['cut_id'] = trim($data['cut_id'],',');
		$cutArr = array();
		if(!empty($data['cut_id'])){
			$cutArr = explode(',',$data['cut_id']);
		}
		if(count($cutArr)>0){
			$this->db->where_in('id', $cutArr);
			$this->db->delete(TBL_DEPARTMENT);
		}

		$TemId = array();
		if(is_array($data['department_names']) && count($data['department_names'])>0){
			foreach($data['department_names'] as $key=>$val){
				$TemId[] = $key;
				$updateArr = array();
				$updateArr['name'] = $val;
				$updateArr['is_enable'] = $data['department_is_enable'][$key];
				$updateArr['last_edited_by'] = $_SESSION['userinfo']['id'];
				$updateArr['last_edited_date'] = date('Y-m-d H:i:s');
				
				$this->db->where(array('id'=>$key)); 
		   	 	$this->db->update(TBL_DEPARTMENT,$updateArr);
			}
		}
		
		if(is_array($data['new_department']) && count($data['new_department'])>0){
			foreach($data['new_department'] as $key=>$val){
				$parentInfo = $this->db->get_where(TBL_DEPARTMENT, array('id' => $key))->row();
				foreach($val as $k=>$item){
					$insertArr = array();
					$insertArr['name'] = $item;
					$insertArr['parent_id'] = $key;
					$insertArr['is_enable'] = $data['is_enable_new'][$k];
					$insertArr['created_by'] = $_SESSION['userinfo']['id'];
					$insertArr['created_date'] = date('Y-m-d H:i:s');
					
					$this->db->insert(TBL_DEPARTMENT, $insertArr);
					$new_id = $this->db->insert_id();
					$path = $parentInfo->path.','.$new_id;
					
					$this->db->where(array('id'=>$new_id)); 
		   	 		$this->db->update(TBL_DEPARTMENT,array('path'=>$path));
				}
			}	
		}
		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE){
			return FALSE;
		}
		return TRUE;
	}
	
	
	/**
	 * 获取所有顶级类
	 */
	public function parent_department_list(){
		return $this->db->get_where(TBL_DEPARTMENT, array('parent_id' => 0))->result_array();
	}
	
	
	/**
	 * 设置用户部门时用到的列表
	 */
	public function get_all_department_info(){
		$list = array();
		$data = $this->db->order_by('created_date')->get(TBL_DEPARTMENT)->result_array();
		
		foreach($data as $key=>$val){
			$path = explode(',',$val['path']);
			$get_name = '';
			foreach($path as $k=>$v){
				$name = $this->db->get_where(TBL_DEPARTMENT,array('id'=>$v))->row()->name;
				$get_name .= $name.'=>';
			}
			$get_name = trim($get_name,'=>');
			$list[$val['id']] = $get_name;
		}
		return $list;
	}
	

	/**
	 * 删除该部门及其所有子部门
	 * @param int $id
	 */
	public function del($id){
		$this->db->trans_start();
		$all_child_ids = $this->get_all_parent_id($id);
		$this->db->where_in('id', $all_child_ids);
		$this->db->delete(TBL_DEPARTMENT);
		
		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE){
			return FALSE;
		}
		return TRUE;
	}

	public function get_department_path($dpt_id){
		$department = $this->db->get_where(TBL_DEPARTMENT, array('id'=>(int)$dpt_id))->row();
		$path_str = '';
		if($department){
			$dpt_path_ids = $department->path;
			$this->db->where_in('id' ,explode(',', $dpt_path_ids));
			$department_path = $this->db->get(TBL_DEPARTMENT)->result();
			foreach ($department_path as $key => $val) {
				$path_str .= $val->name.'=>';
			}
		}
		return rtrim($path_str, '=>');
	}

	public function get_all_departments(){
		$dpts = $this->db->get_where(TBL_DEPARTMENT, array('parent_id' => 0))->result_array();
		foreach ($dpts as $key => $val) {
			$dpts[$key]['child'] = $this->get_all_child($val['id']);
		}
		return $dpts;
	}

	public function get_child_ids($pid, $include_self = TRUE){
		$child_ids = $include_self ? array((int)$pid) : array();
		$query = $this->db->get_where(TBL_DEPARTMENT, array('parent_id' => $pid));
		$result = $query->result();
		foreach($result as $key => $val){
			$child_ids[] = $val->id;
			$sub = $this->get_child_ids($val->id, FALSE);
			$child_ids = array_merge($child_ids, $sub);
		}
		return $child_ids;
	}
}

?>