<?php

header("Access-Control-Allow-Origin: *");
if(!function_exists('getutkn'))
{
    include('cmn.configs.php');
}

$id = "";
$cookies = "";
$playbackurl = "";
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
$data = @json_decode($return, true);

if(isset($data['success']) && $data['success'] == true)
{
    if(isset($data['playback']['playUrl']))
    {
        $playbackurl = $data['playback']['playUrl'];
        if(stripos($playbackurl, 'airtelxstream.in') == false)
        {
            header("Location: $playbackurl");
            exit();
        }
    }
    if(isset($data['playback']['headers']['Cookie']))
    {
        $cookies = $data['playback']['headers']['Cookie'];
    } 
        
    $ucookies = str_replace(';', '&', $data['playback']['headers']['Cookie']);
    $ucookies = str_replace('CloudFront-Policy', 'Policy', $ucookies);
    $ucookies = str_replace('CloudFront-Key-Pair-Id', 'Key-Pair-Id', $ucookies);
    $ucookies = str_replace('CloudFront-Signature', 'Signature', $ucookies);

    if(!empty($playbackurl))
    {
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
            $baseurl = str_replace('https://', '', str_replace(basename($playbackurl), '', $playbackurl));
            $basehash = md5('goajoapoa'.$baseurl);
            $fix1 = str_replace('.m3u8', '.m3u8&'.$ucookies, $playdata);
            $newmark = 'playlist.php?e=.m3u8&b='.base64_encode($baseurl).'&bh='.$basehash.'&q=chunklist_';
            $fix2 = str_replace('chunklist_', $newmark, $fix1);
            if(stripos($fix2, '#EXTM3U') !== false)
            {
                header("Content-Type: application/vnd.apple.mpegurl");
                print($fix2); exit();
            }
            else
            {
                header("X-Error-Type: Invalid Playlist Recieved");
                http_response_code(500);
                exit();
            }
        }
        else
        {
            include('_refreshToken.php');
            header("X-Error-Type: Failed To Fetch Playlist");
            exit();
        }
    }
    else
    {
        include('_refreshToken.php');
        exit();
    }
}
else
{
    include('_refreshToken.php');
}

?>
