<?php

class M_util extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->hackersjob = $this->load->database("hackersjob", TRUE);
        $this->champ = $this->load->database("hackersjob_champ", TRUE);
     }

    public function send_sms_verify($memberName,$memberPhone,$memberNo,$sendMemo) {
		$HOST   = "10.20.19.108";			        # Mysql Address
		$USER   = "RNSMS_champ";			        # Mysql User Id
		$PASSWD = "haccha12@!";			        # Mysql User Password
		$DBNAME = "RNSMS_champ";			        # Mysql Db
		$connect = mysql_connect("$HOST","$USER","$PASSWD");
		mysql_select_db("$DBNAME", $connect);
		mysql_query("set character_set_results=euckr");
		mysql_query("set names euckr");

		$CurrentTime3 = Time();
		$CurrentDateTime3 = date('H:i', $CurrentTime3);
		$SettleDateTime3 = date('YmdHis', $CurrentTime3);
		$CurrentDate3 = date('Ymd', $CurrentTime3);
		$CurrentDateDateHi3 = date('Y:m:d-H:i', $CurrentTime3);
		$CurrentDateHis3 = date('Y-m-d H:i:s', $CurrentTime3);
		$mktime3 = mktime();

		$query = " INSERT INTO SC_TRAN
					SET
					TR_SENDDATE = now(),
					TR_ID = '$memberNo',
					TR_SENDSTAT = '0',
					TR_MSGTYPE = '0',
					TR_PHONE = '$memberPhone',
					TR_CALLBACK = '025660070',   
					TR_RSLTDATE = now(),
					TR_MODIFIED = now(),
					TR_MSG = '$sendMemo',
					TR_ETC3 = '$memberName',
					TR_ETC4 = 'o'";
		
		$result = mysql_query($query);
		mysql_close($connect);
		return $result;
	}


    public function getCategoryData($code){
        
        $this->hackersjob->select("no, list_name , rank");
        $this->hackersjob->where("code",$code);
        $this->hackersjob->order_by("rank","asc");
        $query = $this->hackersjob->get('rankup_category');

        return $query->result_array();
    }
}