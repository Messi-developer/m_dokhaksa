<?php

class M_lecture extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->model("banner/m_banner");
        $this->haksa2080 = $this->load->database("haksa2080", TRUE);
    }


    ## 강의/교재신청 메인배너
    function getLectureBanner()
    {
        $lecture_slide_banner = $this->m_banner->getMainBanner_list('NEW_M_LECTURE_BIG');
        return $lecture_slide_banner;
    }

    ## 강의신청 리스트
    function getLectureList($getPost)
    {
		$addWhere_cate = (!empty($getPost['category_seq_num'])) ? " AND lec_list.big_cate = '". $getPost['category_seq_num'] ."'" : '';
		$addWhere_level = (!empty($getPost['lec_level'])) ? " AND lec." . $getPost['lec_level'] . " = '1'" : '';
		$addWhere_teacher = (!empty($getPost['teacher_list_name'])) ? " AND tea_list.mem_name = '". $getPost['teacher_list_name'] ."'" : '';

        // LIMIT
        $pageStart = (!empty($getPost['page_cnt'])) ? $getPost['page_cnt'] : '0'; // page_cnt == 현재 리스트 갯수

		$sql = "
		SELECT
    		lec_list.big_cate, cate_b.name AS major_name,
			lec_list.middle_cate, lec.no AS lec_no, lec_list.lec_sort, lec.lec_name, cate_b.seq_num, tea_info.teacher_id,
			lec.lec_level1, lec.lec_level2, lec.lec_level3, lec.lec_level4,
			LEFT(lec.wdate, 10) as wdate, lec.lec_term, lec.lec_su, lec.lec_term_date,
			lec.etc, lec.sampleLink, lec.lec_price, lec.lec_real_price,
			lec.lec_join, tea_list.intro_img, tea_list.profile_img, tea_list.mem_name AS t_name, lec.book_type, tea_list.introduction,
			COUNT(lec_list.lec_no) AS t_cnt
    	
    	FROM lectureList AS lec_list
    	JOIN teacher_info AS tea_info ON lec_list.lec_no = tea_info.lec_code
    	JOIN Category_big AS cate_b ON lec_list.big_cate = cate_b.no
    	JOIN Category_middle AS cate_m ON lec_list.middle_cate = cate_m.no
    	JOIN lecture AS lec ON lec.no = lec_list.lec_no
    	JOIN teacher_list AS tea_list ON tea_list.mem_id = tea_info.teacher_id
    	
    	WHERE 1=1 $addWhere_cate $addWhere_level $addWhere_teacher
    	
    	GROUP BY tea_info.lec_code HAVING MIN(tea_info.idx)
    	ORDER BY lec.`no` DESC
    	LIMIT $pageStart, 10
		";
		
        $query = $this->haksa2080->query($sql);
        return $query->result_array();
    }

    ## 강의신청 total cnt
    function getLectureListCnt($getPost)
    {
		$addWhere_cate = (!empty($getPost['category_seq_num'])) ? " AND lec_list.big_cate = '". $getPost['category_seq_num'] ."'" : '';
		$addWhere_level = (!empty($getPost['lec_level'])) ? " AND lec." . $getPost['lec_level'] . " = '1'" : '';
		$addWhere_teacher = (!empty($getPost['teacher_list_name'])) ? " AND tea_list.mem_name = '". $getPost['teacher_list_name'] ."'" : '';

        $query = $this->haksa2080->query("
    	SELECT COUNT(tea_info.idx) AS total_cnt
    	FROM lectureList AS lec_list
    	JOIN teacher_info AS tea_info ON lec_list.lec_no = tea_info.lec_code
    	JOIN Category_big AS cate_b ON lec_list.big_cate = cate_b.no
    	JOIN Category_middle AS cate_m ON lec_list.middle_cate = cate_m.no
    	JOIN lecture AS lec ON lec.no = lec_list.lec_no
    	JOIN teacher_list AS tea_list ON tea_list.mem_id = tea_info.teacher_id
    	
    	WHERE 1=1 $addWhere_cate $addWhere_level $addWhere_teacher
    	
    	GROUP BY tea_info.lec_code HAVING MIN(tea_info.idx)
    	");
        
        $result_array = $query->result_array();
        return $result_array;
    }
    
    ## 검색조건 선생님 이름가져오기
	function getTeacherName($getPost)
	{
		$addWhere_cate = (!empty($getPost['category_seq_num'])) ? " AND lec_list.big_cate = '". $getPost['category_seq_num'] ."'" : '';
		$addWhere_level = (!empty($getPost['lec_level'])) ? " AND lec." . $getPost['lec_level'] . " = '1'" : '';
		$addWhere_teacher = (!empty($getPost['teacher_list_name'])) ? " AND tea_list.mem_name = '". $getPost['teacher_list_name'] ."'" : '';
		
		$sql = "
		SELECT
    		tea_list.mem_name AS t_name
    	
    	FROM lectureList AS lec_list
    	JOIN teacher_info AS tea_info ON lec_list.lec_no = tea_info.lec_code
    	JOIN Category_big AS cate_b ON lec_list.big_cate = cate_b.no
    	JOIN Category_middle AS cate_m ON lec_list.middle_cate = cate_m.no
    	JOIN lecture AS lec ON lec.no = lec_list.lec_no
    	JOIN teacher_list AS tea_list ON tea_list.mem_id = tea_info.teacher_id
    	
    	WHERE 1=1 $addWhere_cate $addWhere_level $addWhere_teacher
    	
    	GROUP BY tea_list.mem_name HAVING MIN(tea_info.idx)
    	ORDER BY lec.`no` DESC
		";
		
		$query = $this->haksa2080->query($sql);
		return $query->result_array();
	}
	
    ## 강의신청 상세보기
    function getLectureView($getLecNo)
    {
    	$sql = "
			SELECT
				lec_list.big_cate, cate_b.name AS major_name,
				lec_list.middle_cate, lec.no AS lec_no, lec_list.lec_sort, lec.lec_name, cate_b.seq_num, tea_info.teacher_id,
				lec.lec_level1, lec.lec_level2, lec.lec_level3, lec.lec_level4,
				lec.wdate, lec.lec_term, lec.lec_su, lec.lec_term_date,
				lec.etc, lec.sampleLink, lec.lec_price, lec.lec_real_price, lec.bookcode,
				lec.lec_desc1, lec.lec_desc2, lec.lec_desc3, lec.lec_desc4, lec.lec_desc5,
				lec.lec_join, tea_list.mem_name AS t_name, lec.book_type,
				tea_list.intro_img, tea_list.profile_img,
				COUNT(lec_list.lec_no) AS t_cnt
			
			FROM lectureList AS lec_list
			JOIN teacher_info AS tea_info ON lec_list.lec_no = tea_info.lec_code
			JOIN Category_big AS cate_b ON lec_list.big_cate = cate_b.no
			JOIN Category_middle AS cate_m ON lec_list.middle_cate = cate_m.no
			JOIN lecture AS lec ON lec.no = lec_list.lec_no
			JOIN teacher_list AS tea_list ON tea_list.mem_id = tea_info.teacher_id
			
			WHERE lec.`no` = '". $getLecNo ."'
			
			GROUP BY tea_info.lec_code HAVING MIN(tea_info.idx)
    	";
        $query = $this->haksa2080->query($sql);

        return $query->row();
    }

    ## 강의신청 교재상세보기
    function getLectureBook($book_code)
    {
        $query = $this->haksa2080->query("SELECT book_id, book_name, book_img, list_comments, page_num, store_link, pamount, amount, sap_book_title, author FROM book_info WHERE book_id = '". $book_code ."' AND use_yn = 'y'");
        return $query->row();
    }
	
	## 교재정보 가져오기
//	function getLectureBookInfoArray($book_code)
//	{
//		$query = $this->haksa2080->query("SELECT book_id, book_name, pamount FROM book_info WHERE book_id = '". $book_code ."'");
//		return $query->result_array();
//	}

    ## 강의신청 강좌구성
    function getLectureKangList($lec_no)
    {
        $query = $this->haksa2080->query("
    	SELECT lecture_no, leck_kang_name, lec_content, leck_mode, leck_time,
    	CASE
    		WHEN leck_mode = 2 THEN leck_url
    	END AS leck_url
    	FROM lecture_kang
    	WHERE lecture_no = '". $lec_no ."' AND leck_kang_name != '0'
    	ORDER BY leck_kang_name ASC
    	");

        return $query->result_array();
    }
	
	function getLectureKang($lec_no)
	{
		$query = $this->haksa2080->query("
    	SELECT lecture_no, leck_kang_name, lec_content, leck_mode, leck_time,
    	CASE
    		WHEN leck_mode = 2 THEN leck_url
    	END AS leck_url
    	FROM lecture_kang
    	WHERE lecture_no = '". $lec_no ."'
    	AND leck_kang_name = '1'
    	");
		
		return $query->row();
	}

    ## 해당강의대한 연결되어있는 교재정보 코드 확인
    function getLectureLecToBookCnt($lec_no)
    {
    	$sql = "
				SELECT
					COUNT(toBook.`lec_code`)as cnt, toBook.`book_code`, lec.`book_type`
				FROM lecture AS lec INNER JOIN LecToBook AS toBook ON lec.`no` = toBook.lec_code
				WHERE lec.`no` = '". $lec_no ."'
				"; // AND (lec.book_type = '3' or lec.book_type = '4') 주석
		
        $query = $this->haksa2080->query($sql);
        return $query->row();
    }

    ## 강의코드에 대한 강의조회
    function getLectureInfo($lec_no)
    {
        $query = $this->haksa2080->query("SELECT * FROM lecture WHERE `no` = '". $lec_no ."'");
        return $query->row();
    }
	
	## 강의코드에 대한 강의조회 (lec_level)
	function getLectureInfoLevel($lec_no)
	{
		$query = $this->haksa2080->query("SELECT lec_level1, lec_level2, lec_level3, lec_level4, lec_level5, lec_level6, lec_level7 FROM lecture WHERE `no` = '". $lec_no ."'");
		return $query->row();
	}


    ########################
	
	## BEST 교재 리스트
	function getBestBookList()
	{
		$this->haksa2080->select("book_id, book_name, category, level, book_img, list_comments, store_link, pamount, amount");
		$this->haksa2080->from("book_info");
		$this->haksa2080->where("recommand_type", '1');
		$this->haksa2080->where("sale_yn", 'Y');
		$this->haksa2080->where("use_yn", 'Y');
		$this->haksa2080->order_by("sort", "DESC");
		
		$query = $this->haksa2080->get();
		return $query->result_array();
	}
	
    ## 교재신청 리스트
    function getBooksList($getPost)
    {
        $default_where = "use_yn = 'Y'";
		$addWhere_cate = (!empty($getPost['category_seq_num'])) ? " AND category = '". $getPost['category_seq_num'] ."'" : '' ;
		$addWhere_level = (!empty($getPost['lec_level'])) ? " AND level = '". $getPost['lec_level'] ."'" : '' ;
		$addWhere_author = (!empty($getPost['author_name'])) ? " AND author = '". $getPost['author_name'] ."'" : '' ;

        // LIMIT
        $pageStart = (!empty($getPost['page_cnt'])) ? $getPost['page_cnt'] : '0'; // page_cnt == 현재 리스트 갯수

		$sql = "
				SELECT book_id, `level`, book_name, pamount, amount, store_link, book_img, author, recommand_type
				FROM `book_info`
				WHERE $default_where $addWhere_cate $addWhere_level $addWhere_author
				ORDER BY category DESC, book_id DESC
				LIMIT $pageStart, 10
				";
		
        $query = $this->haksa2080->query($sql);
        return $query->result_array();
    }

    ## 강의신청 total cnt
    function getBooksListCnt($getPost)
    {
        $default_where = "use_yn = 'Y'";
		$addWhere_cate = (!empty($getPost['category_seq_num'])) ? " AND category = '". $getPost['category_seq_num'] ."'" : '' ;
		$addWhere_level = (!empty($getPost['lec_level'])) ? " AND level = '". $getPost['lec_level'] ."'" : '' ;
		$addWhere_author = (!empty($getPost['author_name'])) ? " AND author = '". $getPost['author_name'] ."'" : '' ;

        $query = $this->haksa2080->query("
									SELECT * FROM book_info
									WHERE $default_where $addWhere_cate $addWhere_level $addWhere_author
									");
        return $query->result_array();
    }


    ## 저자리스트
    function getBookInfoAuthor()
    {
        $query = $this->haksa2080->query("SELECT author FROM book_info WHERE author IS NOT NULL AND author != '' AND use_yn = 'Y' GROUP BY author ORDER BY sort DESC");
        return $query->result_array();
    }

    ## 교재상세보기
    function getBooksView($book_id)
    {
        $query = $this->haksa2080->query("SELECT book.*, SUBSTRING(book.`publish_date`, '1', '4') AS year, SUBSTRING(book.`publish_date`, '6', '2') AS month, SUBSTRING(book.`publish_date`, '9', '2') AS day FROM book_info AS book WHERE book_id = '". $book_id ."'");
        return $query->row();
    }

    ## 교재상세 연계강의
    function getBooksLecture($book_id)
    {
        $query = $this->haksa2080->query("SELECT big_cart, middle_cart, teacher_name, lec_name, lec_join FROM `lecture`  WHERE NO IN (SELECT DISTINCT(lec_code) FROM `LecToBook`  WHERE book_code = '{$book_id}')");
        return $query->result_array();
    }

    ## 교재 미리보기
    function getBooksPreView($book_id)
    {
        $query = $this->haksa2080->query("SELECT book_id, page_num, preview_img FROM book_preview_pages WHERE book_id = '". $book_id ."' ORDER BY page_num ASC");
        return $query->result_array();
    }

    ## 교재 lecture , LecToBook , book_info JOIN
    function getLectureToBooksInfo($book_id)
    {
        $query = $this->haksa2080->query("
			SELECT b_info.book_id, b_info.`pamount`, b_info.`amount`, b_info.`besong`, b_info.`book_name`, b_info.category
			FROM
				book_info AS b_info
			WHERE b_info.book_id = '". $book_id ."'
    	");

        return $query->row();
    }
    
    ## 구매강의 수강중인지 확인
    public function checkLectureState($member_no, $member_id, $lec_no)
    {
		$sql = "
		SELECT `no`, `order_num`, lec_no FROM lecture_mem
		WHERE mem_no = '". $member_no ."' AND mem_user_id = '". $member_id ."' AND total_no = '0' AND lec_no = '". $lec_no ."' AND lec_state IN ('1,', '2')
		";
		$query = $this->haksa2080->query($sql);
		return $query->row();
    }
    
    
    public function callChampAPI($arr_data,$cpn_no)
    {
        $this->haksa2080->set("menu",$arr_data['msg']);
        $this->haksa2080->set("intra_id",$arr_data['d_id']);
        $this->haksa2080->set("intra_name",$arr_data['result']);
        $this->haksa2080->set("run_sql",$cpn_no);
        $this->haksa2080->insert("admin_sql_log");
    }

    ## 강의 수강시 출석
    function lecture_att_insert($__set_array=NULL){

        if(is_array($__set_array)){
            $return_data = $this->haksa2080->insert("LecAttendance",$__set_array);
        }else{
            $return_data = FALSE;
        }

        return $return_data;
    }

    public function lecture_player_data($lmno, $seq_num, $lec_no)
    {
    	$sql = "
    	SELECT
    		kang.leck_url, kang.leck_url_p, kang.leck_url_p2, kang.leck_url_p3,
    		(SELECT `position` FROM lesson_pos WHERE idx = '".$lmno."' AND lesson = '".$seq_num."' AND seq = '".$lec_no."') as position,
    		lecMem.lec_no
    	FROM lecture_kang AS kang
    	JOIN (SELECT lec_no FROM haksa2080.`lecture_mem` WHERE `no` = '". $lmno ."' AND lec_state = '2') AS lecMem
    	ON kang.lecture_no = lecMem.lec_no AND kang.leck_kang_name = '". $seq_num ."'
    	WHERE kang.leck_mode = '2'
    	";
    	
        $query = $this->haksa2080->query($sql);
        return $query->row();
    }

    public function lecture_data($lec_no)
    {
        $this->haksa2080->select("*");
        $this->haksa2080->from("lecture");
        $this->haksa2080->where("no", $lec_no);

        $query = $this->haksa2080->get();
        $result_data = $query->row_array();

        return $result_data;
    }

    public function lectures_data($lec_no)
    {
        $sql = "SELECT * FROM lecture WHERE `no` IN (".substr($lec_no,0,-1).")";
        $query = $this->haksa2080->query($sql);
        $result_data = $query->result_array();

        return $result_data;
    }

    public function insertFreeLectureLog($lmno, $seq_num)
    {
        $this->haksa2080->set("uno",$_SESSION['member_no']);
        $this->haksa2080->set("uid",$_SESSION['member_id']);
        $this->haksa2080->set("lmno",$lmno);
        $this->haksa2080->set("seq",$seq_num);
        $this->haksa2080->set("lectype",'M');
        $this->haksa2080->set("wdate = NOW()",false);
        $this->haksa2080->set("ip",$_SERVER['REMOTE_ADDR']);

        $this->haksa2080->insert("LecAttendance");
    }
    
    ## 마이클래스 부모강의 확인
	function getMyclassLectureParent($page_type, $member_id, $member_no)
	{
		switch($page_type) {
			case "lecStart" :
				$sort = "lecMem.start_date IS NOT NULL DESC, lecMem.start_date DESC, lecMem.`no` DESC";
				$sort_type = "";
				break;
			case "lecWdate" :
				$sort = "end_date IS NULL ASC, end_date ASC ,`no` ASC";
				$sort_type = "";
				break;
			case "bigCate" :
				$sort = "lec.big_cart";
				$sort_type = "ASC";
				break;
			case "sortNo" :
				$sort = "lecMem.`no`";
				$sort_type = "DESC";
				break;
			default :
				$sort = "lecMem.`no`";
				$sort_type = "DESC";
				break;
		}
		
		$lec_state = ((empty($page_type) && $page_type == '') || ($page_type == 'lecStart' || $page_type == 'lecWdate' || $page_type == 'bigCate' || $page_type == 'sortNo')) ? "lecMem.lec_state IN ('1', '2')" : "lecMem.lec_state = '3'";
		$lecNowDate = ((empty($page_type) && $page_type == '') || ($page_type == 'lecStart' || $page_type == 'lecWdate' || $page_type == 'bigCate' || $page_type == 'sortNo')) ? ">=" : "<=";
		
    	$sql = "
		SELECT
			lecMem.order_num, lecMem.`no`, lecMem.mem_user_id, lecMem.lec_no, lecMem.total_no, lecMem.mem_lec_name, lecMem.real_price, lecMem.total_term,
			lecMem.once_stop_su, lecMem.lec_state, lecMem.order_num, LEFT(lecMem.start_date, 10) AS start_date, RIGHT(lecMem.start_date, 10) AS end_date,
			lec.big_cart, lec.middle_cart
		FROM
			haksa2080.`lecture_mem` AS lecMem INNER JOIN haksa2080.`lecture` AS lec ON lecMem.lec_no = lec.`no`
		WHERE
			lecMem.mem_user_id = '". $member_id ."' AND lecMem.mem_no = '". $member_no ."'
			AND lecMem.regi_method IN (1, 2, 3)
			AND (DATE_FORMAT(RIGHT(lecMem.start_date, 10), '%Y-%m-%d %H:%i:%s') ". $lecNowDate ." NOW() OR lecMem.`start_date` IS NULL)
			AND $lec_state
			AND lecMem.total_no = '0'
		ORDER BY $sort $sort_type
		";
    	
		$query = $this->haksa2080->query($sql);
		return $query->result_array();
	}
	
	## 마이클래스 자식강의 확인
	function getMyclassLectureList($page_type, $lec_no, $order_num, $member_id, $member_no)
	{
		switch($page_type) {
			case "lecStart" :
				$sort = "lecMem.start_date IS NOT NULL DESC, lecMem.start_date DESC, lecMem.`no` DESC";
				$sort_type = "";
				break;
			case "lecWdate" :
				$sort = "end_date IS NULL ASC, end_date ASC ,`no` ASC";
				$sort_type = "";
				break;
			case "bigCate" :
				$sort = "lec.big_cart";
				$sort_type = "ASC";
				break;
			case "sortNo" :
				$sort = "lecMem.`no`";
				$sort_type = "DESC";
				break;
			default :
				$sort = "lecMem.`no`";
				$sort_type = ($this->input->get('no') != '') ? 'ASC' : 'DESC';
				// $sort_type = "DESC";
				break;
		}
		
		$lec_state = ((empty($page_type) && $page_type == '') || $page_type == 'lecStart' || $page_type == 'lecWdate' || $page_type == 'bigCate' || $page_type == 'sortNo') ? "lecMem.lec_state IN ('1', '2')" : "lecMem.lec_state = '3'";
		
		$sql = "
		SELECT
			lecMem.`no`, lecMem.mem_user_id, lecMem.lec_no, lecMem.total_no, lecMem.mem_lec_name, lecMem.real_price, lecMem.total_term, lecMem.once_stop_su
			,lecMem.lec_state, lecMem.order_num, LEFT(lecMem.start_date, 10) AS start_date, RIGHT(lecMem.start_date, 10) AS end_date
			,lec.big_cart, lec.middle_cart
		FROM
			haksa2080.`lecture_mem` AS lecMem INNER JOIN haksa2080.`lecture` AS lec ON lecMem.lec_no = lec.`no`
		WHERE
			lecMem.mem_user_id = '". $member_id ."' AND lecMem.mem_no = '". $member_no ."'
			AND lecMem.regi_method IN (1, 2, 3)
			AND (DATE_FORMAT(RIGHT(lecMem.start_date, 10), '%Y-%m-%d %H:%i:%s') >= NOW() OR lecMem.`start_date` IS NULL)
			AND $lec_state
			AND lecMem.total_no = '". $lec_no ."' AND lecMem.order_num = '". $order_num ."'
			ORDER BY $sort $sort_type;
		";
		
		$query = $this->haksa2080->query($sql);
		return $query->result_array();
	}
	
	## 마이클래스 패키지강의 리스트
	function getMyclassLectureParentList($no, $member_id, $member_no)
	{
		$lec_state = "lec_state IN ('1', '2')";
		
		$sql = "
		SELECT `no`, mem_user_id, lec_no, total_no, mem_lec_name, real_price, total_term, once_stop_su, lec_state, order_num, LEFT(start_date, 10) AS start_date, RIGHT(start_date, 10) AS end_date
		FROM haksa2080.`lecture_mem`
		WHERE
			mem_user_id = '". $member_id ."' AND mem_no = '". $member_no ."'
			AND regi_method IN (1, 2, 3)
			AND `no` = '". $no ."' AND $lec_state
			AND total_no = '0'
		ORDER BY `no` DESC
		";
		
		$query = $this->haksa2080->query($sql);
		return $query->row();
	}
	
	## 마이클래스 강의코드별 강의조회
	function getMyclassTeacherInfo($lec_no)
	{
		$query = $this->haksa2080->query("SELECT `no`, teacher_name, lec_su, lec_term  FROM lecture WHERE `no` = '". $lec_no ."'");
		return $query->row();
	}
	
	## 마이클래스 강의 수강대기 -> 수강중 업데이트 (자식강의)
	function updateJoinLecture($no, $lec_no)
	{
		// 주문번호 조회
    	$sql = "SELECT order_num FROM lecture_mem WHERE `no` = '". $no ."'";
    	$query = $this->haksa2080->query($sql);
		$parentOrderNum = $query->row();
		
		$parentSql = "
					SELECT `no`, mem_user_id, lec_no, total_no, mem_lec_name, real_price, total_term, once_stop_su, lec_state, order_num, LEFT(start_date, 10) AS start_date, RIGHT(start_date, 10) AS end_date
					FROM lecture_mem WHERE order_num = '". $parentOrderNum->order_num ."' AND total_no = '". $lec_no ."'
					";
		$query = $this->haksa2080->query($parentSql);
		return $query->result_array();
	}
	
	## 마이클래스 강의 수강대기 -> 수강중 업데이트
	function updateLectureState($no, $lec_no, $total_term)
	{
		$nowDate = date('Y:m:d');
		$totalTerm = strtotime("+" . $total_term ."days");
		$pluseDate = date('Y:m:d', $totalTerm);
		
		## 부모강의 강의시작
		$updateData = array(
			'lec_state' => '2'
			,'start_date' => $nowDate . '-' . $pluseDate
		);
		$updateWhere = "no = '". $no ."' AND lec_no = '". $lec_no ."'";
		return $this->haksa2080->update('lecture_mem', $updateData, $updateWhere);
		
		// 자식강의 존재여부 확인
		$joinLectureList = $this->updateJoinLecture($no, $lec_no);
		if (!empty($joinLectureList)) {
			foreach($joinLectureList as $join_key => $join_item) {
				$updateData = array(
					'lec_state' => '2'
					,'start_date' => $nowDate . '-' . $pluseDate
				);
				$updateWhere = "order_num = '". $join_item['order_num'] ."' AND total_no = '". $lec_no ."'";
				return $this->haksa2080->update('lecture_mem', $updateData, $updateWhere);
			}
			
		} else {
			$updateData = array(
				'lec_state' => '2'
				,'start_date' => $nowDate . '-' . $pluseDate
			);
			$updateWhere = "`no` = '". $no ."' AND lec_no = '". $lec_no ."'";
			return $this->haksa2080->update('lecture_mem', $updateData, $updateWhere);
		}
		
	}
	
	
	## 마이클래스 상세보기 (강의리스트)
	function getMyclassLectureView($member_id, $member_no, $lec_no, $no)
	{
		$sql = "SELECT
		lec_mem.`no`, lec_mem.mem_user_id, lec_mem.lec_no, lec_mem.total_no, lec_mem.mem_lec_name, lec_mem.real_price, lec_mem.total_term, lec_mem.once_stop_su
		, lec_mem.lec_state, lec_mem.order_num, LEFT(lec_mem.start_date, 10) AS start_date, RIGHT(lec_mem.start_date, 10) AS end_date
		, lec.lec_su, lec.lecLImg, lec.lec_term
		FROM lecture_mem AS lec_mem LEFT JOIN lecture AS lec ON lec_mem.lec_no = lec.`no`
		WHERE
			lec_mem.mem_user_id = '". $member_id ."' AND lec_mem.mem_no = '". $member_no ."'
			AND lec_mem.regi_method IN (1, 2, 3)
			AND (lec_mem.lec_state = '1' OR lec_mem.lec_state = '2')
			AND lec_mem.lec_no = '". $lec_no ."' AND lec_mem.`no` = '". $no ."';
		";
		
		$query = $this->haksa2080->query($sql);
		return $query->row();
	}
	
	## 마이클래스 상세보기 (영상리스트)
	function getMyclassLectureKangList($lec_no)
	{
		$sql = "
				SELECT
					lecture_no, leck_mode, leck_kang_name, lec_content, leck_url, leck_time
				FROM
					haksa2080.`lecture_kang`
				WHERE
					lecture_no = '". $lec_no ."' AND leck_mode = '2' ORDER BY leck_kang_name ASC
				";
		
		$query = $this->haksa2080->query($sql);
		return $query->result_array();
	}
	
	## 마이클래스 상세보기 (강의별 user_loading_time check)
	function getMyclassLecKangUserLoadingTime($member_id, $lmno, $lec_no, $lec_num)
	{
		$sql = "SELECT user_loading_time FROM lecture_loading_log WHERE user_id = '". $member_id ."' AND lmno = '". $lmno ."' AND lec_no = '". $lec_no ."' AND lec_num = '". $lec_num ."'";
		$query = $this->haksa2080->query($sql);
		return $query->row();
	}
	
	## 마이클래스 상세보기 (강의별 user_loading_time check)
	function getMyclassLecKangUserLoadingCnt($member_id, $lmno, $lec_no)
	{
		$sql = "SELECT COUNT(`no`) as cnt FROM lecture_loading_log WHERE user_id = '". $member_id ."' AND lmno = '". $lmno ."' AND lec_no = '". $lec_no ."'";
		$query = $this->haksa2080->query($sql);
		return $query->row();
	}
	
	## 마이클래스 상세보기 cnt (영상리스트 Ajax)
	function getMyclassLecKangCnt($lec_no)
	{
		$sql = "SELECT count(`no`) AS cnt FROM haksa2080.`lecture_kang` WHERE lecture_no = '". $lec_no ."' AND leck_mode = '2' ORDER BY leck_kang_name ASC";
		$query = $this->haksa2080->query($sql);
		return $query->row();
	}
	
	## 강좌 -> 강의 총시간
	function getMyclassLectureKangListAllTotalTime($lec_no)
	{
		$sql = "SELECT SUM(leck_time) as total_time FROM haksa2080.`lecture_kang` WHERE lecture_no = '". $lec_no ."' AND leck_mode = '2'";
		$query = $this->haksa2080->query($sql);
		return $query->row();
	}
	
	## 유저 수강한 강좌 -> 강의 총시간
	function getMyclassAllTotalTime($member_id, $lmno, $lec_no)
	{
		$sql = "SELECT SUM(user_loading_time) as user_loading_time FROM haksa2080.`lecture_loading_log` WHERE user_id = '". $member_id ."' AND lmno = '". $lmno ."' AND lec_no = '". $lec_no ."'";
		$query = $this->haksa2080->query($sql);
		return $query->row();
	}

	## LecAttendanceInsert checek
	function LecAttendanceInsertCheck($lmno, $seq, $uno) {
    	$sql = "SELECT uno, lmno, seq FROM LecAttendance WHERE lmno = '". $lmno ."' AND seq = '". $seq ."' AND uno = '". $uno ."'";
    	$query = $this->haksa2080->query($sql);
    	return $query->row();
	}
	
	## LecAttendance Insert
	function LecAttendanceInsert($lmno, $seq, $uno, $uid)
	{
		$insertData = array(
			'uno'    		=> $uno
			,'uid'    		=> $uid
			,'lmno'			=> $lmno
			,'seq'			=> $seq
			,'lectype'		=> 'M'
			,'ip'			=> $_SERVER['REMOTE_ADDR']
			,'wdate'		=> date('Y-m-d H:i:s')
		);
		
		return $this->haksa2080->insert('LecAttendance', $insertData);
	}

}