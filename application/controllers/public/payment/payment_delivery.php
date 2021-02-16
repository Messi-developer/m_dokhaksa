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
	
	## 결제/배송내역 view page
	public function PaymentDelivery()
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
		
		
		// 무통장입금 내역
		$passBookList = $this->m_payment->getUserDeliveryPassBook($this->session->userdata('member_no'), $this->session->userdata('member_id'));
		$passBookListCnt = $this->m_payment->getUserDeliveryPassBookCnt($this->session->userdata('member_no'), $this->session->userdata('member_id'));
		
		foreach($passBookList as $passBook_key => $passBook_item) {
			// 총결제금액
			$passBookList[$passBook_key]['payment_total_price'] = $this->m_payment->getUserDeliveryPassBookTotalPrice($this->session->userdata('member_no'), $this->session->userdata('member_id'), $passBook_item['order_num']);
			
			// 입금기한
			$passBookList[$passBook_key]['wdate'] = str_replace(':', '-', $passBook_item['wdate']);
			$passBookList[$passBook_key]['paymentEndDate'] = date('Y-m-d', strtotime("+1 week", strtotime($passBookList[$passBook_key]['wdate'])));
		}
		
		// 결제완료,취소내역
		$lectureMemList = $this->m_payment->getUserLectureMemList($this->session->userdata('member_no'), $this->session->userdata('member_id'));
		$lectureMemListCnt = $this->m_payment->getUserLectureMemListCnt($this->session->userdata('member_no'), $this->session->userdata('member_id'));
		foreach($lectureMemList as $lecMem_key => $lecMem_item) {
			// 총결제금액
			$lectureMemList[$lecMem_key]['payment_total_price'] = $this->m_payment->getUserDeliveryPassBookTotalPrice($this->session->userdata('member_no'), $this->session->userdata('member_id'), $lecMem_item['order_num']);
			
			## 강의구매시 교재구매 이력있는지 확인
			if ((int)$lecMem_item['lec_no'] > (int)50000) {
				$lectureMemList[$lecMem_key]['book_info'] = $this->m_lecture->getLectureBook(((int)$lecMem_item['lec_no'] - (int)50000));
				$lectureMemList[$lecMem_key]['besong_info'] = $this->m_payment->getBesongInfoList($lecMem_item['order_num'], ((int)$lecMem_item['lec_no'] - (int)50000));
			} else {
				$lectureMemList[$lecMem_key]['book_info'] = '';
				$lectureMemList[$lecMem_key]['besong_info'] = '';
			}
			
			// 등록일
			$lectureMemList[$lecMem_key]['wdate'] = str_replace(':', '-', $lecMem_item['wdate']);
			
			switch ($lecMem_item['lec_state']) {
				case '1' :
					$lectureMemList[$lecMem_key]['lec_state_text'] = "수강대기";
					$lectureMemList[$lecMem_key]['lec_state_style'] = "t2";
					break;
				case '2' :
					$lectureMemList[$lecMem_key]['lec_state_text'] = "수강중";
					$lectureMemList[$lecMem_key]['lec_state_style'] = "t1";
					break;
				case '3' :
					$lectureMemList[$lecMem_key]['lec_state_text'] = "수강완료";
					$lectureMemList[$lecMem_key]['lec_state_style'] = "t1";
					break;
				case '4' :
					$lectureMemList[$lecMem_key]['lec_state_text'] = "일시정지";
					$lectureMemList[$lecMem_key]['lec_state_style'] = "t2";
					break;
				case '5' :
					$lectureMemList[$lecMem_key]['lec_state_text'] = "수강환불";
					break;
				case '6' :
					$lectureMemList[$lecMem_key]['lec_state_text'] = "부분환불";
					break;
				case '9' :
					$lectureMemList[$lecMem_key]['lec_state_text'] = "강의정지";
					break;
			}
			
			switch ($lecMem_item['regi_method']) {
				case '0' :
					$lectureMemList[$lecMem_key]['payment_way'] = "입금대기";
					$lectureMemList[$lecMem_key]['payment_state'] = "결제대기";
					break;
				case '1' :
					$lectureMemList[$lecMem_key]['payment_way'] = "신용카드";
					$lectureMemList[$lecMem_key]['payment_state'] = "결제완료";
					break;
				case '2' :
					$lectureMemList[$lecMem_key]['payment_way'] = "무통장입금";
					$lectureMemList[$lecMem_key]['payment_state'] = "결제완료";
					break;
				case '3' :
					$lectureMemList[$lecMem_key]['payment_way'] = "계좌이체";
					$lectureMemList[$lecMem_key]['payment_state'] = "결제완료";
					break;
				case '5' :
					$lectureMemList[$lecMem_key]['payment_way'] = "휴대폰결제";
					$lectureMemList[$lecMem_key]['payment_state'] = "결제완료";
					break;
				default :
					$lectureMemList[$lecMem_key]['payment_way'] = "결제X";
					$lectureMemList[$lecMem_key]['payment_state'] = "결제취소";
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
	
	## 무통장입금 대기 list(Ajax)
	public function passBookGetPage()
	{
		$passBookGetPage = $this->m_payment->getUserDeliveryPassBook($this->session->userdata('member_no'), $this->session->userdata('member_id'), $this->input->post('list_cnt'));
		if (!empty($passBookGetPage)) {
			foreach($passBookGetPage as $passBook_key => $passBook_item) {
				// 총결제금액
				$passBookGetPage[$passBook_key]['payment_total_price'] = $this->m_payment->getUserDeliveryPassBookTotalPrice($this->session->userdata('member_no'), $this->session->userdata('member_id'), $passBook_item['order_num']);
				
				// 입금기한
				$passBookGetPage[$passBook_key]['wdate'] = str_replace(':', '-', $passBook_item['wdate']);
				$passBookGetPage[$passBook_key]['paymentEndDate'] = date('Y-m-d', strtotime("+1 week", strtotime($passBookGetPage[$passBook_key]['wdate'])));
				
				$passBookGetPage[$passBook_key]['bank_name'] = iconv('EUC-KR', 'UTF-8', $passBook_item['bank_name']);
				$passBookGetPage[$passBook_key]['mem_name'] = iconv('EUC-KR', 'UTF-8', $passBook_item['mem_name']);
				$passBookGetPage[$passBook_key]['mem_lec_name'] = iconv('EUC-KR', 'UTF-8', $passBook_item['mem_lec_name']);
			}
			echo json_encode(array('result' => true, 'msg' => iconv('EUC-KR', 'UTF-8', '리스트 불러오기 성공!!'), 'passBookGetPage' => $passBookGetPage));
		} else {
			echo json_encode(array('result' => false, 'msg' => iconv('EUC-KR', 'UTF-8', '리스트 불러오기 실패!! 관리자에 문의해주세요.')));
		}
		exit;
	}
	
	## 결제완료/취소 list(Ajax)
	public function lectureMemGetPage()
	{
		$lectureMemList = $this->m_payment->getUserLectureMemList($this->session->userdata('member_no'), $this->session->userdata('member_id'), $this->input->post('list_cnt'));
		if ($lectureMemList) {
			foreach($lectureMemList as $lecMem_key => $lecMem_item) {
				// 총결제금액
				$lectureMemList[$lecMem_key]['payment_total_price'] = $this->m_payment->getUserDeliveryPassBookTotalPrice($this->session->userdata('member_no'), $this->session->userdata('member_id'), $lecMem_item['order_num']);
				
				// 등록일
				$lectureMemList[$lecMem_key]['wdate'] = str_replace(':', '-', $lecMem_item['wdate']);
				
				switch ($lecMem_item['lec_state']) {
					case '1' :
						$lectureMemList[$lecMem_key]['lec_state_text'] = "수강대기";
						$lectureMemList[$lecMem_key]['lec_state_style'] = "t2";
						break;
					case '2' :
						$lectureMemList[$lecMem_key]['lec_state_text'] = "수강중";
						$lectureMemList[$lecMem_key]['lec_state_style'] = "t1";
						break;
					case '3' :
						$lectureMemList[$lecMem_key]['lec_state_text'] = "수강완료";
						$lectureMemList[$lecMem_key]['lec_state_style'] = "t1";
						break;
					case '4' :
						$lectureMemList[$lecMem_key]['lec_state_text'] = "일시정지";
						$lectureMemList[$lecMem_key]['lec_state_style'] = "t2";
						break;
					case '5' :
						$lectureMemList[$lecMem_key]['lec_state_text'] = "수강환불";
						break;
					case '6' :
						$lectureMemList[$lecMem_key]['lec_state_text'] = "부분환불";
						break;
					case '9' :
						$lectureMemList[$lecMem_key]['lec_state_text'] = "강의정지";
						break;
				}
				
				switch ($lecMem_item['regi_method']) {
					case '0' :
						$lectureMemList[$lecMem_key]['payment_way'] = "입금대기";
						$lectureMemList[$lecMem_key]['payment_state'] = "결제대기";
						$lectureMemList[$lecMem_key]['lec_state_style'] = "t2";
						break;
					case '1' :
						$lectureMemList[$lecMem_key]['payment_way'] = "신용카드";
						$lectureMemList[$lecMem_key]['payment_state'] = "결제완료";
						$lectureMemList[$lecMem_key]['lec_state_style'] = "t1";
						break;
					case '2' :
						$lectureMemList[$lecMem_key]['payment_way'] = "무통장입금";
						$lectureMemList[$lecMem_key]['payment_state'] = "결제완료";
						$lectureMemList[$lecMem_key]['lec_state_style'] = "t1";
						break;
					case '3' :
						$lectureMemList[$lecMem_key]['payment_way'] = "계좌이체";
						$lectureMemList[$lecMem_key]['payment_state'] = "결제완료";
						$lectureMemList[$lecMem_key]['lec_state_style'] = "t1";
						break;
					case '5' :
						$lectureMemList[$lecMem_key]['payment_way'] = "휴대폰결제";
						$lectureMemList[$lecMem_key]['payment_state'] = "결제완료";
						$lectureMemList[$lecMem_key]['lec_state_style'] = "t1";
						break;
					default :
						$lectureMemList[$lecMem_key]['payment_way'] = "결제X";
						$lectureMemList[$lecMem_key]['payment_state'] = "결제취소";
						$lectureMemList[$lecMem_key]['lec_state_style'] = "t2";
						break;
				}
				
				$lectureMemList[$lecMem_key]['mem_lec_name'] = iconv('EUC-KR', 'UTF-8', $lecMem_item['mem_lec_name']);
				$lectureMemList[$lecMem_key]['lec_state_text'] = iconv('EUC-KR', 'UTF-8', $lectureMemList[$lecMem_key]['lec_state_text']);
				$lectureMemList[$lecMem_key]['payment_way'] = iconv('EUC-KR', 'UTF-8', $lectureMemList[$lecMem_key]['payment_way']);
				$lectureMemList[$lecMem_key]['payment_state'] = iconv('EUC-KR', 'UTF-8', $lectureMemList[$lecMem_key]['payment_state']);
				
				## 강의구매시 교재구매 이력있는지 확인
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
			
			echo json_encode(array('result' => true, 'msg' => iconv('EUC-KR', 'UTF-8', '리스트 불러오기 성공!!'), 'lectureMemList' => $lectureMemList));
		} else {
			echo json_encode(array('result' => false, 'msg' => iconv('EUC-KR', 'UTF-8', '리스트 불러오기 실패!! 관리자에 문의해주세요.')));
		}
		exit;
	}
	
	## 주문번호 삭제
	public function lectureMemDelete()
	{
		$deleteRs = $this->m_payment->lectureMemDelete($this->session->userdata('member_no'), $this->session->userdata('member_id'), $this->input->post('order_num', true));
		if ($deleteRs) {
			echo json_encode(array('result' => true, 'msg' => iconv('EUC-KR', 'UTF-8', '주문내역이 삭제되었습니다.')));
		} else {
			echo json_encode(array('result' => false, 'msg' => iconv('EUC-KR', 'UTF-8', '주문내역 삭제실패!! 관리자에 문의해주세요.')));
		}
		exit;
	}
}