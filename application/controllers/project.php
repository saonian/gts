<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by JetBrains PhpStorm.
 * User: zhangyin
 * Date: 13-12-24
 * Time: 上午10:08
 * 项目管理
 */

class Project extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('project_model');
        $this->load->model('user_model');
    }

    /**
     * 项目入口函数，默认显示项目列表
     * @param 无
     * @return 无
     */
    function index($page = 1)
    {
        $this->page($page);
    }

    //项目列表
    function page($page = 1)
    {
        //获取项目列表
        $data = $this->project_model->get_page($page, PAGE_SIZE);
        $this->layout->view('project/project_list', $data);
    }

    //创建项目
    function create()
    {
        //加载项目负责人列表
        $users = $this->user_model->get_all_users();
        $data['users'] = $users;
        $this->load->model('product_model');
        $data['all_products'] = $this->product_model->get_all_products();
        $this->layout->view('project/project_create', $data);
    }
    function create_project($p_id=null)
    {
        $project_id = $p_id;
        $res = $this->project_model->create_project($project_id);
        if(!$res)
        {
            $this->error('操作失败');
        }else
        {
            $this->page();
        }
    }

    function edit($p_id=null)
    {
        //加载项目负责人列表
        $users = $this->user_model->get_all_users();
        $data['users'] = $users;
        //获取项目信息
        $project = $this->project_model->query_by_id($p_id);
        $data['project'] = $project;
        $this->load->model('product_model');
        $data['all_products'] = $this->product_model->get_all_products();
        $this->layout->view('project/project_edit', $data);
    }
    function edit_project($p_id=null)
    {
        $project_id = $p_id;
        $res = $this->project_model->edit_project($project_id);
        if(!$res)
        {
            $this->error('操作失败');
        }else
        {
            $this->view($project_id);
        }
    }

    //查看项目
    function view($p_id=null)
    {
        //获取项目信息
        $data = $this->load_project_info($p_id);
        $this->layout->view('project/project_view', $data);
    }

    //开启项目
    function start($p_id=null)
    {
        //获取项目信息
        $data = $this->load_project_info($p_id);
        $this->layout->view('project/project_start', $data);
    }
    function start_project($p_id=null)
    {
        $project_id = $p_id;
        $res = $this->project_model->start_project($project_id);
        if(!$res)
        {
            $this->error('操作失败');
        }else
        {
            $this->view($project_id);
        }
    }

    //项目延期
    function delay($p_id=null)
    {
        //获取项目信息
        $data = $this->load_project_info($p_id);
        $this->layout->view('project/project_delay', $data);
    }
    function delay_project($p_id=null)
    {
        $project_id = $p_id;
        $res = $this->project_model->delay_project($project_id);
        if(!$res)
        {
            $this->error('操作失败');
        }else
        {
            $this->view($project_id);
        }
    }

    //项目挂起\
    function hang($p_id=null)
    {
        //获取项目信息
        $data = $this->load_project_info($p_id);
        $this->layout->view('project/project_hang', $data);
    }
    function hang_project($p_id=null)
    {
        $project_id = $p_id;
        $res = $this->project_model->hang_project($project_id);
        if(!$res)
        {
            $this->error('操作失败');
        }else
        {
            $this->view($project_id);
        }
    }

    //项目关闭
    function close($p_id=null)
    {
        //获取项目信息
        $data = $this->load_project_info($p_id);
        $this->layout->view('project/project_close', $data);
    }
    function close_project($p_id=null)
    {
        $project_id = $p_id;
        $res = $this->project_model->close_project($project_id);
        if(!$res)
        {
            $this->error('操作失败');
        }else
        {
            $this->view($project_id);
        }
    }

    //删除项目
    function remove($p_id=null)
    {
        $project_id = $p_id;
        $res = $this->project_model->del_project($project_id);
        if(!$res)
        {
            $this->error('操作失败');
        }else
        {
            $this->page();
        }
    }

    /**
     * 错误提示
     * @param $msg 出错信息
     * @return 无
     */
    function error($msg)
    {
        // echo '<script>alert("'.$msg.'"); </script>';
        show_msg(ERROR, $msg);
    }

    /**
     * 加载项目信息
     * @param $p_id 项目id
     * @return Object $data 包含项目基本信息和项目操作的历史记录
     */
    function load_project_info($p_id)
    {
        $project = $this->project_model->query_by_id($p_id);
        $data['project'] = $project;

        $action = $this->project_model->get_project_history($p_id);
        $data['action'] = $action;
        return $data;
    }
}

?>