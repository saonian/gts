<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * pms下的KEY值使用控制器名字
 * powers下的KEY值使用控制器方法名
 */
$config['pms'] = array(
	'user' => array(
		'value' => 'user',
		'display' => '用户',
		'table' => TBL_USER,
		'actions' => array(
			'login' => array('value'=>'login','display'=>'登入'),
			'logout' => array('value'=>'logout','display'=>'登出')
		),
		'powers' => array(
/*			'read' => array('value'=>'READ_USER','display'=>'读取用户'),
			'create' => array('value'=>'ADD_USER','display'=>'创建用户'),
			'edit' => array('value'=>'EDIT_USER','display'=>'编辑用户'),
			'remove' => array('value'=>'REMOVE_USER','display'=>'删除用户'),
			'list' => array('value'=>'LIST_USER','display'=>'用户列表'),
			'detail' => array('value'=>'DETAIL_USER','display'=>'用户详情')*/
		)
	),
	'project' => array(
		'value' => 'project',
		'display' => '项目',
		'table' => TBL_PROJECT,
		'action' => array(
			'opened' => array('value'=>'opened','display'=>'创建项目'),
			'closed' => array('value'=>'closed','display'=>'关闭项目'),
			'canceled' => array('value'=>'canceled','display'=>'取消项目'),
			'resolved' => array('value'=>'resolved','display'=>'完成项目'),
            'edited' => array('value'=>'edited','display'=>'编辑项目'),
			'started' => array('value'=>'started','display'=>'开始项目'),
            'delayed' => array('value'=>'delayed','display'=>'延迟项目'),
            'hanged' => array('value'=>'hanged','display'=>'挂起')
		),
		'status' => array(
			'wait' => array('value'=>'wait','display'=>'未开始'),
			'doing' => array('value'=>'doing','display'=>'进行中'),
			'done' => array('value'=>'done','display'=>'已完成'),
			'closed' => array('value'=>'closed','display'=>'已关闭'),
			'delay' => array('value'=>'delay','display'=>'延迟'),
            'hang' => array('value'=>'hang','display'=>'挂起'),
            'delete' => array('value'=>'delete','display'=>'删除')
		),
		'powers' => array(
			'read' => array('value'=>'READ_PROJECT','display'=>'读取项目'),
			'create' => array('value'=>'ADD_PROJECT','display'=>'创建项目'),
			'edit' => array('value'=>'EDIT_PROJECT','display'=>'编辑项目'),
			'remove' => array('value'=>'REMOVE_PROJECT','display'=>'删除项目'),
			'page' => array('value'=>'LIST_PROJECT','display'=>'项目列表'),
			'view' => array('value'=>'VIEW_PROJECT','display'=>'项目详情'),
			'start' => array('value'=>'START_PROJECT','display'=>'开始项目'),
			'cancel' => array('value'=>'CANCEL_PROJECT','display'=>'取消项目'),
			'finish' => array('value'=>'FINISH_PROJECT','display'=>'完成项目'),
			'close' => array('value'=>'CLOSE_PROJECT','display'=>'关闭项目'),
            'delay' => array('value'=>'DELAY_PROJECT','display'=>'延期项目'),
            'hang' => array('value'=>'HANG_PROJECT','display'=>'挂起项目')
		)
	),
	'story' => array(
		'value' => 'story',
		'display' => '需求',
		'table' => TBL_STORY,
		'action' => array(
			'opened' => array('value'=>'opened','display'=>'创建需求'),
			'closed' => array('value'=>'closed','display'=>'关闭需求'),
			'canceled' => array('value'=>'canceled','display'=>'取消需求'),
			'resolved' => array('value'=>'resolved','display'=>'解决需求'),
			'edited' => array('value'=>'edited','display'=>'编辑需求'),
			'reviewed' => array('value'=>'edited','display'=>'评审需求'),
			'actived' => array('value'=>'actived','display'=>'激活需求'),
			'commented' => array('value'=>'commented','display'=>'备注需求'),
			'summary' => array('value'=>'summary','display'=>'填写任务心得'),
			'changed' => array('value'=>'changed','display'=>'变更需求'),
			'remark'=>array('value'=>'remark','display'=>'备注需求'),
			'assigned'=>array('value'=>'assigned','display'=>'指派需求'),
			'reviewed' => array('value'=>'reviewed','display'=>'审核需求'),
			'deleted' => array('value'=>'deleted','display'=>'删除需求'),
			'deletedfile' => array('value'=>'deletedfile','display'=>'删除附件')
		),
		'status' => array(
			'draft' => array('value'=>'draft','display'=>'草案','color'=>'blue'),
			'active' => array('value'=>'active','display'=>'激活','color'=>'red'),
			'finished' => array('value'=>'finished','display'=>'已完成','color'=>'green'),
			'closed' => array('value'=>'closed','display'=>'已关闭','color'=>'grey')
		),
		'sources' => array(
			'customer' => array('value'=>'customer','display'=>'客户'),
			'user' => array('value'=>'user','display'=>'用户'),
			'po' => array('value'=>'po','display'=>'产品经理'),
			'market' => array('value'=>'market','display'=>'市场'),
			'service' => array('value'=>'service','display'=>'客服'),
			'competitor' => array('value'=>'competitor','display'=>'竞争对手'),
			'partner' => array('value'=>'partner','display'=>'合作伙伴'),
			'dev' => array('value'=>'dev','display'=>'开发人员'),
			'tester' => array('value'=>'tester','display'=>'测试人员'),
			'bug' => array('value'=>'bug','display'=>'Bug'),
			'other' => array('value'=>'other','display'=>'其他')
		),
		'stages' => array(
			'wait' => array('value'=>'wait','display'=>'未开始'),
			'planned' => array('value'=>'planned','display'=>'已计划'),
			'projected' => array('value'=>'projected','display'=>'已立项'),
			'developing' => array('value'=>'developing','display'=>'开发中'),
			'developed' => array('value'=>'developed','display'=>'开发完毕'),
			'testing' => array('value'=>'testing','display'=>'测试中'),
			'tested' => array('value'=>'tested','display'=>'测试完毕'),
			'released' => array('value'=>'released','display'=>'已发布')
		),
		'close_reason' => array(
			'done' => array('value'=>'done','display'=>'已完成'),
			'subdivided' => array('value'=>'subdivided','display'=>'已细分'),
			'duplicate' => array('value'=>'duplicate','display'=>'重复'),
			'postponed' => array('value'=>'postponed','display'=>'延期'),
			'cancel' => array('value'=>'cancel','display'=>'已取消'),
			'willnotdo' => array('value'=>'willnotdo','display'=>'不做')
		),
		'reviewed_result' => array(
			'pass' => array('value'=>'pass','display'=>'确认通过'),
			'clarify' => array('value'=>'clarify','display'=>'有待明确'),
			'reject' => array('value'=>'reject','display'=>'拒绝')
		),
		'quality' => array(
			'good' => array('value'=>'good','display'=>'好'),
			'average' => array('value'=>'average','display'=>'基本符合'),
			'bad' => array('value'=>'bad','display'=>'差')
		),
		'powers' => array(
			'create' => array('value'=>'ADD_STORY','display'=>'创建需求'),
			'edit' => array('value'=>'EDIT_STORY','display'=>'编辑需求'),
			'batch_edit' => array('value'=>'BATCH_EDIT_STORY','display'=>'批量编辑需求'),
			'delete' => array('value'=>'REMOVE_STORY','display'=>'删除需求'),
			'page' => array('value'=>'LIST_STORY','display'=>'需求列表'),
			'view' => array('value'=>'DETAIL_STORY','display'=>'需求详情'),
			'assign' => array('value'=>'ASSIGN_STORY','display'=>'指派需求'),
			'change' => array('value'=>'CHANGE_STORY','display'=>'变更需求'),
			'finish' => array('value'=>'FINISH_STORY','display'=>'完成需求'),
			'close' => array('value'=>'CLOSE_STORY','display'=>'关闭需求'),
			'batch_close' => array('value'=>'BATCH_CLOSE_STORY','display'=>'批量关闭需求'),
			'active' => array('value'=>'ACTIVE_STORY','display'=>'激活需求'),
			'verify' => array('value'=>'REVIEW_STORY','display'=>'审核需求'),
			'copy' => array('value'=>'COPY_STORY','display'=>'复制需求')
		)
	),
	'task' => array(
		'value' => 'task',
		'display' => '任务',
		'table' => TBL_TASK,
		'action' => array(
			'opened' => array('value'=>'opened','display'=>'创建任务'),
			'closed' => array('value'=>'closed','display'=>'关闭任务'),
			'canceled' => array('value'=>'canceled','display'=>'取消任务'),
			'edited' => array('value'=>'edited','display'=>'编辑任务'),
			'started' => array('value'=>'started','display'=>'开始任务'),
			'submittest' => array('value'=>'submittest','display'=>'提交测试'),
			'verifytest' => array('value'=>'verifytest','display'=>'审核测试'),
			'verifyok' => array('value'=>'verifyok','display'=>'审核通过'),
			'starttest' => array('value'=>'starttest','display'=>'开始测试'),
			'comptest' => array('value'=>'comptest','display'=>'测试完成'),
			'online' => array('value'=>'online','display'=>'上线任务'),
			'actived' => array('value'=>'actived','display'=>'激活任务'),
			'finished' => array('value'=>'finished','display'=>'完成任务'),
			'assigned' => array('value'=>'assigned','display'=>'指派任务'),
			'commented' => array('value'=>'commented','display'=>'备注任务'),
			'summary' => array('value'=>'summary','display'=>'填写任务心得'),
			'deleted' => array('value'=>'deleted','display'=>'删除任务'),
			'deletedfile' => array('value'=>'deletedfile','display'=>'删除附件')
		),
		'status' => array(
			'wait' => array('value'=>'wait','display'=>'未开始','color'=>'blue'),
			'doing' => array('value'=>'doing','display'=>'进行中','color'=>'red'),
			// 'submittest' => array('value'=>'submittest','display'=>'提交测试','color'=>'brown'),
			'verifytest' => array('value'=>'verifytest','display'=>'测试审核','color'=>'brown'),
			'waittest' => array('value'=>'waittest','display'=>'未测试','color'=>'brown'),
			'testing' => array('value'=>'testing','display'=>'测试中','color'=>'brown'),
			'comptest' => array('value'=>'comptest','display'=>'测试完成','color'=>'brown'),
			'online' => array('value'=>'online','display'=>'上线','color'=>'red'),
			'closed' => array('value'=>'closed','display'=>'已关闭','color'=>'grey'),
			'canceled' => array('value'=>'canceled','display'=>'已取消','color'=>'grey')
		),
		'types' => array(
			'newdev' => array('value'=>'newdev','display'=>'新开发'),
			'upgrade' => array('value'=>'upgrade','display'=>'功能升级'),
			'dataio' => array('value'=>'dataio','display'=>'数据导入导出'),
			'dataopt' => array('value'=>'dataopt','display'=>'数据优化'),
			'sysopt' => array('value'=>'sysopt','display'=>'系统优化'),
			'codeopt' => array('value'=>'codeopt','display'=>'代码优化'),
			'seo' => array('value'=>'seo','display'=>'SEO'),
			'special' => array('value'=>'special','display'=>'专题'),
			'ads' => array('value'=>'ads','display'=>'站内广告'),
			'osads' => array('value'=>'osads','display'=>'站外广告'),
			'page' => array('value'=>'page','display'=>'页面修改'),
			'website' => array('value'=>'website','display'=>'网站改版'),
			'mobdesign' => array('value'=>'mobdesign','display'=>'移动端设计'),
			'emaildesign' => array('value'=>'emaildesign','display'=>'邮件设计'),
			'front' => array('value'=>'front','display'=>'前端'),
			'suspend' => array('value'=>'suspend','display'=>'问题排查'),
			'amend' => array('value'=>'amend','display'=>'修正数据'),
			'others' => array('value'=>'others','display'=>'其他'),
		),
		'close_reason' => array(
			'done' => array('value'=>'done','display'=>'已完成'),
			'cancel' => array('value'=>'cancel','display'=>'已取消')
		),
		'powers' => array(
			'create' => array('value'=>'ADD_TASK','display'=>'创建任务'),
			'batch_create' => array('value'=>'BATCH_CREATE_TASK','display'=>'批量分解任务'),
			'edit' => array('value'=>'EDIT_TASK','display'=>'编辑任务'),
			'batch_edit' => array('value'=>'BATCH_EDIT_TASK','display'=>'批量编辑任务'),
			'delete' => array('value'=>'REMOVE_TASK','display'=>'删除任务'),
			'page' => array('value'=>'LIST_TASK','display'=>'任务列表'),
			'view' => array('value'=>'DETAIL_TASK','display'=>'任务详情'),
			'start' => array('value'=>'START_TASK','display'=>'开始任务'),
			'cancel' => array('value'=>'CANCEL_TASK','display'=>'取消任务'),
			// 'finish' => array('value'=>'FINISH_TASK','display'=>'完成任务'),
			'close' => array('value'=>'CLOSE_TASK','display'=>'关闭任务'),
			'batch_close' => array('value'=>'BATCH_CLOSE_TASK','display'=>'批量关闭任务'),
			// 'assign' => array('value'=>'ASSIGN_TASK','display'=>'指派任务'),
			'active' => array('value'=>'ACTIVE_TASK','display'=>'激活任务'),
			'submittest' => array('value'=>'SUBMIT_TEST', 'display'=>'提交测试'),
			'verifytest' => array('value'=>'VERIFY_TEST', 'display'=>'审核测试'),
			'verifyok' => array('value'=>'VERIFY_OK_TEST', 'display'=>'审核通过'),
			'online' => array('value'=>'ONLINE_TASK', 'display'=>'任务上线'),
			'starttest' => array('value'=>'START_TEST','display'=>'开始测试'),
			'finishtest' => array('value'=>'FINISH_TEST','display'=>'测试完成'),
			// 'block' => array('value'=>'BLOCK_TASK','display'=>'阻塞任务'),
			// 'suspend' => array('value'=>'SUSPEND_TASK','display'=>'挂起任务')
		)
	),
	'taskprizes' => array(
		'value' => 'taskprizes',
		'display' => '积分任务',
		'table' => TBL_TASK_PRIZES,
		'action' => array(
			'opened' => array('value'=>'opened','display'=>'创建任务'),
			'closed' => array('value'=>'closed','display'=>'关闭任务'),
			'canceled' => array('value'=>'canceled','display'=>'取消任务'),
			'edited' => array('value'=>'edited','display'=>'编辑任务'),
			'get' => array('value'=>'get','display'=>'领取任务'),

			'started' => array('value'=>'started','display'=>'开始任务'),
			'submittask' => array('value'=>'submittask','display'=>'提交审核'),
			// 'verifytest' => array('value'=>'verifytest','display'=>'审核测试'),
			'verify' => array('value'=>'verify','display'=>'进行审核'),
			// 'starttest' => array('value'=>'starttest','display'=>'开始测试'),
			// 'comptest' => array('value'=>'comptest','display'=>'测试完成'),
			'online' => array('value'=>'online','display'=>'上线任务'),
			'actived' => array('value'=>'actived','display'=>'激活任务'),
			'finished' => array('value'=>'finished','display'=>'完成任务'),
			'assigned' => array('value'=>'assigned','display'=>'指派任务'),
			'commented' => array('value'=>'commented','display'=>'备注任务'),
			'summary' => array('value'=>'summary','display'=>'填写任务心得'),
			'deleted' => array('value'=>'deleted','display'=>'删除任务'),
			'deletedfile' => array('value'=>'deletedfile','display'=>'删除附件')
		),
		'status' => array(
			'apply' => array('value'=>'apply','display'=>'未认领','color'=>'blue'),
			'wait' => array('value'=>'wait','display'=>'未开始','color'=>'blue'),
			'doing' => array('value'=>'doing','display'=>'进行中','color'=>'red'),
			'comptest' => array('value'=>'comptest','display'=>'提交审核','color'=>'brown'),
			'verifyok' => array('value'=>'verifyok','display'=>'审核完成','color'=>'brown'),
			'online' => array('value'=>'online','display'=>'上线','color'=>'red'),
			'confirm' => array('value'=>'confirm','display'=>'提交确认','color'=>'red'),
			'closed' => array('value'=>'closed','display'=>'已关闭','color'=>'grey'),
			'canceled' => array('value'=>'canceled','display'=>'已取消','color'=>'grey')
		),
		'reviewed_result' => array(
			'pass' => array('value'=>'pass','display'=>'审核通过，可以上线'),
			'reject' => array('value'=>'reject','display'=>'有问题，重新测试再提交审核')
		),
		// 'types' => array(
		// 	'newdev' => array('value'=>'newdev','display'=>'新开发'),
		// 	'upgrade' => array('value'=>'upgrade','display'=>'功能升级'),
		// 	'dataio' => array('value'=>'dataio','display'=>'数据导入导出'),
		// 	'dataopt' => array('value'=>'dataopt','display'=>'数据优化'),
		// 	'sysopt' => array('value'=>'sysopt','display'=>'系统优化'),
		// 	'codeopt' => array('value'=>'codeopt','display'=>'代码优化'),
		// 	'seo' => array('value'=>'seo','display'=>'SEO'),
		// 	'special' => array('value'=>'special','display'=>'专题'),
		// 	'ads' => array('value'=>'ads','display'=>'站内广告'),
		// 	'osads' => array('value'=>'osads','display'=>'站外广告'),
		// 	'page' => array('value'=>'page','display'=>'页面修改'),
		// 	'website' => array('value'=>'website','display'=>'网站改版'),
		// 	'mobdesign' => array('value'=>'mobdesign','display'=>'移动端设计'),
		// 	'emaildesign' => array('value'=>'emaildesign','display'=>'邮件设计'),
		// 	'front' => array('value'=>'front','display'=>'前端'),
		// 	'suspend' => array('value'=>'suspend','display'=>'问题排查'),
		// 	'amend' => array('value'=>'amend','display'=>'修正数据'),
		// 	'others' => array('value'=>'others','display'=>'其他'),
		// ),
		'close_reason' => array(
			'done' => array('value'=>'done','display'=>'已完成'),
			'cancel' => array('value'=>'cancel','display'=>'已取消')
		),
		'powers' => array(
			'create' => array('value'=>'ADD_TASKPRIZES','display'=>'创建任务'),
			'batch_create' => array('value'=>'BATCH_CREATED_TASKPRIZES','display'=>'批量分解任务'),
			'edit' => array('value'=>'EDITD_TASKPRIZES','display'=>'编辑任务'),
			'batch_edit' => array('value'=>'BATCH_EDITD_TASKPRIZES','display'=>'批量编辑任务'),
			'delete' => array('value'=>'REMOVED_TASKPRIZES','display'=>'删除任务'),
			'page' => array('value'=>'LISTD_TASKPRIZES','display'=>'任务列表'),
			'view' => array('value'=>'DETAILD_TASKPRIZES','display'=>'任务详情'),
			'gettask' => array('value'=>'GET_TASKPRIZES','display'=>'领取任务'),
			'start' => array('value'=>'STARTD_TASKPRIZES','display'=>'开始任务'),
			'submittask' => array('value'=>'SUBMIT_TASKPRIZES','display'=>'提交审核'),
			'verify' => array('value'=>'VERIFY_TASKPRIZES','display'=>'审核'),
			'cancel' => array('value'=>'CANCELD_TASKPRIZES','display'=>'取消任务'),
			'close' => array('value'=>'CLOSED_TASKPRIZES','display'=>'关闭任务'),
			'batch_close' => array('value'=>'BATCH_CLOSED_TASKPRIZES','display'=>'批量关闭任务'),
			'active' => array('value'=>'ACTIVED_TASKPRIZES','display'=>'激活任务'),
			'online' => array('value'=>'ONLINED_TASKPRIZES', 'display'=>'任务上线')
		)
	),
	'bug' => array(
		'value' => 'bug',
		'display' => 'Bug',
		'table' => TBL_BUG,
		'action' => array(
			'opened' => array('value'=>'opened','display'=>'创建BUG'),
			'closed' => array('value'=>'closed','display'=>'关闭BUG'),
			'assigned'=>array('value'=>'assigned','display'=>'重新指派BUG'),
			'canceled' => array('value'=>'canceled','display'=>'删除BUG'),
			'resolved' => array('value'=>'resolved','display'=>'解决BUG'),
			'edited' => array('value'=>'edited','display'=>'编辑BUG'),
			'deletedfile' => array('value'=>'deletedfile','display'=>'删除附件')
		),
		'status' => array(
			'active' => array('value'=>'active','display'=>'激活'),
			'resolved' => array('value'=>'resolved','display'=>'已解决'),
			'closed' => array('value'=>'closed','display'=>'已关闭')
		),
		'types' => array(
			'codeerror' => array('value'=>'codeerror','display'=>'代码错误'),
			'interface' => array('value'=>'interface','display'=>'界面优化'),
			'designdefect' => array('value'=>'designdefect','display'=>'设计缺陷'),
			'config' => array('value'=>'config','display'=>'配置相关'),
			'install' => array('value'=>'install','display'=>'安装部署'),
			'security' => array('value'=>'security','display'=>'安全相关'),
			'performance' => array('value'=>'performance','display'=>'性能问题'),
			'standard' => array('value'=>'standard','display'=>'标准规范'),
			'automation' => array('value'=>'automation','display'=>'测试脚本'),
			'others' => array('value'=>'others','display'=>'其他')
		),
		'resolutions' => array(
			'bydesign' => array('value'=>'bydesign','display'=>'设计如此'),
			'duplicate' => array('value'=>'duplicate','display'=>'重复Bug'),
			'external' => array('value'=>'external','display'=>'外部原因'),
			'fixed' => array('value'=>'fixed','display'=>'已解决'),
			'notrepro' => array('value'=>'notrepro','display'=>'无法重现'),
			'postponed' => array('value'=>'postponed','display'=>'延期处理'),
			'willnotfix' => array('value'=>'willnotfix','display'=>'不予解决'),
		),
		'powers' => array(
			'read' => array('value'=>'READ_BUG','display'=>'读取BUG'),
			'create' => array('value'=>'ADD_BUG','display'=>'创建BUG'),
			'edit' => array('value'=>'EDIT_BUG','display'=>'编辑BUG'),
			'active'=> array('value'=>'ACTIVE_BUG','display'=>'激活BUG'),
			'remove' => array('value'=>'REMOVE_BUG','display'=>'删除BUG'),
			'list' => array('value'=>'LIST_BUG','display'=>'BUG列表'),
			'detail' => array('value'=>'DETAIL_BUG','display'=>'BUG详情'),
			'resolve' => array('value'=>'FINISH_BUG','display'=>'解决BUG'),
			'resolve_form' =>array('value'=>'FINISH_BUG_EDIT','display'=>'BUG解决编辑'),
			'close' => array('value'=>'CLOSE_BUG','display'=>'关闭BUG'),
			'assign' => array('value'=>'ASSIGN_BUG','display'=>'BUG 指派'),
			'assign_form' =>array('value'=>'ASSIGN_BUG_EDIT','display'=>'BUG指派编辑')
		)
	),
	'testtask' => array(
		'value' => 'testtask',
		'display' => '测试任务',
		'table' => TBL_TESTTASK,
		'action' => array(
			'opened' => array('value'=>'opened','display'=>'创建测试任务'),
			'closed' => array('value'=>'closed','display'=>'关闭测试任务'),
			'canceled' => array('value'=>'canceled','display'=>'取消测试任务'),
			'resolved' => array('value'=>'resolved','display'=>'解决测试任务'),
			'edited' => array('value'=>'edited','display'=>'编辑测试任务'),
            'started' => array('value'=>'started','display'=>'开始测试任务')
		),
		'status' => array(
			'wait' => array('value'=>'wait','display'=>'未开始'),
			'doing' => array('value'=>'doing','display'=>'进行中'),
			'done' => array('value'=>'done','display'=>'已完成'),
			//'closed' => array('value'=>'closed','display'=>'已关闭'),
			'blocked' => array('value'=>'blocked','display'=>'被阻塞'),
			//'suspended' => array('value'=>'suspended','display'=>'挂起')
		),
		'powers' => array(
			'read' => array('value'=>'READ_TESTTASK','display'=>'读取测试任务'),
			'create' => array('value'=>'ADD_TESTTASK','display'=>'提交测试任务'),
			'edit' => array('value'=>'EDIT_TESTTASK','display'=>'编辑测试任务'),
			'delete' => array('value'=>'REMOVE_TESTTASK','display'=>'删除测试任务'),
			'page' => array('value'=>'PAGE_TESTTASK','display'=>'测试任务列表'),
			'view' => array('value'=>'VIEW_TESTTASK','display'=>'测试任务详情'),
			'start' => array('value'=>'START_TESTTASK','display'=>'开始测试任务'),
			'cancel' => array('value'=>'CANCEL_TESTTASK','display'=>'取消测试任务'),
			'finish' => array('value'=>'FINISH_TESTTASK','display'=>'完成测试任务'),
			'close' => array('value'=>'CLOSE_TESTTASK','display'=>'关闭测试任务'),
			'block' => array('value'=>'BLOCK_TESTTASK','display'=>'阻塞测试任务'),
			'suspend' => array('value'=>'SUSPEND_TESTTASK','display'=>'挂起测试任务')
		)
	),
	'product' => array(
		'value' => 'product',
		'display' => '产品',
		'table' => TBL_PRODUCT,
		'action' => array(
			'opened' => array('value'=>'opened','display'=>'创建产品'),
			'closed' => array('value'=>'closed','display'=>'关闭产品'),
			'edited' => array('value'=>'edited','display'=>'编辑产品'),
			'commented' => array('value'=>'commented','display'=>'备注产品')
		),
		'status' => array(
			'normal' => array('value'=>'normal','display'=>'正常','color'=>'red'),
			'closed' => array('value'=>'closed','display'=>'已关闭','color'=>'grey')
		),
		'powers' => array(
			'read' => array('value'=>'READ_PRODUCT','display'=>'读取产品'),
			'create' => array('value'=>'ADD_PRODUCT','display'=>'提交产品'),
			'edit' => array('value'=>'EDIT_PRODUCT','display'=>'编辑产品'),
			'delete' => array('value'=>'REMOVE_PRODUCT','display'=>'删除产品'),
			'page' => array('value'=>'PAGE_PRODUCT','display'=>'产品列表'),
			'view' => array('value'=>'VIEW_PRODUCT','display'=>'产品详情'),
			'close' => array('value'=>'CLOSE_PRODUCT','display'=>'关闭产品'),
			'story' => array('value'=>'STORY_PRODUCT','display'=>'产品需求列表')
		)
	),
	'role' => array(
		'value' => 'role',
		'display' => '角色',
		'powers' => array(
			'create' => array('value'=>'ADD_ROLE','display'=>'创建角色'),
			'edit' => array('value'=>'EDIT_ROLE','display'=>'编辑角色'),
			'delete' => array('value'=>'REMOVE_ROLE','display'=>'删除角色'),
			'page' => array('value'=>'LIST_ROLE','display'=>'角色列表')
		)
	),
	'department' => array(
		'value' => 'department',
		'display' => '部门',
		'table' => TBL_DEPARTMENT,
		'action' => array(
			'index' => array('value'=>'index','display'=>'部门列表'),
			'department_add' => array('value'=>'department_add','display'=>'创建部门'),
			'view' => array('value'=>'view','display'=>'查看部门'),
			'edit' => array('value'=>'edit','display'=>'编辑部门'),
			'del' => array('value'=>'del','display'=>'删除该部门及其所有子部门'),
            'department_update' => array('value'=>'department_update','display'=>'编辑部门'),
			'check_name' => array('value'=>'check_name','display'=>'验证部门名称'),
			'get_child_info' => array('value'=>'get_child_info','display'=>'获取子部门信息'),
		),
		'powers' => array(
			'index' => array('value'=>'LIST_DEPARTMENT','display'=>'部门列表'),
			'department_add' => array('value'=>'ADD_DEPARTMENT','display'=>'创建部门'),
			'view' => array('value'=>'VIEW_DEPARTMENT','display'=>'查看部门'),
			'edit' => array('value'=>'EDIT_DEPARTMENT','display'=>'编辑部门'),
			'department_update' => array('value'=>'UPDATE_DEPARTMENT','display'=>'编辑部门'),
			'check_name' => array('value'=>'CHECK_DEPARTMENT','display'=>'验证部门名称'),
			'del' => array('value'=>'DEL_DEPARTMENT','display'=>'删除该部门及其所有子部门'),
			'get_child_info' => array('value'=>'GET_CHILD_INFO_DEPARTMENT','display'=>'获取子部门信息'),
		)
	),
	'index' => array(
		'display' => '附件',
		'powers' => array(
			'upload4kindeditor' => array('value'=>'EDITOR_UPLOAD','display'=>'文本编辑器上传文件'),
			'download' => array('value'=>'DOWNLOAD_ATTACHMENT','display'=>'下载附件'),
			'delfile' => array('value'=>'DELETE_ATTACHMENT','display'=>'删除附件')
		)
	),
	'account' => array(
		'display' => '用户',
		'powers' => array(
			'account_set' => array('value'=>'ACCOUNT_SET','display'=>'设置用户部门&角色'),
			'add' => array('value'=>'ACCOUNT_ADD','display'=>'添加用户'),
			'edit' => array('value'=>'ACCOUNT_EDIT','display'=>'编辑用户'),
		)
	),
	'team' => array(
		'value' => 'team',
		'display' => '项目团队',
		'table' => TBL_PROJECT_TEAM,
		'action' => array(
			'team_manage' => array('value'=>'team_manage','display'=>'添加团队成员'),
			'team_del' => array('value'=>'team_del','display'=>'删除团队成员'),
			'user_list_ajax' => array('value'=>'user_list_ajax','display'=>'选择团队成员'),
		),
		'powers' => array(
			'team_manage' => array('value'=>'TEAM_MANAGE','display'=>'添加团队成员'),
			'team_del' => array('value'=>'TEAM_DEL','display'=>'删除团队成员'),
			'user_list_ajax' => array('value'=>'USER_LIST_AJAX','display'=>'选择团队成员'),
		)
	),
	'grade' => array(
		'display' => '评分',
		'powers' => array(
			'setlist' => array('value'=>'GRADE_SETTING_LIST','display'=>'项目评价设置列表'),
			'setting' => array('value'=>'GRADE_SETTING','display'=>'项目评价设置'),
			'gradetasklist' => array('value'=>'GRADE_TASK_LIST','display'=>'任务评分列表'),
			'gradestorylist' => array('value'=>'GRADE_STORY_LIST','display'=>'需求评分列表'),
			'gradeadmin' => array('value'=>'GRADE_ADMIN_LIST','display'=>'管理员评分列表'),
			'taskview' => array('value'=>'GRADE_TASK_VIEW','display'=>'查看任务评分'),
			'storyview' => array('value'=>'GRADE_STORY_VIEW','display'=>'查看需求评分'),
			'adminview' => array('value'=>'GRADE_ADMIN_VIEW','display'=>'管理员查看评分')
		)
	),
	'overtime' => array(
		'value' => 'overtime',
		'display' => '加班',
		'powers' => array(
			'add' => array('value'=>'ADD_OVERTIME','display'=>'加班申请/编辑'),
			'view' => array('value'=>'VIEW_OVERTIME','display'=>'查看'),
			'shenhe' => array('value'=>'VERIFY_OVERTIME','display'=>'加班审核'),
			'delete' => array('value'=>'REMOVE_OVERTIME','display'=>'加班删除')
		)
	),
	'my' => array(
		'display' => '我的视图',
		'powers' => array()
	),
	'statistics' => array(
		'display' => '统计',
		'powers' => array(
			'get_dpt_overtime_stat' => array('value'=>'DEPARTMENT_OVERTIME_STAT','display'=>'部门加班统计'),
			'exporting' => array('value'=>'EXPORT_STAT','display'=>'导出统计数据'),
			'pro_story' => array('value'=>'PRO_STORY_STAT','display'=>'项目需求统计')
		)
	),
	'ratting' => array(
		'display' => '能力评分',
		'powers' => array(
			'userlist' => array('value'=>'USER_LIST','display'=>'我要评分'),
			'personal_grade' => array('value'=>'MY_GRADE','display'=>'查看个人得分'),
			'rattinglist' => array('value'=>'MY_RATTING_LIST','display'=>'我的评分列表'),
			'rattingreport' => array('value'=>'RATTING_REPORT','display'=>'评分报表'),
			'auditlist' => array('value'=>'AUDIT_LIST','display'=>'审核列表'),
			'ratsetting' => array('value'=>'RAT_SETTING','display'=>'评分设置'),
			'infoset' => array('value'=>'INFO_SET','display'=>'资料设置'),
		)
	),
	'module' => array(
		'value' => 'module',
		'display' => '模块',
		'powers' => array(
			'edit' => array('value'=>'EDIT_MODULE','display'=>'编辑模块'),
			'delete' => array('value'=>'REMOVE_MODULE','display'=>'删除模块'),
		)
	),
	'menu_show' => array(
		'value' => 'menu',
		'display' => '菜单显示',
		'powers' => array(
			'my' => array('value'=>'ALL_MY','display'=>'我的视图'),
			'product' => array('value'=>'ALL_PRODUCT','display'=>'产品'),
			'project' => array('value'=>'ALL_PROJECT','display'=>'项目'),
			'statistics' => array('value'=>'ALL_STATISTICS','display'=>'统计'),
			'overtime' => array('value'=>'ALL_OVERTIME','display'=>'加班'),
			'grade' => array('value'=>'ALL_GRADE','display'=>'评分'),
			'sys' => array('value'=>'ALL_SYS','display'=>'组织'),
			'ratting' => array('value'=>'ALL_RATTING','display'=>'能量评分'),
		)
	)
);