<?php

//Airtel TV Channel List

include('cmn.configs.php');

//-----------------------------------------------------//

$get_refresh = "";
if(isset($_GET['force_refresh'])){ $get_refresh = $_GET['force_refresh']; }

if($get_refresh !== "yes")
{
    $getikd = @file_get_contents('_chndata');
    $imchnd = hideit('decrypt', $getikd);
    if(!empty($imchnd))
    {
        $ivbz = @json_decode($imchnd, true);
        if(!empty($ivbz))
        {
            api_res('success', 200, 'OK (CACHED)', $ivbz);
        }
    }
}

$items = array();
$lang_code = array('hi' => 'Hindi',
                   'en' => 'English',
                   'ur' => 'Urdu',
                   'ml' => 'Malayalam',
                   'bn' => 'Bengali',
                   'or' => 'Odia',
                   'kn' => 'Kannada',
                   'gu' => 'Gujarati',
                   'te' => 'Telugu',
                   'mr' => 'Marathi',
                   'ta' => 'Tamil',
                   'as' => 'Assamese',
                   'pa' => 'Punjabi',
                   'bh' => 'Bhojpuri',
                   'ra' => 'Rajasthani');

$a = 'https://content.airtel.tv/app/v3/content/channels?mwTvPack=25002&dt=phone&os=ANDROID&ln=hi&isDth=false&dth=false&bn=12674&packageExperimentId=1&appId=MOBILITY&mwTvPack=25002&dt=phone&os=ANDROID&ln=hi&isDth=false&dth=false&bn=12674&packageExperimentId=1';
$b = @file_get_contents($a);
$c = json_decode($b, true);

foreach($c['channels'] as $d)
{
    if(isset($d['imgs']['LANDSCAPE_43'])){ $logo = $d['imgs']['LANDSCAPE_43']; }else{ $logo = ""; }
    if(isset($d['lang'][0])){ if(isset($lang_code[$d['lang'][0]])){ $lang = $lang_code[$d['lang'][0]]; }else{ $lang = ""; } }else{ $lang = ""; }
    if(isset($d['genres'][0])){ $genre = $d['genres'][0]; }else{ $genre = ""; }
    if(isset($d['title'])){ $title = $d['title']; }else{ $title = ""; }
    if(isset($d['id'])){ $chn_id = $d['id']; }else{ $chn_id = ""; }

    if(!empty($chn_id) && !empty($title) && !empty($logo) && !empty($genre) && !empty($lang))
    {
        $items[] = array('id' => $chn_id,
                         'title' => $title,
                         'logo' => $logo,
                         'category' => $genre,
                         'language' => $lang);
    }
}

if(!empty($items))
{
    @file_put_contents('_chndata', hideit('encrypt', json_encode($items)));
    api_res('success', 200, 'OK', $items);
}
else
{
    api_res('error', 404, 'No Channels Found', '');
}


?>
