<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$config['email'] = array(
	'protocol' => 'smtp',
	'smtp_host' => 'smtp.263.net',
	'smtp_port' => 25,
	'smtp_user' => 'liukai@rapoo.com',
	'smtp_pass' => 'Rapoo@123',
	'validate' => TRUE,
	'mailtype' => 'html',
	'priority' => 1,
	'crlf' => '\r\n',
	'charset' => 'UTF-8',
	'wordwrap' => TRUE
);

$config['email_subjects'] = array(
	'story_wait_review' => '#name# 需求等待评审',
	'story_review_ok' => '#name# 需求已经评审通过',
	'story_finished' => '#name# 需求已完成，等待关闭',
	'task_assigined' => '#name# 任务已下达，等待完成',
	'task_wait_test_review' => '#name# 任务等待测试评审',
	'task_wait_test' => '#name# 任务等待测试',
	'task_wait_online' => '#name# 任务等待上线',
	'task_wait_close' => '#name# 任务等待关闭',
	'bug_wait_process' => '#name# BUG等待处理',
	'bug_resolved' => '#name# BUG已经处理，等待关闭',
	'overtime_wait_review' => '#name# 加班任务等待审核',
	'overtime_review_ok' => '#name# 加班任务已审核通过',
	'overtime_review_reject' => '#name# 加班任务已驳回'
);