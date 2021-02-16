<?php

class M_board extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		$this->haksa2080 = $this->load->database("haksa2080", TRUE);
	}
	
	## 선택사항에따른 select box option 가져오기
	function getLectureMemOption($member_no, $member_id, $select_type, $lmno)
	{
		$select_type = (!empty($select_type)) ? $select_type : 'book';
		$addWhere = ($select_type == 'lecture') ? 'lec_no < 50000' : 'lec_no > 50000' ;
		$lmnoWhere = (!empty($lmno)) ? 'AND `no` = ' . $lmno : '' ;
		
		$sql = "
				SELECT
					mem_lec_name, `no`, lec_no
				FROM haksa2080.`lecture_mem`
				WHERE
					mem_no = '". $member_no ."' AND mem_user_id = '". $member_id ."'
					AND lec_state IN (1, 2, 3)
					AND regi_method IN (1, 2, 3)
					AND (mem_lec_name != '' or mem_lec_name IS NOT NULL) AND $addWhere $lmnoWhere
				";
		
		$query = $this->haksa2080->query($sql);
		return $query->result_array();
	}
	
	## 학습질문관리 질문 리스트 불러오기
	function getBoardList($member_no, $board_id, $limit)
	{
		$limit = (!empty($limit)) ? $limit : '0' ;
		$board = "HKboard_" . $board_id;
		$sql = "
				SELECT
					`no`, main_board.`no`, main_board.division, main_board.memo, main_board.`name`, main_board.subject, LEFT(main_board.wdate, 10) AS c_wdate, big_cate,
					main_board.islevel, main_board.sm_cate,
					(SELECT COUNT(`no`) FROM $board WHERE father = main_board.no) AS answer_cnt
				FROM
					$board AS main_board
				WHERE ismember = '". $member_no ."' AND father = '0' AND (`x` != 'del' OR `x` IS NULL) ORDER BY wdate DESC LIMIT $limit, 5
				";
		
		$query = $this->haksa2080->query($sql);
		return $query->result_array();
	}
	
	## 학습질문 리스트 답변가져오기
	function getBoardListReview($board_id, $board_no)
	{
		$board = "HKboard_" . $board_id;
		$sql = "SELECT `no`, `no`, division, memo, `name`, subject, LEFT(wdate, 10) AS c_wdate, big_cate, sm_cate FROM $board WHERE father = '". $board_no ."' AND (`x` != 'del' OR `x` IS NULL)";
		$query = $this->haksa2080->query($sql);
		return $query->result_array();
	}
	
	## 강의별 big_cate, middle_cate 확인
	function getBoardBigcategory($big_cate)
	{
		$sql = "SELECT `name` FROM haksa2080.Category_big WHERE `no` = '". $big_cate ."'";
		$query = $this->haksa2080->query($sql);
		return $query->row();
	}
	
	## 학습질문관리 질문 리스트 불러오기(cnt)
	function getBoardListCnt($member_no, $board_id)
	{
		$board = "HKboard_" . $board_id;
		$sql = "SELECT COUNT(`no`) AS cnt FROM $board WHERE ismember = '". $member_no ."' AND father = '0' AND (`x` != 'del' OR `x` IS NULL)";
		$query = $this->haksa2080->query($sql);
		return $query->row();
	}
	
	## 게시글 삭제
	function getBoardDelete($board_id, $board_no)
	{
		$this->haksa2080->where('no', $board_no);
		return $this->haksa2080->delete('HKboard_' . $board_id);
	}
	
	## 강좌명, 교재명 선택 강의정보 가져오기
	function getLearningQnaLectureInfo ($member_no, $member_id, $lecMemNo)
	{
		$lecMemSql = "
					SELECT lec_no, lec_big_cart, mem_middle_cart
					FROM haksa2080.`lecture_mem` WHERE mem_no = '". $member_no ."' AND mem_user_id = '". $member_id ."' AND `no` = '". $lecMemNo ."'
					";
		$query = $this->haksa2080->query($lecMemSql);
		$lecMem = $query->row();
		
		$lecSql = "
					SELECT `no`, big_cart, middle_cart, teacher_name, lec_name, lec_level1, lec_level2, lec_level3, lec_level4, lec_level5
					FROM haksa2080.`lecture` WHERE `no` = '". $lecMem->lec_no ."'
					";
		$query = $this->haksa2080->query($lecSql);
		return $query->row();
	}
	
	## HKboard_ID INSERT
	function HKboardInsert($member_no, $member_name, $formData)
	{
		$sql = "SELECT lec_no, lec_big_cart, mem_lec_name, mem_middle_cart FROM haksa2080.lecture_mem WHERE `no` = '". $formData['select_lec_no'] ."'";
		$query = $this->haksa2080->query($sql);
		$lecMemRs = $query->row();
		
		$insertData = array(
			'division'    		=> $lecMemRs->mem_lec_name . "[" . $formData['check_select_type'] . "]"
			,'big_cate'			=> $lecMemRs->lec_big_cart
			,'mid_cate'			=> $lecMemRs->mem_middle_cart
			,'sm_cate'			=> $lecMemRs->lec_no
			,'ismember'    		=> $member_no
			,'islevel'			=> $formData['islevel']
			,'name' 			=> $member_name
			,'memo'				=> $formData['memo']
			,'subject'			=> $formData['subject']
			,'ip'				=> $_SERVER['REMOTE_ADDR']
			,'reg_date'			=> mktime()
			,'wdate'			=> date('Y-m-d H:i:s')
		);
		
		return $this->haksa2080->insert('HKboard_' . $formData['board_id'], $insertData);
	}
}