<?php

class Member_info extends CI_Controller {
	
	function __construct() {
		parent::__construct();
		$this->load->model("login/m_member");
	}
	
	public function index()
	{
		$get = (!empty($_GET['memType'])) ? $this->input->get('memType') : '' ;
		$this->memberInfo();
	}
	
	function memberInfo()
	{
		## �ʼ��� Ȯ��
		if ($this->session->userdata('member_no') == '' || $this->session->userdata('member_id') == '') {
			echo "<script>alert('�α����� �ʿ��� �������Դϴ�. �α����� �̿����ּ���.'); location.href='/login/main?return_url=' + location.pathname</script>";
		}
		
		// Header user_info, coupon_cnt
		$use_point = $this->m_member->getUserPoint($this->session->userdata('member_id')); // use_point (��������Ʈ)
		$used_point = $this->m_member->getUserUsedPoint($this->session->userdata('member_id')); // use_point (��������Ʈ)
		$user_point = ((int)$use_point->use_point - (int)$used_point->used_point);
		$userInfo = $this->m_member->getUserInfo($this->session->userdata('member_no', true), $this->session->userdata('member_id', true));
		$userCouponData = $this->m_member->getUserCoupon($this->session->userdata('member_no', true), $this->session->userdata('member_id', true));
		
		$head = array(
			'_css' => 'member'
			,'_js' => 'member'
			,'user_info' => $userInfo
			,'user_point' => $user_point
			,'user_coupon_cnt' => count($userCouponData)
		);
		
		if ($this->input->get('memType') == 'memUpdate') { // �������� ����
			$memberInfo = $this->m_member->memberInfo($this->session->userdata('member_no', true));
			$memberInfo->lec_object = explode(',', $memberInfo->lec_object);
			$memberInfo->join_route = explode(',', $memberInfo->join_route);
			$memberInfo->email = explode('@', $memberInfo->email);
			$view_page = 'member_info';
			
			$data = array(
				'images' => '//gscdn.hackers.co.kr/haksa2080/images'
				,'memberInfo' => $memberInfo
			);
		}
		
		$this->load->view('common/header',$head);
		$this->load->view('member/'. $view_page, $data);
		$this->load->view('common/footer');
	}
	
	function memberUpdate()
	{
		// zetyx_member_table
		$member = array();
		
		$member_info = $this->m_member->memberInfo($this->input->post('member_no'));
		if ($member_info->password_new != hash('sha256', $this->input->post('password_new'))) {
			echo "<script>alert('�Է��Ͻ� ��й�ȣ�� �����ʽ��ϴ�.'); location.href='/member/member_info?memType=memUpdate';</script>";
			exit;
		}
		
		$member['member_no'] = $this->input->post('member_no');
		$member['email'] = $this->input->post('email') . '@' . $this->input->post('join_email');
		$member['job'] = $this->input->post('user_job'); // ��������
		
		$handphone = $this->input->post('handphone_index');
		if(substr($handphone,0,3) == "010") {
			// -�� ����
			$member['handphone'] = substr($handphone,0,3). "-" . substr($handphone,3,4). "-" . substr($handphone,7,4);
		} else {
			$member['handphone'] = substr($handphone,0,3). "-" . substr($handphone,3,3). "-" . substr($handphone,6,4);
		}
		$member['handphone_index'] = $this->input->post('handphone_index');
		
		$member['birth'] = substr($this->input->post('birth'),0,4) . "-" . substr($this->input->post('birth'),4,2) . "-" . substr($this->input->post('birth'),6,2);
		$member['new_birth'] = $this->input->post('birth');
		
		$member['reg_date'] = time();
		$member['rc_date'] = date('Y-m-d H:i:s');
		$member['rc_ip'] = $_SERVER['REMOTE_ADDR'];
		$member['rc_cnt'] = $this->input->post('rc_cnt');
		
		$member['uno_new'] = $this->input->post('postCode'); // �����ȣ
		$member['home_address'] = $this->input->post('roadAddress_01'); // �ּ�����
		$member['hobby'] = $this->input->post('roadAddress_02'); // �ּһ�����
		
		// zetyx_member_table_detail
		$member_detail = array();
		
		$member_detail['level_edu'] = (int)$this->input->post('level_edu'); // �����з�
		$member_detail['level_edu_major'] = $this->input->post('level_edu_major'); // �����з� ������
		
		$member_detail['question'] = $this->input->post('question'); // ���ǻ���
		$member_detail['hope_majer'] = $this->input->post('hope_majer'); // ����а�
		$member_detail['hope_majer_sub'] = $this->input->post('hope_majer_sub'); // ����а� sub
		$member_detail['lec_object'] = implode(',', $this->input->post('lec_object'));
		$member_detail['user_job'] = $this->input->post('user_job'); // ��������
		$member_detail['join_route'] = implode(',', $this->input->post('join_route')); // ���԰��
		$member_detail['study_object'] = $this->input->post('study_object'); // �н�����
		$member_detail['sms'] = $this->input->post('evt_agree'); // �̸���, ���� ���ŵ���
		
		$memberUpdate = $this->m_member->memberInfoUpdateQuery($member, $member_detail);
		if ($memberUpdate['result'] == 1) {
			echo "<script>alert('ȸ������ �����Ǿ����ϴ�.'); location.href='/';</script>";
		} else {
			echo "<script>alert('ȸ������ ��������!!! �����ڿ� �������ּ���.'); history.back(-1);</script>";
		}
		exit;
	}
	
	
}