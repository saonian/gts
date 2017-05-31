<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rbac_model extends MY_Model {

    const TYPE_OPERATION = 0;
    const TYPE_TASK = 1;
    const TYPE_ROLE = 2;
    
    private $_table;
    private $_childs_table;
    private $_assignments_table;
    private $_primary_key;
    private $_all_powers;

    function __construct()
    {
        parent::__construct();
    	$this->_initialize();
        $this->load->database();
    }
    
    private function _initialize() {
        $this->config->load('rbac');
        $this->_table = $this->config->item('table');
        $this->_primary_key = $this->config->item('primary_key');
        $this->_childs_table = $this->config->item('childs_table');
        $this->_assignments_table = $this->config->item('assignments_table');

        $this->_all_powers = array();
        foreach ($this->pmsdata as $key => $val) {
            if(!empty($val['powers'])){

            }
        }
    }

    function get_item_by_pk($id) 
    {
        return $this->db->get_where($this->_table, array($this->_primary_key => $id))->row();
    }
    
    function get_item_by_name($name)
    {
        return $this->db->get_where($this->_table, array('name' => $name))->row();
    }
    
    function create_item($name, $type, $description='', $biz_rule='', $data='') 
    {
        $conditions = array('name' => $name, 'type' => $type, 'description' => $description, 'bizRule' => $biz_rule, 'data' => $data);
        
        $q = $this->db->get_where($this->_table, $conditions);
        
        if ($q->num_rows() === 0)
        {
            $this->db->insert($this->_table, $conditions);
            return $this->db->insert_id();
        } 
        else
        {
            return $q->row()->id;
        }
    }
    
    function add_child($parent, $child)
    {
        if ($parent == $child)
        {
            return FALSE;
        }

        $conditions = array('parent_id' => $parent, 'child_id' => $child);

        if ($this->db->get_where($this->_childs_table, $conditions)->num_rows() === 0)
        {
            $this->db->insert($this->_childs_table, $conditions);
            return $this->db->insert_id();
        }
    }
    
    function add_childs($parent, $array_childs)
    {
        foreach ($array_childs as $child)
        {
            $this->add_child($parent, $child);
        } 
    }
    
    function item_has_child($item, $child)
    {
        $this->db->where(array('parent_id' => $item, 'child_id' => $child));
        return ($this->db->count_all_results($this->_childs_table) ? TRUE : FALSE);
    }
    
    function has_childs($item)
    {
        $this->db->where(array('parent_id' => $item));
        return ($this->db->count_all_results($this->_childs_table) ? TRUE : FALSE);
    }
    
    function get_item_childs($item)
    {
        $this->db->select("{$this->_table}.*");
        $this->db->where(array('parent_id' => $item));
        $this->db->join("{$this->_childs_table}", "{$this->_table}.id={$this->_childs_table}.child_id");
        return $this->db->get($this->_table)->result();
    }
    
    function get_inherited_roles($itemid)
    {
        $this->db->select("{$this->_table}.*");
        $this->db->where(array('parent_id' => $itemid, 'type' => self::TYPE_ROLE));
        $this->db->where("{$this->_childs_table}.child_id !=", $itemid );
        $this->db->join("{$this->_childs_table}", "{$this->_table}.id={$this->_childs_table}.child_id");
        return $this->db->get($this->_table)->result();
    }
    
    function check_access($item_name, $childname)
    {
        if ($item = $this->get_item_by_name($item_name) && $child = $this->get_item_by_name($childname))
        {
            return $this->check_access_by_id($item->id, $child->id);
        }

    }
    
    function check_access_by_id($itemid, $somethingid)
    {
        if ($this->item_has_child($itemid, $somethingid))
        {
            return TRUE;
        }
        
        if ($roles = $this->get_inherited_roles($itemid)) 
        {
            foreach ($roles as $role) 
            {
                return $this->check_access_by_id($role->id, $somethingid);
            }
        }
        return FALSE;
    }
    
    function check_user_access($uid, $item_name) 
    {
        if ($item = $this->get_item_by_name($item_name))
        {
            return $this->check_user_access_id($uid, $item->id);
        }
        return FALSE;
    }
    
    function check_user_access_id($uid, $itemid)
    {
        if ($this->user_has_item($uid, $itemid))
        {
            return TRUE;
        }

        if ($assignments = $this->get_assignments($uid))
        {
            foreach ($assignments as $assignment)
            {
                return $this->check_access_by_id($assignment->id, $itemid);
            }
        }
        return FALSE;
    }
    
    function user_has_item($uid, $itemid)
    {
        $this->db->where(array('userid' => $uid, 'itemid' => $itemid));
        return ($this->db->count_all_results($this->_assignments_table) ? TRUE : FALSE);
    }
    
    function get_assignments($uid)
    {
        $this->db->select("{$this->_table}.*");
        $this->db->join($this->_assignments_table, "{$this->_table}.id=itemid");
        $this->db->where('userid', $uid);
        return $this->db->get($this->_table)->result();
    }
    
    function assign($item_name,$user_id, $biz_rule='',$data='')
    {
        if ($item = $this->get_item_by_name($item_name))
        {
        	return $this->assign_by_id($item->id, $user_id, $biz_rule, $data);
        }
    }
    
    function assign_by_id($itemId,$user_id, $biz_rule='',$data='')
    {
        $conditions = array('itemid' => $itemId, 'userid' => $user_id, 'bizrule' => $biz_rule, 'data' => $data);
        $q = $this->db->get_where($this->_assignments_table, $conditions);
        
        if ($q->num_rows() === 0)
        {
            $this->db->insert($this->_assignments_table, $conditions);
            return $this->db->insert_id();
        }
        return $q->row()->id;
    }
    
    function create_role($name, $description='', $biz_rule=NULL, $data=NULL) 
    {
        return $this->create_item($name, self::TYPE_ROLE, $description, $biz_rule, $data);
    }

    function create_task($name, $description='', $biz_rule=NULL, $data=NULL) 
    {
        return $this->create_item($name, self::TYPE_TASK, $description, $biz_rule, $data);
    }

    function create_operation($name, $description='', $biz_rule=NULL, $data=NULL) 
    {
        return $this->create_item($name, self::TYPE_OPERATION, $description, $biz_rule, $data);
    }
    
    function get_roles() 
    {
        $order = $this->input->get('order');
        $sort = $this->input->get('sort');
        if($order && $sort){
            $this->db->order_by($order, $sort);
        }else{
            $this->db->order_by('id', 'desc');
        }
        return $this->db->get_where($this->_table, array('type' => self::TYPE_ROLE))->result();
    }

    function get_tasks() 
    {
		return $this->db->get_where($this->_table, array('type' => self::TYPE_TASK))->result();
    }

    function get_operations() 
    {
        return $this->db->get_where($this->_table, array('type' => self::TYPE_OPERATION))->result();
    }

	function get_item_operations($item_id, &$array)
	{
        if(empty($item_id)){
            return FALSE;
        }
		$this->db->select("child_id, {$this->_table}.name, type");
    	$this->db->join($this->_table, "{$this->_table}.id={$this->_childs_table}.child_id");
    	$q = $this->db->get_where($this->_childs_table, array('parent_id' => $item_id));
    	if ($q->num_rows() > 0)
    	{
    		foreach($q->result() as $item)
    		{
    			if (!$this->_is_duplicated_operation($item, $array) && $this->_is_operation($item))
    			{
    				$array[] = $item->name;
    			}
    			$this->get_item_operations($item->child_id, $array);
    		}
    	} else {
    		if ($item = $this->db->get_where($this->_table, array('id' => $item_id))->row())
    		{
    			if (!$this->_is_duplicated_operation($item, $array) && $this->_is_operation($item))
    			{
    				$array[] = $item->name;
    			}
    			
    		}
    	}		
	}
	
	function get_user_operations($uid, &$array)
	{
		foreach($this->db->get_where($this->_assignments_table, array('userid' => $uid))->result() as $item) 
		{
			$this->get_item_operations($item->itemid, $array);
		}
	}

    public function get_user_role($uid){
        $this->db->select("{$this->_table}.*");
        $this->db->join($this->_assignments_table, "{$this->_table}.id={$this->_assignments_table}.itemid");
        return $this->db->get_where($this->_table, array('userid' => $uid, 'type' => self::TYPE_ROLE))->row();
    }

    public function get_role_by_id($rid){
        return $this->db->get_where($this->_table, array('type' => self::TYPE_ROLE, 'id' => (int)$rid))->row();
    }

    public function remove_child($item_id, $child_id){
        return $this->db->delete($this->_childs_table, array('parent_id' => (int)$item_id, 'child_id' => (int)$child_id));
    }

    public function delete_role($rid){
         return $this->db->delete($this->_table, array('type' => (int)self::TYPE_ROLE, 'id' => (int)$rid));
    }

    public function save_role(){
        $role_name = $this->input->post('name', TRUE);
        $role_description = $this->input->post('description', TRUE);
        $powers = $this->input->post('powers');
        $role_id = $this->input->post('id');
        if(empty($role_name)){
            return FALSE;
        }

        $this->db->trans_start();
        // 保存角色(有则返回,无责增加)
        if(empty($role_id)){
            $role_id = $this->create_role($role_name, $role_description);
        }else{
            $this->db->where('id', $role_id);
            $this->db->update('gg_auth_item', array('name'=>$role_name,'description'=>$role_description));
        }
        // 处理权限
        if($powers && is_array($powers)){
            $operations = array();
            $this->get_item_operations($role_id, $operations);
            $need_delete_oprations = array_diff($operations, $powers);
            $need_add_oprations = array_diff($powers, $need_delete_oprations);
            // print_r($need_delete_oprations);
            // print_r($need_add_oprations);exit;

            foreach ($need_add_oprations as $key => $power) {
                // 加上新增的权限
                if(!in_array(trim($power), $operations)){
                    $item_id = $this->create_item(trim($power), self::TYPE_OPERATION, '');
                    $this->add_child($role_id, $item_id);
                }
            }

            // 删除已取消的权限
            foreach ($need_delete_oprations as $val) {
                $item = $this->get_item_by_name(trim($val));
                if($item){
                    $this->remove_child($role_id, $item->id);
                }
            }
        }
        
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return FALSE;
        }
        return TRUE;
    }
	
	private function _is_duplicated_operation($item, $array) {
		return (in_array($item->name, $array));
	}
	
	private function _is_operation($item) {
		return (!in_array($item->type, array(self::TYPE_ROLE, self::TYPE_TASK)));
	}
	
    public function get_item_by_userid($userid) {
        return $this->db->get_where($this->_assignments_table, array('userid' => $userid))->row();
    }

}

