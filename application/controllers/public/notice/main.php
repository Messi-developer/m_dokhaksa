<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller {
	
	function __construct() {
		parent::__construct();
		$this->load->model("login/m_member");
		$this->load->model("notice/m_notice");
		$this->load->model("main/m_contents");
	}
	
	public function index()
	{
		$checkPage = (!empty($_GET['customerType'])) ? $_GET['customerType'] : 'consulting';
		$this->customerCenter($checkPage);
	}
	
	public function customerCenter($getType)
	{
		// Header user_info, coupon_cnt
		$use_point = $this->m_member->getUserPoint($this->session->userdata('member_id')); // use_point (��������Ʈ)
		$used_point = $this->m_member->getUserUsedPoint($this->session->userdata('member_id')); // use_point (��������Ʈ)
		$user_point = ((int)$use_point->use_point - (int)$used_point->used_point);
		$userInfo = $this->m_member->getUserInfo($this->session->userdata('member_no', true), $this->session->userdata('member_id', true));
		$userCouponData = $this->m_member->getUserCoupon($this->session->userdata('member_no', true), $this->session->userdata('member_id', true));
		
		$header = array(
			'_css' => 'customer'
			,'_js' => 'customer'
			,'user_info' => $userInfo
			,'user_point' => $user_point
			,'user_coupon_cnt' => count($userCouponData)
		);
		
		if ($getType == 'consulting') { // 1:1 ���ǻ��
			$data = array(
				'images' => '//gscdn.hackers.co.kr/haksa2080/images'
			);
			
		} else if ($getType == 'faq') { // FAQ
			if ($this->input->get('tab') == 2) {
				$checkType = "ȸ������";
			} else {
				$checkType = "���Ǽ���";
			}
			
			// ������ FAQ ����Ʈ �ҷ�����
			$faqList = $this->m_notice->getFaqListQuery($checkType);
			
			$data = array(
				'images' => '//gscdn.hackers.co.kr/haksa2080/images'
				,'faq_list' => $faqList
			);
			
		} else if ($getType == 'notice') { // ��������
			// ���� �������� (��� 5��)
			$noticeBestList = $this->m_contents->getMainNotice('notice');
			
			// �������� ����Ʈ
			$noticeListItem = ($this->input->post('notice_list_cnt') != '') ? $this->input->post('notice_list_cnt') : '5' ;
			$noticeList = $this->m_contents->getNoticeList($noticeListItem);
			$noticeListCnt = $this->m_contents->getNoticeListCnt();
			
			$data = array(
				'images' => '//gscdn.hackers.co.kr/haksa2080/images'
				,'noticeBestList' => $noticeBestList
				,'noticeList' => $noticeList
				,'noticeListCnt' => $noticeListCnt
			);
			
		} else if ($getType == 'support_write') { // FAQ
			$data = array(
				'images' => '//gscdn.hackers.co.kr/haksa2080/images'
			);
		}
		
		$this->load->view('common/header', $header);
		$this->load->view('notice/' . $getType , $data);
		$this->load->view('common/footer');
	}
	
	## 1:1 ������
	public function consultingInsert()
	{
		$_POST['ask'] = implode(',', $this->input->post('ask'));
		$_POST['course'] = implode(',', $this->input->post('course'));
		$_POST['major'] = implode(',', $this->input->post('major'));
		$_POST['phone'] = substr($this->input->post('phone'), '0', '3') . '-' . substr($this->input->post('phone'), '3', '4') . '-' . substr($this->input->post('phone'), '7', '4');
		
		$checkAgree = ($_POST['agree_all'] == 'on') ? 'y' : '' ;
		$nowDate = date('Y-m-d H:i:s');
		
		$consultingCheck = $this->m_notice->consultingCheck($_POST);
		if (count($consultingCheck) > 0) {
			if ($consultingCheck['diff'] < 30) {
				echo "<script>alert('�̹� ��ϵ� �����ֽ��ϴ�. Ȯ���� �ٽ� �õ����ּ���.'); location.href='/notice/main?customerType=consulting';</script>";
				exit;
			}
		}
		
		$insertResult = $this->m_notice->consultingInsertQuery($_POST, $checkAgree, $nowDate);
		if ($insertResult > 0) {
			echo "<script>alert('1:1 ���ǻ�� ��û�Ǿ����ϴ�.'); location.href='/notice/main?customerType=consulting';</script>";
		} else {
			echo "<script>alert('1:1 ���ǻ�� ��� ����!! �����ڿ� �������ּ���.');</script>";
		}
	}
	
	## FAQ ����Ʈ
	public function getFaqList()
	{
		$category = iconv( 'UTF-8', 'CP949', $_POST['category']);
		$faqList = $this->m_notice->getFaqListQuery($category);
		
		foreach($faqList as $faq_key => $faq_item) {
			$faqList[$faq_key]['question'] = iconv('EUC-KR', 'UTF-8', $faq_item['question']);
			$faqList[$faq_key]['answer'] = iconv('EUC-KR', 'UTF-8', $faq_item['answer']);
		}
		
		if (count($faqList) > 0) {
			echo json_encode(array('result' => true, 'msg' => iconv('CP949', 'UTF-8', 'FAQ ����Ʈ ȣ�� ����!!'), 'faq_list' => $faqList));
		} else {
			echo json_encode(array('result' => false, 'msg' => iconv('CP949', 'UTF-8', 'FAQ ����Ʈ ȣ�� ����!!')));
		}
		
		exit;
	}
	
	## �������� (Ajax)
	public function getNoticeList()
	{
		$noticeListItem = ($this->input->post('notice_list') != '') ? $this->input->post('notice_list') : '5' ;
		$noticeList = $this->m_contents->getNoticeList($noticeListItem);
		
		foreach($noticeList as $notice_key => $notice_item) {
			$noticeList[$notice_key]['subject'] = iconv('CP949', 'UTF-8', $notice_item['subject']);
		}
		
		if (count($noticeList) > 0) {
			echo json_encode(array('result' => true, 'msg' => iconv('CP949', 'UTF-8', '�������� ����Ʈ �ҷ����� ����!!'), 'notice_list' => $noticeList));
		} else {
			echo json_encode(array('result' => false, 'msg' => iconv('CP949', 'UTF-8', '�������� ����Ʈ �ҷ����� ����!! �����ڿ� �������ּ���.')));
		}
		exit;
	}
	
	
	## ��Ŀ���� �ٶ���
	public function hackersHelp()
	{
		$helpIp = $this->m_notice->hackersHelpIp($this->input->post('session_ip'));
		if (count($helpIp) > 0) {
			echo "<script>alert('��� �� �ٽ� �õ��� �ּ���.'); location.href='/notice/main?customerType=support_write';</script>";
			exit;
		}
		
		// �ع� ����
		$helpSend = array(
			"TYPE" => "27" // ����Ʈ�� �ڵ�(���)  *�ʼ��Է»���( ��ܿ� �� ����Ʈ�� �´� �ڵ� �Է� )
			// 1:Ī���մϴ� 2:������� 3:������ 4:��Ÿ
			, "REQUEST_TYPE" => $this->input->post('help_type') //��û�з���  *�ʼ��Է»��� , ## ���� ��� 1 �Է� , ���� ��� �ش� ����Ʈ�� �´� ��û �з��� �Է�
			, "DOC_TYPE" => "1"    //��������( 1: ��Ŀ���� �ٶ��� , 2: 1:1���� , 3: ������׽Ű� )
			, "USERNIC" => ($this->input->post('name') != '') ? $this->input->post('name') : '' //�Է¹����� �г����� ���� ��� �ش�ĭ�� �Է�, ������ ��ĭ �Է�
			, "USERID" => ($this->session->userdata('member_id') != '') ? $this->session->userdata('member_id') : '' // ���̵� �Է¹����� �Է�
			, "USER_NM" => ($this->input->post('name') != '') ? $this->input->post('name') : '' //�̸� �Է� ������ �Է�
			, "USER_TEL" => preg_replace('/[^0-9]/', '', implode($_POST['phone'])) //����ó ��) 01090578386 ��� Ư�� ��ȣ ����
			, "USER_EMAIL" => $this->input->post('email') . '@' . $this->input->post('join_email') //�̸��� ��) abcd@naver.com
			, "TITLE" => $this->input->post('subject') //��û���� �ۼ��� ����
			, "CONTENT" => $this->input->post('contents') //��û���� �ۼ��� ����
			, "IP_ADDR" => $_SERVER['REMOTE_ADDR'] //����� ip
			, "AGREE_CHK" => "Y" //����� �������� ���� -> Y, N
			, "WRITE_TOOL" => "smarteditor" // ����ڰ� �ۼ��� ����� ������ ) ��) textarea,daumeditor
			, "REGDATE" => date("YmdHis") //��û�� ��Ͻð�
			, "IS_MOBILE" => "N" //����ڰ� �ۼ��� ȯ�� ( ����� : Y ,  PC : N )
			, "USER_AGENT" => $_SERVER['HTTP_USER_AGENT']//������� USER_AGENT
			, "ATT_FILE" => ""  // ��)http://img.hackers.com/ab/a/jpg
		);
		
		$toData = array(
			'url' => 'http://222.122.234.15/admin/external_site/siteToHackersManagerAll.php',
			'data' => array(
				'tohackers' => array($helpSend),
				'act' => 'indata',
			),
		);
		
		$options = array(
			'http' => array(
				'header' => "Content-type: application/x-www-form-urlencoded\r\n",
				'method' => 'POST',
				'Content-length: '.strlen(http_build_query($toData['data'])).'\r\n',
				'content' => http_build_query($toData['data']),
			),
		);
		
		$context = stream_context_create($options);
		$result = file_get_contents($toData['url'], false, $context);
		$json = json_decode($result, true);
		
		// �ع� insert
		$userInfo = array(
			'member_id' => $this->session->userdata('member_id')
			,'ip_addr' => $_SERVER['REMOTE_ADDR']
		);
		
		$hackersInsert = $this->m_notice->hackersHelpInsert($_POST, $userInfo);
		if ($hackersInsert) {
			echo "<script>alert('��Ŀ���� �ٶ��� ��ϵǾ����ϴ�.'); location.href='/notice/main?customerType=support_write';</script>";
		} else {
			echo "<script>alert('��Ͻ���!! �����ڿ� �������ּ���.');</script>";
		}
		exit;
	}
	
}