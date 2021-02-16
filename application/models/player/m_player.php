<?php

class M_player extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->haksa2080 = $this->load->database("haksa2080", TRUE);
        $this->user_id = $_SESSION['member_id'];
        $this->user_name = $_SESSION['member_name'];
        $this->user_no = $_SESSION['member_no'];
    }

    /**
     * player return�� üũ
     *
     * @param $__set_array
     */
    public function insert_test($__set_array)
    {
        $this->haksa2080->insert("admin_sql_log",$__set_array);
    }

    /**
     * ���������н� üũ
     *
     * @param $lmno
     * @return mixed
     */
    public function checkChampFreepass($lmno)
    {
        $this->haksa2080->select("discount_div");
        $this->haksa2080->from("lecture_mem");
        $this->haksa2080->where("no",$lmno);
        $query = $this->haksa2080->get();
        $result_data = $query->row_array();

        return $result_data['discount_div'];
    }

    /**
     * ���������н� device_id ����
     *
     * @param $user_id
     * @param $d_id
     * @param $d_model
     * @param $idx
     * @param $lesson
     * @param $seq
     */
    public function champDeviceInsertAPI($user_id,$d_id,$d_model,$idx,$lesson,$seq)
    {
        //Parameter
        $site_mem_id = $user_id;
        $site_code = 'JR';//�������ΰ�:GI, �����������ΰ�:PI, �����߰����ΰ�:LI, ���ð�����:HI, ����:FN, �ӿ��ΰ�:TE, �߱���:HC, ����ΰ� : JI

        //������ ������ ����.
        $post_data = http_build_query(
            array(
                'm' => 'api',
                'a' => 'evt_freepass_deviceid_insert',
                'site_mem_id' => $site_mem_id,
                'site_code' => $site_code,
                'd_id' => $d_id,
                'd_model' => $d_model,
                'idx' => $idx,
                'lesson' => $lesson,
                'seq' => $seq
            )
        );

        $opt = array('http' =>
            array(
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => $post_data
            )
        );

        $context = stream_context_create($opt);

        //����URL
        $target_url = "https://champ.hackers.com";
        $result = file_get_contents($target_url, false, $context);

        $arr_data = json_decode($result,true);

        $sql = "INSERT INTO admin_sql_log 
                SET 
	            menu = '{$arr_data['msg']}',
	            intra_id = '{$d_id}',
	            intra_name = '{$arr_data[result]}',
	            run_sql = '{$idx}'";
        $this->haksa2080->query($sql);
    }

    ## lecture_loadgin_log INSERT
    function check_player_log($user_id, $lmno, $lec_code, $ls_kind, $lec_num, $speed_rate, $lec_type)
    {
    	## lecture_loadging_log Insert Ȯ��
        $sql = "
				SELECT `no`, user_loading_time FROM lecture_loading_log
                WHERE user_id = '". $user_id ."' AND lec_no = '". $lec_code ."' AND lmno = '". $lmno ."' AND lec_num = '". $lec_num ."' AND lec_type = '". $lec_type ."'
                ";
        
        $query = $this->haksa2080->query($sql);
        $lecLoadingData = $query->row();

        if (empty($lecLoadingData)) {
			## ���� -> ���� �ѽð� Ȯ��
			$lecsql = "SELECT * FROM lecture_kang WHERE lecture_no = '". $lec_code ."' AND leck_kang_name = '". $lec_num ."'";
			$query = $this->haksa2080->query($lecsql);
			$lecKangInfo = $query->row();
			$lecKangTime = ((int)$lecKangInfo->leck_time * 60);
			
			$insertData = array(
				'user_id' => $user_id
				,'lmno' => $lmno
				,'lec_no' => $lec_code
				,'lec_num' => $lec_num
				,'leck_time' => $lecKangTime
				,'lec_type' => $lec_type
				,'user_agent' => $_SERVER['HTTP_USER_AGENT']
				,'REMOTE_ADDR' => $_SERVER['XFFCLIENTIP']
				,'user_loading_time' => 0
				,'edate' => date('Y-m-d H:i:s')
				,'wdate' => date('Y-m-d H:i:s')
			);
			$this->haksa2080->insert('lecture_loading_log', $insertData);
			$loading_insert_id = $this->haksa2080->insert_id();
			
//            $sql_reselect = "
//							SELECT `no`, user_loading_time FROM lecture_loading_log
//							WHERE user_id = '". $user_id ."' AND lec_num = '". $lec_num ."' AND lec_type = '". $lec_type ."'
//                             ";
//
//            $query = $this->haksa2080->query($sql_reselect);
//            $data_reslect = $query->row_array();

            $loading_time_no =  $loading_insert_id;
			// $loading_time_no = $data_reslect["no"];

        } else {
            $loading_time = $lecLoadingData->user_loading_time;
            $loading_time_no =  $lecLoadingData->no;
        }
        
        $return_value['original_loading_time'] = $lecLoadingData->user_loading_time;
        $return_value['loading_time'] = $loading_time;
        $return_value['loading_time_no'] = $loading_time_no;

        return $return_value;
    }

}