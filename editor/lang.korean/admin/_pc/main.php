<?php include_once $g['path_module'].$module.'/var/var.editor.php'?>

<div id="editorbox">

	<form name="procForm" action="<?php echo $g['s']?>/" method="post" target="_action_frame_<?php echo $m?>" onsubmit="return saveCheck(this);">
	<input type="hidden" name="r" value="<?php echo $r?>" />
	<input type="hidden" name="m" value="<?php echo $module?>" />
	<input type="hidden" name="a" value="config" />
	<input type="hidden" name="compo" value="<?php echo $d['editor']['compo']?>" />

	<div class="title">
		편집기 설정
	</div>

	<table>
		<tr>
			<td class="td1">편집기 스킨</td>
			<td class="td2">
				
				<select name="skin" class="select1">
				<?php $tdir = $g['path_module'].$module.'/theme/'?>
				<?php $dirs = opendir($tdir)?>
				<?php while(false !== ($skin = readdir($dirs))):?>
				<?php if($skin=='.' || $skin == '..' || is_file($tdir.$skin))continue?>
				<option value="<?php echo $skin?>" title="<?php echo $skin?>"<?php if($d['editor']['skin']==$skin):?> selected="selected"<?php endif?>>ㆍ<?php echo getFolderName($tdir.$skin)?>(<?php echo $skin?>)</option>
				<?php endwhile?>
				<?php closedir($dirs)?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="td1">사용 확장도구</td>
			<td class="td2 shift">
				
				<?php $i=0?>
				<?php $tdir = $g['path_module'].$module.'/component/'?>
				<?php $dirs = opendir($tdir)?>
				<?php while(false !== ($skin = readdir($dirs))):?>
				<?php if($skin=='.' || $skin == '..' || is_file($tdir.$skin))continue?>
				<?php $i++?>
				<input type="checkbox" name="compo_members[]" value="<?php echo $skin?>"<?php if(strstr($d['editor']['compo'],'['.$skin.']')):?> checked="checked"<?php endif?> /><?php echo getFolderName($tdir.$skin)?>(<?php echo $skin?>)
				<a href="<?php echo $g['s']?>/?r=<?php echo $r?>&amp;m=<?php echo $module?>&amp;a=compo_delete&amp;compo=<?php echo $skin?>" onclick="return confirm('정말로 삭제하시겠습니까?  ');"><img src="<?php echo $g['img_core']?>/_public/btn_del_s01.gif" class="del" alt="" /></a>
				<br />
				<?php endwhile?>
				<?php closedir($dirs)?>
				<?php if(!$i):?>
				<input type="checkbox" disabled="disabled" />등록된 확장도구가 없습니다.
				<?php else:?>
				<div class="guide">
				사용할 확장도구를 체크해 주세요.
				</div>
				<?php endif?>
			</td>
		</tr>
	</table>

	<div class="submitbox">
		<input type="submit" class="btnblue" value=" 확인 " />
	</div>

	</form>

</div>




<script type="text/javascript">
//<![CDATA[
function saveCheck(f)
{
    var l = document.getElementsByName('compo_members[]');
    var n = l.length;
    var i;
	var j=0;
	var s='';

	for	(i = 0; i < n; i++)
	{
		if (l[i].checked == true)
		{
			j++;
			s += '['+l[i].value +']';
		}
	}
	f.compo.value = s;

	return confirm('정말로 실행하시겠습니까?         ');
}
//]]>
</script>




