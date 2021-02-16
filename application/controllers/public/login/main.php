<?php

class Main extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('cookie');
        $this->load->model("login/m_member");
		$this->load->library('session');
        // $this->load->model("main/m_contents");
    }
    
    ## �α��� ������
    public function index()
    {
		// Header user_info, coupon_cnt
		$use_point = $this->m_member->getUserPoint($this->session->userdata('member_id')); // use_point (��������Ʈ)
		$used_point = $this->m_member->getUserUsedPoint($this->session->userdata('member_id')); // use_point (��������Ʈ)
		$user_point = ((int)$use_point->use_point - (int)$used_point->used_point);
		$userInfo = $this->m_member->getUserInfo($this->session->userdata('member_no', true), $this->session->userdata('member_id', true));
		$userCouponData = $this->m_member->getUserCoupon($this->session->userdata('member_no', true), $this->session->userdata('member_id', true));
  
		$head = array(
			'title' => '��Ŀ�����л� �α���',
			'_css' => '',
			'_js' => 'member'
			,'user_info' => $userInfo
			,'user_point' => $user_point
			,'user_coupon_cnt' => count($userCouponData)
		);
 

		$data = array(
			'images' => 'https://gscdn.hackers.co.kr/haksa2080/images',
		);

		
        $this->load->view('common/header',$head);
		// $this->load->view('common/allimpopup');
        $this->load->view('login/main', $data);
        $this->load->view('common/footer');
    }

    ## �α���
	function memberLogin()
	{
		// $_SESSION = array();
		// session_start();
		
		$user_id = @trim($_POST['user_id']);
		$user_password = @trim($_POST['password']);
		
		// �޸�ȸ�� Ȯ��
		$dormant_id = $this->m_member->checkDormant($user_id);
		if ($dormant_id) {
			echo "<script>location.href='/login/main?no=". $dormant_id->no ."&restMember=". $dormant_id->user_id ."';</script>";
			exit;
		}
		
		// ���̵� üũ
		$check_id = $this->m_member->checkUserId($user_id);
		if ($check_id <= 0) {
			echo "<script>alert('���Ե��� ���� ���̵� �Դϴ�. Ȯ�����ּ���.'); location.href='/login/main';</script>";
			exit;
		}
		
		// ȸ������ Ȯ�� (���̵�, ��й�ȣ)
		$check_member_password = $this->m_member->checkUserPassword($user_password);
		$check_member = $this->m_member->checkUserData($user_id, $check_member_password->password_new);
		
		if (!$check_member) {
			echo "<script>alert('��ϵ��� ���� ���̵��̰ų� ���̵� �Ǵ� ��й�ȣ�� �߸� �Է��ϼ̽��ϴ�. Ȯ�����ּ���.'); location.href='/login/main';</script>";
			exit;
		}
		
		// ID ����
		if ($_POST['IDSAVECHECK'] == "checked" || $_POST['IDSAVECHECK'] == "on" ) {
			setcookie("IDSAVECHECK", "checked", time() + 2592000 , "/");
			setcookie("SAVEID", $user_id, time() + 2592000 , "/");
			setcookie("SAVEPASS", $check_member_password->password_new, time() + 2592000 , "/");
		} else {
			setcookie("IDSAVECHECK", "", time() + 3600 , "/");
			setcookie("SAVEID", "", time() + 3600 , "/");
			setcookie("SAVEPASS", "", time() + 3600 , "/");
		}
		
		
		$session_data = array(
			'zb_logged_no' 			=> $check_member['no']
			,'zb_logged_name' 		=> $check_member['name']
			,'zb_logged_level' 		=> $check_member['level']
			,'zb_logged_user_id' 	=> $check_member['user_id']
			,'zb_logged_user_no' 	=> $check_member['no']
			,'zb_logged_email' 		=> $check_member['email']
			,'zb_logged_mailing' 	=> $check_member['mailing']
			,'zb_logged_handphone' 	=> $check_member['handphone']
			,'zb_logged_is_admin' 	=> $check_member['is_admin']
			,'zb_logged_time' 		=> time()
			,'zb_logged_ip' 		=> $_SERVER['REMOTE_ADDR']
			,'zb_writer_name' 		=> $check_member['name']
			,'member_id' 			=> $check_member['user_id']
			,'member_no' 			=> $check_member['no']
			,'member_name' 			=> $check_member['name']
//			,'user_id' 				=> $check_member['user_id']
//			,'user_no' 				=> $check_member['user_no']

//			,'member_id' 			=> $this->session->userdata('zb_logged_user_id')
//			,'member_no' 			=> $this->session->userdata('zb_logged_no')
//			,'member_name' 			=> $this->session->userdata('zb_logged_name')
//			,'user_id' 				=> $this->session->userdata('zb_logged_user_id')
//			,'user_no' 				=> $this->session->userdata('zb_logged_no')
		
		);
		$this->session->set_userdata($session_data); // session ���
		
		$return_url = (!empty($_POST['return_url'])) ? $_POST['return_url'] : '/' ;
		echo "<script>alert('�α��εǾ����ϴ�.'); location.href='". $return_url ."';</script>";
		exit;
		
	}
	
	## �޸���� �Ϲ�ȸ������ ��ȯ
	public function restMemberAgain()
	{
		if ($this->input->post('no') == '' && $this->input->post('member_id') == '') {
			echo json_encode(array('result' => false, 'msg' => iconv('EUC-KR', 'UTF-8', 'ȸ�������� �����ϴ�. �����ڿ� �������ּ���.')));
			exit;
		}
		
		$restMember = $this->m_member->restMemberAgain($this->input->post('no'), $this->input->post('member_id'));
		if ($restMember) {
			echo json_encode(array('result' => true, 'msg' => iconv('EUC-KR', 'UTF-8', '�޸������� �Ϸ� �Ǿ����ϴ�. �α��� �������� �̵��մϴ�.')));
		} else {
			echo json_encode(array('result' => false, 'msg' => iconv('EUC-KR', 'UTF-8', '�޸������� ���� �Ǿ����ϴ�. �����ڿ� �������ּ���.')));
		}
		exit;
	}
	
	
	## �α׾ƿ�
	public function memberLogOut()
	{
		$member_id = $this->input->post('member_id');
		if (!empty($member_id)) {
			$unset_session = array(
				'zb_logged_no' 			=> ''
				,'zb_logged_name' 		=> ''
				,'zb_logged_level' 		=> ''
				,'zb_logged_user_id' 	=> ''
				,'zb_logged_user_no' 	=> ''
				,'zb_logged_email' 		=> ''
				,'zb_logged_mailing' 	=> ''
				,'zb_logged_handphone' 	=> ''
				,'zb_logged_is_admin' 	=> ''
				,'zb_logged_time' 		=> ''
				,'zb_logged_ip' 		=> ''
				,'zb_writer_name' 		=> ''
				,'member_id' 			=> ''
				,'member_no' 			=> ''
				,'member_name' 			=> ''
				,'ut_name' 				=> ''
				,'ut_uno' 				=> ''
				,'ut_home_address' 		=> ''
				,'ut_home_address2' 	=> ''
				,'ut_home_tel' 			=> ''
				,'ut_sex' 				=> ''
				,'ut_birth' 			=> ''
				,'ut_handphone' 		=> ''
				,'ut_email' 			=> ''
			);

			$this->session->unset_userdata($unset_session);
			echo json_encode(array('result' => true, 'msg' => iconv('EUC-KR', 'UTF-8', '���������� �α׾ƿ� �Ǿ����ϴ�.')));
			exit;
		}
		
	}
    
    ## ���� �α���
	function memberUtLogin()
	{
		$unset_session = array(
			'ut_name' 				=> ''
			,'ut_uno' 				=> ''
			,'ut_home_address' 		=> ''
			,'ut_home_address2' 	=> ''
			,'ut_home_tel' 			=> ''
			,'ut_sex' 				=> ''
			,'ut_birth' 			=> ''
			,'ut_handphone' 		=> ''
			,'ut_email' 			=> ''
		);
		
		$this->session->unset_userdata($unset_session);
		
		$memberData = array(
			'url' => 'http://www.hackersut.com/member/member_json.php',
			'data' => array(
				"uid"=> $this->input->post('user_id'),
				"upw" => $this->input->post('password'),
				"host_name" =>"haksa2080.com"
			)
		);
		
		$options = array(
			'http' => array(
				'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
				'method'  => 'POST',
				'content' => http_build_query($memberData['data'])
			)
		);
		
		$context  = stream_context_create($options);
		$post_result = file_get_contents($memberData['url'], false, $context);
		$result_array = json_decode($post_result, true);
		
		if ($result_array['code'] == "OK") {
			// ���� ������ ���ǿ� ������
			$_SESSION['ut_name'] = iconv("UTF-8", "EUC-KR", $result_array['data']['name']);
			$_SESSION['ut_uno'] = $result_array['data']['uno'];
			$_SESSION['ut_home_address'] = iconv("UTF-8", "EUC-KR", $result_array['data']['home_address']);
			$_SESSION['ut_home_address2'] = iconv("UTF-8", "EUC-KR", $result_array['data']['address2']);
			$_SESSION['ut_home_tel'] = $result_array['data']['home_tel'];
			$_SESSION['ut_sex'] = $result_array['data']['sex'];
			$_SESSION['ut_birth'] = $result_array['data']['birth'];
			$_SESSION['ut_handphone'] = $result_array['data']['handphone'];
			$_SESSION['ut_email'] = $result_array['data']['email'];
			$_SESSION['ut_cnt'] = "";
			
			// ���
			$massage = array();
			$massage['code'] = "OK";
			$massage['data'] = $result_array['server_addr'];
			echo json_encode($massage);
			exit;
			
		} else if ($result_array['code'] == "ERROR") {
			if ($result_array['data'] == "sleep") {
				// �޸� ���� ó��
				$ut_cnt = $ut_cnt . "A";
				$_SESSION['ut_cnt'] = $ut_cnt;
				
				$massage = array();
				$massage['code'] = "SLEEP";
				$massage['data'] = $result_array['server_addr'];
				$massage['count'] = $ut_cnt;
				echo json_encode($massage);
				exit;
			} else {
				// ����
				$ut_cnt = $ut_cnt . "A";
				$_SESSION['ut_cnt'] = $ut_cnt;
				
				$massage = array();
				$massage['code'] = "NO";
				$massage['data'] = "e1";
				$massage['count'] = $ut_cnt;
				echo json_encode($massage);
				exit;
			}
		} else if ($result_array['code'] == "NO") {
			// ����
			$ut_cnt = $ut_cnt . "A";
			$_SESSION['ut_cnt'] = $ut_cnt;
			
			$massage = array();
			$massage['code'] = "NO";
			$massage['data'] = "e2";
			$massage['count'] = $ut_cnt;
			echo json_encode($massage);
			exit;
		}
		
	}
	
	
 
//    public function setUserSession(){
//
//        $userInfo = $this->m_member->checkUserId($this->input->post("user_id",true)) ;
//
//        $_SESSION['niceId'] = $this->input->post("user_id",true) ;
//        $_SESSION['user_id'] = $this->input->post("user_id",true);
//        $_SESSION['user_no'] = $userInfo['no'];
//        $_SESSION['user_name'] = $userInfo['name'];
//
//        //�α��� �α� ����
//        $this->m_member->save_access_log($_SESSION['niceId'],$_SESSION['user_name']);
//
//        echo $_SESSION['niceId'];
//    }

}