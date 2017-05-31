<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by JetBrains PhpStorm.
 * User: zhangyin
 * Date: 13-12-24
 * Time: 下午4:50
 * 测试任务模块
 */
class Testtask_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('project_model');
        $this->load->model('story_model');
        $this->load->model('task_model');
    }

    /**
     * 分页显示测试任务
     * @param  int $page 第几页
     * @param  int $page_size 每页数量
     * @param  array $condition CI Active Record风格的sql条件
     * @return array $data 页面数据
     */
    public function get_page($page = 1, $page_size = 50, $condition = '')
    {
        $page = $page < 0 ? 1 : (int)$page;
        $limit = $page_size;
        $start = $limit * ($page - 1);

        $assignedtome = $this->input->get('assignedtome');
        $status = $this->input->get('status');
        if($assignedtome){
            $condition = array('owner'=>$this->current_user_id);
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
        $this->db->where("is_deleted <> '1'");
        $this->db->where('project_id', $this->current_project_id);
        $result = $this->db->get(TBL_TESTTASK, $limit, $start)->result();
        $data['data'] = $result;

        foreach($result as $row)
        {
            $status = $row->status;
            $row->status = $this->pmsdata['task']['status'][$status]['display'];
            $owner = $this->user_model->get_user_by_id($row->owner);
            $row->owner = $owner->real_name;
        }

        if($condition && is_array($condition)){
            $this->db->where($condition);
        }
        $this->db->where("is_deleted <> '1'");
        $this->db->where('project_id', $this->current_project_id);
        $data['total'] = $this->db->count_all_results(TBL_TESTTASK);
        $data['current_page'] = $page;
        $data['total_page'] = (int)(($data['total']-1)/$limit + 1);
        return $data;
    }

    /**
     * 创建和修改测试任务
     * @param  int $t_id 测试id，如果不传id，新建，id存在为修改
     * @return 成功返回TRUE；失败返回FALSE
     */
    public function create_test($t_id)
    {
        $t_id = isset($t_id)?$t_id:$this->input->get_post('t_id',TRUE);
        $test_project_id    = $this->input->post('test_project_id',TRUE);
        $test_owner         = $this->input->post('test_owner',TRUE);
        $test_level         = $this->input->post('test_level',TRUE);
        $test_begin_date    = $this->input->post('test_begin_date',TRUE);
        $test_end_date      = $this->input->post('test_end_date',TRUE);
        $test_status        = $this->input->post('test_status',TRUE);
        $test_story_id      = $this->input->post('test_story_id',TRUE);
        $test_task_id       = $this->input->post('test_task_id',TRUE);
        $test_name          = $this->input->post('test_name',TRUE);
        $test_description   = $this->input->post('test_description',TRUE);

        if(empty($test_project_id) || empty($test_owner) || empty($test_level) || empty($test_begin_date)
            || empty($test_end_date) || empty($test_status) || empty($test_name))
        {
            return FALSE;
        }
        $this->db->trans_start();
        //id为空，创建
        if(!$t_id)
        {
            $old = array();
            $data = array(
                'project_id'=>$test_project_id,
                'owner'=>$test_owner,
                'level'=>$test_level,
                'begin_date'=>$test_begin_date,
                'end_date'=>$test_end_date,
                'status'=>$test_status,
                'story_id'=>$test_story_id,
                'task_id'=>$test_task_id,
                'name'=>$test_name,
                'description'=>$test_description,
                'report'=>'',
                'is_deleted'=>'0'
            );
            $this->db->insert(TBL_TESTTASK, $data);
            $new_id = $this->db->insert_id();
            $cur_id = $new_id;
            $action_data = array(
                'project_id' => $test_project_id,
                'object_id' => $cur_id,
                'type' => $this->pmsdata['testtask']['value'],
                'action' => $this->pmsdata['testtask']['action']['opened']['value']
            );
        }else{//修改
            $old = $this->db->get_where(TBL_TESTTASK, array('id' => $t_id))->row();
            $new_data = array(
                'project_id'=>$test_project_id,
                'owner'=>$test_owner,
                'level'=>$test_level,
                'begin_date'=>$test_begin_date,
                'end_date'=>$test_end_date,
                'story_id'=>$test_story_id,
                'task_id'=>$test_task_id,
                'name'=>$test_name,
                'description'=>$test_description
            );
            $data = $this->diff_data($old, $new_data);
            if(!$data)
                return TRUE;
            $cur_id = $t_id;
            $this->db->where('id', $cur_id);
            $this->db->update(TBL_TESTTASK, $data);
            $action_data = array(
                'project_id' => $test_project_id,
                'object_id' => $cur_id,
                'type' => $this->pmsdata['testtask']['value'],
                'action' => $this->pmsdata['testtask']['action']['edited']['value']
            );
        }
        $changes = $this->create_change($old, $data);
        // 有改动的时候才创建编辑动作
        if(!empty($changes) || empty($old)){
            $action_id = $this->create_action($action_data);
            if(!$action_id){
                $this->db->trans_rollback();
                return FALSE;
            }
        }

        $this->save_changes($action_id, $changes);
        $this->db->trans_complete();
        if($this->db->trans_status() == FALSE)
        {
            return FALSE;
        }
        return TRUE;
    }

    /**
     * 修改测试
     * @param  int $t_id 测试id
     * @return  成功返回TRUE, 否则返回FALSE
     */
    public function edit_test($t_id=null)
    {
        $t_id = isset($t_id)?$t_id:$this->input->get_post('t_id',TRUE);

        return $this->create_test($t_id);

    }

    /**
     * 启动测试
     * @param  int $t_id 测试id
     * @return  成功返回TRUE, 否则返回FALSE
     */
    public function start_test($t_id=null)
    {
        $t_id           = isset($t_id)?$t_id:$this->input->get_post('t_id',TRUE);
        $test_status    = $this->pmsdata['testtask']['status']['doing']['value'];
        $test_comment   = $this->input->post('test_comment');

        $this->db->trans_start();
        $old = $this->db->get_where(TBL_TESTTASK, array('id'=>$t_id))->row();
        //设置测试任务状态为进行中
        $data = array('status'=>$test_status);
        $this->db->where('id',$t_id);
        $this->db->update(TBL_TESTTASK, $data);

        $action_data = array(
            'project_id' => $old->project_id,
            'object_id' => $t_id,
            'type' => $this->pmsdata['testtask']['value'],
            'action' => $this->pmsdata['testtask']['action']['started']['value'],
            'comment' => $test_comment
        );
        $this->update_action($old, $data, $action_data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return FALSE;
        }
        return TRUE;
    }

    /**
     * 关闭测试
     * @param  int $t_id 测试id
     * @return  成功返回TRUE, 否则返回FALSE
     */
    public function close_test($t_id=null)
    {
        $t_id           = isset($t_id)?$t_id:$this->input->get_post('t_id',TRUE);
        $test_status    = $this->pmsdata['testtask']['status']['done']['value'];
        $test_report   = $this->input->post('test_report');
        $test_comment   = $this->input->post('test_comment');

        $this->db->trans_start();
        $old = $this->db->get_where(TBL_TESTTASK, array('id'=>$t_id))->row();
        //设置测试任务状态为进行中
        $data = array(
            'status'=>$test_status,
            'report'=>$test_report
        );
        $this->db->where('id',$t_id);
        $this->db->update(TBL_TESTTASK, $data);
        $action_data = array(
            'project_id' => $old->project_id,
            'object_id' => $t_id,
            'type' => $this->pmsdata['testtask']['value'],
            'action' => $this->pmsdata['testtask']['action']['closed']['value'],
            'comment' => $test_comment
        );
        $this->update_action($old, $data, $action_data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return FALSE;
        }
        return TRUE;
    }

    /**
     * 获取测试状态类型
     * @param  int $t_id 测试id
     * @return  成功返回TRUE, 否则返回FALSE
     */
    function get_test_status()
    {
        $status = $this->pmsdata['testtask']['status'];
        return $status;
    }

    /**
     * 根据项目id获取需求
     * @param  int $id 项目id
     * @return  json 所属的需求数据
     */
    function json_story_by_pid($id)
    {
        if(empty($id)) return null;
        $this->db->where('gg_story.is_deleted', '0');
        $this->db->select('gg_story.id, gg_story.project_id, gg_story.name');
        $this->db->from('gg_project');
        $this->db->join('gg_story','gg_story.project_id=gg_project.id and gg_project.id='.$id);
        $story = $this->db->get()->result_array();
        return json_encode($story);
    }

    /**
     * 根据需求id获取任务
     * @param  int $id 需求id
     * @return  json 所属的任务数据
     */
    function json_task_by_tid($id)
    {
        if(!isset($id)) return null;
        // $this->db->select('gg_task.id, gg_task.name, gg_task.project_id, gg_task.story_id');
        // $this->db->from('gg_story');
        // $this->db->join('gg_task','gg_story.id=gg_task.story_id and gg_story.id='.$id);
        // $task = $this->db->get()->result_array();

        $projects = $this->project_model->get_all_projects();
        $allowed_projects_id = array();
        foreach ($projects as $key => $val) {
            $allowed_projects_id[] = $val->id;
        }
        // print_r($allowed_projects_id);exit;
        if(!empty($allowed_projects_id)){
            $this->db->where_in('project_id', $allowed_projects_id);
        }
        $task = $this->db->get_where(TBL_TASK, array('story_id' => $id, 'is_deleted' => '0'))->result_array();
        return json_encode($task);
    }

    function get_task($tid){
        if(!$tid){
            return NULL;
        }
        $task = $this->db->get_where(TBL_TASK, array('id' => (int)$tid))->row();
        return empty($task) ? NULL : $task;
    }

    /**
     * 根据测试id获取测试信息
     * @param  int $t_id 测试id
     * @return  object 返回测试信息
     */
    function query_by_id($t_id)
    {
        $t_id = isset($t_id)?$t_id:$this->input->get_post('t_id',TRUE);
        //获取测试数据
        $t_data = $this->db->get_where(TBL_TESTTASK, array('id'=>$t_id))->row();
        //获取测试的状态
        $status = $t_data->status;
        $t_data->status_display = $this->pmsdata['testtask']['status'][$status]['display'];
        //获取测试的负责人
        $owner = $this->user_model->get_user_by_id($t_data->owner);
        $t_data->owner_account = $owner->account;
        //获取测试所属的项目名称
        $t_data->project_name = $this->project_model->query_name_by_id($t_data->project_id);
        //获取测试所属的需求名称
        $t_story = $this->story_model->get_story_by_id($t_data->story_id);
        $t_data->story_name = empty($t_story)?'':$t_story->name;
        //获取测试素数的任务名称
        $t_task = $this->task_model->get_task_by_id($t_data->task_id);
        $t_data->task_name = empty($t_task)?'':$t_task->name;

        return $t_data;
    }

    /**
     * 根据测试id获取历史记录
     * @param  int $t_id 测试id
     * @return  object 返回测试操作的历史记录
     */
    public function get_test_history($t_id)
    {
        $t_id = isset($t_id)?$t_id:$this->input->get_post('t_id',TRUE);

        $this->db->select(TBL_ACTION.'.*,'.TBL_USER.'.real_name AS actor');
        $this->db->join(TBL_USER, TBL_ACTION.'.actor_id = '.TBL_USER.'.id');
        $story_actions = $this->db->get_where(TBL_ACTION, array('type' => $this->pmsdata['testtask']['value'], 'object_id' => $t_id))->result();

        foreach ($story_actions as $key => $val) {
            $histories = $this->db->get_where(TBL_HISTORY, array('action_id' => $val->id))->result();
            //转义html字符，历史记录显示转义后的。。。不需要解析html
            foreach ($histories as $row)
            {
                $row->old = htmlspecialchars($row->old);
                $row->new = htmlspecialchars($row->new);
            }
            $val->history = $histories;
        }
        return $story_actions;
    }

    /**
     * 更新测试操作的历史记录
     * @param  object $old 修改前的测试数据
     * @param  object $new 需要修改的测试数据
     * @param  object $actions 新增的测试操作
     * @return  bool 成功返回TRUE, 否则返回FALSE
     */
    function update_action($old, $new, $actions)
    {
        $changes = $this->create_change((array)$old, $new);
        $action_id = $this->create_action($actions);
        if(!$action_id){
            $this->db->trans_rollback();
            return FALSE;
        }
        if(!empty($changes)){
            $this->save_changes($action_id, $changes);
        }
        return TRUE;
    }

    function delete($id){
        $this->db->where(array('id' => (int)$id));
        return $this->db->update(TBL_TESTTASK, array('is_deleted' => '1'));
    }
}

?>