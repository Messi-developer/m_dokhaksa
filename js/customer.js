$(document).ready(function() {
	//��� ���� ����Ʈ
	$('.ul_list ul li a').on('click',function(){
		var _li = $(this).parent();
		_li.addClass('on').siblings().removeClass('on');
	});

	//��㵵������
	$('.cs_list ul li a').on('click',function(){
		var _li = $(this).parent();
		_li.toggleClass('on');
	});

	//����������������
	$('.click_area input[type="checkbox"]').on('change',function(){
		if($(this).prop('checked')) {
           $('.ico_down').addClass('on');
		   $('.agree_box').slideDown();
        }else{
           $('.ico_down').removeClass('on');
		   $('.agree_box').slideUp();
        }
	});

	//faq ��
	$('.qna_wrap > ul > li > a').on('click',function(){
		var _li = $(this).parent(),
			_idx = _li.index();
			_li.addClass('on').siblings().removeClass('on');
			$('.qna_wrap').find('.qna_tab_box').eq(_idx).addClass('on').siblings().removeClass('on')

	});


	//�ʱ� ����Ʈ���� ����
	function list_view(){
		var _li = $('.notice_list ul li');

		_li.each(function(idx){
			if(idx > 9){
				$(this).hide();
			}
		});
	}

	list_view();


	// �޴�����ȣ
	$('#phone').on('change', function() {
		if(fncheckNum($('#phone').val()) === false) {
			alert('�޴��� ��ȣ�� ���ڸ� �Է°����մϴ�.');
			$('#phone').val('');
			$('#phone_feedback').show();
			return;
		}
	});


});


// faq������ ��ũ�ѽ� ��� �� ����

$(window).scroll(function(){
    var nowScroll = $(document).scrollTop();
    var conScroll = $('.customer_wrap').offset().top;
    if(nowScroll > conScroll){
        $('#notice_container .ul_list > ul').css({"position":"fixed"});
    }
    else{
        $('#notice_container .ul_list > ul').css({"position":"relative"});
    }
});


// faq ���� �亯
function notice_list_toggle(cont){
	var _all = $('.qna_tab_box ul li dl dd'),
		_ico = $('.ico_down');

	if(cont.next('dd').is(':visible')){
		cont.next('dd').slideUp();
		cont.find('.ico_down').removeClass('on');
	}
	else{
		_all.slideUp();
		_ico.removeClass('on');
		cont.next('dd').slideDown();
		cont.find('.ico_down').addClass('on');
	}
}


// 1:1 ������
function noticeSend(){

	var name = $('#name').val();
	var phone = $('#phone').val();
	var email = $('#email').val();
	var subject = $('#subject').val();
	var contents = $('#contents').val();

	if (name == '') {
		alert('�̸��� �Է����ּ���.');
		$('#name').focus();
		$("#name_feedback").empty().append("�̸��� �Է����ּ���.").show();
		return;
	}

	if (phone == '') {
		alert('����ó�� �Է����ּ���.');
		$('#phone').focus();
		$("#phone_feedback").empty().append("����ó�� �Է����ּ���.").show();
		return;
	}

	if(fncheckNum(phone) === false) {
		alert('����ó�� ���ڸ� �Է��� �ּ���.');
		$('#email').focus();
		$("#phone_feedback").empty().append("����ó�� ���ڸ� �Է��� �ּ���.").show();
		return;
	}

	if (email == '') {
		alert('�̸����� �Է����ּ���.');
		$('#email').focus();
		$("#email_feedback").empty().append("�̸����� �Է����ּ���.").show();
		return;
	}

	if (subject == '') {
		alert('������ �Է����ּ���.');
		$('#subject').focus();
		$("#subject_feedback").empty().append("������ �Է����ּ���.").show();
		return;
	}

	if (contents == '') {
		alert('���ǳ����� �Է����ּ���.');
		$('#contents').focus();
		$("#contents_feedback").empty().append("���ǳ����� �Է����ּ���.").show();
		return;
	}

	if ($('#agree_all').is(':checked') == false) {
		alert('�������� ���� ����üũ���ּ���.');
		$('#agree_all').focus();
		return;
	}

	var form = document.getElementById('consultingForm');
	form.submit();

}



// FAQ �ϴ��� �˻����Ȯ��
function noticeBoard(category){
	// console.log(category);

	$.ajax({
		url : "/notice/main/getFaqList"
		,type : "POST"
		,dataType : "JSON"
		,data : {
			customerType : 'faq'
			,category : category

		},success : function(data) {
			// console.log(data);

			if (data.result) {
				var notice_list = "";

				for (var i=0; i<data.faq_list.length; i++){
					notice_list += "<li>";
					notice_list += "<dl>";
					notice_list += "<dt onclick=\"notice_list_toggle($(this));\">"+ data.faq_list[i].question +" <span class=\"ico_down\">��</span></dt>";
					notice_list += "<dd>"+ data.faq_list[i].answer +"</dd>";
					notice_list += "</dl>";
					notice_list += "</li>";
				}

				$('#notice_list_content').html('');
				$('#notice_list_content').append(notice_list);
			}
		}
	})

}


// �������� ������
function noticePluse(total_cnt)
{
	var notice_list = $('.notice_item_list').length;
	notice_list = (parseInt(notice_list) + 5)
	limitCountCheck(notice_list, total_cnt, 'notice_pluse');

	$.ajax({
		url : '/notice/main/getNoticeList'
		,type : 'POST'
		,dataType : 'JSON'
		,async : false
		,data : {
			notice_list : notice_list
		},success : function(data){
			// console.log(data);

			if (data.result) {
				var noticeList = "";
				var checkNotice = "";
				var checkNoticeColor = "";

				noticeList += "<ul>";
				for(var i=0; i<data.notice_list.length; i++) {

					if (data.notice_list[i].big_cate == '4') {
						checkNotice = '����'
						checkNoticeColor = "#0097a3";
					} else {
						checkNotice = '�̺�Ʈ'
						checkNoticeColor = "#8f69b2";
					}
					
					noticeList += "<li class=\"notice_item_list\">";
					noticeList += "<a href=\"/notice/view?viewType=notice&notice_no="+ data.notice_list[i].no +"\">";
					// noticeList += "<span class=\"num\">"+ numberPad((i + 1) + $('.notice_item_list').length, 2) +"</span>";
					noticeList += "<span class=\"num\">"+ numberPad(((total_cnt - notice_list) - i), 2) +"</span>";
					noticeList += "<span class=\"color t1\" style=\"color:"+ checkNoticeColor +"\">["+ checkNotice +"]&nbsp;&nbsp;</span>";
					noticeList += ""+ data.notice_list[i].subject +"";
					noticeList += "</a>";
					noticeList += "</li>";
				}
				noticeList += "</ul>";
				$('#noticePluseContent').append(noticeList);
			}

		}
	})
}


// ��Ŀ���� �ٶ��� send
function hackersHelpSend()
{
	var name = $('#name').val();
	var phone = $('#phone').val();
	var email = $('#email').val();
	var subject = $('#subject').val();
	var contents = $('#contents').val();

	if (name == '') {
		alert('�̸��� �Է����ּ���.');
		$('#name').focus();
		$("#name_feedback").empty().append("�̸��� �Է����ּ���.").show();
		return;
	}

	if (phone == '') {
		alert('����ó�� �Է����ּ���.');
		$('#phone').focus();
		$("#phone_feedback").empty().append("����ó�� �Է����ּ���.").show();
		return;
	}

	if(fncheckNum(phone) === false) {
		alert('����ó�� ���ڸ� �Է��� �ּ���.');
		$('#email').focus();
		$("#phone_feedback").empty().append("����ó�� ���ڸ� �Է��� �ּ���.").show();
		return;
	}

	if (email == '') {
		alert('�̸����� �Է����ּ���.');
		$('#email').focus();
		$("#email_feedback").empty().append("�̸����� �Է����ּ���.").show();
		return;
	}

	if (subject == '') {
		alert('������ �Է����ּ���.');
		$('#subject').focus();
		$("#subject_feedback").empty().append("������ �Է����ּ���.").show();
		return;
	}

	if (contents == '') {
		alert('���ǳ����� �Է����ּ���.');
		$('#contents').focus();
		$("#contents_feedback").empty().append("���ǳ����� �Է����ּ���.").show();
		return;
	}

	if ($('#agree_all').is(':checked') == false) {
		alert('�������� ���� ����üũ���ּ���.');
		$('#agree_all').focus();
		return;
	}

	var form = document.getElementById('hackersHelp');
	form.submit();
}