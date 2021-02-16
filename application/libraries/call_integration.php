<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/application/libraries/hackers_crypt.php';

class CallApi
{
    public function exec($method, $action, $contents)
    {
        $_config = array(
            'APP_LEGACY'	=>3080,
            'APP_ENV_CHAR'	=>APP_ENV_CHAR,
            'APP_TOKEN'		=>'Api@Token.Hackers',
            'APP_KEY'		=>'Api@Key.Hackers',
            'CRYPT_KEY'		=>'Api@Crypt.hackers',
            'APP_DEVICE'		=>'Mobile'
        );

        $hdFlag = time();
        $appLegacy = $_config['APP_LEGACY'] . '|' . $hdFlag;
        $appToken = $_config['APP_TOKEN'] . '|' . $hdFlag;
        $appKey = $_config['APP_KEY'] . '|' . $hdFlag;
        $appIp = $_SERVER['REMOTE_ADDR'] . '|' . $hdFlag;
        $appDevice = $_config['APP_DEVICE'] . '|' . $hdFlag;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'serviceId: ' . Crypt::encrypt($appLegacy, $_config['CRYPT_KEY']),
            'token: ' . Crypt::encrypt($appToken, $_config['CRYPT_KEY']),
            'apiKey: ' . Crypt::encrypt($appKey, $_config['CRYPT_KEY']),
            'appIp: ' . Crypt::encrypt($appIp, $_config['CRYPT_KEY']),
            'appDevice: ' . Crypt::encrypt($appDevice, $_config['CRYPT_KEY']),
            'hdFlag: ' . $hdFlag,
            'Cookie: ' . http_build_query($_COOKIE, '', ';'),
        ));

        if($method == "POST") {
            $url = 'http://' . $_config['APP_ENV_CHAR'] . 'api.hackers.com/v1/member/api';

            curl_setopt($ch, CURLOPT_URL, $url);

            $curl_post_data = array(
                'action'	=> $action,
                'contents'	=> $contents,
            );

            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $curl_post_data);
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        $json = curl_exec($ch);
        $arr_return = array('json' => $json, 'curl_errno' => curl_errno($ch), 'curl_http_code' => curl_getinfo($ch, CURLINFO_HTTP_CODE), 'msg' => curl_error($ch));
        curl_close($ch);

        return $arr_return;
    }
}

?>