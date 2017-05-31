<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*数组key为控制器名*/
$config['menu'] = array(
	'my' => array(
		'display' => '我的视图',
		'url' => '/my'
	),
	'product' => array(
		'display' => '产品',
		'url' => '/product/story',
		'children' => array(
			'index' => array('display' => '产品', 'url' => '/product'),
			'story' => array('display' => '需求', 'url' => '/product/story'),
			'module' => array('display' => '模块', 'url' => '/module')
		)
	),
	'project' => array(
		'display' => '项目',
		'url' => '/project',
		'children' => array(
			'index' => array('display' => '项目', 'url' => '/project'),
			'story' => array('display' => '需求', 'url' => '/story?assignedtome=1'),
			'task' => array('display' => '任务', 'url' => '/task?assignedtome=1'),
			// 'testtask' => array('display' => '测试', 'url' => '/testtask?assignedtome=1'),
			// 'taskprizes' => array('display' => '积分任务', 'url' => '/taskprizes'),
			'bug' => array('display' => 'BUG', 'url' => '/bug?assignedtome=1'),
			'team' => array('display' => '团队', 'url' => '/team')
		)
	),
	'statistics' => array(
		'display' => '统计',
		'url' => '/statistics/overtime',
		'children' => array(
			'overtime'=>array('display'=>'加班统计','url'=>'/statistics/overtime'),
			'work'=>array('display'=>'工作统计','url'=>'/statistics/work'),
			'pro_story'=>array('display'=>'项目需求统计','url'=>'/statistics/pro_story','is_admin' => TRUE)
		)
	),
	'overtime' => array(
		'display' => '加班',
		'url' => '/overtime/index',
		'children' => array(
			'overtime'=>array('display'=>'加班管理','url'=>'/overtime/index'),
			// 'duty'=>array('display'=>'值班管理','url'=>'/overtime/duty')
		)
	),
    'grade' => array(
        'display' => '评分',
        'url' => '/grade',
        'children' => array(
            'setlist'=>array('display'=>'评价设置','url'=>'/grade/setlist'),
            'gradetasklist'=>array('display'=>'任务评分','url'=>'/grade/gradetasklist'),
            'gradestorylist'=>array('display'=>'需求评分','url'=>'/grade/gradestorylist'),
            'gradeadmin'=>array('display'=>'管理员评分','url'=>'/grade/gradeadmin','is_admin'=>TRUE)
        )
    ),
	'sys' => array(
		'display' => '组织',
		'url' => '/role',
		'is_admin' => TRUE,
		'children' => array(
			'role' => array('display'=>'角色管理','url'=>'/role'),
			'account' => array('display'=>'用户管理','url'=>'/account'),
			'department' => array('display'=>'部门管理','url'=>'/department'),
		)
	),
	'ratting' => array(
        'display' => '能量评分',
        'url' => '/ratting/userlist',
        'children' => array(
            'userlist'=>array('display'=>'我要评分','url'=>'/ratting/userlist'),
            'personal_grade'=>array('display'=>'查看个人得分','url'=>'/ratting/personal_grade'),
            'rattinglist'=>array('display'=>'我的评分列表','url'=>'/ratting/rattinglist'),
            'infoset'=>array('display'=>'资料设置','url'=>'/ratting/infoset'),
            'rattingreport'=>array('display'=>'评分报表','url'=>'/ratting/rattingreport','is_manage'=>TRUE),
            'auditlist'=>array('display'=>'审核列表','url'=>'/ratting/auditlist','is_manage'=>TRUE),
            'ratsetting'=>array('display'=>'评分设置','url'=>'/ratting/ratsetting','is_manage'=>TRUE)
        )
    ),
);

//能力评分
$config['ratting'] = array(
	'1' => array(
		'title' => '个人工作业绩考核',
		'child' => array(
			'quality' => '大小项目完成质量及整体协作性'
			),
		'percent' => '0.6'
		),
	'2' => array(
		'title' => '周边行为考核',
		'child' => array(
			'attitude' => '协作与态度',
			'loyalty' => '忠诚度',
			'discipline' => '遵守纪律'
			),
		'percent' => '0.4'
		)
	);

//能力评分 类型
$config['ratting_type'] = array('quality','attitude','loyalty','discipline');

//能力评分 控制 管理员角色
$config['ratting_role'] = array('1','121');
// $config['ratting_role'] = array('121');

//地址设置
$config['upimg'] = array(
	'small_size' => 64,
	'big_size' => 160,
	'big_img' => "public/headimg/uploads/big160_",
	'small_img' => "public/headimg/uploads/small64_",
	'img' => "public/headimg/uploads/"
	);
	
//值班
$config['duty'] = array(
	array(
		'type'=>'web',
		'title'=>'网站',
		'desc'=>'ERP、PMS、WMS、ebay-ERP各系统访问及使用支持'		
	),
	array(
		'type'=>'sys',
		'title'=>'系统',
		'desc'=>'各网站异常问题手机反馈、客户异常问题反馈、网站功能使用支持'		
	),
);