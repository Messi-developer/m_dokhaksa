<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Payment_basket extends CI_Controller {
	
	function __construct() {
		parent::__construct();
		$this->load->model("login/m_member");
		$this->load->model("lecture/m_lecture");
		$this->load->model("payment/m_payment");
	}
	
	public function index()
	{
		$this->PaymentBasket();
	}
	
	public function PaymentBasket()
	{
		## �ʼ��� Ȯ��
		if ($this->session->userdata('member_no') == '' || $this->session->userdata('member_id') == '') {
			echo json_encode(array('result' => false, 'msg' => iconv('CP949', 'UTF-8', '�α����� �ʿ��� �������Դϴ�. �α����� �̿����ּ���.'), 'return_url' => '/login/main?return_url=' . $_SERVER['REQUEST_URI']));
			exit;
		}
		
		// Header user_info, coupon_cnt
		$use_point = $this->m_member->getUserPoint($this->session->userdata('member_id')); // use_point (��������Ʈ)
		$used_point = $this->m_member->getUserUsedPoint($this->session->userdata('member_id')); // use_point (��������Ʈ)
		$user_point = ((int)$use_point->use_point - (int)$used_point->used_point);
		$userInfo = $this->m_member->getUserInfo($this->session->userdata('member_no', true), $this->session->userdata('member_id', true));
		$userCouponData = $this->m_member->getUserCoupon($this->session->userdata('member_no', true), $this->session->userdata('member_id', true));
		
		## ��ٱ��� ����Ʈ
		$getBasketList = $this->m_payment->getUserBasketCode($_SESSION['member_no']);
		foreach($getBasketList as $basket_key => $basket_item) {
			if ($basket_item['lec_no'] > 50000) {
				$basketCheckList['lec_no'][] = $basket_item['lec_no'];
				$basketList[] = $this->m_payment->getUserBasketBookList((int)$basket_item['lec_no'] - 50000); // ����
			} else {
				$basketCheckList['lec_no'][] = $basket_item['lec_no'];
				$basketList[] = $this->m_payment->getUserBasketLectureList($basket_item['lec_no']); // ����
			}
		}
		
		$head = array(
			'_css' => 'payment'
			,'_js' => 'payment'
			,'user_info' => $userInfo
			,'user_point' => $user_point
			,'user_coupon_cnt' => count($userCouponData)
		);
		
		$data = array(
			'images' => '//gscdn.hackers.co.kr/haksa2080/images'
			,'basketList' => $basketList
			,'basketCheckList' => $basketCheckList
		);
		
		$this->load->view('common/header', $head);
		$this->load->view('payment/payment_basket', $data);
        $this->load->view('common/allimpopup');
		$this->load->view('common/footer');
	}
	
}