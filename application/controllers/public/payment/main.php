<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller {
	
	function __construct() {
		parent::__construct();
		$this->load->model("login/m_member");
		$this->load->model("lecture/m_lecture");
		$this->load->model("payment/m_payment");
	}
	
	
	public function index()
	{
		$this->paymentContent();
	}
	
	public function paymentContent()
	{
		## �ʼ��� Ȯ��
		if ($this->session->userdata('member_no') == '' || $this->session->userdata('member_id') == '') {
			echo "<script>alert('�α����� �ʿ��� �������Դϴ�. �α����� �̿����ּ���.'); location.href='/login/main?return_url=' + location.pathname;</script>";
		}
		
		// Header user_info, coupon_cnt
		$use_point = $this->m_member->getUserPoint($this->session->userdata('member_id')); // use_point (��������Ʈ)
		$used_point = $this->m_member->getUserUsedPoint($this->session->userdata('member_id')); // use_point (��������Ʈ)
		$user_point = ((int)$use_point->use_point - (int)$used_point->used_point);
		$userInfo = $this->m_member->getUserInfo($this->session->userdata('member_no', true), $this->session->userdata('member_id', true));
		$userCouponData = $this->m_member->getUserCoupon($this->session->userdata('member_no', true), $this->session->userdata('member_id', true)); // ���� ��������
		
		foreach($userCouponData as $coupon_key => $coupon_item) {
			if (!empty($coupon_item['lec_no'])) {
				$userCouponData[$coupon_key]['checkLecNo'] = explode(',', $coupon_item['lec_no']);
				$checkLecNo = explode(',', $this->input->get('lec_no'));
				
				// ���� lec_no ��밡���� ������ �ҷ�����
				foreach($userCouponData[$coupon_key]['checkLecNo'] as $check_coupon_key => $check_coupon_item) {
					if (in_array($checkLecNo, $check_coupon_item)) {
						$lectureUseCoupon = $this->m_member->getLectureMemCoupon($this->session->userdata('member_no', true), $this->session->userdata('member_id', true), $check_coupon_item);
					} else {
						$lectureUseCoupon = '';
					}
				}
			} else {
				// ���� lec_no �ɷ������������ ������ ����
				$lectureUseCoupon = $this->m_member->getLectureMemCoupon($this->session->userdata('member_no', true), $this->session->userdata('member_id', true), $coupon_item['lec_no']);
			}
		}
		
		$lec_no = explode(',', $this->input->get('lec_no'));
		
		$paymentList = array();
		$lecNo = array();
		foreach($lec_no as $lec_key => $lec_item) {
			if ($lec_item > 50000) { // ����
				$lecNo[] = $lec_item;
				$changeLecNo = ((int)$lec_item - 50000);
				$paymentList[] = $this->m_lecture->getLectureBook($changeLecNo);
			} else { // ����
				$lecNo[] = $lec_item;
				$paymentList[] = $this->m_lecture->getLectureInfo($lec_item);
			}
		}
		
		## ȸ�����Խ� ��ϵ� ���� ��������
		$user_info = $this->m_member->getUserInfo($this->session->userdata('member_no'), $this->session->userdata('member_id'));
		
		## ���� ����Ʈ
		// $user_point = $this->m_member->getUserPoint($this->session->userdata('member_id'));
		
		$head = array(
			'_css' => 'payment'
			,'_js' => 'payment'
			,'user_info' => $userInfo
			,'user_point' => $user_point
			,'user_coupon_cnt' => count($userCouponData)
		);
		
		$data = array(
			'images' => 'https://gscdn.hackers.co.kr/haksa2080/images'
			,'lecNo' => $lecNo
			,'user_info' => $user_info
			,'user_point' => $user_point
			,'paymentList' => $paymentList
			// ,'coupon_list' => $userCouponData
			,'lectureUseCoupon' => $lectureUseCoupon
		);
		
		$this->load->view('common/header', $head);
		$this->load->view('common/allimpopup');
		$this->load->view('payment/payment', $data);
		$this->load->view('common/footer');
	}
	
	## ��ٱ��� insert
	public function basketInert()
	{
		if (!empty($_POST['lec_no'])) {
			$this->m_payment->basketInert($this->input->post('lec_no'), $_SESSION['member_no'], $this->input->post('return_url'));
		}
	}
	
	## ��ٱ��� ����
	public function basketDelete()
	{
		if (!empty($_POST['lec_no']) && !empty($_SESSION['member_no'])) {
			$this->m_payment->basketDelete($this->input->post('lec_no'), $_SESSION['member_no']);
		}
	}
	
	## ��ٱ��� insert (���� �󼼺��� ������)
	public function basketInertArray()
	{
		if (!empty($_POST['lec_no'])) {
			$arrayLecNo = explode(',', $_POST['lec_no']);
			$this->m_payment->basketInertArray($arrayLecNo, $this->session->userdata('member_no'), $this->input->post('return_url'));
		}
	}
	
	## ������Ͻ� ǥ��
	public function authListUpdateCheck()
	{
		$updateCoupon = $this->m_payment->authListUpdateCheck($this->session->userdata('member_no'), $this->session->userdata('member_id'), $this->input->post('coupon_num'));
		if ($updateCoupon) {
			echo json_encode(array('result' => true, 'msg' => iconv('EUC-KR', 'UTF-8', '������ ���')));
		} else {
			echo json_encode(array('result' => false, 'msg' => iconv('EUC-KR', 'UTF-8', '������ ����!! �����ڿ� �������ּ���.')));
		}
		exit;
	}
	
	## lecture_mem ( order_number ) ����
	public function lectureMemOrder()
	{
		$orderNumber = 'M' . mktime() . "a" . $this->session->userdata('member_no', true);
		// $CurrentDateTime = date('Y:m:d-H:i:s', mktime());
		// $CurrentDate = date('Y:m:d', mktime());
		
		$lecNoArray = explode(',', $this->input->post('lec_no', true));
		
		foreach($lecNoArray as $lec_key => $lec_item) {
			if ((int)$lec_item > (int)50000) {
				$book_id = (int)$lec_item - (int)50000;
				$lectureInfo['books'][] = $this->m_lecture->getLectureToBooksInfo($book_id);
			} else {
				$lectureInfo['lecture'][] = $this->m_lecture->getLectureInfo($lec_item);
			}
		}
		
		## �ֹ���ȣ���� lecture_mem Insert
		$this->m_payment->lectureMemInert($orderNumber, $lectureInfo);
		echo json_encode(array('result' => true, 'msg' => iconv('CP949', 'UTF-8', '�ֹ���ȣ ����!!'), 'orderNum' => $orderNumber));
		exit;
	}
	
	## ������ ���� ����
	public function paymentInertCheck()
	{
		## �ʼ��� Ȯ��
		if ($this->session->userdata('member_no') == '' || $this->session->userdata('member_id') == '') {
			echo "<script>alert('�α����� �ʿ��� �������Դϴ�. �α����� �̿����ּ���.'); location.href='/login/main?return_url=' + location.pathname</script>";
		}
		
		## ����, ����Ʈ ��Ͻ� �ֹ���ȣ Update
		foreach($this->input->post('payment_lecture_list', true) as $payment_key => $payment_item) {
			if ($_POST['payment_coupon_number'][$payment_key] != 0 || !empty($_POST['payment_coupon_number'][$payment_key])) {
				$check_coupon = $this->paymentCouponCheck($this->input->post('order_num'), $_POST['payment_coupon_number'][$payment_key], $this->session->userdata('member_id'), $this->session->userdata('member_no'), $payment_item);
			}
			
			if ($_POST['used_point'][$payment_key] != 0 || !empty($_POST['used_point'][$payment_key])) {
				$check_point = $this->paymentPointCheck($this->input->post('order_num'), str_replace(',', '', $_POST['used_point'][$payment_key]), $this->session->userdata('member_id'), $this->session->userdata('member_no'), $payment_item);
			}
		}
		
		
		## ��������
		$memberInfo = $this->m_member->memberInfo($this->session->userdata('mem_no'));
		
		## �ֹ������� ��ȸ Array (�θ���)
		$lectureMemParentInfo = array();
		foreach($this->input->post('payment_lecture_list') as $lecNo_key => $lecNo_item) {
			$lectureMemParentInfo[] = $this->m_payment->getOrderNumData($this->session->userdata('member_no'), $this->session->userdata('member_id'), $this->input->post('order_num', true), $lecNo_item);
		}
		// $device = $this->checkDevice(); // ����̽� üũ
		
		## �ֹ���ȣ ���°� UPDATE
		$testId = explode("|",PAYADMIN);
		$testDomain = array(
			'mq.haksa2080.com'
			,'ml.haksa2080.com'
		);
		if (in_array($this->session->userdata('member_id'), $testId) || in_array($_SERVER['HTTP_HOST'], $testDomain)){
			$champ_person = 'y';
		} else {
			$champ_person = 'n';
		}
		
		$realPrice = array();
		foreach($lectureMemParentInfo as $lecMem_key => $lecMem_item) {
			if ($lecMem_item->discount_div != '') {
				switch($lecMem_item->discount_div) {
					case 'P' :
						$realPrice[] = (int)$lecMem_item->real_price - (int)$lecMem_item->discount_price; // �����ݾ�
						$regi_method = ($lecMem_item->real_price == $lecMem_item->discount_price) ? '8' : $this->input->post('pay_method') ;
						break;
						
					case 'C' :
						if ($lecMem_item->discount_percent != '') {
							$realPrice[] = (int)$lecMem_item->real_price - ((int)$lecMem_item->real_price * ((int)$lecMem_item->discount_percent * 0.01)); // �����ݾ�
							$regi_method = ((int)$lecMem_item->real_price - ((int)$lecMem_item->real_price * ((int)$lecMem_item->discount_percent * 0.01)) == 0) ? '9' : $this->input->post('pay_method') ;
						} else {
							$realPrice[] = (int)$lecMem_item->real_price - (int)$lecMem_item->discount_price; // �����ݾ�
							$regi_method = ((int)$lecMem_item->real_price == (int)$lecMem_item->discount_price) ? '9' : $this->input->post('pay_method') ;
						}
						break;
						
					default :
						$realPrice[] = $lecMem_item->real_price; // �����ݾ�
						$regi_method = ($this->input->post('pay_method') == '1') ? '1' : '2';
						break;
				}
			} else {
				$realPrice[] = $lecMem_item->real_price; // �����ݾ�
				$regi_method = ($this->input->post('pay_method') == '1') ? '1' : '2';
			}
		}
		
		## ��ۺ� Ȯ�� �߰�
		foreach($lectureMemParentInfo as $lecMem_key => $lecMem_item) {
			// ���籸�Ž�
			if ((int)$lecMem_item->lec_no > 50000) {
				if ((int)array_sum($realPrice) < (int)30000) {
					$total_realPrice = ((int)$realPrice[$lecMem_key] + (int)3000);
				} else {
					$total_realPrice = (int)$realPrice[$lecMem_key];
				}
			} else {
				$total_realPrice = (int)$realPrice[$lecMem_key];
			}
			
			## �θ��Ǹ� ������Ʈ ( total_price �������� )
			$this->m_payment->lectureMemRegiMethod($this->session->userdata('member_no'), $this->session->userdata('member_id'), $this->input->post('order_num', true), $regi_method, $champ_person, $lecMem_item->no, $total_realPrice);
		}
		
		
		## �����ݾ��� 0���Ͻ� ������������ �ٷ��̵�
		if ($regi_method == '8' || $regi_method == '9') {
			echo "<script>location.href='/payment/payment_lgu/pay_result?order_num=". $this->input->post('order_num') ."'</script>";
			exit;
		}
		
		## lecture_mem total_price ��ȸ
		$totalPrice = $this->m_payment->lectureMemTotalPrice($this->input->post('order_num', true), $this->session->userdata('member_no'), $this->session->userdata('member_id'));
		
		## LGU session ����
		$payReqMap = $this->m_payment->set_LGU_data($lectureMemParentInfo, $memberInfo, $_POST, $totalPrice);
		
		$data = array(
			'order_num' => $this->input->post('order_num', true)
			,'regi_method' => $this->input->post('pay_method', true)
			,'payReqMap' => $payReqMap
		);
		
		$this->load->view('payment/pay_progress', $data);
	}
	
	
	## ����Ʈ���� �ֹ���ȣ�� ǥ��
	public function paymentPointCheck($order_num, $used_point, $member_id, $member_no, $lec_no)
	{
		$updatePoint = $this->m_payment->paymentPointUpdate($order_num, $used_point, $member_id, $member_no, $lec_no);
		return $updatePoint;
	}
	
	## �������� �ֹ���ȣ�� ǥ��
	public function paymentCouponCheck($order_num, $coupon_num, $member_id, $member_no, $lec_no)
	{
		$updateCoupon = $this->m_payment->paymentCouponUpdate($order_num, $coupon_num, $member_id, $member_no, $lec_no);
		return $updateCoupon;
	}
	
	
}