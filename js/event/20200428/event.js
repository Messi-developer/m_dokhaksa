function choice_lec() {
	var lec_no = "";
	if ($("#checkRadion1").is(":checked")) {
		lec_no = $("#checkRadion1").val();
		$("#view_price").empty().append("139,000");
		$('#paymentLectureNo').val(lec_no);
	}

	if ($("#checkRadion2").is(":checked")) {
		// alert('7/18 TESAT 응시접수가 마감되어 현재는 응시권 미포함만 수강신청 가능합니다.');
		// $("#checkRadion2").prop("checked",false);
		// $("#checkRadion1").prop("checked",true);
		// return;
		lec_no = $("#checkRadion2").val();
		$('#paymentLectureNo').val(lec_no);
		$("#view_price").empty().append("159,000");
	}

	if (lec_no == "") {
		alert("응시권을 선택하세요.");
		return;
	}
};

// 결제페이지 이동
function paymentLecture()
{
	paymentApply($('#paymentLectureNo').val());
}