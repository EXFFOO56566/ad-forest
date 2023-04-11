<?php
/* Some Thing */
function adforestAPI_basic_auth() {
    
    $pc = $cs = false;
    global $adforestAPI;
    $response = '';
    if (ADFOREST_API_REQUEST_FROM == 'ios') {
        $pcode = ( isset($adforestAPI['appKey_pCode_ios']) && $adforestAPI['appKey_pCode_ios'] != "" ) ? $adforestAPI['appKey_pCode_ios'] : '';
        $ccode = ( isset($adforestAPI['appKey_Scode_ios']) && $adforestAPI['appKey_Scode_ios'] != "" ) ? $adforestAPI['appKey_Scode_ios'] : '';
    } else {
        $pcode = ( isset($adforestAPI['appKey_pCode']) && $adforestAPI['appKey_pCode'] != "" ) ? $adforestAPI['appKey_pCode'] : '';
        $ccode = ( isset($adforestAPI['appKey_Scode']) && $adforestAPI['appKey_Scode'] != "" ) ? $adforestAPI['appKey_Scode'] : '';
    }

    if ($pcode == "" || $ccode == "") {
        return false;
    }

    foreach (adforestAPI_getallheaders() as $name => $value) {
        if ($name == "Authorization" || $name == "authorization") {
            $adminInfo = base64_decode(trim(str_replace("Basic", "", $value)));
        }


        if (($name == "Purchase-Code" || $name == "purchase-code" ) && $pcode == $value) {

            $pc = true;
        }
        if (( $name == "Custom-Security" || $name == "custom-security" ) && $ccode == $value) {
            $cs = true;
        }
    }
    

    return ( $pc == true && $cs == true ) ? true : false;
}

if (!function_exists('getallheaders')) {

    function getallheaders() {
        $headers = array();

        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }

        return $headers;
    }

} 