<?php

class M_payment extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		$this->load->model("lecture/m_lecture");
		$this->haksa2080 = $this->load->database("haksa2080", TRUE);
	}
	
	## 강의정보 가져오기
	function getCouponList($member_no, $member_id)
	{
		$query = $this->haksa2080->query("SELECT * FROM auth_list WHERE user_no = '". $member_no ."' AND user_id = '". $member_id ."' AND order_num = ''");
		return $query->result_array();
	}
	
	## 장바구니 등록
	function basketInert($lec_no, $member_no, $return_url)
	{
		$checkBasket = $this->m_payment->getUserBasket($lec_no, $member_no);
		if (count($checkBasket) > 0){
			echo json_encode(array('result' => false, 'msg' => iconv('CP949', 'UTF-8', '해당강의는 이미 장바구니에 등록되어있습니다. 확인해주세요.'), 'return_url' => $return_url));
			exit;
		}
		
		$basketInertData = array(
			'mem_no' => $member_no
		,'lec_no' => $lec_no
		,'reg_date' => date('Y-m-d H:i:s')
		,'reg_ip' => $_SERVER['REMOTE_ADDR']
		);
		
		$insertRs = $this->haksa2080->insert('lecture_cart', $basketInertData);
		if ($insertRs) {
			echo json_encode(array('result' => true, 'msg' => iconv('CP949', 'UTF-8', '장바구니 등록성공')));
		} else {
			echo json_encode(array('result' => false, 'msg' => iconv('CP949', 'UTF-8', '장바구니 등록실패 관리자에 문의해주세요.')));
		}
		exit;
	}
	
	## 장바구니 삭제
	function basketDelete($lec_no, $member_no)
	{
		$deleteQuery = $this->haksa2080->query("DELETE FROM lecture_cart WHERE mem_no = '". $member_no ."' AND lec_no IN ($lec_no)");
		if ($deleteQuery) {
			echo json_encode(array('result' => true, 'msg' => iconv('CP949', 'UTF-8', '장바구니 삭제 성공')));
		} else {
			echo json_encode(array('result' => false, 'msg' => iconv('CP949', 'UTF-8', '장바구니 삭제실패 관리자에 문의해주세요.')));
		}
		exit;
	}
	
	## 장바구니 등록 (강의상세보기 페이지)
	function basketInertArray($arrayLecNo, $member_no, $return_url)
	{
		foreach($arrayLecNo as $lec_key => $lec_item) {
			$checkBasket = $this->m_payment->getUserBasket($lec_item, $member_no);
			if (count($checkBasket) > 0){
				echo json_encode(array('result' => false, 'msg' => iconv('CP949', 'UTF-8', '해당강의는 이미 장바구니에 등록되어있습니다. 확인해주세요.'), 'return_url' => $return_url));
				exit;
			} else {
				$basketInertData = array(
					'mem_no' => $member_no
				,'lec_no' => $lec_item
				,'reg_date' => date('Y-m-d H:i:s')
				,'reg_ip' => $_SERVER['REMOTE_ADDR']
				);
				
				$insertRs = $this->haksa2080->insert('lecture_cart', $basketInertData);
			}
		}
		
		if ($insertRs) {
			echo json_encode(array('result' => true, 'msg' => iconv('CP949', 'UTF-8', '장바구니 등록성공')));
			exit;
		}
		
	}
	
	## 장바구니 등록여부 확인
	function getUserBasket($lec_no, $member_no)
	{
		$query = $this->haksa2080->query("SELECT sno, lec_no, mem_no FROM lecture_cart WHERE lec_no = '". $lec_no ."' AND mem_no = '". $member_no ."'");
		return $query->row();
	}
	
	## 장바구니 리스트 불러오기
	function getUserBasketCode($member_no)
	{
		$query = $this->haksa2080->query("SELECT lec_no FROM lecture_cart WHERE mem_no = '". $member_no ."' ORDER BY sno DESC");
		return $query->result_array();
	}
	
	## 장바구니에 등록된 강의정보 가져오기
	function getUserBasketLectureList($getLectureCode)
	{
		$query = $this->haksa2080->query("SELECT `no`, teacher_name, lec_name, lec_price, lec_real_price, lec_join, book_join FROM lecture WHERE `no` = '". $getLectureCode ."'");
		return $query->row();
	}
	
	## 장바구니에 등록된 강의정보 가져오기
	function getUserBasketBookList($getBookCode)
	{
		$query = $this->haksa2080->query("SELECT book_id, book_name, author, store_link, pamount FROM book_info WHERE book_id = '". $getBookCode ."'");
		return $query->row();
	}
	
	## 결제/배송내역 조회 (무통장) : total_price
	function getUserDeliveryPassBookTotalPrice($member_no, $member_id, $order_num)
	{
		$sql = "
				SELECT SUM(total_price) as total_price, order_num
				FROM
					lecture_mem AS lecMem
				WHERE mem_no = '". $member_no ."' AND mem_user_id = '". $member_id ."' AND order_num = '". $order_num ."' AND total_no = '0'
				";

		$query = $this->haksa2080->query($sql);
		return $query->row();
	}
	
	## 결제/배송내역 조회 (무통장) : 부모강의만 노출
	function getUserDeliveryPassBook($member_no, $member_id, $limit)
	{
		// after_check ( e = 입금, 0 = 입금전 )
		$limit = (!empty($limit)) ? $limit : '0' ;
		$sql = "
				SELECT
					DISTINCT(order_num), lec_no, GROUP_CONCAT(mem_lec_name) AS mem_lec_name, mem_name, mem_user_id, real_price, wdate, bank_name, account_num, `no`
					,(SELECT COUNT(`order_num`) FROM haksa2080.lecture_mem WHERE lecMem.order_num = order_num AND total_no = '0') AS sub_lecture_cnt
				FROM
					haksa2080.`lecture_mem` AS lecMem
				WHERE
					mem_no = '". $member_no ."' AND mem_user_id = '". $member_id ."'
					AND regi_method IN ('0', '2') AND lec_state = '0'
					AND account_num != '' AND account_num IS NOT NULL
					AND bank_name IS NOT NULL AND bank_name != ''
					AND after_check = '0' AND total_no = '0'
					AND DATE_ADD(wdate, INTERVAL 1 WEEK) >= NOW()
					
				GROUP BY order_num
				ORDER BY wdate DESC
				LIMIT $limit, 5
				";
		
		$query = $this->haksa2080->query($sql);
		return $query->result_array();
	}
	
	## 결제/배송내역 조회 (무통장)
	function getUserDeliveryPassBookCnt($member_no, $member_id)
	{
		$sql = "
				SELECT
					DISTINCT(order_num), lec_no, GROUP_CONCAT(mem_lec_name) AS mem_lec_name, mem_name, mem_user_id, real_price, wdate, bank_name, account_num, `no`
					,(SELECT COUNT(`order_num`) FROM haksa2080.lecture_mem WHERE lecMem.order_num = order_num AND total_no = '0') AS sub_lecture_cnt
				FROM
					haksa2080.`lecture_mem` AS lecMem
				WHERE
					mem_no = '". $member_no ."' AND mem_user_id = '". $member_id ."'
					AND regi_method IN ('0', '2') AND lec_state = '0'
					AND account_num != '' AND account_num IS NOT NULL
					AND bank_name IS NOT NULL AND bank_name != ''
					AND after_check = '0' AND total_no = '0'
					AND DATE_ADD(wdate, INTERVAL 1 WEEK) >= NOW()
					
				GROUP BY order_num
				ORDER BY wdate DESC
				";
		
		$query = $this->haksa2080->query($sql);
		return $query->result_array();
	}

	## 결제/배송내역 조회 (결제완료/취소내역) : 부모강의만 노출
	// lec_state (1:수강대기, 2:수강중, 3:수강완료, 4:일시정지, 5:수강환불, 6:부분환불, 9:강의정지)
	function getUserLectureMemList($member_no, $member_id, $limit)
	{
		$limit = (!empty($limit)) ? $limit : '0' ;
		$sql = "
		SELECT
			order_num, lec_no, GROUP_CONCAT(mem_lec_name) AS mem_lec_name, mem_name, mem_user_id, real_price, total_price, LEFT(wdate, 10) as wdate, regi_method, `no`, lec_state
			,discount_div, discount_price, discount_percent
			,(SELECT COUNT(`order_num`) FROM haksa2080.lecture_mem WHERE lecMem.order_num = order_num AND total_no = '0') AS sub_lecture_cnt
		FROM
			lecture_mem AS lecMem
		WHERE
			mem_no = '". $member_no ."' AND mem_user_id = '". $member_id ."'
			AND regi_method IN (1, 2, 3)
			AND lec_state IN (1, 2, 3, 4, 5, 6, 9)
			AND total_no = '0'
			
		GROUP BY order_num
		ORDER BY `no` DESC
		LIMIT $limit, 5
		";
		
		$query = $this->haksa2080->query($sql);
		return $query->result_array();
	}
	
	## 결제/배송내역 조회 (결제완료/취소내역)
	function getUserLectureMemListCnt($member_no, $member_id)
	{
		$sql = "
		SELECT
			order_num, lec_no, GROUP_CONCAT(mem_lec_name) AS mem_lec_name, mem_name, mem_user_id, real_price, total_price, LEFT(wdate, 10) as wdate, regi_method, `no`, lec_state
			,discount_div, discount_price, discount_percent
			,(SELECT COUNT(`order_num`) FROM haksa2080.lecture_mem WHERE lecMem.order_num = order_num AND total_no = '0') AS sub_lecture_cnt
		FROM
			lecture_mem AS lecMem
		WHERE
			mem_no = '". $member_no ."' AND mem_user_id = '". $member_id ."'
			AND regi_method IN (1, 2, 3)
			AND lec_state IN (1, 2, 3, 4, 5, 6, 9)
			AND total_no = '0'
			
		GROUP BY order_num
		ORDER BY `no` DESC
		"; // lec_state (1:수강대기, 2:수강중, 3:수강완료, 4:일시정지, 5:수강환불, 6:부분환불, 9:강의정지)
		
		$query = $this->haksa2080->query($sql);
		return $query->result_array();
	}
	
	## 결제/배송내역 주문번호 삭제처리
	function lectureMemDelete ($member_no, $member_id, $order_num)
	{
		$sql = "SELECT mem_no, mem_user_id, order_num FROM haksa2080.lecture_mem WHERE mem_no = '". $member_no ."' AND mem_user_id = '". $member_id ."' AND order_num = '". $order_num ."'";
		$query = $this->haksa2080->query($sql);
		$lectureMem = $query->result_array();
		
		foreach($lectureMem as $lecMem_key => $lecMem_item) {
			$this->haksa2080->where('order_num', $lecMem_item['order_num']);
			$this->haksa2080->where('mem_no', $member_no);
			$this->haksa2080->where('mem_user_id', $member_id);
			$this->haksa2080->delete('lecture_mem');
		}
		
		return $lectureMem;
	}
	
	## 주문번호로 besong_info list 확인
	function getBesongInfoList($order_num, $book_code)
	{
		$sql = "
				SELECT
					besong.book_code, besong.uno, besong.home_address, besong.tail_address, besong.book_sale, besong.book_order, besong.book_move_num
					, besong.booksender, besong.book_price, besong.sap_ship_state
				FROM besong_info AS besong
				WHERE besong.order_num = '". $order_num ."' AND besong.book_code = '". $book_code ."'
				";
		$query = $this->haksa2080->query($sql);
		return $query->row();
	}
	
	## 결제시 쿠폰등록 check
	function authListUpdateCheck($member_no, $member_id, $coupon_num)
	{
		// etc :  1.결제페이지 사용, 2. 결제완료시 최종사용
		$updateData = array(
			'use_date_temp' => date('Y:m:d')
			,'etc' => '1'
		);
		$updateWhere = "user_no = '". $member_no ."' AND user_id = '". $member_id ."' AND cupone_number = '". $coupon_num ."'";
		return $this->haksa2080->update('auth_list', $updateData, $updateWhere);
	}
	
	## 강의구매시 lecture_mem INSERT
	function lectureMemInert($orderNum, $lectureInfo)
	{
		## 구매시 교재가있을경우
		if (!empty($lectureInfo['books'])) { // 교재 insert
			foreach($lectureInfo['books'] as $book_key => $book_item) {
				$lectureMemInsert = array(
					'no' => '',
					'order_num' => $orderNum,
					'mem_no' => $this->session->userdata('member_no', true),
					'mem_user_id' => $this->session->userdata('member_id', true),
					'lec_no' => ((int)$book_item->book_id + 50000),
					'total_no' => 0,
					'mem_name' => $this->session->userdata('member_name', true),
					'regi_method' => 0,
					'lec_big_cart' => $book_item->category,
					'mem_middle_cart' => $book_item->middle_cart,
					'mem_lec_name' => $book_item->book_name,
					'real_price' => $book_item->pamount,
					'total_price' => 0,
					'one_kang_price' => 0,
					'discount_price' => '',
					'discount_div' => '',
					'discount_detail' => '',
					'discount_etc' => '',
					'discount_kind' => '',
					// 'discount_kind' => '{$_GET['event_sno']}', // 이벤트코드별 할일금액 적용 제거
					'total_term' => 0,
					'lec_state' => 0,
					'book_sale' => '',
					'down_count' => 0,
					'once_stop_su' => 0,
					'wdate' => date('Y:m:d'),
					'tdate' => date('Y:m:d-H:i:s'),
				);
				
				$this->haksa2080->insert('lecture_mem', $lectureMemInsert);
			}
		}
		
		## 구매시 강의가 있을경우
		if (!empty($lectureInfo['lecture'])) {
			foreach($lectureInfo['lecture'] as $lecture_key => $lecture_item) {
				
				## 부모강의 INSERT
				$parentLectureInfo = $this->m_lecture->getLectureInfo($lecture_item->no);
				if ($parentLectureInfo->lec_term < '30') {
					$p_stop_cnt = '2';
				} else if ($parentLectureInfo->lec_term < '20') {
					$p_stop_cnt = '1';
				} else if ($parentLectureInfo->lec_term < '10') {
					$p_stop_cnt = '0';
				} else {
					$p_stop_cnt = '3';
				}
				
				$parentInsert = array(
					'no' => '',
					'order_num' => $orderNum,
					'mem_no' => $this->session->userdata('member_no'),
					'mem_user_id' => $this->session->userdata('member_id'),
					'lec_no' => $parentLectureInfo->no, // 강의코드
					'total_no' => 0, // 부모강의 코드
					'mem_name' => $this->session->userdata('member_name'),
					'regi_method' => 0,
					'lec_big_cart' => $parentLectureInfo->big_cart,
					'mem_middle_cart' => $parentLectureInfo->middle_cart,
					'mem_lec_name' => $parentLectureInfo->lec_name,
					'real_price' => ($parentLectureInfo->lec_real_price != '') ? $parentLectureInfo->lec_real_price : $parentLectureInfo->real_price ,
					'total_price' => 0,
					'one_kang_price' => 0,
					'discount_price' => '',
					'discount_div' => '',
					'discount_detail' => '',
					'discount_etc' => '',
					'discount_kind' => '',
					// 'discount_kind' => '{$_GET['event_sno']}', // 이벤트코드별 할일금액 적용 제거
					'total_term' => ($parentLectureInfo->lec_term != '') ? (int)$parentLectureInfo->lec_term : '' ,
					'lec_state' => 0,
					'book_sale' => '',
					'down_count' => 0,
					'once_stop_su' => $p_stop_cnt,
					'wdate' => date('Y:m:d'),
					'tdate' => date('Y:m:d-H:i:s'),
				);
				$this->haksa2080->insert('lecture_mem', $parentInsert);
				
				## 조인 강의 INSERT
				if ($lecture_item->lec_join != '' && $lecture_item->lec_join != null) {
					$joinLectureNo = explode(',', $lecture_item->lec_join);
					foreach($joinLectureNo as $join_key => $join_item) {
						$joinLectureInfo = $this->m_lecture->getLectureInfo($join_item); // 강의조회
						if ($joinLectureInfo->lec_term < '30') {
							$stop_cnt = '2';
						} else if ($joinLectureInfo->lec_term < '20') {
							$stop_cnt = '1';
						} else if ($joinLectureInfo->lec_term < '10') {
							$stop_cnt = '0';
						} else {
							$stop_cnt = '3';
						}
						
						$joinLectureInsert = array(
							'no' => '',
							'order_num' => $orderNum,
							'mem_no' => $this->session->userdata('member_no'),
							'mem_user_id' => $this->session->userdata('member_id'),
							'lec_no' => $join_item, // 강의코드
							'total_no' => $lecture_item->no, // 부모강의 코드
							'mem_name' => $this->session->userdata('member_name'),
							'regi_method' => 0,
							'lec_big_cart' => $joinLectureInfo->big_cart,
							'mem_middle_cart' => $joinLectureInfo->middle_cart,
							'mem_lec_name' => $joinLectureInfo->lec_name,
							'real_price' => ($joinLectureInfo->lec_real_price != '') ? $joinLectureInfo->lec_real_price : $joinLectureInfo->real_price,
							'total_price' => 0,
							'one_kang_price' => 0,
							'discount_price' => '',
							'discount_div' => '',
							'discount_detail' => '',
							'discount_etc' => '',
							'discount_kind' => '',
							// 'discount_kind' => '{$_GET['event_sno']}', // 이벤트코드별 할일금액 적용 제거
							'total_term' => ($joinLectureInfo->lec_term != '') ? (int)$joinLectureInfo->lec_term : '' ,
							'lec_state' => 0,
							'book_sale' => '',
							'down_count' => 0,
							'once_stop_su' => $stop_cnt,
							'wdate' => date('Y:m:d'),
							'tdate' => date('Y:m:d-H:i:s'),
						);
						$this->haksa2080->insert('lecture_mem', $joinLectureInsert);
					}
				}
			}
		}
	}
	
	
	## 결제시 주문번호 부모강의 조회
	function getOrderNumData($member_no, $member_id, $orderNum, $lecNo)
	{
		$sql = "
				SELECT *
				FROM lecture_mem
				WHERE order_num = '". $orderNum ."' AND total_no = '0' AND mem_no = '". $member_no ."' AND mem_user_id = '". $member_id ."' AND lec_no = '". $lecNo ."'
				";
		
		$query = $this->haksa2080->query($sql);
		return $query->row();
	}
	
	## 결제시 주문번호 부모강의 조회
	function getTradIncParentData($member_no, $orderNum, $lecNo)
	{
		$sql = "
				SELECT * FROM lecture_mem
				WHERE order_num = '". $orderNum ."' AND total_no = '0' AND mem_no = '". $member_no ."' AND lec_no = '". $lecNo ."'
				";
		$query = $this->haksa2080->query($sql);
		return $query->row();
	}
	
	## 결제시 주문번호 자식강의 조회
	function getTradIncJoinData($member_no, $order_num, $lecNo)
	{
		$sql = "
				SELECT * FROM lecture_mem
				WHERE order_num = '". $order_num ."' AND mem_no = '". $member_no ."' AND total_no = '". $lecNo ."'
				";
		$query = $this->haksa2080->query($sql);
		return $query->result_array();
	}
	
	## 결제 포인트사용시 주문번호에 부모 강의에 표시
	function paymentPointUpdate($order_num, $point_num, $member_id, $member_no, $lec_no)
	{
		$updateData = array(
			'discount_div' => 'P'
			,'discount_price' => $point_num
		);
		
		$updateWhere = "`order_num` = '". $order_num ."' AND total_no = '0' AND mem_user_id = '". $member_id ."' AND mem_no = '". $member_no ."' AND lec_no = '". $lec_no ."'";
		$updateRs = $this->haksa2080->update('lecture_mem', $updateData, $updateWhere);
		
		return $updateRs;
	}
	
	
	## 결제 쿠폰사용시 주문번호에 부모 강의에 표시
	function paymentCouponUpdate($order_num, $coupon_num, $member_id, $member_no, $lec_no)
	{
		// 해당쿠폰 조회
		$query = $this->haksa2080->query("SELECT lucky_price, lucky_percent, cupone_number FROM auth_list WHERE user_id = '". $member_id ."' AND user_no = '". $member_no ."' AND cupone_number = '". $coupon_num ."'");
		$couponList = $query->row();
		
		$updateData = array();
		if ($couponList->lucky_percent != 0) {
			$updateData['discount_div'] = 'C';
			$updateData['discount_percent'] = $couponList->lucky_percent;
			$updateData['discount_etc'] = $couponList->cupone_number;
		} else {
			$updateData['discount_div'] = 'C';
			$updateData['discount_price'] = $couponList->lucky_price;
			$updateData['discount_etc'] = $couponList->cupone_number;
		}
		
		$updateWhere = "`order_num` = '". $order_num ."' AND total_no = '0' AND mem_user_id = '". $member_id ."' AND mem_no = '". $member_no ."' AND lec_no = '". $lec_no ."'";
		$updateRs = $this->haksa2080->update('lecture_mem', $updateData, $updateWhere);
		
		return $updateRs;
	}
	
	
	
	#############
	
	## 결제 완료 페이지 결제정보 가져오기
	public function resultPaymentInfo($order_num, $member_no, $member_id)
	{
		$sql = "
				SELECT
					`no`, order_num, mem_no, mem_name, mem_user_id, lec_no, total_no, mem_lec_name, regi_method, real_price, total_price,
					total_term, lec_state, bank_name, account_num, wdate, tdate, discount_div, discount_price, discount_percent, discount_etc
				FROM lecture_mem
				WHERE order_num = '". $order_num ."' AND mem_no = '". $member_no ."' AND mem_user_id = '". $member_id ."' AND total_no = '0'
				";
		$query = $this->haksa2080->query($sql);
		return $query->row();
	}
	
	## 결제 완료 페이지 (payment_result = 부모강의조회)
	public function resultParentArray($order_num, $member_no, $member_id)
	{
		$sql = "
				SELECT
					`no`, order_num, mem_no, mem_name, mem_user_id, lec_no, total_no, mem_lec_name, regi_method, real_price, total_price,
					total_term, lec_state, bank_name, account_num, wdate, tdate, discount_div, discount_price, discount_percent, discount_etc
				FROM lecture_mem
				WHERE order_num = '". $order_num ."' AND mem_no = '". $member_no ."' AND mem_user_id = '". $member_id ."' AND total_no = '0'
				";
		$query = $this->haksa2080->query($sql);
		return $query->result_array();
	}
	
	## 결제 완료 페이지 (payment_result = 자식강의 조희)
	public function resultJoinLectureArray($order_num, $member_no, $member_id, $lec_no)
	{
		$sql = "
				SELECT
					`no`, order_num, mem_no, mem_name, mem_user_id, lec_no, total_no, mem_lec_name, regi_method, real_price, total_price,
					total_term, lec_state, bank_name, account_num, wdate, tdate
				FROM lecture_mem
				WHERE order_num = '". $order_num ."' AND total_no = '". $lec_no ."' AND mem_no = '". $member_no ."' AND mem_user_id = '". $member_id ."'
				";
		$query = $this->haksa2080->query($sql);
		return $query->result_array();
	}
	
	## 주문번호 생성이후 pay_method -> regi_method update
	public function lectureMemRegiMethod($member_no, $member_id, $order_num, $regi_method, $champ_person, $lmno, $total_price)
	{
		## order_num Update
		$updateData = array(
			'regi_method' => $regi_method
			,'champ_person' => $champ_person // 테스트결제 상태값
		);
		$updateWhere = "order_num = '". $order_num ."' AND mem_no = '". $member_no ."' AND mem_user_id = '". $member_id ."'";
		$this->haksa2080->update('lecture_mem', $updateData, $updateWhere);
		
		## 부모강의 Update
		$updateData = array(
			'total_price' => $total_price
			,'LGD_CLOSEDATE' => ($regi_method == '2') ? date("YmdHis", strtotime("+7 day", strtotime(date("Y-m-d H:i:s")))) : '' // 무통장입금시 입금기한
		);
		
		$updateWhere = "order_num = '". $order_num ."' AND mem_no = '". $member_no ."' AND mem_user_id = '". $member_id ."' AND `no` = '". $lmno ."'";
		return $this->haksa2080->update('lecture_mem', $updateData, $updateWhere);
	}
	
	## 결제 총상품금액 조회
	public function lectureMemRealPrice($order_num, $member_no, $member_id)
	{
		$query = $this->haksa2080->query("SELECT SUM(real_price) AS real_price FROM lecture_mem WHERE order_num = '". $order_num ."' AND mem_no = '". $member_no ."' AND mem_user_id = '". $member_id ."' AND total_no = '0'");
		return $query->row();
	}
	
	## 최종결제금액 조회
	public function lectureMemTotalPrice($order_num, $member_no, $member_id)
	{
		$query = $this->haksa2080->query("SELECT SUM(total_price) AS total_price FROM lecture_mem WHERE order_num = '". $order_num ."' AND mem_no = '". $member_no ."' AND mem_user_id = '". $member_id ."' AND total_no = '0'");
		return $query->row();
	}
	
	/**
	 * 결제 3단계 : LGU+결제창 띠우고 결제처리
	 */
	public function set_LGU_data($lecture_mem, $member_info, $getPost, $total_realPrice)
	{
		## 필수값 확인
		if ($this->session->userdata('member_no') == '' || $this->session->userdata('member_id') == '') {
			echo "<script>alert('로그인이 필요한 페이지입니다. 로그인후 이용해주세요.'); location.href='/login/main?return_url=' + location.pathname</script>";
		}
		
		$payReqMap['returnUrl'] = '/payment/main?lec_no=' . $getPost['lec_no'] . '&orderNum=' . $getPost['order_num'];
		
		/**********************************
		 * =====결제 모드=====
		 * test : 테스트결제
		 * service : 실결제모드
		 ***********************************/
		$testId = explode("|",PAYADMIN);
		$testDomain = array(
			'mq.haksa2080.com'
			,'ml.haksa2080.com'
		);
		
		if (in_array($this->session->userdata('member_id'), $testId) || in_array($_SERVER['HTTP_HOST'], $testDomain)){
			$HTTP_POST_VARS["CST_PLATFORM"] = "test";
		} else {
			$HTTP_POST_VARS["CST_PLATFORM"] = "service";
		}
		
		/* 결제 수단 선택 */
		$customtype = ($getPost['pay_method'] == '1') ? 'SC0010' : 'SC0040'; // $pay_method [ 1:카드 , 2:무통장 ]
		$HTTP_POST_VARS["CST_MID"] = "edu20802"; // 상점고유 ID
		
		$HTTP_POST_VARS["LGD_OID"] = (!empty($getPost['order_num'])) ? $getPost['order_num'] : $lecture_mem['0']->order_num ;
		$HTTP_POST_VARS["LGD_AMOUNT"] = $total_realPrice->total_price;
		$HTTP_POST_VARS["LGD_BUYER"] = ($this->session->userdata('member_name') != "") ? $this->session->userdata('member_name') : $member_info->name ; // 구매자이름
		$HTTP_POST_VARS["LGD_BUYERID"] = ($this->session->userdata('member_no') != "") ? $this->session->userdata('member_no') : $member_info->no ; // 구매자 mem_no
		$HTTP_POST_VARS["LGD_BUYEREMAIL"] = $member_info->email; // 구매자 이메일
		$HTTP_POST_VARS["LGD_BUYERADDRESS"] = (!empty($getPost['home_address'])) ? $getPost['home_address'] . $getPost['tail_address'] . '(' . $getPost['uno_new'] .')' : $member_info->home_address . $member_info->hobby . '(' . $member_info->uno_new . ')'; // 구매자 주소
		$HTTP_POST_VARS["LGD_BUYERPHONE"] = (!empty($getPost['phone_mobi'])) ? $getPost['phone_mobi'] : $member_info->handphone_index; // 구매자 전화번호
		$HTTP_POST_VARS["LGD_BUYERSSN"] = $member_info->new_birth; // 구매자 주민번호
		$HTTP_POST_VARS["LGD_PRODUCTCODE"] = $getPost['lec_no']; // 구매 강의코드
		$HTTP_POST_VARS["LGD_RECEIVER"] = $member_info->name; // 수취인
		$HTTP_POST_VARS["LGD_RECEIVERPHONE"] = $member_info->handphone; // 수취인 전화번호
		$HTTP_POST_VARS["LGD_DELIVERYINFO"] = (!empty($getPost['uno_new'])) ? $getPost['uno_new'] : $member_info->uno_new; // 구매자 주소
		$HTTP_POST_VARS["LGD_PRODUCTINFO"] = ((int)count($lecture_mem) >= 2) ? $lecture_mem['0']->mem_lec_name . ' 외' . (count($lecture_mem) - 1) . '건' : $lecture_mem['0']->mem_lec_name;
		
		$HTTP_POST_VARS["LGD_BUYERIP"] = $_SERVER['REMOTE_ADDR'];
		$HTTP_POST_VARS["LGD_CUSTOM_FIRSTPAY"] = $customtype; //변경해야함 변경 뒤 주석 삭제
		
		// 면세값
		// $HTTP_POST_VARS["LGD_TAXFREEAMOUNT"] = ($data["vat_type"] == 2 || $data["vat_type"] ==4 ) ? $data['tot_price'] : "";
		
		/* 가상계좌(무통장) 입금 마감 시간 설정 결제일로부터 7일 이후까지*/
		$LGD_CLOSEDATE = date("YmdHis", strtotime("+7 day", strtotime(date("Y-m-d H:i:s"))));
		
		/*
		 * [결제 인증요청 페이지(STEP2-1)]
		 *
		 * 샘플페이지에서는 기본 파라미터만 예시되어 있으며, 별도로 필요하신 파라미터는 연동메뉴얼을 참고하시어 추가 하시기 바랍니다.
		 */
		
		/*
		 * 1. 기본결제 인증요청 정보 변경
		 *
		 * 기본정보를 변경하여 주시기 바랍니다.(파라미터 전달시 POST를 사용하세요)
		 */
		
		/***********************************/
		$CST_PLATFORM               = $HTTP_POST_VARS["CST_PLATFORM"];      //LG유플러스 결제 서비스 선택(test:테스트, service:서비스)
		$CST_MID                    = $HTTP_POST_VARS["CST_MID"];           //상점아이디(LG유플러스으로 부터 발급받으신 상점아이디를 입력하세요)
		//테스트 아이디는 't'를 반드시 제외하고 입력하세요.
		$LGD_MID                    = (("test" == $CST_PLATFORM) ? "t" : "").$CST_MID;  //상점아이디(자동생성)
		$LGD_OID                    = $HTTP_POST_VARS["LGD_OID"];           //주문번호(상점정의 유니크한 주문번호를 입력하세요)
		$LGD_AMOUNT                 = $HTTP_POST_VARS["LGD_AMOUNT"];        //결제금액("," 를 제외한 결제금액을 입력하세요)
		$LGD_TAXFREEAMOUNT			= $HTTP_POST_VARS["LGD_TAXFREEAMOUNT"]; //면세금액
		
		$LGD_BUYER                  = $HTTP_POST_VARS["LGD_BUYER"];         			// 구매자명
		$LGD_BUYERID                = $HTTP_POST_VARS["LGD_BUYERID"];       			// 구매자 아이디
		$LGD_PRODUCTINFO            = $HTTP_POST_VARS["LGD_PRODUCTINFO"];   			// 상품명
		$LGD_BUYERADDRESS        	= $HTTP_POST_VARS["LGD_BUYERADDRESS"];        		// 구매자 주소
		$LGD_BUYERPHONE          	= $HTTP_POST_VARS["LGD_BUYERPHONE"];          		// 구매자 전화번호
		$LGD_BUYEREMAIL          	= $HTTP_POST_VARS["LGD_BUYEREMAIL"];          		// 구매자 이메일
		$LGD_BUYERSSN            	= $HTTP_POST_VARS["LGD_BUYERSSN"];            		// 구매자 주민번호
		$LGD_RECEIVER            	= $HTTP_POST_VARS["LGD_RECEIVER"];            		// 수취인
		$LGD_RECEIVERPHONE      	= $HTTP_POST_VARS["LGD_RECEIVERPHONE"];        		// 수취인 전화번호
		$LGD_DELIVERYINFO        	= $HTTP_POST_VARS["LGD_DELIVERYINFO"];        		// 배송지
		$LGD_PRODUCTCODE			= $HTTP_POST_VARS["LGD_PRODUCTCODE"];				// 구매 강의코드
		
		
		$LGD_CUSTOM_FIRSTPAY        = $HTTP_POST_VARS["LGD_CUSTOM_FIRSTPAY"];    //상점정의 초기결제수단
		$LGD_TIMESTAMP              = date('YmdHms');                         //타임스탬프
		$LGD_CUSTOM_SKIN            = "blue";                        //상점정의 결제창 스킨
		$LGD_MERTKEY				= "a10bdbec2b1f22eec8dcb63ba7feeb3e";	//모바일 상점아이디껄로 바꾼 뒤 주석 삭제
		$configPath 				= $_SERVER["DOCUMENT_ROOT"]."/application/views/payment/LGU/lgdacom";  						//LG유플러스에서 제공한 환경파일("/conf/lgdacom.conf") 위치 지정.
		
		/*
		 * 가상계좌(무통장) 결제 연동을 하시는 경우 아래 LGD_CASNOTEURL 을 설정하여 주시기 바랍니다.
		 */
		$LGD_CASNOTEURL				= "https://".$_SERVER['HTTP_HOST']."/payment/payment_lgu/cas_noteurl";
		
		/*
		 * LGD_RETURNURL 을 설정하여 주시기 바랍니다. 반드시 현재 페이지와 동일한 프로트콜 및  호스트이어야 합니다. 아래 부분을 반드시 수정하십시요.
		 */
		$LGD_RETURNURL				= "//".$_SERVER['HTTP_HOST']."/payment/payment_lgu/returnurl";
		// $LGD_RETURNURL				= "//".$_SERVER['HTTP_HOST']."/payment/main?pay_type=" . $_SESSION['PAYREQ_MAP']['pay_type'] . '&lec_no=' . $_SESSION['PAYREQ_MAP']['lec_no'] . '&orderNum=' . $_SESSION['PAYREQ_MAP']['order_num'];
		
		/*
		 * ISP 카드결제 연동중 모바일ISP방식(고객세션을 유지하지않는 비동기방식)의 경우, LGD_KVPMISPNOTEURL/LGD_KVPMISPWAPURL/LGD_KVPMISPCANCELURL를 설정하여 주시기 바랍니다.
		 */
		$LGD_KVPMISPNOTEURL       	= "https://".$_SERVER['HTTP_HOST']."/payment/payment_lgu/note_url";
		$LGD_KVPMISPWAPURL			= "https://".$_SERVER['HTTP_HOST']."/payment/payment_lgu/mispwapurl?LGD_OID=".$LGD_OID;   //ISP 카드 결제시, URL 대신 앱명 입력시, 앱호출함
		$LGD_KVPMISPCANCELURL     	= "https://".$_SERVER['HTTP_HOST']."/payment/payment_lgu/cancel_url";
		
		//에스크로 적용여부
//		if ($data['BSCnt'] > 0) {
//			$LGD_ESCROW_USEYN = ""; //유저선택
//		} else {
//			$LGD_ESCROW_USEYN = "N";
//		}
		
		/*
		 *************************************************
		 * 2. MD5 해쉬암호화 (수정하지 마세요) - BEGIN
		 *
		 * MD5 해쉬암호화는 거래 위변조를 막기위한 방법입니다.
		 *************************************************
		 *
		 * 해쉬 암호화 적용( LGD_MID + LGD_OID + LGD_AMOUNT + LGD_TIMESTAMP + LGD_MERTKEY )
		 * LGD_MID          : 상점아이디
		 * LGD_OID          : 주문번호
		 * LGD_AMOUNT       : 금액
		 * LGD_TIMESTAMP    : 타임스탬프
		 * LGD_MERTKEY      : 상점MertKey (mertkey는 상점관리자 -> 계약정보 -> 상점정보관리에서 확인하실수 있습니다)
		 *
		 * MD5 해쉬데이터 암호화 검증을 위해
		 * LG유플러스에서 발급한 상점키(MertKey)를 환경설정 파일(lgdacom/conf/mall.conf)에 반드시 입력하여 주시기 바랍니다.
		 */
		require_once($_SERVER["DOCUMENT_ROOT"]."/application/views/payment/LGU/lgdacom/XPayClient.php");
		
		$xpay = &new XPayClient($configPath, $LGD_PLATFORM);
		$xpay->Init_TX($LGD_MID);
		$LGD_HASHDATA = md5($LGD_MID . $LGD_OID . $LGD_AMOUNT . $LGD_TIMESTAMP . $xpay->config[$LGD_MID]);
		$LGD_CUSTOM_PROCESSTYPE = "TWOTR";
		
		######### GIT error 확인
		
		/*
		 *************************************************
		 * 2. MD5 해쉬암호화 (수정하지 마세요) - END
		 *************************************************
		 */
		$CST_WINDOW_TYPE = "submit";                                       // 수정불가
		$payReqMap['CST_PLATFORM']           = $CST_PLATFORM;              // 테스트, 서비스 구분
		$payReqMap['CST_WINDOW_TYPE']        = $CST_WINDOW_TYPE;           // 수정불가
		$payReqMap['CST_MID']                = $CST_MID;                   // 상점아이디
		$payReqMap['LGD_MID']                = $LGD_MID;                   // 상점아이디
		$payReqMap['LGD_OID']                = $LGD_OID;                   // 주문번호
		$payReqMap['LGD_AMOUNT']             = $LGD_AMOUNT;                // 결제금액
		$payReqMap['LGD_TAXFREEAMOUNT']      = $LGD_TAXFREEAMOUNT;         // 면세금액
		$payReqMap['LGD_CLOSEDATE']			 = $LGD_CLOSEDATE;			   // 결제가능시간
		
		
		$payReqMap['LGD_BUYER']              	= $LGD_BUYER;            	// 구매자
		$payReqMap['LGD_BUYERID']            	= $LGD_BUYERID;             // 구매자아이디
		$payReqMap['LGD_PRODUCTINFO']        	= $LGD_PRODUCTINFO;     	// 상품정보
		$payReqMap['LGD_BUYERADDRESS']        	= $LGD_BUYERADDRESS;     	// 구매자 주소
		$payReqMap['LGD_BUYERPHONE']        	= $LGD_BUYERPHONE;     	   	// 구매자 전화번호
		$payReqMap['LGD_BUYEREMAIL']         	= $LGD_BUYEREMAIL;          // 구매자 이메일
		$payReqMap['LGD_BUYERSSN']        		= $LGD_BUYERSSN;     	   	// 구매자 주민번호
		$payReqMap['LGD_PRODUCTCODE']		 	= $LGD_PRODUCTCODE;			// 강의코드
		$payReqMap['LGD_RECEIVER']        		= $LGD_RECEIVER;     	   	// 수취인
		$payReqMap['LGD_RECEIVERPHONE']        	= $LGD_RECEIVERPHONE;     	// 수취인 전화번호
		$payReqMap['LGD_DELIVERYINFO']        	= $LGD_DELIVERYINFO;     	// 배송지
		
		
		$payReqMap['LGD_CUSTOM_SKIN']        = $LGD_CUSTOM_SKIN;           // 결제창 SKIN
		$payReqMap['LGD_CUSTOM_PROCESSTYPE'] = $LGD_CUSTOM_PROCESSTYPE;    // 트랜잭션 처리방식
		$payReqMap['LGD_TIMESTAMP']          = $LGD_TIMESTAMP;             // 타임스탬프
		$payReqMap['LGD_HASHDATA']           = $LGD_HASHDATA;              // MD5 해쉬암호값
		$payReqMap['LGD_RETURNURL']   		 = $LGD_RETURNURL;      	   // 응답수신페이지
		$payReqMap['LGD_VERSION']         	 = "PHP_SmartXPay_1.0";		   // 버전정보 (삭제하지 마세요)
		$payReqMap['LGD_CUSTOM_FIRSTPAY']  	 = $LGD_CUSTOM_FIRSTPAY;	   // 디폴트 결제수단
		$payReqMap['LGD_CUSTOM_SWITCHINGTYPE']  = "SUBMIT";	       		   // 신용카드 카드사 인증 페이지 연동 방식
		$payReqMap['LGD_ESCROW_USEYN']       =  $LGD_ESCROW_USEYN;         // 에스크로 사용여부
		/*
		****************************************************
		* 안드로이드폰 신용카드 ISP(국민/BC)결제에만 적용 (시작)*
		****************************************************

		(주의)LGD_CUSTOM_ROLLBACK 의 값을  "Y"로 넘길 경우, LG U+ 전자결제에서 보낸 ISP(국민/비씨) 승인정보를 고객서버의 note_url에서 수신시  "OK" 리턴이 안되면  해당 트랜잭션은  무조건 롤백(자동취소)처리되고,
		LGD_CUSTOM_ROLLBACK 의 값 을 "C"로 넘길 경우, 고객서버의 note_url에서 "ROLLBACK" 리턴이 될 때만 해당 트랜잭션은  롤백처리되며  그외의 값이 리턴되면 정상 승인완료 처리됩니다.
		만일, LGD_CUSTOM_ROLLBACK 의 값이 "N" 이거나 null 인 경우, 고객서버의 note_url에서  "OK" 리턴이  안될시, "OK" 리턴이 될 때까지 3분간격으로 2시간동안  승인결과를 재전송합니다.
		*/
		
		$payReqMap['LGD_CUSTOM_ROLLBACK']    = "";			   	   				     // 비동기 ISP에서 트랜잭션 처리여부
		$payReqMap['LGD_KVPMISPNOTEURL']  	 = $LGD_KVPMISPNOTEURL;			   // 비동기 ISP(ex. 안드로이드) 승인결과를 받는 URL
		$payReqMap['LGD_KVPMISPWAPURL']  	 = $LGD_KVPMISPWAPURL;			   // 비동기 ISP(ex. 안드로이드) 승인완료후 사용자에게 보여지는 승인완료 URL
		$payReqMap['LGD_KVPMISPCANCELURL']   = $LGD_KVPMISPCANCELURL;		   // ISP 앱에서 취소시 사용자에게 보여지는 취소 URL
		
		/*
		****************************************************
		* 안드로이드폰 신용카드 ISP(국민/BC)결제에만 적용    (끝) *
		****************************************************
		*/
		// 안드로이드 에서 신용카드 적용  ISP(국민/BC)결제에만 적용 (선택)
		// $payReqMap['LGD_KVPMISPAUTOAPPYN'] = "Y";
		// Y: 안드로이드에서 ISP신용카드 결제시, 고객사에서 'App To App' 방식으로 국민, BC카드사에서 받은 결제 승인을 받고 고객사의 앱을 실행하고자 할때 사용
		
		// 가상계좌(무통장) 결제연동을 하시는 경우  할당/입금 결과를 통보받기 위해 반드시 LGD_CASNOTEURL 정보를 LG 유플러스에 전송해야 합니다 .
		$payReqMap['LGD_CASNOTEURL'] = $LGD_CASNOTEURL;               // 가상계좌 NOTEURL
		
		//Return URL에서 인증 결과 수신 시 셋팅될 파라미터 입니다.*/
		$payReqMap['LGD_RESPCODE']           = "";
		$payReqMap['LGD_RESPMSG']            = "";
		$payReqMap['LGD_PAYKEY']             = "";
		
		// $_SESSION['ev'] = $evtCode;
		$_SESSION['PAYREQ_MAP'] = $payReqMap;
		
		return $payReqMap;
	}
	
	
	/**
	 * 결제 최종결과 처리 로직
	 */
	function payres_model()
	{
		/*
		 * [최종결제요청 페이지(STEP2-2)]
		 *
		 * LG유플러스으로 부터 내려받은 LGD_PAYKEY(인증Key)를 가지고 최종 결제요청.(파라미터 전달시 POST를 사용하세요)
		 */
		
		/* ※ 중요
		* 환경설정 파일의 경우 반드시 외부에서 접근이 가능한 경로에 두시면 안됩니다.
		* 해당 환경파일이 외부에 노출이 되는 경우 해킹의 위험이 존재하므로 반드시 외부에서 접근이 불가능한 경로에 두시기 바랍니다.
		* 예) [Window 계열] C:\inetpub\wwwroot\lgdacom ==> 절대불가(웹 디렉토리)
		*/
		
		$configPath = $_SERVER["DOCUMENT_ROOT"]."/application/views/payment/LGU/lgdacom";  //LG유플러스에서 제공한 환경파일("/conf/lgdacom.conf,/conf/mall.conf") 위치 지정.
		
		$CST_PLATFORM				= $_POST["CST_PLATFORM"];
		$CST_MID                    = $_POST["CST_MID"];
		$LGD_MID                    = (("test" == $CST_PLATFORM) ? "t" : "") . $CST_MID;
		$LGD_PAYKEY                 = $_POST["LGD_PAYKEY"];
		
		require_once($_SERVER["DOCUMENT_ROOT"]."/application/views/payment/LGU/lgdacom/XPayClient.php");
		$xpay = &new XPayClient($configPath, $CST_PLATFORM);
		$xpay->Init_TX($LGD_MID);
		
		$xpay->Set("LGD_TXNAME", "PaymentByKey");
		$xpay->Set("LGD_PAYKEY", $LGD_PAYKEY);
		//금액을 체크하시기 원하는 경우 아래 주석을 풀어서 이용하십시요.
		//$DB_AMOUNT = "DB나 세션에서 가져온 금액"; //반드시 위변조가 불가능한 곳(DB나 세션)에서 금액을 가져오십시요.
		//$xpay->Set("LGD_AMOUNTCHECKYN", "Y");
		//$xpay->Set("LGD_AMOUNT", $DB_AMOUNT);
		
		/*
		*************************************************
		* 1.최종결제 요청(수정하지 마세요) - END
		*************************************************
		*/
		/*
		* 2. 최종결제 요청 결과처리
		*
		* 최종 결제요청 결과 리턴 파라미터는 연동메뉴얼을 참고하시기 바랍니다.
		*/
		if ($xpay->TX()) {
			//1)결제결과 화면처리(성공,실패 결과 처리를 하시기 바랍니다.)
			$LGD_RESPCODE				= $xpay->Response_Code();
			$LGD_RESPMSG				= $xpay->Response_Msg();
			$LGD_MID					= $xpay->Response("LGD_MID",0);
			$LGD_OID					= $xpay->Response("LGD_OID",0);
			$LGD_AMOUNT					= $xpay->Response("LGD_AMOUNT",0);
			$LGD_TAXFREEAMOUNT			= $xpay->Response("LGD_TAXFREEAMOUNT",0);
			$LGD_TID					= $xpay->Response("LGD_TID",0);
			$LGD_PAYTYPE				= $xpay->Response("LGD_PAYTYPE",0);
			$LGD_PAYDATE				= $xpay->Response("LGD_PAYDATE",0);
			$LGD_HASHDATA				= $xpay->Response("LGD_HASHDATA",0);
			$LGD_FINANCECODE			= $xpay->Response("LGD_FINANCECODE",0);
			$LGD_FINANCENAME			= $xpay->Response("LGD_FINANCENAME",0);
			$LGD_ESCROWYN				= $xpay->Response("LGD_ESCROWYN",0);
			$LGD_TIMESTAMP				= $xpay->Response("LGD_TIMESTAMP",0);
			$LGD_ACCOUNTNUM				= $xpay->Response("LGD_ACCOUNTNUM",0);
			$LGD_CASTAMOUNT				= $xpay->Response("LGD_CASTAMOUNT",0);
			$LGD_CASCAMOUNT				= $xpay->Response("LGD_CASCAMOUNT",0);
			$LGD_CASFLAG				= $xpay->Response("LGD_CASFLAG",0);
			$LGD_CASSEQNO				= $xpay->Response("LGD_CASSEQNO",0);
			$LGD_CASHRECEIPTNUM			= $xpay->Response("LGD_CASHRECEIPTNUM",0);
			$LGD_CASHRECEIPTSELFYN		= $xpay->Response("LGD_CASHRECEIPTSELFYN",0);
			$LGD_CASHRECEIPTKIND		= $xpay->Response("LGD_CASHRECEIPTKIND",0);
			$LGD_PAYER					= $xpay->Response("LGD_PAYER",0);
			
			$LGD_BUYER					= $xpay->Response("LGD_BUYER",0);
			$LGD_PRODUCTINFO			= $xpay->Response("LGD_PRODUCTINFO",0);
			$LGD_BUYERID				= $xpay->Response("LGD_BUYERID",0);
			$LGD_BUYERADDRESS			= $xpay->Response("LGD_BUYERADDRESS",0);
			$LGD_BUYERPHONE				= $xpay->Response("LGD_BUYERPHONE",0);
			$LGD_BUYEREMAIL				= $xpay->Response("LGD_BUYEREMAIL",0);
			$LGD_BUYERSSN				= $xpay->Response("LGD_BUYERSSN",0);
			$LGD_PRODUCTCODE			= $xpay->Response("LGD_PRODUCTCODE",0);
			$LGD_RECEIVER				= $xpay->Response("LGD_RECEIVER",0);
			$LGD_RECEIVERPHONE			= $xpay->Response("LGD_RECEIVERPHONE",0);
			$LGD_DELIVERYINFO			= $xpay->Response("LGD_DELIVERYINFO",0);
			
			// 결제결과 로그 (LGU 로그 insert)
			$log_array = array(
				'LGD_MID' => $LGD_MID,
				'LGD_OID' => $LGD_OID,
				'LGD_TID' => $LGD_TID,
				'LGD_BUYERID' => $LGD_BUYERID,
				'LGD_PRODUCTCODE' => $LGD_PRODUCTCODE,
				'LGD_PAYTYPE' => $LGD_PAYTYPE,
				'LGD_ESCROWYN' => $LGD_ESCROWYN,
				'LGD_AMOUNT' => $LGD_AMOUNT,
				'LGD_RESPCODE' => $LGD_RESPCODE,
				'LGD_RESPMSG' => $LGD_RESPMSG,
				'Reg_DT' => date('Y-m-d H:i:s')
			);
			$result = $this->haksa2080->insert("Payment_Log", $log_array);
			
			if( "0000" == $xpay->Response_Code() ) { // 결제로직 성공
				
				// 최종결제요청 결과 성공 DB처리
				echo "최종결제요청 결과 성공 DB처리하시기 바랍니다.<br>";
				
				$isDBOK = true; // DB처리 실패시 false로 변경해 주세요.
				
				switch($LGD_PAYTYPE) {
					## 무통장 결제
					case 'SC0040' :
						// 무통장입금시 은행정보 update
						$updateData = array(
							'account_num' => $LGD_ACCOUNTNUM
							,'bank_name' => $LGD_FINANCENAME
							,'receiptnumber' => $LGD_CASHRECEIPTNUM
						);
						$updateWhere = "`order_num` = '". $LGD_OID ."' AND total_no = '0' AND mem_no = '". $LGD_BUYERID ."'";
						$this->haksa2080->update('lecture_mem', $updateData, $updateWhere);
						
						echo "<script type='text/javascript'>top.location.href='/payment/payment_lgu/pay_result?order_num=" . $LGD_OID . "'</script>";
						break;
					
					## 카드결제
					case 'SC0010' :
						## trade_inc (결제완료시 DB 처리)
						
						$this->trade_inc($CST_PLATFORM, $LGD_OID, $LGD_PAYTYPE, $LGD_BUYER, $LGD_BUYERID, $LGD_AMOUNT, $LGD_PLATFORM, $LGD_ESCROWYN, $LGD_CASHRECEIPTNUM, $LGD_TID, $LGD_BUYERPHONE, $LGD_BUYERADDRESS, $LGD_PRODUCTCODE);
						
						// 결제확인정보 INSERT
						$hcPay_set = array(
							"tid"	=> $xpay->Response("LGD_TID",0),
							"pay_valid_yn"	=> "Y",
							"pay_final_yn"	=> "Y",
							"amount"	=> $LGD_AMOUNT,
							"financename"	=> $LGD_FINANCENAME,
							"financecode"	=> $LGD_FINANCECODE,
							"financeauthnum"=> $xpay->Response("LGD_FINANCEAUTHNUM",0),
							"pay_stat"	=> "1",
							"paytype"	=> "SC0010",
							"paydate"	=> $LGD_PAYDATE,
							"pay_src_type"	=> '2',
							"buyer"	=> $LGD_BUYER,
							"buyerid"	=> $LGD_BUYERID,
							"buyeremail"	=> $LGD_BUYEREMAIL,
							"buyerphone"	=> $LGD_BUYERPHONE,
							"reg_date"	=> $LGD_PAYDATE,
							// "reg_id"	=> $_SESSION[zb_logged_user_id],
							"reg_id"	=> ($this->session->userdata('member_id') != '') ? $this->session->userdata('member_id') : '' ,
							// "reg_name"	=> $_SESSION[zb_logged_name],
							"reg_name"	=> ($this->session->userdata('member_name') != '') ? $this->session->userdata('member_name') : '',
							"site_code"	=> '1',
							"mid"	=> $LGD_MID,
							"oid"	=> $LGD_OID,
							"cardacquirer"	=> $LGD_FINANCECODE,
							"cardnointyn"	=> $xpay->Response("LGD_CARDNOINTYN",0),
							"cardinstallmonth"	=> $xpay->Response("LGD_CARDINSTALLMONTH",0),
							"cardnum"	=> $xpay->Response("LGD_CARDNUM",0),
							"respmsg"	=> $xpay->Response("LGD_RESPMSG",0),
							"respcode"	=> $xpay->Response("LGD_RESPCODE",0),
							"exchangerate"	=> $xpay->Response("LGD_EXCHANGERATE",0),
							"ref_id"	=> "주문번호",
							"ref_type"	=> "1",
							"mod_id"	=> $xpay->Response("LGD_PAYER",0),
							"escrowyn"	=> "N",
							"hashdata"	=> $xpay->Response("LGD_HASHDATA",0),
							"transamount"	=> $xpay->Response("LGD_TRANSAMOUNT",0)
						);
						
						$isDBOK = $this->haksa2080->insert('hc_pay_log', $hcPay_set);
						
						// 최종결제요청 결과 성공 DB처리 실패시 Rollback 처리
						if( !$isDBOK ) {
							echo "<p>";
							$xpay->Rollback("상점 DB처리 실패로 인하여 Rollback 처리 [TID:" . $xpay->Response("LGD_TID",0) . ",MID:" . $xpay->Response("LGD_MID",0) . ",OID:" . $xpay->Response("LGD_OID",0) . "]");
							
							echo "TX Rollback Response_code = " . $xpay->Response_Code() . "<br>";
							echo "TX Rollback Response_msg = " . $xpay->Response_Msg() . "<p>";
							
							if( "0000" == $xpay->Response_Code() ) {
								echo "<script>alert('일시적인 네트워크 장애로 인한 오류가 발생 하였습니다. 결제를 다시 시도 해주세요.');top.location.href='/lecture/lecture_view?lec_no=".$LGD_PRODUCTCODE."';</script>";
								$this->champ->set("LGD_RESPMSG", '결제 자동취소 성공');
								$this->champ->where("LGD_OID", $LGD_OID);
								$this->champ->update("Payment_Log");
							} else {
								echo "<script>alert('일시적인 네트워크 장애로 인한 오류가 발생 하였습니다. 결제를 다시 시도 해주세요.');top.location.href='/lecture/lecture_view?lec_no=".$LGD_PRODUCTCODE."';</script>";
								$this->champ->set("LGD_RESPMSG", '결제 자동취소 실패');
								$this->champ->where("LGD_OID", $LGD_OID);
								$this->champ->update("Payment_Log");
							}
						} else { // 결제 최종 성공
							echo "<script type='text/javascript'>top.location.href='/payment/payment_lgu/pay_result?order_num=" . $LGD_OID . "'</script>";
						}
						break;
				}
				
			} else {
				// 최종결제요청 결과 실패 DB처리
				echo ("<script>alert('잘못된 접근입니다. 메인페이지로 돌아갑니다.'); location.href ='/';</script>");
			}
		} else {
			//2) API 요청실패 화면처리
			echo "결제요청이 실패하였습니다.  <br>";
			echo "TX Response_code = " . $xpay->Response_Code() . "<br>";
			echo "TX Response_msg = " . $xpay->Response_Msg() . "<p>";
			
			// 최종결제요청 결과 실패 DB처리
			echo "최종결제요청 결과 실패 DB처리하시기 바랍니다.<br>";
		}
	}
	
	/**
	 * 무통장 처리 로직
	 */
	function cas_noteurl_model($LGD_RESPCODE,$LGD_RESPMSG,$LGD_MID,$LGD_OID,$LGD_AMOUNT,$LGD_TID,$LGD_PAYTYPE,$LGD_PAYDATE,$LGD_HASHDATA,$LGD_FINANCECODE,$LGD_FINANCENAME,$LGD_ESCROWYN,$LGD_TIMESTAMP,$LGD_ACCOUNTNUM,$LGD_CASTAMOUNT,$LGD_CASCAMOUNT,$LGD_CASFLAG,$LGD_CASSEQNO,$LGD_CASHRECEIPTNUM,$LGD_CASHRECEIPTSELFYN,$LGD_CASHRECEIPTKIND,$LGD_PAYER,$LGD_BUYER,$LGD_PRODUCTINFO,$LGD_BUYERID,$LGD_BUYERADDRESS,$LGD_BUYERPHONE,$LGD_BUYEREMAIL,$LGD_BUYERSSN,$LGD_PRODUCTCODE,$LGD_RECEIVER,$LGD_RECEIVERPHONE,$LGD_DELIVERYINFO)
	{
		$LGD_MERTKEY = "a10bdbec2b1f22eec8dcb63ba7feeb3e";  // LG텔레콤에서 발급한 상점키로 변경해 주시기 바랍니다.
		$LGD_HASHDATA2 = md5($LGD_MID.$LGD_OID.$LGD_AMOUNT.$LGD_RESPCODE.$LGD_TIMESTAMP.$LGD_MERTKEY);
		
		/*
		 * 상점 처리결과 리턴메세지
		 *
		 * OK  : 상점 처리결과 성공
		 * 그외 : 상점 처리결과 실패
		 *
		 * ※ 주의사항 : 성공시 'OK' 문자이외의 다른문자열이 포함되면 실패처리 되오니 주의하시기 바랍니다.
		 */
		$resultMSG = "결제결과 상점 DB처리(LGD_CASNOTEURL) 결과값을 입력해 주시기 바랍니다.";
		
		// 결제결과 로그
		$log_array = array(
			'LGD_MID' => $LGD_MID,
			'LGD_OID' => $LGD_OID,
			'LGD_TID' => $LGD_TID,
			'LGD_BUYERID' => $LGD_BUYERID,
			'LGD_PRODUCTCODE' => $LGD_PRODUCTCODE,
			'LGD_PAYTYPE' => $LGD_PAYTYPE,
			'LGD_ESCROWYN' => $LGD_ESCROWYN,
			'LGD_AMOUNT' => $LGD_AMOUNT,
			'LGD_RESPCODE' => $LGD_RESPCODE,
			'LGD_RESPMSG' => $LGD_RESPMSG,
			'Reg_DT' => date('Y-m-d H:i:s')
		);
		$result = $this->haksa2080->insert("Payment_Log", $log_array);
		
		if ( $LGD_HASHDATA2 == $LGD_HASHDATA ) { // 해쉬값 검증이 성공이면
			if ( "0000" == $LGD_RESPCODE ) { // 결제가 성공이면
				if( "R" == $LGD_CASFLAG ) { // 무통장입금 플래그(무통장입금) - 'R':계좌할당, 'I':입금, 'C':입금취소
					$after_check = ($LGD_ESCROWYN == 'Y') ? 'e' : '0' ;
					
					$updateData = array(
						'account_num' => $LGD_ACCOUNTNUM
						,'bank_name' => $LGD_FINANCENAME
						// ,'regi_method' => 2
						,'receiptnumber' => $LGD_CASHRECEIPTNUM
						,'after_check' => $after_check
					);
					$updateWhere = "`order_num` = '". $LGD_OID ."' AND mem_no = '". $LGD_BUYERID ."'";
					$this->haksa2080->update('lecture_mem', $updateData, $updateWhere);
					
					/*
					 * 무통장 할당 성공 결과 상점 처리(DB) 부분
					 * 상점 결과 처리가 정상이면 "OK"
					 */
					//if( 무통장 할당 성공 상점처리결과 성공 )
					$resultMSG = "OK";
					
				} else if ( "I" == $LGD_CASFLAG ) { // 무통장입금 완료시 결제완료 로직
					
					$this->haksa2080->set("receiptnumber", $LGD_CASHRECEIPTNUM); // $LGD_CASHRECEIPTNUM == 현금영수증 승인번호
					$this->haksa2080->where("order_num", $LGD_OID, "total_no", 0);
					$this->haksa2080->update("lecture_mem");
					
					// 무통장입금 완료시 결제완료 로직
					$this->trade_inc($CST_PLATFORM, $LGD_OID, $LGD_PAYTYPE, $LGD_BUYER, $LGD_BUYERID, $LGD_AMOUNT, $LGD_PLATFORM, $LGD_ESCROWYN, $LGD_CASHRECEIPTNUM, $LGD_TID , $LGD_BUYERPHONE, $LGD_BUYERADDRESS, $LGD_PRODUCTCODE);
					
					/*
					* 무통장 입금 성공 결과 상점 처리(DB) 부분
					* 상점 결과 처리가 정상이면 "OK"
					*/
					//if( 무통장 입금 성공 상점처리결과 성공 )
					$resultMSG = "OK";
					
					########################결제완료 알림톡 전송 - 인강(무통장)###########################
					/*$sql = "SELECT handphone FROM zetyx_member_table WHERE user_id='{$LGD_BUYERID}'";
					$query = $this->champ->query($sql);
					$val = $query->row_array();

					$data = array($LGD_PRODUCTINFO);
					sendTalk($val["handphone"],'F00015',$data);*/
					#########################################################################################
					
				}else if( "C" == $LGD_CASFLAG ) {
					
					/*
					* 무통장 입금취소 성공 결과 상점 처리(DB) 부분
					* 상점 결과 처리가 정상이면 "OK"
					*/
					//if( 무통장 입금취소 성공 상점처리결과 성공 )
					$resultMSG = "OK";
				}
			} else { //결제가 실패이면
				/*
				 * 거래실패 결과 상점 처리(DB) 부분
				 * 상점결과 처리가 정상이면 "OK"
				 */
				//if( 결제실패 상점처리결과 성공 )
				$resultMSG = "OK";
			}
		} else { //해쉬값이 검증이 실패이면
			/*
			 * hashdata검증 실패 로그를 처리하시기 바랍니다.
			 */
			$resultMSG = "결제결과 상점 DB처리(LGD_CASNOTEURL) 해쉬값 검증이 실패하였습니다.";
		}
		
		echo $resultMSG;
	}
	
	/**
	 * 결제 성공 시 DB처리 로직
	 */
	function trade_inc($CST_PLATFORM, $LGD_OID, $LGD_PAYTYPE, $LGD_BUYER, $LGD_BUYERID, $LGD_AMOUNT, $LGD_PLATFORM, $LGD_ESCROWYN, $LGD_CASHRECEIPTNUM, $LGD_TID, $LGD_BUYERPHONE, $LGD_BUYERADDRESS, $LGD_PRODUCTCODE)
	{
		/* 결제수단 확인 */
		$regi_method = $LGD_PAYTYPE;
		switch($regi_method){
			case 'SC0010': $regi_method = '1'; break;	//신용카드
			case 'SC0030': $regi_method = '3'; break;	//계좌이체
			case 'SC0040': $regi_method = '2'; break;	//무통장
			case 'SC0060': $regi_method = '5'; break;	//휴대폰
			case 'SC0070': $regi_method = '0'; break;	//유선전화결제
			case 'SC0090': $regi_method = '0'; break;	//OK캐쉬백
			case 'SC0111': $regi_method = '0'; break;	//문화상품권
			case 'SC0112': $regi_method = '0'; break;	//게임문화상품권
			case 'homeplus': $regi_method = '4'; break;	//홈플러스상품권
			default : $regi_method = '0'; break;
		}
		
		
		## 주문강의코드로 조회 (부모강의 Array)
		$lectureMemParentInfo = array();
		$paymentLecNo = explode(',', $LGD_PRODUCTCODE); // 구매강의코드 : $LGD_PRODUCTCODE
		foreach($paymentLecNo as $lecNo_key => $lecNo_item) {
			$lectureMemParentInfo[] = $this->m_payment->getTradIncParentData($LGD_BUYERID, $LGD_OID, $lecNo_item); ## 부모강의생성
		}
		
		## 자식강의 조회 생성, 실결제금액 추출
		$paymentLectureName = array(); // 구매한강의 이름
		foreach($lectureMemParentInfo as $lecMem_key => $lecMem_item) {
			$paymentLectureName[] = $lecMem_item->mem_lec_name;
			
			## 자식강의 조회
			$lectureMemParentInfo[$lecMem_key]->join_lecture_list = $this->m_payment->getTradIncJoinData($LGD_BUYERID, $LGD_OID, $lecMem_item->lec_no);
		}
		
		## 유저정보 조회
		$query = $this->haksa2080->query("SELECT * FROM zetyx_member_table WHERE `no` = '". $LGD_BUYERID ."'");
		$memberInfo = $query->row();
		
		## 결제로직 start
		if (!empty($lectureMemParentInfo)) {
			
			## 동일한 주문번호에 Update
			$updateData = array(
				'regi_method' => $regi_method
				,'lec_state' => '1'
				,'wdate' => date('Y:m:d')
				,'tdate' => date('Y:m:d-H:i:s')
			);
			$updateWhere = "`order_num` = '". $LGD_OID ."' AND mem_no = '". $LGD_BUYERID ."'";
			$this->haksa2080->update('lecture_mem', $updateData, $updateWhere);
			
			## 부모 lecture_mem Update
			$updateData = array(
				'tid' => $LGD_TID
			);
			$updateWhere = "`order_num` = '". $LGD_OID ."' AND mem_no = '". $LGD_BUYERID ."' AND total_no = '0'";
			$this->haksa2080->update('lecture_mem', $updateData, $updateWhere);
			
			
			/************** 구매강의 장구니 삭제 START ****************/
			foreach ($paymentLecNo as $lecNo_key => $lecNo_item) { // 구매강의 코드 -> 장바구니 조회
				$this->haksa2080->where("mem_no", $LGD_BUYERID);
				$this->haksa2080->where("lec_no", $lecNo_item);
				$this->haksa2080->delete("lecture_cart");
			}
			/************** 구매강의 장구니 삭제 end ****************/
			
			
			/************** 쿠폰 사용확인 START ****************/
			foreach ($lectureMemParentInfo as $lecMem_key => $lecMem_item) {
				if ($lecMem_item->discount_div == 'C') {
					// 보유쿠폰 주문번호, 상태값변경
					$updateData = array(
						'order_num' => $LGD_OID
						, 'use_date_temp' => date('Y:m:d')
						, 're_buy' => 3
						, 'etc' => $lecMem_item->lec_no
					);
					$updateWhere = "cupone_number = '" . $lecMem_item->discount_etc . "' AND user_no = '" . $LGD_BUYERID . "'";
					$this->haksa2080->update('auth_list', $updateData, $updateWhere);
					
					// 사용쿠폰 INSERT
					$this->haksa2080->query("INSERT INTO auth_list_used (SELECT * FROM auth_list WHERE cupone_number = '" . $lecMem_item->discount_etc . "' AND user_no = '" . $LGD_BUYERID . "')");
					
					// 보유쿠폰 DELETE
					$this->haksa2080->where("cupone_number", $lecMem_item->discount_etc);
					$this->haksa2080->where("user_no", $LGD_BUYERID);
					$this->haksa2080->delete("auth_list");
				}
			}
			/************** 쿠폰 사용확인 END ****************/
			
			
			/************** 포인트 사용확인 START ****************/
			foreach ($lectureMemParentInfo as $lecMem_key => $lecMem_item) {
				if ($lecMem_item->discount_div == 'P') {
					// 포인트 사용 테이블에 사용내역 INSERT
					$pointUsedData = array(
						'order_num' => $LGD_OID,
						'mem_user_id' => $lecMem_item->mem_user_id,
						'lecture_no' => $lecMem_item->lec_no,
						'point_title' => $lecMem_item->mem_lec_name,
						'point' => $lecMem_item->discount_price,
						'point_term' => date('Y:m:d-H:i:s'),
						'point_state' => '1',
						'wdate' => date('Y:m:d-H:i:s')
					);
					$this->haksa2080->insert('used_point', $pointUsedData);
					
					$updateData = array(
						'usepoint' => ((int)$memberInfo->usepoint - (int)$lecMem_item->discount_price)
					);
					$updateWhere = "user_id = '" . $memberInfo->user_id . "' AND `no` = '" . $LGD_BUYERID . "'";
					$this->haksa2080->update('zetyx_member_table', $updateData, $updateWhere);
				}
			}
			/************** 포인트 사용확인 END ****************/
			
			
			/************** 포인트 적립 start ****************/
			## member table usepoint update
			$savePoint = (int)$memberInfo->usepoint + ((int)$LGD_AMOUNT * 0.05);
			$updateData = array(
				'usepoint' => $savePoint
			);
			$updateWhere = "user_id = '" . $memberInfo->user_id . "' AND `no` = '" . $LGD_BUYERID . "'";
			$this->haksa2080->update('zetyx_member_table', $updateData, $updateWhere);
			
			// use_point table insert
			$insertData = array(
				'order_num' => $LGD_OID
				, 'mem_user_id' => $memberInfo->user_id
				, 'lecture_no' => $LGD_PRODUCTCODE
				, 'point_title' => (count($paymentLectureName) >= 2) ? $paymentLectureName['0'] . ' 외 ' . (count($paymentLectureName) - 1) . '건' : $paymentLectureName['0']
				, 'point' => ((int)$LGD_AMOUNT * 0.05)
				, 'point_term' => date('Y:m:d-H:i:s')
				, 'point_state' => '1'
				, 'wdate' => date('Y:m:d-H:i:s')
			);
			$this->haksa2080->insert('use_point', $insertData);
			/************** 포인트 적립 end ****************/
			
			
			## 교재구매시 besong_info Insert
			foreach($lectureMemParentInfo as $lecMem_key => $lecMem_item) {
				# 부모강의중 교재구매시
				if ((int)$lecMem_item->lec_no > (int)50000) { // lecture_mem 단일로 교재만 구매했을시
					$book_code = (int)$lecMem_item->lec_no - (int)50000;
					$besongData = array(
						'order_num' => $LGD_OID
						,'user_id' => $memberInfo->user_id
						,'phone_home' => (!empty($LGD_RECEIVERPHONE)) ? $LGD_RECEIVERPHONE : $memberInfo->home_tel
						,'phone_mobi' => (!empty($LGD_BUYERPHONE)) ? $LGD_BUYERPHONE : $memberInfo->handphone
						,'uno' => (!empty($LGD_DELIVERYINFO)) ? $LGD_DELIVERYINFO : $memberInfo->uno_new
						,'home_address' => (!empty($LGD_BUYERADDRESS)) ? $LGD_BUYERADDRESS : $memberInfo->home_address
						,'tail_address' => $memberInfo->hobby
						,'lec_code' => $lecMem_item->lec_no
						,'book_code' => $book_code
						,'sap_ship_state' => '1'
						,'vsbed' => ''
						,'remark2' => ''
						,'discount_rate' => ''
					);
					$this->haksa2080->insert('besong_info', $besongData);
				}
				
				## 자식강의
				if ($lecMem_item->join_lecture_list != '') {
					foreach($lecMem_item->join_lecture_list as $joinLecture_key => $joinLecture_item) {
						## 자식강의 교재구매시
						if ((int)$joinLecture_item['lec_no'] > 50000) { // lecture_mem 단일로 교재만 구매했을시
							$book_code = (int)$joinLecture_item['lec_no'] - 50000;
							$besongData = array(
								'order_num' => $LGD_OID
								,'user_id' => $memberInfo->user_id
								,'phone_home' => (!empty($LGD_RECEIVERPHONE)) ? $LGD_RECEIVERPHONE : $memberInfo->home_tel
								,'phone_mobi' => (!empty($LGD_BUYERPHONE)) ? $LGD_BUYERPHONE : $memberInfo->handphone
								,'uno' => (!empty($LGD_DELIVERYINFO)) ? $LGD_DELIVERYINFO : $memberInfo->uno_new
								,'home_address' => (!empty($LGD_BUYERADDRESS)) ? $LGD_BUYERADDRESS : $memberInfo->home_address
								,'tail_address' => $memberInfo->hobby
								,'lec_code' => $joinLecture_item['lec_no']
								,'book_code' => $book_code
								,'sap_ship_state' => '1'
								,'vsbed' => ''
								,'remark2' => ''
								,'discount_rate' => ''
							);
							$this->haksa2080->insert('besong_info', $besongData);
						}
					}
				}
			}
			
		} else {
			// 결제성공 DB처리 실패 시 Rollback
			$isDBOK = false;
			$this->haksa2080->set("SQL_ERRORMSG","isDBOK = false : Empty Payment Data. (lec_state = 1)");
			$this->haksa2080->where("LGD_OID", $LGD_OID);
			$this->haksa2080->set("Payment_Log");
			
			// 결제실패시 update 원복
			$updateData = array(
				'tid' => ''
				,'lec_state' => '0'
				,'wdate' => date('Y:m:d')
				,'tdate' => date('Y:m:d-H:i:s')
			);
			$updateWhere = "`order_num` = '". $LGD_OID ."' AND member_no = '". $LGD_BUYERID ."'";
			$this->haksa2080->update('lecture_mem', $updateData, $updateWhere);
		}
		
		## 결제로직 end
		
	}
	
	
}