<?php

include('cmn.configs.php');
header("Content-Type: application/json");

$id = "";
$chtvs = array();
$drmurl = ""; $playurl = "";
$streamenvproto = "http";

if(isset($_GET['id']))
{
    $id = $_GET['id'];
}

if(isset($_POST['id']))
{
    $id = $_POST['id'];
}

if(empty($id))
{
    api_res('error', 400, 'Channel ID Required', '');
}

$getikd = @file_get_contents('_chndata');
$imchnd = hideit('decrypt', $getikd);
if(!empty($imchnd))
{
    $ivbz = @json_decode($imchnd, true);
    if(!empty($ivbz))
    {
        foreach($ivbz as $yad)
        {
            if($id == $yad['id'])
            {
                $chtvs = $yad;
            }
        }
    }
}

if($_SERVER['HTTPS'] == "on")
{
    $streamenvproto = "https";
}

if(empty($imchnd))
{
    api_res('error', 400, 'Go Back To Homepage and Try Again', '');
}

if(empty($chtvs))
{
    api_res('error', 400, 'Channel ID Invalid', '');
}

if(!isset($AIRAUTH['access_token']) || empty($AIRAUTH['access_token']))
{
    api_res('error', 401, 'Please Login First To Continue Using This App', '');
    exit();
}

$chn_id = $id;

$playapi = 'https://play.airtel.tv/v4/user/playback?grace=false&contentId='.$chn_id.'&contentStatus=&transactionId=&ln=en%2Chi&cl=ue&ut=PR&lg=en%2Chi&op=AIRTEL&rg=true&cp=asianet%2Ccreator%2Cdevils_circuit%2Cdivo%2Ceditorji%2Ceditorjivod%2Cerosnow%2Chungama%2Ckeyentertainments%2Ckidsflix%2Clionsgateplay%2Cmillenniumvideos%2Cmubi%2Cmwtv%2Cndtv%2Cnodwin%2Cshemaroome%2Cshortstv%2Csillymonks%2Csonyliv%2Csribalajivideo%2Csriganeshvideo%2Csunnxt%2Csunnxt_full%2Ctheqyou%2Cultra%2Cvolgavideos%2Cvoot%2Cyoutube&os=ANDROID&dt=phone&bn=12674&pacp=&pncp=&refresh=true&recInProg=false&currSeg=ATVLITE&layoutExperimentId=DKGg0LiWDBeD3wEay0&chromecast=true&dth=false&isDth=false&appId=MOBILITY';
$playhead[] = 'x-atv-did: '.$AIRDVID;
$playhead[] = 'content-type: application/json';
$playhead[] = 'x-atv-utkn: '.getutkn('GET/v4/user/playback?grace=false&contentId='.$chn_id.'&contentStatus=&transactionId=&ln=en%2Chi&cl=ue&ut=PR&lg=en%2Chi&op=AIRTEL&rg=true&cp=asianet%2Ccreator%2Cdevils_circuit%2Cdivo%2Ceditorji%2Ceditorjivod%2Cerosnow%2Chungama%2Ckeyentertainments%2Ckidsflix%2Clionsgateplay%2Cmillenniumvideos%2Cmubi%2Cmwtv%2Cndtv%2Cnodwin%2Cshemaroome%2Cshortstv%2Csillymonks%2Csonyliv%2Csribalajivideo%2Csriganeshvideo%2Csunnxt%2Csunnxt_full%2Ctheqyou%2Cultra%2Cvolgavideos%2Cvoot%2Cyoutube&os=ANDROID&dt=phone&bn=12674&pacp=&pncp=&refresh=true&recInProg=false&currSeg=ATVLITE&layoutExperimentId=DKGg0LiWDBeD3wEay0&chromecast=true&dth=false&isDth=false&appId=MOBILITY', $AIRAUTH['token'], $AIRAUTH['uid']);
$playhead[] = 'x-atv-customer: 404-11|PREPAID|1|11|5';
$playhead[] = 'x-atv-circle: ue';
$playhead[] = 'x-atv-segment: ATVLITE';
$playhead[] = 'x-atv-stkn: '.$AIRAUTH['access_token'];
$process = curl_init($playapi);
curl_setopt($process, CURLOPT_HTTPHEADER, $playhead);
curl_setopt($process, CURLOPT_HEADER, 0);
curl_setopt($process, CURLOPT_ENCODING, '');
curl_setopt($process, CURLOPT_TIMEOUT, 10);
curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
$resma = curl_exec($process);
curl_close($process);
$data = @json_decode($resma, true);
$pbtype = "";
if(isset($data['success']) && $data['success'] == true)
{
    if(isset($data['drm']['url']))
    {
        $pbtype = "DRM";
    }
    else
    {
        $pbtype = "HLS";
    }
}
else
{
    include('_refreshToken.php');
}

if(!empty($pbtype))
{
    if($pbtype == "DRM")
    {
        $drmurl = $streamenvproto."://".$_SERVER['HTTP_HOST'].str_replace(basename($_SERVER['PHP_SELF']), '', $_SERVER['PHP_SELF']).'wvproxy.php?id='.$chtvs['id'];
        $playurl = $streamenvproto."://".$_SERVER['HTTP_HOST'].str_replace(basename($_SERVER['PHP_SELF']), '', $_SERVER['PHP_SELF']).'manifest.php?id='.$chtvs['id'].'&e=.mpd';
    }
    if($pbtype == "HLS")
    {
        $playurl = $streamenvproto."://".$_SERVER['HTTP_HOST'].str_replace(basename($_SERVER['PHP_SELF']), '', $_SERVER['PHP_SELF']).'master.php?id='.$chtvs['id'].'&e=.m3u8';
    }
    $uoro = array('id' => $chtvs['id'],
                  'title' => $chtvs['title'],
                  'logo' => $chtvs['logo'],
                  'category' => $chtvs['category'],
                  'language' => $chtvs['language'],
                  'playurl' => $playurl,
                  'drmurl' => $drmurl,
                  'type' => $pbtype);
    api_res('success', 200, 'OK', $uoro);
}
else
{
    //Error
    api_res('error', 500, 'No Data Found For This Channel', '');
}

?>