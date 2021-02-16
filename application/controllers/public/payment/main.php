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
		## 필수값 확인
		if ($this->session->userdata('member_no') == '' || $this->session->userdata('member_id') == '') {
			echo "<script>alert('로그인이 필요한 페이지입니다. 로그인후 이용해주세요.'); location.href='/login/main?return_url=' + location.pathname;</script>";
		}
		
		// Header user_info, coupon_cnt
		$use_point = $this->m_member->getUserPoint($this->session->userdata('member_id')); // use_point (보유포인트)
		$used_point = $this->m_member->getUserUsedPoint($this->session->userdata('member_id')); // use_point (보유포인트)
		$user_point = ((int)$use_point->use_point - (int)$used_point->used_point);
		$userInfo = $this->m_member->getUserInfo($this->session->userdata('member_no', true), $this->session->userdata('member_id', true));
		$userCouponData = $this->m_member->getUserCoupon($this->session->userdata('member_no', true), $this->session->userdata('member_id', true)); // 유저 보유쿠폰
		
		foreach($userCouponData as $coupon_key => $coupon_item) {
			if (!empty($coupon_item['lec_no'])) {
				$userCouponData[$coupon_key]['checkLecNo'] = explode(',', $coupon_item['lec_no']);
				$checkLecNo = explode(',', $this->input->get('lec_no'));
				
				// 쿠폰 lec_no 사용가능한 쿠폰만 불러오기
				foreach($userCouponData[$coupon_key]['checkLecNo'] as $check_coupon_key => $check_coupon_item) {
					if (in_array($checkLecNo, $check_coupon_item)) {
						$lectureUseCoupon = $this->m_member->getLectureMemCoupon($this->session->userdata('member_no', true), $this->session->userdata('member_id', true), $check_coupon_item);
					} else {
						$lectureUseCoupon = '';
					}
				}
			} else {
				// 쿠폰 lec_no 걸려있지않을경우 무조건 노출
				$lectureUseCoupon = $this->m_member->getLectureMemCoupon($this->session->userdata('member_no', true), $this->session->userdata('member_id', true), $coupon_item['lec_no']);
			}
		}
		
		$lec_no = explode(',', $this->input->get('lec_no'));
		
		$paymentList = array();
		$lecNo = array();
		foreach($lec_no as $lec_key => $lec_item) {
			if ($lec_item > 50000) { // 교재
				$lecNo[] = $lec_item;
				$changeLecNo = ((int)$lec_item - 50000);
				$paymentList[] = $this->m_lecture->getLectureBook($changeLecNo);
			} else { // 강의
				$lecNo[] = $lec_item;
				$paymentList[] = $this->m_lecture->getLectureInfo($lec_item);
			}
		}
		
		## 회원가입시 등록된 정보 가져오기
		$user_info = $this->m_member->getUserInfo($this->session->userdata('member_no'), $this->session->userdata('member_id'));
		
		## 유저 포인트
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
	
	## 장바구니 insert
	public function basketInert()
	{
		if (!empty($_POST['lec_no'])) {
			$this->m_payment->basketInert($this->input->post('lec_no'), $_SESSION['member_no'], $this->input->post('return_url'));
		}
	}
	
	## 장바구니 삭제
	public function basketDelete()
	{
		if (!empty($_POST['lec_no']) && !empty($_SESSION['member_no'])) {
			$this->m_payment->basketDelete($this->input->post('lec_no'), $_SESSION['member_no']);
		}
	}
	
	## 장바구니 insert (강의 상세보기 페이지)
	public function basketInertArray()
	{
		if (!empty($_POST['lec_no'])) {
			$arrayLecNo = explode(',', $_POST['lec_no']);
			$this->m_payment->basketInertArray($arrayLecNo, $this->session->userdata('member_no'), $this->input->post('return_url'));
		}
	}
	
	## 쿠폰등록시 표시
	public function authListUpdateCheck()
	{
		$updateCoupon = $this->m_payment->authListUpdateCheck($this->session->userdata('member_no'), $this->session->userdata('member_id'), $this->input->post('coupon_num'));
		if ($updateCoupon) {
			echo json_encode(array('result' => true, 'msg' => iconv('EUC-KR', 'UTF-8', '결제시 사용')));
		} else {
			echo json_encode(array('result' => false, 'msg' => iconv('EUC-KR', 'UTF-8', '결제시 실패!! 관리자에 문의해주세요.')));
		}
		exit;
	}
	
	## lecture_mem ( order_number ) 생성
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
		
		## 주문번호생성 lecture_mem Insert
		$this->m_payment->lectureMemInert($orderNumber, $lectureInfo);
		echo json_encode(array('result' => true, 'msg' => iconv('CP949', 'UTF-8', '주문번호 생성!!'), 'orderNum' => $orderNumber));
		exit;
	}
	
	## 구매자 정보 적용
	public function paymentInertCheck()
	{
		## 필수값 확인
		if ($this->session->userdata('member_no') == '' || $this->session->userdata('member_id') == '') {
			echo "<script>alert('로그인이 필요한 페이지입니다. 로그인후 이용해주세요.'); location.href='/login/main?return_url=' + location.pathname</script>";
		}
		
		## 쿠폰, 포인트 등록시 주문번호 Update
		foreach($this->input->post('payment_lecture_list', true) as $payment_key => $payment_item) {
			if ($_POST['payment_coupon_number'][$payment_key] != 0 || !empty($_POST['payment_coupon_number'][$payment_key])) {
				$check_coupon = $this->paymentCouponCheck($this->input->post('order_num'), $_POST['payment_coupon_number'][$payment_key], $this->session->userdata('member_id'), $this->session->userdata('member_no'), $payment_item);
			}
			
			if ($_POST['used_point'][$payment_key] != 0 || !empty($_POST['used_point'][$payment_key])) {
				$check_point = $this->paymentPointCheck($this->input->post('order_num'), str_replace(',', '', $_POST['used_point'][$payment_key]), $this->session->userdata('member_id'), $this->session->userdata('member_no'), $payment_item);
			}
		}
		
		
		## 유저정보
		$memberInfo = $this->m_member->memberInfo($this->session->userdata('mem_no'));
		
		## 주문데이터 조회 Array (부모강의)
		$lectureMemParentInfo = array();
		foreach($this->input->post('payment_lecture_list') as $lecNo_key => $lecNo_item) {
			$lectureMemParentInfo[] = $this->m_payment->getOrderNumData($this->session->userdata('member_no'), $this->session->userdata('member_id'), $this->input->post('order_num', true), $lecNo_item);
		}
		// $device = $this->checkDevice(); // 디바이스 체크
		
		## 주문번호 상태값 UPDATE
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
						$realPrice[] = (int)$lecMem_item->real_price - (int)$lecMem_item->discount_price; // 결제금액
						$regi_method = ($lecMem_item->real_price == $lecMem_item->discount_price) ? '8' : $this->input->post('pay_method') ;
						break;
						
					case 'C' :
						if ($lecMem_item->discount_percent != '') {
							$realPrice[] = (int)$lecMem_item->real_price - ((int)$lecMem_item->real_price * ((int)$lecMem_item->discount_percent * 0.01)); // 결제금액
							$regi_method = ((int)$lecMem_item->real_price - ((int)$lecMem_item->real_price * ((int)$lecMem_item->discount_percent * 0.01)) == 0) ? '9' : $this->input->post('pay_method') ;
						} else {
							$realPrice[] = (int)$lecMem_item->real_price - (int)$lecMem_item->discount_price; // 결제금액
							$regi_method = ((int)$lecMem_item->real_price == (int)$lecMem_item->discount_price) ? '9' : $this->input->post('pay_method') ;
						}
						break;
						
					default :
						$realPrice[] = $lecMem_item->real_price; // 결제금액
						$regi_method = ($this->input->post('pay_method') == '1') ? '1' : '2';
						break;
				}
			} else {
				$realPrice[] = $lecMem_item->real_price; // 결제금액
				$regi_method = ($this->input->post('pay_method') == '1') ? '1' : '2';
			}
		}
		
		## 배송비 확인 추가
		foreach($lectureMemParentInfo as $lecMem_key => $lecMem_item) {
			// 교재구매시
			if ((int)$lecMem_item->lec_no > 50000) {
				if ((int)array_sum($realPrice) < (int)30000) {
					$total_realPrice = ((int)$realPrice[$lecMem_key] + (int)3000);
				} else {
					$total_realPrice = (int)$realPrice[$lecMem_key];
				}
			} else {
				$total_realPrice = (int)$realPrice[$lecMem_key];
			}
			
			## 부모강의만 업데이트 ( total_price 각각적용 )
			$this->m_payment->lectureMemRegiMethod($this->session->userdata('member_no'), $this->session->userdata('member_id'), $this->input->post('order_num', true), $regi_method, $champ_person, $lecMem_item->no, $total_realPrice);
		}
		
		
		## 결제금액이 0원일시 결제페이지로 바로이동
		if ($regi_method == '8' || $regi_method == '9') {
			echo "<script>location.href='/payment/payment_lgu/pay_result?order_num=". $this->input->post('order_num') ."'</script>";
			exit;
		}
		
		## lecture_mem total_price 조회
		$totalPrice = $this->m_payment->lectureMemTotalPrice($this->input->post('order_num', true), $this->session->userdata('member_no'), $this->session->userdata('member_id'));
		
		## LGU session 생성
		$payReqMap = $this->m_payment->set_LGU_data($lectureMemParentInfo, $memberInfo, $_POST, $totalPrice);
		
		$data = array(
			'order_num' => $this->input->post('order_num', true)
			,'regi_method' => $this->input->post('pay_method', true)
			,'payReqMap' => $payReqMap
		);
		
		$this->load->view('payment/pay_progress', $data);
	}
	
	
	## 포인트사용시 주문번호에 표시
	public function paymentPointCheck($order_num, $used_point, $member_id, $member_no, $lec_no)
	{
		$updatePoint = $this->m_payment->paymentPointUpdate($order_num, $used_point, $member_id, $member_no, $lec_no);
		return $updatePoint;
	}
	
	## 쿠폰사용시 주문번호에 표시
	public function paymentCouponCheck($order_num, $coupon_num, $member_id, $member_no, $lec_no)
	{
		$updateCoupon = $this->m_payment->paymentCouponUpdate($order_num, $coupon_num, $member_id, $member_no, $lec_no);
		return $updateCoupon;
	}
	
	
}