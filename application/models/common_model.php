<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Common_model extends MY_Model {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * 下载文件并增加下载次数
	 * @param int 文件ID
	 * @return void
	 */
	public function download($file_id){
		$file = $this->db->get_where(TBL_FILE, array('id' => (int)$file_id))->row();
		if(!$file){
			return;
		}
		$target_path = realpath('./').$file->path;
		$target_path = str_replace('\\', '/', $target_path);
		// echo $target_path;exit;
		if (!file_exists($target_path)){
			header("Content-type: text/html; charset=utf-8");
			die('File not found!');
		}

		$file_size = filesize($target_path);
		$file_name = empty($file->title) ? basename($target_path) : $file->title.$file->extension;
		// 下载次数加一
		$this->db->query("UPDATE ".TBL_FILE." SET downloads=downloads+1 WHERE id={$file_id}");

		// header("Content-Transfer-Encoding: binary");
		header("Content-type: application/octet-stream");  
		header("Accept-Ranges: bytes");
		header("Accept-Length: ".$file_size);
		//处理中文文件名
	    $ua = $_SERVER["HTTP_USER_AGENT"];
	    $encoded_filename = rawurlencode($file_name);
	    if (preg_match("/MSIE/", $ua)) {
			header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
	    } else if (preg_match("/Firefox/", $ua)) {
			header("Content-Disposition: attachment; filename*=\"utf8''" . $file_name . '"');
	    } else {
			header('Content-Disposition: attachment; filename="' . $file_name . '"');
	    }
		ob_clean();
		readfile($target_path);
		ob_flush();
		exit;
	}

	/**
	 * 删除文件(只是标记为删除)并创建动作
	 * @param int 文件ID
	 * @return void
	 */
	public function delete_file($file_id){
		$fileinfo = $this->db->get_where(TBL_FILE, array('id' => (int)$file_id))->row();

		if($fileinfo){
			// 标记为删除
			$this->db->where(array('id' => (int)$file_id));
			$this->db->update(TBL_FILE, array('is_deleted' => '1'));

			$target_table = $this->pmsdata[$fileinfo->type]['table'];
			if(!empty($target_table)){
				$target = $this->db->get_where($target_table, array('id' => $fileinfo->object_id))->row();
				if($target){
					$action = array(
						'project_id' => $target->project_id,
						'object_id' => $target->id,
						'type' => $fileinfo->type,
						'action' => $this->pmsdata['story']['action']['deletedfile']['value'],
						'comment' => '删除了附件: '.$fileinfo->title
					);
					$this->create_action($action);
				}
			}
		}

		header("Location: {$_SERVER['HTTP_REFERER']}");
	}

	/**
	 * 移除文件(删除数据库记录并删除文件)
	 * @param int 文件ID
	 * @return void
	 */
	public function remove_file($file_id){
		$fileinfo = $this->db->get_where(TBL_FILE, array('id' => (int)$file_id))->row();
		if($fileinfo){
			$this->db->delete(TBL_FILE, array('id' => (int)$file_id));
			@unlink(realpath('./').$fileinfo->path);
		}
		header("Location: {$_SERVER['HTTP_REFERER']}");
	}

	/**
	 * 获取我的视图数据
	 * @return array 视图数据
	 */
	public function get_my_data(){
		//取出 ：非（已关闭的、还是草案的）或者 是草案 并且审核结果等于clarify、reject
		$data['my_story'] = $this->db->query("SELECT * FROM ".TBL_STORY." WHERE assigned_to={$this->current_user_id} AND is_deleted='0' AND (status NOT IN ('{$this->pmsdata['story']['status']['closed']['value']}','{$this->pmsdata['story']['status']['draft']['value']}') OR (status='{$this->pmsdata['story']['status']['draft']['value']}' AND reviewed_result<>'' AND reviewed_result<>'{$this->pmsdata['story']['reviewed_result']['pass']['value']}')) ORDER BY level DESC")->result();
		$this->db->order_by('level', 'DESC');
		// $data['my_verify_story'] = $this->db->get_where(TBL_STORY, array('reviewed_by' => $this->current_user_id, 'is_deleted' => '0', 'status' => $this->pmsdata['story']['status']['draft']['value']))->result();
		$data['my_verify_story'] = $this->db->query("SELECT * FROM ".TBL_STORY." WHERE reviewed_by = {$this->current_user_id} AND is_deleted='0' AND status = '{$this->pmsdata['story']['status']['draft']['value']}' AND reviewed_result <> '{$this->pmsdata['story']['reviewed_result']['reject']['value']}'")->result();
		$this->db->order_by('level', 'DESC');
		$data['my_task'] = $this->db->get_where(TBL_TASK, array('assigned_to' => $this->current_user_id, 'is_deleted' => '0', 'status <>' => $this->pmsdata['task']['status']['closed']['value']))->result();
		// $this->db->order_by('id', 'DESC');
		// $data['my_testtask'] = $this->db->get_where(TBL_TESTTASK, array('owner' => $this->current_user_id, 'is_deleted' => '0', 'status <>' => $this->pmsdata['testtask']['status']['done']['value']))->result();
		$this->db->order_by('last_edited_date', 'DESC');
		$data['my_bug'] = $this->db->get_where(TBL_BUG, array('assigned_to' => $this->current_user_id, 'is_deleted' => '0', 'status <>' => $this->pmsdata['bug']['status']['closed']['value']))->result();
		$sql_story = "SELECT g.id, s.name, s.project_id FROM ".TBL_GRADE." g JOIN ".TBL_STORY." s ON g.type='story' AND g.object_id=s.id AND g.grade_by={$this->current_user_id} AND s.is_deleted='0' AND g.is_graded='0' ORDER BY s.last_edited_date DESC";
		$sql_task = "SELECT g.id, t.name, t.project_id FROM ".TBL_GRADE." g JOIN ".TBL_TASK." t ON g.type='task' AND g.object_id=t.id AND g.grade_by={$this->current_user_id} AND t.is_deleted='0' AND g.is_graded='0' ORDER BY t.last_edited_date DESC";
		$data['my_story_grade'] = $this->db->query($sql_story)->result();
		$data['my_task_grade'] = $this->db->query($sql_task)->result();
		return $data;
	}
}