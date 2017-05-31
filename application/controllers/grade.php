<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by JetBrains PhpStorm.
 * User: zhangyin
 * Date: 13-12-24
 * Time: 上午10:08
 * 项目管理
 */

class Grade extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('grade_model');
        $this->load->model('task_model');
        $this->load->model('user_model');
        $this->load->model('project_model');
    }

    function index()
    {
        $this->gradestorylist();
    }

    //管理员查看评分入口,查询所有任务评价
    function gradeadmin($page = 1)
    {
        $body = $this->input->get('body');
        $result['body'] = $body;
        $result = $this->grade_model->grade_by_admin($page, PAGE_SIZE);
        //$this->layout->view('grade/grade_adminlist',$result);
        if($body){
            $this->layout->view('grade/grade_adminlist', $result, FALSE, FALSE);
        }else{
            $this->layout->view('grade/grade_adminlist', $result);
        }
    }

    //管理员查看评分详情
    function adminview($t_id=null,$type=null)
    {
        $body = $this->input->get('body');
        $data['body'] = $body;
        $story = null;
        $task = null;
        if($type == 'story')
            $story = $this->grade_model->grade_story_view($t_id);
        else if($type == 'task')
            $task = $this->grade_model->grade_task_view($t_id);

        $data['story'] = $story;
        $data['task'] = $task;
        $data['type'] = $type;
        if($body){
            $this->layout->view('grade/grade_adminview', $data, FALSE, FALSE);
        }else{
            $this->layout->view('grade/grade_adminview', $data);
        }
        //$this->layout->view('grade/grade_adminview',$data);
    }

    //当前用户查看需求评分入口
    function gradestorylist($page = 1)
    {
        $body = $this->input->get('body');
        $result['body'] = $body;
        $result = $this->grade_model->grade_storylist($page, PAGE_SIZE);
       // $this->layout->view('grade/grade_storylist',$result);
        if($body){
            $this->layout->view('grade/grade_storylist', $result, FALSE, FALSE);
        }else{
            $this->layout->view('grade/grade_storylist', $result);
        }
    }

    //当前用户,需求评分页面
    function storyview($g_id=null)
    {
        $body = $this->input->get('body');
        $data['body'] = $body;
        $data = $this->grade_model->grade_story_view($g_id);
        if($body){
            $this->layout->view('grade/grade_storyview', $data, FALSE, FALSE);
        }else{
            $this->layout->view('grade/grade_storyview', $data);
        }
        //$this->layout->view('grade/grade_storyview',$data);
    }
    function gradestoryedit($g_id=null)
    {
        $body = $this->input->get('body');
        $result = $this->grade_model->grade_storyedit($g_id);
        if(!$result)
        {
            echo '出错了';
        }
        header("Location:/grade/gradestorylist?body=".$body);
        exit;
    }

    //当前用户查看任务评分入口
    function gradetasklist($page = 1)
    {
        $body = $this->input->get('body');
        $result['body'] = $body;
        $result = $this->grade_model->grade_tasklist($page, PAGE_SIZE);
        if($body){
            $this->layout->view('grade/grade_tasklist', $result, FALSE, FALSE);
        }else{
            $this->layout->view('grade/grade_tasklist', $result);
        }
        //$this->layout->view('grade/grade_tasklist',$result);
    }

    //当前用户,需求评分页面
    function taskview($g_id=null)
    {
        $body = $this->input->get('body');
        $data['body'] = $body;
        $data = $this->grade_model->grade_task_view($g_id);
        //$this->layout->view('grade/grade_taskview',$data);
        if($body){
            $this->layout->view('grade/grade_taskview', $data, FALSE, FALSE);
        }else{
            $this->layout->view('grade/grade_taskview', $data);
        }
    }
    function gradetaskedit($g_id=null)
    {
        $body = $this->input->get('body');
        $result = $this->grade_model->grade_taskedit($g_id);
        if(!$result)
        {
            echo '出错了';
        }
        header("Location:/grade/gradetasklist?body=".$body);
        exit;
    }

    function setlist($page = 1)
    {
        $body = $this->input->get('body');
        $result['body'] = $body;
        //获取当前项目id下的任务列表
        $result = $this->grade_model->grade_setlist($page, PAGE_SIZE);
        //$this->layout->view('grade/grade_setlist',$result);
        if($body){
            $this->layout->view('grade/grade_setlist', $result, FALSE, FALSE);
        }else{
            $this->layout->view('grade/grade_setlist', $result);
        }
    }

    //管理员设置评价内容页面
    function setting($p_id=null)
    {
        $body = $this->input->get('body');
        $data['body'] = $body;
        $data['project'] = $this->project_model->get_project_by_id($p_id);
        $data['settings'] = $this->grade_model->get_grade_setting($p_id);
        //$this->layout->view('grade/grade_setgrade',$data);
        if($body){
            $this->layout->view('grade/grade_setgrade', $data, FALSE, FALSE);
        }else{
            $this->layout->view('grade/grade_setgrade', $data);
        }
    }

    //管理员设置评价内容
    function setgrade($p_id=null)
    {
        $body = $this->input->get('body');
        $result = $this->grade_model->set_grade($p_id);
        if($result){
            header('Location:/grade/setlist?body='.$body);
        }else{
            show_msg(ERROR ,'保存失败');
        }
    }

}

?>