<?php

if(!function_exists('getutkn'))
{
    include('cmn.configs.php');
}

$fcdl = array();
$savime = "";

if(!isset($AIRAUTH['access_token']) || empty($AIRAUTH['access_token']))
{
    add_logs("Login Session Not Available. Please Login To Continue Using This App");
    exit();
}

//RefreshToken
$ref_tok_api = 'https://api.airtel.tv/v2/user/session/refreshAuthToken?appId=MOBILITY';
$ref_tok_post = '{"token": "'.$AIRAUTH['access_token'].'"}';
$ref_tok_hd[] = 'Accept: application/json, text/plain, */*';
$ref_tok_hd[] = 'Content-Type: application/json';
$ref_tok_hd[] = 'Referer: https://www.airtelxstream.in/';
$ref_tok_hd[] = 'x-atv-did: '.$AIRDVID;
$ref_tok_hd[] = 'x-atv-utkn: '.getutkn('POST/v2/user/session/refreshAuthToken?appId=MOBILITY'.$ref_tok_post, $AIRAUTH['token'], $AIRAUTH['uid']);
$ref_tok_hd[] = 'Origin: https://www.airtelxstream.in';
$process = curl_init($ref_tok_api);
curl_setopt($process, CURLOPT_POST, 1);
curl_setopt($process, CURLOPT_POSTFIELDS, $ref_tok_post);
curl_setopt($process, CURLOPT_HTTPHEADER, $ref_tok_hd);
curl_setopt($process, CURLOPT_HEADER, 0);
curl_setopt($process, CURLOPT_ENCODING, '');
curl_setopt($process, CURLOPT_TIMEOUT, 8);
curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
$nimi = curl_exec($process);
curl_close($process);
$mini = @json_decode($nimi, true);

if(isset($mini['uid']) && $mini['token'])
{
    $fcdl = @json_encode(array('uid' => $mini['uid'],
                               'token' => $AIRAUTH['token'],
                               'access_token' => $mini['token']));
    $savime = hideit('encrypt', $fcdl);
}

if(!empty($savime))
{
    if(file_put_contents('_isidata', $savime))
    {
        //Success
        add_logs("Token Refresh Successful");
    }
    else
    {
        //Error
        add_logs("Token Refresh Successful. But Failed To Save File.");
    }
}
else
{
    //Error
    $err_msg = "";
    if(isset($mini['message'])){ $err_msg = $mini['message']; }
    if(isset($mini['msg'])){ $err_msg = $mini['msg']; }
    add_logs("Token Refresh Failed ".$err_msg);
}




?>
