<?php
session_start();
if (isset($_GET['lang']) && in_array($_GET['lang'], ['zh', 'en'])) {
    $_SESSION['lang'] = $_GET['lang'];
    setcookie('lang', $_GET['lang'], time() + 86400 * 30, '/');
} elseif (isset($_COOKIE['lang']) && in_array($_COOKIE['lang'], ['zh', 'en'])) {
    $_SESSION['lang'] = $_COOKIE['lang'];
} else {
    $_SESSION['lang'] = 'zh';
}

require '../xy_config.php';

$lang_file = dirname(dirname(__FILE__)) . '/lang/' . $_SESSION['lang'] . '.php';
if (file_exists($lang_file)) {
    $lang = include($lang_file);
} else {
    $lang = include(dirname(dirname(__FILE__)) . '/lang/zh.php');
}

require '../xy_lib/core/DBAccess.class';
require '../xy_lib/core/Object.class';
require '../xy_action/xy_default/WebBase.class.php';
require '../xy_action/xy_default/WebLoginBase.class.php';

$para=array();

if(isset($_SERVER['PATH_INFO'])){
    $para=explode('/', substr($_SERVER['PATH_INFO'],1));
    if($control=array_shift($para)){
        if(count($para)){
            $action=array_shift($para);
        }else{
            $action=$control;
            $control='index';
        }
    }else{
        $control='index';
        $action='main';
    }
}else{
    $control='index';
    $action='main';
}
$control=ucfirst($control);

if(strpos($action,'-')!==false){
    list($action, $page)=explode('-',$action);
}

$file=$conf['action']['modals'].$control.'.class.php';
if(!is_file($file)) notfound('找不到控制器');
try{
    require $file;
}catch(Exception $e){
    print_r($e);
    exit;
}

if(!class_exists($control)) notfound('找不到控制器1');
$jms=new $control($conf['db']['dsn'], $conf['db']['user'], $conf['db']['password']);
$jms->debugLevel=$conf['debug']['level'];

if(!method_exists($jms, $action)) notfound('方法不存在');
$reflection=new ReflectionMethod($jms, $action);
if($reflection->isStatic()) notfound('不允许调用Static修饰的方法');
if(!$reflection->isFinal()) notfound('只能调用final修饰的方法');

$jms->controller=$control;
$jms->action=$action;

$jms->charset=$conf['db']['charset'];
$jms->cacheDir=$conf['cache']['dir'];
$jms->setCacheDir($conf['cache']['dir']);
$jms->actionTemplate=$conf['action']['template'];
$jms->prename=$conf['db']['prename'];
$jms->title=$conf['web']['title'];
if(method_exists($jms, 'getSystemSettings')) $jms->getSystemSettings();

if($jms->settings['switchWeb']=='0'){
    $jms->display('close-service.php');
    exit;
}

if(isset($page)) $jms->page=$page;

if($q=$_SERVER['QUERY_STRING']){
    $para=array_merge($para, explode('/', $q));
}
if($para==null) $para=array();

$jms->headers=getallheaders();
if(isset($jms->headers['x-call'])){
    header('content-Type: application/json');
    try{
        ob_start();
        echo json_encode($reflection->invokeArgs($jms, $_POST));
        ob_flush();
    }catch(Exception $e){
        $jms->error($e->getMessage(), true);
    }
}elseif(isset($jms->headers['x-form-call'])){
    $accept=strpos($jms->headers['Accept'], 'application/json')===0;
    if($accept) header('content-Type: application/json');
    try{
        ob_start();
        if($accept){
            echo json_encode($reflection->invokeArgs($jms, $_POST));
        }else{
            json_encode($reflection->invokeArgs($jms, $_POST));
        }
        ob_flush();
    }catch(Exception $e){
        $jms->error($e->getMessage(), true);
    }
}elseif(strpos($jms->headers['Accept'], 'application/json')===0){
    header('content-Type: application/json');
    try{
        echo json_encode(call_user_func_array(array($jms, $action), $para));
    }catch(Exception $e){
        $jms->error($e->getmessage());
    }
}else{
    header('content-Type: text/html;charset=utf-8');
    call_user_func_array(array($jms, $action), $para);
}
$jms=null;

function notfound($message){
    header('content-Type: text/plain; charset=utf8');
    header('HTTP/1.1 404 Not Found');
    die($message);
}