<?php

class m_banner extends CI_Model
{
    private $hackersjob = null;

    function __construct(){
        parent::__construct();
		$this->haksa2080 = $this->load->database("haksa2080", TRUE);
    }
	
	## 메인 롤링배너
    function getMainBanner_list ($type)
	{
		$this->haksa2080->select("imgBanner, enddate, URL_1, target");
		$this->haksa2080->where('type' , $type);
		$this->haksa2080->where('used' , '1');
		$this->haksa2080->where("(enddate is NULL or enddate >= NOW())", null, false);
		
		$this->haksa2080->order_by("main_dis_num", "DESC");
		$query = $this->haksa2080->get('banner_2019');
		
		return $query->result_array();
	}
	
	## 메인 롤링배너
	function getMainSubBanner($type)
	{
		$sql = "
		SELECT imgBanner, enddate, URL_1, target
		FROM banner_2019 WHERE type = '". $type ."' AND used = '1' AND (enddate IS NULL or enddate >= NOW())
		ORDER BY RAND() LIMIT 0, 1
		";
		
		$query = $this->haksa2080->query($sql);
		return $query->row();
	}
    
    // 배너 기수리스트
//	function period_list($period_key=''){
//		$this->champ->select("*");
//		if(!empty($period_key)) {
//			$this->champ->where("e_key", $period_key);
//		}
//		$this->champ->where("del_yn", "Y");
//		$this->champ->order_by("evt_no");
//		//$this->champ->limit(1);
//		$query = $this->champ->get('event_period');
//		$period_lists = $query->result_array();
//
//		foreach ($period_lists as $key => $rs){
//			$result[$rs['e_key']] = $rs;
//		}
//
//		return $result;
//    }
    
    //배너 리스트
    function banner_lists($position){
        $this->hackersjob->select("edate, VIEW, address, target, attached, banner_text, content, type, period_key, position, banner_alt");
        $this->hackersjob->where("position",$position);
        $this->hackersjob->where("(sdate is NULL or sdate<=now())",null,false);
        $this->hackersjob->where("(edate is NULL or edate>=now())",null,false);
        $this->hackersjob->where("view",'yes');
        $this->hackersjob->order_by("position, rank, bind");
        $query = $this->hackersjob->get('rankup_banner');

        return $query->result_array();
    }

    //배너 리스트 랜덤
//    function banner_lists_rand($position,$limit)
//    {
//        $this->hackersjob->select("edate, VIEW, address, target, attached, banner_text, position");
//        $this->hackersjob->where("position",$position);
//        $this->hackersjob->where("view",'yes');
//        $this->hackersjob->order_by("RAND()");
//        $this->hackersjob->limit($limit,0);
//        $query = $this->hackersjob->get('rankup_banner');
//
//        return $query->row_array();
//    }

}