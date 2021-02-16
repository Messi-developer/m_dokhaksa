$(function(){

	//üũ�ڽ�
	$('.content_con').find("on").removeClass('on');

	// ID ����
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

	// üũ�ڽ� depth2 ���� ��
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


	// ȸ������ ��ε���
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

	// ��ε��� �׸�üũ
	$('.input_chk>input[type=checkbox]').on('click', function(){
		if ($('.input_chk>input[type=checkbox]:checked').size() != $('.input_chk').length) {
			$('#agree_all_bnt').removeClass('on');
		} else {
			$('#agree_all_bnt').addClass('on');
		}
	})

	// ȸ�����Խ� ���̵� ��ȿ���˻�
	$('#user_id').on('change', function() {
		if (checkKorean($('#user_id').val()) == false) {
			alert('���̵�� ���ڿ� �����ڸ� ��밡�� �մϴ�.');
			$('#user_id').val('');
			return;
		}

		if ($(this).val() != '') {
			$('#user_id_feedback').hide();
		} else {
			$('#user_id_feedback').show();
		}
	})

	// ȸ�����Խ� �̸� ��ȿ���˻�
	$('#name').on('change', function() {
		if ($(this).val().length < $(this).data('check-length')) {
			alert('�̸��� �ѱ� �Ǵ� �������� 2���̻� �Է����ּ���.');
			$("#name_feedback").empty().append("�̸��� �ѱ� �Ǵ� �������� 2���̻� �Է����ּ���.").show();
			$(this).val($(this).val().substr(0, $(this).attr('check-length')));
			return;
		} else {
			$("#name_feedback").hide();
		}
	})


	// ȸ������ �������
	$('#birth').on('change', function() {

		// 8������ Ȯ��
		if ($(this).val().length < $(this).attr('maxlength')) {
			alert('������� Ȥ�� �޴��� ��ȣ�� ��Ȯ�� �Է����ּ���.');
			$("#birth_feedback").empty().append("������� Ȥ�� �޴��� ��ȣ�� ��Ȯ�� �Է����ּ���.").show();
			$(this).val($(this).val().substr(0, $(this).attr('maxlength')));
			$('#joinSmsBtn').attr('onclick', ''); // SMS ��������
			// $('#searchJoinSmsBtn').attr('onclick', ''); // ��й�ȣ ã�� SMS ��������
			return;
		} else {
			$('#joinSmsBtn').attr('onclick', 'joinSms()'); // SMS ��������
			// $('#searchJoinSmsBtn').attr('onclick', "searchJoinSms('search_tel')"); // ��й�ȣ ã�� SMS ��������
		}

		if ($(this).val() != '') {
			$('#birth_feedback').hide();
		} else {
			$('#birth_feedback').show();
		}

		if(fncheckNum($('#birth').val()) === false) {
			alert('��������� ���ڸ� �Է°����մϴ�.');
			$('#birth').val('');
			$('#birth_feedback').show();
			return;
		}
	})

	// ��й�ȣ üũ
	$('#password_new, #password').on('change', function() {
		if ($(this).val() != '') {
			$('#password_new_feedback').hide();
		} else {
			$('#password_new_feedback').show();
		}
	})

	// ȸ������ �޴�����ȣ
	$('#handphone_index').on('change', function() {
		if ($(this).val().length < $(this).attr('maxlength')) {
			alert('�޴��� ��ȣ�� ��Ȯ�� �Է����ּ���.');
			$("#handphone_feedback").empty().append("�޴��� ��ȣ�� ��Ȯ�� �Է����ּ���.").show();
			$(this).val($(this).val().substr(0, $(this).attr('maxlength')));
			$('#joinSmsBtn').attr('onclick', ''); // SMS ��������
			return;
		} else {
			$('#joinSmsBtn').attr('onclick', 'joinSms()'); // SMS ��������
		}

		if ($(this).val() != '') {
			$('#handphone_feedback').hide();
		} else {
			$('#handphone_feedback').show();
		}

		if(fncheckNum($('#handphone_index').val()) === false) {
			alert('�޴��� ��ȣ�� ���ڸ� �Է°����մϴ�.');
			$('#handphone_index').val('');
			$('#handphone_feedback').show();
			return;
		}
	})

	// ȸ�� �̸��� üũ
	$('#email').on('change', function(){
		if ($(this).val() != '') {
			$('#email_feedback').hide();
		}

		if ($(this).val() == '') {
			alert('�̸����� �Է����ּ���.');
			$('#email_feedback').show();
		}

		if (checkKorean($('#email').val()) == false) {
			alert('�̸����� ���ڿ� �����ڸ� ��밡�� �մϴ�.');
			$('#email').val('');
			return;
		}
	})

	$('#join_email').on('change', function(){
		if (checkEmail($('#join_email').val()) == false) {
			alert('�̸����� ���ڿ� �����ڸ� ��밡�� �մϴ�.');
			$('#join_email').val('');
			$("#email_feedback").empty().append("�̸����� ���ڿ� �����ڸ� ��밡�� �մϴ�.").show();
			return;
		} else {
			$("#email_feedback").hide();
		}
	})

	// if ($('#certification_state').val() != '1') {
	// 	alert("�޴��� ������ �������ּ���.");
	// 	$('#handphone_index').focus();
	// 	return;
	// }

	// ���԰�� 2���̻� �������� X
	$('.join_route').on('click', function(){
		var checked_box = $('.join_route:checked').size();
		if (checked_box >= 3) {
			// alert('�ִ� 2������ ���� ���� ���� �մϴ�.');
			$('#join_route_pop').show();
			$(this).prop('checked', false);
			return;
		}
	})

	// ȸ������ �����з� �Է� (��Ÿ)
	$('#level_edu').on('change', function(){
		if ($(this).val() == '15') {
			$('#level_edu_major').show();
		} else {
			$('#level_edu_major').hide();
		}
	})

    // �α��� �Է½� x��ư Ŭ��
    $('.value_clear').click(function () {
        $(this).parent().find('input').val('');
        $(this).hide();
    });


    // $('#join_email').change(function() {
    //     var state = $('#join_email option:selected').val();
    //     if ( state == '�����Է�' ) {
    //         $('.email_input').show();
    //     }else {
    //         $('.email_input').hide();
	// 	}
    // })


	// ����а�
	$('.chk_depth2').on('change', function () {
		if ($(this).val() != '') {
			$('#hope_majer_feedback').hide();
		}
	})

	// ��������
	$('.lec_object').on('change', function () {
		if ($(this).val() != '') {
			$('#lec_object_feedback').hide();
		}
	})

	// ���԰��
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

// ȸ������(�������) step 01
function joinStepCheck() {

	var form = document.getElementById('joinStep_01');

	if ($('#agree').is(':checked') == false) {
		alert('�̿��� ���Ǵ� �ʼ��׸��Դϴ�.');
		$('#agree').focus();
		return;
	}

	if ($('#private_agree').is(':checked') == false) {
		alert('������������ �� �̿뵿�Ǵ� �ʼ��׸��Դϴ�.');
		$('#private_agree').focus();
		return;
	}

	form.submit();
}

// �ߺ����̵� Ȯ��
function memberIdCheck() {
	var member_id = $('#user_id').val();

	if (member_id == '') {
		alert('���̵� �Է��ϼ���.');
		$('#user_id_feedback').show();
		$('#user_id').focus();
		return;
	}

	if (checkSpecial(member_id) == false) {
		alert('����� �� ���� ���̵��Դϴ�. ������ Ȯ�����ּ���.');
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
				$('#useridCheck').val('Y'); // ���̵� �ߺ�Ȯ�� üũ
				$("#user_id_feedback").empty().hide();
			} else {
				$("#user_id_feedback").empty().append("�̹� ������� ���̵��Դϴ�.").show();
			}
		}
	})
}

// �޴�������
function joinSms() {

	var user_birth = $("#birth").val();
	var handphone = $("#handphone_index").val();

	if (user_birth == "") {
		alert('��������� �Է����ּ���.');
		$("#birth_feedback").empty().append("��������� �Է����ּ���.");
		return;
	}

	if(fncheckNum(user_birth) === false) {
		alert('��������� ���ڸ� �Է��� �ּ���.');
		$("#birth_feedback").empty().append("��������� ���ڸ� �Է��� �ּ���.");
		return;
	}

	if(handphone == "") {
		alert('�޴�����ȣ�� �Է����ּ���.');
		$("#handphone_feedback").empty().append("�޴�����ȣ�� �Է����ּ���.");
		return;
	}

	if (handphone.length > 11) {
		alert('������� Ȥ�� �޴��� ��ȣ�� ��Ȯ�� �Է����ּ���.');
		$("#handphone_feedback").empty().append("������� Ȥ�� �޴��� ��ȣ�� ��Ȯ�� �Է����ּ���.");
		return;
	}

	if(fncheckNum(handphone) === false) {
		alert('�޴�����ȣ�� ���ڸ� �Է��� �ּ���.');
		$("#tip_user_birth").empty().append("�޴�����ȣ�� ���ڸ� �Է��� �ּ���.");
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

// ȸ���������� �޴�������
function memberInfojoinSms() {

	var user_birth = $("#birth").val();
	var handphone = $("#handphone_index").val();

	if (user_birth == "") {
		alert('��������� �Է����ּ���.');
		$("#birth_feedback").empty().append("��������� �Է����ּ���.");
		return;
	}

	if (user_birth.length > 8) {
		alert('������� Ȥ�� �޴��� ��ȣ�� ��Ȯ�� �Է����ּ���.');
		$("#birth_feedback").empty().append("������� Ȥ�� �޴��� ��ȣ�� ��Ȯ�� �Է����ּ���.");
		return;
	}

	if(fncheckNum(user_birth) === false) {
		alert('��������� ���ڸ� �Է��� �ּ���.');
		$("#birth_feedback").empty().append("��������� ���ڸ� �Է��� �ּ���.");
		return;
	}

	if(handphone == "") {
		alert('�޴�����ȣ�� �Է����ּ���.');
		$("#handphone_feedback").empty().append("�޴�����ȣ�� �Է����ּ���.");
		return;
	}

	if (handphone.length > 11) {
		alert('������� Ȥ�� �޴��� ��ȣ�� ��Ȯ�� �Է����ּ���.');
		$("#handphone_feedback").empty().append("������� Ȥ�� �޴��� ��ȣ�� ��Ȯ�� �Է����ּ���.");
		return;
	}

	if(fncheckNum(handphone) === false) {
		alert('�޴�����ȣ�� ���ڸ� �Է��� �ּ���.');
		$("#tip_user_birth").empty().append("�޴�����ȣ�� ���ڸ� �Է��� �ּ���.");
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


// �޴�������
function searchJoinSms(searchType) {
	var member_id = $("#user_id").val();
	var member_name = $("#name").val();
	var member_birth = $("#birth").val();
	var member_email = $("#email").val();
	var handphone = $("#handphone_index").val();

	if (searchType == 'search_email') {
		if (member_email == "") {
			alert('�̸����� �Է����ּ���.');
			$('#email').focus();
			$("#email_feedback").empty().append("�̸����� �Է����ּ���.").show();
			return;
		}
	}

	if (member_id == "") {
		alert('���̵� �Է����ּ���.');
		$('#user_id').focus();
		$("#id_feedback").empty().append("���̵� �Է����ּ���.").show();
		return;
	}

	if (member_name == "") {
		alert('�̸��� �Է����ּ���.');
		$('#name').focus();
		$("#name_feedback").empty().append("�̸��� �Է����ּ���.").show();
		return;
	}

	if (member_birth == "") {
		alert('��������� �Է����ּ���.');
		$('#birth').focus();
		$("#birth_feedback").empty().append("��������� �Է����ּ���.").show();
		return;
	}

	if(fncheckNum(member_birth) === false) {
		alert('��������� ���ڸ� �Է��� �ּ���.');
		$('#birth').focus();
		$("#birth_feedback").empty().append("��������� ���ڸ� �Է��� �ּ���.").show();
		return;
	}

	if(handphone == "") {
		alert('�޴�����ȣ�� �Է����ּ���.');
		$('#handphone_index').focus();
		$("#handphone_feedback").empty().append("�޴�����ȣ�� �Է����ּ���.").show();
		return;
	}

	if(fncheckNum(handphone) === false) {
		alert('�޴�����ȣ�� ���ڸ� �Է��� �ּ���.');
		$('#handphone_index').focus();
		$("#handphone_feedback").empty().append("�޴�����ȣ�� ���ڸ� �Է��� �ּ���.").show();
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

// �޴������� (��й�ȣ ã��)
function searchJoinSms_certification(searchType)
{
	var insert_id = $('#certification_id').val();
	var send_code = $('#send_code').val();

	var member_id = $("#user_id").val();
	var member_name = $("#name").val();
	var member_birth = $("#birth").val();
	var handphone = $("#handphone_index").val();

	if (member_id == '') {
		alert('���̵� �Է����ּ���.');
		$('#user_id').focus();
		$("#id_feedback").empty().append("���̵� �Է����ּ���.").show();
		return;
	}

	if (member_name == '') {
		alert('�̸��� �Է����ּ���.');
		$('#name').focus();
		$("#name_feedback").empty().append("�̸��� �Է����ּ���.").show();
		return;
	}

	if (member_birth == '') {
		alert('��������� �Է����ּ���.');
		$('#birth').focus();
		$("#birth_feedback").empty().append("��������� ���ڸ� �Է��� �ּ���.").show();
		return;
	}

	if ($('#email').val() == '') {
		alert('�̸����� �Է����ּ���.');
		$('#email').focus();
		$("#email_feedback").empty().append("�̸����� �Է����ּ���.").show();
		return;
	}

	if (handphone == '') {
		alert('�ڵ�����ȣ�� �Է����ּ���.');
		$('#handphone_index').focus();
		$("#handphone_feedback").show();
		return;
	}

	if (searchType == 'search_email') {
		if ($('#email').val() == '') {
			alert('�̸����� �Է����ּ���.');
			$('#email').focus();
			return;
		}
	}

	if (send_code == '') {
		alert('������ȣ�� �Է����ּ���.');
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

// �̸��� (��й�ȣ ã��)
function searchMemberInfo(searchType){

	var member_id = $("#user_id").val();
	var member_name = $("#name").val();
	var member_birth = $("#birth").val();
	var email = $("#email").val();

	if (member_id == "") {
		alert('���̵� �Է����ּ���.');
		$("#id_feedback").empty().append("���̵� �Է����ּ���.");
		return;
	}

	if (member_name == "") {
		alert('�̸��� �Է����ּ���.');
		$("#name_feedback").empty().append("�̸��� �Է����ּ���.");
		return;
	}

	if (member_birth == "") {
		alert('��������� �Է����ּ���.');
		$("#birth_feedback").empty().append("��������� �Է����ּ���.");
		return;
	}

	if(fncheckNum(member_birth) === false) {
		alert('��������� ���ڸ� �Է��� �ּ���.');
		$("#birth_feedback").empty().append("��������� ���ڸ� �Է��� �ּ���.");
		return;
	}

	if(email == "") {
		alert('�̸����� �Է����ּ���.');
		$("#email_feedback").empty().append("�޴�����ȣ�� �Է����ּ���.");
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

// ���� ��й�ȣ ����
function memberInfoUpdate(update_type)
{
	var memberNo = $('#data_memberNo').val();
	var password_new = $('#password_new').val();
	var password = $('#password').val();

	//console.log(memberNo);

	if (memberNo == "") {
		alert('�ʼ����� �����ϴ�. �޴����������� �ٽ����ּ���.');
		location.href='/member/search?searchType=password&search=tel';
		return;
	}

	if(password_new == "") {
		alert("��й�ȣ�� �Է��ϼ���.");
		$("#password_new").focus();
		$("#password_new_feedback").empty().append("��й�ȣ�� �Է��� �ּ���.");
		return;
	}

	if(password_new.length < 10){
		alert("��й�ȣ�� 10�̻� 32�� ���Ϸ� �ۼ����ּ���.");
		$("#password_new").focus();
		$("#password_new_feedback").empty().append("��й�ȣ�� 10�̻� 32�� ���Ϸ� �ۼ����ּ���.");
		return;;
	}

	if(password_new.length > 32){
		alert("��й�ȣ�� 10�̻� 32�� ���Ϸ� �ۼ����ּ���.");
		$("#password_new").focus();
		$("#password_new_feedback").empty().append("��й�ȣ�� 10�̻� 32�� ���Ϸ� �ۼ����ּ���.");
		return;;
	}

	if(!fnCheckPassword(password_new)) {
		$("#password_new").focus();
		return;
	}

	if( password_new != password ) {
		alert("��й�ȣȮ�ΰ� ���� �ʽ��ϴ�. �ٽ� Ȯ�����ּ���.");
		// $("#useridCheck").value('');
		$("#password_new").focus();
		$("#password_new_feedback").empty().append("��й�ȣȮ�ΰ� ���� �ʽ��ϴ�. �ٽ� Ȯ�����ּ���.");
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
				$('#passwordChangePop').show(); // ��й�ȣ �����˾� ����
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


// �޴������� ��ȣȮ��
function joinSms_certification()
{
	var insert_id = $('#certification_id').val();
	var send_code = $('#send_code').val();

	if (send_code == '') {
		alert('������ȣ�� �Է����ּ���.');
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

// ��õ���̵� ��ȸ
function memberRecommender() {

	if ($('#recommender').val() == '') {
		alert('��õ�� ���̵� �Է����ּ���.');
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

// ȸ������ ��������
function joinStepFinsh()
{
	var form = document.joinStep_02;
	var name = $("#name").val(); // �̸�

	if (name == "") {
		alert("�̸��� �Է��ϼ���.");
		$("#name").focus();
		$("#name_feedback").empty().append("�̸��� �Է��� �ּ���.").show();
		return;
	}

	var user_birth = $("#birth").val(); // �������

	if (user_birth == "") {
		alert("��������� �Է��ϼ���.");
		$("#birth").focus();
		$("#birth_feedback").empty().append("��������� �Է��� �ּ���.").show();
		return;
	}

	if (user_birth.length != 8) {
		alert("��������� ��Ȯ�� �Է��ϼ���.");
		$("#birth").focus();
		$("#birth_feedback").empty().append("��������� ��Ȯ�� �Է��� �ּ���.").show();
		return;
	}

	if (fncheckNum(user_birth) === false) {
		alert("��������� ���ڸ� �Է��ϼ���.");
		$("#birth").focus();
		$("#birth_feedback").empty().append("��������� ���ڸ� �Է��ϼ���.").show();
		return;
	}

	if ($('#user_id').val() == '') {
		alert("���̵� �Է��ϼ���.");
		$("#user_id").focus();
		$("#user_id_feedback").empty().append("���̵� �Է��ϼ���.").show();
		return;
	}

	if ($('#useridCheck').val() == '') {
		alert('���̵� �ߺ�üũ ���ּ���.');
		$('#user_id').focus();
		$("#user_id_feedback").empty().append("���̵� �ߺ�üũ ���ּ���.").show();
		return;
	}

	var password_new = $("#password_new").val();
	var password = $("#password").val();

	if(password_new == "") {
		alert("��й�ȣ�� �Է��ϼ���.");
		$("#password_new").focus();
		$("#password_new_feedback").empty().append("��й�ȣ�� �Է��� �ּ���.").show();
		return;
	}

	if(password_new.length < 10){
		alert("��й�ȣ�� 10�̻� 32�� ���Ϸ� �ۼ����ּ���.");
		$("#password_new").focus();
		$("#password_new_feedback").empty().append("��й�ȣ�� 10�̻� 32�� ���Ϸ� �ۼ����ּ���.").show();
		return;;
	}

	if(password_new.length > 32){
		alert("��й�ȣ�� 10�̻� 32�� ���Ϸ� �ۼ����ּ���.");
		$("#password_new").focus();
		$("#password_new_feedback").empty().append("��й�ȣ�� 10�̻� 32�� ���Ϸ� �ۼ����ּ���.").show();
		return;;
	}

	if(!fnCheckPassword(password_new)) {
		$("#password_new").focus();
		return;
	}

	if( password_new != password ) {
		alert("��й�ȣȮ�ΰ� ���� �ʽ��ϴ�. �ٽ� Ȯ�����ּ���.");
		// $("#useridCheck").value('');
		$("#password_new").focus();
		$("#password_new_feedback").empty().append("��й�ȣȮ�ΰ� ���� �ʽ��ϴ�. �ٽ� Ȯ�����ּ���.").show();
		return;
	}

	if ($('#email').val() == '') {
		alert('�̸����� �Է����ּ���.');
		$("#email_feedback").empty().append("�̸����� �Է����ּ���.").show();
		$('#email').focus();
		return;
	}

	if ($('#join_email').val() == '') {
		alert('�̸����� �Է����ּ���.');
		$("#email_feedback").empty().append("�̸����� �Է����ּ���.").show();
		$('#email').focus();
		return;
	}

	if ($('#certification_state').val() == '') {
		alert('�޴��� ������ �����Ͽ� �ּ���.');
		$('#handphone_index').focus();
		$('#handphone_feedback').empty().append('�޴��� ������ �����Ͽ� �ּ���.').show();
		return;
	}

	if ($('#uno_new').val() == '' && $('#home_address').val() == '' && $('#tail_address').val() == '') {
		alert('�ּҸ� �Է����ּ���.');
		$('#uno_new').focus();
		$('#uno_new_feedback').empty().append('�ּҸ� �Է����ּ���.').show();
		return;
	}

	if ($('.chk_depth2').is(':checked') == false) {
		alert('����а��� �������ּ���.');
		$('#hope_majer').focus();
		$('#hope_majer_feedback').empty().append("����а��� �������ּ���.").show();
		return;
	}

	// if ($('.lec_object').is(':checked') == false) {
	// 	alert('���������� �������ּ���.');
	// 	$('#lec_object').focus();
	// 	$('#lec_object_feedback').empty().append("���������� �������ּ���.").show();
	// 	return;
	// }

	// if ($('.join_route').is(':checked') == false) {
	// 	alert('���԰�θ� �������ּ���.');
	// 	$('#join_route').focus();
	// 	$('#join_route_feedback').empty().append("���԰�θ� �������ּ���.").show();
	// 	return;
	// }

	if ($('#recommender').val() != '' && $('#recommenderCheck').val() == '') {
		alert('��õ���̵� ��ȸ ��ư�� Ŭ�����ּ���.');
		$('#recommender').focus();
		return;
	}

	form.submit();
}

// ȸ������ ����
function memberInfoUpdateForm(member_no)
{
	var form = document.getElementById('memberInfo');

	var password_new = $("#password_new").val();
	var password = $("#password").val();

	if (member_no == "") {
		alert("�ʼ����� �����ϴ�. �����ڿ� �������ּ���.");
		location.href='/';
		return;
	}

	if(password_new == "") {
		alert("��й�ȣ�� �Է��ϼ���.");
		$("#password_new").focus();
		$("#password_new_feedback").empty().append("��й�ȣ�� �Է��� �ּ���.").show();
		return;
	}

	if(password_new.length < 10){
		alert("��й�ȣ�� 10�̻� 32�� ���Ϸ� �ۼ����ּ���.");
		$("#password_new").focus();
		$("#password_new_feedback").empty().append("��й�ȣ�� 10�̻� 32�� ���Ϸ� �ۼ����ּ���.").show();
		return;;
	}

	if(password_new.length > 32){
		alert("��й�ȣ�� 10�̻� 32�� ���Ϸ� �ۼ����ּ���.");
		$("#password_new").focus();
		$("#password_new_feedback").empty().append("��й�ȣ�� 10�̻� 32�� ���Ϸ� �ۼ����ּ���.").show();
		return;;
	}

	if(!fnCheckPassword(password_new)) {
		$("#password_new").focus();
		return;
	}

	if( password_new != password ) {
		alert("��й�ȣȮ�ΰ� ���� �ʽ��ϴ�. �ٽ� Ȯ�����ּ���.");
		$("#password_new").focus();
		$("#password_new_feedback").empty().append("��й�ȣȮ�ΰ� ���� �ʽ��ϴ�. �ٽ� Ȯ�����ּ���.").show();
		return;
	}

	if ($('#certification_state').val() != '1') {
		alert("�޴��� ������ �������ּ���.");
		$('#handphone_index').focus();
		$('#handphone_feedback').empty().append('�޴��� ������ �������ּ���.').show();
		return;
	}

	form.submit();
}

// �α���
function memberLogin()
{
	var user_id = $('#user_id').val();
	var password = $('#password').val();
	var form = $("#loginform");

	if (user_id == '') {
		alert('���̵� �Է����ּ���.');
		$('#user_id').focus();
		return;
	}
	if (password == '') {
		alert('��й�ȣ�� �Է����ּ���.');
		$('#password').focus();
		return;
	}

	// console.log(form);
	form.submit();
}

// �α׾ƿ�
function memberLogOut(member_id)
{
	if (confirm('�α׾ƿ� �Ͻðڽ��ϱ�?')) {
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

// �޸�ȸ�� �Ϲ�ȸ������ ��ȯ
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

// ȸ������ ��ҹ�ư
function joinBack()
{
	if (confirm('ȸ�������� ����Ͻðڽ��ϱ�?')) {
		location.href='/login/main';
	} else {
		return;
	}
}

// �α���(����)
function memberUtLogin()
{
	var user_id = $('#user_id').val();
	var password = $('#password').val();

	if (user_id == '') {
		alert('���̵� �Է����ּ���.');
		$('#user_id').focus();
		return;
	}
	if (password == '') {
		alert('��й�ȣ�� �Է����ּ���.');
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
				alert("�޸���� �Դϴ�.");
			} else if(data.code  == "LIMIT") {
				alert("ȸ������ ��ȸ Ƚ��(5ȸ)�� �ʰ��Ͽ����ϴ�.");
			} else {
				alert("���Ե� ȸ�� ������ ���ų� �߸��� ��й�ȣ �Դϴ�.\r\n�ٽ� Ȯ�� �ٶ��ϴ�.");
			}
		}
	})

}

// ����ȸ������
function user_search_agree()
{
	if ($('#user_search_agree').is(':checked') == false) {
		alert('�������� ���� �� ������ ���� üũ���ּ���.');
		$('#user_search_agree').focus();
		return;
	}

	location.href='/member/main?pageType=ut&step=03';
}

// ���̵�ã��
function searchMemberId(searchType)
{
	var user_name = $('#name').val();
	var birth = $('#birth').val();
	var handphone_index = $('#handphone_index').val();
	var email = $('#email').val();

	if (user_name == '') {
		alert('�̸��� �Է����ּ���.');
		$('#name').focus();
		$('#name_feedback').show();
		return;
	}

	if (birth == '') {
		alert('��������� �Է����ּ���.');
		$('#birth').focus();
		$('#birth_feedback').show();
		return;
	}

	if (searchType == 'search_tel') {
		if (handphone_index == '') {
			alert('�޴�����ȣ�� �Է����ּ���.');
			$('#handphone_index').focus();
			$('#handphone_feedback').show();
			return;
		}
	} else {
		if (email == '') {
			alert('�̸����� �Է����ּ���.');
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
				searchCheck += "<a href=\"/member/search?searchType=password&search=tel\" class=\"btn gray\">��й�ȣã��</a>";
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

// ���� �α���
function enterkey_ut(t) {
    $(t).parent().find('.value_clear').show();
}