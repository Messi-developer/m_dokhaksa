$(function(){

	//체크박스
	$('.content_con').find("on").removeClass('on');

	// ID 저장
	$(".input_chk>input[type=checkbox]").click(function(){
		if($(this).is(":checked")) {
			$(this).parent(".input_chk").addClass("on");
		}else{
			$(this).parent(".input_chk").removeClass("on");
		}
	});

	// toggle_btn();

	$(".icon-arr-down").click(function(){
		$(this).toggleClass("on");
	});

	// 체크박스 depth2 있을 때
	$(".chk_depth2").click(function () {
		var check = $(this).is(':checked');

		if(check){
			// console.log($(this).val());

			$(this).prop('checked', true);
			$(".depth2_box").hide();
			if ($(this).val() == '5' || $(this).val() == '6' || $(this).val() == '7') {
				$(".depth2_wrap").css('margin-bottom', '0px');
			} else {
				$(".depth2_wrap").css('margin-bottom','53px');
			}
			$(this).next().next(".depth2_box").show();
		}
	});


	// 회원가입 모두동의
	$('#agree_all').on('click', function() {
		if ($(this).is(':checked')) {
			$('#agree_all_bnt').addClass('on');
			$('.input_chk').each(function(){
				$(this).addClass('on')
				$(this).find('input[type=checkbox]').prop('checked', true);
			});
		} else {
			$('#agree_all_bnt').removeClass('on');
			$('.input_chk').each(function(){
				$(this).removeClass('on')
				$(this).find('input[type=checkbox]').prop('checked', false);
			});
		}
	})

	// 모두동의 항목체크
	$('.input_chk>input[type=checkbox]').on('click', function(){
		if ($('.input_chk>input[type=checkbox]:checked').size() != $('.input_chk').length) {
			$('#agree_all_bnt').removeClass('on');
		} else {
			$('#agree_all_bnt').addClass('on');
		}
	})

	// 회원가입시 아이디 유효성검사
	$('#user_id').on('change', function() {
		if (checkKorean($('#user_id').val()) == false) {
			alert('아이디는 숫자와 영문자만 사용가능 합니다.');
			$('#user_id').val('');
			return;
		}

		if ($(this).val() != '') {
			$('#user_id_feedback').hide();
		} else {
			$('#user_id_feedback').show();
		}
	})

	// 회원가입시 이름 유효성검사
	$('#name').on('change', function() {
		if ($(this).val().length < $(this).data('check-length')) {
			alert('이름은 한글 또는 영문으로 2자이상 입력해주세요.');
			$("#name_feedback").empty().append("이름은 한글 또는 영문으로 2자이상 입력해주세요.").show();
			$(this).val($(this).val().substr(0, $(this).attr('check-length')));
			return;
		} else {
			$("#name_feedback").hide();
		}
	})


	// 회원가입 생년월일
	$('#birth').on('change', function() {

		// 8자인지 확인
		if ($(this).val().length < $(this).attr('maxlength')) {
			alert('생년월일 혹은 휴대폰 번호를 정확히 입력해주세요.');
			$("#birth_feedback").empty().append("생년월일 혹은 휴대폰 번호를 정확히 입력해주세요.").show();
			$(this).val($(this).val().substr(0, $(this).attr('maxlength')));
			$('#joinSmsBtn').attr('onclick', ''); // SMS 인증막기
			// $('#searchJoinSmsBtn').attr('onclick', ''); // 비밀번호 찾기 SMS 인증막기
			return;
		} else {
			$('#joinSmsBtn').attr('onclick', 'joinSms()'); // SMS 인증막기
			// $('#searchJoinSmsBtn').attr('onclick', "searchJoinSms('search_tel')"); // 비밀번호 찾기 SMS 인증막기
		}

		if ($(this).val() != '') {
			$('#birth_feedback').hide();
		} else {
			$('#birth_feedback').show();
		}

		if(fncheckNum($('#birth').val()) === false) {
			alert('생년월일은 숫자만 입력가능합니다.');
			$('#birth').val('');
			$('#birth_feedback').show();
			return;
		}
	})

	// 비밀번호 체크
	$('#password_new, #password').on('change', function() {
		if ($(this).val() != '') {
			$('#password_new_feedback').hide();
		} else {
			$('#password_new_feedback').show();
		}
	})

	// 회원가입 휴대폰번호
	$('#handphone_index').on('change', function() {
		if ($(this).val().length < $(this).attr('maxlength')) {
			alert('휴대폰 번호를 정확히 입력해주세요.');
			$("#handphone_feedback").empty().append("휴대폰 번호를 정확히 입력해주세요.").show();
			$(this).val($(this).val().substr(0, $(this).attr('maxlength')));
			$('#joinSmsBtn').attr('onclick', ''); // SMS 인증막기
			return;
		} else {
			$('#joinSmsBtn').attr('onclick', 'joinSms()'); // SMS 인증막기
		}

		if ($(this).val() != '') {
			$('#handphone_feedback').hide();
		} else {
			$('#handphone_feedback').show();
		}

		if(fncheckNum($('#handphone_index').val()) === false) {
			alert('휴대폰 번호는 숫자만 입력가능합니다.');
			$('#handphone_index').val('');
			$('#handphone_feedback').show();
			return;
		}
	})

	// 회원 이메일 체크
	$('#email').on('change', function(){
		if ($(this).val() != '') {
			$('#email_feedback').hide();
		}

		if ($(this).val() == '') {
			alert('이메일을 입력해주세요.');
			$('#email_feedback').show();
		}

		if (checkKorean($('#email').val()) == false) {
			alert('이메일은 숫자와 영문자만 사용가능 합니다.');
			$('#email').val('');
			return;
		}
	})

	$('#join_email').on('change', function(){
		if (checkEmail($('#join_email').val()) == false) {
			alert('이메일은 숫자와 영문자만 사용가능 합니다.');
			$('#join_email').val('');
			$("#email_feedback").empty().append("이메일은 숫자와 영문자만 사용가능 합니다.").show();
			return;
		} else {
			$("#email_feedback").hide();
		}
	})

	// if ($('#certification_state').val() != '1') {
	// 	alert("휴대폰 인증을 진행해주세요.");
	// 	$('#handphone_index').focus();
	// 	return;
	// }

	// 가입경로 2개이상 복수선택 X
	$('.join_route').on('click', function(){
		var checked_box = $('.join_route:checked').size();
		if (checked_box >= 3) {
			// alert('최대 2개까지 복수 선택 가능 합니다.');
			$('#join_route_pop').show();
			$(this).prop('checked', false);
			return;
		}
	})

	// 회원가입 최종학력 입력 (기타)
	$('#level_edu').on('change', function(){
		if ($(this).val() == '15') {
			$('#level_edu_major').show();
		} else {
			$('#level_edu_major').hide();
		}
	})

    // 로그인 입력시 x버튼 클릭
    $('.value_clear').click(function () {
        $(this).parent().find('input').val('');
        $(this).hide();
    });


    // $('#join_email').change(function() {
    //     var state = $('#join_email option:selected').val();
    //     if ( state == '직접입력' ) {
    //         $('.email_input').show();
    //     }else {
    //         $('.email_input').hide();
	// 	}
    // })


	// 희망학과
	$('.chk_depth2').on('change', function () {
		if ($(this).val() != '') {
			$('#hope_majer_feedback').hide();
		}
	})

	// 수강목적
	$('.lec_object').on('change', function () {
		if ($(this).val() != '') {
			$('#lec_object_feedback').hide();
		}
	})

	// 가입경로
	$('.join_route').on('change', function () {
		if ($(this).val() != '') {
			$('#join_route_feedback').hide();
		}
	})


});

// function toggle_btn(el,etc){
// 	$(function(){
// 		$("."+etc).hide();
// 		$("#"+el).toggle();
// 	});
// }

// 회원가입(약관동의) step 01
function joinStepCheck() {

	var form = document.getElementById('joinStep_01');

	if ($('#agree').is(':checked') == false) {
		alert('이용약관 동의는 필수항목입니다.');
		$('#agree').focus();
		return;
	}

	if ($('#private_agree').is(':checked') == false) {
		alert('개인정보수집 및 이용동의는 필수항목입니다.');
		$('#private_agree').focus();
		return;
	}

	form.submit();
}

// 중복아이디 확인
function memberIdCheck() {
	var member_id = $('#user_id').val();

	if (member_id == '') {
		alert('아이디를 입력하세요.');
		$('#user_id_feedback').show();
		$('#user_id').focus();
		return;
	}

	if (checkSpecial(member_id) == false) {
		alert('사용할 수 없는 아이디입니다. 조건을 확인해주세요.');
		return;
	}

	$.ajax({
		url : "/member/main/memberIdCheck"
		,type : "POST"
		,dataType : "JSON"
		,data : {
			member_id : member_id
		},success : function(data) {
			alert(data.msg);
			if (data.result) {
				$('#useridCheck').val('Y'); // 아이디 중복확인 체크
				$("#user_id_feedback").empty().hide();
			} else {
				$("#user_id_feedback").empty().append("이미 사용중인 아이디입니다.").show();
			}
		}
	})
}

// 휴대폰인증
function joinSms() {

	var user_birth = $("#birth").val();
	var handphone = $("#handphone_index").val();

	if (user_birth == "") {
		alert('생년월일을 입력해주세요.');
		$("#birth_feedback").empty().append("생년월일을 입력해주세요.");
		return;
	}

	if(fncheckNum(user_birth) === false) {
		alert('생년월일은 숫자만 입력해 주세요.');
		$("#birth_feedback").empty().append("생년월일은 숫자만 입력해 주세요.");
		return;
	}

	if(handphone == "") {
		alert('휴대폰번호를 입력해주세요.');
		$("#handphone_feedback").empty().append("휴대폰번호를 입력해주세요.");
		return;
	}

	if (handphone.length > 11) {
		alert('생년월일 혹은 휴대폰 번호를 정확히 입력해주세요.');
		$("#handphone_feedback").empty().append("생년월일 혹은 휴대폰 번호를 정확히 입력해주세요.");
		return;
	}

	if(fncheckNum(handphone) === false) {
		alert('휴대폰번호는 숫자만 입력해 주세요.');
		$("#tip_user_birth").empty().append("휴대폰번호는 숫자만 입력해 주세요.");
		return;
	}

	$.ajax({
		url : "/member/main/memberHandPhone"
		,type : "POST"
		,dataType : "JSON"
		,data : {
			cert_type : 'sms'
			,user_birth : user_birth
			,handphone : handphone
		},success : function(data) {
			// console.log(data);
			alert(data.msg);
			if (data.result) {
				$('#member_num_check').show();
				$('#certification_id').val(data.send_code);
				$('#joinSmsCheck').val('Y');
			} else {
				$('#handphone_feedback').empty().append(data.msg).show();
			}
		}
	})
}

// 회원정보수정 휴대폰인증
function memberInfojoinSms() {

	var user_birth = $("#birth").val();
	var handphone = $("#handphone_index").val();

	if (user_birth == "") {
		alert('생년월일을 입력해주세요.');
		$("#birth_feedback").empty().append("생년월일을 입력해주세요.");
		return;
	}

	if (user_birth.length > 8) {
		alert('생년월일 혹은 휴대폰 번호를 정확히 입력해주세요.');
		$("#birth_feedback").empty().append("생년월일 혹은 휴대폰 번호를 정확히 입력해주세요.");
		return;
	}

	if(fncheckNum(user_birth) === false) {
		alert('생년월일은 숫자만 입력해 주세요.');
		$("#birth_feedback").empty().append("생년월일은 숫자만 입력해 주세요.");
		return;
	}

	if(handphone == "") {
		alert('휴대폰번호를 입력해주세요.');
		$("#handphone_feedback").empty().append("휴대폰번호를 입력해주세요.");
		return;
	}

	if (handphone.length > 11) {
		alert('생년월일 혹은 휴대폰 번호를 정확히 입력해주세요.');
		$("#handphone_feedback").empty().append("생년월일 혹은 휴대폰 번호를 정확히 입력해주세요.");
		return;
	}

	if(fncheckNum(handphone) === false) {
		alert('휴대폰번호는 숫자만 입력해 주세요.');
		$("#tip_user_birth").empty().append("휴대폰번호는 숫자만 입력해 주세요.");
		return;
	}

	$.ajax({
		url : "/member/main/memberInfoHandPhone"
		,type : "POST"
		,dataType : "JSON"
		,data : {
			cert_type : 'sms'
			,user_birth : user_birth
			,handphone : handphone
		},success : function(data) {
			// console.log(data);
			alert(data.msg);
			if (data.result) {
				$('#member_num_check').show();
				$('#certification_id').val(data.send_code);
				$('#joinSmsCheck').val('Y');
			}
		}
	})

}


// 휴대폰인증
function searchJoinSms(searchType) {
	var member_id = $("#user_id").val();
	var member_name = $("#name").val();
	var member_birth = $("#birth").val();
	var member_email = $("#email").val();
	var handphone = $("#handphone_index").val();

	if (searchType == 'search_email') {
		if (member_email == "") {
			alert('이메일을 입력해주세요.');
			$('#email').focus();
			$("#email_feedback").empty().append("이메일을 입력해주세요.").show();
			return;
		}
	}

	if (member_id == "") {
		alert('아이디를 입력해주세요.');
		$('#user_id').focus();
		$("#id_feedback").empty().append("아이디를 입력해주세요.").show();
		return;
	}

	if (member_name == "") {
		alert('이름을 입력해주세요.');
		$('#name').focus();
		$("#name_feedback").empty().append("이름을 입력해주세요.").show();
		return;
	}

	if (member_birth == "") {
		alert('생년월일을 입력해주세요.');
		$('#birth').focus();
		$("#birth_feedback").empty().append("생년월일을 입력해주세요.").show();
		return;
	}

	if(fncheckNum(member_birth) === false) {
		alert('생년월일은 숫자만 입력해 주세요.');
		$('#birth').focus();
		$("#birth_feedback").empty().append("생년월일은 숫자만 입력해 주세요.").show();
		return;
	}

	if(handphone == "") {
		alert('휴대폰번호를 입력해주세요.');
		$('#handphone_index').focus();
		$("#handphone_feedback").empty().append("휴대폰번호를 입력해주세요.").show();
		return;
	}

	if(fncheckNum(handphone) === false) {
		alert('휴대폰번호는 숫자만 입력해 주세요.');
		$('#handphone_index').focus();
		$("#handphone_feedback").empty().append("휴대폰번호는 숫자만 입력해 주세요.").show();
		return;
	}

	$.ajax({
		url : "/member/main/memberHandPhoneSearch"
		,type : "POST"
		,dataType : "JSON"
		,data : {
			cert_type : 'sms'
			,searchType : searchType
			,handphone : handphone
			,member_id : member_id
			,member_name : member_name
			,member_birth : member_birth
			,email : member_email
			,join_email : $('#join_email').val()
		},success : function(data) {
			// console.log(data);
			alert(data.msg);

			if (data.result) {
				$('#member_num_check').show();
				$('#certification_id').val(data.send_code);
				$('#joinSmsCheck').val('Y');

				cert_time_start(10);
			}
		}
	})

}

// 휴대폰인증 (비밀번호 찾기)
function searchJoinSms_certification(searchType)
{
	var insert_id = $('#certification_id').val();
	var send_code = $('#send_code').val();

	var member_id = $("#user_id").val();
	var member_name = $("#name").val();
	var member_birth = $("#birth").val();
	var handphone = $("#handphone_index").val();

	if (member_id == '') {
		alert('아이디를 입력해주세요.');
		$('#user_id').focus();
		$("#id_feedback").empty().append("아이디를 입력해주세요.").show();
		return;
	}

	if (member_name == '') {
		alert('이름을 입력해주세요.');
		$('#name').focus();
		$("#name_feedback").empty().append("이름을 입력해주세요.").show();
		return;
	}

	if (member_birth == '') {
		alert('생년월일을 입력해주세요.');
		$('#birth').focus();
		$("#birth_feedback").empty().append("생년월일은 숫자만 입력해 주세요.").show();
		return;
	}

	if ($('#email').val() == '') {
		alert('이메일을 입력해주세요.');
		$('#email').focus();
		$("#email_feedback").empty().append("이메일을 입력해주세요.").show();
		return;
	}

	if (handphone == '') {
		alert('핸드폰번호를 입력해주세요.');
		$('#handphone_index').focus();
		$("#handphone_feedback").show();
		return;
	}

	if (searchType == 'search_email') {
		if ($('#email').val() == '') {
			alert('이메일을 입력해주세요.');
			$('#email').focus();
			return;
		}
	}

	if (send_code == '') {
		alert('인증번호를 입력해주세요.');
		$('#send_code').focus();
		return;
	}

	$.ajax({
		url : "/member/main/memberSearchInfo"
		,type : "POST"
		,dataType : "JSON"
		,data : {
			insert_id : insert_id
			,send_code : send_code
			,searchType : searchType
			,member_id : member_id
			,member_name : member_name
			,member_birth : member_birth
			,handphone : handphone
			,email : $('#email').val()
			,join_email : $('#join_email').val()

		},success : function(data) {

			// console.log(data);

			if (data.result) {
				$('#passwordForm').hide();
				$('#passwordChangeForm, .result_box').show();
				$('#data_memberNo').val(data.member_info.no);
				$('#data_memberId').html(data.member_info.user_id);
				$('#data_memberName').html(data.member_info.name);
			} else {
				alert(data.msg);
			}
		}
	})

}

// 이메일 (비밀번호 찾기)
function searchMemberInfo(searchType){

	var member_id = $("#user_id").val();
	var member_name = $("#name").val();
	var member_birth = $("#birth").val();
	var email = $("#email").val();

	if (member_id == "") {
		alert('아이디를 입력해주세요.');
		$("#id_feedback").empty().append("아이디를 입력해주세요.");
		return;
	}

	if (member_name == "") {
		alert('이름을 입력해주세요.');
		$("#name_feedback").empty().append("이름을 입력해주세요.");
		return;
	}

	if (member_birth == "") {
		alert('생년월일을 입력해주세요.');
		$("#birth_feedback").empty().append("생년월일을 입력해주세요.");
		return;
	}

	if(fncheckNum(member_birth) === false) {
		alert('생년월일은 숫자만 입력해 주세요.');
		$("#birth_feedback").empty().append("생년월일은 숫자만 입력해 주세요.");
		return;
	}

	if(email == "") {
		alert('이메일을 입력해주세요.');
		$("#email_feedback").empty().append("휴대폰번호를 입력해주세요.");
		return;
	}

	$.ajax({
		url : "/member/main/memberSearchInfo"
		,type : "POST"
		,dataType : "JSON"
		,data : {
			searchType : searchType
			,member_id : member_id
			,member_name : member_name
			,member_birth : member_birth
			,email : email
		},success : function(data) {
			// console.log(data);

			if (data.result) {
				$('#passwordForm').hide();
				$('#passwordChangeForm, .result_box').show();
				$('#data_memberNo').val(data.member_info.no);
				$('#data_memberId').html(data.member_info.user_id);
				$('#data_memberName').html(data.member_info.name);

				$('#passwordChangePop').show();
			} else {
				alert(data.msg);
			}
		}
	})

}

// 유저 비밀번호 변경
function memberInfoUpdate(update_type)
{
	var memberNo = $('#data_memberNo').val();
	var password_new = $('#password_new').val();
	var password = $('#password').val();

	//console.log(memberNo);

	if (memberNo == "") {
		alert('필수값이 없습니다. 휴대폰인증부터 다시해주세요.');
		location.href='/member/search?searchType=password&search=tel';
		return;
	}

	if(password_new == "") {
		alert("비밀번호를 입력하세요.");
		$("#password_new").focus();
		$("#password_new_feedback").empty().append("비밀번호를 입력해 주세요.");
		return;
	}

	if(password_new.length < 10){
		alert("비밀번호는 10이상 32자 이하로 작성해주세요.");
		$("#password_new").focus();
		$("#password_new_feedback").empty().append("비밀번호는 10이상 32자 이하로 작성해주세요.");
		return;;
	}

	if(password_new.length > 32){
		alert("비밀번호는 10이상 32자 이하로 작성해주세요.");
		$("#password_new").focus();
		$("#password_new_feedback").empty().append("비밀번호는 10이상 32자 이하로 작성해주세요.");
		return;;
	}

	if(!fnCheckPassword(password_new)) {
		$("#password_new").focus();
		return;
	}

	if( password_new != password ) {
		alert("비밀번호확인과 맞지 않습니다. 다시 확인해주세요.");
		// $("#useridCheck").value('');
		$("#password_new").focus();
		$("#password_new_feedback").empty().append("비밀번호확인과 맞지 않습니다. 다시 확인해주세요.");
		return;
	}

	$.ajax({
		url : "/member/main/memberInfoUpdate"
		,type : "POST"
		,dataType : "JSON"
		,data : {
			update_type : update_type
			,memberNo : memberNo
			,password_new : password_new
			,password : password
		},success : function(data) {
			// alert(data.msg);
			if (data.result) {
				$('#passwordChangePop').show(); // 비밀번호 변경팝업 노출
				// location.href='/login/main';
			}
		}

	})

}

function cert_time_start(duration) {
	timer = duration * 60;
	var minutes, seconds;
	// clearInterval(interval);
	interval = setInterval(function(){
		minutes = parseInt(timer / 60 % 60, 10);
		seconds = parseInt(timer % 60, 10);

		minutes = minutes < 10 ? "0" + minutes : minutes;
		seconds = seconds < 10 ? "0" + seconds : seconds;

		$("#timer").text(minutes + ":" + seconds);

		if (--timer < 0) {
			timer = 0;
			// clearInterval(interval);
		}
	}, 1000);
}


// 휴대폰인증 번호확인
function joinSms_certification()
{
	var insert_id = $('#certification_id').val();
	var send_code = $('#send_code').val();

	if (send_code == '') {
		alert('인증번호를 입력해주세요.');
		$('#send_code').focus();
		return;
	}


	$.ajax({
		url : "/member/main/memberCertification"
		,type : "POST"
		,dataType : "JSON"
		,data : {
			insert_id : insert_id
			,send_code : send_code
		},success : function(data) {
			alert(data.msg);
			if (data.result) {
				$('#certification_state').val('1');
				$('#handphone_feedback').hide();
			} else {
				$('#certification_state').val('');
				$('#handphone_feedback').show();
			}
		}
	})

}

// 추천아이디 조회
function memberRecommender() {

	if ($('#recommender').val() == '') {
		alert('추천인 아이디를 입력해주세요.');
		$('#recommender').focus();
		return;
	}

	$.ajax({
		url : "/member/main/memberRecommender"
		,type : "POST"
		,dataType : "JSON"
		,data : {
			user_id : $('#recommender').val()
		},success : function(data) {
			alert(data.msg);
			if (data.result) {
				$('#recommenderCheck').val('Y');
				$('#recommender_feedback').hide();
			} else {
				$('#recommender_feedback').show();
			}
		}
	})
}

// 회원가입 최종승인
function joinStepFinsh()
{
	var form = document.joinStep_02;
	var name = $("#name").val(); // 이름

	if (name == "") {
		alert("이름을 입력하세요.");
		$("#name").focus();
		$("#name_feedback").empty().append("이름을 입력해 주세요.").show();
		return;
	}

	var user_birth = $("#birth").val(); // 생년월일

	if (user_birth == "") {
		alert("생년월일을 입력하세요.");
		$("#birth").focus();
		$("#birth_feedback").empty().append("생년월일을 입력해 주세요.").show();
		return;
	}

	if (user_birth.length != 8) {
		alert("생년월일을 정확히 입력하세요.");
		$("#birth").focus();
		$("#birth_feedback").empty().append("생년월일을 정확히 입력해 주세요.").show();
		return;
	}

	if (fncheckNum(user_birth) === false) {
		alert("생년월일은 숫자만 입력하세요.");
		$("#birth").focus();
		$("#birth_feedback").empty().append("생년월일은 숫자만 입력하세요.").show();
		return;
	}

	if ($('#user_id').val() == '') {
		alert("아이디를 입력하세요.");
		$("#user_id").focus();
		$("#user_id_feedback").empty().append("아이디를 입력하세요.").show();
		return;
	}

	if ($('#useridCheck').val() == '') {
		alert('아이디 중복체크 해주세요.');
		$('#user_id').focus();
		$("#user_id_feedback").empty().append("아이디 중복체크 해주세요.").show();
		return;
	}

	var password_new = $("#password_new").val();
	var password = $("#password").val();

	if(password_new == "") {
		alert("비밀번호를 입력하세요.");
		$("#password_new").focus();
		$("#password_new_feedback").empty().append("비밀번호를 입력해 주세요.").show();
		return;
	}

	if(password_new.length < 10){
		alert("비밀번호는 10이상 32자 이하로 작성해주세요.");
		$("#password_new").focus();
		$("#password_new_feedback").empty().append("비밀번호는 10이상 32자 이하로 작성해주세요.").show();
		return;;
	}

	if(password_new.length > 32){
		alert("비밀번호는 10이상 32자 이하로 작성해주세요.");
		$("#password_new").focus();
		$("#password_new_feedback").empty().append("비밀번호는 10이상 32자 이하로 작성해주세요.").show();
		return;;
	}

	if(!fnCheckPassword(password_new)) {
		$("#password_new").focus();
		return;
	}

	if( password_new != password ) {
		alert("비밀번호확인과 맞지 않습니다. 다시 확인해주세요.");
		// $("#useridCheck").value('');
		$("#password_new").focus();
		$("#password_new_feedback").empty().append("비밀번호확인과 맞지 않습니다. 다시 확인해주세요.").show();
		return;
	}

	if ($('#email').val() == '') {
		alert('이메일을 입력해주세요.');
		$("#email_feedback").empty().append("이메일을 입력해주세요.").show();
		$('#email').focus();
		return;
	}

	if ($('#join_email').val() == '') {
		alert('이메일을 입력해주세요.');
		$("#email_feedback").empty().append("이메일을 입력해주세요.").show();
		$('#email').focus();
		return;
	}

	if ($('#certification_state').val() == '') {
		alert('휴대폰 인증을 진행하여 주세요.');
		$('#handphone_index').focus();
		$('#handphone_feedback').empty().append('휴대폰 인증을 진행하여 주세요.').show();
		return;
	}

	if ($('#uno_new').val() == '' && $('#home_address').val() == '' && $('#tail_address').val() == '') {
		alert('주소를 입력해주세요.');
		$('#uno_new').focus();
		$('#uno_new_feedback').empty().append('주소를 입력해주세요.').show();
		return;
	}

	if ($('.chk_depth2').is(':checked') == false) {
		alert('희망학과를 선택해주세요.');
		$('#hope_majer').focus();
		$('#hope_majer_feedback').empty().append("희망학과를 선택해주세요.").show();
		return;
	}

	// if ($('.lec_object').is(':checked') == false) {
	// 	alert('수강목적을 선택해주세요.');
	// 	$('#lec_object').focus();
	// 	$('#lec_object_feedback').empty().append("수강목적을 선택해주세요.").show();
	// 	return;
	// }

	// if ($('.join_route').is(':checked') == false) {
	// 	alert('가입경로를 선택해주세요.');
	// 	$('#join_route').focus();
	// 	$('#join_route_feedback').empty().append("가입경로를 선택해주세요.").show();
	// 	return;
	// }

	if ($('#recommender').val() != '' && $('#recommenderCheck').val() == '') {
		alert('추천아이디 조회 버튼을 클릭해주세요.');
		$('#recommender').focus();
		return;
	}

	form.submit();
}

// 회원정보 수정
function memberInfoUpdateForm(member_no)
{
	var form = document.getElementById('memberInfo');

	var password_new = $("#password_new").val();
	var password = $("#password").val();

	if (member_no == "") {
		alert("필수값이 없습니다. 관리자에 문의해주세요.");
		location.href='/';
		return;
	}

	if(password_new == "") {
		alert("비밀번호를 입력하세요.");
		$("#password_new").focus();
		$("#password_new_feedback").empty().append("비밀번호를 입력해 주세요.").show();
		return;
	}

	if(password_new.length < 10){
		alert("비밀번호는 10이상 32자 이하로 작성해주세요.");
		$("#password_new").focus();
		$("#password_new_feedback").empty().append("비밀번호는 10이상 32자 이하로 작성해주세요.").show();
		return;;
	}

	if(password_new.length > 32){
		alert("비밀번호는 10이상 32자 이하로 작성해주세요.");
		$("#password_new").focus();
		$("#password_new_feedback").empty().append("비밀번호는 10이상 32자 이하로 작성해주세요.").show();
		return;;
	}

	if(!fnCheckPassword(password_new)) {
		$("#password_new").focus();
		return;
	}

	if( password_new != password ) {
		alert("비밀번호확인과 맞지 않습니다. 다시 확인해주세요.");
		$("#password_new").focus();
		$("#password_new_feedback").empty().append("비밀번호확인과 맞지 않습니다. 다시 확인해주세요.").show();
		return;
	}

	if ($('#certification_state').val() != '1') {
		alert("휴대폰 인증을 진행해주세요.");
		$('#handphone_index').focus();
		$('#handphone_feedback').empty().append('휴대폰 인증을 진행해주세요.').show();
		return;
	}

	form.submit();
}

// 로그인
function memberLogin()
{
	var user_id = $('#user_id').val();
	var password = $('#password').val();
	var form = $("#loginform");

	if (user_id == '') {
		alert('아이디를 입력해주세요.');
		$('#user_id').focus();
		return;
	}
	if (password == '') {
		alert('비밀번호를 입력해주세요.');
		$('#password').focus();
		return;
	}

	// console.log(form);
	form.submit();
}

// 로그아웃
function memberLogOut(member_id)
{
	if (confirm('로그아웃 하시겠습니까?')) {
		$.ajax({
			url : "/login/main/memberLogOut"
			,type : "POST"
			,dataType : "JSON"
			,data : {
				member_id : member_id
			},success : function(data) {
				alert(data.msg);
				location.href='/'
			}
		})
	}
}

// 휴면회원 일반회원으로 전환
function restMemberAgain(no, member_id)
{
	$.ajax({
		url : "/login/main/restMemberAgain"
		,type : "POST"
		,dataType : "JSON"
		,data : {
			no : no
			, member_id : member_id
		},success : function(data) {
			alert(data.msg);
			location.href='/login/main';
		}
	})
}

// 회원가입 취소버튼
function joinBack()
{
	if (confirm('회원가입을 취소하시겠습니까?')) {
		location.href='/login/main';
	} else {
		return;
	}
}

// 로그인(편입)
function memberUtLogin()
{
	var user_id = $('#user_id').val();
	var password = $('#password').val();

	if (user_id == '') {
		alert('아이디를 입력해주세요.');
		$('#user_id').focus();
		return;
	}
	if (password == '') {
		alert('비밀번호를 입력해주세요.');
		$('#password').focus();
		return;
	}

	$.ajax({
		url : "/login/main/memberUtLogin"
		,type : "POST"
		,dataType : "JSON"
		,data : {
			user_id : user_id
			, password : password
		},success : function(data) {
			if(data.code  == "OK") {
				window.location.href = "/member/main?pageType=ut&step=02";
			} else if(data.code  == "SLEEP") {
				alert("휴면계정 입니다.");
			} else if(data.code  == "LIMIT") {
				alert("회원정보 조회 횟수(5회)를 초과하였습니다.");
			} else {
				alert("가입된 회원 정보가 없거나 잘못된 비밀번호 입니다.\r\n다시 확인 바랍니다.");
			}
		}
	})

}

// 편입회원가입
function user_search_agree()
{
	if ($('#user_search_agree').is(':checked') == false) {
		alert('개인정보 제공 및 공유에 동의 체크해주세요.');
		$('#user_search_agree').focus();
		return;
	}

	location.href='/member/main?pageType=ut&step=03';
}

// 아이디찾기
function searchMemberId(searchType)
{
	var user_name = $('#name').val();
	var birth = $('#birth').val();
	var handphone_index = $('#handphone_index').val();
	var email = $('#email').val();

	if (user_name == '') {
		alert('이름을 입력해주세요.');
		$('#name').focus();
		$('#name_feedback').show();
		return;
	}

	if (birth == '') {
		alert('생년월일을 입력해주세요.');
		$('#birth').focus();
		$('#birth_feedback').show();
		return;
	}

	if (searchType == 'search_tel') {
		if (handphone_index == '') {
			alert('휴대폰번호를 입력해주세요.');
			$('#handphone_index').focus();
			$('#handphone_feedback').show();
			return;
		}
	} else {
		if (email == '') {
			alert('이메일을 입력해주세요.');
			$('#email').focus();
			return;
		}
	}


	$.ajax({
		url : '/member/main/searchMemberId'
		,type : "POST"
		,dataType : "JSON"
		,data : {
			user_name : user_name
			,birth : birth
			,handphone_index : handphone_index
			,email : email
			,join_email : $('#join_email').val()
			,searchType : searchType

		}, success : function(data){
			// console.log(data);
			if (data.result) {
				var searchCheck = '';

				$('.find_form, .btn_area').hide();
				$('#searchSuccess, #searchBtnContent').show();
				$('#searchName').html(data.search_data.name);
				$('#searchId').html(data.search_data.user_id);
				$('#searchCheck').html('');
				searchCheck += "<a href=\"/member/search?searchType=password&search=tel\" class=\"btn gray\">비밀번호찾기</a>";
				$('#searchCheck').append(searchCheck);
			} else {
				$('.find_form, .btn_area').hide();
				$('#searchFail, #searchBtnContent').show();
			}
		}
	})

}

// enter key
function enterkey(t) {
    $(t).parent().find('.value_clear').show();
    if (window.event.keyCode == 13) {
        memberLogin();
    }
}

// 편입 로그인
function enterkey_ut(t) {
    $(t).parent().find('.value_clear').show();
}