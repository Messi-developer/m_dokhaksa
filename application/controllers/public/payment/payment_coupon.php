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
	
	## 결제/배송내역 view page
	public function PaymentCoupon()
	{
		## 필수값 확인
		if ($this->session->userdata('member_no') == '' || $this->session->userdata('member_id') == '') {
			echo "<script>alert('로그인이 필요한 페이지입니다. 로그인후 이용해주세요.'); location.href='/login/main?return_url=' + location.pathname</script>";
		}
		
		// Header user_info, coupon_cnt
		$use_point = $this->m_member->getUserPoint($this->session->userdata('member_id')); // use_point (보유포인트)
		$used_point = $this->m_member->getUserUsedPoint($this->session->userdata('member_id')); // use_point (보유포인트)
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
	
	## 쿠폰(보유) 리스트 (Ajax)
	public function couponGetList()
	{
		if ($this->input->post('page_type') == 'use_coupon_list'){ // 사용가능 쿠폰
			$userCouponData = $this->m_member->getUserCouponList($this->session->userdata('member_no', true), $this->session->userdata('member_id', true), $this->input->post('list_cnt'));
			foreach($userCouponData as $coupon_key => $coupon_item) {
				$userCouponData[$coupon_key]['cupone_name'] = iconv('EUC-KR', 'UTF-8', $coupon_item['cupone_name']);
			}
		} else { // 기간지난 쿠폰
			$userCouponData = $this->m_member->getUserEndCouponList($this->session->userdata('member_no', true), $this->session->userdata('member_id', true), $this->input->post('list_cnt'));
			foreach($userCouponData as $coupon_key => $coupon_item) {
				$userCouponData[$coupon_key]['cupone_name'] = iconv('EUC-KR', 'UTF-8', $coupon_item['cupone_name']);
			}
		}
		
		if ($userCouponData) {
			echo json_encode(array('result' => true, 'msg' => iconv('EUC-KR', 'UTF-8', '리스트 불러오기 성공!!!'), 'userCouponData' => $userCouponData));
		} else {
			echo json_encode(array('result' => false, 'msg' => iconv('EUC-KR', 'UTF-8', '리스트 불러오기 실패!!! 관리자에 문의해주세요.')));
		}
		exit;
	}
	
	## 쿠폰등록
	public function couponInsertCheck()
	{
		$getCouponCheck = $this->m_member->getCouponCheck(trim($this->input->post('coupon_number')));
		if (count($getCouponCheck) <= 0) {
			echo json_encode(array('result' => false, 'msg' => iconv('EUC-KR', 'UTF-8', '존재하지 않는 쿠폰번호입니다. 다시한번 확인해주세요.')));
			exit;
		}
		
		$getUserCouponCheck = $this->m_member->getUserCouponCheck($this->session->userdata('member_no'), $this->session->userdata('member_id'), trim($this->input->post('coupon_number')));
		if (count($getUserCouponCheck) > 0) {
			echo json_encode(array('result' => false, 'msg' => iconv('EUC-KR', 'UTF-8', '이미 등록된 쿠폰이 있습니다. 마이페이지에서 확인해주세요.')));
			exit;
		} else {
			$getUserCouponInsert = $this->m_member->getUserCouponInsert($this->session->userdata('member_no'), $this->session->userdata('member_id'), trim($this->input->post('coupon_number')));
			if ($getUserCouponInsert) {
				echo json_encode(array('result' => true, 'msg' => iconv('EUC-KR', 'UTF-8', '쿠폰 등록되었습니다.')));
				exit;
			}
		}
	}
	
	
}