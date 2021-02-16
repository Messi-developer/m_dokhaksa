
<!--팝업 -->
<input type="hidden" name="sel_memo" id="sel_memo" value="">
<div id="counsel_layer" class="event_wrap">
	<div class="bg"></div>
	<div class="smsApply_con">
		<h2>해커스독학사 <span>1:1 무료상담</span></h2>
				<a href="javascript:e_layer_close('counsel_layer');" class="btn_close">X</a>
				<div class="sms_txt_area">
					<p class="txt_top">전문 상담사가 빠르게 도와드리겠습니다.</p>
					<div class="sms_form">
						<div class="write_sms">
							<ul>
								<li>
									<label for="cname" class="blit_sms user_name">이름</label>
									<input required="" hname="연락처" type="text" name="cname" id="cname" placeholder="" style="color:#616161;" maxlength="11">
								</li>
								<li>
									<label for="phone" class="blit_sms user_phone">전화번호</label>
									<input required="" hname="연락처" type="text" name="phone" id="phone" placeholder="" style="color:#616161;" maxlength="11" onkeyup="javascript:this.value = this.value.replace(/[^0-9]/g, '');">
								</li>
							</ul>
						</div>
						<div class="chk_sms">
							<dl>
								<dt>
									<input required="" hname="개인정보 동의" type="checkbox" name="chk_agree_all" id="chk_agree_all" checked="checked" value="Y" onClick="chk_all()">
									<label for="chk_agree_all" ><span></span>모두 동의합니다.</label>
								</dt>
								<dd>
									<input required="" hname="개인정보 동의" type="checkbox" name="chk_agree1" id="chk_agree1" checked="checked" value="Y">
									<label for="chk_agree1" ><span></span>개인정보 수집 및 이용에동의합니다.(필수)</label>
								</dd>
								<dd>
									<input required="" hname="개인정보 동의" type="checkbox" name="chk_agree2" id="chk_agree2" checked="checked" value="Y">
									<label for="chk_agree2" ><span></span>이벤트/할인(광고성)정보 안내에대한 동의합니다.(선택)</label>
								</dd>
							</dl>
						</div>
						<span class="view_detail">
							<a href="javascript:textbook_list_show();" class="txt_book_state">*내용자세히보기</a>
						</span>
						<a href="#" class="layer_btn" onClick="submit_event()"><img src="//gscdn.hackers.co.kr/haksa2080/images/event/img/layer_btn.png" alt="상담신청" border="0"></a>
						<div class="view_wrap">
			<!-- 개인정보 취급방침 -->
			<div class="per_wrap">
				<strong>개인정보</strong>
				<p>
									 1.개인정보 수집/이용 목적 <br>
										- 상담 진행, 교육 과정 설계, 문의사항 응대<br>
									 광고성 정보 수신에 대하여 별도의 동의를 한 회원에 한하여 “위더스교육”과 각 제휴사의 새로운 서비스, 신상품이나 이벤트, 최신 정보의 안내 등 회원의 취향에 맞는 최적의 정보 제공<br>
									 (※제휴사 : 해커스어학원/ 교암/ 챔프스터디/ 옴니넷/ 해커스어학연구소/ 해커스종로/ 해커스/ 해커스유학) <br><br>
									2. 수집 및 이용하는 개인정보 항목 : 이름, 휴대폰 번호 <br><br>
									3.보유 및 이용 기간 : 법령이 정하는 경우를 제외하고는 이용 목적 달성시까지, 또는 이용자의 동의 철회가 있는 때까지 보유함을 원칙으로 합니다. <br><br>
									4. 이용자는 동의를 거부할 권리가 있으나, 동의를 거부하는 경우 상담 서비스 이용이 제한됩니다.
				 </p>
			</div>
			<!-- //개인정보 취급방침 -->
			</div>
		</div>
		</div>
	</div>
</div>
<!--//팝업-->

<script>
		var book_state = 1;
		function textbook_list_show() {
			$(".view_wrap .per_wrap").toggle();

			if (book_state == 1) {
				var state = "(전문보기 닫기)";
				book_state = 0;
			} else {
				var state = "(전문보기)";
				book_state = 1;
			}
			$(".txt_book_state").html(state);
		}
		// 1:1 무료상담
		function submit_event()
		{
			var memo = $("#sel_memo").val();
			if (memo == "")
			{
				alert("패키지 과목을 선택하세요.");
				return;
			}

			var sub_memo = "";

			if (memo == "간호학 안심패키지")
			{
				if ($("#sub_rdio1").is(":checked")) {
					sub_memo = "[교양]국어/국사";
				} else if ($("#sub_rdio2").is(":checked")) {
					sub_memo = "[교양]국어/영어";
				} else if ($("#sub_rdio3").is(":checked")) {
					sub_memo = "[교양]국사/영어";
				}

				if (sub_memo == "") {
					alert("교양 과목을 선택하세요.");
					$("#sub_rdio1").focus();
					return;
				}
				memo = memo + " " + sub_memo;
			} else if (memo == "간호학 벼락치기") {
				if ($("#sub_rdio4").is(":checked")) {
					sub_memo = "[교양]국어/국사";
				} else if ($("#sub_rdio5").is(":checked")) {
					sub_memo = "[교양]국어/영어";
				} else if ($("#sub_rdio6").is(":checked")) {
					sub_memo = "[교양]국사/영어";
				}

				if (sub_memo == "")
				{
					alert("교양 과목을 선택하세요.");
					$("#sub_rdio4").focus();
					return;
				}

				memo = memo + " " + sub_memo;
			}

			var cname = $("#cname").val();
			var phone = $("#phone").val();

			if (cname == "") {
				alert("이름을 입력하세요.");
				$("#cname").focus();
				return;
			}

			if (phone == "") {
				alert("전화번호를 입력하세요.");
				$("#phone").focus();
				return;
			}

			var chk_agree1 = "";

			if ($("#chk_agree1").is(":checked")) {
				chk_agree1 = "Y";
			}

			if (chk_agree1 == "") {
				alert("[개인정보 수집 및 이용에 동의합니다.]에 체크하여 주시기 바랍니다.");
				$("#chk_agree1").focus();
				return;
			}

			$.ajax({
				url : "/ajax_event.php",
				type : "POST",
				dataType: "json",
				data :{
					memo : memo,
					cname : cname,
					phone : phone
				},
				success : function(data){
					if (data.code == "OK") {
						alert("신청 되었습니다.");
						e_layer_close('counsel_layer');
					} else {
						alert("신청 중 오류가 발생하였습니다.");
						e_layer_close('counsel_layer');
					}
				},
				error : function(data){
					alert("신청 중 오류가 발생하였습니다.");
					e_layer_close('counsel_layer');
				}
			});
		}

		function add_memo(memo) {
			$("#sel_memo").val(memo);
			var sub_memo = "";

			if (memo == "어메이징_간호학") {
				if ($("#sub_rdio1").is(":checked")) {
					sub_memo = "[교양]국어/국사";
				} else if ($("#sub_rdio2").is(":checked")) {
					sub_memo = "[교양]국어/영어";
				} else if ($("#sub_rdio3").is(":checked")) {
					sub_memo = "[교양]국사/영어";
				}

				if (sub_memo == "") {
					alert("교양 과목을 선택하세요.");
					$("#sub_rdio1").focus();
					return;
				}
			}

			e_layer_open('counsel_layer');
		}

		// 1:1 무료상담
		function submit_event() {
			var memo = $("#sel_memo").val();
			if (memo == "") {
				alert("패키지 과목을 선택하세요.");
				return;
			}

			var sub_memo = "";

			if (memo == "어메이징_간호학") {
				if ($("#sub_rdio1").is(":checked")) {
					sub_memo = "[교양]국어/국사";
				} else if ($("#sub_rdio2").is(":checked")) {
					sub_memo = "[교양]국어/영어";
				} else if ($("#sub_rdio3").is(":checked")) {
					sub_memo = "[교양]국사/영어";
				}

				if (sub_memo == "") {
					alert("교양 과목을 선택하세요.");
					$("#sub_rdio1").focus();
					return;
				}
				memo = memo + " " + sub_memo;
			}

			var cname = $("#cname").val();
			var phone = $("#phone").val();

			if (cname == "") {
				alert("이름을 입력하세요.");
				$("#cname").focus();
				return;
			}

			if (phone == "") {
				alert("전화번호를 입력하세요.");
				$("#phone").focus();
				return;
			}

			var chk_agree1 = "";

			if ($("#chk_agree1").is(":checked")) {
				chk_agree1 = "Y";
			}

			if (chk_agree1 == "") {
				alert("[개인정보 수집 및 이용에 동의합니다.]에 체크하여 주시기 바랍니다.");
				$("#chk_agree1").focus();
				return;
			}

			$.ajax({
				url: "/ajax_event.php",
				type: "POST",
				dataType: "json",
				data: {
					memo: memo,
					cname: cname,
					phone: phone
				},
				success: function (data) {
					if (data.code == "OK") {
						alert("신청 되었습니다.");
						e_layer_close('counsel_layer');
					} else {
						alert("신청 중 오류가 발생하였습니다.");
						e_layer_close('counsel_layer');
					}
				},
				error: function (data) {
					alert("신청 중 오류가 발생하였습니다.");
					e_layer_close('counsel_layer');
				}
			});
		}

		function chk_all() {
			if ($("#chk_agree_all").is(":checked")) {
				$("#chk_agree1").prop("checked", true);
				$("#chk_agree2").prop("checked", true);
			} else {
				$("#chk_agree1").prop("checked", false);
				$("#chk_agree2").prop("checked", false);
			}
		}
</script>