$(document).ready(function(){

	// ���Ǹ���Ʈ cnt Ȯ��
	$('#lectureList_cnt').val($('#lectureList_content > li, #bookStoreList > li').length);
	$('#lectureTotal_cnt').html($('#lectureList_cnt').val());


	//select box show, hide
	$(".select-item-area .default-item").on('click', function() {
		var this_target = $(this).data("target");

		if($(this).hasClass("option-disabled")){
			$("#" + this_target).hide();
		}else if($(this).hasClass("option-selected")){
			$(this).removeClass("option-selected");
			$("#" + this_target).hide();
		}else {
			$(".option-group").hide();
			$(".select-item-area .default-item").removeClass("option-selected");
			$("#" + this_target).show();
			$(this).addClass("option-selected");
		}
	});

	$(".option-group input[type=radio]").on('click', function() {

		$('.default-item').removeClass('option-selected'); // ȭ��ǥ
		$('.option-group').css('display', 'none'); // ����â �ݱ�

        var totalAmt = 0;
		var thisCode1 = $(this).val();
		var thisCode2 = "";
		var arrCode = new Array();
		var dup = false;
		var $seletGroup = $(".option-select-group li");
		var txtRadioTit = $(this).parent().find(".product_title").text();
		var plusAmt = Number(uncomma($(this).parent().find(".num").text()));

		//��ǰ �ߺ� üũ
		$seletGroup.each(function() {
			thisCode2 = $(this).data("code");
			if(thisCode1 == thisCode2) {
				dup = true;
				alert("�ߺ��Դϴ�.\n" + txtRadioTit);
				return false;
			}
		});

		if(dup){
			return false;
		}

		//��ǰ �߰�
		$(".option-select-group ul").append(
			"<li data-lec-code='" + thisCode1 +"'><span class='select_tit'>" + txtRadioTit + "</span>" +
			"<strong><em class='select_amt'>" + comma(plusAmt) + "</em><span>��</span>" +
			"<button type='button' class='select_del' onclick='select_del($(this))' value='"+plusAmt+"'>" +
			"<img src='//gscdn.hackers.co.kr/haksa2080/images/books/select_del.png' alt='����'>" +
			"</button></strong></li>"
		);

		// ���õ� ����, ���� lec_code
		var selectLecCode = new Array();
		$('.option-select-group ul li').each(function(){
			selectLecCode.push($(this).data('lec-code'));
			var selectCode = selectLecCode.join(',');

			$('#selectLecCode').val(selectCode);
		});

        //�հ� ���ϱ�
        $('.option-select-group li').each(function(){
            totalAmt += Number($(this).find('.select_amt').text().replace(',',''));
        });

		// �հ�
		$('#amt_point').html(comma(totalAmt * 0.05)); // ����Ʈ ����
		$(".last_total .alltotal_amt").text(comma(totalAmt));
	});

})

//��ǰ ����
function select_del(_this){
    _this.parent().parent().remove(); // Ŭ���� ����Ʈ ����

    var totalSelect = Number(uncomma($(".select-total .alltotal_amt").text()));
    var minusAmt = Number(_this.val());
    var dataCode = _this.parent().parent("li").data("code");
    var totalMinus = totalSelect - minusAmt;

    // console.log(totalSelect);
    // console.log(minusAmt);

    $(".last_total .alltotal_amt").text(comma(totalMinus));
    $('#amt_point').html(comma(totalMinus * 0.05));
    $(".option-select-group li[data-code='"+dataCode+"']").remove();
};


// �󼼺��� ��ٱ��ϵ��
function lectureViewBasket(elId, return_url)
{
	var selectCode = $('#selectLecCode').val();
	if (selectCode == '') {
		alert('���� Ȥ�� ���� �ɼ��� �������ּ���.');
		return;
	}

	$.ajax({
		url : "/payment/main/basketInertArray"
		,type : "POST"
		,dataType : "JSON"
		,data : {
			lec_no : selectCode
			,return_url : return_url
		},success : function(data) {
			if (data.result) {
				$("#" + elId).show();
				$('.fixed_buy_view').css('bottom', '-423px');
				$('.fixed_buy_dim').css('display', 'none');
			} else {
				// alert(data.msg);
				if (confirm('�̹� ��ٱ��Ͽ� ��ϵǾ��ֽ��ϴ�.\n��ٱ��Ϸ� �̵��Ͻðڽ��ϱ�?')) {
					location.href = data.return_url;
				} else {
					return;
				}
			}
		}
	})
}

// �󼼺��� �ٷα����ϱ�
function lectureViewPayment(payment_type)
{
	var selectCode = $('#selectLecCode').val(); // ���Ű����ڵ�
	var selectCodeArray = $('#selectLecCode').val().split(',');
	var select_item = $('.option-select-group').find('ul li').length;

	if (select_item <= 0) {
		alert('�����Ͻ� �׸��� ������ �����������ּ���.');
		return;
	}

	paymentApply(selectCode);
	return;
}

// ����/���� �˻�
function contentSearch()
{
	var form = document.getElementById('contentSearchForm');
	form.submit();
}

// ���ǽ�û ������
function listGetPageLecture(total_cnt)
{
	limitCountCheck($('#lectureList_cnt').val(), total_cnt);

	$.ajax({
		url : "/lecture/main/lectureListContent"
		,type : "POST"
		,dataType : "JSON"
		,data : {
			page_cnt : $('#lectureList_content > li').length
			, category_seq_num : $('#category_seq_num').val()
			, lec_level : $('#lec_level').val()
			, teacher_list_name : $('#teacher_list_name').val()

		},success : function(data){
			//console.log(data);
			if (data.result) {
				var appendList = "";
				var priceCheck = "";

				for (var i=0; i<data.lecture_list.length; i++) {

					if (data.lecture_list[i].lec_price == data.lecture_list[i].lec_real_price || data.lecture_list[i].lec_price <= data.lecture_list[i].lec_real_price) {
						priceCheck = "display:none;"
					} else {
						priceCheck = "display:block;"
					}

					appendList += "<li>";
						appendList += "<div class=\"books_img\">";
							appendList += "<div class=\"teacherImg\">";
								if (data.lecture_list[i].profile_img != '' && data.lecture_list[i].profile_img != undefined && data.lecture_list[i].profile_img != null) {
									appendList += "<img src=\"https://www.haksa2080.com/zfiles/teacher_img/"+ data.lecture_list[i].profile_img +"\" alt=\"\">";
								} else {
									appendList += '-';
								}
							appendList += "</div>";

						appendList += "<div class=\"books_icon\">";
						appendList += "<span class=\"cl_type\">"+ data.lecture_list[i].major_name +"</span>";
						appendList += "<span>"+ data.lecture_list[i].lec_level +"�ܰ�</span>";

						if (data.lecture_list[i].new_icon != '') {
							appendList += "<span>NEW</span>";
						}

						appendList += "</div>";
						appendList += "<p>"+ data.lecture_list[i].etc +"</p>";
						appendList += "<strong>"+ data.lecture_list[i].t_name +" ������</strong>";
						appendList += "</div>";

						appendList += "<a href=\"/lecture/lecture_view?lec_no="+ data.lecture_list[i].lec_no +"\" class=\"books_name\">";
						appendList += "<dl>";
						appendList += "<dt>"+ data.lecture_list[i].lec_name +"</dt>";
						appendList += "<dd class=\"num_day\">"+ data.lecture_list[i].lec_term +"��(�� "+ data.lecture_list[i].lec_su +"��)</dd>";
						appendList += "<dd class=\"line_txt\"></dd>";
						appendList += "<dd class=\"num_point\">"+ number_format_won(data.lecture_list[i].lec_point) +"P </dd>";
						appendList += "</dl>";
						appendList += "</a>";

						appendList += "<div class=\"books_amt\">";
						appendList += "<dl>";
						appendList += "<dt>������</dt>";
						appendList += "<dd class=\"dc_amt\" style=\""+ priceCheck +"\">"+ number_format_won(data.lecture_list[i].lec_price) +"��</dd>";
						appendList += "<dd>";
						appendList += "<strong>"+ number_format_won(data.lecture_list[i].lec_real_price) +"��</strong>";
						appendList += "<span style=\""+ priceCheck +"\">�� "+ Math.round(data.lecture_list[i].lec_percent) +"% ����</span>";
						appendList += "</dd>";
						appendList += "</dl>";
						appendList += "</div>";

						appendList += "<div class=\"books_btn\">";
						appendList += "<ul class=\"col2\">";
						if (data.member_id != '') {
							appendList += "<li><button type=\"button\" class=\"white_btn\" onclick=\"alimLayer('alimBasket', '"+ data.lecture_list[i].lec_no +"', '/payment/payment_basket')\">��ٱ���</button></li>";
							appendList += "<li><button type=\"button\" class=\"black_btn\" onclick=\"paymentApply('lecture', '"+ data.lecture_list[i].lec_no +"');\">������û</button></li>";
						} else {
							appendList += "<li><button type=\"button\" class=\"white_btn\" onclick=\"alimLayer('alimLogin')\">��ٱ���</button></li>";
							appendList += "<li><button type=\"button\" class=\"black_btn\" onclick=\"alimLayer('alimLogin')\">������û</button></li>";
						}
						appendList += "</ul>";
						appendList += "</div>";
					appendList += "</li>";
				}

				$('#lectureList_content').append(appendList);

				var lectureLength = $('#lectureList_content > li').length;
				$('#lectureList_cnt').val(lectureLength); // input�� update
				$('#lectureTotal_cnt').html(lectureLength); // ��ȸ�� update
			}
		}
	})

	// var form = document.getElementById('lectureList');
	// form.submit();
}


// �����û ������
function listGetPageBook(total_cnt)
{
	limitCountCheck($('#lectureList_cnt').val(), total_cnt);

	$.ajax({
		url : "/books/main/bookStoreListAjax"
		,type : "POST"
		,dataType : "JSON"
		,data : {
			page_cnt : $('#bookStoreList > li').length
			, category_seq_num : $('#category_seq_num').val()
			, lec_level : $('#lec_level').val()
			, author_name : $('#author_name').val()
		},success : function(data){
			// console.log(data);

			if (data.result) {
				var appendList = "";

				for (var i=0; i<data.book_plus_list.length; i++) {

					// console.log(data.book_plus_list[i].books_preview.length);

					appendList += "<li>";
						appendList += "<div class=\"books_store_box\">";
							appendList += "<div class=\"books_icon\">";
								if (data.book_plus_list[i].recommand_type == '1') {
									appendList += "<span class=\"cl_type\">BEST</span>";
								}
								appendList += "<span>"+ data.book_plus_list[i].level +" �ܰ�</span>";
							appendList += "</div>";

							appendList += "<div class=\"books_name\">";
								appendList += "<dl>";
									appendList += "<dt><a href=\"/books/books_view?book_id="+ data.book_plus_list[i].book_id +"\">"+ data.book_plus_list[i].book_name +"</a></dt>";
									appendList += "<dd class=\"num_day\">"+ data.book_plus_list[i].author +"</dd>";
									appendList += "<dd class=\"line_txt\"></dd>";
									appendList += "<dd class=\"num_point\">"+ number_format_won(data.book_plus_list[i].books_point) +"P </dd>";
								appendList += "</dl>";
								appendList += "<span class=\"img_upload\">";
									appendList +="<a href=\"/books/books_view?book_id="+ data.book_plus_list[i].book_id +"\">";
									if (data.book_plus_list[i].book_img != '' && data.book_plus_list[i].book_img != undefined && data.book_plus_list[i].book_img != null) {
										appendList += "<img src=\"https://www.haksa2080.com/zfiles/book_img/"+ data.book_plus_list[i].book_img +"\" alt=\"\">";
									} else {
										appendList += '-';
									}
									appendList += "</a>";
								appendList += "</span>";
							appendList += "</div>";

							appendList += "<div class=\"books_amt\">";
								appendList += "<dl>";
								appendList += "<dt>�ݾ�</dt>";
								appendList += "<dd><strong>"+ number_format_won(data.book_plus_list[i].pamount) +"��</strong></dd>";
								appendList += "</dl>";
							appendList += "</div>";
						appendList += "</div>";

						appendList += "<div class=\"books_btn\">";

							if (data.member_id != '') {
								if (data.book_plus_list[i].level != 1 && data.book_plus_list[i].store_link_yn != 'Y'){
									appendList += "<span class=\"first\"><button type=\"button\" class=\"black_btn\" onclick=\"location.href='/payment/main?pay_type=books&lec_no="+ data.book_plus_list[i].payment_book_code +"'\">�ٷα���</button></span>";
								} else {
									appendList += "<span class=\"first\"><button type=\"button\" class=\"black_btn\" onclick=\"location.href='"+ data.book_plus_list[i].store_link +"'\">�����Ϸ�����</button></span>";
								}
							} else {
								if (data.book_plus_list[i].level != 1 && data.book_plus_list[i].store_link_yn != 'Y'){
									appendList += "<span class=\"first\"><button type=\"button\" class=\"black_btn\" onclick=\"alimLayer('alimLogin')\">�ٷα���</button></span>";
								} else {
									appendList += "<span class=\"first\"><button type=\"button\" class=\"black_btn\" onclick=\"alimLayer('alimLogin')\">�����Ϸ�����</button></span>";
								}
							}

							appendList += "<ul class=\"col2\">";
								if (data.member_id != '') { // member_id check

									if (data.book_plus_list[i].books_preview.length > 0) {
										appendList += "<li><button type=\"button\" class=\"white_btn\" onclick=\"location.href='/books/books_view?preview="+ data.book_plus_list[i].book_id +"'\">�̸�����</button></li>";
										if (data.book_plus_list[i].level != 1 && data.book_plus_list[i].store_link_yn != 'Y') {
											appendList += "<li><button type=\"button\" class=\"white_btn\" onclick=\"alimLayer('alimBasket', '"+ data.book_plus_list[i].book_id +"', '/payment/payment_basket')\">��ٱ���</button></li>";
										} else {
											appendList += "<li><button type=\"button\" class=\"white_btn\" onclick=\"location.href='/books/books_view?book_id=" + data.book_plus_list[i].book_id + "'\">�ڼ�������</button></li>";
										}

									} else {
										if (data.book_plus_list[i].level != 1 && data.book_plus_list[i].store_link_yn != 'Y') {
											appendList += "<li><button type=\"button\" class=\"white_btn\" onclick=\"location.href='/books/books_view?book_id=" + data.book_plus_list[i].book_id + "'\">�ڼ�������</button></li>";
											appendList += "<li><button type=\"button\" class=\"white_btn\" onclick=\"alimLayer('alimBasket', '" + data.book_plus_list[i].book_id + "', '/payment/payment_basket')\">��ٱ���</button></li>";
										} else {
											appendList += "<li style=\"width:100%;\"><button type=\"button\" class=\"white_btn\" onclick=\"location.href='/books/books_view?book_id=" + data.book_plus_list[i].book_id + "'\">�ڼ�������</button></li>";
										}
									}

								} else { // end member_id check
									if (data.book_plus_list[i].books_preview.length > 0) {
										appendList += "<li><button type=\"button\" class=\"white_btn\" onclick=\"location.href='/books/books_view?preview="+ data.book_plus_list[i].book_id +"'\">�̸�����</button></li>";
										if (data.book_plus_list[i].level != 1 && data.book_plus_list[i].store_link_yn != 'Y') {
											appendList += "<li><button type=\"button\" class=\"white_btn\" onclick=\"alimLayer('alimLogin')\">��ٱ���</button></li>";
										} else {
											appendList += "<li><button type=\"button\" class=\"white_btn\" onclick=\"location.href='/books/books_view?book_id=" + data.book_plus_list[i].book_id + "'\">�ڼ�������</button></li>";
										}

									} else {
										if (data.book_plus_list[i].level != 1 && data.book_plus_list[i].store_link_yn != 'Y') {
											appendList += "<li><button type=\"button\" class=\"white_btn\" onclick=\"location.href='/books/books_view?book_id=" + data.book_plus_list[i].book_id + "'\">�ڼ�������</button></li>";
											appendList += "<li><button type=\"button\" class=\"white_btn\" onclick=\"alimLayer('alimLogin')\">��ٱ���</button></li>";
										} else {
											appendList += "<li style=\"width:100%;\"><button type=\"button\" class=\"white_btn\" onclick=\"location.href='/books/books_view?book_id=" + data.book_plus_list[i].book_id + "'\">�ڼ�������</button></li>";
										}
									}

								}

							appendList += "</ul>";
						appendList += "</div>";

					appendList += "</li>";
				}

				$('#bookStoreList').append(appendList);

				var lectureLength = $('#bookStoreList > li').length;
				$('#lectureList_cnt').val(lectureLength); // input�� update
				$('#lectureTotal_cnt').html(lectureLength); // ��ȸ�� update
			}
		}
	})

	// var form = document.getElementById('lectureList');
	// form.submit();
}

//�����ϱ� ��ư ����
function buyFixedOpen(){
	$(".fixed_buy_dim").show();
	$(".fixed_buy_view").animate({bottom:0}, 200);
}

//�����ϱ� ��ư �ݱ�
function buyFixedClose(){
	var viewH = $(".fixed_buy_view").height() +156;
	$(".fixed_buy_dim").hide();
	$(".fixed_buy_view").animate({bottom:"-"+viewH}, 200);
}

//�޸����
function comma(numType) {
	numType = String(numType);
	return numType.replace(/(\d)(?=(?:\d{3})+(?!\d))/g, '$1,');
}

//�޸�����
function uncomma(numType) {
	numType = String(numType);
	return numType.replace(/[^\d]+/g, '');
}

// ���籸���ϱ�
function paymentBook(bookcode)
{
	location.href='/payment/main?pay_type=books&lec_no=' + bookcode;
}