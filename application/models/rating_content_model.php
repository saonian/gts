<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rating_content_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('rating_model');
    }
    /**
     *@desc 应该找到最近的设置是哪一年那一月的
     *@param $params['year'] int 年; $params['month'] int 月
     *@return array
     */
    public function search_month($param){
        //查本月的数据有没有
        $sql = "select year,month from gg_rating_content where year = ".$param['year']." and month = ".$param['month'];
        $query = $this->db->query($sql);
        $result = $query->row_array();
        if(empty($result)){//没有，就取得最近的月份
            $sql = "select year,month from gg_rating_content where year = ".$param['year']." and month < ".$param['month']." order by year desc,month desc";
            $query = $this->db->query($sql);
            $result = $query->row_array();
            if(empty($result)){
                $sql = "select year,month from gg_rating_content where year < ".$param['year']." order by year desc,month desc";
                $query = $this->db->query($sql);
                $result = $query->row_array();
            }
        }
        return $result;
    }
    /**
     *@desc 获取某一年某一月的设置
     *@param $year int 年; $month int 月
     *@return array
     */
    // public function get_setting($year,$month){
    // 	$sql = "select * from gg_rating_content where year = ".$year." and month = ".$month." order by type";
    // 	$query = $this->db->query($sql);
    // 	$result = $query->result_array();
    // 	$arr = array();
    // 	foreach ($result as $k => $v) {
    // 		$sql = "select * from gg_rating_description where content_id = ".$v['id'];
    // 		$query = $this->db->query($sql);
    // 		$v['description'] = $query->result_array();
    // 		$arr[$v['type']][] = $v;
    // 	}
    // 	return $arr;
    // }
    /**
     *@desc 获取某一年某一月的设置
     *@param $year int 年; $month int 月
     *@return array
     */
    // public function get_setting_search($param){
    //     $where = " where year = ".$param['year']." and month = ".$param['month'];
    //     if(!empty($param['id'])){
    //         $where .=" and id = ".$param['id'];
    //     }else if(!empty($param['type'])){
    //         $where .=" and type = '".$param['type']."'";
    //     }else if(!empty($param['level'])){
    //         $ratting_sets = config_item('ratting');
    //         $types_arr = $ratting_sets[$param['level']]['child'];
    //         $types_str = implode("','",array_keys($types_arr));
    //         $where .=" and type in ('".$types_str."')";
    //     }
    //     $sql = "select * from gg_rating_content ".$where;
    //     $query = $this->db->query($sql);
    //     $result = $query->result_array();
    //     foreach($result as $k => $v){
    //         $sql = "select * from gg_rating_description where content_id = ".$v['id'];
    //         $query = $this->db->query($sql);
    //         $result[$k]['description'] = $query->result_array();
    //     }
    //     return $result;
    // }
    public function get_setting_search($param){
        $ratting_sets = config_item('ratting');
        $where = " where year = ".$param['year']." and month = ".$param['month'];
        if(!empty($param['id'])){
            $where .=" and id = ".$param['id'];
        }else if(!empty($param['type'])){
            $where .=" and type = '".$param['type']."'";
        }else if(!empty($param['level'])){
            $types_arr = $ratting_sets[$param['level']]['child'];
            $types_str = implode("','",array_keys($types_arr));
            $where .=" and type in ('".$types_str."')";
        }
        if(!empty($param['level'])){
            foreach ($ratting_sets as $key => $value) {
                if($param['level'] != $key){
                    unset($ratting_sets[$key]);
                }
            }
        }
        foreach ($ratting_sets as $key => $value){
            foreach ($value['child'] as $k => $v) {
                $sql = "select id,content,type from gg_rating_content ".$where." and type = '".$k."' order by id asc";
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
     *@desc 查找指定年月是否有评分设置记录
     *@param $params['year'] int 年; $params['month'] int 月
     *@return array
     */
    public function get_setting_by($param){
        $where = "where year = ".$param['year']." and month = ".$param['month'];
        if(!empty($param['type'])){
            $where .=" and type='".$param['type']."'";
        }
        $sql="select * from gg_rating_content ".$where." order by addtime desc";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    /**
     *@desc 根据id获取子表记录
     *@param $id 主键
     *@return array
     */
    public function get_description_by_id($id){
        $sql ="select * from gg_rating_description where id = ".$id;
        $query = $this->db->query($sql);
        return $query->row_array();
    }
    /**
     *@desc 根据id获取主表记录
     *@param $id 主键
     *@return array
     */
    public function get_content_by_id($id){
        $sql ="select * from gg_rating_content where id = ".$id;
        $query = $this->db->query($sql);
        return $query->row_array();
    }
    /**
     *@desc 根据id获取主表以及字表记录
     *@param $id 主键
     *@return array
     */
    public function get_ratset_by_content_id($id){
        $sql ="select a.content,b.* from gg_rating_content as a left join gg_rating_description as b on a.id = b.content_id  where a.id = ".$id;
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    /**
     *@desc 根据content_id获取子表内容
     *@param $id int ; 
     *@return array
     */
    public function get_description_by_content_id($id){
        $sql ="select * from gg_rating_description where content_id = ".$id;
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    /**
     *@desc 删除某项评分设置
     *@param $id int 评分设置id; $db resource 数据库连接
     *@return bool
     */
    public function ratting_sets_del($id,$db){
        $flag1 = $db->delete('gg_rating_description', array('content_id' => $id));//删除子表
        //添加日志
        $this->rating_model->_write_log('modify_content_next_month','set_ratting',$db->last_query(),'评分设置--设定评分项目--修改下月操作--删除子表');

        $flag2 = $db->delete('gg_rating_content', array('id' => $id));//删除主表
        //添加日志
        $this->rating_model->_write_log('modify_content_next_month','set_ratting',$db->last_query(),'评分设置--设定评分项目--修改下月操作--删除主表');
        if($flag1 && $flag2){
            return true;
        }else{
            return false;
        }
    }
    /**
     *@desc 根据uid查询用户信息
     *@param $uid int 
     *@return array
     */
    public function get_user_info_by_uid($uid){
        $query = $this->db->query('select * from gg_user where id = '.$uid);
        return $query->row_array();
    }
    /**
     *@desc 根据uid查询用户信息
     *@param $uid int 
     *@return array
     */
    public function get_user_info_by_name($name){
        $query = $this->db->query("select * from gg_user where real_name = '".$name."' or account = '".$name."'");
        return $query->row_array();
    }
    /**
     *@desc 删除某项评分设置字表的设置
     *@param $id int 字表评分设置id; $db resource 数据库连接
     *@return bool
     */
    public function ratting_sets_description_del($id,$db){
        $flag = $db->delete('gg_rating_description', array('id' => $id));//删除子表
        if($flag){
            //添加日志
            $this->rating_model->_write_log('modify_content_next_month','set_ratting',$db->last_query(),'评分设置--设定评分项目--修改下月操作--删除子表');
            return true;
        }else{
            return false;
        }
    }
    public function add_content(){
        $ratting_type = config_item('ratting_type');
        $flag = true;
        $year = intval(date('Y'));
        $month = intval(date('m'));

        $this->db->trans_start();

        //先查本月的有木有,没有则添加
        $result = $this->get_setting_by(array('year'=>$year,'month'=>$month));
        $data = array(
            'year' => $year,
            'month' => $month,
            'added_by'=> $_SESSION['userinfo']['real_name'],
            'addtime'=> date('Y-m-d H:i:s')
            );
        $data_desc = array(
            'added_by'=>$_SESSION['userinfo']['real_name'],
            'addtime'=>date('Y-m-d H:i:s')
            );
        if(empty($result)){
            //添加日志
            $this->rating_model->_write_log('add_content','set_ratting',$_POST,'评分设置--设定评分项目--新增本月操作');
            foreach($ratting_type as $key => $value) {
                $content = $this->input->post($value.'_content');
                $level = $this->input->post($value.'_level');
                $desc = $this->input->post($value.'_desc');
                $start_value = $this->input->post($value.'_start_value');
                $end_value = $this->input->post($value.'_end_value');
                $review_required = $this->input->post($value.'_reviews_required');
                $setting_id = $this->input->post('setting_'.$value.'_id');
                $setting_description_id = $this->input->post('setting_'.$value.'_description_id');
                foreach($content as $k => $v){
                    $data['content'] = $v;
                    $data['type'] = $value;
                    $return = $this->db->insert('gg_rating_content',$data);
                    $this->rating_model->_write_log('add_content','set_ratting',$this->db->last_query(),'评分设置--设定评分项目--新增本月操作--主表');
                    if(!$return){
                        $flag = false;
                    }
                    $content_id = $this->db->insert_id();
                    $data_desc['content_id'] = $content_id;
                    while(!empty($desc[$k]) || !empty($start_value[$k]) || !empty($end_value[$k]) || !empty($review_required[$k])){
                        $data_desc['level'] = array_shift($level[$k]);
                        $data_desc['desc'] = array_shift($desc[$k]);
                        $data_desc['start_value'] = array_shift($start_value[$k]);
                        $data_desc['end_value'] = array_shift($end_value[$k]);
                        $data_desc['review_required'] = array_shift($review_required[$k]);
                        $return = $this->db->insert('gg_rating_description',$data_desc);
                        $this->rating_model->_write_log('add_content','set_ratting',$this->db->last_query(),'评分设置--设定评分项目--新增本月操作--子表');
                        if(!$return){
                            $flag = false;
                        }
                    }
                }
            }
        }else{//看看下个月的有木有，下个月的没有，则直接添加,有则不做操作
            if($month == 12){
                $month = 1;
                $year = $year + 1;
            }else{
                $month = $month + 1;
            }
            $data['month'] = $month;
            $data['year'] = $year;
            $result = $this->get_setting_by(array('year'=>$year,'month'=>$month));
            if(empty($result)){
                //添加日志
                $this->rating_model->_write_log('add_content','set_ratting',$_POST,'评分设置--设定评分项目--新增下月操作');
                foreach ($ratting_type as $key => $value) {//和上面的那个循环一样
                    $content = $this->input->post($value.'_content');
                    $desc = $this->input->post($value.'_desc');
                    $level = $this->input->post($value.'_level');
                    $start_value = $this->input->post($value.'_start_value');
                    $end_value = $this->input->post($value.'_end_value');
                    $review_required = $this->input->post($value.'_reviews_required');
                    $setting_id = $this->input->post('setting_'.$value.'_id');
                    $setting_description_id = $this->input->post('setting_'.$value.'_description_id');
                    foreach($content as $k => $v){
                        $data['content'] = $v;
                        $data['type'] = $value;
                        $return = $this->db->insert('gg_rating_content',$data);
                        $this->rating_model->_write_log('add_content','set_ratting',$this->db->last_query(),'评分设置--设定评分项目--新增下月操作--主表');
                        if(!$return){
                            $flag = false;
                        }
                        $content_id = $this->db->insert_id();
                        $data_desc['content_id'] = $content_id;
                        while(!empty($desc[$k]) || !empty($start_value[$k]) || !empty($end_value[$k]) || !empty($review_required[$k])){
                            $data_desc['level'] = array_shift($level[$k]);
                            $data_desc['desc'] = array_shift($desc[$k]);
                            $data_desc['start_value'] = array_shift($start_value[$k]);
                            $data_desc['end_value'] = array_shift($end_value[$k]);
                            $data_desc['review_required'] = intval(array_shift($review_required[$k]));
                            $return = $this->db->insert('gg_rating_description',$data_desc);
                            $this->rating_model->_write_log('add_content','set_ratting',$this->db->last_query(),'评分设置--设定评分项目--新增下月操作--子表');
                            if(!$return){
                                $flag = false;
                            }
                        }
                    }
                }
            }
        }
        $this->db->trans_complete();
        if($this->db->trans_status() === false || $flag === false){
            return false;
        }else{
            return true;
        }
    }

    public function set_array($arr){
        $arr_new = array();
        foreach ($arr as $key => $value) {
            $arr_new[] = $value;
        }
        return $arr_new;
    }
    public function modify_content_next_month(){
        $ratting_type = config_item('ratting_type');
        $flag = true;
        $year = intval($this->input->post('year'));
        $month = intval($this->input->post('month'));
        $data = array(
            'year' => $year,
            'month' => $month,
            'added_by'=> $_SESSION['userinfo']['real_name'],
            'addtime'=> date('Y-m-d H:i:s')
            );
        $data_desc = array(
            'added_by'=>$_SESSION['userinfo']['real_name'],
            'addtime'=>date('Y-m-d H:i:s')
            );
        $this->db->trans_start();
        $result = $this->get_setting_by(array('year'=>$year,'month'=>$month));
         // if(!empty($result)){//如果已经有了，就删了再插入
         //     foreach($result as $k => $v) {
         //        $return = $this->ratting_sets_del($v['id'],$this->db);
         //         if(!$return){
         //             $flag = false;
         //         }
         //     }
         // }
        if(empty($result)){
            $this->rating_model->_write_log('modify_content_next_month','set_ratting',$_POST,'评分设置--设定评分项目--新增下月操作');
            foreach ($ratting_type as $key => $value) {//和上面的那个循环一样
                $content = $this->input->post($value.'_content');
                $desc = $this->input->post($value.'_desc');
                $level = $this->input->post($value.'_level');
                $start_value = $this->input->post($value.'_start_value');
                $end_value = $this->input->post($value.'_end_value');
                $review_required = $this->input->post($value.'_reviews_required');
                $setting_id = $this->input->post('setting_'.$value.'_id');
                $setting_description_id = $this->input->post('setting_'.$value.'_description_id');
                foreach($content as $k => $v){
                    $data['content'] = $v;
                    $data['type'] = $value;
                    $return = $this->db->insert('gg_rating_content',$data);
                    $this->rating_model->_write_log('modify_content_next_month','set_ratting',$this->db->last_query(),'评分设置--设定评分项目--新增下月操作--主表');
                    if(!$return){
                        $flag = false;
                    }
                    $content_id = $this->db->insert_id();
                    $data_desc['content_id'] = $content_id;
                    while(!empty($desc[$k]) || !empty($start_value[$k]) || !empty($end_value[$k]) || !empty($review_required[$k])){
                        $data_desc['level'] = array_shift($level[$k]);
                        $data_desc['desc'] = array_shift($desc[$k]);
                        $data_desc['start_value'] = array_shift($start_value[$k]);
                        $data_desc['end_value'] = array_shift($end_value[$k]);
                        $data_desc['review_required'] = intval(array_shift($review_required[$k]));
                        $return = $this->db->insert('gg_rating_description',$data_desc);
                        $this->rating_model->_write_log('modify_content_next_month','set_ratting',$this->db->last_query(),'评分设置--设定评分项目--新增下月操作--子表');
                        if(!$return){
                            $flag = false;
                        }
                    }
                }
            }
         // }
        }else{//有则修改，这个比较麻烦，有可能是新增，也可能是删除，还可能是修改
            $this->rating_model->_write_log('modify_content_next_month','set_ratting',$_POST,'评分设置--设定评分项目--修改下月操作');
            $new_data = array(
                'modified_by' => $_SESSION['userinfo']['real_name'],
                'modifytime' => date('Y-m-d H:i:s')
                );
            $new_data_desc = array(
                'modified_by' => $_SESSION['userinfo']['real_name'],
                'modifytime' => date('Y-m-d H:i:s')
                );

            foreach($ratting_type as $key => $value){
                $content = $this->input->post($value.'_content');
                $desc = $this->set_array($this->input->post($value.'_desc'));
                $level = $this->set_array($this->input->post($value.'_level'));
                $start_value = $this->set_array($this->input->post($value.'_start_value'));
                $end_value = $this->set_array($this->input->post($value.'_end_value'));
                $review_required = $this->set_array($this->input->post($value.'_reviews_required'));
                $setting_id = $this->input->post('setting_'.$value.'_id');
                $setting_description_id = $this->set_array($this->input->post('setting_'.$value.'_description_id'));
                //看看目前表里面都有哪些content_id
                $arr_content = $this->get_setting_by(array('year'=>$year,'month'=>$month,'type'=>$value));
                if(!empty($arr_content)){
                    foreach($arr_content as $ke => $va){
                        if(!in_array($va['id'],$setting_id)){//不在传过来的数组里面，则删除
                            $return = $this->ratting_sets_del($va['id'],$this->db);
                            if(!$return){
                                $flag = false;
                            }
                        }
                    }
                }
                //主表，剩下的就是修改和新增（有id传来的就是更新，没有id传来的就是添加）子表可能修改，可能新增，可能删除
                foreach($content as $k => $v){
                    $new_data['content'] = $v;
                    if(!empty($setting_id[$k])){//有，表示更新
                        $this->db->where(array('id'=>$setting_id[$k]));
                        $return = $this->db->update('gg_rating_content',$new_data);
                        $this->rating_model->_write_log('modify_content_next_month','set_ratting',$this->db->last_query(),'评分设置--设定评分项目--修改下月操作--主表更新记录');
                        $content_id = $setting_id[$k];
                    }else{
                        $data['type'] = $value;
                        $data['content'] = $v;
                        $return = $this->db->insert('gg_rating_content',$data);
                        $this->rating_model->_write_log('modify_content_next_month','set_ratting',$this->db->last_query(),'评分设置--设定评分项目--修改下月操作--主表插入记录');
                        $content_id = $this->db->insert_id();
                    }
                    if(!$return){
                        $flag = false;
                    }
                    $data_desc['content_id'] = $content_id;

                    $description_ids = $this->get_description_by_content_id($content_id);
                    if(!empty($description_ids)){
                        foreach ($description_ids as $ke => $va) {//不在传过来的数组里面，则删除
                            if(!in_array($va['id'],$setting_description_id[$k])){
                                $return = $this->ratting_sets_description_del($va['id'],$this->db);
                                if(!$return){
                                    $flag = false;
                                }
                            }
                        }
                    }
                    // $desc[$k] = $this->set_array($desc[$k]);
                    // $start_value[$k] = $this->set_array($start_value[$k]);
                    // $end_value[$k] = $this->set_array($end_value[$k]);
                    // $review_required[$k] = $this->set_array($review_required[$k]);
                    // $level[$k] = $this->set_array($level[$k]);

                    while(!empty($desc[$k]) || !empty($start_value[$k]) || !empty($end_value[$k]) || !empty($review_required[$k])){
                        $setting_description_id_single = array_shift($setting_description_id[$k]);
                        if(!empty($setting_description_id_single)){//有，表示更新
                            $new_data_desc['desc'] = array_shift($desc[$k]);
                            $new_data_desc['level'] = array_shift($level[$k]);
                            $new_data_desc['start_value'] = array_shift($start_value[$k]);
                            $new_data_desc['end_value'] = array_shift($end_value[$k]);
                            $new_data_desc['review_required'] = intval(array_shift($review_required[$k]));
                            $this->db->where(array('id'=>$setting_description_id_single));
                            $return = $this->db->update('gg_rating_description',$new_data_desc);
                            $this->rating_model->_write_log('modify_content_next_month','set_ratting',$this->db->last_query(),'评分设置--设定评分项目--修改下月操作--子表修改记录');
                        }else{
                            $data_desc['desc'] = array_shift($desc[$k]);
                            $data_desc['level'] = array_shift($level[$k]);
                            $data_desc['start_value'] = array_shift($start_value[$k]);
                            $data_desc['end_value'] = array_shift($end_value[$k]);
                            $data_desc['review_required'] = array_shift($review_required[$k]);
                            $return = $this->db->insert('gg_rating_description',$data_desc);
                            $this->rating_model->_write_log('modify_content_next_month','set_ratting',$this->db->last_query(),'评分设置--设定评分项目--修改下月操作--子表插入记录');
                        }
                        if(!$return){
                            $flag = false;
                        }
                    }
                }
            }
        }
        $this->db->trans_complete();
        if($this->db->trans_status() === false || $flag === false){
            return false;
        }else{
            return true;
        }
        
    }

}

?>