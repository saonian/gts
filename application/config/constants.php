<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');

define('PAGE_SIZE', 20);

// 系统提示类型
define('ERROR', 'error');
define('INFO', 'info');
define('WARNING', 'warning');

// 表常量
define('TBL_PREFIX', 'gg_');
define('TBL_ACTION', TBL_PREFIX.'action');
define('TBL_AUTH_ASSIGNMENT', TBL_PREFIX.'auth_assignment');
define('TBL_AUTH_ITEM', TBL_PREFIX.'auth_item');
define('TBL_AUTH_ITEM_CHILD', TBL_PREFIX.'auth_item_child');
define('TBL_BUG', TBL_PREFIX.'bug');
define('TBL_DEPARTMENT', TBL_PREFIX.'department');
define('TBL_GRADE', TBL_PREFIX.'grade');
define('TBL_OVERTIME', TBL_PREFIX.'overtime');
define('TBL_PROJECT', TBL_PREFIX.'project');
define('TBL_PROJECT_TEAM', TBL_PREFIX.'project_team');
define('TBL_STORY', TBL_PREFIX.'story');
define('TBL_TASK', TBL_PREFIX.'task');
define('TBL_TESTTASK', TBL_PREFIX.'testtask');
define('TBL_USER', TBL_PREFIX.'user');
define('TBL_FILE', TBL_PREFIX.'file');
define('TBL_HISTORY', TBL_PREFIX.'history');
define('TBL_GRADE_SETTING', TBL_PREFIX.'grade_setting');
define('TBL_GRADE_DESCRIPTION', TBL_PREFIX.'grade_description');
define('TBL_GRADE_SCORE', TBL_PREFIX.'grade_score');
define('TBL_PRODUCT', TBL_PREFIX.'product');
define('TBL_PROJECT_PRODUCT', TBL_PREFIX.'project_product');
define('TBL_MODULE', TBL_PREFIX.'module');
define('TBL_TASK_PRIZES', TBL_PREFIX.'task_prizes');
define('TBL_DUTY', TBL_PREFIX.'duty');
/* End of file constants.php */
/* Location: ./application/config/constants.php */