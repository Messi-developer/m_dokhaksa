$(document).ready(function() {

	// ����, ���� ����Ȯ�� (learning_qna_write)
	$('.select_type').on('change', function(){
		// console.log($(this).val());
		$('#check_select_type').val($(this).val());

		$.ajax({
			url : "/myclass/learning_qna/learning_qna_option"
			,type : "POST"
			,dataType : "JSON"
			,data : {
				check_select_type : $('#check_select_type').val()
			}, success : function (data){
				console.log(data);
				var apeendList = "";
				if (data.result) {
					apeendList += "<option value=\"\">���� ���� ���� / ���� �� ���� ��� ���Ǹ� �������ּ���.</option>";
					for(var i=0; i<data.selectBoxOption.length; i++) {
						apeendList += "<option value=\""+ data.selectBoxOption[i].no +"\">"+ data.selectBoxOption[i].mem_lec_name +"</option>";
					}

					$('#select_lec_no').html('');
					$('#select_lec_no').append(apeendList);
				} else {
					apeendList += "<option value=\"\">���� ���� ���� / ���� �� ���� ��� ���ǰ� �����ϴ�. Ȯ�����ּ���.</option>";
					$('#select_lec_no').html('');
					$('#select_lec_no').append(apeendList);
				}
			}
		})
	})

	// ���¸�/����� ���ý� �ۼ���, ������, �ܰ� ǥ��
	$('#select_lec_no').on('change', function (){
		$.ajax({
			url: "/myclass/learning_qna/learning_qna_lecture_info"
			, type : "POST"
			, dataType : "JSON"
			, data: {
				lecMemNo : $(this).val()
			}, success: function (data) {
				if (data.result) {
					// console.log(data);
					var level = "";
					var level_text = "";
					if (data.lectureInfo.lec_level1 == '1') {
						level_text = "1�ܰ�";
						level = '1';
					} else if (data.lectureInfo.lec_level2 == '1') {
						level_text = "2�ܰ�";
						level = '2';
					} else if (data.lectureInfo.lec_level2 == '3') {
						level_text = "3�ܰ�";
						level = '3';
					} else if (data.lectureInfo.lec_level2 == '4') {
						level_text = "4�ܰ�";
						level = '4';
					}

					$('#teacher_name').html(data.lectureInfo.teacher_name);
					$('#lecture_level').html(level_text);
					$('#islevel').val(level);
				} else {
					$('#teacher_name').html('-');
					$('#lecture_level').html('-');
					$('#islevel').val(1);
				}
			}
		})
	})

})

// HKboard_ID INSERT
function HKboardInert() {

	if (($('#select_lec_no').val() == '' || $('#select_lec_no').val() == undefined || $('#select_lec_no').val() == null)) {
		alert('�ʼ��׸��� �Է����ּ���.');
		$('#select_lec_no').focus();
		return;
	}

	if (($('#subject').val() == '' || $('#subject').val() == undefined || $('#subject').val() == null)) {
		alert('�ʼ��׸��� �Է����ּ���.');
		$('#subject').focus();
		return;
	}

	if (($('#memo').val() == '' || $('#memo').val() == undefined || $('#memo').val() == null)) {
		alert('�ʼ��׸��� �Է����ּ���.');
		$('#memo').focus();
		return;
	}

	if (confirm('������ ��� �Ͻðڽ��ϱ�?')) {
		$('#HKboardfromSubmit').submit();
	} else {
		location.reload();
		return;
	}
}