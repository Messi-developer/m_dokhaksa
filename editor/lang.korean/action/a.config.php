<?php
if(!defined('__KIMS__')) exit;

checkAdmin(0);

$_tmpdfile = $g['dir_module'].'var/var.editor.php';
$fp = fopen($_tmpdfile,'w');
fwrite($fp, "<?php\n");

fwrite($fp, "\$d['editor']['skin'] = \"".$skin."\";\n");
fwrite($fp, "\$d['editor']['compo'] = \"".$compo."\";\n");

fwrite($fp, "?>");
fclose($fp);
@chmod($_tmpdfile,0707);



getLink('reload','parent.','','');
?>
