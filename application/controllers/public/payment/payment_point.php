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
	
	## ����/��۳��� view page
	public function PaymentPoint()
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
		
		
		$use_point_list = $this->m_member->getUserPointList($this->session->userdata('member_id')); // use_point (��������Ʈ) list
		$used_point_list = $this->m_member->getUserPointUsedList($this->session->userdata('member_id')); // use_point (��������Ʈ) list
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
	
	## ����Ʈ(����) ����Ʈ (Ajax)
	public function pointGetList()
	{
		if ($this->input->post('page_type') == 'use_point_list') {
			$use_point_list = $this->m_member->getUserPointList($this->session->userdata('member_id'), $this->input->post('list_cnt')); // use_point (��������Ʈ) list
			foreach($use_point_list as $point_key => $point_item) {
				$use_point_list[$point_key]['point_title'] = iconv('EUC-KR', 'UTF-8', $point_item['point_title']);
			}
		} else {
			$use_point_list = $this->m_member->getUserPointUsedList($this->session->userdata('member_id'), $this->input->post('list_cnt')); // use_point (��������Ʈ) list
			foreach($use_point_list as $point_key => $point_item) {
				$use_point_list[$point_key]['point_title'] = iconv('EUC-KR', 'UTF-8', $point_item['point_title']);
			}
		}
		if ($use_point_list) {
			echo json_encode(array('result' => true, 'msg' => iconv('EUC-KR', 'UTF-8', '����Ʈ �ҷ����� ����!!!'), 'use_point_list' => $use_point_list));
		} else {
			echo json_encode(array('result' => false, 'msg' => iconv('EUC-KR', 'UTF-8', '����Ʈ �ҷ����� ����!!! �����ڿ� �������ּ���.')));
		}
		exit;
	}
	
}