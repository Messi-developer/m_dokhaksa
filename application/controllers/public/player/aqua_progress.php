<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Aqua_progress extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->haksa2080 = $this->load->database("haksa2080", TRUE);
		$this->load->model('player/m_player');
		$this->load->model('lecture/m_lecture');
	}
	
	function index(){
		
		############ 테스트 ##############
		/*$test = json_encode($_POST);
		$__set_array = array(
			'run_sql' => $test,
			'wdate' => date('Y-m-d H:i:s'),
		);
		$this->m_player->insert_test($__set_array);*/
		############ 테스트 ##############
		$__action 				= "";
		$params['pos_type'] 	= $this->input->post('pos_type');
		if(!$params['pos_type']) exit;
		
		$params['uid']          = $this->input->post('uid');
		$params['uno'] 			= $this->input->post('userno');
		$params['si'] 			= $this->input->post('si');
		$params['d_model'] 		= $this->input->post('d_model');
		$params['pos'] 			= $this->input->post('pos');
		$params['enable_id'] 	= $this->input->post('enable_id');
		
		if($params['si']){
			$expsi 				= @explode("a",$params['si']);
			$params['idx']	 	= $expsi[0];
			$params['lesson'] 	= $expsi[1];
			$params['seq'] 		= $expsi[2];
		}else{
			$params['idx']	 	= "";
			$params['lesson'] 	= "";
			$params['seq'] 		= "";
		}
		
		switch($params['pos_type']){
			case "mid" 		: $__action = $params['pos_type']; break;
			//case "bookmark" : $__action = "lesson_pos"; $params['savetype'] = "a"; break;
			case "progress" : $__action = "lesson_pos"; $params['savetype'] = "b"; break;
			case "done" 	: $__action = "lesson_pos"; $params['savetype'] = (int)$params['pos']>0?"b":"a"; break;
		}
		
		$this->{$__action}($params);
	}
	
	/**
	 * player 시작 시 호출
	 *
	 * @param $params
	 */
	function mid($params) {
		$user_id = $params['uid'];
		$uno = $params['uno'];
		$d_id = $params['pos'];
		$d_model = $params['d_model'];
		$si = $params['si'];
		$expsi = explode("a",$si);
		$idx	= $expsi[0];	// 수강 인덱스
		$lesson = $expsi[1];	// 차수
		$seq	= $expsi[2];	// 영상번호
		
		$__set_array = array(
			'user_no' 		=> $uno,
			'd_id' 			=> $d_id,
			'd_model' 		=> $d_model,
			'idx' 		    => $idx,
			'lesson' 		=> $lesson,
			'seq' 		    => $seq,
			'wdate' 		=> date('Y-m-d H:i:s')
		);
		$this->m_player->insert_device_id($__set_array);
		
		$discount_div = $this->m_player->checkChampFreepass($idx);
		if ($discount_div) {
			$this->m_player->champDeviceInsertAPI($user_id,$d_id,$d_model,$idx,$lesson,$seq);
		}
		
	}
	
	/**
	 * player 종료 시 호출
	 *
	 * @param $params
	 */
	function done($params)
	{
		$pos = $params['pos'];
		$si = $params['si'];
		$expsi = explode("a",$si);
		$idx	= $expsi[0];	// 수강 인덱스
		$lesson = $expsi[1];	// 차수
		$seq	= $expsi[2];	// 영상번호
		
		if ($pos == '0') {
			$pos = '0';
		}
		
		if ($params['enable_id'] > 0) {
			$pos = intval($pos);
			$pos = floor($pos / 1000);
			
			$sql_update = "
							UPDATE lecture_loading_log
							SET user_loading_time = '". $pos ."'
							WHERE `no` = '". $params['enable_id'] ."'
						";
			$this->haksa2080->query($sql_update);
		}
	}
	
	function lesson_pos($params)
	{
		$pos = $params['pos'];
		$si = $params['si'];
		$expsi = explode("a",$si);
		$idx	= $expsi[0];	// 수강 인덱스
		$lesson = $expsi[1];	// 차수
		$seq	= $expsi[2];	// 영상번호
		
		if ($pos == '0') {
			$pos = '0';
		}
		
		
		$pos = intval($pos);
		$pos = floor($pos/1000);
		
		$sql_update = "UPDATE lecture_loading_log
                           SET user_loading_time=".$pos."
                           WHERE no = ".$params['enable_id'];
		$this->haksa2080->query($sql_update);
		
	}
	
	
}

?>