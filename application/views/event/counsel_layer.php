
<!--�˾� -->
<input type="hidden" name="sel_memo" id="sel_memo" value="">
<div id="counsel_layer" class="event_wrap">
	<div class="bg"></div>
	<div class="smsApply_con">
		<h2>��Ŀ�����л� <span>1:1 ������</span></h2>
				<a href="javascript:e_layer_close('counsel_layer');" class="btn_close">X</a>
				<div class="sms_txt_area">
					<p class="txt_top">���� ���簡 ������ ���͵帮�ڽ��ϴ�.</p>
					<div class="sms_form">
						<div class="write_sms">
							<ul>
								<li>
									<label for="cname" class="blit_sms user_name">�̸�</label>
									<input required="" hname="����ó" type="text" name="cname" id="cname" placeholder="" style="color:#616161;" maxlength="11">
								</li>
								<li>
									<label for="phone" class="blit_sms user_phone">��ȭ��ȣ</label>
									<input required="" hname="����ó" type="text" name="phone" id="phone" placeholder="" style="color:#616161;" maxlength="11" onkeyup="javascript:this.value = this.value.replace(/[^0-9]/g, '');">
								</li>
							</ul>
						</div>
						<div class="chk_sms">
							<dl>
								<dt>
									<input required="" hname="�������� ����" type="checkbox" name="chk_agree_all" id="chk_agree_all" checked="checked" value="Y" onClick="chk_all()">
									<label for="chk_agree_all" ><span></span>��� �����մϴ�.</label>
								</dt>
								<dd>
									<input required="" hname="�������� ����" type="checkbox" name="chk_agree1" id="chk_agree1" checked="checked" value="Y">
									<label for="chk_agree1" ><span></span>�������� ���� �� �̿뿡�����մϴ�.(�ʼ�)</label>
								</dd>
								<dd>
									<input required="" hname="�������� ����" type="checkbox" name="chk_agree2" id="chk_agree2" checked="checked" value="Y">
									<label for="chk_agree2" ><span></span>�̺�Ʈ/����(����)���� �ȳ������� �����մϴ�.(����)</label>
								</dd>
							</dl>
						</div>
						<span class="view_detail">
							<a href="javascript:textbook_list_show();" class="txt_book_state">*�����ڼ�������</a>
						</span>
						<a href="#" class="layer_btn" onClick="submit_event()"><img src="//gscdn.hackers.co.kr/haksa2080/images/event/img/layer_btn.png" alt="����û" border="0"></a>
						<div class="view_wrap">
			<!-- �������� ��޹�ħ -->
			<div class="per_wrap">
				<strong>��������</strong>
				<p>
									 1.�������� ����/�̿� ���� <br>
										- ��� ����, ���� ���� ����, ���ǻ��� ����<br>
									 ���� ���� ���ſ� ���Ͽ� ������ ���Ǹ� �� ȸ���� ���Ͽ� ���������������� �� ���޻��� ���ο� ����, �Ż�ǰ�̳� �̺�Ʈ, �ֽ� ������ �ȳ� �� ȸ���� ���⿡ �´� ������ ���� ����<br>
									 (�����޻� : ��Ŀ�����п�/ ����/ è�����͵�/ �ȴϳ�/ ��Ŀ�����п�����/ ��Ŀ������/ ��Ŀ��/ ��Ŀ������) <br><br>
									2. ���� �� �̿��ϴ� �������� �׸� : �̸�, �޴��� ��ȣ <br><br>
									3.���� �� �̿� �Ⱓ : ������ ���ϴ� ��츦 �����ϰ�� �̿� ���� �޼��ñ���, �Ǵ� �̿����� ���� öȸ�� �ִ� ������ �������� ��Ģ���� �մϴ�. <br><br>
									4. �̿��ڴ� ���Ǹ� �ź��� �Ǹ��� ������, ���Ǹ� �ź��ϴ� ��� ��� ���� �̿��� ���ѵ˴ϴ�.
				 </p>
			</div>
			<!-- //�������� ��޹�ħ -->
			</div>
		</div>
		</div>
	</div>
</div>
<!--//�˾�-->

<script>
		var book_state = 1;
		function textbook_list_show() {
			$(".view_wrap .per_wrap").toggle();

			if (book_state == 1) {
				var state = "(�������� �ݱ�)";
				book_state = 0;
			} else {
				var state = "(��������)";
				book_state = 1;
			}
			$(".txt_book_state").html(state);
		}
		// 1:1 ������
		function submit_event()
		{
			var memo = $("#sel_memo").val();
			if (memo == "")
			{
				alert("��Ű�� ������ �����ϼ���.");
				return;
			}

			var sub_memo = "";

			if (memo == "��ȣ�� �Ƚ���Ű��")
			{
				if ($("#sub_rdio1").is(":checked")) {
					sub_memo = "[����]����/����";
				} else if ($("#sub_rdio2").is(":checked")) {
					sub_memo = "[����]����/����";
				} else if ($("#sub_rdio3").is(":checked")) {
					sub_memo = "[����]����/����";
				}

				if (sub_memo == "") {
					alert("���� ������ �����ϼ���.");
					$("#sub_rdio1").focus();
					return;
				}
				memo = memo + " " + sub_memo;
			} else if (memo == "��ȣ�� ����ġ��") {
				if ($("#sub_rdio4").is(":checked")) {
					sub_memo = "[����]����/����";
				} else if ($("#sub_rdio5").is(":checked")) {
					sub_memo = "[����]����/����";
				} else if ($("#sub_rdio6").is(":checked")) {
					sub_memo = "[����]����/����";
				}

				if (sub_memo == "")
				{
					alert("���� ������ �����ϼ���.");
					$("#sub_rdio4").focus();
					return;
				}

				memo = memo + " " + sub_memo;
			}

			var cname = $("#cname").val();
			var phone = $("#phone").val();

			if (cname == "") {
				alert("�̸��� �Է��ϼ���.");
				$("#cname").focus();
				return;
			}

			if (phone == "") {
				alert("��ȭ��ȣ�� �Է��ϼ���.");
				$("#phone").focus();
				return;
			}

			var chk_agree1 = "";

			if ($("#chk_agree1").is(":checked")) {
				chk_agree1 = "Y";
			}

			if (chk_agree1 == "") {
				alert("[�������� ���� �� �̿뿡 �����մϴ�.]�� üũ�Ͽ� �ֽñ� �ٶ��ϴ�.");
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
						alert("��û �Ǿ����ϴ�.");
						e_layer_close('counsel_layer');
					} else {
						alert("��û �� ������ �߻��Ͽ����ϴ�.");
						e_layer_close('counsel_layer');
					}
				},
				error : function(data){
					alert("��û �� ������ �߻��Ͽ����ϴ�.");
					e_layer_close('counsel_layer');
				}
			});
		}

		function add_memo(memo) {
			$("#sel_memo").val(memo);
			var sub_memo = "";

			if (memo == "�����¡_��ȣ��") {
				if ($("#sub_rdio1").is(":checked")) {
					sub_memo = "[����]����/����";
				} else if ($("#sub_rdio2").is(":checked")) {
					sub_memo = "[����]����/����";
				} else if ($("#sub_rdio3").is(":checked")) {
					sub_memo = "[����]����/����";
				}

				if (sub_memo == "") {
					alert("���� ������ �����ϼ���.");
					$("#sub_rdio1").focus();
					return;
				}
			}

			e_layer_open('counsel_layer');
		}

		// 1:1 ������
		function submit_event() {
			var memo = $("#sel_memo").val();
			if (memo == "") {
				alert("��Ű�� ������ �����ϼ���.");
				return;
			}

			var sub_memo = "";

			if (memo == "�����¡_��ȣ��") {
				if ($("#sub_rdio1").is(":checked")) {
					sub_memo = "[����]����/����";
				} else if ($("#sub_rdio2").is(":checked")) {
					sub_memo = "[����]����/����";
				} else if ($("#sub_rdio3").is(":checked")) {
					sub_memo = "[����]����/����";
				}

				if (sub_memo == "") {
					alert("���� ������ �����ϼ���.");
					$("#sub_rdio1").focus();
					return;
				}
				memo = memo + " " + sub_memo;
			}

			var cname = $("#cname").val();
			var phone = $("#phone").val();

			if (cname == "") {
				alert("�̸��� �Է��ϼ���.");
				$("#cname").focus();
				return;
			}

			if (phone == "") {
				alert("��ȭ��ȣ�� �Է��ϼ���.");
				$("#phone").focus();
				return;
			}

			var chk_agree1 = "";

			if ($("#chk_agree1").is(":checked")) {
				chk_agree1 = "Y";
			}

			if (chk_agree1 == "") {
				alert("[�������� ���� �� �̿뿡 �����մϴ�.]�� üũ�Ͽ� �ֽñ� �ٶ��ϴ�.");
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
						alert("��û �Ǿ����ϴ�.");
						e_layer_close('counsel_layer');
					} else {
						alert("��û �� ������ �߻��Ͽ����ϴ�.");
						e_layer_close('counsel_layer');
					}
				},
				error: function (data) {
					alert("��û �� ������ �߻��Ͽ����ϴ�.");
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