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
     * ���� ���϶� ������û ����޴� ��
     */
	function returnurl()
	{
		if (!isset($_SESSION['PAYREQ_MAP'])) {
			die("������ ������ �߻��Ͽ����ϴ�. �ٽ÷α����� �̿����ּ���.");
		}
		$payReqMap = $_SESSION['PAYREQ_MAP']; // ���� ��û��, Session�� �����ߴ� �Ķ���� MAP
		
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
			// echo "<script>alert('���� ���� �� ���� ��Ҹ� �Ͽ����ϴ�. �ٽ� �ѹ� �õ��� �ֽʽÿ�'); location.href='". $_SESSION['PAYREQ_MAP']['returnUrl'] ."'</script>";
		}
	}
	
	/*
     * ���� ���� ��û & ������� ó���ϴ� ��
     */
	function payres() {
		$result = $this->m_payment->payres_model();
	}
	
	
	/*
	 * ����������� �� LGU+�� �����뺸�� �޴� ��
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
		 * ��������
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
     * ��4�ܰ� : �����Ϸ� ������ (payment_result)
     */
	function pay_result()
	{
		// Header user_info, coupon_cnt
		$use_point = $this->m_member->getUserPoint($this->session->userdata('member_id')); // use_point (��������Ʈ)
		$used_point = $this->m_member->getUserUsedPoint($this->session->userdata('member_id')); // use_point (��������Ʈ)
		$user_point = ((int)$use_point->use_point - (int)$used_point->used_point);
		$userInfo = $this->m_member->getUserInfo($this->session->userdata('member_no', true), $this->session->userdata('member_id', true));
		$userCouponData = $this->m_member->getUserCoupon($this->session->userdata('member_no', true), $this->session->userdata('member_id', true));
		
		## ��������� �θ��� ��ȸ
		$lectureParentArray = $this->m_payment->resultParentArray($this->input->get("order_num", true), $this->session->userdata('member_no'), $this->session->userdata('member_id')); // �������� Ȯ��
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
		
		## �������� ��������
		$resultPaymentInfo = $this->m_payment->resultPaymentInfo($this->input->get("order_num", true), $this->session->userdata('member_no'), $this->session->userdata('member_id')); // �������� Ȯ��
		
		// �ܰ����� ��Ű�� ���Ǻи�
		$single_lecture_list = array();
		$package_lecture_list = array();
		foreach($lectureParentArray as $lecture_key => $lecture_item) {
			if (count($lecture_item['child']) >= 1) {
				$package_lecture_list[] = $lecture_item;
			} else {
				$single_lecture_list[] = $lecture_item;
			}
		}
		
		## �ֹ���ȣ �ѻ�ǰ�ݾ�
		$paymentPrice = $this->m_payment->lectureMemRealPrice($this->input->get("order_num", true), $this->session->userdata('member_no'), $this->session->userdata('member_id'));
		
		## �ֹ���ȣ ���������ݾ�
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
		$this->load->view('payment/payment_result', $data); // �����Ϸ� ������
		$this->load->view('common/footer');
	}
	
	public function note_url()
	{
		/*
		 * ���������� ����
		 */
		$LGD_RESPCODE = "";           			// �����ڵ�: 0000(����) �׿� ����
		$LGD_RESPMSG = "";            			// ����޼���
		$LGD_MID = "";                			// �������̵�
		$LGD_OID = "";                			// �ֹ���ȣ
		$LGD_AMOUNT = "";             			// �ŷ��ݾ�
		$LGD_TID = "";                			// LG���÷������� �ο��� �ŷ���ȣ
		$LGD_PAYTYPE = "";            			// ���������ڵ�
		$LGD_PAYDATE = "";            			// �ŷ��Ͻ�(�����Ͻ�/��ü�Ͻ�)
		$LGD_HASHDATA = "";           			// �ؽ���
		$LGD_FINANCECODE = "";        			// ��������ڵ�(ī������/�����ڵ�/������ڵ�)
		$LGD_FINANCENAME = "";        			// ��������̸�(ī���̸�/�����̸�/������̸�)
		$LGD_ESCROWYN = "";           			// ����ũ�� ���뿩��
		$LGD_TIMESTAMP = "";          			// Ÿ�ӽ�����
		$LGD_FINANCEAUTHNUM = "";     			// ������� ���ι�ȣ(�ſ�ī��, ������ü, ��ǰ��)
		
		/*
		 * �ſ�ī�� ������� ����
		 */
		$LGD_CARDNUM = "";            			// ī���ȣ(�ſ�ī��)
		$LGD_CARDINSTALLMONTH = "";   			// �Һΰ�����(�ſ�ī��)
		$LGD_CARDNOINTYN = "";        			// �������Һο���(�ſ�ī��) - '1'�̸� �������Һ� '0'�̸� �Ϲ��Һ�
		$LGD_TRANSAMOUNT = "";        			// ȯ������ݾ�(�ſ�ī��)
		$LGD_EXCHANGERATE = "";       			// ȯ��(�ſ�ī��)
		
		/*
		 * �޴���
		 */
		$LGD_PAYTELNUM = "";          			// ������ �̿����ȭ��ȣ
		
		/*
		 * ������ü, ������
		 */
		$LGD_ACCOUNTNUM = "";         			// ���¹�ȣ(������ü, �������Ա�)
		$LGD_CASTAMOUNT = "";         			// �Ա��Ѿ�(�������Ա�)
		$LGD_CASCAMOUNT = "";         			// ���Աݾ�(�������Ա�)
		$LGD_CASFLAG = "";            			// �������Ա� �÷���(�������Ա�) - 'R':�����Ҵ�, 'I':�Ա�, 'C':�Ա����
		$LGD_CASSEQNO = "";           			// �Աݼ���(�������Ա�)
		$LGD_CASHRECEIPTNUM = "";     			// ���ݿ����� ���ι�ȣ
		$LGD_CASHRECEIPTSELFYN = "";  			// ���ݿ����������߱������� Y: �����߱��� ����, �׿� : ������
		$LGD_CASHRECEIPTKIND = "";    			// ���ݿ����� ���� 0: �ҵ������ , 1: ����������
		
		/*
		 * OKĳ����
		 */
		$LGD_OCBSAVEPOINT = "";       			// OKĳ���� ��������Ʈ
		$LGD_OCBTOTALPOINT = "";      			// OKĳ���� ��������Ʈ
		$LGD_OCBUSABLEPOINT = "";     			// OKĳ���� ��밡�� ����Ʈ
		
		/*
		 * ��������
		 */
		$LGD_BUYER = "";              			// ������
		$LGD_PRODUCTINFO = "";        			// ��ǰ��
		$LGD_BUYERID = "";            			// ������ ID
		$LGD_BUYERADDRESS = "";       			// ������ �ּ�
		$LGD_BUYERPHONE = "";         			// ������ ��ȭ��ȣ
		$LGD_BUYEREMAIL = "";         			// ������ �̸���
		$LGD_BUYERSSN = "";           			// ������ �ֹι�ȣ
		$LGD_PRODUCTCODE = "";        			// ��ǰ�ڵ�
		$LGD_RECEIVER = "";           			// ������
		$LGD_RECEIVERPHONE = "";      			// ������ ��ȭ��ȣ
		$LGD_DELIVERYINFO = "";       			// �����
		
		
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
		
		$LGD_MERTKEY = "a10bdbec2b1f22eec8dcb63ba7feeb3e";  // LG���÷������� �߱��� ����Ű�� ������ �ֽñ� �ٶ��ϴ�.
		
		$LGD_HASHDATA2 = md5($LGD_MID.$LGD_OID.$LGD_AMOUNT.$LGD_RESPCODE.$LGD_TIMESTAMP.$LGD_MERTKEY);
		
		/*
		 * ���� ó����� ���ϸ޼���
		 *
		 * OK   : ���� ó����� ����
		 * �׿� : ���� ó����� ����
		 *
		 * �� ���ǻ��� : ������ 'OK' �����̿��� �ٸ����ڿ��� ���ԵǸ� ����ó�� �ǿ��� �����Ͻñ� �ٶ��ϴ�.
		 */
		$resultMSG = "������� ���� DBó��(NOTE_URL) ������� �Է��� �ֽñ� �ٶ��ϴ�.";
		
		if ($LGD_HASHDATA2 == $LGD_HASHDATA) {      //�ؽ��� ������ �����ϸ�
			if($LGD_RESPCODE == "0000"){            //������ �����̸�
				/*
				 * �ŷ����� ��� ���� ó��(DB) �κ�
				 * ���� ��� ó���� �����̸� "OK"
				 */
				//if( �������� ����ó����� ���� )
				$resultMSG = "OK";
			}else {                                 //������ �����̸�
				/*
				 * �ŷ����� ��� ���� ó��(DB) �κ�
				 * ������� ó���� �����̸� "OK"
				 */
				//if( �������� ����ó����� ���� )
				$resultMSG = "OK";
			}
		} else {                                    //�ؽ��� ������ �����̸�
			/*
			 * hashdata���� ���� �α׸� ó���Ͻñ� �ٶ��ϴ�.
			 */
			$resultMSG = "������� ���� DBó��(NOTE_URL) �ؽ��� ������ �����Ͽ����ϴ�.";
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
		// �ش� �������� ����ڰ� ISP{����/BC) ī�� ������ �ߴ��Ͽ��� ��, ����ڿ��� �������� �������Դϴ�.
		echo "����ڰ� ISP(����/BC) ī������� �ߴ��Ͽ����ϴ�.";
	}
	
}