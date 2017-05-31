<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by JetBrains PhpStorm.
 * User: zhangyin
 * Date: 13-12-24
 * Time: 上午11:01
 * 项目模块
 */
class Project_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
    }

    /**
     * 分页显示需求
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
        if($_SESSION['userinfo']['is_admin'] == 1){
            $this->db->where("is_deleted = '0'");
            $query = $this->db->get(TBL_PROJECT, $limit, $start);
            $result = $query->result();
        }else{
            $sql = "SELECT * FROM ".TBL_PROJECT." p LEFT JOIN ".TBL_PROJECT_TEAM." pt ON pt.project_id=p.id WHERE ((pt.user_id={$this->current_user_id} AND p.is_private='1') OR p.is_private='0' OR p.opened_by={$this->current_user_id}) AND p.is_deleted='0' GROUP BY p.id";
            $result = $this->db->query($sql)->result();
        }

        foreach($result as $key => $row)
        {
            if($row->is_private == 1 && $_SESSION['userinfo']['is_admin'] == 0 && $row->opened_by != $this->current_user_id){
                $sql = "SELECT COUNT(*) AS count FROM ".TBL_PROJECT_TEAM." pt JOIN ".TBL_PROJECT." p ON pt.project_id=p.id WHERE pt.project_id={$row->id} AND pt.user_id={$this->current_user_id}";
                $query = $this->db->query($sql);
                $count = $query->row();
                if(empty($count) || $count->count == 0){
                    unset($result[$key]);
                    continue;
                }
            }
            $status = $row->status;
            $row->status = $this->pmsdata['project']['status'][$status]['display'];
        }
        
        $data['data'] = $result;
        if($condition && is_array($condition)){
            $this->db->where($condition);
        }

        if($_SESSION['userinfo']['is_admin'] == 1){
            $this->db->where('is_deleted', '0');
            $data['total'] = $this->db->count_all_results(TBL_PROJECT);
        }else{
            $sql = "SELECT COUNT(DISTINCT(p.id)) AS count FROM ".TBL_PROJECT." p LEFT JOIN ".TBL_PROJECT_TEAM." pt ON pt.project_id=p.id WHERE ((pt.user_id={$this->current_user_id} AND p.is_private='1') OR p.is_private='0' OR p.opened_by={$this->current_user_id}) AND p.is_deleted='0'";
            // echo $sql;exit;
            $count = $this->db->query($sql)->row();
            $data['total'] = empty($count)?0:$count->count;
        }
        $data['current_page'] = $page;
        $data['total_page'] = (int)(($data['total']-1)/$limit + 1);
        $data['page_html'] = $this->create_page($data['total'], $page_size);
        return $data;
    }

    /**
     * 分页显示需求
     * @param  int $p_id 项目id
     * @return 添加成功返回项目ID, 否则返回FALSE
     */
    public function create_project($p_id=null)
    {
        $p_id = isset($p_id)?$p_id:$this->input->get_post('p_id',TRUE);
        $project_name       = $this->input->post('project_name',TRUE);
        $project_begin_date = $this->input->post('project_begin_date',TRUE);
        $project_end_date   = $this->input->post('project_end_date',TRUE);
        $project_available_working_days = (int)$this->get_working_days($project_end_date,$project_begin_date);
        $project_manage_by  = (int)$this->input->post('project_manage_by',TRUE);
        $project_description= mb_convert_encoding($this->input->post('project_description'),"UTF-8");
        $project_is_private = $this->input->post('project_is_private',TRUE);
        $product_id = $this->input->post('product_id');

        if(empty($project_name) || empty($project_begin_date) || empty($project_end_date) || empty($project_manage_by) || empty($project_description))
        {
            return FALSE;
        }

        $this->db->trans_start();

        //id为空，创建
        if(!$p_id)
        {
            $old = array();
            $project_status     = $this->pmsdata['project']['status']['wait']['value'];
            $data = array(
                'name'=>$project_name,
                'manage_by'=>$project_manage_by,
                'project_code'=>'',
                'begin_date'=>$project_begin_date,
                'end_date'=>$project_end_date,
                'available_working_days'=>$project_available_working_days,
                'status'=>$project_status,
                'description'=>$project_description,
                'is_private'=>$project_is_private,
                'opened_by'=>$this->current_user_id,
                'opened_date'=>date('Y-m-d H:i:s'),
                'closed_by'=>0,
                'closed_date'=>0,
                'canced_by'=>0,
                'canced_date'=>0,
                'is_deleted'=>'0'
            );
            $this->db->insert(TBL_PROJECT, $data);
            $new_id = $this->db->insert_id();

            if(is_array($product_id)){
                $project_product = array();
                foreach ($product_id as $val) {
                    $project_product[] = array('project_id' => $new_id, 'product_id' => $val);
                }
                if(!empty($project_product)){
                    $this->db->insert_batch(TBL_PROJECT_PRODUCT, $project_product);
                }
            }

            $cur_p_id = $new_id;
            $action_data = array(
                'project_id' => $cur_p_id,
                'object_id' => $cur_p_id,
                'type' => $this->pmsdata['project']['value'],
                'action' => $this->pmsdata['project']['action']['opened']['value']
            );
        }else{//id不为空，修改

            $old = $this->db->get_where(TBL_PROJECT, array('id' => $p_id))->row();

            $new_data = array(
                'name'=>$project_name,
                'manage_by'=>$project_manage_by,
                'begin_date'=>$project_begin_date,
                'end_date'=>$project_end_date,
                'available_working_days'=>$project_available_working_days,
                'description'=>$project_description,
                'is_private'=>$project_is_private
            );
            $data = $this->diff_data($old, $new_data);
            if(!$data)
                return TRUE;
            $cur_p_id = $p_id;
            $this->db->where('id', $cur_p_id);
            $this->db->update(TBL_PROJECT, $data);

            $exists = $this->db->query("SELECT GROUP_CONCAT(product_id) AS pids FROM ".TBL_PROJECT_PRODUCT." WHERE project_id={$cur_p_id}")->row();
            $exists = empty($exists->pids) ? '' : $exists->pids;
            $exists = explode(',', $exists);
            $nedd_del = array_filter(array_diff($exists, $product_id));
            $nedd_add = array_filter(array_diff($product_id, $exists));
            if(!empty($nedd_del)){
                $nedd_del = join(',', $nedd_del);
                $this->db->query("DELETE FROM ".TBL_PROJECT_PRODUCT." WHERE project_id={$cur_p_id} AND product_id IN ({$nedd_del})");
            }
            if(is_array($nedd_add)){
                foreach ($nedd_add as $val) {
                    $project_product[] = array('project_id' => $cur_p_id, 'product_id' => $val);
                }
                if(!empty($project_product)){
                    $this->db->insert_batch(TBL_PROJECT_PRODUCT, $project_product);
                }
            }

            $action_data = array(
                'project_id' => $cur_p_id,
                'object_id' => $cur_p_id,
                'type' => $this->pmsdata['project']['value'],
                'action' => $this->pmsdata['project']['action']['edited']['value']
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
        if ($this->db->trans_status() === FALSE){
            return FALSE;
        }
        return TRUE;
    }

    /**
     * 启动项目
     * @param  int $p_id 项目id
     * @return  成功返回TRUE, 否则返回FALSE
     */
    public function start_project($p_id=null)
    {
        $p_id = isset($p_id)?$p_id:$this->input->get_post('p_id',TRUE);
        $project_comment = $this->input->post('project_comment');
        $project_status = $this->pmsdata['project']['status']['doing']['value'];

        $where = array(
            'id'=>$p_id
        );
        $this->db->trans_start();
        $old = $this->db->get_where(TBL_PROJECT, $where)->row();
        $data = array(
            'status'=>$project_status
        );
        $this->db->where('id', $p_id);
        $this->db->update(TBL_PROJECT, $data);

        $action_data = array(
            'project_id' => $p_id,
            'object_id' => $p_id,
            'type' => $this->pmsdata['project']['value'],
            'action' => $this->pmsdata['project']['action']['started']['value'],
            'comment' => $project_comment
        );

        $this->update_action($old, $data, $action_data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return FALSE;
        }
        return TRUE;
    }

    /**
     * 延期项目
     * @param  int $p_id 项目id
     * @return  成功返回TRUE, 否则返回FALSE
     */
    public function delay_project($p_id=null)
    {
        $p_id = isset($p_id)?$p_id:$this->input->get_post('p_id',TRUE);
        $project_begin_date = $this->input->post('project_begin_date',TRUE);
        $project_end_date   = $this->input->post('project_end_date',TRUE);
        $project_available_working_days = (int)$this->get_working_days($project_end_date,$project_begin_date);
        $project_status = $this->pmsdata['project']['status']['delay']['value'];
        $project_comment = $this->input->post('project_comment');

        $where = array(
            'id'=>$p_id
        );
        $this->db->trans_start();
        $old = $this->db->get_where(TBL_PROJECT, $where)->row();
        $data = array(
            'begin_date'=>$project_begin_date,
            'end_date'=>$project_end_date,
            'available_working_days'=>$project_available_working_days,
            'status'=>$project_status
        );
        $this->db->where('id', $p_id);
        $this->db->update(TBL_PROJECT, $data);

        $action_data = array(
            'project_id' => $p_id,
            'object_id' => $p_id,
            'type' => $this->pmsdata['project']['value'],
            'action' => $this->pmsdata['project']['action']['delayed']['value'],
            'comment' => $project_comment
        );
        $this->update_action($old, $data, $action_data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return FALSE;
        }
        return TRUE;
    }

    /**
     * 挂起项目
     * @param  int $p_id 项目id
     * @return  成功返回TRUE, 否则返回FALSE
     */
    public function hang_project($p_id=null)
    {
        $p_id = isset($p_id)?$p_id:$this->input->get_post('p_id',TRUE);
        $project_comment = $this->input->post('project_comment');
        $project_status = $this->pmsdata['project']['status']['hang']['value'];

        $where = array(
            'id'=>$p_id
        );
        $this->db->trans_start();
        $old = $this->db->get_where(TBL_PROJECT, $where)->row();

        $data = array(
            'status'=>$project_status
        );
        $this->db->where('id', $p_id);
        $this->db->update(TBL_PROJECT, $data);

        $action_data = array(
            'project_id' => $p_id,
            'object_id' => $p_id,
            'type' => $this->pmsdata['project']['value'],
            'action' => $this->pmsdata['project']['action']['hanged']['value'],
            'comment' => $project_comment
        );
        $this->update_action($old, $data, $action_data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return FALSE;
        }
        return $p_id;
    }

    /**
     * 删除项目
     * @param  int $p_id 项目id
     * @return  成功返回TRUE, 否则返回FALSE
     */
    public function del_project($p_id=null)
    {
        $p_id = isset($p_id)?$p_id:$this->input->get_post('p_id',TRUE);
        $project_status = $this->pmsdata['project']['status']['delete']['value'];

        $where = array(
            'id'=>$p_id
        );
        $this->db->trans_start();
        $old = $this->db->get_where(TBL_PROJECT, $where)->row();
        $data = array(
            'status'=>$project_status,
            'is_deleted'=>'1'
        );
        $this->db->where('id', $p_id);
        $this->db->update(TBL_PROJECT, $data);

        $action_data = array(
            'project_id' => $p_id,
            'object_id' => $p_id,
            'type' => $this->pmsdata['project']['value'],
            'action' => $this->pmsdata['project']['action']['canceled']['value']
        );
        $this->update_action($old, $data, $action_data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return FALSE;
        }
        return TRUE;
    }

    /**
     * 结束项目
     * @param  int $p_id 项目id
     * @return  成功返回TRUE, 否则返回FALSE
     */
    public function close_project($p_id=null)
    {
        $p_id = isset($p_id)?$p_id:$this->input->get_post('p_id',TRUE);
        $project_comment = $this->input->post('project_comment');
        $project_status = $this->pmsdata['project']['status']['closed']['value'];

        $where = array(
            'id'=>$p_id
        );
        $this->db->trans_start();
        $old = $this->db->get_where(TBL_PROJECT, $where)->row();
        $data = array(
            'status'=>$project_status,
            'closed_by'=>$this->current_user_id,
            'closed_date'=>date('Y-m-d H:i:s')
        );
        $this->db->where('id', $p_id);
        $this->db->update(TBL_PROJECT, $data);

        $action_data = array(
            'project_id' => $p_id,
            'object_id' => $p_id,
            'type' => $this->pmsdata['project']['value'],
            'action' => $this->pmsdata['project']['action']['closed']['value'],
            'comment' => $project_comment
        );
        $this->update_action($old, $data, $action_data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return FALSE;
        }
        return TRUE;
    }

    /**
     * 修改项目
     * @param  int $p_id 项目id
     * @return  成功返回TRUE, 否则返回FALSE
     */
    public function edit_project($p_id=null)
    {
        $p_id = isset($p_id)?$p_id:$this->input->get_post('p_id',TRUE);
        return $this->create_project($p_id);

    }

    /**
     * 计算时间相差天数
     * @param  string $e_date 开始日期
     * @param  string $b_date 结束日期
     * @return  日期相差天数
     */
    private function get_working_days($e_date, $b_date)
    {
        $e = strtotime($e_date);
        $b = strtotime($b_date);
        $Days=round(($e-$b)/3600/24);
        return $Days+1;
    }

    /**
     * 根据项目id获取项目信息
     * @param  int $p_id 项目id
     * @return  object 返回项目信息
     */
    function query_by_id($p_id)
    {
        $p_id = isset($p_id)?$p_id:$this->input->get_post('p_id',TRUE);
        $p_data = $this->db->get_where(TBL_PROJECT, array('id'=>$p_id))->row();
        $status = empty($p_data->status)?'':$p_data->status;
        // $p_data->status = $this->pmsdata['project']['status'][$status]['display'];
        $p_data->status = $status;
        $manager = $this->user_model->get_user_by_id($p_data->manage_by);
        $p_data->manage_account = $manager?$manager->real_name:'';
        $is_private = (int)$p_data->is_private;
        if($is_private == '0')
        {
            $p_data->is_private_display = '默认设置(有项目视图权限，即可访问)';
        }else if($is_private == '1')
        {
            $p_data->is_private_display = '私有项目(只有项目团队成员才能访问)';
        }

        $exists = $this->db->query("SELECT GROUP_CONCAT(product_id) AS pids FROM ".TBL_PROJECT_PRODUCT." WHERE project_id={$p_id}")->row();
        $exists = empty($exists->pids) ? '' : $exists->pids;
        $p_data->product_ids = explode(',', $exists);
        $p_data->products = !empty($exists) ? $this->db->query("SELECT * FROM ".TBL_PRODUCT." WHERE id IN({$exists})")->result() : array();
        return $p_data;
    }

    /**
     * 根据项目id获取项目名称
     * @param  int $p_id 项目id
     * @return  object 返回项目名称
     */
    function query_name_by_id($p_id)
    {
        $p_id = isset($p_id)?$p_id:$this->input->get_post('p_id',TRUE);
        $p_data = $this->db->get_where(TBL_PROJECT, array('id'=>$p_id))->row();
        return empty($p_data)?'':$p_data->name;
    }

    /**
     * 根据项目id获取历史记录
     * @param  int $p_id 项目id
     * @return  object 返回项目操作的历史记录
     */
    public function get_project_history($p_id)
    {
        $p_id = isset($p_id)?$p_id:$this->input->get_post('p_id',TRUE);

        $this->db->select(TBL_ACTION.'.*,'.TBL_USER.'.real_name AS actor');
        $this->db->join(TBL_USER, TBL_ACTION.'.actor_id = '.TBL_USER.'.id');
        $story_actions = $this->db->get_where(TBL_ACTION, array('type' => $this->pmsdata['project']['value'], 'object_id' => $p_id))->result();

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

    function update_action($old, $new, $actions)
    {
        $changes = $this->create_change($old, $new);
        // 有改动的时候才创建编辑动作
        if(!empty($changes)){
            $action_id = $this->create_action($actions);
            if(!$action_id){
                $this->db->trans_rollback();
                return FALSE;
            }
            $this->save_changes($action_id, $changes);
        }
        return TRUE;
    }

    /**
     * 获取指定项目下所有的需求
     * @param $project_id 项目ID, 默认为当前项目ID
     * @return array 指定项目下的需求数组
    */
    public function get_project_stories($project_id = NULL){
        return $this->db->get_where(TBL_STORY, array('is_deleted' => '0', 'project_id' => empty($project_id) ? $this->current_project_id : (int)$project_id, 'reviewed_result' => 'pass'))->result();
    }

    /**
     * 根据ID获取项目
     * @param $project_id 项目ID, 默认为当前项目ID
     * @return object 项目对象
    */
    public function get_project_by_id($project_id = NULL){
        return $this->db->get_where(TBL_PROJECT, array('is_deleted' => '0', 'id' => (int)$project_id))->row();
    }
    /**
     * 根据项目ID获取关联的产品
     * @param $project_id 项目ID, 默认为当前项目ID
     * @return array
     * @author liuqingling
    */
    public function get_products_in_project($project_id){
        $project_id = empty($project_id)?$this->current_project_id:$project_id;
        $sql = "select * from gg_project_product where project_id = ".$project_id;
        $query = $this->db->query($sql);
        $result = $query->result_array();
        $arr = array();
        foreach($result as $k => $v){
            $arr[] = $v['product_id'];
        }
        return $arr;
    }
    /**
     * 获取系统中所有正在使用中的项目
     * @return array
     * @author liuqingling
    */
    public function get_all_project(){
        $sql = "select * from ".TBL_PROJECT." where is_deleted = '0' and status = 'doing'";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        return $result;
    }

}