var point_rate = 0.05;

$(document).ready(function(){

    if($('.payment .result').length > 0){ // 결과 페이지
        if($('.payment .deposit').length > 0) {
            $('.payment .deposit .result_msg span').last().text('무통장입금이');
        }
    }else{ // 장바구니, 주문내역 갱신
        basket_set();
    }

    if($('.payment .basket').length > 0) { // 장바구니 페이지
        /*장바구니 상품 클릭시*/
        $('.payment .basket .lec_area .lecture .icon_v').on('change',function(){
            $(this).closest('.lecture').toggleClass('active');
            basket_set();
        });

        /*장바구니 삭제 버튼*/
        $('.payment .basket .edit_lec .btn_area .del_lec,' +
            '.payment .basket .lec_area .lecture .close').on('click',function(){
            if($(this).hasClass('close')){
                basketPop('delete',selected_lecNo()); // 장바구니 한개 삭제
            }else{
                if (selected_lec().length == 0) {
                    basketPop('del_empty');
                } else {
                    basketPop('delete',selected_lecNo()); // 장바구니 여러개 삭제
                }
            }
            basket_set();
        });

        /*장바구니 전체 선택*/
        $('.payment .basket .edit_lec #select_all').on('click',function(){
            if($(this).prop('checked')){
                $('.payment .basket .lec_area .lecture').each(function(){
                    $(this).addClass('active')
                    $(this).find('.icon_v').prop('checked',true);
                });
            } else {
                $('.payment .basket .lec_area .lecture').each(function(){
                    $(this).removeClass('active')
                    $(this).find('.icon_v').prop('checked',false);
                });
            }
            basket_set();
        });
    } else { // 결제,결과 페이지
        if($('.order_info').length > 0){ // 결제 페이지
            /*동의버튼 체크*/
            checkBox();

            /*쿠폰 팝업*/
            $('#coupon_pop .evt_coupon input').on('change',function(){
                $('#coupon_pop .evt_coupon').each(function(){
                    $(this).find('img').attr('src',$(this).find('img').attr('src').replace('_on','_off'));
                });
                $(this).parent().find('img').attr('src',$(this).parent().find('img').attr('src').replace('_off','_on'));

                // checked 된 쿠폰번호 입력
                $('#couponNumber').val($(this).val());
            });

            $('.discount .point').on('keyup',function(){
                if($(this).val() == ''){
                    $(this).val(0)
                }

                var cur_val = chgToInt($(this).val());

                $(this).val(addComma(cur_val));
            })
        }
    }

})

function basketPop(warning, lec_no)
{
    //버튼 초기화
    $('#lec_pop .onebox').show();
    $('#lec_pop .btn2').hide();
    $('#lec_pop .btn2 .remove').text('삭제하기');

    prevent_scroll('on');

    if(warning == 'lec_empty') { // 구매 할 상품이 없는 경우
        $('#lec_pop .txt').html('구매하실 강의 혹은 교재를 선택해주세요.')
    }

    if(warning == "delete") { // 상품 삭제
        $('#lec_pop .txt').html('선택하신 강의 혹은<br/>교재를 삭제하시겠습니까?');
        $('#lec_pop .onebox').hide();
        $('#lec_pop .btn2').show();

        $('#lec_pop .remove').one('click',function(){
            basketDelete(lec_no); // 장바구니 리스트 삭제
        });
    }

    if(warning == "del_empty") { // 삭제 할 상품이 없을 경우
        $('#lec_pop .txt').html('삭제하실 강의 혹은 교재를 선택해주세요.');
    }

    if(warning == 'reset'){
        $('#lec_pop .txt').html('입력한 정보를 모두 초기화 하시겠습니까?');
        $('#lec_pop .onebox').hide();
        $('#lec_pop .btn2').show();
        $('#lec_pop .btn2 .remove').text('초기화');

        $('#lec_pop .remove').one('click',function(){
            $('.shipment .tab_button li').last().addClass('on').siblings().removeClass('on'); // 텝활성화 style
            searchReNew('paymentContent'); // 새로입력 활성화
        });
        $(".shipment .tab_button li").first().one('click',function(){
            $(this).addClass('on').siblings().removeClass('on');
        });
    }

    if(warning == 'low_price'){
        $('#lec_pop .txt').html('보유액보다 적은 금액을 입력해주세요.');
    }

    if(warning == 'low_price2'){
        $('#lec_pop .txt').html('100원 단위로 금액을 입력해주세요.');
    }

    if(warning == 'cancel'){
        $('#lec_pop .txt').html('결제를 취소하시겠습니까?<br/>취소 시 입력하신 정보가 모두 초기화됩니다.');
        $('#lec_pop .onebox').hide();
        $('#lec_pop .btn2').show();
        $('#lec_pop .btn2 .remove').text('결제취소');

        $('#lec_pop .remove').one('click',function(){
            history.back(-1);
        });
    }

    if(warning == 'no_coupon'){
        $('#lec_pop .txt').html('적용 가능한 쿠폰이 없습니다.')
    }

    if(warning == 'no_coupon2'){
        $('#lec_pop .txt').html('적립금과 중복사용이 불가능 합니다.')
    }

    if(warning == 'no_point'){
        $('#lec_pop .txt').html('쿠폰과 중복사용이 불가능 합니다.')
    }

    $('#lec_pop').fadeIn(200);

    $('#lec_pop .bg, #lec_pop .close, #lec_pop .btn').on('click',function(){
        $('#lec_pop').fadeOut(200);
        if(warning == 'delete'){
            basket_set();
        }
        $('#lec_pop .bg, #lec_pop .close, #lec_pop .btn').off('click');
        prevent_scroll('off');
    });
}

function basket_set(){
    var lec_arr = selected_lec();

    if($('.payment .basket').length > 0){
        /*장바구니 상품 총개수*/
        var all_lec_num = $('.payment .basket .lec_area .lecture').length;

        /*장바구니 상품 모두 지웠을 경우*/
        if(all_lec_num == 0){
            $('.payment .basket').hide();
            $('.payment .no_basket').show();
            return false;
        }

        /*장바구니 선택된 상품 개수*/
        var select_lec_num = lec_arr.length;

        /*장바구니 선택된 상품 개수*/
        $('.payment .basket .edit_lec .select_lec').text(select_lec_num);

        /*장바구니 상품이 모두 선택 되었을 경우*/
        if(all_lec_num == select_lec_num){
            $('.payment .basket .edit_lec #select_all').prop('checked',true);
        }else{
            $('.payment .basket .edit_lec #select_all').prop('checked',false);
        }

        /*총 상품 개수*/
        $('.payment .price_area .count b').text(select_lec_num);
    }

    /*총 가격 계산*/
    cal_price(lec_arr);
}

function selected_lec(){ // lebel Array
    var lec_arr = [];

    if($('.payment .basket').length > 0){ // 장바구니 페이지
        $('.payment .lec_area .lecture').each(function() {
            if ($(this).find('.icon_v').prop('checked')) {
                lec_arr.push($(this));
            }
        });
    } else { // 결제,결과 페이지
        $('.payment .lec_area .lecture').each(function() {
            lec_arr.push($(this));
        });
    }
    return lec_arr;
}

function selected_lecNo(){ // lec_no Array
    var lec_arr = [];

    if($('.payment .basket').length > 0){ // 장바구니 페이지
        $('.payment .lec_area .lecture').each(function() {
            if ($(this).find('.icon_v').prop('checked')) {
                lec_arr.push($(this).data('lec-no'));
            }
        });
    } else { // 결제,결과 페이지
        $('.payment .lec_area .lecture').each(function() {
            lec_arr.push($(this).data('lec-no'));
        });
    }

    return lec_arr;
}

function cal_price(lec_arr) {
    var all_lec_price = 0;
    var delivery_fee = 0;
    var coupon_price = 0;
    var point_price = sum_point();

    /*주문내역별 포인트 적용*/
    $('.payment .lec_area .lecture').each(function(){
        var point = parseInt(chgToInt($(this).find('.price span').text())*point_rate);

        $(this).find('.save_point b').text(point);
    });

    if($('.order_info').length > 0) { // 결제 페이지

        /*전체 가격, 전체 쿠폰 할인 가격*/
        $('.payment .lec_area .lecture').each(function(){
            all_lec_price += chgToInt($(this).find('.price span').text());

            if($(this).find('.coupon').length != 0){
                coupon_price += chgToInt($(this).find('.coupon').val());
            }
        });

        /*쿠폰할인*/
        if (coupon_price != 0) {
            $('.payment .price_area .coupon_dis').html('<b>' + addComma(coupon_price) + '<b/>');
            $('.payment .price_area .coupon_dis').prepend('- ');
            $('.payment .price_area .coupon_dis').append('원');
        }

        /*적립금사용*/
        if (point_price == '0') {
            $('.payment .price_area .point_use b').html('사용안함');
        } else {
            $('.payment .price_area .point_use b').html(addComma(point_price)+'P');
        }
    }else{
        /*전체 가격*/
        $('.payment .lec_area .lecture').each(function(){
            if($(this).find('.icon_v').prop('checked')){
                all_lec_price += chgToInt($(this).find('.price span').text());
            }
        });
    }

    $('.payment .all_price .price b').text(addComma(all_lec_price + coupon_price + point_price));

    /* 배송비 */
    for(var i=0; i<lec_arr.length; i++) {
        if(all_lec_price < 30000 && selected_lec().length > 0 && lec_arr[i][0].dataset.lecNo > 50000) {
            delivery_fee = 3000;
        }
    }

    /*배달 금액*/
    $('.payment .price_area .delivery b').text(addComma(delivery_fee));

    /*총 결제금액*/
    $('.payment .price_area .final_price b').text(addComma(all_lec_price + delivery_fee));

    /*예상적립포인트*/
    $('.payment .price_area .final_save_point b').text(addComma(parseInt(all_lec_price*point_rate)));
}

function addComma(num){
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function purchase_lec(){
    if(selected_lec().length == 0) {
        basketPop('lec_empty');

    } else {
        var selectFunction = selected_lecNo();
        var selectJoin = selectFunction.join(',');
        $('#purchase_lec').val(selectJoin);

        var selectCode = $('#purchase_lec').val();
        var selectCodeArray = $('#purchase_lec').val().split(',');

        paymentApply(selectCode); // 교재 구매하기
        return;
    }
}

function chgToInt(val){
    return parseInt(val.replace(/[^0-9]/g,''));
}

function checkBox(){
    $('.payment .agreement input[type="checkbox"]').change(function(){
        var wrap_cont = $('.agreement');
        var chk = true;

        if($(this).attr('id').indexOf('agree_all') != -1){
            if($(this).prop('checked')) {
                wrap_cont.find('input[type="checkbox"]').prop('checked',true);
            }else{
                wrap_cont.find('input[type="checkbox"]').prop('checked',false);
            }
        }

        wrap_cont.find('input[type="checkbox"]').not('#agree_all').each(function(){
            if(!$(this).prop('checked')){
                chk = false;
            }
        });

        if(chk) {
            wrap_cont.find('#agree_all').prop('checked',true);
        }else{
            wrap_cont.find('#agree_all').prop('checked',false);
        }
    });
}

function coupon_pop(_this,lec_price){
    var this_point = $(_this).closest('.lecture').find('.point').val();
    var this_set_coupon = $(_this).closest('.lecture').find('.set_coupon').val();

    if(this_point != 0){
        basketPop('no_coupon2');
        return false;
    }

    $('#coupon_pop .evt_coupon').show();
    $('.payment .lecture').each(function(){
        var set_coupon = $(this).find('.set_coupon').val();

        if(this_set_coupon != set_coupon){
            $('#coupon_pop .evt_coupon').each(function(){
                var coupon = $(this).find('.checked_coupon').val();

                if(set_coupon == coupon){
                    $(this).hide();
                }
            });
        }else if(this_set_coupon != ''){
            var set_coupon_obj = $('#coupon_pop .evt_coupon input[value="'+set_coupon+'"]');

            set_coupon_obj.prop('checked',true);
            set_coupon_obj.find('+ img').attr('src',set_coupon_obj.find('+ img').attr('src').replace('_off','_on'));
        }
    });

    $('#coupon_pop').fadeIn(200);

    $('#coupon_pop .check_btn').not('.cancel').on('click',function(){
        var chk = false;

        $('#coupon_pop .evt_coupon').each(function(){
            if($(this).find('.checked_coupon').is(':checked')){
                var discount = $(this).find('.discount_price').text();
                var discount_type = discount.replace(/[0-9]/g,'');

                if(discount_type.indexOf('%') != -1){
                    var dis_price = chgToInt(lec_price) * (chgToInt(discount) / 100);

                    $(_this).closest('.discount').find('.coupon').val(addComma(dis_price));
                    $(_this).closest('.lecture').find('.price span').text(addComma(chgToInt(lec_price)-dis_price)+'원');
                }else{
                    $(_this).closest('.discount').find('.coupon').val(addComma(chgToInt(discount)));
                    $(_this).closest('.lecture').find('.price span').text(addComma(chgToInt(lec_price)-chgToInt(discount))+'원');
                }

                $(_this).closest('.discount').find('.set_coupon').val($(this).find('.checked_coupon').val());

                basket_set();
                coupon_close();

                chk =  true;
            }
        });

        if(!chk){
            basketPop('no_coupon');
            return false;
        }
    });
}

function coupon_close(){
    $('#coupon_pop').fadeOut(200);

    $('#coupon_pop .check_btn').not('.cancel').off('click');

    $('#coupon_pop .evt_coupon').each(function(){
        $(this).find('img').attr('src',$(this).find('img').attr('src').replace('_on','_off'));
        $(this).find('.checked_coupon').prop('checked',false);
    });
};

function point_chk(_this,all_pt,lec_price){
    var coupon_price = chgToInt($(_this).closest('.lecture').find('.coupon').val());
    var all_point = chgToInt(all_pt);
    var lecture_price = chgToInt(lec_price);

    if($(_this).text() == '취소'){
        var set_point = $(_this).closest('.input_area').find('.point').val();

        $('.payment .lecture .mypoint span').text(all_pt+'P');
        $(_this).closest('.lecture').find('.price span').text(lec_price+'원');
        $(_this).closest('.lecture').find('.point span b').text(addComma(lecture_price*point_rate));
        $(_this).closest('.input_area').find('.point').val(0);
        $(_this).text('적용');
        basket_set();
        return false;
    }

    if(coupon_price != 0){
        basketPop('no_point');
        $(_this).val(0);
        return false;
    }

    var point = chgToInt($(_this).closest('.lecture').find('.point').val());

    if((point % 100) != 0){
        basketPop('low_price2');
        return false;
    }

    if(sum_point() > all_point){
        basketPop('low_price');
        return false;
    }else{
        var new_lec_price = lecture_price-point;

        $('.payment .lecture .mypoint span').text(addComma(all_point-sum_point())+'P');
        $(_this).closest('.lecture').find('.price span').text(addComma(new_lec_price)+'원');
        $(_this).closest('.lecture').find('.point span b').text(addComma(new_lec_price*point_rate));
        $(_this).text('취소');
        alert('적립금 적용되었습니다.');
        basket_set();
    }
}

function point_chk2(_this){
    var coupon_price = $(_this).closest('.discount').find('.coupon').val();

    if(coupon_price != 0){
        basketPop('no_point');
        $(_this).val(0)
    }
}

function sum_point(){
    var point_sum = 0;
    $('.payment .lec_area .lecture').each(function() {
        if ($(this).find('.point').length != 0) {
            var use_point = chgToInt($(this).find('.point').val());
            point_sum += use_point;
        }
    });
    return point_sum;
}

function goPayment(pay_type, lec_no, order_num){
    var agree_all = $('#agree_all').prop('checked');
    var pay_method = $('input[name=pay_type_method]:checked').val(); // 무통장, 카드결제 확인
    var lecNoArray = lec_no.split(','); // lec_no Array

    var user_name = $('#user_name').val();
    var uno_new = $('#uno_new').val();
    var home_address = $('#home_address').val();
    var tail_address = $('#tail_address').val();
    var phone_mobi = $('#phone_mobi').val();

    if (pay_type != 'only_lec') { // 교재구매 포함시
        if (user_name == ''){
            alert('배송지명을 입력해주세요.');
            $('#user_name').focus();
            return false;
        }

        if (uno_new == ''){
            alert('주소를 입력해주세요.');
            $('#uno_new').focus();
            return false;
        }

        if (home_address == ''){
            alert('주소를 입력해주세요.');
            $('#home_address').focus();
            return false;
        }

        if (tail_address == ''){
            alert('상세주소를 입력해주세요.');
            $('#tail_address').focus();
            return false;
        }

        if (phone_mobi == ''){
            alert('연락처를 입력해주세요.');
            $('#phone_mobi').focus();
            return false;
        }
    }

    if (pay_method == '' || pay_method == undefined || pay_method == null){
        alert('결제 수단을 선택해주세요.');
        $('#card').focus();
        return false;
    }

    if (!agree_all){
        alert('결제 진행을 위해 필수약관 확인 후 동의해주세요.');
        return false;
    }

    for(var i=0; i<lecNoArray.length; i++) {
        if (parseInt(lecNoArray[i]) > 50000) {
            // $('#pay_type').val('lectureBooks');
            $('#lec_no').val(lec_no);
            $('#order_num').val(order_num);
            $('#pay_method').val(pay_method);

            var paymentApplyBtn = document.getElementById('paymentApplySend');
            paymentApplyBtn.submit();
        } else {
            // $('#pay_type').val('lecture');
            $('#lec_no').val(lec_no);
            $('#order_num').val(order_num);
            $('#pay_method').val(pay_method);

            var paymentApplyBtn = document.getElementById('paymentApplySend');
            paymentApplyBtn.submit();
        }
    }


}

/*팝업창 오픈시 스크롤 막기*/
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


// 장바구니 삭제
function basketDelete(lec_no)
{
    if (lec_no == '') {
        alert('삭제할 강의 또는 교재를 선택해주세요.');
        return;
    }

    var lecJoin = lec_no.join(',');

    $.ajax({
        url: "/payment/main/basketDelete"
        , type: "POST"
        , dataType: "JSON"
        , data: {
            lec_no : lecJoin
        }, success: function (data) {
            alert(data.msg);
            location.href='/payment/payment_basket';
        }
    })
}

// 학습질문 리스트 더보기
function passBookGetPage(total_cnt)
{
    var list_cnt = $('#passBookList > li').length;
    var appendList = "";

    limitCountCheck(list_cnt, total_cnt, 'passBookGetPage');

    $.ajax({
        url: "/payment/payment_delivery/passBookGetPage"
        , type: "POST"
        , dataType: "JSON"
        , data: {
            list_cnt : list_cnt
        }, success: function (data) {
            console.log(data);

            if (data.result) {
                for(var i=0; i<data.passBookGetPage.length; i++) {
                    appendList += "<li>";
                    appendList += "<div class=\"tit\">";
                    appendList += "<span class=\"date\">오늘까지</span><p><span>주문번호</span>"+ data.passBookGetPage[i].order_num +"</p>";
                    appendList += "</div>";
                    appendList += "<div class=\"txt\">";
                    appendList += "<ul>";
                    appendList += "<li><span>은행명</span> "+ data.passBookGetPage[i].bank_name +"</li>";
                    appendList += "<li><span>예금주</span> (주)위더스교육</li>";
                    appendList += "<li><span>계좌번호</span> "+ data.passBookGetPage[i].account_num +"</li>";
                    appendList += "<li class=\"colr\"><span>입금예정금액</span> "+ number_format_won(data.passBookGetPage[i].payment_total_price.total_price) +"원</li>";
                    appendList += "</ul>";
                    appendList += "<ul>";
                    appendList += "<li><span>신청일</span> "+ data.passBookGetPage[i].wdate +"</li>";
                    appendList += "<li><span>입금기한</span> "+ data.passBookGetPage[i].paymentEndDate +"</li>";
                    appendList += "<li style=\"overflow:hidden;\"><span class=\"fl\">강좌/교재</span> <div class=\"fl\">"+ data.passBookGetPage[i].mem_lec_name +"</div></li>";
                    appendList += "<li class=\"colr t2\"><span>총 건수</span> "+ data.passBookGetPage[i].sub_lecture_cnt +"건</li>";
                    appendList += "</ul>";
                    appendList += "</div>";
                    appendList += "<p>※ 계좌번호에 맞게 입금을 하셔야 해당 주문이 정상적으로 처리됩니다.</p>";
                    // appendList += "<div class=\"m_btn_wrap t3\">";
                    // appendList += "<a href=\"#;\" onclick=\"lectureMemDelete('"+ data.passBookGetPage[i].order_num +"');\">취소 신청</a>";
                    // appendList += "</div>";

                    appendList += "</li>";
                }
                $('#passBookList').append(appendList);
            }
        }
    })
}

// 결제완료,취소 내역 불러오기
function lectureMemGetPage(total_cnt)
{
    var list_cnt = $('#lectureMemList > li').length;
    var appendList = "";

    limitCountCheck(list_cnt, total_cnt, 'lectureMemGetPage');

    $.ajax({
        url: "/payment/payment_delivery/lectureMemGetPage"
        , type: "POST"
        , dataType: "JSON"
        , data: {
            list_cnt: list_cnt
        }, success: function (data) {
            console.log(data);

            if (data.result) {
                for(var i=0; i<data.lectureMemList.length; i++) {
                    appendList += "<li>";
                    appendList += "<div class=\"tit\">";
                    appendList += "<span class=\"date "+ data.lectureMemList[i].lec_state_style +"\">"+ data.lectureMemList[i].payment_state +"</span><p><span>주문번호</span>"+ data.lectureMemList[i].order_num +"</p>";
                    appendList += "</div>";
                    appendList += "<div class=\"txt\">";
                    appendList += "<ul>";
                    if (data.lectureMemList[i].lec_no > 50000) {
                        appendList += "<li><span>교재정보</span> "+ data.lectureMemList[i].book_info.book_name +"</li>";
                    } else {
                        appendList += "<li><span>상품정보</span> "+ data.lectureMemList[i].mem_lec_name.replace('<br>', '') +"</li>";
                    }
                    appendList += "<li><span>결제일</span> "+ data.lectureMemList[i].wdate +"</li>";

                    if (data.lectureMemList[i].discount_div != '' || data.lectureMemList[i].discount_div != undefined || data.lectureMemList[i].discount_div != null) {
                        if (parseInt(data.lectureMemList[i].real_price) == parseInt(data.lectureMemList[i].discount_price) || data.lectureMemList[i].discount_percent == '100') {
                            appendList += "<li><span>결제방법</span> 적립금/쿠폰</li>";
                        } else {
                            appendList += "<li><span>결제방법</span> "+ data.lectureMemList[i].payment_way +" (일부) + 적립금/쿠폰</li>";
                        }

                    } else {
                        appendList += "<li><span>결제방법</span> "+ data.lectureMemList[i].payment_way +"</li>";
                    }

                    appendList += "<li class=\"colr\"><span>금액</span> "+ number_format_won(data.lectureMemList[i].payment_total_price.total_price) +"원</li>";
                    appendList += "</ul>";

                    if (data.lectureMemList[i].lec_no > 50000) {
                        appendList += "<ul class=\"t2\">";
                        appendList += "<li style=\"overflow:hidden;\"><span class=\"fl\">배송지정보</span> <div class=\"fl\">"+ data.lectureMemList[i].besong_info.home_address +", "+ data.lectureMemList[i].besong_info.tail_address +" ("+ data.lectureMemList[i].besong_info.uno +")</div></li>";
                        appendList += "</ul>";
                    }
                    appendList += "</div>";

                    appendList += "<div class=\"m_btn_wrap t4\">";
                    if (data.lectureMemList[i].lec_no > 50000) {
                        var besong_no =  numberPad((i + 1) + list_cnt, 2);
                        appendList += "<form action=\"https://delivery.hackers.com/delivery/index.php\" method=\"POST\" id=\"besongSearch_"+ besong_no +"\">";
                        appendList += "<input type=\"hidden\" name=\"delivery_code_"+ besong_no +"\" id=\"delivery_code_"+ besong_no +"\">";
                        appendList += "<input type=\"hidden\" name=\"delivery_number_"+ besong_no +"\" id=\"delivery_number_"+ besong_no +"\" value=\""+ data.lectureMemList[i].book_order +"\">";
                        appendList += "<input type=\"hidden\" name=\"crypt_key\" id=\"crypt_key\" value=\"gP7qls2sla@dl(wks0ekd!!\">";
                        appendList += "</form>";

                        appendList += "<a href=\"#;\" onclick=\"besongSearch("+ data.lectureMemList[i].book_sale +", "+ data.lectureMemList[i].book_order +" , '"+ besong_no +"')\" class=\"more_view\">배송조회</a>";
                    }
                    appendList += "</div>";
                    appendList += "</li>";
                }

                $('#lectureMemList').append(appendList);
            }
        }
    })
}

// 쿠폰(보유)리스트 불러오기
function couponGetList()
{
    var list_cnt = "";
    var total_cnt = "";
    var page_type = "";
    var coupon_state = "";

    if ($('#use_coupon_content').css('display') == 'block') {
        list_cnt = $('#use_coupon_list > li').length;
        total_cnt = $('#userCoupon').val();
        page_type = 'use_coupon_list';
        coupon_state = '';
    } else {
        list_cnt = $('#used_coupon_list > li').length;
        total_cnt = $('#userUsedCoupon').val();
        page_type = 'used_coupon_list';
        coupon_state = 't2';
    }

    var appendList = "";

    limitCountCheck(list_cnt, total_cnt);

    $.ajax({
        url: "/payment/payment_coupon/couponGetList"
        , type: "POST"
        , dataType: "JSON"
        , data: {
            list_cnt : list_cnt
            ,page_type : page_type
        }, success: function (data) {
            // console.log(data);

            if (data.result) {
                for(var i=0; i<data.userCouponData.length; i++) {
                    if (data.userCouponData[i].lucky_percent == 0) {
                        appendList += "<li>";
                        appendList += "<p>";
                        appendList += "<span class=\"name\">"+ data.userCouponData[i].cupone_name +"</span>";
                        appendList += "<span class=\"percent "+ coupon_state +"\">할인금액 쿠폰</span>";
                        appendList += "<span class=\"exp_date\">유효기간 : "+ data.userCouponData[i].start_date +" ~ "+ data.userCouponData[i].end_date +"</span>";
                        appendList += "</p>";
                        appendList += "<div class=\"discount_box\">";
                        appendList += "<img src=\"https://gscdn.hackers.co.kr/haksa2080/images/myclass/bg_cupon2.png\" alt=\"\">";
                        appendList += "<span class=\"discount "+ coupon_state +"\">"+ number_format_won(data.userCouponData[i].lucky_price) +"원 할인</span>";
                        appendList += "</div>";
                        appendList += "</li>";
                    } else {
                        appendList += "<li>";
                        appendList += "<p>";
                        appendList += "<span class=\"name\">"+ data.userCouponData[i].cupone_name +"</span>";
                        appendList += "<span class=\"percent "+ coupon_state +"\">"+ data.userCouponData[i].lucky_percent +"%</span>";
                        appendList += "<span class=\"exp_date\">유효기간 : "+ data.userCouponData[i].start_date +" ~ "+ data.userCouponData[i].end_date +"</span>";
                        appendList += "</p>";
                        appendList += "<div class=\"discount_box\">";
                        appendList += "<img src=\"https://gscdn.hackers.co.kr/haksa2080/images/myclass/bg_cupon2.png\" alt=\"\">";
                        appendList += "<span class=\"discount "+ coupon_state +"\">"+ data.userCouponData[i].lucky_percent +"% 할인</span>";
                        appendList += "</div>";
                        appendList += "</li>";
                    }
                }
                if (page_type == 'use_coupon_list') {
                    $('#use_coupon_list').append(appendList);
                } else {
                    $('#used_coupon_list').append(appendList);
                }
            }
        }
    })
}

// 쿠폰등록
function couponInsertCheck()
{
    $.ajax({
        url: "/payment/payment_coupon/couponInsertCheck"
        , type: "POST"
        , dataType: "JSON"
        , data: {
            coupon_number : $('#coupon_number').val()
        }, success: function (data) {
            alert(data.msg);
            location.reload();
        }
    })

}

// 포인트(보유)리스트 불러오기
function pointGetList()
{
    var list_cnt = "";
    var total_cnt = "";
    var page_type = "";

    if ($('#use_point_content').css('display') == 'block') {
        list_cnt = $('#use_point_list > li').length;
        total_cnt = $('#userPoint').val();
        page_type = 'use_point_list';
    } else {
        list_cnt = $('#used_point_list > li').length;
        total_cnt = $('#userUsedPoint').val();
        page_type = 'used_point_list';
    }

    var appendList = "";

    limitCountCheck(list_cnt, total_cnt);

    $.ajax({
        url: "/payment/payment_point/pointGetList"
        , type: "POST"
        , dataType: "JSON"
        , data: {
            list_cnt : list_cnt
            ,page_type : page_type
        }, success: function (data) {
            console.log(data);

            if (data.result) {
                for(var i=0; i<data.use_point_list.length; i++) {
                    if (page_type == 'use_point_list') {
                        appendList += "<li>";
                        appendList += "<p>";
                        appendList += "<span class=\"name\">"+ data.use_point_list[i].point_title +"</span>";
                        appendList += "<span class=\"percent\">point</span>";
                        appendList += "<span class=\"exp_date\">적립일 : "+ data.use_point_list[i].wdate +"</span>";
                        appendList += "</p>";
                        appendList += "<div class=\"discount_box\">";
                        appendList += "<img src=\"https://gscdn.hackers.co.kr/haksa2080/images/myclass/bg_cupon2.png\" alt=\"\">";
                        appendList += "<span class=\"discount\">"+ number_format_won(data.use_point_list[i].point) +"P</span>";
                        appendList += "</div>";
                        appendList += "</li>";
                    } else {
                        appendList += "<li>";
                        appendList += "<p>";
                        if (data.use_point_list[i].point_state == '1') {
                            appendList += "<span class=\"name\">결제시 사용</span>";
                        } else if (data.use_point_list[i].point_state == '2') {
                            appendList += "<span class=\"name\">기간만료</span>";
                        } else {
                            appendList += "<span class=\"name\">관리자삭제</span>";
                        }
                        appendList += "<span class=\"percent t2\">point</span>";
                        appendList += "<span class=\"exp_date\">적립일 : "+ data.use_point_list[i].wdate +"</span>";
                        appendList += "</p>";
                        appendList += "<div class=\"discount_box\">";
                        appendList += "<img src=\"https://gscdn.hackers.co.kr/haksa2080/images/myclass/bg_cupon2.png\" alt=\"\">";
                        appendList += "<span class=\"discount t2\">"+ number_format_won(data.use_point_list[i].point) +"P</span>";
                        appendList += "</div>";
                        appendList += "</li>";
                    }
                }
                if (page_type == 'use_point_list') {
                    $('#use_point_list').append(appendList);
                } else {
                    $('#used_point_list').append(appendList);
                }
            }
        }
    })
}

// 배송조회
function besongSearch(delivery_name, delivery_number, delivery_code_id)
{
    if (delivery_name == '' || delivery_name == undefined || delivery_name == null) {
        alert('배송준비중입니다. 관리자에 문의해주세요.');
        return;
    }

    if (delivery_number == '' || delivery_number == undefined || delivery_number == null) {
        alert('배송준비중입니다. 관리자에 문의해주세요.');
        return;
    }

    var delivery_code = '';
    switch (delivery_name) {
        case 'CJ대한통운' :
            delivery_code = '04'
            break;
        case '한진택배' :
            delivery_code = '05'
            break;
        case '로젠택배' :
            delivery_code = '06'
            break;
        case '롯데택배' :
            delivery_code = '08'
            break;
        case '건영택배' :
            delivery_code = '18'
            break;
        case '경동택배' :
            delivery_code = '23'
            break;
        case '홈픽택배' :
            delivery_code = '54'
            break;
        case '굿투럭' :
            delivery_code = '40'
            break;
        case '농협택배' :
            delivery_code = '53'
            break;
        case '대신택배' :
            delivery_code = '22'
            break;
        case '세방' :
            delivery_code = '52'
            break;
        case '애니트랙' :
            delivery_code = '43'
            break;
        case '우체국택배' :
            delivery_code = '01'
            break;
        case '일양로지스' :
            delivery_code = '11'
            break;
        case '천일택배' :
            delivery_code = '17'
            break;
        case '한덱스' :
            delivery_code = '20'
            break;
        case '한의사랑택배' :
            delivery_code = '16'
            break;
        case '합동택배' :
            delivery_code = '32'
            break;
        case '호남택배' :
            delivery_code = '45'
            break;
        case 'CU편의점택배' :
            delivery_code = '46'
            break;
        case 'CVSnet편의점택배' :
            delivery_code = '24'
            break;
        case 'KGB택배' :
            delivery_code = '56'
            break;
        case 'KGL네트웍스' :
            delivery_code = '30'
            break;
        case 'SLX' :
            delivery_code = '44'
            break;
    }

    $('#delivery_code_' + delivery_code_id).val(delivery_code);
    if (delivery_code != '' || delivery_code != undefined) {
        $('#besongSearch_' + delivery_code_id).submit();
    }
}