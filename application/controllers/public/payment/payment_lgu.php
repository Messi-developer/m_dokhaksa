<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Payment_lgu extends CI_Controller {
	
	function __construct() {
		parent::__construct();
		
		$this->load->model("login/m_member");
		$this->load->model("lecture/m_lecture");
		$this->load->model("payment/m_payment");
	}
	
	public function index()
	{
		$this->paymentLGU();
	}
	
	public function paymentLGU()
	{
	}
	
	#########################
	
	/*
     * 결제 중일때 인증요청 응답받는 모델
     */
	function returnurl()
	{
		if (!isset($_SESSION['PAYREQ_MAP'])) {
			die("결제시 오류가 발생하였습니다. 다시로그인후 이용해주세요.");
		}
		$payReqMap = $_SESSION['PAYREQ_MAP']; // 결제 요청시, Session에 저장했던 파라미터 MAP
		
		$LGD_RESPCODE = $_REQUEST['LGD_RESPCODE'];
		$LGD_RESPMSG 	= $_REQUEST['LGD_RESPMSG'];
		$LGD_PAYKEY	  = "";
		
		if ($LGD_RESPCODE == "0000") {
			$LGD_PAYKEY = $_REQUEST['LGD_PAYKEY'];
			$payReqMap['LGD_RESPCODE'] = $LGD_RESPCODE;
			$payReqMap['LGD_RESPMSG'] = $LGD_RESPMSG;
			$payReqMap['LGD_PAYKEY'] = $LGD_PAYKEY;
			
			$data = array(
				'payReqMap' => $payReqMap,
				'LGD_RESPCODE' => $LGD_RESPCODE,
				'LGD_RESPMSG' => $LGD_RESPMSG,
				'LGD_PAYKEY' => $LGD_PAYKEY
			);
			
			$this->load->view('payment/pay_returnurl', $data);
			
		} else {
			// echo "<script>alert('인증 실패 및 결제 취소를 하였습니다. 다시 한번 시도해 주십시오'); location.href='". $_SESSION['PAYREQ_MAP']['returnUrl'] ."'</script>";
		}
	}
	
	/*
     * 최종 결제 요청 & 결제결과 처리하는 모델
     */
	function payres() {
		$result = $this->m_payment->payres_model();
	}
	
	
	/*
	 * 무통장결제일 때 LGU+의 결제통보를 받는 모델
	 */
	function cas_noteurl()
	{
		$LGD_RESPCODE 			= $this->input->post("LGD_RESPCODE", true);
		$LGD_RESPMSG 			= $this->input->post("LGD_RESPMSG", true);
		$LGD_MID 				= $this->input->post("LGD_MID", true);
		$LGD_OID 				= $this->input->post("LGD_OID", true);
		$LGD_AMOUNT 			= $this->input->post("LGD_AMOUNT", true);
		$LGD_TID 				= $this->input->post("LGD_TID", true);
		$LGD_PAYTYPE 			= $this->input->post("LGD_PAYTYPE", true);
		$LGD_PAYDATE 			= $this->input->post("LGD_PAYDATE", true);
		$LGD_HASHDATA 			= $this->input->post("LGD_HASHDATA", true);
		$LGD_FINANCECODE 		= $this->input->post("LGD_FINANCECODE", true);
		$LGD_FINANCENAME 		= $this->input->post("LGD_FINANCENAME", true);
		$LGD_ESCROWYN 			= $this->input->post("LGD_ESCROWYN", true);
		$LGD_TIMESTAMP 			= $this->input->post("LGD_TIMESTAMP", true);
		$LGD_ACCOUNTNUM 		= $this->input->post("LGD_ACCOUNTNUM", true);
		$LGD_CASTAMOUNT 		= $this->input->post("LGD_CASTAMOUNT", true);
		$LGD_CASCAMOUNT 		= $this->input->post("LGD_CASCAMOUNT", true);
		$LGD_CASFLAG 			= $this->input->post("LGD_CASFLAG", true);
		$LGD_CASSEQNO 			= $this->input->post("LGD_CASSEQNO", true);
		$LGD_CASHRECEIPTNUM 	= $this->input->post("LGD_CASHRECEIPTNUM", true);
		$LGD_CASHRECEIPTSELFYN 	= $this->input->post("LGD_CASHRECEIPTSELFYN", true);
		$LGD_CASHRECEIPTKIND 	= $this->input->post("LGD_CASHRECEIPTKIND", true);
		$LGD_PAYER 				= $this->input->post("LGD_PAYER", true);
		
		//$CST_PLATFORM = $this->input->post("CST_PLATFORM", true);
		
		/*
		 * 구매정보
		 */
		$LGD_BUYER 				= $this->input->post("LGD_BUYER", true);
		$LGD_PRODUCTINFO 		= $this->input->post("LGD_PRODUCTINFO", true);
		$LGD_BUYERID 			= $this->input->post("LGD_BUYERID", true);
		$LGD_BUYERADDRESS 		= $this->input->post("LGD_BUYERADDRESS", true);
		$LGD_BUYERPHONE 		= str_replace("-", "", $this->input->post("LGD_BUYERPHONE", true));
		$LGD_BUYEREMAIL 		= $this->input->post("LGD_BUYEREMAIL", true);
		$LGD_BUYERSSN 			= $this->input->post("LGD_BUYERSSN", true);
		$LGD_PRODUCTCODE 		= $this->input->post("LGD_PRODUCTCODE", true);
		$LGD_RECEIVER 			= $this->input->post("LGD_RECEIVER", true);
		$LGD_RECEIVERPHONE 		= $this->input->post("LGD_RECEIVERPHONE", true);
		$LGD_DELIVERYINFO 		= $this->input->post("LGD_DELIVERYINFO", true);
		
		$result = $this->m_payment->cas_noteurl_model($LGD_RESPCODE, $LGD_RESPMSG, $LGD_MID, $LGD_OID, $LGD_AMOUNT, $LGD_TID,
			$LGD_PAYTYPE, $LGD_PAYDATE, $LGD_HASHDATA, $LGD_FINANCECODE, $LGD_FINANCENAME, $LGD_ESCROWYN, $LGD_TIMESTAMP,
			$LGD_ACCOUNTNUM, $LGD_CASTAMOUNT, $LGD_CASCAMOUNT, $LGD_CASFLAG, $LGD_CASSEQNO, $LGD_CASHRECEIPTNUM, $LGD_CASHRECEIPTSELFYN,
			$LGD_CASHRECEIPTKIND, $LGD_PAYER, $LGD_BUYER, $LGD_PRODUCTINFO, $LGD_BUYERID, $LGD_BUYERADDRESS, $LGD_BUYERPHONE,
			$LGD_BUYEREMAIL, $LGD_BUYERSSN, $LGD_PRODUCTCODE, $LGD_RECEIVER, $LGD_RECEIVERPHONE, $LGD_DELIVERYINFO);
	}
	
	/*
     * 제4단계 : 결제완료 페이지 (payment_result)
     */
	function pay_result()
	{
		// Header user_info, coupon_cnt
		$use_point = $this->m_member->getUserPoint($this->session->userdata('member_id')); // use_point (보유포인트)
		$used_point = $this->m_member->getUserUsedPoint($this->session->userdata('member_id')); // use_point (보유포인트)
		$user_point = ((int)$use_point->use_point - (int)$used_point->used_point);
		$userInfo = $this->m_member->getUserInfo($this->session->userdata('member_no', true), $this->session->userdata('member_id', true));
		$userCouponData = $this->m_member->getUserCoupon($this->session->userdata('member_no', true), $this->session->userdata('member_id', true));
		
		## 결과페이지 부모강의 조회
		$lectureParentArray = $this->m_payment->resultParentArray($this->input->get("order_num", true), $this->session->userdata('member_no'), $this->session->userdata('member_id')); // 결제정보 확인
		$discount_point = array();
		$discount_coupon = array();
		foreach($lectureParentArray as $parent_key => $parent_item) {
			switch($parent_item['discount_div']) {
				case 'P' :
					$discount_point[] = $parent_item['discount_price'];
					break;
				case 'C' :
					$discount_coupon[] = ((int)$parent_item['real_price'] - (int)$parent_item['total_price']);
					break;
			}
			
			
			if ((int)$parent_item['lec_no'] > 50000) {
				$lectureParentArray[$parent_key]['book_info'] = $this->m_lecture->getLectureBook(((int)$parent_item['lec_no'] - (int)50000));
			} else {
				$lectureParentArray[$parent_key]['lecture_info'] = $this->m_lecture->getLectureView($parent_item['lec_no']);
			}
			$lectureParentArray[$parent_key]['child'] = $this->m_payment->resultJoinLectureArray($this->input->get("order_num", true), $this->session->userdata('member_no'), $this->session->userdata('member_id'), $parent_item['lec_no']);
		}
		
		foreach($lectureParentArray[$parent_key]['child'] as $child_key => $child_item) {
			if ((int)$parent_item['lec_no'] > 50000) {
				$lectureParentArray[$parent_key]['child']['book_info'] = $this->m_lecture->getLectureBook(((int)$child_item['lec_no'] - (int)50000));
			} else {
				$lectureParentArray[$parent_key]['child']['lecture_info'] = $this->m_lecture->getLectureView($child_item['lec_no']);
			}
		}
		
		## 결제정보 가져오기
		$resultPaymentInfo = $this->m_payment->resultPaymentInfo($this->input->get("order_num", true), $this->session->userdata('member_no'), $this->session->userdata('member_id')); // 결제정보 확인
		
		// 단과강의 패키지 강의분리
		$single_lecture_list = array();
		$package_lecture_list = array();
		foreach($lectureParentArray as $lecture_key => $lecture_item) {
			if (count($lecture_item['child']) >= 1) {
				$package_lecture_list[] = $lecture_item;
			} else {
				$single_lecture_list[] = $lecture_item;
			}
		}
		
		## 주문번호 총상품금액
		$paymentPrice = $this->m_payment->lectureMemRealPrice($this->input->get("order_num", true), $this->session->userdata('member_no'), $this->session->userdata('member_id'));
		
		## 주문번호 최종결제금액
		$paymentResultPrice = $this->m_payment->lectureMemTotalPrice($this->input->get("order_num", true), $this->session->userdata('member_no'), $this->session->userdata('member_id'));
		
		$this->head['pageType'] ='payment';
		$this->head['LGD_OID'] = $this->input->get("order_num", true);
		
		
		$head = array(
			'_css' => 'payment'
			,'_js' => 'payment'
			,'user_info' => $userInfo
			,'user_point' => $user_point
			,'user_coupon_cnt' => count($userCouponData)
		);
		
		$data = array(
			'images' => '//gscdn.hackers.co.kr/haksa2080/images'
			,'package_lecture_list' => $package_lecture_list
			,'single_lecture_list' => $single_lecture_list
			,'resultPaymentInfo' => $resultPaymentInfo
			,'paymentPrice' => $paymentPrice
			,'paymentResultPrice' => $paymentResultPrice
			,'discount_point' => $discount_point
			,'discount_coupon' => $discount_coupon
		);
		
		$this->load->view('common/header', $head);
		$this->load->view('payment/payment_result', $data); // 결제완료 페이지
		$this->load->view('common/footer');
	}
	
	public function note_url()
	{
		/*
		 * 공통결제결과 정보
		 */
		$LGD_RESPCODE = "";           			// 응답코드: 0000(성공) 그외 실패
		$LGD_RESPMSG = "";            			// 응답메세지
		$LGD_MID = "";                			// 상점아이디
		$LGD_OID = "";                			// 주문번호
		$LGD_AMOUNT = "";             			// 거래금액
		$LGD_TID = "";                			// LG유플러스에서 부여한 거래번호
		$LGD_PAYTYPE = "";            			// 결제수단코드
		$LGD_PAYDATE = "";            			// 거래일시(승인일시/이체일시)
		$LGD_HASHDATA = "";           			// 해쉬값
		$LGD_FINANCECODE = "";        			// 결제기관코드(카드종류/은행코드/이통사코드)
		$LGD_FINANCENAME = "";        			// 결제기관이름(카드이름/은행이름/이통사이름)
		$LGD_ESCROWYN = "";           			// 에스크로 적용여부
		$LGD_TIMESTAMP = "";          			// 타임스탬프
		$LGD_FINANCEAUTHNUM = "";     			// 결제기관 승인번호(신용카드, 계좌이체, 상품권)
		
		/*
		 * 신용카드 결제결과 정보
		 */
		$LGD_CARDNUM = "";            			// 카드번호(신용카드)
		$LGD_CARDINSTALLMONTH = "";   			// 할부개월수(신용카드)
		$LGD_CARDNOINTYN = "";        			// 무이자할부여부(신용카드) - '1'이면 무이자할부 '0'이면 일반할부
		$LGD_TRANSAMOUNT = "";        			// 환율적용금액(신용카드)
		$LGD_EXCHANGERATE = "";       			// 환율(신용카드)
		
		/*
		 * 휴대폰
		 */
		$LGD_PAYTELNUM = "";          			// 결제에 이용된전화번호
		
		/*
		 * 계좌이체, 무통장
		 */
		$LGD_ACCOUNTNUM = "";         			// 계좌번호(계좌이체, 무통장입금)
		$LGD_CASTAMOUNT = "";         			// 입금총액(무통장입금)
		$LGD_CASCAMOUNT = "";         			// 현입금액(무통장입금)
		$LGD_CASFLAG = "";            			// 무통장입금 플래그(무통장입금) - 'R':계좌할당, 'I':입금, 'C':입금취소
		$LGD_CASSEQNO = "";           			// 입금순서(무통장입금)
		$LGD_CASHRECEIPTNUM = "";     			// 현금영수증 승인번호
		$LGD_CASHRECEIPTSELFYN = "";  			// 현금영수증자진발급제유무 Y: 자진발급제 적용, 그외 : 미적용
		$LGD_CASHRECEIPTKIND = "";    			// 현금영수증 종류 0: 소득공제용 , 1: 지출증빙용
		
		/*
		 * OK캐쉬백
		 */
		$LGD_OCBSAVEPOINT = "";       			// OK캐쉬백 적립포인트
		$LGD_OCBTOTALPOINT = "";      			// OK캐쉬백 누적포인트
		$LGD_OCBUSABLEPOINT = "";     			// OK캐쉬백 사용가능 포인트
		
		/*
		 * 구매정보
		 */
		$LGD_BUYER = "";              			// 구매자
		$LGD_PRODUCTINFO = "";        			// 상품명
		$LGD_BUYERID = "";            			// 구매자 ID
		$LGD_BUYERADDRESS = "";       			// 구매자 주소
		$LGD_BUYERPHONE = "";         			// 구매자 전화번호
		$LGD_BUYEREMAIL = "";         			// 구매자 이메일
		$LGD_BUYERSSN = "";           			// 구매자 주민번호
		$LGD_PRODUCTCODE = "";        			// 상품코드
		$LGD_RECEIVER = "";           			// 수취인
		$LGD_RECEIVERPHONE = "";      			// 수취인 전화번호
		$LGD_DELIVERYINFO = "";       			// 배송지
		
		
		$LGD_RESPCODE            = $_POST["LGD_RESPCODE"];
		$LGD_RESPMSG             = $_POST["LGD_RESPMSG"];
		$LGD_MID                 = $_POST["LGD_MID"];
		$LGD_OID                 = $_POST["LGD_OID"];
		$LGD_AMOUNT              = $_POST["LGD_AMOUNT"];
		$LGD_TID                 = $_POST["LGD_TID"];
		$LGD_PAYTYPE             = $_POST["LGD_PAYTYPE"];
		$LGD_PAYDATE             = $_POST["LGD_PAYDATE"];
		$LGD_HASHDATA            = $_POST["LGD_HASHDATA"];
		$LGD_FINANCECODE         = $_POST["LGD_FINANCECODE"];
		$LGD_FINANCENAME         = $_POST["LGD_FINANCENAME"];
		$LGD_ESCROWYN            = $_POST["LGD_ESCROWYN"];
		$LGD_TRANSAMOUNT         = $_POST["LGD_TRANSAMOUNT"];
		$LGD_EXCHANGERATE        = $_POST["LGD_EXCHANGERATE"];
		$LGD_CARDNUM             = $_POST["LGD_CARDNUM"];
		$LGD_CARDINSTALLMONTH    = $_POST["LGD_CARDINSTALLMONTH"];
		$LGD_CARDNOINTYN         = $_POST["LGD_CARDNOINTYN"];
		$LGD_TIMESTAMP           = $_POST["LGD_TIMESTAMP"];
		$LGD_FINANCEAUTHNUM      = $_POST["LGD_FINANCEAUTHNUM"];
		$LGD_PAYTELNUM           = $_POST["LGD_PAYTELNUM"];
		$LGD_ACCOUNTNUM          = $_POST["LGD_ACCOUNTNUM"];
		$LGD_CASTAMOUNT          = $_POST["LGD_CASTAMOUNT"];
		$LGD_CASCAMOUNT          = $_POST["LGD_CASCAMOUNT"];
		$LGD_CASFLAG             = $_POST["LGD_CASFLAG"];
		$LGD_CASSEQNO            = $_POST["LGD_CASSEQNO"];
		$LGD_CASHRECEIPTNUM      = $_POST["LGD_CASHRECEIPTNUM"];
		$LGD_CASHRECEIPTSELFYN   = $_POST["LGD_CASHRECEIPTSELFYN"];
		$LGD_CASHRECEIPTKIND     = $_POST["LGD_CASHRECEIPTKIND"];
		$LGD_OCBSAVEPOINT        = $_POST["LGD_OCBSAVEPOINT"];
		$LGD_OCBTOTALPOINT       = $_POST["LGD_OCBTOTALPOINT"];
		$LGD_OCBUSABLEPOINT      = $_POST["LGD_OCBUSABLEPOINT"];
		
		$LGD_BUYER               = $_POST["LGD_BUYER"];
		$LGD_PRODUCTINFO         = $_POST["LGD_PRODUCTINFO"];
		$LGD_BUYERID             = $_POST["LGD_BUYERID"];
		$LGD_BUYERADDRESS        = $_POST["LGD_BUYERADDRESS"];
		$LGD_BUYERPHONE          = $_POST["LGD_BUYERPHONE"];
		$LGD_BUYEREMAIL          = $_POST["LGD_BUYEREMAIL"];
		$LGD_BUYERSSN            = $_POST["LGD_BUYERSSN"];
		$LGD_PRODUCTCODE         = $_POST["LGD_PRODUCTCODE"];
		$LGD_RECEIVER            = $_POST["LGD_RECEIVER"];
		$LGD_RECEIVERPHONE       = $_POST["LGD_RECEIVERPHONE"];
		$LGD_DELIVERYINFO        = $_POST["LGD_DELIVERYINFO"];
		
		$LGD_MERTKEY = "a10bdbec2b1f22eec8dcb63ba7feeb3e";  // LG유플러스에서 발급한 상점키로 변경해 주시기 바랍니다.
		
		$LGD_HASHDATA2 = md5($LGD_MID.$LGD_OID.$LGD_AMOUNT.$LGD_RESPCODE.$LGD_TIMESTAMP.$LGD_MERTKEY);
		
		/*
		 * 상점 처리결과 리턴메세지
		 *
		 * OK   : 상점 처리결과 성공
		 * 그외 : 상점 처리결과 실패
		 *
		 * ※ 주의사항 : 성공시 'OK' 문자이외의 다른문자열이 포함되면 실패처리 되오니 주의하시기 바랍니다.
		 */
		$resultMSG = "결제결과 상점 DB처리(NOTE_URL) 결과값을 입력해 주시기 바랍니다.";
		
		if ($LGD_HASHDATA2 == $LGD_HASHDATA) {      //해쉬값 검증이 성공하면
			if($LGD_RESPCODE == "0000"){            //결제가 성공이면
				/*
				 * 거래성공 결과 상점 처리(DB) 부분
				 * 상점 결과 처리가 정상이면 "OK"
				 */
				//if( 결제성공 상점처리결과 성공 )
				$resultMSG = "OK";
			}else {                                 //결제가 실패이면
				/*
				 * 거래실패 결과 상점 처리(DB) 부분
				 * 상점결과 처리가 정상이면 "OK"
				 */
				//if( 결제실패 상점처리결과 성공 )
				$resultMSG = "OK";
			}
		} else {                                    //해쉬값 검증이 실패이면
			/*
			 * hashdata검증 실패 로그를 처리하시기 바랍니다.
			 */
			$resultMSG = "결제결과 상점 DB처리(NOTE_URL) 해쉬값 검증이 실패하였습니다.";
		}
		
		echo $resultMSG;
	}
	
	
	public function mispwapurl()
	{
		$LGD_OID = $this->input->get("LGD_OID", true);
		
		$result = $this->m_payment->trade_inc();
		
		goto_url('payment/payment_lgu/pay_result?order_num=' . $LGD_OID);
	}
	
	public function cancel_url()
	{
		// 해당 페이지는 사용자가 ISP{국민/BC) 카드 결제를 중단하였을 때, 사용자에게 보여지는 페이지입니다.
		echo "사용자가 ISP(국민/BC) 카드결제을 중단하였습니다.";
	}
	
}