<?
include($_SERVER['DOCUMENT_ROOT']."/config.php");
include($_SERVER['DOCUMENT_ROOT']."/". ADMIN_PANEL ."/includes.php");
//защита от иньекций
if( $Main->inject() ){
    header('HTTP/1.0 404 Not Found');
    header('Content-Type: text/html; charset=utf-8');
    include($_SERVER['DOCUMENT_ROOT']."/public/pages/404.php");
    exit;
}

$pageData = $Router->GetCPU();

$Db->setlang($Router->lang);

if($pageData['status']==='404'){
    header('HTTP/1.0 404 Not Found');
    header('Content-Type: text/html; charset=utf-8');
    include($_SERVER['DOCUMENT_ROOT']."/public/pages/404.php");
    exit;
}elseif($pageData['status']==='redirect'){
    header('HTTP/1.1 301 Moved Permanently');
    header("Location: ".$pageData['data']);
    exit;
}

$Main->lang = $Router->lang;
$_SESSION['last_lang'] = $Router->lang;

$GLOBALS['dictionary'] = $Main->GetDefineLangTerms($Router->lang);
$page_data = $Router->GetPageData($pageData);
if(!$page_data){
    header('HTTP/1.0 404 Not Found');
    header('Content-Type: text/html; charset=utf-8');
    include($_SERVER['DOCUMENT_ROOT']."/public/pages/404.php");
    exit;
}

mb_internal_encoding("UTF-8");
header('Content-Type: text/html; charset=utf-8');



include($_SERVER['DOCUMENT_ROOT']."/public/pages".$pageData['page']);