<?

$pathSection = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
$pathSection = str_replace( ADMIN_PANEL , '{ADMIN_PANEL}' , $pathSection );
$Section = $Db->getOne("SELECT * FROM ".DB_PREFIX."_menu_admin WHERE link = '".$pathSection."'");
$_params['table'] = $Section['assoc_table'];
$_params['title'] = $Section['title'];
$_params['access'] = $Section['access'];
$_params['num_page'] = $Section['num_page'];
$_params['access_delete'] = $Section['access_delete'];
$_params['access_edit'] = $Section['access_edit'];
$_params['page_id'] = $Section['page_id'];

// параметры раздела
$getSectionParams = $Db->getAll("SELECT * FROM ".DB_PREFIX."_menu_admin_settings WHERE section_id = ".$Section['id']);
foreach ($getSectionParams AS $k=>$sectionParam){
    $sectionParam['value'] = str_replace( '{ADMIN_PANEL}' , ADMIN_PANEL ,  $sectionParam['value'] );
    $_params[$sectionParam['param']] = $sectionParam['value'];
}
if (isset($_GET['id'])) {$id = (int)$_GET['id'];}else{$id = 0;}
if (isset($_GET['parent'])) {$parent = (int)$_GET['parent'];}else{$parent = 0;}

?>