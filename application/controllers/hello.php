<?php

class Hello extends MY_Controller{
    
    function __construct(){
        parent::__construct();
        
        // $this->load->library('rbac');
        exit;
    }
    
    function index(){
        // 创建一个操作
        $cp = $this->rbac->create_operation("createPage", "create page");
        $rp = $this->rbac->create_operation("readPage", "read page");
        $up = $this->rbac->create_operation("updatePage", "update page");
        $dp = $this->rbac->create_operation("deletePage", "delete page");
        $ci = $this->rbac->create_operation("createIssue", "create issue");
        $ri = $this->rbac->create_operation("readIssue", "read issue");
        $ui = $this->rbac->create_operation("updateIssue", "update issue");
        $di = $this->rbac->create_operation("deleteIssue", "delete issue");
	
        // 创建角色
        $guest 	= $this->rbac->create_role("guest", "guest role");
        $member = $this->rbac->create_role("member", "member role");
        $owner 	= $this->rbac->create_role("owner", "owner role");
        $admin 	= $this->rbac->create_role("admin", "admin role");
        
        // 创建任务  任务是多个操作的集合
        $admMan = $this->rbac->create_task("adminManagement", "adminManagement");
        
        // 给角色授权
        $this->rbac->add_childs($guest, array($rp, $ri));
        $this->rbac->add_childs($member, array($guest, $cp, $ci, $up, $ui));
        $this->rbac->add_childs($owner, array($guest, $member, $cp, $ci, $up, $ui, $dp, $di));
        $this->rbac->add_childs($admin, array($owner, $member, $guest, $admMan));
        $this->rbac->add_childs($admMan, array($di, $dp));
        
        // 授权
        $this->rbac->assign("admin", 1); //admin
        $this->rbac->assign("member", 2); //someone.
        $this->rbac->assign("deleteIssue", 2);        
        $this->rbac->assign("guest", 3);
        $this->rbac->assign("member", 4);
        
        $list_rol = array('1' => 'admin', '2' => 'member (+deleteIssue)', '3' => 'guest', '4' => 'member');
                
        $test_arrays = array(	array('1' => 'readPage'),
        						array('2' => 'readPage'),
        						array('3' => 'readPage'),
        						array('1' => 'deleteIssue'),
        						array('2' => 'deleteIssue'),
        						array('4' => 'deleteIssue'),
        						array('3' => 'deleteIssue'),        						
        						array('1' => 'adminManagement'),
        						array('2' => 'adminManagement'),
        						array('3' => 'adminManagement'));

        foreach($test_arrays as $test)
        {
        	foreach($test as $id => $v)
        	{
				if ($this->rbac->check_user_access($id, $v))
				{
					echo "<span style='color:green'>YES, {$list_rol[$id]} can $v</span>";
				}
				else
				{
					echo "<span style='color:red'>NO, {$list_rol[$id]} can NOT $v</span>";
				}
				echo "<br />";
			}
        }
        
        echo '<br />';
		
		$perms = array();
        $this->rbac->get_item_operations(10, $perms);
        print_r($perms);
        echo '<br /><br />';
   		$perms1 = array();
        $this->rbac->get_user_operations(2, $perms1);
        print_r($perms1);
        echo '<br /><br />';
        print_r($this->rbac->get_user_role(2));
    }
    
}



