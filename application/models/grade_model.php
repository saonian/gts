<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by JetBrains PhpStorm.
 * User: zhangyin
 * Date: 13-12-24
 * Time: 上午11:01
 * 项目模块
 */
class Grade_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('task_model');
        $this->load->model('story_model');
        $this->load->model('project_model');
    }

    //查看当前用户需求评分
    function grade_storylist($page = 1, $page_size = 50, $condition = '')
    {
        $page = $page < 0 ? 1 : (int)$page;
        $limit = $page_size;
        $start = $limit * ($page - 1);

        if($condition && is_array($condition)){
            $this->db->where($condition);
        }

        //查询当前用户id所创建的需求评价
        $order = $this->input->get('order');
        $sort = $this->input->get('sort');
        if($order && $sort){
            $this->db->order_by($order, $sort);
        }else{
            $this->db->order_by('id', 'desc');
        }
        $where = array('grade_by'=>$this->current_user_id,'type'=>'story');
        $result = $this->db->get_where(TBL_GRADE, $where,$limit, $start)->result();

        foreach($result as $row)
        {
            //是否评分
            $grade = $this->db->get_where(TBL_GRADE_SCORE,array('grade_id'=>$row->id))->row();
            if(!$grade)
                $row->is_graded = '未评分';
            else
                $row->is_graded = '已评分';
            //需求id和name
            $story = $this->story_model->get_story_by_id($row->object_id);
            $row->story_name = $story->name;
            $row->story_id = $story->id;
            $row->grade_by = $this->user_model->get_user_by_id($row->grade_by);
        }

        $data['data'] = $result;
        $data['page_total'] = count($result);
        $this->db->where($where);
        $data['total'] = $this->db->count_all_results(TBL_GRADE);
        $data['current_page'] = $page;
        $data['total_page'] = (int)(($data['total']-1)/$limit + 1);
        $data['page_html'] = $this->create_page($data['total'], $page_size);
        return $data;
    }

    //查看当前用户任务评分
    function grade_tasklist($page = 1, $page_size = 50, $condition = '')
    {
        $page = $page < 0 ? 1 : (int)$page;
        $limit = $page_size;
        $start = $limit * ($page - 1);

        if($condition && is_array($condition)){
            $this->db->where($condition);
        }

        //查询当前用户id所创建的需求评价
        $order = $this->input->get('order');
        $sort = $this->input->get('sort');
        if($order && $sort){
            $this->db->order_by($order, $sort);
        }else{
            $this->db->order_by('id', 'desc');
        }
        $where = array('grade_by'=>$this->current_user_id,'type'=>'task');
        $result = $this->db->get_where(TBL_GRADE, $where,$limit, $start)->result();

        foreach($result as $row)
        {
            //是否评分
            $grade = $this->db->get_where(TBL_GRADE_SCORE,array('grade_id'=>$row->id))->row();
            if(!$grade)
                $row->is_graded = '未评分';
            else
                $row->is_graded = '已评分';
            //获取任务名称和id
            $task = $this->task_model->get_task_by_id($row->object_id);
            $row->task_name = $task->name;
            $row->task_id = $task->id;
            $row->grade_by = $this->user_model->get_user_by_id($row->grade_by);
        }

        $data['data'] = $result;
        $data['page_total'] = count($result);
        $this->db->where($where);
        $data['total'] = $this->db->count_all_results(TBL_GRADE);
        $data['current_page'] = $page;
        $data['total_page'] = (int)(($data['total']-1)/$limit + 1);
        $data['page_html'] = $this->create_page($data['total'], $page_size);
        return $data;
    }

    //管理员查看评分列表
    function grade_by_admin($page = 1, $page_size = 50, $condition = '')
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
        $query = $this->db->get(TBL_GRADE, $limit, $start);
        $result = $query->result();
        foreach($result as $row)
        {
            //任务评价
            if($row->type == 'task')
            {
                $story_grade = $this->db->get_where(TBL_GRADE_SCORE,array('grade_id'=>$row->id))->row();
                if(!$story_grade)
                    $row->is_graded = '未评分';
                else
                    $row->is_graded = '已评分';
                $task = $this->task_model->get_task_by_id($row->object_id);
                $row->object_name = $task->name;
                $row->object_id = $task->id;
                $row->type_dis = '任务';
            }
            //需求评价
            if($row->type == 'story')
            {
                $story_grade = $this->db->get_where(TBL_GRADE_SCORE,array('grade_id'=>$row->id))->row();
                if(!$story_grade)
                    $row->is_graded = '未评分';
                else
                    $row->is_graded = '已评分';
                $story = $this->story_model->get_story_by_id($row->object_id);
                $row->object_name = $story->name;
                $row->object_id = $story->id;
                $row->type_dis = '需求';
            }
            $row->grade_by = $this->user_model->get_user_by_id($row->grade_by);
        }

        if($condition && is_array($condition)){
            $this->db->where($condition);
        }
        $data['data'] = $result;
        $data['page_total'] = count($result);
        $data['total'] = $this->db->count_all_results(TBL_GRADE);
        $data['current_page'] = $page;
        $data['total_page'] = (int)(($data['total']-1)/$limit + 1);
        $data['page_html'] = $this->create_page($data['total'], $page_size);
        return $data;
    }

    /*function view_by_admin($t_id=null)
    {
        //任务id
        $task_id = empty($t_id)?1:$t_id;
        //项目id
        $task_data = $this->task_model->get_task_by_id($task_id);
        $project_id = $task_data->project_id;
        //需求id
        $story_id = $task_data->story_id;
        $story_grade = null;
        $task_grade = null;


        $where = array('project_id'=>$project_id);
        $grade = $this->db->get_where(TBL_GRADE_SETTING,$where)->result();
        foreach($grade as $key=>$val)
        {
            //获取需求评价
            if($val->type == 'story')
            {
                $story = $this->db->get_where(TBL_GRADE,array('object_id'=>$story_id,'type'=>'story'))->row();
                $where = array(
                    'grade_setting_id'=>$val->id
                );
                $desc_data = $this->db->get_where(TBL_GRADE_DESCRIPTION,$where)->row();
                $val->score = $story->score;
                $val->desc_name = empty($desc_data)?'未评价':$desc_data->desc;
                $val->description = $story->description;
                $story_grade[] = $val;
            }

            //获取任务评价
            if($val->type == 'task')
            {
                $task = $this->db->get_where(TBL_GRADE,array('object_id'=>$task_id,'type'=>'task'))->row();
                $where = array(
                    'grade_setting_id'=>$val->id,
                    'score'=>$task->score
                );
                $desc_data = $this->db->get_where(TBL_GRADE_DESCRIPTION,$where)->row();
                $val->score = $task->score;
                $val->desc_name = empty($desc_data)?'未评价':$desc_data->desc;
                $val->description = $task->description;

                $task_grade[] = $val;
            }
        }
        return array('stroy_grade'=>$story_grade,'task_grade'=>$task_grade);
    }*/

    //需求评分
    function grade_story_view($g_id)
    {
        $grade_id = (int)$g_id;
        $grade = $this->db->get_where(TBL_GRADE,array('id'=>$grade_id,'type'=>'story'))->row();
        $story_id = empty($grade)?'':$grade->object_id;

        $story_data = $this->story_model->get_story_by_id($story_id);
        $project_id = empty($story_data)?'':$story_data->project_id;

        $where = array('project_id'=>$project_id,'type'=>'story','is_deleted'=>'0');
        $grade = $this->db->get_where(TBL_GRADE_SETTING,$where)->result();
        foreach($grade as $key=>$val)
        {
            $desc_item = $this->db->get_where(TBL_GRADE_DESCRIPTION,array('grade_setting_id'=>$val->id, 'is_deleted'=>'0'))->result();
            $val->description_item = $desc_item;
            //获取评价
            $where = array(
                'setting_id'=>$val->id,
                'grade_id'=>$grade_id
            );
            $grade_score = $this->db->get_where(TBL_GRADE_SCORE,$where)->row();
            $val->score = empty($grade_score)?'':$grade_score;
        }

        $result['data'] = $grade;
        $result['project_id'] = $project_id;
        $result['project_name'] = $this->project_model->query_name_by_id($project_id);
        $result['story_id'] = $story_id;
        $result['grade_id'] = $grade_id;
        $result['story_name'] = empty($story_data)?'':$story_data->name;
        $result['story_description'] = empty($story_data)?'':$story_data->description;
        $result['story_attachments'] = $this->db->get_where(TBL_FILE, array('type'=>'story','object_id'=>$story_id))->result();
        return $result;
    }

    //任务评分
    function grade_task_view($g_id)
    {
        $grade_id = (int)$g_id;
        $grade = $this->db->get_where(TBL_GRADE,array('id'=>$grade_id,'type'=>'task'))->row();

        $task_id = empty($grade)?'':$grade->object_id;
        $task_data = $this->task_model->get_task_by_id($task_id);
        $project_id = empty($task_data)?'':$task_data->project_id;

        $where = array('project_id'=>$project_id,'type'=>'task', 'is_deleted'=>'0');
        $grade = $this->db->get_where(TBL_GRADE_SETTING,$where)->result();
        foreach($grade as $key=>$val)
        {
            $desc_item = $this->db->get_where(TBL_GRADE_DESCRIPTION,array('grade_setting_id'=>$val->id, 'is_deleted'=>'0'))->result();
            $val->description_item = $desc_item;
            //获取评价
            $where = array(
                'setting_id'=>$val->id,
                'grade_id'=>$grade_id
            );
            $grade_score = $this->db->get_where(TBL_GRADE_SCORE,$where)->row();
            $val->score = empty($grade_score)?'':$grade_score;
        }

        $result['data'] = $grade;
        $result['project_id'] = $project_id;
        $result['project_name'] = $this->project_model->query_name_by_id($project_id);
        $result['task_id'] = $task_id;
        $result['grade_id'] = $grade_id;
        $result['task_name'] = empty($task_data)?'':$task_data->name;
        $result['task_description'] = empty($task_data)?'':$task_data->description;
        $result['task_attachments'] = $this->db->get_where(TBL_FILE, array('type'=>'task','object_id'=>$task_id))->result();
        return $result;
    }

    function grade_storyedit($g_id=null)
    {
        $grade_desc_id = $this->input->get_post('grade_desc_id');
        $grade_description_radio = $this->input->get_post('grade_description_radio');
        $grade_description = $this->input->get_post('grade_description');
        $grade_id = (int)$g_id;

        foreach($grade_desc_id as $key=>$desc_id)
        {
            if(!isset($grade_description_radio[$desc_id])){
                continue;
            }
            $grade_desc = $this->db->get_where(TBL_GRADE_DESCRIPTION, array('id'=>$grade_description_radio[$desc_id]))->row();
            $res = $this->db->get_where(TBL_GRADE_SCORE,array('grade_id'=>$grade_id,'setting_id'=>$desc_id))->row();
            if($res)//修改
            {
                $data = array(
                    'description_id'=>$grade_desc->id,
                    'description'=>$grade_description[$desc_id]
                );
                $where = array(
                    'grade_id'=>$grade_id,
                    'setting_id'=>$desc_id
                );
                $this->db->where($where);
                $this->db->update(TBL_GRADE_SCORE, $data);
            }else
            {
                $data = array(
                    'grade_id'=>$grade_id,
                    'setting_id'=>$desc_id,
                    'description_id'=>$grade_desc->id,
                    'description'=>$grade_description[$desc_id]
                );
                // print_r($data);exit;
                $this->db->insert(TBL_GRADE_SCORE, $data);

                //更新grade的评价状态
                $this->db->where(array('id'=>$grade_id));
                $this->db->update(TBL_GRADE, array('is_graded'=>'1', 'grade_date'=>date('Y-m-d H:i:s')));
            }
        }
        return TRUE;
    }

    function grade_taskedit($g_id=null)
    {
        $grade_id = (int)$g_id;
        $grade_desc_id = $this->input->get_post('grade_desc_id');
        $grade_description_radio = $this->input->get_post('grade_description_radio');
        $grade_description = $this->input->get_post('grade_description');

        //获取grade_id
        $grade = $this->db->get_where(TBL_GRADE,array('id'=>$grade_id,'type'=>'task'))->row();
        $grade_id = $grade->id;

        foreach($grade_desc_id as $key=>$desc_id)
        {
            $grade_desc = $this->db->get_where(TBL_GRADE_DESCRIPTION, array('id'=>$grade_description_radio[$desc_id]))->row();
            $res = $this->db->get_where(TBL_GRADE_SCORE,array('grade_id'=>$grade_id,'setting_id'=>$desc_id))->row();
            if($res)//修改
            {
                $data = array(
                    'description_id'=>$grade_desc->id,
                    'description'=>$grade_description[$desc_id]
                );
                $where = array(
                    'grade_id'=>$grade_id,
                    'setting_id'=>$desc_id
                );
                $this->db->where($where);
                $this->db->update(TBL_GRADE_SCORE, $data);
            }else
            {
                $data = array(
                    'grade_id'=>$grade_id,
                    'setting_id'=>$desc_id,
                    'description_id'=>$grade_desc->id,
                    'description'=>$grade_description[$desc_id]
                );
                $this->db->insert(TBL_GRADE_SCORE, $data);

                //更新grade的评价状态
                $this->db->where(array('id'=>$grade_id));
                $this->db->update(TBL_GRADE, array('is_graded'=>'1', 'grade_date'=>date('Y-m-d H:i:s')));
            }
        }
        return TRUE;
    }

    //管理员获取项目下的所有项目列表
    function grade_setlist($page = 1, $page_size = 50, $condition = '')
    {
        $page = $page < 0 ? 1 : (int)$page;
        $limit = $page_size;
        $start = $limit * ($page - 1);

        if($condition && is_array($condition)){
            $this->db->where($condition);
        }

        //查询当前用户id所创建的需求评价
        $order = $this->input->get('order');
        $sort = $this->input->get('sort');
        if($order && $sort){
            $this->db->order_by($order, $sort);
        }else{
            $this->db->order_by('id', 'desc');
        }
        $this->db->where(array('is_deleted' => '0'));
        $project_data = $this->db->get(TBL_PROJECT,$limit, $start)->result();

        foreach($project_data as $row)
        {
            $row->is_graded = false;
            //该项目是否已经设置
            $setting = $this->db->get_where(TBL_GRADE_SETTING,array('project_id'=>$row->id))->result();
            if(!$setting || count($setting)==0)
            {
                $row->setter = '';
                continue;
            }
            //设置人
            $user = $this->user_model->get_user_by_id($setting[0]->create_by);
            $row->setter = empty($user->account)?'':$user->account;

            //只要有评分，就不能设置了
            foreach($setting as $set)
            {
                $grade_score = $this->db->get_where(TBL_GRADE_SCORE,array('setting_id'=>$set->id))->row();
                if($grade_score)
                    $row->is_graded = true;
            }
        }

        $data['data'] = $project_data;
        $data['page_total'] = count($project_data);
        $this->db->where(array('is_deleted' => '0'));
        $data['total'] = $this->db->count_all_results(TBL_PROJECT);
        $data['current_page'] = $page;
        $data['total_page'] = (int)(($data['total']-1)/$limit + 1);
        $data['page_html'] = $this->create_page($data['total'], $page_size);
        return $data;
    }

    //管理员评价设置
    function set_grade($p_id=null)
    {
        // print_r($this->input->post());exit;
        if(empty($p_id)){
            return FALSE;
        }
        $story_content = $this->input->post('story_content');
        $story_desc = $this->input->post('story_desc');
        $story_score = $this->input->post('story_score');
        $story_review_required = $this->input->post('story_reviews_required');
        $task_content = $this->input->post('task_content');
        $task_desc = $this->input->post('task_desc');
        $task_score = $this->input->post('task_score');
        $task_review_required = $this->input->post('task_reviews_required');

        $setting_story_id = $this->input->post('setting_story_id');
        $setting_story_description_id = $this->input->post('setting_story_description_id');
        $setting_task_id = $this->input->post('setting_task_id');
        $setting_task_description_id = $this->input->post('setting_task_description_id');

        $this->db->trans_start();
        $exists_settings = $this->db->get_where(TBL_GRADE_SETTING, array('project_id' => (int)$p_id, 'type' => 'story', 'is_deleted' => '0'))->result_array();
        foreach ($exists_settings as $jj => $hh) {
            $exists_setting_ids[$jj] = $hh['id'];
        }
        $new_setting_ids = array();
        foreach ($story_content as $key => $val) {
            if(empty($val)){
                continue;
            }
            if(!empty($setting_story_id[$key])){
                // 更新
                $data_story_setting = array(
                    'content' => trim($val)
                );
                $this->db->update(TBL_GRADE_SETTING, $data_story_setting, array('id' => $setting_story_id[$key]));
                $grade_setting_id = $setting_story_id[$key];
            }else{
                // 插入
                $data_story_setting = array(
                    'project_id' => $p_id,
                    'type' => 'story',
                    'content' => trim($val),
                    'create_by' => $this->current_user_id,
                    'create_date' => date('Y-m-d H:i:s')
                );
                $this->db->insert(TBL_GRADE_SETTING, $data_story_setting);
                $grade_setting_id = $this->db->insert_id();
            }

            $new_setting_ids[] = $grade_setting_id;

            foreach ($story_desc[$key] as $k => $v) {
                if(!isset($story_desc[$key][$k]) || !isset($story_score[$key][$k])){
                    continue;
                }
                if(!empty($setting_story_description_id[$key][$k])){
                    $data_grade_description = array(
                        'desc' => trim($story_desc[$key][$k]),
                        'score' => (float)$story_score[$key][$k],
                        'review_required' => empty($story_review_required[$key][$k])?'0':'1'
                    );
                    $this->db->update(TBL_GRADE_DESCRIPTION, $data_grade_description, array('id' => $setting_story_description_id[$key][$k]));
                }else{
                    $data_grade_description = array(
                        'grade_setting_id' => $grade_setting_id,
                        'desc' => trim($story_desc[$key][$k]),
                        'score' => (float)$story_score[$key][$k],
                        'review_required' => empty($story_review_required[$key][$k])?'0':'1',
                        'level' => 0
                    );
                    $this->db->insert(TBL_GRADE_DESCRIPTION, $data_grade_description);
                    $setting_story_description_id[$key][] = $this->db->insert_id();
                }
            }
            // print_r($setting_story_description_id);exit;
            $this->db->select('id');
            $exists_ids = $this->db->get_where(TBL_GRADE_DESCRIPTION, array('grade_setting_id'=>$grade_setting_id))->result();
            foreach ($exists_ids as $kk => $obj) {
                $exists_ids[$kk] = $obj->id;
            }
            // print_r($exists_ids);exit;
            $need_delete_ids = array_diff($exists_ids, $setting_story_description_id[$key]);
            // print_r($need_delete_ids);exit;
            if(!empty($need_delete_ids)){
                $this->db->where_in('id', $need_delete_ids);
                $this->db->update(TBL_GRADE_DESCRIPTION, array('is_deleted'=>'1'));
            }
        }
        // print_r($exists_setting_ids);exit;
        // print_r($new_setting_ids);exit;
        $need_delete_setting_ids = array_diff($exists_setting_ids, $new_setting_ids);
        // print_r($need_delete_setting_ids);exit;
        if(!empty($need_delete_setting_ids)){
            $this->db->where_in('id', $need_delete_setting_ids);
            $this->db->update(TBL_GRADE_SETTING, array('is_deleted'=>'1'));
        }

        //---------------------------------------------------------------------------------------------------------//
        
        $exists_settings = $this->db->get_where(TBL_GRADE_SETTING, array('project_id' => (int)$p_id, 'type' => 'task', 'is_deleted' => '0'))->result_array();
        $exists_setting_ids = array();
        foreach ($exists_settings as $jj => $hh) {
            $exists_setting_ids[$jj] = $hh['id'];
        }
        $new_setting_ids = array();
        foreach ($task_content as $key => $val) {
            if(empty($val)){
                continue;
            }
            if(!empty($setting_task_id[$key])){
                // 更新
                $data_story_setting = array(
                    'content' => trim($val)
                );
                $this->db->update(TBL_GRADE_SETTING, $data_story_setting, array('id' => $setting_task_id[$key]));
                $grade_setting_id = $setting_task_id[$key];
            }else{
                $data_task_setting = array(
                    'project_id' => $p_id,
                    'type' => 'task',
                    'content' => trim($val),
                    'create_by' => $this->current_user_id,
                    'create_date' => date('Y-m-d H:i:s')
                );
                $this->db->insert(TBL_GRADE_SETTING, $data_task_setting);
                $grade_setting_id = $this->db->insert_id();
            }

            $new_setting_ids[] = $grade_setting_id;

            // print_r($task_desc[$key]);exit;
            foreach ($task_desc[$key] as $k => $v) {
                if(!isset($task_desc[$key][$k]) || !isset($task_score[$key][$k])){
                    continue;
                }
                if(!empty($setting_task_description_id[$key][$k])){
                    $data_grade_description = array(
                        'desc' => trim($task_desc[$key][$k]),
                        'score' => (float)$task_score[$key][$k],
                        'review_required' => empty($task_review_required[$key][$k])?'0':'1'
                    );
                    $this->db->update(TBL_GRADE_DESCRIPTION, $data_grade_description, array('id' => $setting_task_description_id[$key][$k]));
                }else{
                    $data_grade_description = array(
                        'grade_setting_id' => $grade_setting_id,
                        'desc' => trim($task_desc[$key][$k]),
                        'score' => (float)$task_score[$key][$k],
                        'review_required' => empty($task_review_required[$key][$k])?'0':'1',
                        'level' => 0
                    );
                    // print_r($data_grade_description);exit;
                    $this->db->insert(TBL_GRADE_DESCRIPTION, $data_grade_description);
                    $setting_task_description_id[$key][] = $this->db->insert_id();
                }
            }
            // print_r($setting_task_description_id);exit;
            // echo $grade_setting_id;exit;
            $this->db->select('id');
            $exists_ids = $this->db->get_where(TBL_GRADE_DESCRIPTION, array('grade_setting_id'=>$grade_setting_id))->result();
            foreach ($exists_ids as $kk => $obj) {
                $exists_ids[$kk] = $obj->id;
            }
            // print_r($exists_ids);exit;
            $need_delete_ids = array_diff($exists_ids, $setting_task_description_id[$key]);
            // print_r($need_delete_ids);exit;
            if(!empty($need_delete_ids)){
                $this->db->where_in('id', $need_delete_ids);
                $this->db->update(TBL_GRADE_DESCRIPTION, array('is_deleted'=>'1'));
            }
        }

        // print_r($exists_setting_ids);exit;
        // print_r($new_setting_ids);exit;
        $need_delete_setting_ids = array_diff($exists_setting_ids, $new_setting_ids);
        // print_r($need_delete_setting_ids);exit;
        if(!empty($need_delete_setting_ids)){
            $this->db->where_in('id', $need_delete_setting_ids);
            $this->db->update(TBL_GRADE_SETTING, array('is_deleted'=>'1'));
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return FALSE;
        }
        return TRUE;
    }
    /**
     * 根据项目ID获取项目评分设置
     */
    public function get_grade_setting($project_id){
        $settings = $this->db->get_where(TBL_GRADE_SETTING, array('project_id' => (int)$project_id, 'is_deleted' => '0'))->result();
        foreach ($settings as $key => $val) {
            $val->description = $this->db->get_where(TBL_GRADE_DESCRIPTION, array('grade_setting_id' => $val->id, 'is_deleted' => '0'))->result();
            $settings[$val->type][] = $val;
        }
        // print_r($settings);exit;
        return $settings;
    }

    /**
     * 是否有未关闭、未评价的需求
     * @return boolean [description]
     */
    public function has_ungrade_story(){
        $sql = "SELECT * FROM ".TBL_GRADE." WHERE grade_by={$this->current_user_id} AND is_graded='0' AND type='story'";
        $ungrade_stories = $this->db->query($sql)->result();
        // 已完成未关闭需求的完成时间大于5天的不可新建需求
        $sql = "SELECT * FROM ".TBL_STORY." WHERE assigned_to={$this->current_user_id} AND status='{$this->pmsdata['story']['status']['finished']['value']}' AND finished_date>0 AND datediff(NOW(), finished_date)>=5";
        $unclosed_stories = $this->db->query($sql)->result();
        return empty($ungrade_stories) && empty($unclosed_stories);
    }
}

?>