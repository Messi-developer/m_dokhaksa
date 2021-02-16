<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Payment_coupon extends CI_Controller {
	
	function __construct() {
		parent::__construct();
		$this->load->model("login/m_member");
		$this->load->model("lecture/m_lecture");
		$this->load->model("payment/m_payment");
	}
	
	public function index()
	{
		$this->PaymentCoupon();
	}
	
	## ����/��۳��� view page
	public function PaymentCoupon()
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
		
		// view (html)
		$userCouponList = $this->m_member->getUserCouponList($this->session->userdata('member_no', true), $this->session->userdata('member_id', true));
		$userEndCouponList = $this->m_member->getUserEndCouponList($this->session->userdata('member_no', true), $this->session->userdata('member_id', true));
		$userEndCouponListCnt = $this->m_member->getUserEndCouponListCnt($this->session->userdata('member_no', true), $this->session->userdata('member_id', true));
		
		$head = array(
			'_css' => 'payment'
			,'_js' => 'payment'
			,'user_info' => $userInfo
			,'user_point' => $user_point
			,'user_coupon_cnt' => count($userCouponData)
		);
		
		$data = array(
			'images' => '//gscdn.hackers.co.kr/haksa2080/images'
			,'userCouponData' => $userCouponData // total_cnt
			,'userCouponList' => $userCouponList // limit
			,'userEndCouponListCnt' => $userEndCouponListCnt
			,'userEndCouponList' => $userEndCouponList
		);
		
		$this->load->view('common/header', $head);
		$this->load->view('payment/payment_coupon', $data);
		$this->load->view('common/footer');
	}
	
	## ����(����) ����Ʈ (Ajax)
	public function couponGetList()
	{
		if ($this->input->post('page_type') == 'use_coupon_list'){ // ��밡�� ����
			$userCouponData = $this->m_member->getUserCouponList($this->session->userdata('member_no', true), $this->session->userdata('member_id', true), $this->input->post('list_cnt'));
			foreach($userCouponData as $coupon_key => $coupon_item) {
				$userCouponData[$coupon_key]['cupone_name'] = iconv('EUC-KR', 'UTF-8', $coupon_item['cupone_name']);
			}
		} else { // �Ⱓ���� ����
			$userCouponData = $this->m_member->getUserEndCouponList($this->session->userdata('member_no', true), $this->session->userdata('member_id', true), $this->input->post('list_cnt'));
			foreach($userCouponData as $coupon_key => $coupon_item) {
				$userCouponData[$coupon_key]['cupone_name'] = iconv('EUC-KR', 'UTF-8', $coupon_item['cupone_name']);
			}
		}
		
		if ($userCouponData) {
			echo json_encode(array('result' => true, 'msg' => iconv('EUC-KR', 'UTF-8', '����Ʈ �ҷ����� ����!!!'), 'userCouponData' => $userCouponData));
		} else {
			echo json_encode(array('result' => false, 'msg' => iconv('EUC-KR', 'UTF-8', '����Ʈ �ҷ����� ����!!! �����ڿ� �������ּ���.')));
		}
		exit;
	}
	
	## �������
	public function couponInsertCheck()
	{
		$getCouponCheck = $this->m_member->getCouponCheck(trim($this->input->post('coupon_number')));
		if (count($getCouponCheck) <= 0) {
			echo json_encode(array('result' => false, 'msg' => iconv('EUC-KR', 'UTF-8', '�������� �ʴ� ������ȣ�Դϴ�. �ٽ��ѹ� Ȯ�����ּ���.')));
			exit;
		}
		
		$getUserCouponCheck = $this->m_member->getUserCouponCheck($this->session->userdata('member_no'), $this->session->userdata('member_id'), trim($this->input->post('coupon_number')));
		if (count($getUserCouponCheck) > 0) {
			echo json_encode(array('result' => false, 'msg' => iconv('EUC-KR', 'UTF-8', '�̹� ��ϵ� ������ �ֽ��ϴ�. �������������� Ȯ�����ּ���.')));
			exit;
		} else {
			$getUserCouponInsert = $this->m_member->getUserCouponInsert($this->session->userdata('member_no'), $this->session->userdata('member_id'), trim($this->input->post('coupon_number')));
			if ($getUserCouponInsert) {
				echo json_encode(array('result' => true, 'msg' => iconv('EUC-KR', 'UTF-8', '���� ��ϵǾ����ϴ�.')));
				exit;
			}
		}
	}
	
	
}