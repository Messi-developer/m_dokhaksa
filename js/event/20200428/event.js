function choice_lec() {
	var lec_no = "";
	if ($("#checkRadion1").is(":checked")) {
		lec_no = $("#checkRadion1").val();
		$("#view_price").empty().append("139,000");
		$('#paymentLectureNo').val(lec_no);
	}

	if ($("#checkRadion2").is(":checked")) {
		// alert('7/18 TESAT ���������� �����Ǿ� ����� ���ñ� �����Ը� ������û �����մϴ�.');
		// $("#checkRadion2").prop("checked",false);
		// $("#checkRadion1").prop("checked",true);
		// return;
		lec_no = $("#checkRadion2").val();
		$('#paymentLectureNo').val(lec_no);
		$("#view_price").empty().append("159,000");
	}

	if (lec_no == "") {
		alert("���ñ��� �����ϼ���.");
		return;
	}
};

// ���������� �̵�
function paymentLecture()
{
	paymentApply($('#paymentLectureNo').val());
}