<?php
if(!defined('__KIMS__')) exit;

checkAdmin(0);

if (is_file($g['dir_module'].'component/'.$compo.'/main.php'))
{
	include_once $g['path_core'].'function/dir.func.php';
	DirDelete($g['dir_module'].'component/'.$compo);
}

getLink('reload','parent.','','');
?>
