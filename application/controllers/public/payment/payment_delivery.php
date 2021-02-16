<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Payment_delivery extends CI_Controller {
	
	function __construct() {
		parent::__construct();
		$this->load->model("login/m_member");
		$this->load->model("lecture/m_lecture");
		$this->load->model("payment/m_payment");
	}
	
	public function index()
	{
		$this->PaymentDelivery();
	}
	
	## ����/��۳��� view page
	public function PaymentDelivery()
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
		
		
		// �������Ա� ����
		$passBookList = $this->m_payment->getUserDeliveryPassBook($this->session->userdata('member_no'), $this->session->userdata('member_id'));
		$passBookListCnt = $this->m_payment->getUserDeliveryPassBookCnt($this->session->userdata('member_no'), $this->session->userdata('member_id'));
		
		foreach($passBookList as $passBook_key => $passBook_item) {
			// �Ѱ����ݾ�
			$passBookList[$passBook_key]['payment_total_price'] = $this->m_payment->getUserDeliveryPassBookTotalPrice($this->session->userdata('member_no'), $this->session->userdata('member_id'), $passBook_item['order_num']);
			
			// �Աݱ���
			$passBookList[$passBook_key]['wdate'] = str_replace(':', '-', $passBook_item['wdate']);
			$passBookList[$passBook_key]['paymentEndDate'] = date('Y-m-d', strtotime("+1 week", strtotime($passBookList[$passBook_key]['wdate'])));
		}
		
		// �����Ϸ�,��ҳ���
		$lectureMemList = $this->m_payment->getUserLectureMemList($this->session->userdata('member_no'), $this->session->userdata('member_id'));
		$lectureMemListCnt = $this->m_payment->getUserLectureMemListCnt($this->session->userdata('member_no'), $this->session->userdata('member_id'));
		foreach($lectureMemList as $lecMem_key => $lecMem_item) {
			// �Ѱ����ݾ�
			$lectureMemList[$lecMem_key]['payment_total_price'] = $this->m_payment->getUserDeliveryPassBookTotalPrice($this->session->userdata('member_no'), $this->session->userdata('member_id'), $lecMem_item['order_num']);
			
			## ���Ǳ��Ž� ���籸�� �̷��ִ��� Ȯ��
			if ((int)$lecMem_item['lec_no'] > (int)50000) {
				$lectureMemList[$lecMem_key]['book_info'] = $this->m_lecture->getLectureBook(((int)$lecMem_item['lec_no'] - (int)50000));
				$lectureMemList[$lecMem_key]['besong_info'] = $this->m_payment->getBesongInfoList($lecMem_item['order_num'], ((int)$lecMem_item['lec_no'] - (int)50000));
			} else {
				$lectureMemList[$lecMem_key]['book_info'] = '';
				$lectureMemList[$lecMem_key]['besong_info'] = '';
			}
			
			// �����
			$lectureMemList[$lecMem_key]['wdate'] = str_replace(':', '-', $lecMem_item['wdate']);
			
			switch ($lecMem_item['lec_state']) {
				case '1' :
					$lectureMemList[$lecMem_key]['lec_state_text'] = "�������";
					$lectureMemList[$lecMem_key]['lec_state_style'] = "t2";
					break;
				case '2' :
					$lectureMemList[$lecMem_key]['lec_state_text'] = "������";
					$lectureMemList[$lecMem_key]['lec_state_style'] = "t1";
					break;
				case '3' :
					$lectureMemList[$lecMem_key]['lec_state_text'] = "�����Ϸ�";
					$lectureMemList[$lecMem_key]['lec_state_style'] = "t1";
					break;
				case '4' :
					$lectureMemList[$lecMem_key]['lec_state_text'] = "�Ͻ�����";
					$lectureMemList[$lecMem_key]['lec_state_style'] = "t2";
					break;
				case '5' :
					$lectureMemList[$lecMem_key]['lec_state_text'] = "����ȯ��";
					break;
				case '6' :
					$lectureMemList[$lecMem_key]['lec_state_text'] = "�κ�ȯ��";
					break;
				case '9' :
					$lectureMemList[$lecMem_key]['lec_state_text'] = "��������";
					break;
			}
			
			switch ($lecMem_item['regi_method']) {
				case '0' :
					$lectureMemList[$lecMem_key]['payment_way'] = "�Աݴ��";
					$lectureMemList[$lecMem_key]['payment_state'] = "�������";
					break;
				case '1' :
					$lectureMemList[$lecMem_key]['payment_way'] = "�ſ�ī��";
					$lectureMemList[$lecMem_key]['payment_state'] = "�����Ϸ�";
					break;
				case '2' :
					$lectureMemList[$lecMem_key]['payment_way'] = "�������Ա�";
					$lectureMemList[$lecMem_key]['payment_state'] = "�����Ϸ�";
					break;
				case '3' :
					$lectureMemList[$lecMem_key]['payment_way'] = "������ü";
					$lectureMemList[$lecMem_key]['payment_state'] = "�����Ϸ�";
					break;
				case '5' :
					$lectureMemList[$lecMem_key]['payment_way'] = "�޴�������";
					$lectureMemList[$lecMem_key]['payment_state'] = "�����Ϸ�";
					break;
				default :
					$lectureMemList[$lecMem_key]['payment_way'] = "����X";
					$lectureMemList[$lecMem_key]['payment_state'] = "�������";
					break;
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
			,'passBookList' => $passBookList
			,'passBookListCnt' => count($passBookListCnt)
			,'lectureMemList' => $lectureMemList
			,'lectureMemListCnt' => count($lectureMemListCnt)
		);
		
		$this->load->view('common/header', $head);
		// $this->load->view('common/allimpopup');
		$this->load->view('payment/payment_delivery', $data);
		$this->load->view('common/footer');
	}
	
	## �������Ա� ��� list(Ajax)
	public function passBookGetPage()
	{
		$passBookGetPage = $this->m_payment->getUserDeliveryPassBook($this->session->userdata('member_no'), $this->session->userdata('member_id'), $this->input->post('list_cnt'));
		if (!empty($passBookGetPage)) {
			foreach($passBookGetPage as $passBook_key => $passBook_item) {
				// �Ѱ����ݾ�
				$passBookGetPage[$passBook_key]['payment_total_price'] = $this->m_payment->getUserDeliveryPassBookTotalPrice($this->session->userdata('member_no'), $this->session->userdata('member_id'), $passBook_item['order_num']);
				
				// �Աݱ���
				$passBookGetPage[$passBook_key]['wdate'] = str_replace(':', '-', $passBook_item['wdate']);
				$passBookGetPage[$passBook_key]['paymentEndDate'] = date('Y-m-d', strtotime("+1 week", strtotime($passBookGetPage[$passBook_key]['wdate'])));
				
				$passBookGetPage[$passBook_key]['bank_name'] = iconv('EUC-KR', 'UTF-8', $passBook_item['bank_name']);
				$passBookGetPage[$passBook_key]['mem_name'] = iconv('EUC-KR', 'UTF-8', $passBook_item['mem_name']);
				$passBookGetPage[$passBook_key]['mem_lec_name'] = iconv('EUC-KR', 'UTF-8', $passBook_item['mem_lec_name']);
			}
			echo json_encode(array('result' => true, 'msg' => iconv('EUC-KR', 'UTF-8', '����Ʈ �ҷ����� ����!!'), 'passBookGetPage' => $passBookGetPage));
		} else {
			echo json_encode(array('result' => false, 'msg' => iconv('EUC-KR', 'UTF-8', '����Ʈ �ҷ����� ����!! �����ڿ� �������ּ���.')));
		}
		exit;
	}
	
	## �����Ϸ�/��� list(Ajax)
	public function lectureMemGetPage()
	{
		$lectureMemList = $this->m_payment->getUserLectureMemList($this->session->userdata('member_no'), $this->session->userdata('member_id'), $this->input->post('list_cnt'));
		if ($lectureMemList) {
			foreach($lectureMemList as $lecMem_key => $lecMem_item) {
				// �Ѱ����ݾ�
				$lectureMemList[$lecMem_key]['payment_total_price'] = $this->m_payment->getUserDeliveryPassBookTotalPrice($this->session->userdata('member_no'), $this->session->userdata('member_id'), $lecMem_item['order_num']);
				
				// �����
				$lectureMemList[$lecMem_key]['wdate'] = str_replace(':', '-', $lecMem_item['wdate']);
				
				switch ($lecMem_item['lec_state']) {
					case '1' :
						$lectureMemList[$lecMem_key]['lec_state_text'] = "�������";
						$lectureMemList[$lecMem_key]['lec_state_style'] = "t2";
						break;
					case '2' :
						$lectureMemList[$lecMem_key]['lec_state_text'] = "������";
						$lectureMemList[$lecMem_key]['lec_state_style'] = "t1";
						break;
					case '3' :
						$lectureMemList[$lecMem_key]['lec_state_text'] = "�����Ϸ�";
						$lectureMemList[$lecMem_key]['lec_state_style'] = "t1";
						break;
					case '4' :
						$lectureMemList[$lecMem_key]['lec_state_text'] = "�Ͻ�����";
						$lectureMemList[$lecMem_key]['lec_state_style'] = "t2";
						break;
					case '5' :
						$lectureMemList[$lecMem_key]['lec_state_text'] = "����ȯ��";
						break;
					case '6' :
						$lectureMemList[$lecMem_key]['lec_state_text'] = "�κ�ȯ��";
						break;
					case '9' :
						$lectureMemList[$lecMem_key]['lec_state_text'] = "��������";
						break;
				}
				
				switch ($lecMem_item['regi_method']) {
					case '0' :
						$lectureMemList[$lecMem_key]['payment_way'] = "�Աݴ��";
						$lectureMemList[$lecMem_key]['payment_state'] = "�������";
						$lectureMemList[$lecMem_key]['lec_state_style'] = "t2";
						break;
					case '1' :
						$lectureMemList[$lecMem_key]['payment_way'] = "�ſ�ī��";
						$lectureMemList[$lecMem_key]['payment_state'] = "�����Ϸ�";
						$lectureMemList[$lecMem_key]['lec_state_style'] = "t1";
						break;
					case '2' :
						$lectureMemList[$lecMem_key]['payment_way'] = "�������Ա�";
						$lectureMemList[$lecMem_key]['payment_state'] = "�����Ϸ�";
						$lectureMemList[$lecMem_key]['lec_state_style'] = "t1";
						break;
					case '3' :
						$lectureMemList[$lecMem_key]['payment_way'] = "������ü";
						$lectureMemList[$lecMem_key]['payment_state'] = "�����Ϸ�";
						$lectureMemList[$lecMem_key]['lec_state_style'] = "t1";
						break;
					case '5' :
						$lectureMemList[$lecMem_key]['payment_way'] = "�޴�������";
						$lectureMemList[$lecMem_key]['payment_state'] = "�����Ϸ�";
						$lectureMemList[$lecMem_key]['lec_state_style'] = "t1";
						break;
					default :
						$lectureMemList[$lecMem_key]['payment_way'] = "����X";
						$lectureMemList[$lecMem_key]['payment_state'] = "�������";
						$lectureMemList[$lecMem_key]['lec_state_style'] = "t2";
						break;
				}
				
				$lectureMemList[$lecMem_key]['mem_lec_name'] = iconv('EUC-KR', 'UTF-8', $lecMem_item['mem_lec_name']);
				$lectureMemList[$lecMem_key]['lec_state_text'] = iconv('EUC-KR', 'UTF-8', $lectureMemList[$lecMem_key]['lec_state_text']);
				$lectureMemList[$lecMem_key]['payment_way'] = iconv('EUC-KR', 'UTF-8', $lectureMemList[$lecMem_key]['payment_way']);
				$lectureMemList[$lecMem_key]['payment_state'] = iconv('EUC-KR', 'UTF-8', $lectureMemList[$lecMem_key]['payment_state']);
				
				## ���Ǳ��Ž� ���籸�� �̷��ִ��� Ȯ��
				if ((int)$lecMem_item['lec_no'] > 50000) {
					$lectureMemList[$lecMem_key]['book_info'] = $this->m_lecture->getLectureBook(((int)$lecMem_item['lec_no'] - (int)50000));
					$lectureMemList[$lecMem_key]['book_info']->book_name = iconv('EUC-KR', 'UTF-8', $lectureMemList[$lecMem_key]['book_info']->book_name);
					$lectureMemList[$lecMem_key]['besong_info'] = $this->m_payment->getBesongInfoList($lecMem_item['order_num'], ((int)$lecMem_item['lec_no'] - 50000));
					$lectureMemList[$lecMem_key]['besong_info']->home_address = iconv('EUC-KR', 'UTF-8', $lectureMemList[$lecMem_key]['besong_info']->home_address);
					$lectureMemList[$lecMem_key]['besong_info']->tail_address = iconv('EUC-KR', 'UTF-8', $lectureMemList[$lecMem_key]['besong_info']->tail_address);
				} else {
					$lectureMemList[$lecMem_key]['book_info'] = '';
					$lectureMemList[$lecMem_key]['besong_info'] = '';
				}
			}
			
			echo json_encode(array('result' => true, 'msg' => iconv('EUC-KR', 'UTF-8', '����Ʈ �ҷ����� ����!!'), 'lectureMemList' => $lectureMemList));
		} else {
			echo json_encode(array('result' => false, 'msg' => iconv('EUC-KR', 'UTF-8', '����Ʈ �ҷ����� ����!! �����ڿ� �������ּ���.')));
		}
		exit;
	}
	
	## �ֹ���ȣ ����
	public function lectureMemDelete()
	{
		$deleteRs = $this->m_payment->lectureMemDelete($this->session->userdata('member_no'), $this->session->userdata('member_id'), $this->input->post('order_num', true));
		if ($deleteRs) {
			echo json_encode(array('result' => true, 'msg' => iconv('EUC-KR', 'UTF-8', '�ֹ������� �����Ǿ����ϴ�.')));
		} else {
			echo json_encode(array('result' => false, 'msg' => iconv('EUC-KR', 'UTF-8', '�ֹ����� ��������!! �����ڿ� �������ּ���.')));
		}
		exit;
	}
}