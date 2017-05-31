<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by JetBrains PhpStorm.
 * User: zhangyin
 * Date: 13-12-24
 * Time: 下午4:46
 * 测试任务
 */
class Testtask extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('testtask_model');
        $this->load->model('user_model');
        $this->load->model('project_model');
        $this->load->model('team_model');
    }

    public function index()
    {
        $this->page();
    }

    //测试列表
    function page()
    {
        //获取项目列表
        $data = $this->testtask_model->get_page();
        $this->layout->view('testtask/test_list', $data);
    }

    function create($t_id=null)
    {
        //加载项目负责人列表
        $users = $this->team_model->get_project_team();
        $data['users'] = $users;
        //获取所有项目
        $projects = $this->project_model->get_all_projects();
        $data['projects'] = $projects;
        //获取测试类型
        $status = $this->testtask_model->get_test_status();
        $data['status'] = $status;
        $task = $this->testtask_model->get_task($t_id);
        $data['pid'] = empty($task)?0:$task->project_id;
        $data['tid'] = empty($t_id)?0:(int)$t_id;
        $data['sid'] = empty($task)?0:$task->story_id;
        $data['task_opened_by'] = empty($task)?0:$task->opened_by;
        $this->layout->view('testtask/test_create', $data);
    }
    function create_test($t_id=null)
    {
        $test_id = $t_id;
        $res = $this->testtask_model->create_test($test_id);
        if(!$res)
        {
            $this->error('操作失败');
        }else
        {
            $this->page();
        }
    }

    //编辑
    function edit($t_id=null)
    {
        //加载项目负责人列表
        $users = $this->team_model->get_project_team();
        $data['users'] = $users;
        //获取所有项目
        $projects = $this->project_model->get_all_projects();
        $data['projects'] = $projects;
        //获取测试类型
        $status = $this->testtask_model->get_test_status();
        $data['status'] = $status;
        //获取项目信息
        $testtask = $this->testtask_model->query_by_id($t_id);
        $data['testtask'] = $testtask;

        $this->layout->view('testtask/test_edit', $data);
    }
    function edit_test($t_id=null)
    {
        $test_id = $t_id;
        $res = $this->testtask_model->edit_test($test_id);
        if(!$res)
        {
            $this->error('操作失败');
        }else
        {
            header("Location:/testtask/view/{$test_id}");
            exit;
        }
    }

    //查看测试
    function view($t_id=null)
    {
        //获取项目信息
        $data = $this->load_test_info($t_id);
        $this->layout->view('testtask/test_view', $data);
    }

    //开启测试
    function start($t_id=null)
    {
        //获取项目信息
        $data = $this->load_test_info($t_id);
        $this->layout->view('testtask/test_start', $data);
    }
    function start_test($t_id=null)
    {
        $test_id = $t_id;
        $res = $this->testtask_model->start_test($test_id);
        if(!$res)
        {
            $this->error('操作失败');
        }else
        {
            header("Location:/testtask/view/{$test_id}");
            exit;
        }
    }

    //关闭测试任务
    function close($t_id=null)
    {
        //获取项目信息
        $data = $this->load_test_info($t_id);
        $this->layout->view('testtask/test_close', $data);
    }
    function close_test($t_id=null)
    {
        $test_id = $t_id;
        $res = $this->testtask_model->close_test($test_id);
        if(!$res)
        {
            $this->error('操作失败');
        }else
        {
            header("Location:/testtask/view/{$test_id}");
            exit;
        }
    }

    /**
     * 错误提示
     * @param $msg 出错信息
     * @return 无
     */
    function error($msg)
    {
        show_msg(ERROR, $msg);
    }

    function json_story_by_pid($tid)
    {
        $data = $this->testtask_model->json_story_by_pid($tid);
        echo $data;
    }

    function json_task_by_tid($tid)
    {
        $data = $this->testtask_model->json_task_by_tid($tid);
        echo $data;
    }

    function load_test_info($t_id)
    {
        $testtask = $this->testtask_model->query_by_id($t_id);
        $data['testtask'] = $testtask;

        $action = $this->testtask_model->get_test_history($t_id);
        $data['action'] = $action;
        return $data;
    }

    function delete($id){
        $this->testtask_model->delete($id);
        header('Location:/testtask');
        exit;
    }

}
?>