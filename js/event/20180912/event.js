$(function(){
	$('input[name=lecture_name]').on('change', function() {
		$('#paymentLectureNo').val($(this).val());
	})

	$("input[name='c_id[]']").keyup(function(){
		if(/[ㄱ-ㅎ|ㅏ-ㅣ|가-히]/.test($(this).val())){
			alert('한글은 작정할 수 없습니다.');
			$(this).val('');
		}
	});
});


// 결제페이지 연결
function paymentLecture()
{
	paymentApply($('#paymentLectureNo').val());
}
