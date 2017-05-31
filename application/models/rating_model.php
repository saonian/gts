<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rating_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        //$this->load->model('user_model');
    }
    /**
     *@desc 获取评分设置选项
     *@param $params['year'] int 年; $params['month'] int 月
     *@return array
     */
    public function get_ratset_detail($params){
        $ratting_sets = config_item('ratting');
        foreach ($ratting_sets as $key => $value){
            foreach ($value['child'] as $k => $v) {
                $sql = "select id,content from gg_rating_content where year=".$params['year']." and month = ".$params['month']." and type = '".$k."' order by id asc";
                $query = $this->db->query($sql);
                $result = $query->result_array();
                foreach ($result as $ke => $va) {
                    $sql = "select * from gg_rating_description where content_id = ".$va['id']." order by id asc";
                    $query = $this->db->query($sql);
                    $result[$ke]['description'] = $query->result_array();
                }
                unset($ratting_sets[$key]['child'][$k]);
                $ratting_sets[$key]['child'][] = array('title' => $v,'type' => $k,'child'=>$result);
            }
        }
        return $ratting_sets;
    }
    /**
    *@desc 获取本月 打分 基数，本月没有，就获取最近一个月的
    **/
    public function get_grade_set($year = '',$month = ''){
        $year = empty($year)?intval(date('Y')):$year;
        $month = empty($month)?intval(date('m')):$month;
        $sql = "select * from gg_rating_gradesetting where year = ".$year." and month = ".$month." order by year desc,month desc";
    	$query = $this->db->query($sql);
    	$result = $query->row_array();
        if(empty($result)){
            $sql = "select * from gg_rating_gradesetting where year = ".$year." and month < ".$month." order by year desc,month desc";
            $query = $this->db->query($sql);
            $result = $query->row_array();
            if(empty($result)){
                $sql = "select * from gg_rating_gradesetting where year < ".$year." order by year desc,month desc";
                $query = $this->db->query($sql);
                $result = $query->row_array();
            }
        }
    	return $result;
    }
    /**
    *查看表中是否有当月的信息，如果有则查看是否有下个月的信息（如果有，则修改下个月的，如果无，则添加下个月的），如果没有，则添加当月的信息
    *默认每次进来的，显示的都是当月的
    **/
    public function add_grade_set($data){
        //$result = $this->get_grade_set();
        $sql = "select * from gg_rating_gradesetting where year = ".$data['year']." and month = ".$data['month'];
        $query = $this->db->query($sql);
        $result = $query->row_array();
    	if(empty($result)){//为空，则添加本月的
            $data['addtime'] = date('Y-m-d H:i:s');
            $data['added_by'] = $_SESSION['userinfo']['real_name'];
    		$flag = $this->db->insert('gg_rating_gradesetting',$data);
            //添加日志
            $this->_write_log('add_grade_set','set_ratting',$data,'评分设置--设定普通、管理人员基础分值--新增本月操作');
    		if(!$flag){
    			return false;
    		}
    	}else{//查看下个月的有没有，没有，则添加，有，则更新
    		if($data['month'] == 12){
    			$data['month'] = 1;
    			$data['year'] = $data['year'] + 1;
    		}else{
    			$data['month'] = $data['month'] + 1;
    		}
    		//$result = $this->get_grade_set();
            $sql = "select * from gg_rating_gradesetting where year = ".$data['year']." and month = ".$data['month'];
            $query = $this->db->query($sql);
            $result = $query->row_array();
    		if(empty($result)){
                $data['addtime'] = date('Y-m-d H:i:s');
                $data['added_by'] = $_SESSION['userinfo']['real_name'];
    			$flag = $this->db->insert('gg_rating_gradesetting',$data);
                //添加日志
                $this->_write_log('add_grade_set','set_ratting',$data,'评分设置--设定普通、管理人员基础分值--新增下月操作');
    			if(!$flag){
    				return false;
    			}
    		}else{//更新
    			$this->db->where(array('month'=>$data['month'],'year'=>$data['year']));
    			$data['modifytime'] = date('Y-m-d H:i:s');
                $data['modified_by'] = $_SESSION['userinfo']['real_name'];
                $flag = $this->db->update('gg_rating_gradesetting', $data);
                //添加日志
                $this->_write_log('add_grade_set','set_ratting',$data,'评分设置--设定普通、管理人员基础分值--修改下月操作');
                if(!$flag){
                	return false;
                }
    		}
    	}
    	return true;
    }

    //更新关注
    public function update_attention($uid,$data){
        $this->db->where(array('id'=>$uid));
        $flag = $this->db->update('gg_user',$data);
        $this->_write_log('update_attention','update_attention',$this->db->last_query(),'我要评分--关注、管理设定--修改操作');
        if(!$flag){
            return false;
        }
        return true;
    }

    public function update_grade_summary($uid,$data,$db = '',$desc = ''){
        if(empty($db)){
            $db = $this->db;
        }
        $sql = "select * from gg_ratting_grade_summary where uid = ".$uid." and year = ".$data['year']." and month = ".$data['month'];
        $result = $db->query($sql)->row_array();
        if(empty($result)){//插入
            $desc = empty($desc)?'新增gg_ratting_grade_summary表数据':$desc;
            $data['addtime'] = date('Y-m-d H:i:s');
            $flag = $db->insert('gg_ratting_grade_summary',$data);
            $this->_write_log('modify_ratting_grade_summary','ratting_summary',$db->last_query(),$desc);
        }else{//更新
            $desc = empty($desc)?'更新gg_ratting_grade_summary表数据':$desc;
            $db->where(array('id'=>$result['id']));
            $flag = $db->update('gg_ratting_grade_summary',$data);
            $this->_write_log('modify_ratting_grade_summary','ratting_summary',$db->last_query(),$desc);
        }
        if(!$flag){
            return false;
        }
        return true;
    }

    //保存评分
    public function save_ratting_single($data){
        $flag = true;
        $this->db->trans_start();
        $this->db->insert('gg_rating',$data);
        $this->_write_log('save_ratting','ratting',$data,'我要评分--评分-->新增单个评分');
        $id = $this->db->insert_id();
        if(empty($id)){
            $flag = false;
        }
        //评分人的加分剩余、减分剩余修改
        $last_grade = $this->get_last_grade();
        $grade_summary_data = array(
            'uid' => $_SESSION['userinfo']['id'],
            'real_name' => $_SESSION['userinfo']['real_name'],
            'year' => intval(date('Y')),
            'month' => intval(date('m')),
            'plus_last' => $last_grade['last_plus'],
            'minus_last' => $last_grade['last_minus']
            );
        $desc = '我要评分--评分--评单个引起'.$_SESSION['userinfo']['real_name'].'plus_last、minus_last变化';
        $return = $this->update_grade_summary($data['rating_uid'],$grade_summary_data,$this->db,$desc);
        if(!$return){
            $flag = false;
        }
        $this->db->trans_complete();
        if($this->db->trans_status() === false || $flag === false){
            return 0;
        }else{
            return 1;
        }
    }
    //保存所有评分
    public function save_ratting_all($param){
        $this->load->model('rating_content_model');
        // if($param['uid'] == $_SESSION['userinfo']['id'] || $param['real_name'] == $_SESSION['userinfo']['real_name']){
        //     return '对不起，不能自己给自己评分!';
        // }
        $grade_sum = $this->sum_array($param['grade']);
        $last_grade = $this->get_last_grade();
        if($grade_sum['plus'] > $last_grade['last_plus']){
            return '对不起，您本月加分分值仅剩余'.$last_grade['last_plus'].'分！';
        }
        if(-$grade_sum['minus'] > $last_grade['last_minus']){
            return '对不起，您本月减分分值仅剩余'.$last_grade['last_minus'].'分！';
        }
        $data = array();
        $str = '/^-?[1-9]\d*$/';
        $plus = 0;
        $minus = 0;
        $this->db->trans_start();
        $flag = true;
        foreach ($param['content_id'] as $k => $v){
            if(!empty($param['grade'][$k]) || !empty($param['rating_desc'][$k]) || !empty($param['description_id'][$k])){
                if(empty($param['description_id'][$k])){
                    $flag = false;
                    return $param['content_name'][$k].':请选择您的评价!';
                }
                if(empty($param['grade'][$k])){
                    $flag = false;
                    return $param['content_name'][$k].':请输入得分!';
                }
                
                if(!preg_match($str,$param['grade'][$k])){
                    $flag = false;
                    return $param['content_name'][$k].':得分请输入整数!';
                }
                $desc_info = $this->rating_content_model->get_description_by_id($param['description_id'][$k]);
                if(!($param['grade'][$k] >= $desc_info['start_value'] && $param['grade'][$k] <= $desc_info['end_value'])){
                    $flag = false;
                    return $param['content_name'][$k].':输入的分值必须介于'.$desc_info['start_value'].'和'.$desc_info['end_value'].'之间!';
                }
                if($desc_info['review_required'] == 1 && empty($param['rating_desc'][$k])){
                    $flag = false;
                    return $param['content_name'][$k].':请输入评分事件!';
                }
                // if($param['grade'][$k] > 0){
                //     $plus += $param['grade'][$k];
                // }else{
                //     $minus += $param['grade'][$k];
                // }
                $data = array(
                    'rating_uid' => trim($_SESSION['userinfo']['id']),
                    'rating_name' => trim($_SESSION['userinfo']['real_name']),
                    'rating_account' => trim($_SESSION['userinfo']['account']),
                    'rated_uid' => intval($param['uid']),
                    'rated_name' => trim($param['real_name']),
                    'rated_account' => trim($param['rated_account']),
                    'content_id' => intval($param['content_id'][$k]),
                    'type' => trim($param['content_type'][$k]),
                    'description_id' => intval($param['description_id'][$k]),
                    'content' => trim($param['content_name'][$k]),
                    'level' => trim($param['description_level'][$k]),
                    'grade' => intval($param['grade'][$k]),
                    'rating_desc' => trim($param['rating_desc'][$k]),
                    'status' => 1,
                    'added_by' => $_SESSION['userinfo']['real_name'],
                    'addtime' => date('Y-m-d H:i:s')
                    );
                $this->db->insert('gg_rating',$data);
                $insert_id = $this->db->insert_id();
                $this->_write_log('save_ratting','ratting',$data,'我要评分--评分-->新增多个评分');
                if(empty($insert_id)){
                    $flag = false;
                }
            }
        }
        //评分人的加分剩余、减分剩余修改
        $last_grade = $this->get_last_grade();
        $grade_summary_data = array(
            'uid' => $_SESSION['userinfo']['id'],
            'real_name' => $_SESSION['userinfo']['real_name'],
            'year' => intval(date('Y')),
            'month' => intval(date('m')),
            'plus_last' => $last_grade['last_plus'],
            'minus_last' => $last_grade['last_minus']
            );
        $desc = '我要评分--评分--评多个引起'.$_SESSION['userinfo']['real_name'].'plus_last、minus_last变化';
        $return = $this->update_grade_summary($_SESSION['userinfo']['id'],$grade_summary_data,$this->db,$desc);
        if(!$return){
            $flag = false;
        }
        $this->db->trans_complete();
        if($this->db->trans_status() === false || $flag === false){
            return 0;
        }else{
            return 1;
        }
    }

    //获取某段时间内，某个用户，各项的得分,通过审核的
    public function get_item_grade($params){
        $date = $params['year'].'-'.str_pad($params['month'],2,"0",STR_PAD_LEFT);
        $sql = "select content_id,sum(grade) as grade_sum from gg_rating where status = 2 and substr(`addtime`,1,7)='".$date."'  and rated_uid = ".$params['uid']." group by content_id";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        $arr = array();
        foreach ($result as $k => $v) {
            $arr[$v['content_id']] = $v['grade_sum'];
        }
        return $arr;
    }
    /**
     * 查看某人某方面的详细评分数据
     */
    public function get_rat_detail_by_content($params, $page = 1, $pagesize = 50){
        $page = (int)$page < 1 ? 1 : (int)$page;
        if(!empty($params['level'])){
            $where = " and b.level like '%".$params['level']."%'";
        }
        $total = $this->db->query("SELECT COUNT(1) AS total from gg_rating as a left join gg_rating_description as b on a.description_id = b.id where a.rated_uid = ".$params['uid']." and a.content_id = ".$params['content_id']." and a.status = 2 and a.`addtime` >= '".$params['start_date']."' and a.`addtime` <= '".$params['end_date']."'".$where);
        $total_num = $total->row()->total;
        $sql = "select a.*,b.level from gg_rating as a left join gg_rating_description as b on a.description_id = b.id where a.rated_uid = ".$params['uid']." and a.content_id = ".$params['content_id']." and a.status = 2 and a.`addtime` >= '".$params['start_date']."' and a.`addtime` <= '".$params['end_date']."'".$where;
        $limit = " LIMIT ".($page-1)*$pagesize.",".$pagesize;
        $sql .=  $limit;
        $result = $this->db->query($sql)->result_array();
        $rs['total'] = $total_num;
        $rs['list'] = $result;
        $rs['total_page'] = (int)(($total_num-1)/$pagesize + 1);
        $rs['page_html'] = $this->create_page($total_num, $pagesize);
        return $rs;
    }
    /**
     * 查看某人某方面的详细评分数据---查看详细
     */
    public function get_ratting_content_detail($id){
        $this->load->model('rating_content_model');
        $sql = "select * from gg_rating where id = ".$id;
        $result = $this->db->query($sql)->row_array();
        if(empty($result)){
            return false;
        }
        $rat_set = $this->rating_content_model->get_ratset_by_content_id($result['content_id']);
        $result['rat_set'] = $rat_set;
        return $result;
    }
    /**
     * 审核列表
     */
    public function audit_list($params, $page = 1, $pagesize = 50){
        $page = (int)$page < 1 ? 1 : (int)$page;
        $where = "1";
        if(!empty($params['rated_name'])){
            $where .= " and (a.rated_name like '%".$params['rated_name']."%' or a.rated_account like '%".$params['rated_name']."%')";
        }
        if(!empty($params['rated_uid'])){
            $where .= " and a.rated_uid =".$params['rated_uid'];
        }
        if(!empty($params['rating_name'])){
            $where .= " and (a.rating_name like '%".$params['rating_name']."%' or a.rating_account like '%".$params['rating_name']."%')";
        }
        // if(!empty($params['addtime_start']) && !empty($params['addtime_end'])){
        //     $where .=" and a.addtime >= '".$params['addtime_start']." 00:00:00' and a.addtime <= '".$params['addtime_end']." 23:59:59'";
        // }
        if(!empty($params['addtime_start'])){
            $where .=" and a.addtime >= '".$params['addtime_start']." 00:00:00'";
        }
        if(!empty($params['addtime_end'])){
            $where .=" and a.addtime <= '".$params['addtime_end']." 23:59:59'";
        }

        if(!empty($params['audit_time_start'])){
            $where .=" and a.audit_time >= '".$params['audit_time_start']." 00:00:00'";
        }
        if(!empty($params['audit_time_end'])){
            $where .=" and a.audit_time <= '".$params['audit_time_end']." 23:59:59'";
        }

        // if(!empty($params['audit_time_start']) && !empty($params['audit_time_end'])){
        //     $where .=" and a.audit_time >= '".$params['audit_time_start']." 00:00:00' and a.audit_time <= '".$params['audit_time_end']." 23:59:59'";
        // }
        if(!empty($params['status'])){
            $where .= " and a.status = ".$params['status'];
        }
        if(!empty($params['is_added'])){
            if($params['is_added'] == 1){
               $params['is_added'] = 0; 
            }else if($params['is_added'] == 2){
                $params['is_added'] = 1; 
            }
            $where .= " and a.is_added = ".$params['is_added'];
        }

        if(!empty($params['id'])){
            $where .=" and a.content_id = ".$params['id'];
        }else if(!empty($params['type'])){
            $where .=" and b.type = '".$params['type']."'";
        }else if(!empty($params['level'])){
            $ratting_sets = config_item('ratting');
            $types_arr = $ratting_sets[$params['level']]['child'];
            $types_str = implode("','",array_keys($types_arr));
            $where .=" and b.type in ('".$types_str."')";
        }
        $total = $this->db->query("select count(1) as total from gg_rating as a left join gg_rating_content as b on a.content_id = b.id left join gg_rating_description as c on a.description_id = c.id where ".$where." order by a.status asc,a.addtime asc");
        $total_num = $total->row()->total;
        $sql = "select a.*,b.content,c.level from gg_rating as a left join gg_rating_content as b on a.content_id = b.id left join gg_rating_description as c on a.description_id = c.id where ".$where." order by a.status asc,a.addtime asc";
        $limit = " LIMIT ".($page-1)*$pagesize.",".$pagesize;
        $sql .=  $limit;
        $result = $this->db->query($sql)->result_array();
        $rs['total'] = $total_num;
        $rs['list'] = $result;
        $rs['total_page'] = (int)(($total_num-1)/$pagesize + 1);
        $rs['page_html'] = $this->create_page($total_num, $pagesize);
        return $rs;
    }
    /**
     * 审核列表--确认操作---驳回操作
     */
    public function audit_confirm_reback($id,$data){
        $this->db->trans_start();
        $flag = true;
        $result = $this->db->query("select * from gg_rating where id = ".$id)->row_array();
        $status = $result['status'];
        if($status == 2){
            return '已经确认过，不能再操作';
        }
        if($status == 3){
            return '已经驳回，不能再操作';
        }
        $this->db->where(array('id'=>$id));
        $return = $this->db->update('gg_rating',$data);
        if(!$return){
            $flag = false;
        }
        $this->_write_log('audit_ratting','ratting',$this->db->last_query(),'审核列表--确认驳回单个操作');
        $date = substr($result['addtime'],0,7);
        if($data['status'] == 3){//驳回操作
            //评分人的加分剩余、减分剩余修改
            $last_grade = $this->get_last_grade($date,$result['rating_uid']);
            $grade_summary_data = array(
                'uid' => $result['rating_uid'],
                'real_name' => $result['rating_name'],
                'year' => intval(substr($result['addtime'],0,4)),
                'month' => intval(substr($result['addtime'],5,2)),
                'plus_last' => $last_grade['last_plus'],
                'minus_last' => $last_grade['last_minus']
                );
            $desc = '审核列表--驳回单个操作-引起'.$result['rating_name'].'plus_last、minus_last变化';
            $return = $this->update_grade_summary($result['rating_uid'],$grade_summary_data,$this->db,$desc);
            if(!$return){
                $flag = false;
            }
        }

        // else{//确认操作
        //     $arr = $this->get_new_grade($date,$result['rated_uid']);
        //     $arr['uid'] = $result['rated_uid'];
        //     $arr['real_name'] = $result['rated_name'];
        //     $arr['year'] = intval(substr($result['addtime'],0,4));
        //     $arr['month'] = intval(substr($result['addtime'],5,2));
        //     // $desc = '审核列表--确认单个操作-引起'.$result['rated_name'].'得分变化';
        //     // $return = $this->update_grade_summary($result['rated_uid'],$arr,$this->db,$desc);
        //     if(!$return){
        //         $flag = false;
        //     }
        // }
        $this->db->trans_complete();
        if($this->db->trans_status() === false || $flag === false){
            return '确认失败！';
        }else{
            return 1;
        }
    }
    /**
     * 审核列表--批量确认操作---批量驳回操作
     */
    public function audit_confirm_reback_all($id_str,$data){
       $this->db->trans_start();
       $flag = true;
       $count = $this->db->query("select count(1) as counts from gg_rating where id in (".$id_str.") and status != 1")->row()->counts; 
       if($count>0){
            return "存在已经审核过的记录，请刷新后，重新选择！";
       }
       $id_arr = explode(',',$id_str);
       $this->db->where_in('id',$id_arr);
       $return = $this->db->update('gg_rating',$data);
       if(!$return){
            $flag = false;
        }
       $this->_write_log('audit_ratting','ratting',$this->db->last_query(),'审核列表--确认驳回所有操作');
        $sql = "select * from gg_rating where id in (".$id_str.")";
        $query = $this->db->query($sql);
        $result = $query->result_array();
       if($data['status'] == 3){//驳回操作
           //评分人的加分剩余、减分剩余修改
            foreach($result as $k => $v){
                $date = substr($v['addtime'],0,7);
                $last_grade = $this->get_last_grade($date,$v['rating_uid']);
                $grade_summary_data = array(
                    'uid' => $v['rating_uid'],
                    'real_name' => $v['rating_name'],
                    'year' => intval(substr($v['addtime'],0,4)),
                    'month' => intval(substr($v['addtime'],5,2)),
                    'plus_last' => $last_grade['last_plus'],
                    'minus_last' => $last_grade['last_minus']
                );
                $desc = '审核列表--驳回多个操作-引起'.$v['rating_name'].'plus_last、minus_last变化';
                $return = $this->update_grade_summary($v['rating_uid'],$grade_summary_data,$this->db,$desc);
                if(!$return){
                    $flag = false;
                }
            }
        }

        // else{
        //     foreach($result as $k => $v){
        //         $date = substr($v['addtime'],0,7);
        //         $arr = $this->get_new_grade($date,$v['rated_uid']);
        //         $arr['uid'] = $v['rated_uid'];
        //         $arr['real_name'] = $v['rated_name'];
        //         $arr['year'] = intval(substr($v['addtime'],0,4));
        //         $arr['month'] = intval(substr($v['addtime'],5,2));
        //         $desc = '审核列表--确认多个操作-引起'.$v['rated_name'].'得分变化';
        //         $return = $this->update_grade_summary($v['rated_uid'],$arr,$this->db,$desc);
        //         if(!$return){
        //             $flag = false;
        //         }
        //     }
        // }
       $this->db->trans_complete();
       if($this->db->trans_status() === false || $flag === false){
            return '操作失败！';
        }else{
            return '操作成功！';
        }
    }

    //大于0，小于0的各自求和
    public function sum_array($arr){
        $plus = 0;
        $minus = 0;
        foreach($arr as $v){
            if($v > 0){
                $plus += intval($v);
            }else{
                $minus += intval($v);
            }
        }
        return array('plus' => $plus,'minus' => $minus);
    }

    //获取用户指定日期剩余分数
    public function  get_last_grade($date = '',$uid = ''){
        $this->load->model('rating_content_model');
        $uid = empty($uid)?$_SESSION['userinfo']['id']:$uid;
        $date = empty($date)?date('Y-m'):$date;

        $sql = "select sum(case when grade > 0 then `grade` else 0 end) as grade_plus, sum(case when grade < 0 then `grade` else 0 end) as grade_minus from gg_rating where rating_uid = ".$uid." and status in (1,2) and substr(`addtime`,1,7) = '".$date."'";
        $query = $this->db->query($sql);
        //获取本月给别人加了多少分
        $grade_plus = $query->row()->grade_plus;
        //获取本月给别人减了多少分
        $grade_minus = $query->row()->grade_minus;

        $uinfo = $this->rating_content_model->get_user_info_by_uid($uid);
        $is_manage = $uinfo['is_manage'];
        //获取本月的基数设置
        $params['year'] = intval(substr($date,0,4));
        $params['month'] = intval(substr($date,5,2));
        $grade_set = $this->get_grade_set($params['year'],$params['month']);
        $arr = array();
        if($is_manage == 1){//是管理人员
            $arr['last_plus'] = $grade_set['manage_plus'] - $grade_plus;
            $arr['last_minus'] = $grade_set['manage_minus'] + $grade_minus;
        }else{//普通人员
            $arr['last_plus'] = $grade_set['common_plus'] - $grade_plus;
            $arr['last_minus'] = $grade_set['common_minus'] + $grade_minus;
        }
        return $arr;
    }
        //获取用户指定日期的分数
    public function  get_new_grade($date = '',$uid = ''){
        $rating_set = config_item('ratting');
        $performance = array_keys($rating_set[1]['child']);
        $performance = implode("','",$performance);
        $behavior = array_keys($rating_set[2]['child']);
        $behavior = implode("','",$behavior);
        $uid = empty($date)?$_SESSION['userinfo']['id']:$uid;
        $date = empty($date)?date('Y-m'):$date;

        //业绩
        $sql = "select sum(case when grade > 0 then `grade` else 0 end) as plus, sum(case when grade < 0 then `grade` else 0 end) as minus from gg_rating where rated_uid = ".$uid." and status = 2 and is_added = 1 and substr(`addtime`,1,7) = '".$date."' and type in ('".$performance."')";
        $query = $this->db->query($sql);
        $performance_arr = $query->row_array();
        //行为
        $sql = "select sum(case when grade > 0 then `grade` else 0 end) as plus, sum(case when grade < 0 then `grade` else 0 end) as minus from gg_rating where rated_uid = ".$uid." and status = 2 and is_added = 1 and substr(`addtime`,1,7) = '".$date."' and type in ('".$behavior."')";
        $query = $this->db->query($sql);
        $behavior_arr = $query->row_array();
        $arr = array(
            'performance_score' => $performance_arr['plus'] + $performance_arr['minus'],
            'behavior_score' => $behavior_arr['plus'] + $behavior_arr['minus'],
            'plus' => $performance_arr['plus'] * $rating_set[1]['percent'] + $behavior_arr['plus'] * $rating_set[2]['percent'],
            'minus' => $performance_arr['minus'] * $rating_set[1]['percent'] + $behavior_arr['minus'] * $rating_set[2]['percent'],
            'performance_plus' => $performance_arr['plus'],
            'performance_minus' => $performance_arr['minus'],
            'behavior_plus' => $behavior_arr['plus'],
            'behavior_minus' => $behavior_arr['minus']
            );
        $arr['grade'] = $arr['plus'] + $arr['minus'];
        return $arr;
    }
    /**
     *计算从用户从开始评分以来，总得分
     */
    public function get_user_total_grade($uid){
        $sql = "select sum(grade) as total_grade from gg_ratting_grade_summary where uid = ".$uid;
        $query = $this->db->query($sql);
        return $query->row()->total_grade;
    }
    /**
     *多维数组的排序
     */
    public function multi_array_sort($arr, $keys, $type = 'asc') {
        $keysvalue = $new_array = array();
        foreach ($arr as $k => $v) {
            $keysvalue[$k] = $v[$keys];
        }
        if ($type == 'asc') {
            asort($keysvalue);
        } else {
            arsort($keysvalue);
        }
            reset($keysvalue);
        foreach ($keysvalue as $k => $v) {
            $new_array[] = $arr[$k];
        }
        return $new_array;
    }
    /**
    * @desc 我的评分列表
    */
    public function get_my_ratting_list($params, $page = 1, $pagesize = 50){
        $this->load->model('department_model');
        $page = (int)$page < 1 ? 1 : (int)$page;
        $where = "where 1 ";
        $where .=" and rating_uid = ".$_SESSION['userinfo']['id'];
        if(!empty($params['rated_name'])){
            $where .= " and rated_name like '%".$params['rated_name']."%'";
        }
        if(!empty($params['begin'])){
            $where .=" and addtime >= '".$params['begin']." 00:00:00'";
        }
        if(!empty($params['end'])){
            $where .=" and addtime <= '".$params['end']." 23:59:59'";
        }
        if(!empty($params['status'])){
            $where .= " and status = ".$params['status'];
        }
        if(!empty($params['is_added'])){
            if($params['is_added'] == 1){
               $params['is_added'] = 0; 
            }else if($params['is_added'] == 2){
                $params['is_added'] = 1; 
            }
            $where .= " and is_added = ".$params['is_added'];
        }
         if(!empty($params['level'])){
            $where .= " and level = '".$params['level']."'";
        }

        if(!empty($params['department_id'])){
            $all_child_id = $this->department_model->get_all_parent_id($params['department_id']);
            $ids = '';
            foreach($all_child_id as $key=>$val){
                $ids .= $val.',';
            }
            $ids = trim($ids,',');
            $where .= "  AND b.`department_id` IN (".$ids.")";
        }

        $total = $this->db->query("select count(1) as total from gg_rating as a left join gg_user as b on a.rated_uid = b.id ".$where);
        $total_num = $total->row()->total;
        $sql = "select a.* from gg_rating as a left join gg_user as b on a.rated_uid = b.id ".$where." order by a.status desc,a.addtime desc";
        $limit = " LIMIT ".($page-1)*$pagesize.",".$pagesize;
        $sql .=  $limit;
        $result = $this->db->query($sql)->result_array();
        $rs['total'] = $total_num;
        $rs['list'] = $result;
        $rs['total_page'] = (int)(($total_num-1)/$pagesize + 1);
        $rs['page_html'] = $this->create_page($total_num, $pagesize);
        return $rs;
    }
    /**
     *我的评分列表---删除
     */
    public function myratting_del($id){
        $this->db->trans_start();
        $flag = true;
        $result = $this->db->query("select * from gg_rating where id = ".$id)->row_array();
        $status = $result['status'];
        if($status == 2){
            return '已通过审核，不能删除';
        }
        $return = $this->db->delete('gg_rating', array('id' => $id,'rating_uid' => $_SESSION['userinfo']['id']));
        if(!$return){
            $flag = false;
        }
        $this->_write_log('del_ratting','ratting',$this->db->last_query(),'我的评分列表--删除操作');
        if($status == 1){//待审核状态的
            //评分人的加分剩余、减分剩余修改
            $date = substr($result['addtime'],0,7);
            $last_grade = $this->get_last_grade($date,$result['rating_uid']);
            $grade_summary_data = array(
                'uid' => $result['rating_uid'],
                'real_name' => $result['rating_name'],
                'year' => intval(substr($result['addtime'],0,4)),
                'month' => intval(substr($result['addtime'],5,2)),
                'plus_last' => $last_grade['last_plus'],
                'minus_last' => $last_grade['last_minus']
                );
            $desc = '我的评分列表--删除待审核状态记录操作-引起'.$result['rating_name'].'plus_last、minus_last变化';
            $return = $this->update_grade_summary($result['rating_uid'],$grade_summary_data,$this->db,$desc);
            if(!$return){
                $flag = false;
            }
        }
        $this->db->trans_complete();
        if($this->db->trans_status() === false || $flag === false){
            return 0;
        }else{
            return 1;
        }
    }
    /**
     *我的评分列表---修改
     */
    public function update_ratting_single($id,$data){
        $this->db->trans_start();
        $flag = true;
        $result = $this->db->query("select * from gg_rating where id = ".$id)->row_array();
        $status = $result['status'];
        if($status == 2){
            return '已通过审核，不能修改';
        }
        $this->db->where('id', $id);
        $return = $this->db->update('gg_rating', $data);
        if(!$return){
            $flag = false;
        }
        $this->_write_log('modify_ratting','ratting',$this->db->last_query(),'我的评分列表--修改操作');
        //if($status == 3){//已经驳回的
            //评分人的加分剩余、减分剩余修改
            $date = substr($result['addtime'],0,7);
            $last_grade = $this->get_last_grade($date,$result['rating_uid']);
            $grade_summary_data = array(
                'uid' => $result['rating_uid'],
                'real_name' => $result['rating_name'],
                'year' => intval(substr($result['addtime'],0,4)),
                'month' => intval(substr($result['addtime'],5,2)),
                'plus_last' => $last_grade['last_plus'],
                'minus_last' => $last_grade['last_minus']
                );
            $desc = '我的评分列表--修改操作-引起'.$result['rating_name'].'plus_last、minus_last变化';
            $return = $this->update_grade_summary($result['rating_uid'],$grade_summary_data,$this->db,$desc);
            if(!$return){
                $flag = false;
            }
        //}
        $this->db->trans_complete();
        if($this->db->trans_status() === false || $flag === false){
            return 0;
        }else{
            return 1;
        }
    }

    public function get_ratting_by_id($id){
        $sql = "select * from gg_rating where id=".$id;
        $query = $this->db->query($sql);
        $result = $query->row_array();
        return $result;
    }



    /**
    * @desc 部门平均分统计
    */
    public function department_rate_statistics($params){
        $where = ' AND status=2 and is_added = 1';
        if($params['begindate'] != ''){
            $params['begindate'] = $params['begindate'].' 00:00:00';
            $where .= " AND addtime >= '".$params['begindate']."'";
        }
        if($params['enddate'] != ''){
            $params['enddate'] = $params['enddate'].' 23:59:59';
            $where .= " AND addtime <= '".$params['enddate']."'";
        }
        $this->load->model('department_model');
        $dpts = $this->db->get_where(TBL_DEPARTMENT, array('parent_id'=>0))->result();
        $result = array();
        foreach ($dpts as $key => $val) {
            $dpt_ids = $this->department_model->get_child_ids($val->id);
            $sql = "SELECT GROUP_CONCAT(id) AS uids FROM ".TBL_USER." WHERE department_id IN (".join(',', $dpt_ids).")";
            $user_ids = $this->db->query($sql)->row();
            $user_ids = $user_ids->uids;
            if($user_ids){
                $users_amounts = count(explode(',', $user_ids));//用户数量
                $sql1 = "SELECT sum(case when type='quality' then `grade`*0.6 else `grade`*0.4 end) as grades from gg_rating where rated_uid in (".$user_ids.") ".$where;
                $grades = $this->db->query($sql1)->row();
                $grades = $grades->grades;
                $result[$key]['id'] = $val->id;
                $result[$key]['deptname'] = $val->name;
                $result[$key]['grades'] = round((!empty($grades)?$grades:0)/$users_amounts,2);
            }else{
                $result[$key]['id'] = $val->id;
                $result[$key]['deptname'] = $val->name;
                $result[$key]['grades'] = 0;
            }
            
        }
        $result = $this->multi_array_sort($result,'grades','desc');
        return $result;
    }


    /**
    * @desc 某个部门下人员的评分统计
    */
    public function deptuser_statistics($params){
        $where = ' AND status=2 and is_added = 1';
        if($params['begindate'] != ''){
            $params['begindate'] = $params['begindate'].' 00:00:00';
            $where .= " AND addtime >= '".$params['begindate']."'";
        }
        if($params['enddate'] != ''){
            $params['enddate'] = $params['enddate'].' 23:59:59';
            $where .= " AND addtime <= '".$params['enddate']."'";
        }

        $this->load->model('department_model');
        $dpt_ids = $this->department_model->get_child_ids($params['department_id']);
        $sql = "SELECT id,real_name FROM ".TBL_USER." WHERE department_id IN (".join(',', $dpt_ids).")";
        $users = $this->db->query($sql)->result();
        $result = array();
        if(!empty($users)){
            foreach ($users as $key => $val) {
                $sql = "SELECT sum(case when type='quality' then `grade`*0.6 else `grade`*0.4 end) as grades from gg_rating where rated_uid={$val->id} ".$where;
                $grade = $this->db->query($sql)->row();
                if($grade->grades != 0){
                    $result[$key]['user'] = $val->real_name;
                    $result[$key]['grades'] = $grade->grades;
                }
            }
        }
        $result = $this->multi_array_sort($result,'grades','desc');
        return $result;
    }



    public function get_department_name($department_id){
        $sql = "SELECT name from gg_department where id = {$department_id}";
        $name = $this->db->query($sql)->row();
        return $name->name;
    }

    /**
    * @desc 统计个人得分
    */
    public function get_my_rattings($params){
        $where = ' status=2 and is_added = 1';
        if($params['user']!=''){
            $where .= " and (rated_name = '{$params['user']}' or rated_account = '{$params['user']}')";
        }else{
            return array();
        }
        if($params['begindate'] != ''){
            $params['begindate'] = $params['begindate'].' 00:00:00';
            $where.=" and addtime >= '{$params['begindate']}' ";

        }
        if($params['enddate'] != ''){
            $params['enddate'] = $params['enddate'].' 23:59:59';
            $where.=" and addtime <= '{$params['enddate']}' ";

        }
        if($params['begindate'] != ''||$params['enddate'] != ''){
            $sql = "SELECT YEAR(addtime) AS years,MONTH(addtime) AS mon,sum(case when type='quality' then `grade`*0.6 else `grade`*0.4 end) AS grades FROM gg_rating WHERE {$where} GROUP BY years,mon ORDER BY id DESC";
        }else{
            $sql = "SELECT YEAR(addtime) AS years,MONTH(addtime) AS mon,sum(case when type='quality' then `grade`*0.6 else `grade`*0.4 end) AS grades FROM gg_rating WHERE {$where} GROUP BY years,mon ORDER BY id DESC LIMIT 6";
        }
        return $this->db->query($sql)->result_array();
    }

    /**
     * 记录日志
     * @access public
     * @param string $filename 日志文件名称,系统会自动在文件名称前加上当前日期  这里一般设为action名
     * @param string $dirname 日志存放目录名称 一般设为controller名
     * @param string $log_msg 日志内容
     */
    public function _write_log($filename,$dirname,$log_msg='',$content){
        $url = $_SERVER['REQUEST_URI'];
        $log_path = 'ratting_log/';       //日志根目录
        $month = date('Y_m');           //当前月份
        $day = date('Y_m_d');           //当前日期
        $now = date('Y-m-d H:i:s');
        $log_no = time();
        
        if( empty($dirname) ){
            $dirname = 'undefined';
        }
        if( empty($filename) ){
            $filename = 'undefined';
        }
        if(is_dir($log_path.$month) == false){
            mkdir($log_path.$month, 0777,recursive);
        }
        if(is_dir($log_path.$month.'/'.$dirname) == false){
            mkdir($log_path.$month.'/'.$dirname, 0777,recursive);
        }
        
        $log_content = "编号:{$log_no}\n";
        $log_content .= "登录名:{$_SESSION['userinfo']['account']}\n";
        $log_content .= "真实姓名:{$_SESSION['userinfo']['real_name']}\n";
        $log_content .= "[时间]{$now}\t[url]{$url}\t[日志内容]\n";
        $log_content .= "操作内容:{$content}\n";
        $log_content .= "操作数据：".print_r($log_msg,true)."\n\n";
        $log_content .= "--------- End({$log_no}) ---------\n\n";
        
        $path = $log_path.$month.'/'.$dirname.'/'.$day.'_'.$filename.'.txt';
        file_put_contents($path,$log_content,FILE_APPEND);
    }
}

?>