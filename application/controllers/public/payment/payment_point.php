<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Payment_point extends CI_Controller {
	
	function __construct() {
		parent::__construct();
		$this->load->model("login/m_member");
		$this->load->model("lecture/m_lecture");
		$this->load->model("payment/m_payment");
	}
	
	public function index()
	{
		$this->PaymentPoint();
	}
	
	## 결제/배송내역 view page
	public function PaymentPoint()
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
		
		
		$use_point_list = $this->m_member->getUserPointList($this->session->userdata('member_id')); // use_point (보유포인트) list
		$used_point_list = $this->m_member->getUserPointUsedList($this->session->userdata('member_id')); // use_point (보유포인트) list
		$use_point_list_cnt = $this->m_member->getUserPointListAll($this->session->userdata('member_id'));
		$used_point_list_cnt = $this->m_member->getUserPointUsedListAll($this->session->userdata('member_id'));
		
		$head = array(
			'_css' => 'payment'
			,'_js' => 'payment'
			,'user_info' => $userInfo
			,'user_point' => $user_point
			,'user_coupon_cnt' => count($userCouponData)
		);
		
		$data = array(
			'images' => '//gscdn.hackers.co.kr/haksa2080/images'
			,'user_point' => $user_point
			,'use_point_list' => $use_point_list
			,'used_point_list' => $used_point_list
			,'use_point_list_cnt' => $use_point_list_cnt
			,'used_point_list_cnt' => $used_point_list_cnt
		);
		
		$this->load->view('common/header', $head);
		$this->load->view('payment/payment_point', $data);
		$this->load->view('common/footer');
	}
	
	## 포인트(보유) 리스트 (Ajax)
	public function pointGetList()
	{
		if ($this->input->post('page_type') == 'use_point_list') {
			$use_point_list = $this->m_member->getUserPointList($this->session->userdata('member_id'), $this->input->post('list_cnt')); // use_point (보유포인트) list
			foreach($use_point_list as $point_key => $point_item) {
				$use_point_list[$point_key]['point_title'] = iconv('EUC-KR', 'UTF-8', $point_item['point_title']);
			}
		} else {
			$use_point_list = $this->m_member->getUserPointUsedList($this->session->userdata('member_id'), $this->input->post('list_cnt')); // use_point (보유포인트) list
			foreach($use_point_list as $point_key => $point_item) {
				$use_point_list[$point_key]['point_title'] = iconv('EUC-KR', 'UTF-8', $point_item['point_title']);
			}
		}
		if ($use_point_list) {
			echo json_encode(array('result' => true, 'msg' => iconv('EUC-KR', 'UTF-8', '리스트 불러오기 성공!!!'), 'use_point_list' => $use_point_list));
		} else {
			echo json_encode(array('result' => false, 'msg' => iconv('EUC-KR', 'UTF-8', '리스트 불러오기 실패!!! 관리자에 문의해주세요.')));
		}
		exit;
	}
	
}