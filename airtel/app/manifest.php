<?php

header("Access-Control-Allow-Origin: *");
if(!function_exists('getutkn'))
{
    include('cmn.configs.php');
}

$id = "";
if(isset($_GET['id']))
{
    $id = $_GET['id'];
}
if(empty($id))
{
    http_response_code(400);
    exit();
}

$playapi = 'https://play.airtel.tv/v4/user/playback?grace=false&contentId='.$id.'&contentStatus=&transactionId=&ln=en%2Chi&cl=ue&ut=PR&lg=en%2Chi&op=AIRTEL&rg=true&cp=asianet%2Ccreator%2Cdevils_circuit%2Cdivo%2Ceditorji%2Ceditorjivod%2Cerosnow%2Chungama%2Ckeyentertainments%2Ckidsflix%2Clionsgateplay%2Cmillenniumvideos%2Cmubi%2Cmwtv%2Cndtv%2Cnodwin%2Cshemaroome%2Cshortstv%2Csillymonks%2Csonyliv%2Csribalajivideo%2Csriganeshvideo%2Csunnxt%2Csunnxt_full%2Ctheqyou%2Cultra%2Cvolgavideos%2Cvoot%2Cyoutube&os=ANDROID&dt=phone&bn=12674&pacp=&pncp=&refresh=true&recInProg=false&currSeg=ATVLITE&layoutExperimentId=DKGg0LiWDBeD3wEay0&chromecast=true&dth=false&isDth=false&appId=MOBILITY';
$playhead[] = 'x-atv-did: '.$AIRDVID;
$playhead[] = 'content-type: application/json';
$playhead[] = 'x-atv-utkn: '.getutkn('GET/v4/user/playback?grace=false&contentId='.$id.'&contentStatus=&transactionId=&ln=en%2Chi&cl=ue&ut=PR&lg=en%2Chi&op=AIRTEL&rg=true&cp=asianet%2Ccreator%2Cdevils_circuit%2Cdivo%2Ceditorji%2Ceditorjivod%2Cerosnow%2Chungama%2Ckeyentertainments%2Ckidsflix%2Clionsgateplay%2Cmillenniumvideos%2Cmubi%2Cmwtv%2Cndtv%2Cnodwin%2Cshemaroome%2Cshortstv%2Csillymonks%2Csonyliv%2Csribalajivideo%2Csriganeshvideo%2Csunnxt%2Csunnxt_full%2Ctheqyou%2Cultra%2Cvolgavideos%2Cvoot%2Cyoutube&os=ANDROID&dt=phone&bn=12674&pacp=&pncp=&refresh=true&recInProg=false&currSeg=ATVLITE&layoutExperimentId=DKGg0LiWDBeD3wEay0&chromecast=true&dth=false&isDth=false&appId=MOBILITY', $AIRAUTH['token'], $AIRAUTH['uid']);
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
$return = curl_exec($process);
curl_close($process);
$data = json_decode($return, true);

if(isset($data['success']) && $data['success'] == true)
{
    $playbackurl = $data['playback']['playUrl'];
    $cookies = $data['playback']['headers']['Cookie'];
    $ucookies = str_replace(';', '&amp;', $data['playback']['headers']['Cookie']);
    $ucookies = str_replace('CloudFront-Policy', 'Policy', $ucookies);
    $ucookies = str_replace('CloudFront-Key-Pair-Id', 'Key-Pair-Id', $ucookies);
    $ucookies = str_replace('CloudFront-Signature', 'Signature', $ucookies);
    $playhdd[] = 'Cookie: '.$cookies;
    $process = curl_init($playbackurl);
    curl_setopt($process, CURLOPT_HTTPHEADER, $playhdd);
    curl_setopt($process, CURLOPT_HEADER, 0);
    curl_setopt($process, CURLOPT_ENCODING, '');
    curl_setopt($process, CURLOPT_TIMEOUT, 10);
    curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
    $playdata = curl_exec($process);
    curl_close($process);
    if(!empty($playdata))
    {
        header("Content-Type: application/dash+xml");
        $fix1 = str_replace('.m4s', '.m4s?'.$ucookies, $playdata);
        $fix2 = str_replace('</ProgramInformation>', '</ProgramInformation><BaseURL>'.str_replace(basename($playbackurl), '', $playbackurl).'</BaseURL>', $fix1);
        print(trim($fix2));
        exit();
    }
    else
    {
        include('_refreshToken.php');
    }
}
else
{
    include('_refreshToken.php');
}

?>