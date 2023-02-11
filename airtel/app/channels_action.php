<?php

if(!function_exists('getutkn'))
{
    include('cmn.configs.php');
}

$tvresult = array();
$q = ""; $action = "";
$allChannels = array();
$category = $language = "";

if(isset($_GET['action']))
{
    $action = $_GET['action'];
}
if(isset($_GET['q']))
{
    $q = $_GET['q'];
}
if(isset($_GET['c']))
{
    $c = $_GET['c'];
    $category = $_GET['c'];
}
if(isset($_GET['l']))
{
    $l = $_GET['l'];
    $language = $_GET['l'];
}

$getikd = @file_get_contents('_chndata');
$imchnd = hideit('decrypt', $getikd);
if(!empty($imchnd))
{
    $allChannels = @json_decode($imchnd, true);
    if(empty($allChannels))
    {
        api_res('error', 401, 'Please Login First and Generate Channel List', '');
    }
}

if($action == "search")
{
    if(empty($q))
    {
        api_res('error', 400, 'Please Enter Something To Search', '');
    }
    foreach($allChannels as $acLNs)
    {
        if(stripos($acLNs['title'], $q) !== false)
        {
            $tvresult[] = $acLNs;
        }
    }
    if(!empty($tvresult))
    {
        api_res('success', 200, 'OK', $tvresult);
    }
    else
    {
        api_res('error', 404, 'Nothing Found', '');
    }
}
elseif($action == "sort")
{
    if(empty($c) && empty($l))
    {
        //Category Is Empty
        //Language Is Empty
        $tvresult = $allChannels;
    }
    if(!empty($c) && empty($l))
    {
        //Category Is Not Empty
        //Language Is Empty
        foreach($allChannels as $acLNs)
        {
            if($c == $acLNs['category'])
            {
                $tvresult[] = $acLNs;
            }
        }
    }
    if(empty($c) && !empty($l))
    {
        //Category Is Empty
        //Language Is Not Empty
        foreach($allChannels as $acLNs)
        {
            if($l == $acLNs['language'])
            {
                $tvresult[] = $acLNs;
            }
        }
    }
    if(!empty($c) && !empty($l))
    {
        //Category Is Not Empty
        //Language Is Not Empty
        foreach($allChannels as $acLNs)
        {
            if($c == $acLNs['category'] && $l == $acLNs['language'])
            {
                $tvresult[] = $acLNs;
            }
        }
    }
    if(!empty($tvresult))
    {
        api_res('success', 200, 'OK', $tvresult);
    }
    else
    {
        header("Content-Type: application/json");
    }
}
else
{
    api_res('error', 400, 'Invalid Actions Supplied', '');
}

?>