<style type="text/css">
body {padding:15px;}
</style>

<table width="100%" cellspacing="0" cellpadding="0">
	<tr height="30">
		<td>
			<img src="<?php echo $g['img_module']?>/dot_01.gif" align="absmiddle" /> <b>아이콘 삽입하기</b> 
		</td>
		<td align="right">

<?php 
$iconFolder = $iconFolder ? $iconFolder : "set1";
$odir = $g['dir_module'].'lib/icon/';
$DB_DIR = opendir($odir);
while(false !== ($file = readdir($DB_DIR))) :
	if(is_file($odir.$file.'/name.txt')) :
?>

&nbsp;&nbsp;<a href="./?m=<?php echo $m?>&front=<?php echo $front?>&compo=<?php echo $compo?>&iconFolder=<?php echo $file?>"<?php if($iconFolder==$file):?> style="font-weight:bold;"<?php endif?>><?php echo implode('',file($odir.$file.'/name.txt'))?></a>

<?php 
endif;
endwhile;
closedir($DB_DIR);
?>

		</td>
	</tr>
	<tr><td colspan="2" height="1" background="<?php echo $g['img_module']?>/line_01.gif"></td></tr>
	<tr><td colspan="2" height="25"></td></tr>
</table>




<div style="width:<?php echo $PreviewWidth?>px;height:380px;overflow:auto;">

<?php 
$RECNUM = 1000;
if ( !$p ) { $p = 1; $PAGENUM = 1; } else {$PAGENUM = $p;}
$START_NUM = ($PAGENUM-1)*$RECNUM;

$odir = $g['dir_module'].'lib/icon/'.$iconFolder.'/';
$DB_DIR = opendir($odir);
while(false !== ($file = readdir($DB_DIR))) {
	if(is_file($odir.$file) && $file != 'Thumbs.db' && $file != 'name.txt') $files[] = $file;				
}
closedir($DB_DIR);

//rsort($tables);
//reset($tables);

$Table_Num  = sizeof($files);
$TOTAL_PAGE = intval(($Table_Num-1)/$RECNUM)+1;
?>

<table width="100%" cellspacing="0" cellpadding="3">
	<tr align="center" valign="top">

<?php 
$j=0;
for( $i = $Table_Num- 1 -(($p-1)*$RECNUM); $i > $Table_Num - 1 -($p*$RECNUM);$i--) :
if ($i > -1) :
$j++;
?>

	<td width="20%">
		<img src="<?php echo $odir.$files[$i]?>" title="이 이미지를 본문에 삽입합니다." style="cursor:pointer;" onclick="ResultCode('<?php echo $iconFolder.'/'.$files[$i]?>')" />
	</td>

<?php if(!(($j)%5)):?>
	</tr>
	<tr align="center" valign="top">
<?php 
endif;
endif;
endfor;

if($j%4 != 0)
{
	$term = 5-($j%4);
	for ($x = 0; $x < $term; $x++)
	{
		echo "<td width='20%' align=center></td>";
	}
}
?>


	</tr>
</table>


</div>


<table width="100%" cellspacing="0" cellpadding="0">
	<tr><td height="15"></td></tr>
	<tr><td height="1" bgcolor="#efefef"></td></tr>
	<tr><td height="15"></td></tr>
</table>



<script type="text/javascript">
//<![CDATA[
function ResultCode(src)
{
	opener.EditDrop('<img src="'+rooturl+'/modules/editor/lib/icon/'+src+'" alt="" />');
	window.focus();
}
self.resizeTo(550,570);
//]]>
</script>

