<?php

include('cmn.configs.php');
header("Content-Type: application/json");

ini_set('max_execution_time', 0);
set_time_limit(0);

$action = "";
$streamenvproto = "http";

if(isset($_GET['action']))
{
    $action = $_GET['action'];
}

if(empty($action))
{
    api_res('error', 400, 'Action Required', '');
}

if($_SERVER['HTTPS'] == "on")
{
    $streamenvproto = "https";
}

$local_ip = getHostByName(php_uname('n'));
if($_SERVER['SERVER_ADDR'] !== "127.0.0.1"){ $plhoth = $_SERVER['HTTP_HOST']; }else{ $plhoth = $local_ip; }


if($action == "build")
{
    if(file_exists('_chadata'))
    {
        $ndg = hideit('decrypt', @file_get_contents('_chadata'));
        $cvg = @json_decode($ndg, true);
        if(!empty($cvg))
        {
            api_res('success', 200, 'Playlist Build Successful', '');
        }
    }

    //---------------------------------------------//
    $mimi = array();
    $getikd = @file_get_contents('_chndata');
    $imchnd = hideit('decrypt', $getikd);
    if(!empty($imchnd))
    {
    $ivbz = @json_decode($imchnd, true);
    if(!empty($ivbz))
    {
        $mimi = array();
        foreach($ivbz as $rcmb)
        {
            $playhead = array();
            $playhead[] = 'x-atv-did: '.$AIRDVID;
            $playhead[] = 'content-type: application/json';
            $playhead[] = 'x-atv-utkn: '.getutkn('GET/v4/user/playback?grace=false&contentId='.$rcmb['id'].'&contentStatus=&transactionId=&ln=en%2Chi&cl=ue&ut=PR&lg=en%2Chi&op=AIRTEL&rg=true&cp=asianet%2Ccreator%2Cdevils_circuit%2Cdivo%2Ceditorji%2Ceditorjivod%2Cerosnow%2Chungama%2Ckeyentertainments%2Ckidsflix%2Clionsgateplay%2Cmillenniumvideos%2Cmubi%2Cmwtv%2Cndtv%2Cnodwin%2Cshemaroome%2Cshortstv%2Csillymonks%2Csonyliv%2Csribalajivideo%2Csriganeshvideo%2Csunnxt%2Csunnxt_full%2Ctheqyou%2Cultra%2Cvolgavideos%2Cvoot%2Cyoutube&os=ANDROID&dt=phone&bn=12674&pacp=&pncp=&refresh=true&recInProg=false&currSeg=ATVLITE&layoutExperimentId=DKGg0LiWDBeD3wEay0&chromecast=true&dth=false&isDth=false&appId=MOBILITY', $AIRAUTH['token'], $AIRAUTH['uid']);
            $playhead[] = 'x-atv-customer: 404-11|PREPAID|1|11|5';
            $playhead[] = 'x-atv-circle: ue';
            $playhead[] = 'x-atv-segment: ATVLITE';
            $playhead[] = 'x-atv-stkn: '.$AIRAUTH['access_token'];
            $playapi = 'https://play.airtel.tv/v4/user/playback?grace=false&contentId='.$rcmb['id'].'&contentStatus=&transactionId=&ln=en%2Chi&cl=ue&ut=PR&lg=en%2Chi&op=AIRTEL&rg=true&cp=asianet%2Ccreator%2Cdevils_circuit%2Cdivo%2Ceditorji%2Ceditorjivod%2Cerosnow%2Chungama%2Ckeyentertainments%2Ckidsflix%2Clionsgateplay%2Cmillenniumvideos%2Cmubi%2Cmwtv%2Cndtv%2Cnodwin%2Cshemaroome%2Cshortstv%2Csillymonks%2Csonyliv%2Csribalajivideo%2Csriganeshvideo%2Csunnxt%2Csunnxt_full%2Ctheqyou%2Cultra%2Cvolgavideos%2Cvoot%2Cyoutube&os=ANDROID&dt=phone&bn=12674&pacp=&pncp=&refresh=true&recInProg=false&currSeg=ATVLITE&layoutExperimentId=DKGg0LiWDBeD3wEay0&chromecast=true&dth=false&isDth=false&appId=MOBILITY';
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
            $mimi[] = array('id' => $rcmb['id'],
                            'title' => $rcmb['title'],
                            'logo' =>  $rcmb['logo'],
                            'category' =>  $rcmb['category'],
                            'language' =>  $rcmb['language'],
                            'typ' => $pbtype);
        }
    }
    }

    if(!empty($mimi))
    {
        @file_put_contents('_chadata', hideit('encrypt', json_encode($mimi)));
        api_res('success', 200, 'Playlist Build Successful', $items);
    }
    else
    {
        api_res('error', 500, 'Failed To Build Channels Data', $items);
    }
}
elseif($action == "render")
{
    if(file_exists('_chadata'))
    {
        $ndg = hideit('decrypt', @file_get_contents('_chadata'));
        $cvg = @json_decode($ndg, true);
        if(!empty($cvg))
        {
            $inus_data = '#EXTM3U'.PHP_EOL;
            foreach($cvg as $nta)
            {
                $inus_data .= '#EXTINF:-1 tvg-id="'.$nta['id'].'" tvg-name="'.$nta['title'].'" tvg-country="IN" tvg-logo="'.$nta['logo'].'" tvg-chno="'.$nta['id'].'" group-title="'.$nta['category'].' - '.$nta['language'].'",'.$nta['title'].PHP_EOL;

                if($nta['typ'] == "HLS")
                {
                    $inus_data .= $streamenvproto."://".$plhoth.str_replace(basename($_SERVER['PHP_SELF']), '', $_SERVER['PHP_SELF']).'master.php?id='.$nta['id'].'&e=.m3u8'.PHP_EOL;
                }
                else
                {
                    $inus_data .= '#KODIPROP:inputstream=inputstream.adaptive'.PHP_EOL;
                    $inus_data .= '#KODIPROP:inputstreamaddon=inputstream.adaptive'.PHP_EOL;
                    $inus_data .= '#KODIPROP:inputstream.adaptive.manifest_type=mpd'.PHP_EOL;
                    $inus_data .= '#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha'.PHP_EOL;
                    $inus_data .= '#KODIPROP:inputstream.adaptive.license_key='.$streamenvproto."://".$plhoth.str_replace(basename($_SERVER['PHP_SELF']), '', $_SERVER['PHP_SELF']).'wvproxy.php?id='.$nta['id'].PHP_EOL;
                    $inus_data .= $streamenvproto."://".$plhoth.str_replace(basename($_SERVER['PHP_SELF']), '', $_SERVER['PHP_SELF']).'manifest.php?id='.$nta['id'].'&e=.mpd'.PHP_EOL;
                }
            }
        }
        if(isset($inus_data) && !empty($inus_data))
        {
            $playlist_path = 'playmess.m3u';
            if(file_put_contents($playlist_path, $inus_data))
            {
                api_res('success', 200, 'Playlist Generated Successfully', array('playlist_link' => 'http://'.$plhoth.str_replace(" ", "%20", str_replace(basename($_SERVER['PHP_SELF']), '', $_SERVER['PHP_SELF']).$playlist_path.'?v='.time())));
            }
            else
            {
                api_res('error', 500, 'Failed To Generate Playlist. File Permission Issues', '');
            }
        }
        else
        {
            api_res('error', 500, 'Please Build Playlist Again.', '');
        }
    }
    else
    {
        api_res('error', 500, 'Please Build Playlist First and Then Render It.', '');
    }
}
else
{
    api_res('error', 401, 'Unauthenticated Access', '');
}
?>