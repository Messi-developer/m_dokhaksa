<style type="text/css">
body {padding:15px;}
</style>


<table width="100%" cellspacing="0" cellpadding="0">
	<tr height="30">
		<td>
			<img src="<?php echo $g['img_module']?>/dot_01.gif" align="absmiddle" /> 
			<b>HTML�ڵ� �ٿ��ֱ�</b> 
		</td>
		<td align="right">

		</td>
	</tr>
	<tr><td colspan="2" height="1" background="<?php echo $g['img_module']?>/line_01.gif"></td></tr>
	<tr><td colspan="2" height="25"></td></tr>
</table>

<table width="100%" cellpadding="2" cellspacing="1" bgcolor="#dfdfdf"> 
	<tr bgcolor="#F5F5F5" height="200">
		<td bgcolor="#ffffff" colspan="3"> 

			<div id="xpreview" style="width:577px;height:202px;overflow:auto;padding:5px 5px 5px 5px;"></div>

		</td>
	</tr>
</table>

<br />
<table width="100%" cellspacing="0" cellpadding="0">
	<tr height="30">
		<td style="font-size:11px;font-family:dotum;color:#909090;">
			�������̳� �÷������Կ� �ҽ��ڵ带 �Ʒ��� �Է¶��� �ٿ�(Ctrl+v) �ֽ� �� ���� �̸�������� Ŭ���� �ּ���.
		</td>
	</tr>
</table>

<textarea id="content" rows="5" cols="70" onblur="srcShow();" onfocus="srcShow();" onchange="srcShow();" style="width:600px;height:120px;overflow:auto;padding:5px 5px 5px 5px;border:#dfdfdf solid 1px;background:#efefef;"></textarea>


<table width="100%" cellspacing="0" cellpadding="0">
	<tr><td height="15"></td></tr>
	<tr>
		<td height="30" align="center">

			<img src="<?php echo $g['img_module']?>/btn_aply.gif" hspace="5" style="cursor:pointer;" onclick="aplySrc()" />
			<img src="<?php echo $g['img_module']?>/btn_cancel.gif" style="cursor:pointer;" onclick="window.close();" />

		</td>
	</tr>
</table>
</form>



<script language="javascript">
//<![CDATA[
function srcShow()
{
	document.getElementById('xpreview').innerHTML = document.getElementById('content').value;
}
function aplySrc()
{
	var rcode = document.getElementById('xpreview').innerHTML;
	opener.EditDrop(rcode);
	window.close(); 
}
srcShow();
self.resizeTo(650,600);
//]]>
</script>
