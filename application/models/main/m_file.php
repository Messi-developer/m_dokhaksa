<?php
class M_file extends CI_Model
{

    private $file;
    private $uploadDir;
    private $addInfo;
	private $fileInfo;
    private $uploadPath;
    private $result = array();

    public function __construct()
    {
        parent::__construct();

        $this->result['status'] = true;
        $this->result['msg']    = "파일을 업로드할 수 없습니다.";
        $this->uploadPath       = $_SERVER['DOCUMENT_ROOT'];

        $this->addInfo['max_file_size'] = 1024 * 1024 * 25; // 25MB
        $this->addInfo['allow_ext']     = array("gif", "jpg", "png", "zip", "ppt", "docx", "doc", "hwp", "mp3", "mp4", "wmv", "pptx", "pdf", "mov");

		$this->fileInfo['allow_name_type'] = array(
            "hqx"   => "application/mac-binhex40",
            "cpt"	=> "application/mac-compactpro",
            "bin"	=> "application/macbinary",
            "dms"	=> "application/octet-stream",
            "lha"	=> "application/octet-stream",
            "lzh"	=> "application/octet-stream",
            "exe"	=> "application/octet-stream",
            "class"	=> "application/octet-stream",
            "psd"	=> "application/x-photoshop",
            "so"	=> "application/octet-stream",
            "sea"	=> "application/octet-stream",
            "dll" 	=> "application/octet-stream",
            "oda"	=> "application/oda",
            "ai"	=> "application/postscript",
            "eps"	=> "application/postscript",
            "ps"	=> "application/postscript",
            "smi"	=> "application/smil",
            "smil"	=> "application/smil",
            "mif"	=> "application/vnd.mif",
            "wbxml" => "application/wbxml",
            "wmlc"	=> "application/wmlc",
            "dcr"	=> "application/x-director",
            "dir"	=> "application/x-director",
            "dxr"	=> "application/x-director",
            "dvi"	=> "application/x-dvi",
            "gtar"	=> "application/x-gtar",
            "gz"	=> "application/x-gzip",
            "php"	=> "application/x-httpd-php",
            "php4"	=> "application/x-httpd-php",
            "php3"	=> "application/x-httpd-php",
            "phtml" => "application/x-httpd-php",
            "phps"	=> "application/x-httpd-php-source",
            "js"	=> "application/x-javascript",
            "swf"	=> "application/x-shockwave-flash",
            "sit"	=> "application/x-stuffit",
            "tar"	=> "application/x-tar",
            "tgz"	=> "application/x-tar",
            "xhtml" => "application/xhtml+xml",
            "xht"	=> "application/xhtml+xml",
            "mid"	=> "audio/midi",
            "midi"	=> "audio/midi",
            "mpga"	=> "audio/mpeg",
            "mp2"	=> "audio/mpeg",
            "aif"	=> "audio/x-aiff",
            "aiff"	=> "audio/x-aiff",
            "aifc"	=> "audio/x-aiff",
            "ram"	=> "audio/x-pn-realaudio",
            "rm"	=> "audio/x-pn-realaudio",
            "rpm"	=> "audio/x-pn-realaudio-plugin",
            "ra"	=> "audio/x-realaudio",
            "rv"	=> "video/vnd.rn-realvideo",
            "wav"	=> "audio/x-wav",
            "bmp"	=> "image/bmp",
            "tiff"	=> "image/tiff",
            "tif"	=> "image/tiff",
            "css"	=> "text/css",
            "html"	=> "text/html",
            "htm"	=> "text/html",
            "shtml" => "text/html",
            "txt"	=> "text/plain",
            "text"	=> "text/plain",
            "rtx"	=> "text/richtext",
            "rtf"	=> "text/rtf",
            "xml"	=> "text/xml",
            "xsl"	=> "text/xml",
            "mpeg"	=> "video/mpeg",
            "mpg"	=> "video/mpeg",
            "mpe"	=> "video/mpeg",
            "qt"	=> "video/quicktime",
            "mov"	=> "video/quicktime",
            "avi"	=> "video/x-msvideo",
            "movie" => "video/x-sgi-movie",
            "doc"	=> "application/msword",
            "docx"	=> "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
            "xlsx"	=> "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            "xl"	=> "application/excel",
            "eml"	=> "message/rfc822",
            "hwp"	=> "application/haansofthwp",
            "flv"	=> "application/octet-stream",
            "pptx"	=> "application/vnd.openxmlformats-officedocument.presentationml.presentation",
            "csv"	=> array("text/x-comma-separated-values", "text/comma-separated-values", "application/octet-stream", "application/vnd.ms-excel", "text/csv", "application/csv", "application/excel", "application/vnd.msexcel"),
            "xls"	=> array("application/excel", "application/vnd.ms-excel", "application/msexcel"),
            "ppt"	=> array("application/powerpoint", "application/vnd.ms-powerpoint"),
            "zip"	=> array("application/x-zip", "application/zip", "application/x-zip-compressed"),
            "mp3"	=> array("audio/mpeg", "audio/mpg"),
            "log"	=> array("text/plain", "text/x-log"),
            "word"	=> array("application/msword", "application/octet-stream"),
            "gif"	=> array("image/gif", "application/octet-stream"),
            "jpeg"	=> array("image/jpeg", "image/pjpeg", "application/octet-stream"),
            "jpg"	=> array("image/jpeg", "image/pjpeg", "application/octet-stream"),
            "jpe"	=> array("image/jpeg", "image/pjpeg", "application/octet-stream"),
            "png"	=> array("image/png",  "image/x-png", "application/octet-stream"),
            "pdf"	=> array("application/pdf", "application/x-download", "application/download"),
            "asx"	=> array("video/x-ms-asf")
        );
    }
    
    // 파일 업로드
    // CDN 및 ftp 파일 업로드 추가 개발 필요
    public function fileUpload($file, $uploadDir, $params = array())
    {
		$max_file_size = 1024 * 1024 * 50; // 50MB
		if ($file['size'] >= $max_file_size) {
			$this->result['status'] = false;
			$this->result['msg'] = '50MB 까지만 업로드가 가능합니다.';
			return $this->result;
		}
    	
        if (!$file || !$uploadDir) {
            $this->result['status'] = false;
            return $this->result;
        }

        $this->file     = $file;
        $this->uploadDir= $uploadDir;

        if ($params) {
            foreach ($params as $key => $item) $this->addInfo[$key] = $item;
        }

        $result = $this->chkMimeType();
        if ($result !== true) {
            $this->result['status'] = false;
            $this->result['msg']    = $result;
            return $this->result;
        }

        $result = $this->chkFileVolume();
        if ($result !== true) {
            $this->result['status'] = false;
            $this->result['msg']    = $result;
            return $this->result;
        }

//        $result = $this->checkXssClean();
//        if ($result !== true) {
//            $this->result['status'] = false;
//            $this->result['msg']    = $result;
//            return $this->result;
//        }

        $result = $this->chkFileSize();
        if ($result !== true) {
            $this->result['status'] = false;
            $this->result['msg']    = $result;
            return $this->result;
        }

        $result = $this->upload();
        if ($result === false) {
            $this->result['status'] = false;
            return $this->result;
        }

        $this->result['file_name'] = $result;
        return $this->result;
    }

    // 파일 확장자 확인
    private function chkMimeType()
    {
        if (is_array($this->addInfo['allow_ext']))
        {
            foreach ($this->addInfo['allow_ext'] as $i => $allowExt)
            {
                if (is_array($this->fileInfo['allow_name_type'][$allowExt])) {
                    if (in_array($this->file['type'], $this->fileInfo['allow_name_type'][$allowExt])) return true;
                }
                else if ($this->file['type'] == $this->fileInfo['allow_name_type'][$allowExt]) {
                    return true;
                }
            }

            return "업로드 할 수 없는 확장자 입니다.";
        }
        else
        {
            if (is_array($this->fileInfo['allow_name_type'][$this->addInfo['allow_ext']])) {
                return (!in_array($this->file['type'], $this->fileInfo['allow_name_type'][$this->addInfo['allow_ext']])) ? "업로드 할 수 없는 확장자 입니다." : true;
            }
            else if ($this->file['type'] != $this->fileInfo['allow_name_type'][$this->addInfo['allow_ext']]) {
                return "업로드 할 수 없는 확장자 입니다.";
            }

            return true;
        }
    }

    // 파일 용량 확인
    private function chkFileVolume()
    {
        if ($this->file['size'] <= 0 || $this->file['error'] > 0) return "업로드 할 수 없는 파일입니다. 새로고침 후 다시 시도해 주세요.";

        if ($this->addInfo['max_file_size'] && $this->file['size'] > $this->addInfo['max_file_size']) {
            if ($this->addInfo['max_file_size'] < 1024) return $this->addInfo['max_file_size'] . "byte 이상의 파일은 업로드할 수 없습니다.";
            else if ($this->addInfo['max_file_size'] < 1024 * 1024) return ($this->addInfo['max_file_size']/1024) . "KB 이상의 파일은 업로드할 수 없습니다.";
            else if ($this->addInfo['max_file_size'] < 1024 * 1024 * 1024) return ($this->addInfo['max_file_size']/(1024*1024)) . "MB 이상의 파일은 업로드할 수 없습니다.";
        }

        return true;
    }

    // 파일 크기 확인
    private function chkFileSize()
    {
        if ($this->addInfo['width'] || $this->addInfo['height'])
        {
            $fileData = @getimagesize($this->file['tmp_name']);
            if ($fileData !== false) {
                if ($fileData[0] != $this->addInfo['width'] || $fileData[1] != $this->addInfo['height']) return "너비: " . $this->addInfo['width'] . "px, 높이: " . $this->addInfo['height'] . "px 이 아닌 이미지는 업로드할 수 없습니다.";
            }
        }

        return true;
    }

    private function checkXssClean()
    {
        $chkFileName = $this->file['tmp_name'];
        if (@getimagesize($chkFileName) !== false)
        {
            if (($chkFileName= @fopen($chkFileName, "rb")) === false) return "정상적인 파일이 아닙니다.1";

            $openBytes = @fread($chkFileName, 256);
            @fclose($chkFileName);

            if (preg_match("/<(a|body|head|html|img|plaintext|pre|script|table|title)[\s>]/i", $openBytes)) return "정상적인 파일이 아닙니다.";
        }

        return true;
    }

    private function uniqueTimeStamp() {
        list($msec, $sec) = explode(" ", microtime());
        $msec = explode(".", $msec);
        return $sec.substr(array_pop($msec), 0, 4);
    }

    private function getExt() {
        $nx = explode(".", $this->file['name']);
        return $nx[count($nx)-1];
    }

    // 파일 업로드
    private function upload()
    {
        $prefixName = ($this->addInfo['prefix']) ? $this->addInfo['prefix'] . "_" : "";
        $fileName   = ($this->addInfo['file_name']) ? $this->addInfo['file_name'] : $prefixName . md5($this->uniqueTimeStamp()) . "." . $this->getExt();
        $chkDirArr  = explode("/", $this->uploadDir);
        $chkDir     = "";

        $uploadDir  = $this->uploadPath . $this->uploadDir;

        foreach ($chkDirArr as $i => $dir)
        {
            $chkDir .= ($i > 0) ? "/" . $dir : $dir;
            if (!is_dir($this->uploadPath . $chkDir)) {
                $mkdirResult = mkdir($this->uploadPath . $chkDir, 0777, true);
                if (!$mkdirResult) return false;

                @chmod($this->uploadPath . $chkDir, 0777);
            }
        }

        if (!is_file($uploadDir . "/" . $fileName)) {
            $uploadResult = move_uploaded_file($this->file['tmp_name'], $uploadDir . "/" . $fileName);
            if (!$uploadResult) return false;
        }

        return $fileName;
    }
    
    ## 파일다운로드
	function re_force_download($filename) {
		$data = @file_get_contents($filename);
		
		if($data) {
			$basename = basename($filename);
			
			$expires = gmdate("D, d M Y H:i:s", mktime(
				date("H")+2, date("i"), date("s"), date("m"), date("d"), date("Y")
			));
			
			header("Content-Type: application-x/force-download");
			header("Content-Disposition: attachment; filename=$basename");
			header("Content-length: " . strlen($data));
			header("Expires: " . $expires . " GMT");
			header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
			
			if(false === strpos($_SERVER["HTTP_USER_AGENT"], 'MSIE ')) {
				header("Cache-Control: no-cache, must-revalidate");
			}
			header("Pragma: no-cache");
			
			flush();
			ob_start();
			
			echo $data;
		} else {
			die($filename . " 파일 열기에 실패하였습니다.");
		}
	}
}


?>