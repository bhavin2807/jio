<?php

header("Access-Control-Allow-Origin: *");
if(!function_exists('getutkn'))
{
    include('cmn.configs.php');
}

$b = "";
$hash = "";
$q = "";
$policy = "";
$keypairid = "";
$signature = "";
$cloudfrontauth = "";
$playbackurl = "";

if(isset($_GET['b']))
{
    $basehost = base64_decode($_GET['b']);
}

if(isset($_GET['bh']))
{
    $basehash = $_GET['bh'];
}

if(isset($_GET['q']))
{
    $q = $_GET['q'];
}

if(isset($_GET['Policy']))
{
    $policy = $_GET['Policy'];
}

if(isset($_GET['Key-Pair-Id']))
{
    $keypairid = $_GET['Key-Pair-Id'];
}

if(isset($_GET['Signature']))
{
    $signature = $_GET['Signature'];
}

if(!empty($policy) && !empty($keypairid) && !empty($signature))
{
    $cloudfrontauth = 'Policy='.$policy.'&Key-Pair-Id='.$keypairid.'&Signature='.$signature;
}

if(!empty($cloudfrontauth))
{
    $playbackurl = 'https://'.$basehost.$q.'?'.$cloudfrontauth;
}

$realbasehash = md5('goajoapoa'.$basehost);

if(empty($cloudfrontauth))
{
    header("X-Error-Type: Exception 1");
    exit();
}

if(empty($playbackurl))
{
    header("X-Error-Type: Exception 2");
    exit();
}

if(trim($basehash) !== trim($realbasehash))
{
    header("X-Error-Type: Exception 3");
    exit();
}

$playhdd[] = 'User-Agent: ExoPlayer Demo (Android 6)';
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
    $baseurl = 'https://'.$basehost;
    $fix1 = str_replace('.ts', '.ts?'.$cloudfrontauth, $playdata);
    $fix2 = str_replace('media-', $baseurl.'media-', $fix1);
    if(stripos($fix2, '#EXTM3U') !== false)
    {
        header("Content-Type: application/vnd.apple.mpegurl");
        print($fix2);
        exit();
    }
    else
    {
        header("X-Error-Type: Exception 4");
        exit();
    }
}

?>
