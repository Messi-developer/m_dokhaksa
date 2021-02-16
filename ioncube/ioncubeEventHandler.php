<?php
/**
 * ioncube 에러 처리 함수.(*함수 이름이 바뀔경우 호출이 안됨으로 변경하지 말것.)
 * @param $err_code
 * @param $params
 */
function ioncube_event_handler($err_code, $params)
{
    echo "<html><body>ionCube error has occurred: ".$params['current_file']."<br><br>";

    switch ($err_code){
        case ION_CORRUPT_FILE:
            echo "corrupt file!";
            break;
        case ION_EXPIRED_FILE:
            echo "expired-file.";
            break;
        case ION_NO_PERMISSIONS:
            echo "no-permissions";
            break;
        case ION_CLOCK_SKEW:
            echo "clock-skew";
            break;
        case ION_LICENSE_NOT_FOUND:
            echo "A license could not be found.";
            break;
        case ION_LICENSE_CORRUPT:
            echo "license-corrupt";
            break;
        case ION_LICENSE_EXPIRED:
            echo "license expired";
            break;
        case ION_LICENSE_PROPERTY_INVALID:
            echo "license-property-invalid";
            break;
        case ION_LICENSE_HEADER_INVALID:
            echo "license-header-invalid";
            break;
        case ION_LICENSE_SERVER_INVALID:
            echo "license-server-invalid";
            break;
        case ION_UNAUTH_INCLUDING_FILE:
            echo "unauth-including-file";
            break;
        case ION_UNAUTH_INCLUDED_FILE:
            echo "unauth-included-file";
            break;
        case ION_UNAUTH_APPEND_PREPEND_FILE:
            echo "unauth-append-prepend-file";
            break;
        default:
            echo "An unknown error occurred. by ionCube";
            break;
    }//end of switch.

    echo "</body></html>";
    exit;
}//end of ioncube_event_handler().
