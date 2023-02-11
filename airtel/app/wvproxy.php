<?php

header("Access-Control-Allow-Origin: *");

if(!function_exists('getutkn'))
{
    include('cmn.configs.php');
}

$id = ""; $nvauth = "";
$drmserver = ""; $drmpayload = "";
if(isset($_GET['id']))
{
    $id = $_GET['id'];
}
if(empty($id))
{
    http_response_code(400);
    exit();
}

if($_SERVER['REQUEST_METHOD'] !== "POST")
{
    http_response_code(405);
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
    if(isset($data['drm']['url']))
    {
        $drmserver = $data['drm']['url'];
    }
    if(isset($data['drm']['headers']['nv-authorizations']))
    {
        $nvauth = $data['drm']['headers']['nv-authorizations'];
    }
        
    $drmpayload = @file_get_contents("php://input");
    if(empty($drmpayload))
    {
        http_response_code(400);
        header("X-Error-Type: DRM Payload Missing");
        exit();
    }

    $bsdpayload = '{"challenge":"'.base64_encode($drmpayload).'"}';
    $playhdd[] = 'Accept: application/json';
    $playhdd[] = 'Content-Type: application/json';
    $playhdd[] = 'nv-authorizations: '.$nvauth;
    $playhdd[] = 'Referer: https://www.airtelxstream.in/';
    $playhdd[] = 'Origin: https://www.airtelxstream.in/';
    $process = curl_init($drmserver);
    curl_setopt($process, CURLOPT_POST, 1);
    curl_setopt($process, CURLOPT_POSTFIELDS, $bsdpayload);
    curl_setopt($process, CURLOPT_HTTPHEADER, $playhdd);
    curl_setopt($process, CURLOPT_HEADER, 0);
    curl_setopt($process, CURLOPT_ENCODING, '');
    curl_setopt($process, CURLOPT_TIMEOUT, 10);
    curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
    $drmlicrsl = curl_exec($process);
    curl_close($process);
    $fdrh = json_decode($drmlicrsl, true);
    if(!empty($fdrh))
    {
        if(isset($fdrh['license'][0]))
        {
            header("Content-Type: application/binary");
            print(base64_decode($fdrh['license'][0]));
            exit();
        }
        else
        {
            header("X-Error-Type: Failed To Fetch DRM");
            exit();
        }
    }
    else
    {
        header("Content-Type: application/binary");
        print($drmlicrsl);
        exit();
    }
}
else
{
    include('_refreshToken.php');
}

?>
