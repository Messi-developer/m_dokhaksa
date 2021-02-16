$(document).ready(function() {

    // GNB swiper
    var swiper = new Swiper('.swiper-container', {
        slidesPerView: 3,
        grabCursor: true
    });

    //�ڷΰ���
    $('.his_back').bind('touchstart click',function(e){
        history.back(-1);
        e.preventDefault();
    });

    //�����Ǹ޴�
    $('.tab_area li a').on('click',function(){
        var _li = $(this).parent(),
            _idx = _li.index(),
            _div = _li.parent().parent();

        _li.addClass('on').siblings().removeClass('on');

        _div.find('.tab_con').eq(_idx).addClass('on').siblings().removeClass('on');
        autoMap.printMap();
    });

    // ��ü����
    $('.subCheckbox').on('change', function(){
        if ($('.checkbox_length > input[type=checkbox]:checked').size() != $('.checkbox_length').length) {
            $('#all').prop('checked', false);
        } else {
            $('#all').prop('checked', true);
        }
    })

    // ����÷�α��
    var fileTarget = $('.filebox #upload-name');
    fileTarget.on('change', function () {  // ���� ����Ǹ�
        if (window.FileReader) {  // modern browser
            var filename = $(this)[0].files[0].name;
        }
        else {  // old IE
            var filename = $(this).val().split('/').pop().split('\\').pop();  // ���ϸ� ����
        }
        // ������ ���ϸ� ����
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

            //ù��° �޴� swiper
            var swiperFamSlideArea = new Swiper('.fam_slide', {
                pagination: '.swiper-pagination',
                slidesPerView: "auto",
                paginationClickable: true,
                spaceBetween: 0,
                freeMode: true,
                initialSlide: 0,
                pagination: false

            });

            //�ι�° contents swiper
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

            //�ι�° contents slide �϶� �̺�Ʈ
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

    //�йи� ����Ʈ
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

    //���� ����
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

    //���� - ������� �հ� ���Ͽ�
    var swiperMainPassTip = new Swiper('.main_bnr_tip', {
        slidesPerView: 1.7,
        spaceBetween: 10
    });

    //���� - ��Ÿ����
    var swiperMainPassTip = new Swiper('.main_bnr_star', {
        slidesPerView: 2.7,
        spaceBetween: 10
    });


    //���ǽ�û ����
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

    //�����û ����
    var booksBestStore = new Swiper('.books_best_store', {
        slidesPerView: 1.3,
        spaceBetween: 20,
        loop: true,
        centeredSlides: true
    });

    //�����û �󼼺��� - ���谭��
    var swiperMainPassTip = new Swiper('.store_view_bnr', {
        slidesPerView: 1.8,
        spaceBetween: 10
    });

    //�����û �̸�����
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

    // Ŭ���� ù��° �����̵�
    $('.swiper-prev-first').click(swiper,function(){
        booksBestStore.slideTo(1);
    });

    // Ŭ���� ������ �����̵�
    $('.swiper-next-last').click(swiper,function(){
        var slide_wrap = $(this).parent().parent();
        var slide_last = slide_wrap.find('li:last-child').index() -1;

        booksBestStore.slideTo(slide_last);
    });

    //alim dim Ŭ����
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
            $(this).text('��ǰ�ȳ�/ȯ�ұ��� �ݱ� ��');
        }else{
            $(this).text('��ǰ�ȳ�/ȯ�ұ��� ���ĺ��� ��');
        }
    })
});

//���ǻ��� �˾�
function proRefundview(idx){
    $("#proRefundview" + idx).before("<div class='dimBg' onclick='proRefundviewClose()'></div>")
    $("#proRefundview" + idx).show();
}
function proRefundviewClose(){
    $(".dimBg").remove();
    $(".proRefundview").hide();
}

// ���ȯ�� �ȳ�
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

//�˻� �г�
function btnClickClass(cls){
    $(".btnSearch").toggleClass("active");
    var elView = $("."+cls).css("display");
    if(elView == "block"){
        $("."+cls).hide();
    } else {
        $("."+cls).show();
    }
}


//�˸� ���̾� ����
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

//�˸� ���̾� �ݱ�
function alimLayerClose(elId){
    $("#" + elId).hide();
    prevent_scroll('off'); // ��ũ�� ���

    if (elId == 'restMember') {
        location.href='/login/main';
    }
}

// ��ȣ�Է°���
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

// Ư�� ���ڰ� �ֳ� ���� üũ
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
        alert('��й�ȣ�� ���ڿ� ������ �������� 4~32�ڸ��� ����ؾ� �մϴ�.');
        return false;
    }

    var chk_num = password.search(/[0-9]/g);
    var chk_eng = password.search(/[a-z]/ig);
    if(chk_num < 0 || chk_eng < 0)
    {
        alert('��й�ȣ�� ���ڿ� �����ڸ� ȥ���Ͽ��� �մϴ�.');
        return false;
    }

    if(/(\w)\1\1\1/.test(password))
    {
        alert('��й�ȣ�� ���� ���ڸ� 4�� �̻� ����Ͻ� �� �����ϴ�.');
        return false;
    }
    return true;
}

// �ּ�ã��
function daumPostcode() {

    // �˾�����
    // $('#find_address_pop').fadeIn();

    new daum.Postcode({
        oncomplete: function(data) {
            // �˾����� �˻���� �׸��� Ŭ�������� ������ �ڵ带 �ۼ��ϴ� �κ�.

            // ���θ� �ּ��� ���� ��Ģ�� ���� �ּҸ� ǥ���Ѵ�.
            // �������� ������ ���� ���� ��쿣 ����('')���� �����Ƿ�, �̸� �����Ͽ� �б� �Ѵ�.
            var roadAddr = data.roadAddress; // ���θ� �ּ� ����
            var extraRoadAddr = ''; // ���� �׸� ����

            // ���������� ���� ��� �߰��Ѵ�. (�������� ����)
            // �������� ��� ������ ���ڰ� "��/��/��"�� ������.
            if(data.bname !== '' && /[��|��|��]$/g.test(data.bname)){
                extraRoadAddr += data.bname;
            }
            // �ǹ����� �ְ�, ���������� ��� �߰��Ѵ�.
            if(data.buildingName !== '' && data.apartment === 'Y'){
                extraRoadAddr += (extraRoadAddr !== '' ? ', ' + data.buildingName : data.buildingName);
            }
            // ǥ���� �����׸��� ���� ���, ��ȣ���� �߰��� ���� ���ڿ��� �����.
            if(extraRoadAddr !== ''){
                extraRoadAddr = ' (' + extraRoadAddr + ')';
            }

            // �����ȣ�� �ּ� ������ �ش� �ʵ忡 �ִ´�.
            document.getElementById('uno_new').value = data.zonecode;

            if (data.userSelectedType == 'R') { // ���θ� Ŭ����
                document.getElementById("home_address").value = roadAddr;
            } else {
                document.getElementById("home_address").value = data.jibunAddress;
            }

            $('#tail_address').val('');
            $('#uno_new_feedback').hide(); // feedback hide
        }
    }).open();
}

// �˻���� �ʱ�ȭ
function searchReNew(className)
{
    $('.' + className).val('');
}

// 3�ڸ��� , ���̱�
function number_format_won(number) {
    if (number == null || number == undefined || number == '') {
        return '-';
    } else {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
}

// 2�ڸ��� 0 ���̱�
function numberPad(n, width) {
    n = n + '';
    return n.length >= width ? n : new Array(width - n.length + 1).join('0') + n;
}

// �˾�â ���½� ��ũ�� ����
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


// �ֹ���ȣ ����
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

// ��������
// function paymentApplySend(pay_type, lec_no, orderNum, pay_method)
// {
//     location.href='/payment/main/paymentInertCheck?pay_type=' + pay_type + '&lec_no=' + lec_no + '&orderNum=' + orderNum + '&pay_method=' + pay_method;
// }

// ��� id
function packageToggle(_this)
{
    _this.toggleClass('on');
    _this.parent().parent().next().stop(true, false).slideToggle();
}

// �����Ϸ�������
function paymentToggle(_this)
{
    _this.toggleClass('on');
    _this.parent().parent().siblings().stop(true, false).slideToggle();
}

// ��� id
function packageToggleNotice(_this)
{
    _this.toggleClass('on');
    _this.parent().next().stop(true, false).slideToggle();
}

// ��� id
function packageToggleHeader(_this)
{
    _this.toggleClass('on');
    _this.next().stop(true, false).slideToggle();
}


// ������ ��ư page�� Ȯ��
function limitCountCheck(start, end, hide_id)
{
    if (start == end) {
        alert('������ �������Դϴ�.');
        $('.books_more, .books_list_more').hide(); // �������ư ����
        $('#' + hide_id).hide();
        return;
    }
}

// üũ�ڽ� ��μ���
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
    if (confirm('����Ͻø� �ش� ��û���� �����˴ϴ�.\n�׷��� ����Ͻðڽ��ϱ�?')) {
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

// ���ð��Ǻ���
function samplePlayer(type, lmno, lec_no, leck_kang_name)
{
    var enable_pos = 0;
    var enable_id = 0;
    if(confirm('3G/LTE������ ������ ���� �� ���Կ������ ���� ����� �ΰ� �� �� �ֽ��ϴ�. ����Ͻðڽ��ϱ�?')) {
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
                        if(confirm("���� ����� �ֽ��ϴ�. \n "+data.startTime_viewformat+" ���� ���Ǹ� �̾ ��� �Ͻðڽ��ϱ�?") == true){
                            enable_pos = data.loading_time;
                        }
                    }
                    // enable_pos == �̾��� time
                    // enable_id == lecture_loadging_log insert_id
                    enable_id = data.loading_time_no;
                    location.href = "/player/aquanmanager/"+type+"?lmno="+lmno+"&lec_no="+lec_no+"&leck_kang_name="+leck_kang_name+"&enable_pos="+enable_pos+"&enable_id="+enable_id;
                }
            }
        });
    }
}