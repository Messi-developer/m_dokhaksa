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
		$use_point = $this->m_member->getUserPoint($this->session->userdata('member_id')); // use_point (보유포인트)
		$used_point = $this->m_member->getUserUsedPoint($this->session->userdata('member_id')); // use_point (보유포인트)
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
		
		if ($getType == 'consulting') { // 1:1 문의상담
			$data = array(
				'images' => '//gscdn.hackers.co.kr/haksa2080/images'
			);
			
		} else if ($getType == 'faq') { // FAQ
			if ($this->input->get('tab') == 2) {
				$checkType = "회원정보";
			} else {
				$checkType = "강의수강";
			}
			
			// 고객센터 FAQ 리스트 불러오기
			$faqList = $this->m_notice->getFaqListQuery($checkType);
			
			$data = array(
				'images' => '//gscdn.hackers.co.kr/haksa2080/images'
				,'faq_list' => $faqList
			);
			
		} else if ($getType == 'notice') { // 공지사항
			// 메인 공지사항 (상단 5개)
			$noticeBestList = $this->m_contents->getMainNotice('notice');
			
			// 공지사항 리스트
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
	
	## 1:1 무료상담
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
				echo "<script>alert('이미 등록된 글이있습니다. 확인후 다시 시도해주세요.'); location.href='/notice/main?customerType=consulting';</script>";
				exit;
			}
		}
		
		$insertResult = $this->m_notice->consultingInsertQuery($_POST, $checkAgree, $nowDate);
		if ($insertResult > 0) {
			echo "<script>alert('1:1 문의상담 신청되었습니다.'); location.href='/notice/main?customerType=consulting';</script>";
		} else {
			echo "<script>alert('1:1 문의상담 등록 실패!! 관리자에 문의해주세요.');</script>";
		}
	}
	
	## FAQ 리스트
	public function getFaqList()
	{
		$category = iconv( 'UTF-8', 'CP949', $_POST['category']);
		$faqList = $this->m_notice->getFaqListQuery($category);
		
		foreach($faqList as $faq_key => $faq_item) {
			$faqList[$faq_key]['question'] = iconv('EUC-KR', 'UTF-8', $faq_item['question']);
			$faqList[$faq_key]['answer'] = iconv('EUC-KR', 'UTF-8', $faq_item['answer']);
		}
		
		if (count($faqList) > 0) {
			echo json_encode(array('result' => true, 'msg' => iconv('CP949', 'UTF-8', 'FAQ 리스트 호출 성공!!'), 'faq_list' => $faqList));
		} else {
			echo json_encode(array('result' => false, 'msg' => iconv('CP949', 'UTF-8', 'FAQ 리스트 호출 실패!!')));
		}
		
		exit;
	}
	
	## 공지사항 (Ajax)
	public function getNoticeList()
	{
		$noticeListItem = ($this->input->post('notice_list') != '') ? $this->input->post('notice_list') : '5' ;
		$noticeList = $this->m_contents->getNoticeList($noticeListItem);
		
		foreach($noticeList as $notice_key => $notice_item) {
			$noticeList[$notice_key]['subject'] = iconv('CP949', 'UTF-8', $notice_item['subject']);
		}
		
		if (count($noticeList) > 0) {
			echo json_encode(array('result' => true, 'msg' => iconv('CP949', 'UTF-8', '공지사항 리스트 불러오기 성공!!'), 'notice_list' => $noticeList));
		} else {
			echo json_encode(array('result' => false, 'msg' => iconv('CP949', 'UTF-8', '공지사항 리스트 불러오기 실패!! 관리자에 문의해주세요.')));
		}
		exit;
	}
	
	
	## 해커스에 바란다
	public function hackersHelp()
	{
		$helpIp = $this->m_notice->hackersHelpIp($this->input->post('session_ip'));
		if (count($helpIp) > 0) {
			echo "<script>alert('잠시 후 다시 시도해 주세요.'); location.href='/notice/main?customerType=support_write';</script>";
			exit;
		}
		
		// 해바 전송
		$helpSend = array(
			"TYPE" => "27" // 사이트별 코드(상단)  *필수입력사항( 상단에 각 사이트에 맞는 코드 입력 )
			// 1:칭찬합니다 2:불편사항 3:고객제안 4:기타
			, "REQUEST_TYPE" => $this->input->post('help_type') //요청분류값  *필수입력사항 , ## 없을 경우 1 입력 , 있을 경우 해당 사이트에 맞는 요청 분류값 입력
			, "DOC_TYPE" => "1"    //문서종류( 1: 해커스에 바란다 , 2: 1:1문의 , 3: 불편사항신고 )
			, "USERNIC" => ($this->input->post('name') != '') ? $this->input->post('name') : '' //입력받을시 닉네임을 받을 경우 해당칸에 입력, 없을시 빈칸 입력
			, "USERID" => ($this->session->userdata('member_id') != '') ? $this->session->userdata('member_id') : '' // 아이디를 입력받을시 입력
			, "USER_NM" => ($this->input->post('name') != '') ? $this->input->post('name') : '' //이름 입력 받을시 입력
			, "USER_TEL" => preg_replace('/[^0-9]/', '', implode($_POST['phone'])) //연락처 예) 01090578386 모든 특수 기호 제외
			, "USER_EMAIL" => $this->input->post('email') . '@' . $this->input->post('join_email') //이메일 예) abcd@naver.com
			, "TITLE" => $this->input->post('subject') //요청사항 작성시 제목
			, "CONTENT" => $this->input->post('contents') //요청사항 작성시 내용
			, "IP_ADDR" => $_SERVER['REMOTE_ADDR'] //사용자 ip
			, "AGREE_CHK" => "Y" //사용자 수집동의 여부 -> Y, N
			, "WRITE_TOOL" => "smarteditor" // 사용자가 작성시 사용한 에디터 ) 예) textarea,daumeditor
			, "REGDATE" => date("YmdHis") //요청자 등록시간
			, "IS_MOBILE" => "N" //사용자가 작성한 환경 ( 모바일 : Y ,  PC : N )
			, "USER_AGENT" => $_SERVER['HTTP_USER_AGENT']//사용자의 USER_AGENT
			, "ATT_FILE" => ""  // 예)http://img.hackers.com/ab/a/jpg
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
		
		// 해바 insert
		$userInfo = array(
			'member_id' => $this->session->userdata('member_id')
			,'ip_addr' => $_SERVER['REMOTE_ADDR']
		);
		
		$hackersInsert = $this->m_notice->hackersHelpInsert($_POST, $userInfo);
		if ($hackersInsert) {
			echo "<script>alert('해커스에 바란다 등록되었습니다.'); location.href='/notice/main?customerType=support_write';</script>";
		} else {
			echo "<script>alert('등록실패!! 관리자에 문의해주세요.');</script>";
		}
		exit;
	}
	
}