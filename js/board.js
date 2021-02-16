$(document).ready(function() {

	// 강좌, 교재 선택확인 (learning_qna_write)
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
					apeendList += "<option value=\"\">수강 중인 강의 / 교재 중 질문 대상 강의를 선택해주세요.</option>";
					for(var i=0; i<data.selectBoxOption.length; i++) {
						apeendList += "<option value=\""+ data.selectBoxOption[i].no +"\">"+ data.selectBoxOption[i].mem_lec_name +"</option>";
					}

					$('#select_lec_no').html('');
					$('#select_lec_no').append(apeendList);
				} else {
					apeendList += "<option value=\"\">수강 중인 강의 / 교재 중 질문 대상 강의가 없습니다. 확인해주세요.</option>";
					$('#select_lec_no').html('');
					$('#select_lec_no').append(apeendList);
				}
			}
		})
	})

	// 강좌명/교재명 선택시 작성자, 교수명, 단계 표시
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
						level_text = "1단계";
						level = '1';
					} else if (data.lectureInfo.lec_level2 == '1') {
						level_text = "2단계";
						level = '2';
					} else if (data.lectureInfo.lec_level2 == '3') {
						level_text = "3단계";
						level = '3';
					} else if (data.lectureInfo.lec_level2 == '4') {
						level_text = "4단계";
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
		alert('필수항목을 입력해주세요.');
		$('#select_lec_no').focus();
		return;
	}

	if (($('#subject').val() == '' || $('#subject').val() == undefined || $('#subject').val() == null)) {
		alert('필수항목을 입력해주세요.');
		$('#subject').focus();
		return;
	}

	if (($('#memo').val() == '' || $('#memo').val() == undefined || $('#memo').val() == null)) {
		alert('필수항목을 입력해주세요.');
		$('#memo').focus();
		return;
	}

	if (confirm('질문을 등록 하시겠습니까?')) {
		$('#HKboardfromSubmit').submit();
	} else {
		location.reload();
		return;
	}
}