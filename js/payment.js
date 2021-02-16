var point_rate = 0.05;

$(document).ready(function(){

    if($('.payment .result').length > 0){ // ��� ������
        if($('.payment .deposit').length > 0) {
            $('.payment .deposit .result_msg span').last().text('�������Ա���');
        }
    }else{ // ��ٱ���, �ֹ����� ����
        basket_set();
    }

    if($('.payment .basket').length > 0) { // ��ٱ��� ������
        /*��ٱ��� ��ǰ Ŭ����*/
        $('.payment .basket .lec_area .lecture .icon_v').on('change',function(){
            $(this).closest('.lecture').toggleClass('active');
            basket_set();
        });

        /*��ٱ��� ���� ��ư*/
        $('.payment .basket .edit_lec .btn_area .del_lec,' +
            '.payment .basket .lec_area .lecture .close').on('click',function(){
            if($(this).hasClass('close')){
                basketPop('delete',selected_lecNo()); // ��ٱ��� �Ѱ� ����
            }else{
                if (selected_lec().length == 0) {
                    basketPop('del_empty');
                } else {
                    basketPop('delete',selected_lecNo()); // ��ٱ��� ������ ����
                }
            }
            basket_set();
        });

        /*��ٱ��� ��ü ����*/
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
    } else { // ����,��� ������
        if($('.order_info').length > 0){ // ���� ������
            /*���ǹ�ư üũ*/
            checkBox();

            /*���� �˾�*/
            $('#coupon_pop .evt_coupon input').on('change',function(){
                $('#coupon_pop .evt_coupon').each(function(){
                    $(this).find('img').attr('src',$(this).find('img').attr('src').replace('_on','_off'));
                });
                $(this).parent().find('img').attr('src',$(this).parent().find('img').attr('src').replace('_off','_on'));

                // checked �� ������ȣ �Է�
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
    //��ư �ʱ�ȭ
    $('#lec_pop .onebox').show();
    $('#lec_pop .btn2').hide();
    $('#lec_pop .btn2 .remove').text('�����ϱ�');

    prevent_scroll('on');

    if(warning == 'lec_empty') { // ���� �� ��ǰ�� ���� ���
        $('#lec_pop .txt').html('�����Ͻ� ���� Ȥ�� ���縦 �������ּ���.')
    }

    if(warning == "delete") { // ��ǰ ����
        $('#lec_pop .txt').html('�����Ͻ� ���� Ȥ��<br/>���縦 �����Ͻðڽ��ϱ�?');
        $('#lec_pop .onebox').hide();
        $('#lec_pop .btn2').show();

        $('#lec_pop .remove').one('click',function(){
            basketDelete(lec_no); // ��ٱ��� ����Ʈ ����
        });
    }

    if(warning == "del_empty") { // ���� �� ��ǰ�� ���� ���
        $('#lec_pop .txt').html('�����Ͻ� ���� Ȥ�� ���縦 �������ּ���.');
    }

    if(warning == 'reset'){
        $('#lec_pop .txt').html('�Է��� ������ ��� �ʱ�ȭ �Ͻðڽ��ϱ�?');
        $('#lec_pop .onebox').hide();
        $('#lec_pop .btn2').show();
        $('#lec_pop .btn2 .remove').text('�ʱ�ȭ');

        $('#lec_pop .remove').one('click',function(){
            $('.shipment .tab_button li').last().addClass('on').siblings().removeClass('on'); // ��Ȱ��ȭ style
            searchReNew('paymentContent'); // �����Է� Ȱ��ȭ
        });
        $(".shipment .tab_button li").first().one('click',function(){
            $(this).addClass('on').siblings().removeClass('on');
        });
    }

    if(warning == 'low_price'){
        $('#lec_pop .txt').html('�����׺��� ���� �ݾ��� �Է����ּ���.');
    }

    if(warning == 'low_price2'){
        $('#lec_pop .txt').html('100�� ������ �ݾ��� �Է����ּ���.');
    }

    if(warning == 'cancel'){
        $('#lec_pop .txt').html('������ ����Ͻðڽ��ϱ�?<br/>��� �� �Է��Ͻ� ������ ��� �ʱ�ȭ�˴ϴ�.');
        $('#lec_pop .onebox').hide();
        $('#lec_pop .btn2').show();
        $('#lec_pop .btn2 .remove').text('�������');

        $('#lec_pop .remove').one('click',function(){
            history.back(-1);
        });
    }

    if(warning == 'no_coupon'){
        $('#lec_pop .txt').html('���� ������ ������ �����ϴ�.')
    }

    if(warning == 'no_coupon2'){
        $('#lec_pop .txt').html('�����ݰ� �ߺ������ �Ұ��� �մϴ�.')
    }

    if(warning == 'no_point'){
        $('#lec_pop .txt').html('������ �ߺ������ �Ұ��� �մϴ�.')
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
        /*��ٱ��� ��ǰ �Ѱ���*/
        var all_lec_num = $('.payment .basket .lec_area .lecture').length;

        /*��ٱ��� ��ǰ ��� ������ ���*/
        if(all_lec_num == 0){
            $('.payment .basket').hide();
            $('.payment .no_basket').show();
            return false;
        }

        /*��ٱ��� ���õ� ��ǰ ����*/
        var select_lec_num = lec_arr.length;

        /*��ٱ��� ���õ� ��ǰ ����*/
        $('.payment .basket .edit_lec .select_lec').text(select_lec_num);

        /*��ٱ��� ��ǰ�� ��� ���� �Ǿ��� ���*/
        if(all_lec_num == select_lec_num){
            $('.payment .basket .edit_lec #select_all').prop('checked',true);
        }else{
            $('.payment .basket .edit_lec #select_all').prop('checked',false);
        }

        /*�� ��ǰ ����*/
        $('.payment .price_area .count b').text(select_lec_num);
    }

    /*�� ���� ���*/
    cal_price(lec_arr);
}

function selected_lec(){ // lebel Array
    var lec_arr = [];

    if($('.payment .basket').length > 0){ // ��ٱ��� ������
        $('.payment .lec_area .lecture').each(function() {
            if ($(this).find('.icon_v').prop('checked')) {
                lec_arr.push($(this));
            }
        });
    } else { // ����,��� ������
        $('.payment .lec_area .lecture').each(function() {
            lec_arr.push($(this));
        });
    }
    return lec_arr;
}

function selected_lecNo(){ // lec_no Array
    var lec_arr = [];

    if($('.payment .basket').length > 0){ // ��ٱ��� ������
        $('.payment .lec_area .lecture').each(function() {
            if ($(this).find('.icon_v').prop('checked')) {
                lec_arr.push($(this).data('lec-no'));
            }
        });
    } else { // ����,��� ������
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

    /*�ֹ������� ����Ʈ ����*/
    $('.payment .lec_area .lecture').each(function(){
        var point = parseInt(chgToInt($(this).find('.price span').text())*point_rate);

        $(this).find('.save_point b').text(point);
    });

    if($('.order_info').length > 0) { // ���� ������

        /*��ü ����, ��ü ���� ���� ����*/
        $('.payment .lec_area .lecture').each(function(){
            all_lec_price += chgToInt($(this).find('.price span').text());

            if($(this).find('.coupon').length != 0){
                coupon_price += chgToInt($(this).find('.coupon').val());
            }
        });

        /*��������*/
        if (coupon_price != 0) {
            $('.payment .price_area .coupon_dis').html('<b>' + addComma(coupon_price) + '<b/>');
            $('.payment .price_area .coupon_dis').prepend('- ');
            $('.payment .price_area .coupon_dis').append('��');
        }

        /*�����ݻ��*/
        if (point_price == '0') {
            $('.payment .price_area .point_use b').html('������');
        } else {
            $('.payment .price_area .point_use b').html(addComma(point_price)+'P');
        }
    }else{
        /*��ü ����*/
        $('.payment .lec_area .lecture').each(function(){
            if($(this).find('.icon_v').prop('checked')){
                all_lec_price += chgToInt($(this).find('.price span').text());
            }
        });
    }

    $('.payment .all_price .price b').text(addComma(all_lec_price + coupon_price + point_price));

    /* ��ۺ� */
    for(var i=0; i<lec_arr.length; i++) {
        if(all_lec_price < 30000 && selected_lec().length > 0 && lec_arr[i][0].dataset.lecNo > 50000) {
            delivery_fee = 3000;
        }
    }

    /*��� �ݾ�*/
    $('.payment .price_area .delivery b').text(addComma(delivery_fee));

    /*�� �����ݾ�*/
    $('.payment .price_area .final_price b').text(addComma(all_lec_price + delivery_fee));

    /*������������Ʈ*/
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

        paymentApply(selectCode); // ���� �����ϱ�
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
                    $(_this).closest('.lecture').find('.price span').text(addComma(chgToInt(lec_price)-dis_price)+'��');
                }else{
                    $(_this).closest('.discount').find('.coupon').val(addComma(chgToInt(discount)));
                    $(_this).closest('.lecture').find('.price span').text(addComma(chgToInt(lec_price)-chgToInt(discount))+'��');
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

    if($(_this).text() == '���'){
        var set_point = $(_this).closest('.input_area').find('.point').val();

        $('.payment .lecture .mypoint span').text(all_pt+'P');
        $(_this).closest('.lecture').find('.price span').text(lec_price+'��');
        $(_this).closest('.lecture').find('.point span b').text(addComma(lecture_price*point_rate));
        $(_this).closest('.input_area').find('.point').val(0);
        $(_this).text('����');
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
        $(_this).closest('.lecture').find('.price span').text(addComma(new_lec_price)+'��');
        $(_this).closest('.lecture').find('.point span b').text(addComma(new_lec_price*point_rate));
        $(_this).text('���');
        alert('������ ����Ǿ����ϴ�.');
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
    var pay_method = $('input[name=pay_type_method]:checked').val(); // ������, ī����� Ȯ��
    var lecNoArray = lec_no.split(','); // lec_no Array

    var user_name = $('#user_name').val();
    var uno_new = $('#uno_new').val();
    var home_address = $('#home_address').val();
    var tail_address = $('#tail_address').val();
    var phone_mobi = $('#phone_mobi').val();

    if (pay_type != 'only_lec') { // ���籸�� ���Խ�
        if (user_name == ''){
            alert('��������� �Է����ּ���.');
            $('#user_name').focus();
            return false;
        }

        if (uno_new == ''){
            alert('�ּҸ� �Է����ּ���.');
            $('#uno_new').focus();
            return false;
        }

        if (home_address == ''){
            alert('�ּҸ� �Է����ּ���.');
            $('#home_address').focus();
            return false;
        }

        if (tail_address == ''){
            alert('���ּҸ� �Է����ּ���.');
            $('#tail_address').focus();
            return false;
        }

        if (phone_mobi == ''){
            alert('����ó�� �Է����ּ���.');
            $('#phone_mobi').focus();
            return false;
        }
    }

    if (pay_method == '' || pay_method == undefined || pay_method == null){
        alert('���� ������ �������ּ���.');
        $('#card').focus();
        return false;
    }

    if (!agree_all){
        alert('���� ������ ���� �ʼ���� Ȯ�� �� �������ּ���.');
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

/*�˾�â ���½� ��ũ�� ����*/
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


// ��ٱ��� ����
function basketDelete(lec_no)
{
    if (lec_no == '') {
        alert('������ ���� �Ǵ� ���縦 �������ּ���.');
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

// �н����� ����Ʈ ������
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
                    appendList += "<span class=\"date\">���ñ���</span><p><span>�ֹ���ȣ</span>"+ data.passBookGetPage[i].order_num +"</p>";
                    appendList += "</div>";
                    appendList += "<div class=\"txt\">";
                    appendList += "<ul>";
                    appendList += "<li><span>�����</span> "+ data.passBookGetPage[i].bank_name +"</li>";
                    appendList += "<li><span>������</span> (��)����������</li>";
                    appendList += "<li><span>���¹�ȣ</span> "+ data.passBookGetPage[i].account_num +"</li>";
                    appendList += "<li class=\"colr\"><span>�Աݿ����ݾ�</span> "+ number_format_won(data.passBookGetPage[i].payment_total_price.total_price) +"��</li>";
                    appendList += "</ul>";
                    appendList += "<ul>";
                    appendList += "<li><span>��û��</span> "+ data.passBookGetPage[i].wdate +"</li>";
                    appendList += "<li><span>�Աݱ���</span> "+ data.passBookGetPage[i].paymentEndDate +"</li>";
                    appendList += "<li style=\"overflow:hidden;\"><span class=\"fl\">����/����</span> <div class=\"fl\">"+ data.passBookGetPage[i].mem_lec_name +"</div></li>";
                    appendList += "<li class=\"colr t2\"><span>�� �Ǽ�</span> "+ data.passBookGetPage[i].sub_lecture_cnt +"��</li>";
                    appendList += "</ul>";
                    appendList += "</div>";
                    appendList += "<p>�� ���¹�ȣ�� �°� �Ա��� �ϼž� �ش� �ֹ��� ���������� ó���˴ϴ�.</p>";
                    // appendList += "<div class=\"m_btn_wrap t3\">";
                    // appendList += "<a href=\"#;\" onclick=\"lectureMemDelete('"+ data.passBookGetPage[i].order_num +"');\">��� ��û</a>";
                    // appendList += "</div>";

                    appendList += "</li>";
                }
                $('#passBookList').append(appendList);
            }
        }
    })
}

// �����Ϸ�,��� ���� �ҷ�����
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
                    appendList += "<span class=\"date "+ data.lectureMemList[i].lec_state_style +"\">"+ data.lectureMemList[i].payment_state +"</span><p><span>�ֹ���ȣ</span>"+ data.lectureMemList[i].order_num +"</p>";
                    appendList += "</div>";
                    appendList += "<div class=\"txt\">";
                    appendList += "<ul>";
                    if (data.lectureMemList[i].lec_no > 50000) {
                        appendList += "<li><span>��������</span> "+ data.lectureMemList[i].book_info.book_name +"</li>";
                    } else {
                        appendList += "<li><span>��ǰ����</span> "+ data.lectureMemList[i].mem_lec_name.replace('<br>', '') +"</li>";
                    }
                    appendList += "<li><span>������</span> "+ data.lectureMemList[i].wdate +"</li>";

                    if (data.lectureMemList[i].discount_div != '' || data.lectureMemList[i].discount_div != undefined || data.lectureMemList[i].discount_div != null) {
                        if (parseInt(data.lectureMemList[i].real_price) == parseInt(data.lectureMemList[i].discount_price) || data.lectureMemList[i].discount_percent == '100') {
                            appendList += "<li><span>�������</span> ������/����</li>";
                        } else {
                            appendList += "<li><span>�������</span> "+ data.lectureMemList[i].payment_way +" (�Ϻ�) + ������/����</li>";
                        }

                    } else {
                        appendList += "<li><span>�������</span> "+ data.lectureMemList[i].payment_way +"</li>";
                    }

                    appendList += "<li class=\"colr\"><span>�ݾ�</span> "+ number_format_won(data.lectureMemList[i].payment_total_price.total_price) +"��</li>";
                    appendList += "</ul>";

                    if (data.lectureMemList[i].lec_no > 50000) {
                        appendList += "<ul class=\"t2\">";
                        appendList += "<li style=\"overflow:hidden;\"><span class=\"fl\">���������</span> <div class=\"fl\">"+ data.lectureMemList[i].besong_info.home_address +", "+ data.lectureMemList[i].besong_info.tail_address +" ("+ data.lectureMemList[i].besong_info.uno +")</div></li>";
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

                        appendList += "<a href=\"#;\" onclick=\"besongSearch("+ data.lectureMemList[i].book_sale +", "+ data.lectureMemList[i].book_order +" , '"+ besong_no +"')\" class=\"more_view\">�����ȸ</a>";
                    }
                    appendList += "</div>";
                    appendList += "</li>";
                }

                $('#lectureMemList').append(appendList);
            }
        }
    })
}

// ����(����)����Ʈ �ҷ�����
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
                        appendList += "<span class=\"percent "+ coupon_state +"\">���αݾ� ����</span>";
                        appendList += "<span class=\"exp_date\">��ȿ�Ⱓ : "+ data.userCouponData[i].start_date +" ~ "+ data.userCouponData[i].end_date +"</span>";
                        appendList += "</p>";
                        appendList += "<div class=\"discount_box\">";
                        appendList += "<img src=\"https://gscdn.hackers.co.kr/haksa2080/images/myclass/bg_cupon2.png\" alt=\"\">";
                        appendList += "<span class=\"discount "+ coupon_state +"\">"+ number_format_won(data.userCouponData[i].lucky_price) +"�� ����</span>";
                        appendList += "</div>";
                        appendList += "</li>";
                    } else {
                        appendList += "<li>";
                        appendList += "<p>";
                        appendList += "<span class=\"name\">"+ data.userCouponData[i].cupone_name +"</span>";
                        appendList += "<span class=\"percent "+ coupon_state +"\">"+ data.userCouponData[i].lucky_percent +"%</span>";
                        appendList += "<span class=\"exp_date\">��ȿ�Ⱓ : "+ data.userCouponData[i].start_date +" ~ "+ data.userCouponData[i].end_date +"</span>";
                        appendList += "</p>";
                        appendList += "<div class=\"discount_box\">";
                        appendList += "<img src=\"https://gscdn.hackers.co.kr/haksa2080/images/myclass/bg_cupon2.png\" alt=\"\">";
                        appendList += "<span class=\"discount "+ coupon_state +"\">"+ data.userCouponData[i].lucky_percent +"% ����</span>";
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

// �������
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

// ����Ʈ(����)����Ʈ �ҷ�����
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
                        appendList += "<span class=\"exp_date\">������ : "+ data.use_point_list[i].wdate +"</span>";
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
                            appendList += "<span class=\"name\">������ ���</span>";
                        } else if (data.use_point_list[i].point_state == '2') {
                            appendList += "<span class=\"name\">�Ⱓ����</span>";
                        } else {
                            appendList += "<span class=\"name\">�����ڻ���</span>";
                        }
                        appendList += "<span class=\"percent t2\">point</span>";
                        appendList += "<span class=\"exp_date\">������ : "+ data.use_point_list[i].wdate +"</span>";
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

// �����ȸ
function besongSearch(delivery_name, delivery_number, delivery_code_id)
{
    if (delivery_name == '' || delivery_name == undefined || delivery_name == null) {
        alert('����غ����Դϴ�. �����ڿ� �������ּ���.');
        return;
    }

    if (delivery_number == '' || delivery_number == undefined || delivery_number == null) {
        alert('����غ����Դϴ�. �����ڿ� �������ּ���.');
        return;
    }

    var delivery_code = '';
    switch (delivery_name) {
        case 'CJ�������' :
            delivery_code = '04'
            break;
        case '�����ù�' :
            delivery_code = '05'
            break;
        case '�����ù�' :
            delivery_code = '06'
            break;
        case '�Ե��ù�' :
            delivery_code = '08'
            break;
        case '�ǿ��ù�' :
            delivery_code = '18'
            break;
        case '�浿�ù�' :
            delivery_code = '23'
            break;
        case 'Ȩ���ù�' :
            delivery_code = '54'
            break;
        case '������' :
            delivery_code = '40'
            break;
        case '�����ù�' :
            delivery_code = '53'
            break;
        case '����ù�' :
            delivery_code = '22'
            break;
        case '����' :
            delivery_code = '52'
            break;
        case '�ִ�Ʈ��' :
            delivery_code = '43'
            break;
        case '��ü���ù�' :
            delivery_code = '01'
            break;
        case '�Ͼ������' :
            delivery_code = '11'
            break;
        case 'õ���ù�' :
            delivery_code = '17'
            break;
        case '�ѵ���' :
            delivery_code = '20'
            break;
        case '���ǻ���ù�' :
            delivery_code = '16'
            break;
        case '�յ��ù�' :
            delivery_code = '32'
            break;
        case 'ȣ���ù�' :
            delivery_code = '45'
            break;
        case 'CU�������ù�' :
            delivery_code = '46'
            break;
        case 'CVSnet�������ù�' :
            delivery_code = '24'
            break;
        case 'KGB�ù�' :
            delivery_code = '56'
            break;
        case 'KGL��Ʈ����' :
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