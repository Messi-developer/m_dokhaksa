$(document).ready(function(){

	//�Ǹ޴�
	$('.my_tab_area li a').on('click',function(){
		var _li = $(this).parent(),
			_idx = _li.index(),
			_div = _li.parent().parent();
			_li.addClass('on').siblings().removeClass('on');
			_div.find('.my_tab_con').eq(_idx).addClass('on').siblings().removeClass('on');
	});

	//���󸮽�Ʈ
	$('.mov_list ul li a').on('click',function(){
		// console.log('aa');
		$(this).find('.bg_box').hide();

	});

});

// ��Ű�� ���ǽ��� check
function packageClassStart(no, lec_no, package_type, lec_state, total_term)
{
	if (lec_state == '1') {
		if (confirm('������� �����Դϴ�. ���Ǹ� �����Ͻðڽ��ϱ�?')) {
			$.ajax({
				url: "/myclass/main/updateLectureState"
				, type: "POST"
				, dataType: "JSON"
				, data: {
					no : no
					,lec_no: lec_no
					,total_term : total_term
				}, success: function (data) {
					if (data.result) {
						location.replace('/myclass/main/getMyclassParentListView?no=' + no);
					} else {
						alert(data.msg);
					}
				}
			});
		}
	} else {
		location.replace('/myclass/main/getMyclassParentListView?no=' + no);
	}
}

// ���ǽ�����
function classStart(no, lec_no, package_type, lec_state, total_term){
	if (lec_state == '1') {
		if (confirm('������� �����Դϴ�. ���Ǹ� �����Ͻðڽ��ϱ�?')) {
			$.ajax({
				url: "/myclass/main/updateLectureState"
				, type: "POST"
				, dataType: "JSON"
				, data: {
					no : no
					,lec_no: lec_no
					,total_term : total_term
				}, success: function (data) {
					if (data.result) {
						location.replace('/myclass/player_list?lec_no=' + lec_no + '&no='+ no +'&package_type=' + package_type);
					} else {
						alert(data.msg);
					}
				}
			});
		}
	} else if (lec_state == '2') {
		location.replace('/myclass/player_list?lec_no=' + lec_no + '&no='+ no +'&package_type=' + package_type);
	}
}


// ����Ŭ����
function checkMyClassType(page_type)
{
	$.ajax({
		url : "/myclass/main/checkMyClassType"
		,type : "POST"
		,dataType : "JSON"
		,data : {
			page_type : page_type
		},success : function(data) {
			console.log(data);

			if (data.result) {
				$('.myClassContent').html(''); // ����Ʈ �ʱ�ȭ
				var appendList = "";
				var appendListSingle = "";

				// alert(data.getLectureList[0].child.length);

				var lectureType = "";
				var lectureState = "";
				var lectureState_actrive = "";
				var lectureStateBtn ="";
				var child_lectureState = "";
				var child_lectureStateBtn = "";
				var checkPackage = "";

					// ��Ű�� ����
					appendList += "<div class=\"lec_list\">"; // lec_list

						appendList += "<div class=\"lec_tit\">";
							appendList += "<a href=\"#;\" class=\"on\">��Ű�� ���� <span class=\"txt_show\" onclick=\"packageToggle($(this))\">��</span></a>";
						appendList += "</div>";

						appendList += "<ul>";

							for(var i=0; i<data.package_lecture_list.length; i++) {

								// ���� ���� Ÿ��
								if (data.package_lecture_list[i].child.length >= 1) {
									lectureType = "��Ű��";
									checkPackage = "package";
								} else {
									lectureType = "�ܰ�";
									checkPackage = "single";
								}

								// ���� ���Ǻ� Ÿ��
								if (data.package_lecture_list[i].lec_state == '1') {
									lectureState = "�������";
									lectureState_actrive = "lec_stay";
									lectureStateBtn = "�������";
								} else if (data.package_lecture_list[i].lec_state == '2') {
									lectureState = "������";
									lectureState_actrive = "";
									lectureStateBtn = "���Ǹ��";
								} else if (data.package_lecture_list[i].lec_state == '3') {
									lectureState = "�����Ϸ�";
								}

								appendList += "<li>";
									appendList += "<div class=\"books_store_box\">"; // books_store_box
										appendList += "<div class=\"books_icon\">";
										appendList += "<span class=\"cl_type\">"+ lectureType +"</span>";
										if (data.package_lecture_list[i].lec_state == '2') {
											appendList += "<span class=\"on\" style=\"margin-left:2px;\">"+ lectureState +"</span>";
										} else {
											appendList += "<span style=\"margin-left:2px;\">"+ lectureState +"</span>";
										}
										appendList += "</div>";

										appendList += "<div class=\"books_name\">";
											appendList += "<dl>";
											appendList += "<dt>"+ data.package_lecture_list[i].mem_lec_name +"</dt>";
											appendList += "<dd class=\"num_name\">"+ data.package_lecture_list[i].teacher_info.teacher_name +"</dd>";
											appendList += "<dd class=\"line_txt\"></dd>";
											appendList += "<dd class=\"num_pen\">�� "+ data.package_lecture_list[i].teacher_info.lec_su +"��</dd>";
											appendList += "<dd class=\"line_txt t2\"></dd>";
											appendList += "<dd class=\"num_day num_day_sub\">"+ data.package_lecture_list[i].total_term +"��";
											if (data.package_lecture_list[i].lec_state != '1') {
												appendList += " ("+ data.package_lecture_list[i].start_date +" ~ "+ data.package_lecture_list[i].end_date +")</dd>";
											}
											appendList += "</dl>";
										appendList += "</div>";
									appendList += "</div>"; // books_store_box end

									appendList += "<div class=\"m_btn_wrap "+ lectureState_actrive +"\">";
										appendList += "<a href=\"#;\" onclick=\"classStart('"+ data.package_lecture_list[i].no +"', '"+ data.package_lecture_list[i].lec_no +"', '"+ checkPackage +"', '"+ data.package_lecture_list[i].lec_state +"', '"+ data.package_lecture_list[i].total_term +"')\">"+ lectureStateBtn +"</a>";
									appendList += "</div>";
								appendList += "</li>";
							}
					appendList += "</ul>";
					appendList += "</div>"; // lec_list end


				$('#myClassContentPackage').html(appendList);

					// �ܰ�����
					appendListSingle += "<div class=\"lec_list\">"; // lec_list

					appendListSingle += "<div class=\"lec_tit\">";
					appendListSingle += "<a href=\"#;\" class=\"on\">�ܰ� ���� <span class=\"txt_show\" onclick=\"packageToggle($(this))\">��</span></a>";
					appendListSingle += "</div>";

					appendListSingle += "<ul>";

					for(var i=0; i<data.single_lecture_list.length; i++) {

						// ���� ���� Ÿ��
						if (data.single_lecture_list[i].child.length >= 1) {
							lectureType = "��Ű��";
							checkPackage = "package";
						} else {
							lectureType = "�ܰ�";
							checkPackage = "single";
						}

						// ���� ���Ǻ� Ÿ��
						if (data.single_lecture_list[i].lec_state == '1') {
							lectureState = "�������";
							lectureState_actrive = "lec_stay";
							lectureStateBtn = "���ǽ���";
						} else if (data.single_lecture_list[i].lec_state == '2') {
							lectureState = "������";
							lectureState_actrive = "";
							lectureStateBtn = "���ǽ� ����";
						} else if (data.single_lecture_list[i].lec_state == '3') {
							lectureState = "�����Ϸ�";
						}

						appendListSingle += "<li>";
						appendListSingle += "<div class=\"books_store_box\">"; // books_store_box
						appendListSingle += "<div class=\"books_icon\">";
						appendListSingle += "<span class=\"cl_type\">"+ lectureType +"</span>";
						if (data.single_lecture_list[i].lec_state == '2') {
							appendListSingle += "<span class=\"on\" style=\"margin-left:2px;\">"+ lectureState +"</span>";
						} else {
							appendListSingle += "<span style=\"margin-left:2px;\">"+ lectureState +"</span>";
						}
						appendListSingle += "</div>";

						appendListSingle += "<div class=\"books_name\">";
						appendListSingle += "<dl>";
						appendListSingle += "<dt>"+ data.single_lecture_list[i].mem_lec_name +"</dt>";
						appendListSingle += "<dd class=\"num_name\">"+ data.single_lecture_list[i].teacher_info.teacher_name +"</dd>";
						appendListSingle += "<dd class=\"line_txt\"></dd>";
						appendListSingle += "<dd class=\"num_pen\">�� "+ data.single_lecture_list[i].teacher_info.lec_su +"��</dd>";
						appendListSingle += "<dd class=\"line_txt t2\"></dd>";
						appendListSingle += "<dd class=\"num_day num_day_sub\">"+ data.single_lecture_list[i].total_term +"��";
						if (data.single_lecture_list[i].lec_state != '1') {
							appendListSingle += " ("+ data.single_lecture_list[i].start_date +" ~ "+ data.single_lecture_list[i].end_date +")</dd>";
						}

						appendListSingle += "</dl>";
						appendListSingle += "</div>";
						appendListSingle += "</div>"; // books_store_box end

						appendListSingle += "<div class=\"m_btn_wrap "+ lectureState_actrive +"\">";
						appendListSingle += "<a href=\"#;\" onclick=\"classStart('"+ data.single_lecture_list[i].no +"', '"+ data.single_lecture_list[i].lec_no +"', '"+ checkPackage +"', '"+ data.single_lecture_list[i].lec_state +"', '"+ data.single_lecture_list[i].total_term +"')\">"+ lectureStateBtn +"</a>";
						appendListSingle += "</div>";
						appendListSingle += "</li>";
					}
					appendListSingle += "</ul>";
					appendListSingle += "</div>"; // lec_list end


				$('#myClassContentSingle').html(appendListSingle);
			}
		}
	})

}

// �÷��̾� ����
function lecture_view(type, lmno, lec_no, leck_kang_name, member_no, member_id){
	var enable_pos = 0;
	var enable_id = 0;

	// LecAttendance Insert
	$.ajax({
		url : '/player/aquanmanager/playerLecAttendanceCheck',
		type : "POST",
		dataType : "JSON",
		async : false,
		data : {
			lmno : lmno
			,seq : leck_kang_name
			,uno : member_no
			,uid : member_id
		},
		success : function(data){
			console.log(data.msg);
		}
	});


	if(confirm('3G/LTE������ ������ ���� �� ���Կ������ ���� ����� �ΰ� �� �� �ֽ��ϴ�.\n����Ͻðڽ��ϱ�?')) {
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


// function lec_data_down(fname){
// 	var url = 'https://hackersjob.com/A_online/myclass/?c=myroom&action=lec_data_down&fname='+fname;
// 	location.href=url;
// }

// ���������� ������ ��ư
function listGetPage(mem_no, lec_no, total_cnt)
{

	var page_cnt = $('#listGetPageConent > li').length;
	limitCountCheck(page_cnt, total_cnt);

	$.ajax({
		url : "/myclass/player_list/getMyclassLecKang"
		,type : "POST"
		,dataType : "JSON"
		,data : {
			lec_no : lec_no
			,page_cnt : page_cnt
		}, success : function(data){
			console.log(data);
			var appendList = "";
			var player_img = "";
			var loading_time = "";
			var loading_state = "";
			var loading_state_style = "";

			for(var i=0; i<data.getLecKang.length; i++){

				if (data.getLecKang[i].player_img == undefined || data.getLecKang[i].player_img == '') {
					player_img = "https://gscdn.hackers.co.kr/haksa2080/images/myclass/test_img.jpg";
				} else {
					player_img = "https://www.haksa2080.com/zfiles/lecLImg/"+ data.getLecKang[i].player_img +"";
				}

				if (data.getLecKang[i].loading_time == '') {
					loading_time = '0��';
					loading_state = '���';
					loading_state_style = '3'
				} else if ((data.getLecKang[i].loading_time / 60) >= data.getLecKang[i].leck_time) {
					loading_time = (data.getLecKang[i].loading_time / 60) + '��';
					loading_state = '�Ϸ�';
					loading_state_style = '2'
				} else {
					loading_time = (data.getLecKang[i].loading_time / 60) + '��';
					loading_state = '������';
					loading_state_style = '1'
				}

				appendList += "<li>";
					appendList += "<div class=\"f_l\">";
						appendList += "<a href=\"#none;\" onclick=\"lecture_view('player_view', '"+ mem_no +"', '"+ lec_no +"', '"+ data.getLecKang[i].leck_kang_name +"'); void 0;\">";
							appendList += "<img src=\""+ player_img +"\" alt=\"\">";
							appendList += "<div class=\"bg_box\">";
								appendList += "<img src=\"//gscdn.hackers.co.kr/haksa2080/images/myclass/bg_mov.png\" alt=\"\">";
								appendList += "<span class=\"time\">"+ data.getLecKang[i].leck_time +" : 00</span>";
							appendList += "</div>";
						appendList += "</a>";
					appendList += "</div>";

					appendList += "<div class=\"f_r\">";
						appendList += "<strong><span>�� "+ data.getLecKang[i].leck_kang_name.replace('[', '') +"��</span>"+ data.getLecKang[i].lec_content[0] +"</strong>";
						appendList += "<ul>";
							appendList += "<li>�������� : <span class=\"t"+ loading_state_style +"\"> "+ loading_state +"</span> </li>";
							appendList += "<li>��û�ð� : "+ loading_time +" </li>";
						appendList += "</ul>";
					appendList += "</div>";
				appendList += "</li>";
			}
			$('#listGetPageConent').append(appendList);
		}
	})
}

// ���������û submit
function lecReqfromSubmit()
{
	if ($('#upload-name').val() == '' || $('#upload-name').val() == undefined) {
		alert('�ʼ������� ��� �Է��� �ּ���.');
		$('#upload-name').focus();
		return;
	}

	if ($('.checkbox_length > input[type=checkbox]:checked').size() == 0) {
		alert('�ʼ������� ��� �Է��� �ּ���.');
		$('#all').focus();
		return;
	}

	// if ($('#hackers_helper').val() == '' || $('#hackers_helper').val() == undefined) {
	// 	alert('��Ŀ���� �����̵Ǿ������� �Է����ּ���.');
	// 	$('#hackers_helper').focus();
	// 	return;
	// }
	//
	// if ($('#hackers_hope').val() == '' || $('#hackers_hope').val() == undefined) {
	// 	alert('��Ŀ���� �ٶ������ �Է����ּ���.');
	// 	$('#hackers_hope').focus();
	// 	return;
	// }

	$('#lecReqfromSubmit').submit();
}

// �������� ����Ʈ ������
function lecReqGetPage(total_cnt)
{
	var list_cnt = $('#lecReq_list > li').length;
	var appendList = "";

	limitCountCheck(list_cnt, total_cnt);

	$.ajax({
		url: "/myclass/extension/extensionListAjax"
		, type: "POST"
		, dataType: "JSON"
		, async : false
		, data: {
			mode : 'lecReqView'
			,list_cnt: list_cnt
		}, success: function (data) {
			console.log(data);

			var mem_lec_name = "";
			var req_state = "";
			var req_state_style = "";

			if (data.result){

				for(var i=0; i<data.lecReqGetPage.length; i++) {

					if (data.lecReqGetPage[i].lec_req_state == '1') {
						req_state = '��û�Ϸ�';
						req_state_style = '#0097a3';
					} else if (data.lecReqGetPage[i].lec_req_state == '2') {
						req_state = '����Ϸ�';
						req_state_style = '#8f69b2';
					} else {
						req_state = '�ݷ�';
						req_state_style = '#d61e1e';
					}

					if (data.lecReqGetPage[i].mem_lec_name == '' || data.lecReqGetPage[i].mem_lec_name == undefined) {
						mem_lec_name = '-';
					} else {
						mem_lec_name = data.lecReqGetPage[i].mem_lec_name;
					}

					appendList += "<li>";
						appendList += "<ul>";
						appendList += "<li>";
						appendList += "<span>�����û����</span>";
						appendList += "<strong>"+ mem_lec_name +"</strong>";
						appendList += "</li>";
						appendList += "<li>";
						appendList += "<span>����</span>";
						appendList += "<strong class=\"t1\" style=\"color:"+ req_state_style +"\">"+ req_state +"</strong>";
						appendList += "</li>";
						appendList += "<li>";
						appendList += "<span>�����ö��</span>";
						appendList += "<strong>"+ data.lecReqGetPage[i].regdate.substring(0, 10) +"</strong>";
						appendList += "</li>";

						appendList += "<li>";
						appendList += "<span>����Ⱓ</span>";
						if (data.lecReqGetPage[i].end_no != '' && data.lecReqGetPage[i].end_no != undefined && data.lecReqGetPage[i].end_no != null) {
							appendList += "<strong>"+ data.lecReqGetPage[i].end_no.replace(':', '-') +"</strong>";
						} else {
							appendList += "<strong>-</strong>";
						}

						appendList += "</li>";
						appendList += "</ul>";
						appendList += "<div class=\"m_btn_wrap\">";
						appendList += "<a href=\"extension/extensionView?lecReq="+ data.lecReqGetPage[i].lecReqNo +"&lmno="+ data.lecReqGetPage[i].no +"\" class=\"more_view\">�ڼ�������</a>";
						appendList += "</div>";
					appendList += "</li>";

				}

				$('#lecReq_list').append(appendList);
			}
		}
	})
}

// �н����� ����Ʈ ������
function HKboardGetPage(total_cnt)
{
	var list_cnt = $('#HKboardBoardList > li').length;
	var appendList = "";

	limitCountCheck(list_cnt, total_cnt);

	$.ajax({
		url: "/myclass/learning_qna/learning_qna_listAjax"
		, type: "POST"
		, dataType: "JSON"
		, data: {
			list_cnt : list_cnt
		}, success: function (data) {
			console.log(data);

			var review_text = "";
			var review_style = "";
			var big_category = "";
			var division = "";

			if (data.result) {
				for(var i=0; i<data.board_list.length; i++) {

					// ��� ����Ȯ��
					if (data.board_list[i].answer_cnt > 0){
						review_text = '�亯�Ϸ�';
						review_style = '';
					} else {
						review_text = '�亯�����';
						review_style = 't2';
					}

					// ī�װ� ��з��� Ȯ��
					if ((data.board_list[i].big_category.name == '' || data.board_list[i].big_category.name == undefined)) {
						big_category = '��з���X';
					} else {
						big_category = data.board_list[i].big_category.name;
					}

					if (data.board_list[i].division.indexOf('[lecture]') != -1) {
						division = data.board_list[i].division.replace(/\[lecture\]/g, ' [ ���� ]');
					} else {
						division = data.board_list[i].division.replace(/\[book\]/g, ' [ ���� ]');
					}

					appendList += "<li>";
						appendList += "<div class=\"q_box\">";
							appendList += "<span class=\"state "+ review_style +"\">"+ review_text +"</span>";
							appendList += "<a href=\"#;\" >"+ data.board_list[i].subject +" <span class=\"txt_show\" onclick=\"packageToggle($(this))\">��</span></a>";
							appendList += "<ul class=\"cler\">";
								appendList += "<li>"+ data.board_list[i].c_wdate +"</li>";
								appendList += "<li class=\"line_txt\"></li>";
								appendList += "<li>"+ division +"</li>";
							appendList += "</ul>";
						appendList += "</div>";

						appendList += "<div class=\"a_box\">";


							appendList += "<div class=\"q_view\">";
								appendList += "<strong>"+ division +"</strong>";
								appendList += "<ul class=\"cler\">";
									appendList += "<li>"+ big_category +"</li>";
									appendList += "<li class=\"line_txt\"></li>";
									appendList += "<li>"+ data.board_list[i].islevel +"�ܰ�</li>";
								appendList += "</ul>";
								appendList += "<p>"+ data.board_list[i].memo +"</p>";
							appendList += "</div>";

							if (data.board_list[i].answer_cnt > 0) {
								for (var j=0; j<data.board_list[i].board_review.length; j++) {
									appendList += "<div class=\"a_view\">";
										appendList += "<h5>"+ data.board_list[i].board_review[j].name +"������</h5>";
										appendList += "<p>"+ data.board_list[i].board_review[j].memo +"</p>";
									appendList += "</div>";
								}
							}

						appendList += "</div>";

					appendList += "</li>";
				}

				$('#HKboardBoardList').append(appendList);
			}

		}
	})

}

// �н����� ����
function HKboardDelete(board_id, board_no)
{
	if (confirm('�ۼ��Ͻű� ���� �����Ͻðڽ��ϱ�?')){
		$.ajax({
			url: "/myclass/learning_qna/learning_qna_delete"
			, type: "POST"
			, dataType: "JSON"
			, data: {
				board_id : board_id
				, board_no : board_no
			}, success: function (data) {
				alert(data.msg);
				location.reload();
			}
		})
	}
}

// ����Ŭ���� ���Ǹ���Ʈ -> �ٿ�ε���
function downloadFile(lec_no)
{
	if (confirm('�����Ǵ� �����ڷ�� ���Ǻ� �ѹ����� �ٿ�����ø� �˴ϴ�. �ٿ�����ðڽ��ϱ�?')){
		location.href="/myclass/player_list/fileDownLoad?lec_no=" + lec_no;
	}
}
