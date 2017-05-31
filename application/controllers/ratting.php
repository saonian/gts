<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *@author liuqingling
 *date 2014-08-25
 * 正负能力评分
 */
// error_reporting(E_ALL);
class Ratting extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('rating_model');
        $this->load->model('rating_content_model');
        $this->load->model('account_model');
        $this->load->model('department_model');
        $this->load->model('ratting_grade_summary');
        $this->ratting_grade_summary->login_log();
    }


     /**
     * 评分设置，默认取得当月的信息,当前月以前的数据不能更改，当前月的数据更改后下个月生效
     */
    public function ratsetting()
    {
        if($_POST){
            $str = '/^\+?[1-9][0-9]*$/';
            if(!preg_match($str,$_POST['manage_plus'])){
                $this->alert_msg('管理用户月度初始加分分值输入不合法,必须是正整数!');
            }
            if(!preg_match($str,$_POST['manage_minus'])){
                $this->alert_msg('管理用户月度初始减分分值输入不合法,必须是正整数!');
            }
            if(!preg_match($str,$_POST['common_plus'])){
                $this->alert_msg('普通用户月度初始加分分值输入不合法,必须是正整数!');
            }
            if(!preg_match($str,$_POST['common_minus'])){
                $this->alert_msg('普通用户月度初始减分分值输入不合法,必须是正整数!');
            }
            if(!preg_match($str,$_POST['delay_days'])){
                $this->alert_msg('延迟显示天数输入不合法,必须是正整数!');
            }
            //判断数据的合法性
            $str = '/^-?[1-9]\d*$/';
            $ratting_type = config_item('ratting_type');
            foreach($ratting_type as $key => $value) {
                $content = $this->input->post($value.'_content');
                $desc = $this->input->post($value.'_desc');
                $start_value = $this->input->post($value.'_start_value');
                $end_value = $this->input->post($value.'_end_value');
                $review_required = $this->input->post($value.'_reviews_required');

                foreach($content as $k => $v){
                    if(mb_strlen($v,'UTF-8') > 50){
                        $this->alert_msg("评价内容：\'$v\'已超过50个字符！");
                    }
                    while(!empty($desc[$k]) || !empty($start_value[$k]) || !empty($end_value[$k]) ){
                        $data_desc['desc'] = array_shift($desc[$k]);
                        if(!empty($data_desc['desc']) && mb_strlen( $data_desc['desc'],'UTF-8') > 100 ){
                            $this->alert_msg("评价说明：\'".$data_desc['desc']."\'已超过100个字符！");
                        }
                        $data_desc['start_value'] = array_shift($start_value[$k]);
                        if(!empty($data_desc['start_value']) && !preg_match($str,$data_desc['start_value'])){
                            $this->alert_msg("区间分值：\'".$data_desc['start_value']."\'不是整数！");
                        }
                        $data_desc['end_value'] = array_shift($end_value[$k]);
                        if(!empty($data_desc['end_value']) && !preg_match($str,$data_desc['end_value'])){
                            $this->alert_msg("区间分值：\'".$data_desc['end_value']."\'不是整数！");
                        }
                        if(!empty($data_desc['start_value']) && !empty($data_desc['end_value']) && $data_desc['start_value'] >= $data_desc['end_value']){
                            $this->alert_msg("区间分值：初始值必须小于结束值！");
                        }
                    }
                }
            }
            $grade_data = array(
                'manage_plus' => intval($_POST['manage_plus']),
                'manage_minus' => intval($_POST['manage_minus']),
                'common_plus' => intval($_POST['common_plus']),
                'common_minus' => intval($_POST['common_minus']),
                'delay_days' => intval($_POST['delay_days']),
                'year' => intval(date('Y')),
                'month' => intval(date('m'))
                );
            $return_grade_set = $this->rating_model->add_grade_set($grade_data);
            //想修改以前的数据，是不允许的
            if(strtotime($_POST['year'].'-'.$_POST['month']) < strtotime(date('Y-m'))){
                $this->alert_msg('不允许修改当月以前的数据！');
            }else{//修改的是当月的数据
                if($_POST['year'] == intval(date('Y')) && $_POST['month'] == intval(date('m'))){
                    //判断这个月及下个月的数据有没有
                    $check_month = $_POST['month'];
                    $check_year = $_POST['year'];
                    $is_exist1 = $this->rating_content_model->get_setting_by(array('year'=>$check_year,'month'=>$check_month));
                    if($check_month == 12){
                        $check_month = 1;
                        $check_year = $check_year + 1;
                    }else{
                        $check_month = $check_month + 1;
                    }
                    $is_exist2 = $this->rating_content_model->get_setting_by(array('year'=>$check_year,'month'=>$check_month));
                    if(empty($is_exist1) || empty($is_exist2)){
                        $return_content = $this->rating_content_model->add_content();
                        if($return_content){
                            $this->alert_msg('保存成功！');
                        }else{
                             $this->alert_msg('保存失败！');
                        }
                    }else{
                        $this->alert_msg($_POST['year'].'年'.$_POST['month'].'月及'.$check_year.'年'.$check_month.'月的数据已经存在，若要修改'.$check_year.'年'.$check_month.'月的数据，点搜索到'.$check_year.'年'.$check_month.'月的数据进行修改!');
                    }
                }else{//修改的是下个月的数据
                    $return_content = $this->rating_content_model->modify_content_next_month();
                    if($return_content){
                        $this->alert_msg('保存成功！');
                    }else{
                         $this->alert_msg('保存失败！');
                    }
                } 
            }
        }else{
            $params['year'] = !empty($_REQUEST['year'])?intval($_REQUEST['year']):intval(date('Y'));
            $params['month'] = !empty($_REQUEST['month'])?intval($_REQUEST['month']):intval(date('m'));

            $ratting_lists = $this->rating_model->get_ratset_detail($params);
            //print_r($ratting_lists);
            $this->load->vars('params',$params);
            //用来生成日期下拉框的参数

            /************如果本月数据和下月的数据都有，则隐藏保存按钮 start ***************/
            $check_year = intval(date('Y'));
            $check_month = intval(date('m'));
            $is_exist1 = $this->rating_content_model->get_setting_by(array('year'=>$check_year,'month'=>$check_month));
            if($check_month == 12){
                $check_month = 1;
                $check_year = $check_year + 1;
            }else{
                $check_month = $check_month + 1;
            }
            $is_exist2 = $this->rating_content_model->get_setting_by(array('year'=>$check_year,'month'=>$check_month));
            
            $button_hidden = 0;
            if(!empty($is_exist1) && !empty($is_exist2)){
               $button_hidden = 1;
            }
            /************如果本月数据和下月的数据都有，则隐藏保存按钮 end ***************/
            $this->load->vars('button_hidden',$button_hidden);
            $this->load->vars('year',$check_year);
            $this->load->vars('month',$check_month);
            $this->load->vars('ratting_lists',$ratting_lists);
            $this->layout->view('ratting/rat_setting');
        }
    }


    /**
     * 我要评分：普通用户登录则取出除自己以外的所有用户,管理员则可以取出自己的数据
     */
    public function userlist($page = 1){
        $params = array();  
        $params['department_id'] = $this->input->get('department_id');
        $params['real_name'] = trim($this->input->get('real_name'));
        $params['is_login'] = intval($this->input->get('is_login'));
        $year = empty($_REQUEST['year'])?intval(date('Y')):intval($_REQUEST['year']);
        $month = empty($_REQUEST['month'])?intval(date('m')):intval($_REQUEST['month']);
        $params['year'] = $year;
        $params['month'] = $month;
        $account = $this->ratting_grade_summary->account_list($params,$page);
        $this->load->vars('year',intval(date('Y')));
        $this->load->vars('month',intval(date('m')));
        $parent_department = $this->department_model->get_all_department_info();
        //$parent_department = $this->department_model->parent_department_list();
        $this->load->vars('params',$params);
        $this->load->vars('account',$account);
        $this->load->vars('parent_department',$parent_department);
        $this->layout->view('ratting/user_list');
    }
    
    /**
     * 关注 or 取消关注
     */
    public function attention(){
        $attention = $_SESSION['userinfo']['attention'];
        $attention_arr = array();
        $attention_arr = explode(",",$attention);
        $uid = $this->input->post('uid');
        if(in_array($uid,$attention_arr)){//原来已经关注了，现在取消关注
            $key = array_search($uid,$attention_arr);
            array_splice($attention_arr,$key,1);
            $msg = '关注';
        }else{
            $attention_arr[] = $uid;
            $msg = '取消关注';
        }
        $new_uid_str = implode(',',$attention_arr);
        $new_uid_str = trim($new_uid_str,',');
        $data = array('attention'=>$new_uid_str);
        $_SESSION['userinfo']['attention'] = $new_uid_str;
        $return = $this->rating_model->update_attention($_SESSION['userinfo']['id'],$data);
        if($return){
            echo $msg;
        }else{
            echo 0;
        }
    }
    /**
     * 设为管理成员或者取消管理成员
     */
    public function change_manage(){
        $uid = $this->input->post('uid');
        $grade = $this->ratting_grade_summary->get_grade_detail($uid,intval(date('Y')),intval(date('m')));
        $grade_set = $this->rating_model->get_grade_set();
        $user_info = $this->rating_content_model->get_user_info_by_uid($uid);
        $is_manage = $user_info['is_manage'];

        $is_rating = $this->ratting_grade_summary->get_is_rating($uid,intval(date('Y')),intval(date('m')));
        if(!empty($grade) && !$is_rating && $grade['plus_last'] == 0 && $grade['minus_last'] == 0){//这里是一种异常的情况，就是没有评分，而且剩余分数都是0
            if($is_manage == 1){
                $grade = array(
                    'plus_last' => $grade_set['manage_plus'],
                    'minus_last' => $grade_set['manage_minus']
                );
            }else{
                $grade = array(
                    'plus_last' => $grade_set['common_plus'],
                    'minus_last' => $grade_set['common_minus']
                );
            }
        }
        if($is_manage == 1){//这个是取消管理的操作
            if(!empty($grade) && ((($grade_set['manage_plus'] - $grade['plus_last']) >  $grade_set['common_plus'])  || (($grade_set['manage_minus'] - $grade['minus_last']) >  $grade_set['common_minus']))){//不能修改，下个月再进行修改
                echo 1;
                return;
            }else{
                if(empty($grade)){
                    $grade = array(
                        'plus_last' => $grade_set['manage_plus'],
                        'minus_last' => $grade_set['manage_minus']
                        );
                }
                $data = array(
                    'uid' => $uid,
                    'year' => intval(date('Y')),
                    'month' => intval(date('m')),
                    'plus_last' => $grade_set['common_plus'] - ($grade_set['manage_plus'] - $grade['plus_last']),
                    'minus_last' => $grade_set['common_minus'] - ($grade_set['manage_minus'] - $grade['minus_last'])
                    );
                $desc = '我要评分-取消管理-引起'.$user_info['real_name'].'plus_last、minus_last变化';
                $this->rating_model->update_grade_summary($uid,$data,'',$desc);
            }
            $manage = 2;
            $msg = '设为管理';
        }else{//这个是设为管理的操作
            if(empty($grade)){
                $grade = array(
                    'plus_last' => $grade_set['common_plus'],
                    'minus_last' => $grade_set['common_minus']
                    );
                }
            $data = array(
                'uid' => $uid,
                'year' => intval(date('Y')),
                'month' => intval(date('m')),
                'plus_last' => $grade_set['manage_plus'] - ($grade_set['common_plus'] - $grade['plus_last']),
                'minus_last' => $grade_set['manage_minus'] - ($grade_set['common_minus'] - $grade['minus_last'])
                );
            $desc = '我要评分-设为管理-引起'.$user_info['real_name'].'plus_last、minus_last变化';
            $this->rating_model->update_grade_summary($uid,$data,'',$desc);
            $manage = 1;
            $msg = '取消管理';
        }
        $data = array('is_manage'=>$manage);
        $_SESSION['userinfo']['is_manage'] = $manage;
        $return = $this->rating_model->update_attention($uid,$data);
        if($return){
            echo $msg;
        }else{
            echo 0;
        }
    }
   
    /**
     * 给某个人的评分页面
     */
    function rat_index(){
       $uid = intval($_REQUEST['uid']);
       $rated_user = $this->rating_content_model->get_user_info_by_uid($uid);
       if(empty($_REQUEST['uid']) || empty($rated_user)){
            $this->alert_msg('用户不存在!');
        }
       $ratting_sets = config_item('ratting');
       //判断应该获取哪个月的评分设置
       $param['year'] = intval(date('Y'));
       $param['month'] = intval(date('m'));

       $date = $this->rating_content_model->search_month($param);
       $param['year'] = isset($date['year']) ? $date['year'] : intval(date('Y'));
       $param['month'] = isset($date['month']) ? $date['month'] : intval(date('m'));

       $param['level'] = trim($_REQUEST['level']);//相当于搜索的一级
       $param['type'] = trim($_REQUEST['type']);//相当于搜索的二级
       $param['id'] = trim($_REQUEST['id']);//相当于搜索的三级
       $ratting_lists = $this->rating_content_model->get_setting_search($param);
       //$ratting_lists = $this->rating_model->get_ratset_detail($params);
       $this->load->vars('param',$param);
       $this->load->vars('rated_user',$rated_user);
       $this->load->vars('ratting_sets',$ratting_sets);
       $this->load->vars('ratting_lists',$ratting_lists);
       $this->load->vars('ratting_lists_json',json_encode($ratting_lists));
       $this->layout->view('ratting/rat_index');
    }
    /**
     * 保存单个某项评分
     */
    public function save_ratting_single(){
        if(empty($_POST['grade'])){
            $msg = '请作出评价!';
            $arr = array('flag' => -1,'info' => $msg);
            echo json_encode($arr);return;
        }
        // $str = '/^-?[1-9]\d*$/';
        // if(!preg_match($str,$_POST['grade'])){
        //     $msg = '请输入整数!';
        //     $arr = array('flag' => -1,'info' => $msg);
        //     echo json_encode($arr);return;
        // }
        $desc_info = $this->rating_content_model->get_description_by_id($_POST['description_id']);
        // if(!($_POST['grade'] >= $desc_info['start_value'] && $_POST['grade'] <= $desc_info['end_value'])){
        //     $msg = '输入的分值必须介于'.$desc_info['start_value'].'和'.$desc_info['end_value'].'之间!';
        //     $arr = array('flag' => -1,'info' => $msg);
        //     echo json_encode($arr);return;
        // }
        $last_grade = $this->rating_model->get_last_grade();
        if($_POST['grade'] > 0 && $_POST['grade'] > $last_grade['last_plus']){
            $msg = '对不起，您本月加分分值仅剩余'.$last_grade['last_plus'].'分！';
            $arr = array('flag' => -1,'info' => $msg);
            echo json_encode($arr);return;
        }
        if($_POST['grade'] < 0 && -$_POST['grade'] > $last_grade['last_minus']){
            $msg = '对不起，您本月减分分值仅剩余'.$last_grade['last_minus'].'分！';
            $arr = array('flag' => -1,'info' => $msg);
            echo json_encode($arr);return;
        }
        if($desc_info['review_required'] == 1 && empty($_POST['rating_desc'])){
            $msg = '请输入评分事件!';
            $arr = array('flag' => -1,'info' => $msg);
            echo json_encode($arr);return;
        }
        // if($_POST['uid'] == $_SESSION['userinfo']['id'] || $_POST['real_name'] == $_SESSION['userinfo']['real_name']){
        //     echo '对不起，不能自己给自己评分!';
        //     return;
        // }
        $data = array(
            'rating_uid' => trim($_SESSION['userinfo']['id']),
            'rating_name' => trim($_SESSION['userinfo']['real_name']),
            'rating_account' => trim($_SESSION['userinfo']['account']),
            'rated_uid' => intval($_POST['uid']),
            'rated_name' => trim($_POST['real_name']),
            'rated_account' => trim($_POST['rated_account']),
            'content_id' => intval($_POST['content_id']),
            'type' => trim($_POST['content_type']),
            'description_id' => intval($_POST['description_id']),
            'content' => trim($_POST['content_name']),
            'level' => trim($_POST['description_level']),
            'grade' => intval($_POST['grade']),
            'rating_desc' => trim($_POST['rating_desc']),
            'status' => 1,
            'added_by' => $_SESSION['userinfo']['real_name'],
            'addtime' => date('Y-m-d H:i:s')
            );
        $flag = $this->rating_model->save_ratting_single($data);
        $last_grade = $this->rating_model->get_last_grade();
        $arr = array('flag' => $flag,'last_plus'=>$last_grade['last_plus'],'last_minus'=>$last_grade['last_minus']);
        echo json_encode($arr);
    }
    /**
     * 保存所有评分
     */
    public function save_ratting_all(){
        $param = $_POST;
        $flag = $this->rating_model->save_ratting_all($param);
        $last_grade = $this->rating_model->get_last_grade();
        $arr = array('flag' => $flag,'last_plus'=>$last_grade['last_plus'],'last_minus'=>$last_grade['last_minus']);
        echo json_encode($arr);
    }
    /**
     * 查看某人的详细得分情况
     */
    public function rat_detail(){
        $params['uid'] = intval($_REQUEST['uid']);
        $userinfo = $this->rating_content_model->get_user_info_by_uid($params['uid']);
        if(empty($_REQUEST['uid']) || empty($userinfo)){
            $this->alert_msg('用户不存在!');
        }
        $params['year'] = intval($_REQUEST['year']);
        $params['month'] = intval($_REQUEST['month']);
        
        $params['real_name'] = $userinfo['real_name'];
        //获取评分设置
        $ratset_detail = $this->rating_model->get_ratset_detail($params);
        //获取每项的得分
        $item_grade = $this->rating_model->get_item_grade($params);
        foreach ($ratset_detail as $key => $value) {
            $count = 0;
            foreach ($value['child'] as $ke => $va) {
                $sum = 0;
                foreach ($va['child'] as $k => $v) {
                   $count += $item_grade[$v['id']];
                   $sum += $item_grade[$v['id']];
                   $ratset_detail[$key]['child'][$ke]['child'][$k]['grade'] = $item_grade[$v['id']];
                }
                $ratset_detail[$key]['child'][$ke]['total_score'] = $sum;
            }
            $ratset_detail[$key]['total_score'] = $count;
        }
        $this->load->vars('year',intval(date('Y')));
        $this->load->vars('month',intval(date('m')));
        $this->load->vars('params',$params);
        $this->load->vars('ratset_detail',$ratset_detail);
        $this->load->vars('item_grade',$item_grade);
        $this->layout->view('ratting/rat_detail');
    }
    /**
     * 查看某人某方面的详细评分数据
     */
    public function rat_detail_by_content($page = 1){
        $params['uid'] = intval($_REQUEST['uid']);
        $params['level'] = trim($_REQUEST['level']);
        $userinfo = $this->rating_content_model->get_user_info_by_uid($params['uid']);
        if(empty($_REQUEST['uid']) || empty($userinfo)){
            $this->alert_msg('用户不存在!');
        }
        if(empty($_REQUEST['content_id'])){
            $this->alert_msg('数据不存在!');
        }
        $params['real_name'] = $userinfo['real_name'];
        if(empty($_REQUEST['start_date']) && empty($_REQUEST['end_date'])){
            $params['start_date'] = $_REQUEST['year'].'-'.str_pad($_REQUEST['month'],2,"0",STR_PAD_LEFT).'-01 00:00:00';
            $params['end_date'] = date('Y-m-t',strtotime($params['start_date'])).' 23:59:59';
        }else{
            $params['start_date'] = trim($_REQUEST['start_date']);
            $params['end_date'] = trim($_REQUEST['end_date']);
        }
        $params['content_id'] = intval($_REQUEST['content_id']);
        $content_info = $this->rating_content_model->get_content_by_id($params['content_id']);
        if(empty($content_info)){
            $this->alert_msg('数据不存在!');
        }
        $params['content'] = $content_info['content'];
        $detail_lists = $this->rating_model->get_rat_detail_by_content($params,$page);
        $this->load->vars('detail_lists',$detail_lists);
        $this->load->vars('params',$params);
        $this->layout->view('ratting/rat_detail_by_content');
    }
    /**
     * 查看某人某方面的详细评分数据---查看详细
     */
    public function ratting_content_detail(){
        $id = intval($_REQUEST['id']);
        if(empty($_REQUEST['id'])){
            $this->alert_msg('数据不存在!');
        }
        $ratting_content_details = $this->rating_model->get_ratting_content_detail($id);
        if(empty($ratting_content_details)){
            $this->alert_msg('数据不存在!');
        }
        $this->load->vars('ratting_content_details',$ratting_content_details);
        $this->layout->view('ratting/ratting_content_detail');
    }

     /**
     * 审核列表:当月的务必当月审核完毕，这个评分名称搜索有问题，觉得不要比较好，需要问下需求
     */
    public function auditlist($page = 1){
        $params = array(
            'rated_name' => !empty($_REQUEST['rated_name'])?trim($_REQUEST['rated_name']):'',
            'rated_uid' => !empty($_REQUEST['rated_uid'])?intval($_REQUEST['rated_uid']):'',
            'rating_name' => !empty($_REQUEST['rating_name'])?trim($_REQUEST['rating_name']):'',
            'status' => !empty($_REQUEST['status'])?intval($_REQUEST['status']):0,
            'addtime_start' => !empty($_REQUEST['addtime_start'])?trim($_REQUEST['addtime_start']):'',
            'audit_time_start' => !empty($_REQUEST['audit_time_start'])?trim($_REQUEST['audit_time_start']):'',
            'addtime_end' => !empty($_REQUEST['addtime_end'])?trim($_REQUEST['addtime_end']):'',
            'audit_time_end' => !empty($_REQUEST['audit_time_end'])?trim($_REQUEST['audit_time_end']):'',
            'year' => intval(date('Y')),
            'month' => intval(date('m')),
            'level' => trim($_REQUEST['level']),
            'type' => trim($_REQUEST['type']),
            'id' => trim($_REQUEST['id']),
            'is_added' => !empty($_REQUEST['is_added'])?intval($_REQUEST['is_added']):0,
            );
        $audit_list = $this->rating_model->audit_list($params,$page);
        $ratting_sets = config_item('ratting');
        $this->load->vars('params',$params);
        $this->load->vars('audit_list',$audit_list);
        $this->load->vars('ratting_sets',$ratting_sets);
        $this->layout->view('ratting/audit_list');
    }
    /**
     * 审核列表---确认操作--驳回
     */
    public function audit_confirm_reback(){
        $id = intval($_REQUEST['id']);
        $data = array(
            'audited_by' => $_SESSION['userinfo']['real_name'],
            'audit_time' => date('Y-m-d H:i:s')
            );
        if($_REQUEST['log'] == 1){
            $data['remark'] = $_REQUEST['reason'];
            $data['status'] = 3;
        }else{
            $data['status'] = 2;
            $data['is_added'] = 0;
        }
        $flag = $this->rating_model->audit_confirm_reback($id,$data);
        if($flag == 1){
            echo json_encode(array('return'=>1,'info'=>$data));
        }else{
            echo json_encode(array('return'=>0,'info'=>$flag));
        }
    }
    /**
     * 审核列表---批量确认---批量驳回
     */
    public function audit_confirm_reback_all(){
        $id_str = trim($_REQUEST['id_str']);
        $data = array(
            'audited_by' => $_SESSION['userinfo']['real_name'],
            'audit_time' => date('Y-m-d H:i:s')
            );
        if($_REQUEST['log'] == 1){
            $data['remark'] = trim($_REQUEST['reason']);
            $data['status'] = 3;
        }else{
            $data['status'] = 2;
            $data['is_added'] = 0;
        }
        $flag = $this->rating_model->audit_confirm_reback_all($id_str,$data);
        echo $flag;
    }

    /**
     * 级联菜单
     */
    public function get_types(){
        $key = trim($_POST['key']);
        $type= trim($_POST['type']);
        if($key){
            $ratting_sets = config_item('ratting');
            echo json_encode($ratting_sets[$key]['child']);
        }
        if($type){
            $param = array(
                'year' => intval(date('Y')),
                'month' => intval(date('m')),
                'type' => $type
                );
            $date = $this->rating_content_model->search_month($param);
            $param['year'] = $date['year'];
            $param['month'] = $date['month'];
            $arr = $this->rating_content_model->get_setting_by($param);
            echo json_encode($arr);
        }

    }

    /**
     * 消息提示封装函数
     */
    public function alert_msg($msg){
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        echo "<script type='text/javascript'>alert('".$msg."');history.go(-1);</script>";
        exit();
    }
    /**
     *获取月，获取到当前月
     */
    public function get_month(){
        $year = date('Y');
        $arr = array();
        $i = 1;
        if($_REQUEST['year'] == $year && $year == 2014){//今年就是2014年，则月份从8月份开始，一直到当前月
            $month = intval(date('m'));
            $i = 9;
        }else{//今年不是2014年
            if($_REQUEST['year'] == 2014){//2014年已经过去了，月份从8月份到12月份
                $i = 9;
                $month = 12;
            }else if($_REQUEST['year'] == $year){//选择的是今年且不是2014年
                $month = intval(date('m'));
            }else{//选择的不是今年，也不是2014年
                $month = 12;
            }
        }
        for($i;$i<=$month;$i++){
            $arr[]['month'] = $i;
        }
        echo json_encode($arr);
    }

    /**
     *获取月,获取到当前月的下个月
     */
    public function get_month_next(){
        $year = date('Y');
        $arr = array();
        $i = 1;
        if($_REQUEST['year'] == $year && $year == 2014){//今年就是2014年，则月份从9月份开始，一直到当前月
            $month = intval(date('m'));
            $i = 9;
        }else{//今年不是2014年
            if($_REQUEST['year'] == 2014){//2014年已经过去了，月份从8月份到12月份
                $i = 9;
                $month = 12;
            }else if($_REQUEST['year'] == $year){//选择的是今年且不是2014年
                $month = intval(date('m'));
            }else if($_REQUEST['year'] > $year){//选择的不是今年，也不是2014年,选择的是未来的一年
                $month = 0;
            }else{
                $month = 12;
            }
        }
        if($month == 12){
            $month = 11;
        }
        for($i;$i<=$month+1;$i++){
            $arr[]['month'] = $i;
        }
        echo json_encode($arr);
    }

    /**
    * @desc 查看个人得分页面
    */
    public function personal_grade(){
        $roles = config_item('ratting_role');
        if(!in_array($_SESSION['userinfo']['role']->id,$roles) && $_SESSION['userinfo']['real_name'] != $_REQUEST['user'] && !empty($_REQUEST['user'])){
            $this->alert_msg('请求错误!');
        }
        $last_grade = $this->rating_model->get_last_grade();
        $params = array();
        $params['user'] = isset($_REQUEST['user'])?trim($_REQUEST['user']):$_SESSION['userinfo']['real_name'];
        $params['begindate'] = isset($_REQUEST['begindate'])?trim($_REQUEST['begindate']):'';
        $params['enddate'] = isset($_REQUEST['enddate'])?trim($_REQUEST['enddate']):'';
        $results = $this->rating_model->get_my_rattings($params);
        $userinfo = $this->rating_content_model->get_user_info_by_name($params['user']);

        // echo '<pre>';
        // print_r($results);
        // echo '</pre>';exit;
        $this->load->vars('results',$results);
        $this->load->vars('params',$params);
        $this->load->vars('userinfo',$userinfo);
        $this->load->vars('last_grade',$last_grade);
        $this->layout->view('ratting/mygrade');
    }

    /**
    * @desc 所有部门的评分列表
    */
    public function rattingreport(){
        $params = array();
        $params['begindate'] = isset($_REQUEST['begindate'])?trim($_REQUEST['begindate']):date('Y-m-01',time());
        $params['enddate'] = isset($_REQUEST['enddate'])?trim($_REQUEST['enddate']):date('Y-m-t',time());
        $results = $this->rating_model->department_rate_statistics($params);

        $this->load->vars('results',$results);
        $this->load->vars('params',$params);
        $this->layout->view('ratting/rattingreport');
    }
    /**
    * @desc 某个部门的统计数据
    */
    public function dept_rattingreport(){
        $params = array();
        if(empty($_REQUEST['department_id'])){
            $this->alert_msg('数据不存在!');
        }
        $params['department_id'] = isset($_REQUEST['department_id'])?$_REQUEST['department_id']:'';
        $params['begindate'] = isset($_REQUEST['begindate'])?trim($_REQUEST['begindate']):date('Y-m-01',time());
        $params['enddate'] = isset($_REQUEST['enddate'])?trim($_REQUEST['enddate']):date('Y-m-t',time());
        $params['name'] = $this->rating_model->get_department_name($params['department_id']);
        $results = $this->rating_model->deptuser_statistics($params);
        $parent_department = $this->department_model->get_all_department_info();
        $this->load->vars('params',$params);
        $this->load->vars('results',$results);
        $this->load->vars('parent_department',$parent_department);
        $this->layout->view('ratting/dept_rattingreport');
    }


    /**
     *我的评分列表
     */
    public function rattinglist($page = 1){
        $params = array();
        $params['department_id'] = isset($_REQUEST['department_id'])?$_REQUEST['department_id']:'';
        $params['begin'] = isset($_REQUEST['begin'])?trim($_REQUEST['begin']):'';
        $params['end'] = isset($_REQUEST['end'])?trim($_REQUEST['end']):'';
        $params['rated_name'] = isset($_REQUEST['rated_name'])?trim($_REQUEST['rated_name']):'';
        $params['status'] = isset($_REQUEST['status'])?intval($_REQUEST['status']):'';
        $params['is_added'] = isset($_REQUEST['is_added'])?intval($_REQUEST['is_added']):'';
        $params['level'] = isset($_REQUEST['level'])?trim($_REQUEST['level']):'';

        $myrattinglist = $this->rating_model->get_my_ratting_list($params,$page);
        $parent_department = $this->department_model->get_all_department_info();
        $this->load->vars('parent_department',$parent_department);
        $this->load->vars('params',$params);
        $this->load->vars('myrattinglist',$myrattinglist);
        $this->layout->view('ratting/myrattinglist');
    }
    /**
     *我的评分列表---删除
     */
    public function ratting_del(){
        $id = intval($_REQUEST['id']);
        $this_grade = $this->rating_model->get_ratting_by_id($id);
        $flag = $this->rating_model->myratting_del($id);
        $date = substr($this_grade['addtime'],0,7);
        $last_grade = $this->rating_model->get_last_grade($date);
        $arr = array('flag' => $flag,'last_plus'=>$last_grade['last_plus'],'last_minus'=>$last_grade['last_minus']);
        echo json_encode($arr);
    }
     /** 
     *我的评分列表---查看 ratting_content_detail
     */

    /**
     *我的评分列表---修改
     */
    public function ratting_modify(){
        if($_POST['log'] == 1){//保存修改
            if(empty($_POST['grade'])){
                $msg = '请作出评价!';
                $arr = array('flag' => -1,'info' => $msg);
                echo json_encode($arr);return;
            }
            //这一条数据占用的分值
            $this_grade = $this->rating_model->get_ratting_by_id($_POST['id']);
            $date = substr($this_grade['addtime'],0,7);
            $last_grade = $this->rating_model->get_last_grade($date);
            if($this_grade['status'] == 1){
                if($this_grade['grade'] > 0){
                    $last_grade['last_plus'] += $this_grade['grade'];
                }else{
                    $last_grade['last_minus'] += -$this_grade['grade'];
                }
            }
            
            if($_POST['grade'] > 0 && $_POST['grade'] > $last_grade['last_plus']){
                $msg = '对不起，您'.substr($this_grade['addtime'],0,4).'年'.intval(substr($result['addtime'],5,2)).'月份加分分值仅剩余'.$last_grade['last_plus'].'分！';
                $arr = array('flag' => -1,'info' => $msg);
                echo json_encode($arr);return;
            }
            if($_POST['grade'] < 0 && -$_POST['grade'] > $last_grade['last_minus']){
                $msg = '对不起，您'.substr($this_grade['addtime'],0,4).'年'.intval(substr($result['addtime'],5,2)).'月份减分分值仅剩余'.$last_grade['last_minus'].'分！';
                $arr = array('flag' => -1,'info' => $msg);
                echo json_encode($arr);return;
            }
            if($desc_info['review_required'] == 1 && empty($_POST['rating_desc'])){
                $msg = '请输入评分事件!';
                $arr = array('flag' => -1,'info' => $msg);
                echo json_encode($arr);return;
            }
            $data = array(
                'description_id' => intval($_POST['description_id']),
                'level' => trim($_POST['description_level']),
                'grade' => intval($_POST['grade']),
                'status' => 1,
                'rating_desc' => trim($_POST['rating_desc']),
                'modified_by' => $_SESSION['userinfo']['real_name'],
                'modifytime' => date('Y-m-d H:i:s')
                );
            $flag = $this->rating_model->update_ratting_single($_POST['id'],$data);
            $last_grade = $this->rating_model->get_last_grade($date);
            $arr = array('flag' => $flag,'last_plus'=>$last_grade['last_plus'],'last_minus'=>$last_grade['last_minus']);
            echo json_encode($arr);
        }else{
            $id = intval($_REQUEST['id']);
            $ratting_content_details = $this->rating_model->get_ratting_content_detail($id);
            $this->load->vars('ratting_content_details',$ratting_content_details);
            $this->layout->view('ratting/myratting_modify');
        }
        
    }

    //资料设置
    public function infoset(){
        $this->layout->view('ratting/infoset');
    }
    //上传图片，切图
    public function setimg(){
        $up_config = config_item('upimg');
        $upfolder = $up_config['img']; //文件上传目录
        if(is_dir($upfolder) == false){
            mkdir($upfolder, 0777,recursive);
        }
        if(!empty($_FILES)){
            if($_FILES['_160img']['type'] == 'image/jpeg'){
                $last = 'jpg';
            }
            if($_FILES['_160img']['type'] == 'image/gif'){
                $last = 'gif';
            }
            if($_FILES['_160img']['type'] == 'image/png'){
                $last = 'png';
            }
            $data['image'] = $_SESSION['userinfo']['id'].'_'.$_SESSION['userinfo']['account'].'.'.$last;

            $tempfile = $_FILES['_160img']['tmp_name']; //文件临时目录
            $filename = 'big160_'.$data['image'];
            $imagepath1 = $upfolder.$filename;
            $msg1 = move_uploaded_file($tempfile,$imagepath1);

            $tempfile = $_FILES['_64img']['tmp_name']; //文件临时目录
            $filename = 'small64_'.$data['image'];
            $imagepath2 = $upfolder.$filename;
            $msg2 = move_uploaded_file($tempfile,$imagepath2);

            $this->load->model('user_model');
            $data['sign'] = trim($_GET['sign']);
            $this->user_model->save_user($data);
            if(!empty($data['image'])){
                $_SESSION['userinfo']['image'] = $data['image'];
            }
            if($msg1 && $msg2){
                echo json_encode(array('success'=>true,'url'=>'/'.$imagepath1));
            }else{
                echo json_encode(array('success'=>false));
            }
            exit;
        }
    }

    public function save_sign(){
        $data['sign'] = trim($_POST['sign']);
        $this->load->model('user_model');
        $this->user_model->save_user($data);
        if(!empty($data['sign'])){
            $_SESSION['userinfo']['sign'] = $data['sign'];
        }
    }

    // public function cutimg(){
    //      /*************按照坐标裁切图片********************/
    //     $dir = "http://".$_SERVER['HTTP_HOST'];
    //     $action = $_GET['action'];
    //     $up_config = config_item('upimg');
    //     if($action=='jcrop'){
    //         $crop = $_POST['crop'];
    //         if($crop){
    //             $src = $dir.$crop['path']; //图片全地址
    //             $pathinfo = pathinfo($src);
    //             $basename = $pathinfo['basename'];
    //             $extension = strtolower($pathinfo['extension']);
    //             $filename = $up_config['big_img'].$basename;
    //             $this->user_model->thumb($src, 160, 160, $crop['w'],$crop['h'], $$crop['x'],$crop['y'],$filename);
    //             $filename = $up_config['small_img'].$basename;
    //             $this->user_model->thumb($src, 64, 64, $crop['w'],$crop['h'], $$crop['x'],$crop['y'],$filename);
    //             $data['image'] = $basename;
    //         }
    //         $this->load->model('user_model');
    //         $data['sign'] = trim($_GET['sign']);
    //         $this->user_model->save_user($data);
    //         if(!empty($data['sign'])){
    //             $_SESSION['userinfo']['sign'] = $data['sign'];
    //         }
    //         if(!empty($data['image'])){
    //             $_SESSION['userinfo']['image'] = $basename;
    //         }
    //         echo 1;
    //         exit;
    //     }else{
    //         echo 2;
    //     }
    // }

    //设定每个人的评分初始值
    public function set_init_summary_value(){
        $flag = $this->ratting_grade_summary->set_init_summary_value();
        if($flag){
            echo '成功!';
        }else{
            echo '失败！';
        }
    }

    //延迟加分
    public function add_grade_by_time(){
        $flag = $this->ratting_grade_summary->add_grade_by_time();
        if($flag){
            echo 1;
        }else{
            echo 2;
        }
    }

}

