$(document).ready(function() {

    // GNB swiper
    var swiper = new Swiper('.swiper-container', {
        slidesPerView: 3,
        grabCursor: true
    });

    //뒤로가기
    $('.his_back').bind('touchstart click',function(e){
        history.back(-1);
        e.preventDefault();
    });

    //공통탭메뉴
    $('.tab_area li a').on('click',function(){
        var _li = $(this).parent(),
            _idx = _li.index(),
            _div = _li.parent().parent();

        _li.addClass('on').siblings().removeClass('on');

        _div.find('.tab_con').eq(_idx).addClass('on').siblings().removeClass('on');
        autoMap.printMap();
    });

    // 전체선택
    $('.subCheckbox').on('change', function(){
        if ($('.checkbox_length > input[type=checkbox]:checked').size() != $('.checkbox_length').length) {
            $('#all').prop('checked', false);
        } else {
            $('#all').prop('checked', true);
        }
    })

    // 파일첨부기능
    var fileTarget = $('.filebox #upload-name');
    fileTarget.on('change', function () {  // 값이 변경되면
        if (window.FileReader) {  // modern browser
            var filename = $(this)[0].files[0].name;
        }
        else {  // old IE
            var filename = $(this).val().split('/').pop().split('\\').pop();  // 파일명만 추출
        }
        // 추출한 파일명 삽입
        $(this).siblings('.upload-name').val(filename);
    });

    var gnb_top = $('#gnb').offset().top;
    var gnb_h = $('#gnb').height();

    $(window).scroll(function() {
        var scrollHeigth = $(document).scrollTop();

        if(scrollHeigth > gnb_top) {
            $("#wrap").addClass('scroll_fixed');
            $("#wrap #gnb").next().css('margin-top',$("#wrap #gnb").height());

            $('#gnb').next().css('margin-top',gnb_h);
        } else {
            $("#wrap").removeClass('scroll_fixed');
            $("#wrap #gnb").next().css('margin-top',0);
            $('#gnb').next().css('margin-top',0);
        }
    });

    $('#header .btn-family-site').click(function(){
        var famDisPlay = $(".fam_slide_area").css("display");

        if(famDisPlay=="none"){
            $(this).addClass("active");
            $('.fam_site_dim').show();
            $(".fam_slide_area").show();

            //첫번째 메뉴 swiper
            var swiperFamSlideArea = new Swiper('.fam_slide', {
                pagination: '.swiper-pagination',
                slidesPerView: "auto",
                paginationClickable: true,
                spaceBetween: 0,
                freeMode: true,
                initialSlide: 0,
                pagination: false

            });

            //두번째 contents swiper
            var swiperFamCategory = new Swiper('.fam_category', {
                autoHeight: true,
                slidesPerView: 1,
                loop:true,
                spaceBetween: 0,
                initialSlide: 0,
                pagination: false
            });

            //menu index
            var menuTabLen = $('.fam_slide .sw_menu > li').length;

            $('.fam_slide .sw_menu > li').on('click', function(e){
                $(this).addClass('active').siblings().removeClass('active');
                tabIdx = $(this).index();
                swiperFamCategory.slideTo(tabIdx+1, 300);
                e.preventDefault();
            });

            //두번째 contents slide 일때 이벤트
            swiperFamCategory.on('slideChange', function(){
                var famCategoryIdx = swiperFamCategory.activeIndex - 1;
                if( famCategoryIdx < 0 ) {
                    famCategoryIdx = menuTabLen - 1;
                } else if( famCategoryIdx == menuTabLen ){
                    famCategoryIdx = 0;
                }
                $('.sw_menu > li').removeClass('active').eq(famCategoryIdx).addClass('active');
                if( famCategoryIdx < menuTabLen ) {
                    swiperFamSlideArea.slideTo(famCategoryIdx-1, 300);

                }
            });
        }else {
            $(".btn-family-site").removeClass("active");
            $(".fam_slide_area").hide();
            $(".fam_site_dim").hide();
        }
    });

    //패밀리 사이트
    $('.fam_site_dim').click(function(){
        $(this).hide();
        $(".btn-family-site").removeClass("active");
        $(".fam_slide_area").hide();
    });

    // lnb
    $(".lnb_menu").on("click", function(){
        $(".lnb_all_menu").show();
        $(".lnb_all_menu").animate({
            "left":"0%"
        },300);

        $("html,body").css({
            "height":"100%",
            "overflow":"hidden"
        });

        return false;
    });

    // lnb
    $(".lnb_all_menu").find(".lnb_menu_close").on("click", function(){
        $(".lnb_all_menu").animate({
                "left":"-100%"
            },300,
            function(){
                $(".lnb_all_menu").hide();
            });
        $("html,body").css({
            "height":"auto",
            "overflow":"auto"
        })

        return false;
    });

    //메인 빅배너
    var swiperMainBnrTop = new Swiper('.main_bnr_top', {
		autoplay: {
            disableOnInteraction:false ,
			delay : 4000
        },
        speed : 2000,
        loop:true,
		 pagination: {
            el: '.swiper-pagination',
            type: 'fraction',
             renderFraction: function (currentClass, totalClass) {
                 var total_num = $('.main_bnr_top li').length-2;

                 return '<span class="' + currentClass + '"></span>' +
                     ' / ' +
                     '<span>'+total_num+'</span>';
             },
        }
    });

    //메인 - 선배들의 합격 노하우
    var swiperMainPassTip = new Swiper('.main_bnr_tip', {
        slidesPerView: 1.7,
        spaceBetween: 10
    });

    //메인 - 스타교수
    var swiperMainPassTip = new Swiper('.main_bnr_star', {
        slidesPerView: 2.7,
        spaceBetween: 10
    });


    //강의신청 빅배너
    var swiperBooksBnrTop = new Swiper('.books_bnr_top', {
        pagination: {
            el: '.swiper-pagination',
            type: 'fraction',
        },
		autoplay: {
            delay : 4000,
            disableOnInteraction:false
        },
        speed : 2000,
        loop:true
    });

    //교재신청 빅배너
    var booksBestStore = new Swiper('.books_best_store', {
        slidesPerView: 1.3,
        spaceBetween: 20,
        loop: true,
        centeredSlides: true
    });

    //교재신청 상세보기 - 연계강의
    var swiperMainPassTip = new Swiper('.store_view_bnr', {
        slidesPerView: 1.8,
        spaceBetween: 10
    });

    //교재신청 미리보기
    var booksBestStore = new Swiper('.view_books_list', {
        // slidesPerView: 1.3,
        spaceBetween: 20,
        loop: true,
        centeredSlides: true,
        controller : {
            inverse : true
        },
        navigation : {
            nextEl : '.swiper-next',
            prevEl : '.swiper-prev'
        },
        pagination: {
            el: '.swiper-pagination',
            type: 'fraction',
        },
    });

    // 클릭시 첫번째 슬라이드
    $('.swiper-prev-first').click(swiper,function(){
        booksBestStore.slideTo(1);
    });

    // 클릭시 마지막 슬라이드
    $('.swiper-next-last').click(swiper,function(){
        var slide_wrap = $(this).parent().parent();
        var slide_last = slide_wrap.find('li:last-child').index() -1;

        booksBestStore.slideTo(slide_last);
    });

    //alim dim 클릭시
    $(".alim_dim").click(function(){
        $(".alim_layer").hide();
    });

    // lecture tab
    $(".tab_menu_area .tab_tit li").click(function(){
        var tabMenuIndex = $(this).index();
        var tabTitEl = $(this).parent();

        $(tabTitEl).find("li").each(function(idx){
            $(this).addClass("active");
            if(tabMenuIndex != idx) {
                $(this).removeClass("active");
            }
        });
        $(tabTitEl).parent().find("div.tab_con_box").eq(tabMenuIndex).show().addClass("active").siblings('div').removeClass("active").hide();
    });

    $('#event_guide .btn_area').on('click',function(){
        $(this).toggleClass('on');

        if($(this).hasClass('on')){
            $(this).text('상품안내/환불기준 닫기 ▲');
        }else{
            $(this).text('상품안내/환불기준 펼쳐보기 ▼');
        }
    })
});

//유의사항 팝업
function proRefundview(idx){
    $("#proRefundview" + idx).before("<div class='dimBg' onclick='proRefundviewClose()'></div>")
    $("#proRefundview" + idx).show();
}
function proRefundviewClose(){
    $(".dimBg").remove();
    $(".proRefundview").hide();
}

// 배송환불 안내
function btnClickView(cls){
    $(".btnTit").toggleClass("active");
    var elView = $("."+cls).css("display");
    if(elView == "block"){
        $("."+cls).hide();
        $("."+cls).removeClass("active");
    } else {
        $("."+cls).show();
        $("."+cls).addClass("active");
    }
}

//검색 패널
function btnClickClass(cls){
    $(".btnSearch").toggleClass("active");
    var elView = $("."+cls).css("display");
    if(elView == "block"){
        $("."+cls).hide();
    } else {
        $("."+cls).show();
    }
}


//알림 레이어 오픈
function alimLayer(elId, lec_no, return_url){
    if (elId == 'alimBasket') {
        $.ajax({
            url : "/payment/main/basketInert"
            ,type : "POST"
            ,dataType : "JSON"
            ,data : {
                lec_no : lec_no
                ,return_url : return_url
            },success : function(data) {
                if (data.result) {
                    $("#" + elId).show();
                } else {
                    alimLayer('alimBasketUser', lec_no, return_url);
                }
            }
        })
    }
    else {
        $("#" + elId).show();
    }
}

//알림 레이어 닫기
function alimLayerClose(elId){
    $("#" + elId).hide();
    prevent_scroll('off'); // 스크롤 허용

    if (elId == 'restMember') {
        location.href='/login/main';
    }
}

// 번호입력가능
function fncheckNum(str) {
    var regnum = /^[0-9]*$/;
    if(!regnum.test(str)) {
        return false;
    }
    return true;
}

// email
function checkKorean(str)
{
    var korean = /^[a-zA-Z0-9]*$/;
    if(korean.test(str))
    {
        return true;
    }
    return false;
}

// join_email
function checkEmail(str)
{
    var email = /^[0-9a-zA-Z]([-_.]?[0-9a-zA-Z])*.[a-zA-Z]{2,3}$/i;
    if(email.test(str))
    {
        return true;
    }
    return false;
}

// 특수 문자가 있나 없나 체크
function checkSpecial(str) {
    var special_pattern = /[`~!@#$%^&*|\\\'\";:\/?]/gi;
    if(special_pattern.test(str) == true) {
        return false;
    } else {
        return true;
    }
}


// password
function fnCheckPassword(password)
{
    if(!/^[a-zA-Z0-9]{4,32}$/.test(password))
    {
        alert('비밀번호는 숫자와 영문자 조합으로 4~32자리를 사용해야 합니다.');
        return false;
    }

    var chk_num = password.search(/[0-9]/g);
    var chk_eng = password.search(/[a-z]/ig);
    if(chk_num < 0 || chk_eng < 0)
    {
        alert('비밀번호는 숫자와 영문자를 혼용하여야 합니다.');
        return false;
    }

    if(/(\w)\1\1\1/.test(password))
    {
        alert('비밀번호에 같은 문자를 4번 이상 사용하실 수 없습니다.');
        return false;
    }
    return true;
}

// 주소찾기
function daumPostcode() {

    // 팝업노출
    // $('#find_address_pop').fadeIn();

    new daum.Postcode({
        oncomplete: function(data) {
            // 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.

            // 도로명 주소의 노출 규칙에 따라 주소를 표시한다.
            // 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
            var roadAddr = data.roadAddress; // 도로명 주소 변수
            var extraRoadAddr = ''; // 참고 항목 변수

            // 법정동명이 있을 경우 추가한다. (법정리는 제외)
            // 법정동의 경우 마지막 문자가 "동/로/가"로 끝난다.
            if(data.bname !== '' && /[동|로|가]$/g.test(data.bname)){
                extraRoadAddr += data.bname;
            }
            // 건물명이 있고, 공동주택일 경우 추가한다.
            if(data.buildingName !== '' && data.apartment === 'Y'){
                extraRoadAddr += (extraRoadAddr !== '' ? ', ' + data.buildingName : data.buildingName);
            }
            // 표시할 참고항목이 있을 경우, 괄호까지 추가한 최종 문자열을 만든다.
            if(extraRoadAddr !== ''){
                extraRoadAddr = ' (' + extraRoadAddr + ')';
            }

            // 우편번호와 주소 정보를 해당 필드에 넣는다.
            document.getElementById('uno_new').value = data.zonecode;

            if (data.userSelectedType == 'R') { // 도로명 클릭시
                document.getElementById("home_address").value = roadAddr;
            } else {
                document.getElementById("home_address").value = data.jibunAddress;
            }

            $('#tail_address').val('');
            $('#uno_new_feedback').hide(); // feedback hide
        }
    }).open();
}

// 검색기능 초기화
function searchReNew(className)
{
    $('.' + className).val('');
}

// 3자리수 , 붙이기
function number_format_won(number) {
    if (number == null || number == undefined || number == '') {
        return '-';
    } else {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
}

// 2자리수 0 붙이기
function numberPad(n, width) {
    n = n + '';
    return n.length >= width ? n : new Array(width - n.length + 1).join('0') + n;
}

// 팝업창 오픈시 스크롤 막기
function prevent_scroll(status){
    if(status == 'on'){
        var window_offset = $(window).scrollTop();

        $('html').css({'position':'fixed','top':-window_offset,'left':'0','width':'100%'});
    }else if(status == 'off'){
        var window_offset = parseInt($('html').css('top').replace(/\-|px/g,''));

        $('html').attr('style','');
        $('html,body').scrollTop(window_offset);
    }
}


// 주문번호 생성
function paymentApply(lec_no)
{
    $.ajax({
        url : "/payment/main/lectureMemOrder"
        ,type : "POST"
        ,dataType : "JSON"
        ,async : false
        ,data : {
            lec_no : lec_no
        },success : function(data) {
            if (data.result) {
                location.href='/payment/main?lec_no=' + lec_no + '&orderNum=' + data.orderNum;
            } else {
                alert(data.msg);
            }
        }
    })
}

// 결제전송
// function paymentApplySend(pay_type, lec_no, orderNum, pay_method)
// {
//     location.href='/payment/main/paymentInertCheck?pay_type=' + pay_type + '&lec_no=' + lec_no + '&orderNum=' + orderNum + '&pay_method=' + pay_method;
// }

// 토글 id
function packageToggle(_this)
{
    _this.toggleClass('on');
    _this.parent().parent().next().stop(true, false).slideToggle();
}

// 결제완료페이지
function paymentToggle(_this)
{
    _this.toggleClass('on');
    _this.parent().parent().siblings().stop(true, false).slideToggle();
}

// 토글 id
function packageToggleNotice(_this)
{
    _this.toggleClass('on');
    _this.parent().next().stop(true, false).slideToggle();
}

// 토글 id
function packageToggleHeader(_this)
{
    _this.toggleClass('on');
    _this.next().stop(true, false).slideToggle();
}


// 더보기 버튼 page수 확인
function limitCountCheck(start, end, hide_id)
{
    if (start == end) {
        alert('마지막 페이지입니다.');
        $('.books_more, .books_list_more').hide(); // 더보기버튼 제거
        $('#' + hide_id).hide();
        return;
    }
}

// 체크박스 모두선택
function allCheckBox()
{
    $('.checkbox_length').find('input').prop('checked', true);
    if ($('#all').is(':checked') != true) {
        $('.checkbox_length').find('input').prop('checked', false);
    }
}

// lecture_mem Delete
function lectureMemDelete(order_num)
{
    if (confirm('취소하시면 해당 신청건은 삭제됩니다.\n그래도 취소하시겠습니까?')) {
        $.ajax({
            url : "/payment/payment_delivery/lectureMemDelete"
            ,type : "POST"
            ,dataType : "JSON"
            ,data : {
                order_num : order_num
            },success : function(data) {
                alert(data.msg);
                location.reload();
            }
        })
    }
}

// 샘플강의보기
function samplePlayer(type, lmno, lec_no, leck_kang_name)
{
    var enable_pos = 0;
    var enable_id = 0;
    if(confirm('3G/LTE망에서 동영상 수강 시 가입요금제에 따라 요금이 부과 될 수 있습니다. 재생하시겠습니까?')) {
        var data = {
            lmno : lmno,
            lec_code : lec_no,
            lec_num : leck_kang_name
        }
        $.ajax({
            url : '/player/aquanmanager/play_check',
            type : "POST",
            data : data,
            dataType : "JSON",
            success : function(data){
                if (data.success) {
                    if(data.loading_time > 0){
                        if(confirm("수강 기록이 있습니다. \n "+data.startTime_viewformat+" 부터 강의를 이어서 재생 하시겠습니까?") == true){
                            enable_pos = data.loading_time;
                        }
                    }
                    // enable_pos == 이어듣기 time
                    // enable_id == lecture_loadging_log insert_id
                    enable_id = data.loading_time_no;
                    location.href = "/player/aquanmanager/"+type+"?lmno="+lmno+"&lec_no="+lec_no+"&leck_kang_name="+leck_kang_name+"&enable_pos="+enable_pos+"&enable_id="+enable_id;
                }
            }
        });
    }
}