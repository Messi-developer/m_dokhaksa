$(document).ready(function() {
	//상단 공통 리스트
	$('.ul_list ul li a').on('click',function(){
		var _li = $(this).parent();
		_li.addClass('on').siblings().removeClass('on');
	});

	//상담도움정보
	$('.cs_list ul li a').on('click',function(){
		var _li = $(this).parent();
		_li.toggleClass('on');
	});

	//개인정보수집동의
	$('.click_area input[type="checkbox"]').on('change',function(){
		if($(this).prop('checked')) {
           $('.ico_down').addClass('on');
		   $('.agree_box').slideDown();
        }else{
           $('.ico_down').removeClass('on');
		   $('.agree_box').slideUp();
        }
	});

	//faq 탭
	$('.qna_wrap > ul > li > a').on('click',function(){
		var _li = $(this).parent(),
			_idx = _li.index();
			_li.addClass('on').siblings().removeClass('on');
			$('.qna_wrap').find('.qna_tab_box').eq(_idx).addClass('on').siblings().removeClass('on')

	});


	//초기 리스트갯수 설정
	function list_view(){
		var _li = $('.notice_list ul li');

		_li.each(function(idx){
			if(idx > 9){
				$(this).hide();
			}
		});
	}

	list_view();


	// 휴대폰번호
	$('#phone').on('change', function() {
		if(fncheckNum($('#phone').val()) === false) {
			alert('휴대폰 번호는 숫자만 입력가능합니다.');
			$('#phone').val('');
			$('#phone_feedback').show();
			return;
		}
	});


});


// faq페이지 스크롤시 상단 탭 고정

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


// faq 질문 답변
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


// 1:1 무료상담
function noticeSend(){

	var name = $('#name').val();
	var phone = $('#phone').val();
	var email = $('#email').val();
	var subject = $('#subject').val();
	var contents = $('#contents').val();

	if (name == '') {
		alert('이름을 입력해주세요.');
		$('#name').focus();
		$("#name_feedback").empty().append("이름을 입력해주세요.").show();
		return;
	}

	if (phone == '') {
		alert('연락처를 입력해주세요.');
		$('#phone').focus();
		$("#phone_feedback").empty().append("연락처를 입력해주세요.").show();
		return;
	}

	if(fncheckNum(phone) === false) {
		alert('연락처는 숫자만 입력해 주세요.');
		$('#email').focus();
		$("#phone_feedback").empty().append("연락처는 숫자만 입력해 주세요.").show();
		return;
	}

	if (email == '') {
		alert('이메일을 입력해주세요.');
		$('#email').focus();
		$("#email_feedback").empty().append("이메일을 입력해주세요.").show();
		return;
	}

	if (subject == '') {
		alert('제목을 입력해주세요.');
		$('#subject').focus();
		$("#subject_feedback").empty().append("제목을 입력해주세요.").show();
		return;
	}

	if (contents == '') {
		alert('문의내용을 입력해주세요.');
		$('#contents').focus();
		$("#contents_feedback").empty().append("문의내용을 입력해주세요.").show();
		return;
	}

	if ($('#agree_all').is(':checked') == false) {
		alert('개인정보 수집 동의체크해주세요.');
		$('#agree_all').focus();
		return;
	}

	var form = document.getElementById('consultingForm');
	form.submit();

}



// FAQ 하단텝 검색결과확인
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
					notice_list += "<dt onclick=\"notice_list_toggle($(this));\">"+ data.faq_list[i].question +" <span class=\"ico_down\">〉</span></dt>";
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


// 공지사항 더보기
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
						checkNotice = '공지'
						checkNoticeColor = "#0097a3";
					} else {
						checkNotice = '이벤트'
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


// 해커스에 바란다 send
function hackersHelpSend()
{
	var name = $('#name').val();
	var phone = $('#phone').val();
	var email = $('#email').val();
	var subject = $('#subject').val();
	var contents = $('#contents').val();

	if (name == '') {
		alert('이름을 입력해주세요.');
		$('#name').focus();
		$("#name_feedback").empty().append("이름을 입력해주세요.").show();
		return;
	}

	if (phone == '') {
		alert('연락처를 입력해주세요.');
		$('#phone').focus();
		$("#phone_feedback").empty().append("연락처를 입력해주세요.").show();
		return;
	}

	if(fncheckNum(phone) === false) {
		alert('연락처는 숫자만 입력해 주세요.');
		$('#email').focus();
		$("#phone_feedback").empty().append("연락처는 숫자만 입력해 주세요.").show();
		return;
	}

	if (email == '') {
		alert('이메일을 입력해주세요.');
		$('#email').focus();
		$("#email_feedback").empty().append("이메일을 입력해주세요.").show();
		return;
	}

	if (subject == '') {
		alert('제목을 입력해주세요.');
		$('#subject').focus();
		$("#subject_feedback").empty().append("제목을 입력해주세요.").show();
		return;
	}

	if (contents == '') {
		alert('문의내용을 입력해주세요.');
		$('#contents').focus();
		$("#contents_feedback").empty().append("문의내용을 입력해주세요.").show();
		return;
	}

	if ($('#agree_all').is(':checked') == false) {
		alert('개인정보 수집 동의체크해주세요.');
		$('#agree_all').focus();
		return;
	}

	var form = document.getElementById('hackersHelp');
	form.submit();
}