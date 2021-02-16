<?php
if(!defined('__KIMS__')) exit;

$iframe = 'Y';
$mod = 'main';

include_once $g['dir_module'].'var/var.editor.php';

if ($front == 'lib')
{
	$g['main'] = $g['dir_module'].$front.'/'.$compo.'.php';
}
else {

	$d['module']['skin'] = $skin ? $skin : $d['editor']['skin']; //½ºÅ²

	$g['dir_module_skin'] = $g['dir_module'].'theme/'.$d['module']['skin'].'/';
	$g['url_module_skin'] = $g['url_module'].'/theme/'.$d['module']['skin'];
	$g['img_module_skin'] = $g['url_module_skin'].'/image';

	$g['dir_module_mode'] = $g['dir_module_skin'].$mod;
	$g['url_module_mode'] = $g['url_module_skin'].'/'.$mod;

	$g['main'] = $g['dir_module_mode'].'.php';
}
?>
