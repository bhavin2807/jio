<?php

error_reporting(0);

date_default_timezone_set('Asia/Kolkata');

function android_id()
{
    $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
    $oixweu = hash('sha256', str_shuffle($permitted_chars).time().str_shuffle($permitted_chars));
    return substr($oixweu, 0, 16);
}

function getutkn($urlbody, $usertoken, $uid)
{
    $h = "https://crazy-inc.studyeasy.workers.dev/tools/internal/airtel/?action=utkn&body=".base64_encode($urlbody)."&token=".base64_encode($usertoken)."&id=".base64_encode($uid);
    $y = @file_get_contents($h);
    return $y;
}

function getatvauth($urlbody, $usertoken)
{
    $h = "https://crazy-inc.studyeasy.workers.dev/tools/internal/airtel/?action=atvauth&body=".base64_encode($urlbody)."&token=".base64_encode($usertoken);
    $y = @file_get_contents($h);
    return $y;
}

function add_logs($task)
{
    $logs_file = "mylogs";
    $logs_info = date('F d, Y h:i:s A').' - '.$task.' - '.$_SERVER['REMOTE_ADDR'].' - '.$_SERVER['HTTP_USER_AGENT'].PHP_EOL;
    $savify = fopen("$logs_file", "a");
    fwrite($savify, $logs_info);
    fclose($savify);
}

function hideit($action, $data)
{
    $response = "";
    $method = "aes-128-cbc";
    $iv = "lifeisaboutdares";
    $key = "lifeisaboutdares";
    if($action == "encrypt")
    {
        $encrypted = openssl_encrypt($data, $method, $key, OPENSSL_RAW_DATA, $iv);
        if(!empty($encrypted))
        {
            $response = bin2hex($encrypted);
        }
    }
    elseif($action == "decrypt")
    {
        $decrypted = openssl_decrypt(hex2bin($data), $method, $key, OPENSSL_RAW_DATA, $iv);
        if(!empty($decrypted))
        {
            $response = $decrypted;
        }
    }
    else{ }
    return $response;
}

function api_res($status, $code, $msg, $data)
{
    header("Content-Type: application/json");
    if($status == "success" || $status == "error")
    {
        if($status == "error"){ $data = array(); }
        $out = array('status' => $status, 'code' => $code, 'msg' => $msg, 'data' => $data);
        print(json_encode($out));
        exit();
    }
    else
    {
        http_response_code(500);
        exit();
    }
}

//-----------------------------------------------------------------------------//

if(!file_exists('_andrid'))
{
    @file_put_contents('_andrid', android_id());
}

$AIRAUTH = array();
$ANDROID_ID = @file_get_contents('_andrid');
$AIRDVID = $ANDROID_ID.'|phone|android|28|12674|1.36.1';

if(file_exists('_isidata'))
{
    $kair = @file_get_contents('_isidata');
    if(!empty($kair))
    {
        $kbir = hideit('decrypt', $kair);
        if(!empty($kbir))
        {
            $kwire = @json_decode($kbir, true);
            if(!empty($kwire))
            {
                $AIRAUTH = $kwire;
            }
        }
    }
}



?>