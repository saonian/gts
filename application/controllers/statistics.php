<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Statistics extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('statistics_model');
	}

	public function index(){
		$this->overtime();
	}
	
	public function overtime(){
		$this->layout->view('statistics/stat_overtime_page');
	}

	public function get_chart_data($type = 1){
		$result = array();
		switch ((int)$type) {
			case 1:
				$result = $this->statistics_model->my_monthly_overtime_stat();
				break;
			default:
				$result = $this->statistics_model->dpt_overtime_stat();
				break;
		}
		echo json_encode($result);
	}

	public function get_my_overtime_monthly_stat(){
		$result = $this->statistics_model->my_monthly_overtime_stat();
		file_put_contents(APPPATH.'logs/log.txt', print_r($result,true)."\r\n",FILE_APPEND);
		echo json_encode($result);
	}

	public function get_dpt_overtime_stat(){
		$result = $this->statistics_model->dpt_overtime_stat();
		echo json_encode($result);
	}

	public function get_my_dpt_ranking(){
		$this->load->model('department_model');
		$result = $this->statistics_model->dpt_overtime_stat();
		$ranking = array();
		foreach ($result as $key => $val) {
			$dpt_id_arr = $this->department_model->get_child_ids($val['dpt_id']);
			if(in_array($_SESSION['userinfo']['department_id'], $dpt_id_arr)){
				$ranking = $val['detail'];
				break;
			}
		}
		echo json_encode($ranking);
	}
	
	public function exporting(){
		$begin_date = $this->input->get('begindate', TRUE);
		$end_date = $this->input->get('enddate', TRUE);
		$stattype = $this->input->get('stattype', TRUE);
		$overtimetype = $this->input->get('overtimetype', TRUE);
		if(!strtotime($begin_date) || !strtotime($end_date)){
			show_msg(ERROR, '请填写正确的日期');
		}
		$stat_type_arr = array(
			'my' => '我的月度',
			'dpt' => '部门'
		);
		$overtime_type_arr = array(
			'all' => '加班',
			'workday' => '工作日加班',
			'weekend' => '周末加班'
		);
		$overtimetype = in_array($overtimetype, array_keys($overtime_type_arr))?$overtimetype:'all';
		switch ($stattype) {
			case 'my':
				$result = $this->statistics_model->my_monthly_overtime_stat($begin_date, $end_date, $overtimetype);
				break;
			case 'dpt':
				$result = $this->statistics_model->dpt_overtime_stat($begin_date, $end_date, $overtimetype);
				$r = array();
				foreach ($result as $key => $val) {
					$r = array_merge($r, $val['detail']);
				}
				$result = $r;
				break;
		}
		$this->load->library('Excel/PHPExcel');
		$title = $begin_date.'到'.$end_date.$stat_type_arr[$stattype].$overtime_type_arr[$overtimetype];
		$excel = new PHPExcel();
		$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
		$excel->setActiveSheetIndex(0);
		$sheet = $excel->getActiveSheet();
		$sheet->setTitle($title);
		$sheet->mergeCells('A1:S1');
		$sheet->setCellValueByColumnAndRow(0, 1, $title);
		$sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$sheet->getStyle('A1')->getFont()->setBold(true);
		if($stattype == 'my'){
			$sheet->setCellValueByColumnAndRow(0, 2, '月份');
			$sheet->setCellValueByColumnAndRow(1, 2, '总加班时间(小时)');
			$sheet->getColumnDimension('B')->setWidth(18);
		}else if($stattype == 'dpt'){
			$sheet->setCellValueByColumnAndRow(0, 2, '姓名');
			$sheet->setCellValueByColumnAndRow(1, 2, '加班时间(小时)');
			$sheet->setCellValueByColumnAndRow(2, 2, '调休时间(小时)');
			$sheet->getColumnDimension('B')->setWidth(15);
			$sheet->getColumnDimension('C')->setWidth(15);
		}
		$row = 3;
		// print_r($result);exit;
		$total = 0;
		$total_days_off = 0;
		foreach ($result as $key => $val) {
			if($stattype == 'my'){
				$sheet->setCellValueByColumnAndRow(0, $row, $val->mon.'月');
				$sheet->setCellValueByColumnAndRow(1, $row, $val->total);
				$total += $val->total;
			}else if($stattype == 'dpt'){
				$sheet->setCellValueByColumnAndRow(0, $row, $val[0]);
				$sheet->setCellValueByColumnAndRow(1, $row, $val[1]);
				$sheet->setCellValueByColumnAndRow(2, $row, $val[2]);
				$total += $val[1];
				$total_days_off += $val[2];
			}
			$row++;
		}
		$sheet->mergeCells("A{$row}:B{$row}");
		$sheet->setCellValueByColumnAndRow(0, $row, "共计{$total}小时");
		$sheet->getStyleByColumnAndRow(0, $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$sheet->getStyleByColumnAndRow(0, $row)->getFont()->setBold(true);
		$sheet->setCellValueByColumnAndRow(2, $row, "共计{$total_days_off}小时");
		$sheet->getStyleByColumnAndRow(2, $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$sheet->getStyleByColumnAndRow(2, $row)->getFont()->setBold(true);
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
		header("Content-Type:application/force-download");
		header("Content-Type:application/vnd.ms-execl");
		header("Content-Type:application/octet-stream");
		header("Content-Type:application/download");
		header("Content-Disposition:attachment;filename=\"{$title}.xls\"");
		header("Content-Transfer-Encoding:binary");
		$writer->save('php://output');
	}

	public function work(){
		$begin_date = $this->input->post('begin_date');
		$end_date = $this->input->post('end_date');
		if((!$begin_date || !$end_date) || (!strtotime($begin_date) || !strtotime($end_date))){
			$begin_date = date('Y-m-01 00:00:00',time());
			$end_date = date('Y-m-t 23:59:59',time());
		}
		$data['begin_date'] = $begin_date;
		$data['end_date'] = $end_date;
		$data['result'] = $this->statistics_model->work_stat($begin_date, $end_date);
		$is_admin = is_admin();
		$data['is_admin'] = $is_admin || (!$is_admin && count($data['result'])>1);
		// print_r($data);exit;
		$this->layout->view('statistics/stat_work_page', $data);
	}

	public function pro_story(){
		$begin_date = $this->input->post('begin_date');
		$end_date = $this->input->post('end_date');
		if((!$begin_date || !$end_date) || (!strtotime($begin_date) || !strtotime($end_date))){
			$begin_date = date('Y-m-01 00:00:00',time());
			$end_date = date('Y-m-t 23:59:59',time());
		}
		$data['begin_date'] = $begin_date;
		$data['end_date'] = $end_date;
		$data['result'] = $this->statistics_model->pro_story_stat($begin_date, $end_date);
		$is_admin = is_admin();
		$data['is_admin'] = $is_admin || (!$is_admin && count($data['result'])>1);
		$this->layout->view('statistics/stat_pro_story_page', $data);
	}

	public function fix_story(){
		$result = array();
		$stories = $this->db->get_where(TBL_STORY, array('status'=>'closed','estimate'=>0))->result();
		foreach ($stories as $key => $val) {
			$actions = $this->db->get_where(TBL_ACTION, array('type'=>'story', 'object_id'=>$val->id, 'action'=>'closed'))->result();
			foreach ($actions as $v) {
				$histories = $this->db->get_where(TBL_HISTORY, array('action_id'=>$v->id,'field'=>'estimate','old >'=>0,'new'=>0))->result();
				// print_r($histories);exit;
				foreach ($histories as $h) {
					$this->db->where('id', $val->id);
					$this->db->update(TBL_STORY, array('estimate'=>$h->old));
					$result[] = $val->id;
				}
			}
		}
		echo '修复了需求ID为'.join(',', $result).'的预计工时';exit;
	}

	public function fix_task(){
		$result = array();
		$tasks = $this->db->query("SELECT id FROM ".TBL_TASK." WHERE ((status='submittest' AND need_test='0') OR (status='online' AND need_test='1') OR status='closed') AND consumed=0")->result();
		foreach ($tasks as $key => $val) {
			$actions = $this->db->query("SELECT * FROM ".TBL_ACTION." WHERE TYPE='task' AND ACTION IN ('submittest','online','finished') AND object_id={$val->id}")->result();
			foreach ($actions as $v) {
				$histories = $this->db->get_where(TBL_HISTORY, array('action_id'=>$v->id,'field'=>'consumed','old >'=>0,'new'=>0))->result();
				foreach ($histories as $h) {
					$this->db->where('id', $val->id);
					$this->db->update(TBL_TASK, array('consumed'=>$h->old));
					$result[] = $val->id;
				}
			}
		}
		echo '修复了任务ID为'.join(',', $result).'的消耗工时';exit;
	}
}	