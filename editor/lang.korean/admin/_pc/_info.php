

<div id="infobox">

	<h2>��� �⺻����</h2>
	<table>
	<tr>
		<td class="td1">����</td>
		<td>:</td>
		<td class="td2"><?php echo $MD['name']?></td>
	</tr>
	<tr>
		<td class="td1">�����̵�</td>
		<td>:</td>
		<td class="td2"><?php echo $MD['id']?></td>
	</tr>
	<tr>
		<td class="td1">�������ġ</td>
		<td>:</td>
		<td class="td2"><?php echo $g['path_module'].$module?>/</td>
	</tr>
	<tr>
		<td class="td1">���̺����</td>
		<td>:</td>
		<td class="td2">
			<?php if($MD['tblnum']):?>
			<?php echo $MD['tblnum']?>��
			<?php else:?>
			����
			<?php endif?>
		</td>
	</tr>
	<tr>
		<td class="td1">�������</td>
		<td>:</td>
		<td class="td2">
			<?php echo getDateFormat($MD['d_regis'],'Y/m/d')?>
		</td>
	</tr>
	<tr>
		<td class="td1">����</td>
		<td>:</td>
		<td class="td2">
			1.0
		</td>
	</tr>
	<tr>
		<td class="td1">�ֱپ�����Ʈ</td>
		<td>:</td>
		<td class="td2">
			2011.02.08
		</td>
	</tr>
	</table>



	<h2>���ۻ� ����</h2>
	<table>
	<tr>
		<td class="td1">���ۻ�</td>
		<td>:</td>
		<td class="td2">�����</td>
	</tr>
	<tr>
		<td class="td1">ȸ�����̵�</td>
		<td>:</td>
		<td class="td2">�����(kims)</td>
	</tr>
	<tr>
		<td class="td1">�̸���</td>
		<td>:</td>
		<td class="td2"><a href="mailto:admin@kimsq.com">admin@kimsq.com</a></td>
	</tr>
	<tr>
		<td class="td1">Ȩ������</td>
		<td>:</td>
		<td class="td2">
			<a href="http://www.kimsq.co.kr" target="_blank">www.kimsq.co.kr</a>
		</td>
	</tr>
	<tr>
		<td class="td1">���̼���</td>
		<td>:</td>
		<td class="td2">
			LGPL
		</td>
	</tr>
	</table>



</div>
