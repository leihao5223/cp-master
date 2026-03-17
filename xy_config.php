<?php
require_once('xy_sqlin.php');
$conf['debug']['level']=5;

/*		数据库配置		*/
$conf['db']['dsn']='mysql:host='.getenv('DB_HOST').';dbname='.getenv('DB_NAME').';charset=utf8';
$dbname=getenv('DB_NAME');
$dbhost=getenv('DB_HOST');
$conf['db']['user']=getenv('DB_USER');
$conf['db']['password']=getenv('DB_PASSWORD');
$conf['db']['charset']='utf8';
$conf['db']['prename']='xy_';

/*		语言设置		*/
$conf['language'] = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'zh';

$conf['cache']['expire']=0;
$conf['cache']['dir']='_cache/';

$conf['url_modal']=2;

$conf['action']['template']='xy_inc/xy_default/';
$conf['action']['modals']='xy_action/xy_default/';

$conf['member']['sessionTime']=15*60;	// 会员有效时间
$weburl='https://cp-master.vercel.app';
error_reporting(E_ERROR & ~E_NOTICE);

ini_set('date.timezone', 'asia/shanghai');
ini_set('display_errors', 'Off');

// 语言文件加载
$lang_file = dirname(__FILE__) . '/lang/' . $conf['language'] . '.php';
if (file_exists($lang_file)) {
    $lang = include($lang_file);
} else {
    $lang = include(dirname(__FILE__) . '/lang/zh.php');
}

if(strtotime(date('Y-m-d',time()))>strtotime(date('Y-m-d',time()))){
	$GLOBALS['fromTime']=strtotime(date('Y-m-d',strtotime("-1 day")));
	$GLOBALS['toTime']=strtotime(date('Y-m-d',time()));
}else{
	$GLOBALS['fromTime']=strtotime(date('Y-m-d'));
	$GLOBALS['toTime']=strtotime(date('Y-m-d',strtotime("+1 day")));
}