$(function(){
	$('input[name=lecture_name]').on('change', function() {
		$('#paymentLectureNo').val($(this).val());
	})

	$("input[name='c_id[]']").keyup(function(){
		if(/[��-��|��-��|��-��]/.test($(this).val())){
			alert('�ѱ��� ������ �� �����ϴ�.');
			$(this).val('');
		}
	});
});


// ���������� ����
function paymentLecture()
{
	paymentApply($('#paymentLectureNo').val());
}
